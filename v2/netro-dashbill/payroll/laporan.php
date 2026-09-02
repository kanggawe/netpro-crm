<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Rekapitulasi Gaji, BPJS & PPh 21 Bulanan";
$page_subtitle = "Konsolidasi total beban pengupahan, setoran iuran jaminan sosial, dan pajak penghasilan staf.";
$active_menu = "m-payroll";
require_once __DIR__ . '/../includes/header.php';
?>

<?php
$records = PayrollRecord::all();
$basic = 0; $allowanceBonus = 0; $bpjsTk = 0; $bpjsKes = 0; $pph21 = 0; $thp = 0;
foreach ($records as $r) {
    $basic += floatval($r['basic_salary'] ?? 0);
    $allowanceBonus += floatval($r['allowance'] ?? 0) + floatval($r['bonus'] ?? 0);
    $ded = floatval($r['deductions'] ?? 0);
    $bpjsTk += $ded * 0.4;
    $bpjsKes += $ded * 0.2;
    $pph21 += $ded * 0.4;
    $thp += floatval($r['thp'] ?? 0);
}
?>

<div class="space-y-6 text-xs max-w-4xl mx-auto">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-scale-balanced text-emerald-600"></i> Rekapitulasi Alokasi Beban Payroll & Pajak
                </h3>
                <p class="text-slate-400">Periode Penggajian: <strong><?= date('F Y') ?></strong></p>
            </div>
            <a href="cetak_rekap.php" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-3.5 py-1.5 rounded-lg shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak / Export PDF
            </a>
        </div>

        <div class="space-y-3">
            <div class="p-3.5 bg-slate-50 rounded-xl flex justify-between items-center">
                <span class="text-slate-700 font-medium">1. Beban Gaji Pokok & Tunjangan Tetap</span>
                <strong class="font-mono text-slate-900 text-sm"><?= format_rupiah($basic) ?></strong>
            </div>

            <div class="p-3.5 bg-slate-50 rounded-xl flex justify-between items-center">
                <span class="text-slate-700 font-medium">2. Tunjangan Shift Malam NOC & Insentif Pasang Baru</span>
                <strong class="font-mono text-slate-900 text-sm"><?= format_rupiah($allowanceBonus) ?></strong>
            </div>

            <div class="p-3.5 bg-slate-50 rounded-xl flex justify-between items-center">
                <span class="text-slate-700 font-medium">3. Total Iuran BPJS Ketenagakerjaan (JKK, JKM, JHT, JP)</span>
                <strong class="font-mono text-rose-600 text-sm"><?= format_rupiah($bpjsTk) ?></strong>
            </div>

            <div class="p-3.5 bg-slate-50 rounded-xl flex justify-between items-center">
                <span class="text-slate-700 font-medium">4. Total Iuran BPJS Kesehatan (5.0%)</span>
                <strong class="font-mono text-rose-600 text-sm"><?= format_rupiah($bpjsKes) ?></strong>
            </div>

            <div class="p-3.5 bg-slate-50 rounded-xl flex justify-between items-center">
                <span class="text-slate-700 font-medium">5. Setoran Pajak PPh Pasal 21 Masa (DJP)</span>
                <strong class="font-mono text-indigo-600 text-sm"><?= format_rupiah($pph21) ?></strong>
            </div>

            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex justify-between items-center font-bold text-emerald-950">
                <span class="text-sm">TOTAL PENGELUARAN KAS PAYROLL (TAKE HOME PAY)</span>
                <span class="font-mono text-lg text-emerald-700"><?= format_rupiah($thp) ?></span>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
