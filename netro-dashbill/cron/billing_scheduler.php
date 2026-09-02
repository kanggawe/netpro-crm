<?php
/**
 * NETPRO CRM — Automated Background Billing Scheduler & Daemon (CLI)
 * Task: TASK-02 (Mass Generation, Leap-Year Safe Clamping, Auto-Isolate, & WhatsApp Reminders)
 * 
 * Usage via CLI / Linux Crontab:
 *   php cron/billing_scheduler.php --all
 *   php cron/billing_scheduler.php --generate
 *   php cron/billing_scheduler.php --isolir
 *   php cron/billing_scheduler.php --reminder
 * 
 * Crontab Recommended Setup (Linux Server):
 *   # Run monthly invoice generation on 1st of every month at 00:05 WIB
 *   5 0 1 * * php /var/www/html/cron/billing_scheduler.php --generate >> /var/log/netpro_billing.log 2>&1
 *   # Run daily overdue check & auto-isolir at 00:15 WIB
 *   15 0 * * * php /var/www/html/cron/billing_scheduler.php --isolir >> /var/log/netpro_billing.log 2>&1
 *   # Run daily WhatsApp payment reminders at 09:00 WIB
 *   0 9 * * * php /var/www/html/cron/billing_scheduler.php --reminder >> /var/log/netpro_billing.log 2>&1
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

// Check if running from CLI or authorized Web Request
$isCli = (php_sapi_name() === 'cli');
$action = 'all';

if ($isCli) {
    global $argv;
    if (isset($argv[1])) {
        $arg = ltrim($argv[1], '-');
        if (in_array($arg, ['all', 'generate', 'isolir', 'reminder'])) {
            $action = $arg;
        }
    }
} else {
    $action = $_GET['action'] ?? 'all';
    // Web protection: only allow logged in super admin or secret cron key
    if (!is_logged_in()) {
        $token = $_GET['token'] ?? '';
        $expectedToken = Setting::get('cron_secret_token', 'netpro_cron_secret_2026');
        if ($token !== $expectedToken) {
            http_response_code(403);
            die(json_encode(['status' => 'error', 'message' => 'Unauthorized cron trigger']));
        }
    }
}

$startTime = microtime(true);
$today = date('Y-m-d H:i:s');
$currentMonthPeriod = date('F Y');

$outputLogs = [];
$outputLogs[] = "=================================================================";
$outputLogs[] = "🚀 NETPRO CRM: AUTOMATED BILLING SCHEDULER DAEMON";
$outputLogs[] = "📅 Timestamp: $today WIB | Mode: " . strtoupper($action);
$outputLogs[] = "=================================================================";

$stats = [
    'invoices_generated' => 0,
    'customers_isolated' => 0,
    'reminders_queued' => 0
];

// =================================================================
// 1. GENERASI TAGIHAN BULANAN MASSAL (LEAP-YEAR & MONTH-SAFE)
// =================================================================
if ($action === 'all' || $action === 'generate') {
    $outputLogs[] = "\n[STEP 1] Memulai Pengecekan & Generasi Tagihan Massal ($currentMonthPeriod)...";
    
    $generatedCount = Invoice::generateMassal($currentMonthPeriod);
    $stats['invoices_generated'] = $generatedCount;
    
    $daysInMonth = Invoice::getDaysInMonth(date('Y'), date('m'));
    $outputLogs[] = "  ✓ Total hari bulan ini: $daysInMonth hari (" . (date('L') ? "TAHUN KABISAT" : "TAHUN NORMAL") . ")";
    $outputLogs[] = "  ✓ Berhasil menerbitkan $generatedCount invoice pascabayar baru untuk periode $currentMonthPeriod.";
}

// =================================================================
// 2. PENGECEKAN JATUH TEMPO & AUTO-ISOLIR MIKROTIK
// =================================================================
if ($action === 'all' || $action === 'isolir') {
    $outputLogs[] = "\n[STEP 2] Memeriksa Tagihan Melewati Jatuh Tempo & Grace Period...";
    
    $isolateResult = Invoice::checkAndAutoIsolate();
    $stats['customers_isolated'] = $isolateResult['isolated_count'] ?? 0;
    
    if (($isolateResult['status'] ?? '') === 'disabled') {
        $outputLogs[] = "  ℹ Fitur Auto-Isolir MikroTik sedang NONAKTIF pada Pengaturan Billing.";
    } else {
        $outputLogs[] = "  ✓ Batas Cut-Off Tanggal Jatuh Tempo: " . ($isolateResult['cutoff_date'] ?? '-');
        $outputLogs[] = "  ✓ Jumlah pelanggan diisolir otomatis: " . $stats['customers_isolated'] . " pelanggan.";
        if (!empty($isolateResult['customers'])) {
            foreach ($isolateResult['customers'] as $c) {
                $outputLogs[] = "    - [ISOLIR] #{$c['customer_id']} {$c['name']} (User: {$c['pppoe_user']}) | Inv: {$c['invoice_no']} Rp " . number_format($c['total_amount']);
            }
        }
    }
}

// =================================================================
// 3. ANTREAN PENGINGAT TAGIHAN WHATSAPP (REMINDER QUEUE H-3, H-1, H+1)
// =================================================================
if ($action === 'all' || $action === 'reminder') {
    $outputLogs[] = "\n[STEP 3] Menyiapkan Antrean Pesan Pengingat Pembayaran (WhatsApp Gateway)...";
    
    $reminders = Invoice::getReminderQueue();
    $stats['reminders_queued'] = count($reminders);
    
    $outputLogs[] = "  ✓ Ditemukan " . count($reminders) . " pelanggan dalam antrean notifikasi:";
    foreach ($reminders as $r) {
        $type = $r['reminder_type'];
        $outputLogs[] = "    - [{$type}] Pelanggan: {$r['name']} ({$r['phone']}) | Tagihan: {$r['invoice_no']} (Jatuh Tempo: {$r['due_date']})";
    }
}

$elapsed = round((microtime(true) - $startTime) * 1000, 2);
$outputLogs[] = "\n=================================================================";
$outputLogs[] = "✅ DAEMON SELESAI DIPROSES DALAM {$elapsed} ms";
$outputLogs[] = "📊 Ringkasan: {$stats['invoices_generated']} Invoice Terbit | {$stats['customers_isolated']} Isolir | {$stats['reminders_queued']} Reminder";
$outputLogs[] = "=================================================================\n";

$fullLog = implode("\n", $outputLogs);

if ($isCli) {
    echo $fullLog;
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'elapsed_ms' => $elapsed,
        'stats' => $stats,
        'log' => $outputLogs
    ], JSON_PRETTY_PRINT);
}
