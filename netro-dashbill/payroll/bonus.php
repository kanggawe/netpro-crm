<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Insentif Teknisi Pasang & Komisi Sales";
$page_subtitle = "Hitungan bonus per-titik aktivasi pelanggan baru FTTH dan komisi sales agen.";
$active_menu = "m-payroll";
require_once __DIR__ . '/../includes/header.php';

$claims = BonusClaim::all();
$totalBonus = 0;
foreach ($claims as $cl) {
    $totalBonus += $cl['total_amount'];
}
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'bonus_approved'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Klaim insentif berhasil disetujui & status diubah menjadi DICAIRKAN (PAYROLL)!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <!-- Top 3 Incentive Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Tarif Insentif Pasang Baru</span>
            <strong class="text-2xl font-bold text-blue-600">Rp 50.000</strong>
            <span class="text-slate-400 block">Per titik instalasi FTTH terverifikasi BAST</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Total Klaim Bulan Ini</span>
            <strong class="text-2xl font-bold text-emerald-600"><?= count($claims) ?> Klaim Staf</strong>
            <span class="text-emerald-600 font-medium block">✓ Seluruhnya Lulus Uji Redaman OPM</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Total Alokasi Bonus Dicairkan</span>
            <strong class="text-2xl font-bold text-purple-600"><?= format_rupiah($totalBonus) ?></strong>
            <span class="text-purple-600 font-medium block">Otomatis Masuk ke Slip Gaji</span>
        </div>
    </div>

    <!-- Incentive Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Rincian Perolehan Insentif & Komisi Aktivasi Juni 2026</h3>
                <p class="text-slate-400">Verifikasi jumlah instalasi sukses berdasarkan Berita Acara Serah Terima (BAST).</p>
            </div>
            <button onclick="triggerToast('Bonus Approved', 'Seluruh perolehan bonus disetujui untuk pencairan.')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg shadow">
                ✓ Approve Semua Bonus
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Nama Staf</th>
                        <th class="py-3 px-4">Peran / Divisi</th>
                        <th class="py-3 px-4">No. BAST</th>
                        <th class="py-3 px-4 font-mono text-center">Jumlah Poin</th>
                        <th class="py-3 px-4 font-mono text-right">Tarif / Titik</th>
                        <th class="py-3 px-4 font-mono text-right">Total Insentif (Rp)</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($claims as $cl): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($cl['employee_name']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($cl['role']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-blue-600"><?= htmlspecialchars($cl['bast_no'] ?? '-') ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-center text-blue-600"><?= $cl['points'] ?> Titik</td>
                        <td class="py-3.5 px-4 font-mono text-slate-600 text-right"><?= format_rupiah($cl['rate']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-600 text-right"><?= format_rupiah($cl['total_amount']) ?></td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 <?= strpos($cl['status'], 'DICAIRKAN') !== false ? 'bg-purple-50 text-purple-700' : 'bg-emerald-50 text-emerald-700' ?> font-bold rounded-full text-[10px]"><?= htmlspecialchars($cl['status']) ?></span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <?php if (strpos($cl['status'], 'DICAIRKAN') === false): ?>
                            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline">
                                <input type="hidden" name="action" value="approve_bonus_claim">
                                <input type="hidden" name="id" value="<?= $cl['id'] ?>">
                                <input type="hidden" name="redirect" value="payroll/bonus.php">
                                <button type="submit" class="text-emerald-600 font-bold hover:underline">Cairkan</button>
                            </form>
                            <?php else: ?>
                            <span class="text-slate-400 font-semibold">Tuntas</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
