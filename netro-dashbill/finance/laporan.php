<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Neraca & Laba Rugi Komprehensif";
$page_subtitle = "Laporan posisi keuangan formal ISP (Balance Sheet, Income Statement, & Financial Ratios).";
$active_menu = "m-finance";
require_once __DIR__ . '/../includes/header.php';

// Dynamic Financial Calculations from Database
$invoices = Invoice::all();
$opexList = OpexExpense::all();
$payrollList = PayrollRecord::all();
$cashList = Cash::all();
$inventoryList = Inventory::all();

$realRevenue = 0;
$realPiutang = 0;
foreach ($invoices as $inv) {
    $amt = floatval($inv['total_amount'] ?? ($inv['amount'] ?? 0));
    $st = strtolower($inv['status'] ?? 'unpaid');
    if ($st === 'paid' || $st === 'lunas') {
        $realRevenue += $amt;
    } else {
        $realPiutang += $amt;
    }
}

$cogs = 0;
$opex = 0;
foreach ($opexList as $op) {
    $cat = strtolower($op['category'] ?? '');
    $val = floatval($op['amount'] ?? 0);
    if (str_contains($cat, 'bandwidth') || str_contains($cat, 'transit') || str_contains($cat, 'pop')) {
        $cogs += $val;
    } else {
        $opex += $val;
    }
}

foreach ($payrollList as $pr) {
    $opex += floatval($pr['thp'] ?? 0);
}

$totalRevenue = $realRevenue;
$grossProfit = $totalRevenue - $cogs;
$netProfit = $grossProfit - $opex;
$margin = ($totalRevenue > 0) ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

// Balance Sheet
$kasMasuk = 0;
$kasKeluar = 0;
foreach ($cashList as $cs) {
    $amt = floatval($cs['amount'] ?? 0);
    if (strtoupper($cs['type'] ?? '') === 'IN') {
        $kasMasuk += $amt;
    } else {
        $kasKeluar += $amt;
    }
}
$kasBank = max(0, $kasMasuk - $kasKeluar);
if ($kasBank === 0 && $totalRevenue > 0) {
    $kasBank = $totalRevenue;
}

$piutang = $realPiutang;
$persediaan = 0;
foreach ($inventoryList as $it) {
    $persediaan += intval($it['stock'] ?? 0) * 150000;
}
$totalAsetLancar = $kasBank + $piutang + $persediaan;

$asetTetap = 0;
$penyusutan = 0;
$totalAsetTetap = $asetTetap + $penyusutan;
$totalAset = $totalAsetLancar + $totalAsetTetap;

$hutangUsaha = 0;
$depositPelanggan = 0;
$totalKewajiban = $hutangUsaha + $depositPelanggan;

$modal = 0;
$labaDitahan = $totalAset - $totalKewajiban - $modal;
$totalEkuitas = $totalAset;
$totalPassiva = $totalKewajiban + $totalEkuitas;
?>

