<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Kalkulasi Batch Payroll & Slip Gaji Digital";
$page_subtitle = "Proses persetujuan batch penggajian bulanan dan penerbitan slip gaji resmi.";
$active_menu = "m-payroll";
require_once __DIR__ . '/../includes/header.php';

$records = PayrollRecord::all();
$totalEmployees = count($records);
$totalGross = 0;
$totalDeductions = 0;
$totalThp = 0;

foreach ($records as $r) {
    $totalGross += ($r['basic_salary'] + $r['allowance'] + $r['bonus']);
    $totalDeductions += $r['deductions'];
    $totalThp += $r['thp'];
}

$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'payroll_processed'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-4xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Batch Payroll periode Juni 2026 berhasil diproses ke <?= $_GET['count'] ?? '3' ?> rekening karyawan!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-4xl mx-auto">
    <!-- Top Banner Overview (RedDash Style) -->
    <div class="bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 text-white p-7 rounded-3xl shadow-xl border border-brand-900/40 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative overflow-hidden">
        <div class="absolute top-0 left-1/4 w-32 h-32 bg-brand-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full font-bold text-[10px]">
                    ● STATUS: APPROVED (SIAP DITRANSFER)
                </span>
                <span class="text-brand-300/80 font-mono">Periode: <strong>Juni 2026</strong></span>
            </div>
            <h3 class="font-extrabold text-lg text-white">Batch Payroll Penggajian Karyawan & Teknisi</h3>
            <p class="text-slate-300 text-xs mt-0.5">Total <?= $totalEmployees ?> Staf telah dihitung otomatis dengan potongan BPJS dan PPh 21 TER.</p>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="relative z-10">
            <input type="hidden" name="action" value="process_payroll_batch">
            <input type="hidden" name="period" value="Juni 2026">
            <input type="hidden" name="redirect" value="payroll/generate.php">
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-5 py-2.5 rounded-xl shadow-lg shadow-brand-950/50 border border-brand-500/30 transition flex items-center gap-2">
                <i class="fa-solid fa-money-check-dollar"></i> Eksekusi Payroll Batch
            </button>
        </form>
    </div>

    <!-- Summary Box -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Total Penghasilan Bruto</span>
            <strong class="text-2xl font-bold text-slate-900"><?= format_rupiah($totalGross) ?></strong>
            <span class="text-slate-400 block">Gaji Pokok + Tunjangan + Insentif</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Total Potongan (BPJS & Pajak)</span>
            <strong class="text-2xl font-bold text-rose-600">(<?= format_rupiah($totalDeductions) ?>)</strong>
            <span class="text-rose-600 block font-medium">Iuran BPJS TK, Kes & PPh 21</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1 bg-gradient-to-br from-emerald-50 to-white">
            <span class="text-emerald-800 font-semibold uppercase text-[10px] block">Total Take Home Pay (THP)</span>
            <strong class="text-2xl font-bold text-emerald-700"><?= format_rupiah($totalThp) ?></strong>
            <span class="text-emerald-700 block font-bold">Dana Bersih Ditransfer</span>
        </div>
    </div>

    <!-- Employee Salary Slips List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Slip Gaji Digital Staf Periode Juni 2026</h3>
            <span class="text-slate-400 font-semibold"><?= $totalEmployees ?> Record Payroll</span>
        </div>

        <div class="space-y-3">
            <?php foreach ($records as $r): ?>
            <div class="p-4 rounded-xl border border-slate-200 hover:border-blue-400 transition bg-slate-50/50 space-y-2">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                    <div>
                        <strong class="text-slate-900 font-bold text-sm"><?= htmlspecialchars($r['employee_name']) ?></strong>
                        <span class="text-slate-400 block text-[11px]"><?= htmlspecialchars($r['bank_name'] ?? 'Bank Mandiri') ?>: <?= htmlspecialchars($r['account_no'] ?? '124-000-9981') ?></span>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 text-[10px] block">Gaji Bersih (THP):</span>
                        <strong class="text-emerald-600 font-mono text-base font-bold"><?= format_rupiah($r['thp']) ?></strong>
                    </div>
                </div>
                <div class="flex flex-wrap gap-x-4 gap-y-1 text-slate-600 pt-1 border-t border-slate-200/60 text-[11px]">
                    <span>Gaji Pokok: <strong><?= format_rupiah($r['basic_salary']) ?></strong></span>
                    <span>Tunjangan: <strong><?= format_rupiah($r['allowance']) ?></strong></span>
                    <span>Insentif/Bonus: <strong><?= format_rupiah($r['bonus']) ?></strong></span>
                    <span class="text-rose-600">Potongan BPJS & Pajak: <strong>(<?= format_rupiah($r['deductions']) ?>)</strong></span>
                </div>
                <div class="pt-2 flex justify-end">
                    <a href="cetak_slip.php?id=<?= $r['id'] ?? 1 ?>" target="_blank" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-3.5 py-1.5 rounded-lg flex items-center gap-1.5 shadow transition">
                        <i class="fa-solid fa-file-pdf text-rose-400"></i> Cetak / Unduh Slip Gaji PDF
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
