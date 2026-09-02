<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\BillingService;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class BillingSchedulerCommand extends Command
{
    protected $signature = 'isp:billing-scheduler 
                            {--all : Jalankan seluruh rutin penagihan}
                            {--generate : Terbitkan tagihan pascabayar massal}
                            {--isolir : Eksekusi auto-isolir akun jatuh tempo & expired}
                            {--reminder : Kirim notifikasi WhatsApp pengingat tagihan}';

    protected $description = 'Automated Leap-Year Safe ISP Billing Daemon Scheduler';

    public function handle(BillingService $billingService, NotificationService $notifService): int
    {
        $this->info('====================================================');
        $this->info('🚀 NETPRO CRM — ISP BILLING SCHEDULER DAEMON');
        $this->info('====================================================');

        $runAll = $this->option('all') || (!$this->option('generate') && !$this->option('isolir') && !$this->option('reminder'));

        // 1. Generate Invoices
        if ($runAll || $this->option('generate')) {
            $this->line('<fg=yellow>-> Menjalankan penerbitan tagihan massal...</>');
            $res = $billingService->generateMonthlyInvoices();
            $this->info("   [OK] Periode: {$res['period']} | Terbit: {$res['generated_count']} | Dilewati: {$res['skipped_count']}");
        }

        // 2. Process Auto-Isolir
        if ($runAll || $this->option('isolir')) {
            $this->line('<fg=yellow>-> Memeriksa akun jatuh tempo & masa aktif habis (Auto-Isolir & CoA Kick)...</>');
            $res = $billingService->processAutoIsolir();
            $this->info("   [OK] Total pelanggan diisolir: {$res['isolated_count']}");
        }

        // 3. Send Payment Reminders
        if ($runAll || $this->option('reminder')) {
            $this->line('<fg=yellow>-> Mengirim notifikasi pengingat WhatsApp...</>');
            $unpaid = Invoice::where('status', 'unpaid')->with('customer')->get();
            $reminded = 0;
            foreach ($unpaid as $inv) {
                if ($notifService->sendInvoiceReminder($inv)) {
                    $reminded++;
                }
            }
            $this->info("   [OK] Total notifikasi dikirim: {$reminded}");
        }

        $this->info('====================================================');
        $this->info('✅ Eksekusi Rutin Billing Selesai Sempurna.');
        return self::SUCCESS;
    }
}