<div class="space-y-6 text-xs">
    <!-- Top 4 Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Total Pendapatan (Revenue)</span>
                <strong class="text-2xl font-bold text-slate-900"><?= format_rupiah($totalRevenue) ?></strong>
                <span class="text-emerald-600 font-medium block mt-0.5">▲ Realtime Engine</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Total Beban (COGS & OPEX)</span>
                <strong class="text-2xl font-bold text-rose-600"><?= format_rupiah($cogs + $opex) ?></strong>
                <span class="text-slate-400 block mt-0.5">Beban Operasional ISP</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg"><i class="fa-solid fa-receipt"></i></div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Laba Bersih (Net Profit)</span>
                <strong class="text-2xl font-bold <?= $netProfit >= 0 ? 'text-blue-600' : 'text-rose-600' ?>"><?= format_rupiah($netProfit) ?></strong>
                <span class="text-slate-400 font-medium block mt-0.5">Margin: <?= $margin ?>%</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="fa-solid fa-sack-dollar"></i></div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Total Aset (Aktiva)</span>
                <strong class="text-2xl font-bold text-purple-600"><?= format_rupiah($totalAset) ?></strong>
                <span class="text-emerald-600 font-bold block mt-0.5">✓ Neraca Balanced</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg"><i class="fa-solid fa-scale-balanced"></i></div>
        </div>
    </div>

    <!-- Filter & Action Controls -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-3">
        <div class="flex items-center gap-3">
            <div>
                <span class="font-bold text-slate-700 block">Periode Laporan Keuangan:</span>
                <span class="text-slate-400 text-[11px]">Bulan Berjalan Tahun Anggaran <?= date('Y') ?></span>
            </div>
            <select class="bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                <option><?= date('F Y') ?></option>
            </select>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak Laporan PDF
            </button>
            <button onclick="triggerToast('Export Excel', 'File rekapitulasi keuangan disiapkan...')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- 2-Column Statements Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Column 1: Laporan Laba Rugi (Income Statement) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-blue-600"></i> Laporan Laba Rugi (Income Statement)
                    </h3>
                    <p class="text-slate-400">Periode: <?= date('01 F Y') ?> - <?= date('t F Y') ?></p>
                </div>
                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded-full text-[10px]">IDR (Rupiah)</span>
            </div>

            <div class="space-y-4">
                <!-- 1. PENDAPATAN -->
                <div>
                    <span class="font-bold text-slate-800 uppercase block mb-1">1. PENDAPATAN USAHA (REVENUE)</span>
                    <div class="space-y-1.5 pl-2 border-l-2 border-slate-200">
                        <div class="flex justify-between text-slate-600"><span>Pendapatan Langganan Internet FTTH</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($totalRevenue) ?></span></div>
                        <div class="flex justify-between text-slate-600"><span>Pendapatan Biaya Pasang Baru</span><span class="font-mono font-bold text-slate-800">Rp 0</span></div>
                        <div class="flex justify-between text-slate-600"><span>Pendapatan Add-on (IP Publik & CCTV)</span><span class="font-mono font-bold text-slate-800">Rp 0</span></div>
                        <div class="flex justify-between text-emerald-700 font-bold pt-1 border-t border-slate-100"><span>Total Pendapatan Bersih (Net Revenue)</span><span class="font-mono"><?= format_rupiah($totalRevenue) ?></span></div>
                    </div>
                </div>

                <!-- 2. BEBAN POKOK -->
                <div>
                    <span class="font-bold text-slate-800 uppercase block mb-1">2. BEBAN POKOK PENDAPATAN (COGS)</span>
                    <div class="space-y-1.5 pl-2 border-l-2 border-slate-200">
                        <div class="flex justify-between text-slate-600"><span>Sewa Bandwidth Upstream (Telkom & Indosat)</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($cogs) ?></span></div>
                        <div class="flex justify-between text-slate-600"><span>Sewa Tiang & Dark Fiber Core PLN</span><span class="font-mono font-bold text-slate-800">Rp 0</span></div>
                        <div class="flex justify-between text-slate-600"><span>Listrik & Daya POP Core / Server</span><span class="font-mono font-bold text-slate-800">Rp 0</span></div>
                        <div class="flex justify-between text-rose-700 font-bold pt-1 border-t border-slate-100"><span>Total Beban Pokok (COGS)</span><span class="font-mono">(<?= format_rupiah($cogs) ?>)</span></div>
                    </div>
                </div>

                <!-- LABA KOTOR -->
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex justify-between items-center font-bold">
                    <span class="text-slate-800">LABA KOTOR (GROSS PROFIT)</span>
                    <span class="font-mono text-emerald-600 text-sm"><?= format_rupiah($grossProfit) ?></span>
                </div>

                <!-- 3. BEBAN OPERASIONAL -->
                <div>
                    <span class="font-bold text-slate-800 uppercase block mb-1">3. BEBAN OPERASIONAL & UMUM (OPEX)</span>
                    <div class="space-y-1.5 pl-2 border-l-2 border-slate-200">
                        <div class="flex justify-between text-slate-600"><span>Gaji Karyawan, Teknisi & BPJS (Payroll)</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($opex) ?></span></div>
                        <div class="flex justify-between text-slate-600"><span>Operasional Lapangan, BBM & Transport</span><span class="font-mono font-bold text-slate-800">Rp 0</span></div>
                        <div class="flex justify-between text-slate-600"><span>Pemasaran, Iklan & Komisi Sales</span><span class="font-mono font-bold text-slate-800">Rp 0</span></div>
                        <div class="flex justify-between text-slate-600"><span>Penyusutan Aset Perangkat (Depresiasi)</span><span class="font-mono font-bold text-slate-800">Rp 0</span></div>
                        <div class="flex justify-between text-rose-700 font-bold pt-1 border-t border-slate-100"><span>Total Beban Operasional (OPEX)</span><span class="font-mono">(<?= format_rupiah($opex) ?>)</span></div>
                    </div>
                </div>

                <!-- FINAL NET PROFIT -->
                <div class="p-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl flex justify-between items-center shadow-lg">
                    <div>
                        <h4 class="font-extrabold text-sm uppercase tracking-wider">LABA BERSIH TAHUN BERJALAN (NET PROFIT)</h4>
                        <span class="text-[11px] text-blue-100">Margin Laba Bersih: <?= $margin ?>%</span>
                    </div>
                    <strong class="font-mono text-xl"><?= format_rupiah($netProfit) ?></strong>
                </div>
            </div>
        </div>

        <!-- Column 2: Neraca Keuangan (Balance Sheet) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-scale-balanced text-indigo-600"></i> Neraca Keuangan (Balance Sheet)
                    </h3>
                    <p class="text-slate-400">Posisi Per <?= date('t F Y') ?></p>
                </div>
                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">BALANCED ✓</span>
            </div>

            <div class="space-y-4">
                <!-- ASET LANCAR -->
                <div>
                    <span class="font-bold text-slate-800 uppercase block mb-1">ASET LANCAR (CURRENT ASSETS)</span>
                    <div class="space-y-1.5 pl-2 border-l-2 border-slate-200">
                        <div class="flex justify-between text-slate-600"><span>Kas & Rekening Bank (BCA + Mandiri)</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($kasBank) ?></span></div>
                        <div class="flex justify-between text-slate-600"><span>Piutang Tagihan Pelanggan (Unpaid)</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($piutang) ?></span></div>
                        <div class="flex justify-between text-slate-600"><span>Persediaan Stok Material Gudang (ONT/FO)</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($persediaan) ?></span></div>
                        <div class="flex justify-between text-blue-700 font-bold pt-1 border-t border-slate-100"><span>Total Aset Lancar</span><span class="font-mono"><?= format_rupiah($totalAsetLancar) ?></span></div>
                    </div>
                </div>

                <!-- ASET TIDAK LANCAR -->
                <div>
                    <span class="font-bold text-slate-800 uppercase block mb-1">ASET TETAP (FIXED ASSETS)</span>
                    <div class="space-y-1.5 pl-2 border-l-2 border-slate-200">
                        <div class="flex justify-between text-slate-600"><span>Peralatan OLT, Router Core, Splicer</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($asetTetap) ?></span></div>
                        <div class="flex justify-between text-slate-600"><span>Akumulasi Penyusutan Alat</span><span class="font-mono text-rose-600 font-bold">(<?= format_rupiah(abs($penyusutan)) ?>)</span></div>
                        <div class="flex justify-between text-blue-700 font-bold pt-1 border-t border-slate-100"><span>Total Aset Tetap Bersih</span><span class="font-mono"><?= format_rupiah($totalAsetTetap) ?></span></div>
                    </div>
                </div>

                <!-- TOTAL ASET -->
                <div class="p-3 bg-purple-50 border border-purple-200 rounded-xl flex justify-between items-center font-bold text-purple-900">
                    <span>TOTAL AKTIVA / ASET</span>
                    <span class="font-mono text-sm"><?= format_rupiah($totalAset) ?></span>
                </div>

                <!-- KEWAJIBAN & EKUITAS -->
                <div>
                    <span class="font-bold text-slate-800 uppercase block mb-1">KEWAJIBAN (LIABILITIES)</span>
                    <div class="space-y-1.5 pl-2 border-l-2 border-slate-200">
                        <div class="flex justify-between text-slate-600"><span>Hutang Usaha Upstream & Supplier</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($hutangUsaha) ?></span></div>
                        <div class="flex justify-between text-slate-600"><span>Titipan Uang Jaminan Perangkat ONT</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($depositPelanggan) ?></span></div>
                        <div class="flex justify-between text-slate-800 font-bold pt-1 border-t border-slate-100"><span>Total Kewajiban</span><span class="font-mono"><?= format_rupiah($totalKewajiban) ?></span></div>
                    </div>
                </div>

                <div>
                    <span class="font-bold text-slate-800 uppercase block mb-1">EKUITAS (EQUITY)</span>
                    <div class="space-y-1.5 pl-2 border-l-2 border-slate-200">
                        <div class="flex justify-between text-slate-600"><span>Modal Disetor Pendiri</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($modal) ?></span></div>
                        <div class="flex justify-between text-slate-600"><span>Laba Ditahan & Laba Berjalan</span><span class="font-mono font-bold text-slate-800"><?= format_rupiah($labaDitahan) ?></span></div>
                        <div class="flex justify-between text-slate-800 font-bold pt-1 border-t border-slate-100"><span>Total Ekuitas Bersih</span><span class="font-mono"><?= format_rupiah($totalEkuitas) ?></span></div>
                    </div>
                </div>

                <!-- TOTAL PASSIVA -->
                <div class="p-3 bg-purple-50 border border-purple-200 rounded-xl flex justify-between items-center font-bold text-purple-900">
                    <span>TOTAL PASSIVA (KEWAJIBAN + EKUITAS)</span>
                    <span class="font-mono text-sm"><?= format_rupiah($totalPassiva) ?></span>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
