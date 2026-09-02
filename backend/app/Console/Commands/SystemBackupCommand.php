<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class SystemBackupCommand extends Command
{
    protected $signature = 'isp:db-backup';
    protected $description = 'Create a snapshot backup of application database and FreeRADIUS configurations';

    public function handle(BackupService $backupService): int
    {
        $this->info('📦 Membuat snapshot cadangan sistem database PostgreSQL & RADIUS...');

        try {
            $backup = $backupService->createBackup('system_cron');
            $this->info("✅ Snapshot backup database berhasil dibuat: {$backup->filename} ({$backup->filesize})");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Gagal membuat snapshot backup: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
