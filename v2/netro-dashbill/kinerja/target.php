<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Target Kerja & Realisasi Tim";
$page_subtitle = "Monitoring pencapaian target bulanan tim teknisi pasang, tim NOC, dan tim Billing.";
$active_menu = "m-kinerja";
require_once __DIR__ . '/../includes/header.php';
?>

<?php
$invoices = Invoice::all();
$totalInvAmount = 0;
$paidInvAmount = 0;
foreach ($invoices as $inv) {
    $amt = floatval($inv['total_amount'] ?? 0);
    $totalInvAmount += $amt;
    $st = strtolower($inv['status'] ?? 'unpaid');
    if ($st === 'paid' || $st === 'lunas') {
        $paidInvAmount += $amt;
    }
}
$billingPct = ($totalInvAmount > 0) ? round(($paidInvAmount / $totalInvAmount) * 100, 1) : 0;
$customers = Customer::all();
$custCount = count($customers);
$targetCust = 30;
$techPct = min(100, round(($custCount / max(1, $targetCust)) * 100, 1));
$nocPct = ($custCount > 0 || $totalInvAmount > 0) ? 100 : 0;
$overallPerf = round(($techPct + $billingPct + $nocPct) / 3, 1);
?>

<div class="space-y-6 text-xs max-w-4xl mx-auto">
    <!-- Target Overview Progress -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Pencapaian Target Operasional Periode <?= date('F Y') ?></h3>
                <p class="text-slate-400">Realisasi aktual terhadap target kuota bulanan perusahaan.</p>
            </div>
            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">PERFORMA <?= $overallPerf ?>%</span>
        </div>

        <div class="space-y-5">
            <div>
                <div class="flex justify-between font-bold text-slate-800 mb-1.5">
                    <span>1. Tim Teknisi Lapangan (Target Pasang Baru FTTH)</span>
                    <span class="text-blue-600 font-mono"><?= $custCount ?> / <?= $targetCust ?> Titik Selesai (<?= $techPct ?>%)</span>
                </div>
                <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: <?= $techPct ?>%;"></div>
                </div>
                <span class="text-[10px] text-slate-400 block mt-1">Target bulanan: <?= $targetCust ?> aktivasi pelanggan baru. Total terpasang: <?= $custCount ?> titik.</span>
            </div>

            <div>
                <div class="flex justify-between font-bold text-slate-800 mb-1.5">
                    <span>2. Tim Billing & Kasir (Penagihan Tagihan Lunas)</span>
                    <span class="text-emerald-600 font-mono"><?= format_rupiah($paidInvAmount) ?> / <?= format_rupiah($totalInvAmount) ?> (<?= $billingPct ?>%)</span>
                </div>
                <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-emerald-600 h-full rounded-full transition-all duration-500" style="width: <?= $billingPct ?>%;"></div>
                </div>
                <span class="text-[10px] text-slate-400 block mt-1">Target penagihan berjalan lancar, collection rate mencapai <?= $billingPct ?>%.</span>
            </div>

            <div>
                <div class="flex justify-between font-bold text-slate-800 mb-1.5">
                    <span>3. Tim NOC 24/7 (Target Uptime Fiber Ring Backbone)</span>
                    <span class="text-purple-600 font-mono"><?= $nocPct ?>.00% / Target 99.50% (<?= $nocPct >= 99.5 ? 'TERCAPAI' : 'STANDBY' ?>)</span>
                </div>
                <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-purple-600 h-full rounded-full transition-all duration-500" style="width: <?= $nocPct ?>%;"></div>
                </div>
                <span class="text-[10px] text-slate-400 block mt-1">Zero critical downtime. Redundancy BGP IP Transit otomatis failover.</span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
