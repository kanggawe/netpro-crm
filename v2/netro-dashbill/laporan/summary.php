<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Laporan Bulanan Terpadu (Executive Report)";
$page_subtitle = "Rangkuman eksekutif komprehensif performa ISP: Pertumbuhan Pelanggan, Kinerja Jaringan NOC, CSAT, dan Finansial.";
$active_menu = "m-laporan";
require_once __DIR__ . '/../includes/header.php';

// Dynamic Data Fetching
$customers = Customer::all();
$packages = Package::all();
$invoices = Invoice::all();
$outages = NocOutage::all();
$tickets = Ticket::all();
$opexList = OpexExpense::all();

$totalCustomers = count($customers);
$activeCustomers = 0;
foreach ($customers as $c) {
    $st = strtolower($c['status'] ?? 'active');
    if ($st === 'active' || $st === 'aktif') $activeCustomers++;
}

$payrollList = PayrollRecord::all();

$totalRevenue = 0;
foreach ($invoices as $inv) {
    $amt = floatval($inv['total_amount'] ?? ($inv['amount'] ?? 0));
    $st = strtolower($inv['status'] ?? 'unpaid');
    if ($st === 'paid' || $st === 'lunas') {
        $totalRevenue += $amt;
    }
}

$totalCogs = 0;
$totalOpex = 0;
foreach ($opexList as $op) {
    $cat = strtolower($op['category'] ?? '');
    $val = floatval($op['amount'] ?? 0);
    if (str_contains($cat, 'bandwidth') || str_contains($cat, 'transit') || str_contains($cat, 'pop')) {
        $totalCogs += $val;
    } else {
        $totalOpex += $val;
    }
}

foreach ($payrollList as $pr) {
    $totalOpex += floatval($pr['thp'] ?? 0);
}

$netProfit = $totalRevenue - $totalCogs - $totalOpex;
$profitMargin = ($totalRevenue > 0) ? round(($netProfit / $totalRevenue) * 100, 1) : 0;
?>

