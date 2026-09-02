<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Backup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupService
{
    /**
     * Generate full SQL snapshot backup of CRM database and RADIUS database.
     */
    public function createBackup(?string $triggeredBy = null): Backup
    {
        $user = $triggeredBy ?? auth()->user()->username ?? 'system_cron';
        $timestamp = Carbon::now()->format('Ymd_His');
        $backupDir = storage_path('app/backups');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = "netpro_db_backup_{$timestamp}.sql.gz";
        $filePath = "{$backupDir}/{$filename}";

        $sqlContent = "-- ==========================================================\n";
        $sqlContent .= "-- NETPRO CRM OS & FreeRADIUS Engine Snapshot Database Backup\n";
        $sqlContent .= "-- Generated At: " . Carbon::now()->toIso8601String() . "\n";
        $sqlContent .= "-- Created By: {$user}\n";
        $sqlContent .= "-- ==========================================================\n\n";

        // 1. Dump Application CRM Database (pgsql)
        $sqlContent .= "-- ----------------------------------------------------------\n";
        $sqlContent .= "-- DATABASE: CRM APPLICATION (" . config('database.connections.pgsql.database', 'netprocrm') . ")\n";
        $sqlContent .= "-- ----------------------------------------------------------\n\n";
        $sqlContent .= $this->dumpConnection('pgsql');

        // 2. Dump FreeRADIUS Database (radius)
        $sqlContent .= "\n\n-- ----------------------------------------------------------\n";
        $sqlContent .= "-- DATABASE: FREERADIUS AAA (" . config('database.connections.radius.database', 'netpro_radius') . ")\n";
        $sqlContent .= "-- ----------------------------------------------------------\n\n";
        $sqlContent .= $this->dumpConnection('radius');

        // Compress SQL content with gzip
        $gzData = function_exists('gzencode') ? gzencode($sqlContent, 9) : $sqlContent;
        if (!function_exists('gzencode')) {
            $filename = "netpro_db_backup_{$timestamp}.sql";
            $filePath = "{$backupDir}/{$filename}";
        }

        File::put($filePath, $gzData);

        $bytes = File::size($filePath);
        $filesize = $this->formatBytes($bytes);

        $backup = Backup::create([
            'filename' => $filename,
            'filesize' => $filesize,
            'path' => $filePath,
        ]);

        AuditLog::log($user, 'AUTO_BACKUP_SQL', "Database auto-dump completed: {$filename} ({$filesize})");
        Log::info("Backup database successfully created: {$filePath} ({$filesize})");

        return $backup;
    }

    /**
     * Dump all tables and rows from a specific database connection.
     */
    protected function dumpConnection(string $connectionName): string
    {
        $out = "";
        try {
            $connection = DB::connection($connectionName);
            $driver = $connection->getDriverName();

            if ($driver === 'pgsql') {
                $tables = $connection->select("
                    SELECT table_name 
                    FROM information_schema.tables 
                    WHERE table_schema = 'public' 
                      AND table_type = 'BASE TABLE'
                    ORDER BY table_name ASC
                ");

                foreach ($tables as $t) {
                    $tableName = $t->table_name;
                    $out .= "-- Table: {$tableName}\n";
                    $out .= "DROP TABLE IF EXISTS \"{$tableName}\" CASCADE;\n";

                    $rows = $connection->table($tableName)->get();
                    if ($rows->count() > 0) {
                        foreach ($rows as $row) {
                            $rowArray = (array) $row;
                            $columns = array_keys($rowArray);
                            $escapedColumns = array_map(fn($c) => "\"{$c}\"", $columns);
                            $values = array_map(function ($val) use ($connection) {
                                if (is_null($val)) {
                                    return 'NULL';
                                }
                                if (is_bool($val)) {
                                    return $val ? 'TRUE' : 'FALSE';
                                }
                                if (is_numeric($val) && !is_string($val)) {
                                    return $val;
                                }
                                return $connection->getPdo()->quote((string) $val);
                            }, array_values($rowArray));

                            $out .= "INSERT INTO \"{$tableName}\" (" . implode(', ', $escapedColumns) . ") VALUES (" . implode(', ', $values) . ");\n";
                        }
                    }
                    $out .= "\n";
                }
            } elseif ($driver === 'sqlite') {
                $tables = $connection->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
                foreach ($tables as $t) {
                    $tableName = $t->name;
                    $rows = $connection->table($tableName)->get();
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $columns = array_keys($rowArray);
                        $values = array_map(fn($v) => is_null($v) ? 'NULL' : $connection->getPdo()->quote((string) $v), array_values($rowArray));
                        $out .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");\n";
                    }
                    $out .= "\n";
                }
            }
        } catch (\Throwable $e) {
            $out .= "-- Error dumping connection [{$connectionName}]: " . $e->getMessage() . "\n";
            Log::error("Error dumping connection [{$connectionName}]: " . $e->getMessage());
        }

        return $out;
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
