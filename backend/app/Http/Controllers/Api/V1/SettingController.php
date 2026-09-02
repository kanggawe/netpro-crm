<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Backup;
use App\Models\Setting;
use App\Services\BackupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');
        return response()->json([
            'status' => 'success',
            'data' => $settings,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->all();
        foreach ($data as $key => $value) {
            Setting::set($key, (string) $value);
        }

        AuditLog::log(auth()->user()->username ?? 'system', 'SETTINGS_UPDATE', 'Update konfigurasi global sistem');

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaturan sistem berhasil disimpan.',
            'data' => Setting::all()->pluck('value', 'key'),
        ]);
    }

    public function auditLogs(Request $request): JsonResponse
    {
        $query = AuditLog::orderBy('id', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('username', 'like', "%{$s}%")
                    ->orWhere('action', 'like', "%{$s}%")
                    ->orWhere('details', 'like', "%{$s}%")
                    ->orWhere('ip_address', 'like', "%{$s}%");
            });
        }

        $logs = $query->paginate($request->get('per_page', 50));

        return response()->json([
            'status' => 'success',
            'data' => $logs,
        ]);
    }

    public function backups(): JsonResponse
    {
        $backups = Backup::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $backups,
        ]);
    }

    public function createBackup(BackupService $backupService): JsonResponse
    {
        try {
            $backup = $backupService->createBackup(auth()->user()->username ?? 'admin');
            return response()->json([
                'status' => 'success',
                'message' => "File arsip backup {$backup->filename} ({$backup->filesize}) berhasil dibuat.",
                'data' => $backup,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat backup database: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function downloadBackup(int $id): BinaryFileResponse|JsonResponse
    {
        $backup = Backup::findOrFail($id);
        if (!File::exists($backup->path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File fisik arsip backup tidak ditemukan di server.',
            ], 404);
        }

        AuditLog::log(auth()->user()->username ?? 'admin', 'DATABASE_BACKUP_DOWNLOAD', "Unduh arsip backup {$backup->filename}");

        return response()->download($backup->path, $backup->filename, [
            'Content-Type' => 'application/gzip',
        ]);
    }

    public function destroyBackup(int $id): JsonResponse
    {
        $backup = Backup::findOrFail($id);
        if (File::exists($backup->path)) {
            File::delete($backup->path);
        }
        $filename = $backup->filename;
        $backup->delete();

        AuditLog::log(auth()->user()->username ?? 'admin', 'DATABASE_BACKUP_DELETE', "Hapus file arsip backup {$filename}");

        return response()->json([
            'status' => 'success',
            'message' => "Arsip backup {$filename} berhasil dihapus.",
        ]);
    }
}
