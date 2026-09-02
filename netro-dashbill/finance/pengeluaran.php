<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Pengeluaran Operasional (OPEX)";
$page_subtitle = "Rekapitulasi biaya sewa upstream bandwidth, listrik POP, BBM teknisi, dan maintenance jaringan.";
$active_menu = "m-finance";
require_once __DIR__ . '/../includes/header.php';

// Dynamic Database Fetch
$opexList = OpexExpense::all();
$msg = $_GET['msg'] ?? '';

$totalOpex = 0;
$upstreamOpex = 0;
$fieldOpex = 0;
foreach ($opexList as $op) {
    $totalOpex += $op['amount'];
    if (str_contains($op['category'], 'Upstream') || str_contains($op['category'], 'Tiang')) {
        $upstreamOpex += $op['amount'];
    }
    if (str_contains($op['category'], 'BBM') || str_contains($op['category'], 'Teknisi')) {
        $fieldOpex += $op['amount'];
    }
}
?>

<?php if ($msg === 'created_opex'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Beban pengeluaran OPEX baru berhasil dicatat dan dipotong dari kas perusahaan!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <!-- 3 Top Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Total OPEX Bulan Ini</span>
                <strong class="text-2xl font-bold text-rose-600"><?= format_rupiah($totalOpex) ?></strong>
                <span class="text-slate-400 block mt-0.5"><?= count($opexList) ?> Transaksi Beban</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg"><i class="fa-solid fa-receipt"></i></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Beban Upstream & Core</span>
                <strong class="text-2xl font-bold text-blue-600"><?= format_rupiah($upstreamOpex) ?></strong>
                <span class="text-slate-400 block mt-0.5">IP Transit & Tiang Tumpu</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="fa-solid fa-network-wired"></i></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Beban BBM & Lapangan</span>
                <strong class="text-2xl font-bold text-amber-600"><?= format_rupiah($fieldOpex) ?></strong>
                <span class="text-slate-400 block mt-0.5">Armada Teknisi Lapangan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg"><i class="fa-solid fa-gas-pump"></i></div>
        </div>
    </div>

    <!-- OPEX Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Pengeluaran Beban Operasional ISP (OPEX)</h3>
                <p class="text-slate-400">Pencatatan pengeluaran tersinkronisasi otomatis dengan arus kas dan buku besar.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="document.getElementById('modalAddOpex').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-plus text-xs"></i> + Catat Pengeluaran
                </button>
                <a href="cetak_pengeluaran.php" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-3.5 py-2 rounded-lg shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i> Cetak / Export PDF
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">No Voucher</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Kategori Beban</th>
                        <th class="py-3 px-4">Penerima / Vendor</th>
                        <th class="py-3 px-4">Keterangan Pengeluaran</th>
                        <th class="py-3 px-4 font-mono text-right">Nominal</th>
                        <th class="py-3 px-4">Akun Pembayaran</th>
                        <th class="py-3 px-4">Otorisator</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($opexList)): ?>
                    <tr><td colspan="10" class="py-6 text-center text-slate-400">Belum ada catatan pengeluaran OPEX di database.</td></tr>
                    <?php else: ?>
                    <?php foreach ($opexList as $op): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($op['voucher_no']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-500"><?= htmlspecialchars($op['exp_date']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($op['category']) ?></td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium"><?= htmlspecialchars($op['vendor_name']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($op['description']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-rose-600 text-right"><?= format_rupiah($op['amount']) ?></td>
                        <td class="py-3.5 px-4 font-semibold text-blue-600"><?= htmlspecialchars($op['bank_account']) ?></td>
                        <td class="py-3.5 px-4 font-semibold text-slate-700"><?= htmlspecialchars($op['approver']) ?></td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]"><?= htmlspecialchars($op['status']) ?></span></td>
                        <td class="py-3.5 px-4 text-right">
                            <button onclick="triggerToast('Download Nota', 'Mengunduh Nota PDF <?= htmlspecialchars($op['voucher_no']) ?>...')" class="text-blue-600 font-bold hover:underline">
                                📄 Nota.pdf
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Catat OPEX -->
<div id="modalAddOpex" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-receipt text-rose-400"></i> Catat Beban Pengeluaran (OPEX)
            </h3>
            <button onclick="document.getElementById('modalAddOpex').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_opex">
            <input type="hidden" name="redirect" value="finance/pengeluaran.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Kategori Beban</label>
                <select name="category" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option>Sewa Bandwidth Upstream</option>
                    <option>Sewa Tiang & Right of Way</option>
                    <option>Listrik POP & Server Room</option>
                    <option>BBM & Transport Teknisi</option>
                    <option>Lisensi Software & Cloud</option>
                    <option>Pemasaran & Iklan</option>
                    <option>Pemeliharaan & Tooling</option>
                </select>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Penerima Dana / Nama Vendor</label>
                <input type="text" name="vendor_name" required placeholder="Contoh: PT Telkom Indonesia" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Akun Pembayaran</label>
                    <select name="bank_account" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                        <option>Bank BCA Bisnis</option>
                        <option>Bank Mandiri</option>
                        <option>Kas Tunai Kantor</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Jumlah Nominal (Rp)</label>
                    <input type="number" name="amount" required placeholder="500000" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-rose-600 font-mono">
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Uraian / Deskripsi Pengeluaran</label>
                <input type="text" name="description" required placeholder="Keterangan pengeluaran..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Pemberi Otorisasi (Approver)</label>
                <input type="text" name="approver" value="Manager Finance" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
            </div>
            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 rounded-xl shadow transition">Simpan & Ajukan Pengeluaran</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