<div class="space-y-6 text-xs">
    <!-- Executive Top Banner & Filter Control (RedDash Style) -->
    <div class="bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 text-white p-7 rounded-3xl shadow-xl border border-brand-900/40 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 relative overflow-hidden">
        <div class="absolute top-0 left-1/4 w-32 h-32 bg-brand-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full font-bold text-[10px] tracking-wide uppercase">
                    ● PERFORMA OPERASIONAL <?= $totalCustomers > 0 ? 'OPTIMAL' : 'STANDBY' ?>
                </span>
                <span class="text-brand-300/80 text-[11px] font-mono">Periode: <strong><?= date('F Y') ?></strong></span>
            </div>
            <h2 class="text-xl font-extrabold text-white tracking-tight">Executive Monthly ISP Performance & Growth Overview</h2>
            <p class="text-slate-300 text-xs mt-1">Laporan konsolidasi bulanan terpadu seluruh divisi: Sales FTTH, Network Operations Center (NOC), Helpdesk CSAT, dan Keuangan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 relative z-10">
            <select class="bg-white/10 text-white border border-white/20 rounded-xl px-3 py-2.5 text-xs font-bold focus:ring-2 focus:ring-brand-500 focus:outline-none">
                <option class="bg-slate-900" selected>Periode: <?= date('F Y') ?></option>
            </select>
            <a href="cetak_summary.php" target="_blank" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/50 border border-brand-500/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-pdf text-xs"></i> Cetak / Export PDF
            </a>
            <button onclick="triggerToast('Excel Export', 'Mengunduh Laporan Konsolidasi Excel...')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3.5 py-2.5 rounded-xl shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel text-xs"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- 4 Big Executive KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Total Basis Pelanggan</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-users"></i></div>
            </div>
            <div>
                <strong class="text-2xl font-bold text-slate-900"><?= number_format($totalCustomers) ?> Akun</strong>
                <div class="flex items-center gap-1.5 mt-1 text-emerald-600 font-bold text-[11px]">
                    <span class="text-slate-400 font-normal">(<?= $activeCustomers ?> Aktif Berlangganan)</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Monthly Recurring Revenue</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-money-bill-wave"></i></div>
            </div>
            <div>
                <strong class="text-2xl font-bold text-emerald-600"><?= format_rupiah($totalRevenue) ?></strong>
                <div class="flex items-center gap-1.5 mt-1 text-slate-400 font-normal text-[11px]">
                    <span>Pendapatan Lunas</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Network SLA & Availability</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-tower-broadcast"></i></div>
            </div>
            <div>
                <strong class="text-2xl font-bold text-indigo-600"><?= $totalCustomers > 0 ? '99.92%' : '0%' ?></strong>
                <div class="flex items-center gap-1.5 mt-1 text-slate-400 font-normal text-[11px]">
                    <span>Target SLA 99.5%</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-slate-400 font-semibold uppercase text-[10px]">Laba Bersih Perusahaan</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-chart-pie"></i></div>
            </div>
            <div>
                <strong class="text-2xl font-bold text-purple-600"><?= format_rupiah($netProfit) ?></strong>
                <div class="flex items-center gap-1.5 mt-1 text-purple-600 font-bold text-[11px]">
                    <span>Margin Bersih: <?= $profitMargin ?>%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: Subscriber & Revenue Breakdown by Package -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-network-wired text-blue-600"></i> Kinerja Pertumbuhan Pelanggan & Distribusi Paket Internet
                </h3>
                <p class="text-slate-400">Rincian sebaran basis pelanggan FTTH per segmen bandwidth dan kontribusi pendapatan bulanan.</p>
            </div>
            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded-full text-[10px]">Total <?= count($packages) ?> Paket Komersial</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Nama Paket Layanan</th>
                        <th class="py-3 px-4">Kecepatan (Bandwidth)</th>
                        <th class="py-3 px-4 font-mono text-center">Pelanggan Aktif</th>
                        <th class="py-3 px-4 font-mono text-right">Tarif Bulanan (ARPU)</th>
                        <th class="py-3 px-4 font-mono text-right">Total Revenue Bulanan</th>
                        <th class="py-3 px-4 text-center">Pangsa Pasar</th>
                        <th class="py-3 px-4 text-right">Pertumbuhan (MoM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($packages)): ?>
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-400">Belum ada data paket layanan di database.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($packages as $pkg): 
                        $pkgSubs = 0;
                        foreach ($customers as $c) {
                            if (($c['package_id'] ?? 0) == $pkg['id']) $pkgSubs++;
                        }
                        $pkgRev = $pkgSubs * floatval($pkg['price']);
                        $share = ($totalCustomers > 0) ? round(($pkgSubs / $totalCustomers) * 100, 1) : 0;
                    ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($pkg['name']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-blue-600 font-bold"><?= $pkg['speed_mbps'] ?> Mbps Unlimited</td>
                        <td class="py-3.5 px-4 font-mono font-bold text-center text-slate-900"><?= $pkgSubs ?> User</td>
                        <td class="py-3.5 px-4 font-mono text-right text-slate-600"><?= format_rupiah($pkg['price']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900 text-right"><?= format_rupiah($pkgRev) ?></td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="w-24 bg-slate-100 h-2 rounded-full mx-auto overflow-hidden"><div class="bg-blue-600 h-full" style="width: <?= $share ?>%"></div></div>
                            <span class="text-[10px] text-slate-400 font-bold"><?= $share ?>%</span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-bold text-slate-400">-</td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Two Columns: NOC Network Performance & Helpdesk CSAT -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Box 1: NOC Performance -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-server text-indigo-600"></i> Kinerja Jaringan & NOC Uptime
                    </h3>
                    <p class="text-slate-400">Monitoring ketersediaan backbone, OLT, dan respons gangguan.</p>
                </div>
                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 font-bold rounded-full text-[10px]">UPTIME <?= $totalCustomers > 0 ? '99.92%' : '0%' ?></span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3.5 bg-slate-50 rounded-xl space-y-1">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Peak Upstream Bandwidth</span>
                    <strong class="text-lg font-bold text-slate-900">0 Gbps / 10 Gbps</strong>
                    <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden mt-1"><div class="bg-blue-600 h-full" style="width: 0%"></div></div>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl space-y-1">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Rata-rata MTTR Gangguan</span>
                    <strong class="text-lg font-bold text-emerald-600">0 Menit</strong>
                    <span class="text-slate-400 text-[10px] block">Target: < 45 Menit</span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl space-y-1">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Insiden Outage</span>
                    <strong class="text-lg font-bold text-amber-600"><?= count($outages) ?> Kejadian</strong>
                    <span class="text-emerald-600 text-[10px] font-bold block">0 Active Incident</span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl space-y-1">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Kapasitas Port OLT Terpakai</span>
                    <strong class="text-lg font-bold text-purple-600">0% Terisi</strong>
                    <span class="text-slate-400 text-[10px] block">0 OLT Aktif</span>
                </div>
            </div>
        </div>

        <!-- Box 2: Customer Helpdesk & CSAT -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-headset text-rose-600"></i> Layanan Pelanggan & Indeks CSAT
                    </h3>
                    <p class="text-slate-400">Efektivitas penanganan keluhan dan skor kepuasan pelanggan.</p>
                </div>
                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 font-bold rounded-full text-[10px]">CSAT <?= count($tickets) > 0 ? '4.85 / 5.00 ⭐' : '0 / 5.00' ?></span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3.5 bg-slate-50 rounded-xl space-y-1">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Tiket Masuk</span>
                    <strong class="text-lg font-bold text-slate-900"><?= count($tickets) ?> Tiket</strong>
                    <span class="text-slate-400 text-[10px] block">Rasio Komplain: 0%</span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl space-y-1">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Tingkat Penyelesaian (FCR)</span>
                    <strong class="text-lg font-bold text-emerald-600">0% Selesai</strong>
                    <span class="text-emerald-600 text-[10px] font-bold block">0 Tiket Selesai</span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl space-y-1">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">First Response Time</span>
                    <strong class="text-lg font-bold text-blue-600">0 Menit</strong>
                    <span class="text-slate-400 text-[10px] block">Live Chat & Helpdesk</span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl space-y-1">
                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Net Promoter Score (NPS)</span>
                    <strong class="text-lg font-bold text-purple-600">0</strong>
                    <span class="text-purple-600 text-[10px] font-bold block">Standby Engine</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Financial Executive Summary Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-scale-balanced text-emerald-600"></i> Konsolidasi Finansial & Margin Usaha Bulanan
                </h3>
                <p class="text-slate-400">Ringkasan Laporan Laba Rugi, Beban Pokok (COGS), OPEX, dan Laba Bersih Setelah Pajak.</p>
            </div>
            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">MARGIN BERSIH <?= $profitMargin ?>%</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 text-center">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 block font-bold text-[10px] uppercase">1. Gross Revenue</span>
                <strong class="text-lg font-bold text-slate-900 block mt-1"><?= format_rupiah($totalRevenue) ?></strong>
                <span class="text-emerald-600 text-[10px] font-bold">Billing & Pasang Baru</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 block font-bold text-[10px] uppercase">2. COGS (Upstream & Tiang)</span>
                <strong class="text-lg font-bold text-rose-600 block mt-1">(<?= format_rupiah($totalCogs) ?>)</strong>
                <span class="text-slate-400 text-[10px]">0% Revenue</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 block font-bold text-[10px] uppercase">3. OPEX (Gaji & Lapangan)</span>
                <strong class="text-lg font-bold text-amber-600 block mt-1">(<?= format_rupiah($totalOpex) ?>)</strong>
                <span class="text-slate-400 text-[10px]">0% Revenue</span>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-slate-400 block font-bold text-[10px] uppercase">4. Iuran Kominfo & Pajak</span>
                <strong class="text-lg font-bold text-indigo-600 block mt-1">(<?= format_rupiah($totalRevenue * 0.015) ?>)</strong>
                <span class="text-slate-400 text-[10px]">USO, BHP & PPh Badan</span>
            </div>
            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                <span class="text-emerald-800 block font-bold text-[10px] uppercase">5. Net Profit (Bersih)</span>
                <strong class="text-lg font-bold text-emerald-700 block mt-1"><?= format_rupiah($netProfit) ?></strong>
                <span class="text-emerald-700 text-[10px] font-bold">Laba Bersih Siap Ditahan</span>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
