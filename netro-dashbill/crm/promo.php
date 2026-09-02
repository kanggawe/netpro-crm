<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Promo & Kupon Diskon Langganan";
$page_subtitle = "Manajemen kode promo pemasangan gratis, potongan tagihan, dan voucher cashback.";
$active_menu = "m-crm";
require_once __DIR__ . '/../includes/header.php';
?>


<?php
$promos = Promo::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Kode promo diskon baru berhasil diterbitkan!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Kode Promo & Diskon Aktif (<?= count($promos) ?> Voucher)</h3>
                <p class="text-slate-400">Kode promo yang dapat diaplikasikan saat registrasi pelanggan.</p>
            </div>
            <button onclick="document.getElementById('modalAddPromo').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2 rounded-xl shadow-lg shadow-brand-950/30 flex items-center gap-1.5 transition">
                <i class="fa-solid fa-tag text-xs"></i> + Buat Kode Promo
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Kode Promo</th>
                        <th class="py-3 px-4">Judul Campaign</th>
                        <th class="py-3 px-4">Nilai Diskon</th>
                        <th class="py-3 px-4">Kuota Maksimal</th>
                        <th class="py-3 px-4">Masa Berlaku</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($promos as $pro): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-indigo-600 text-sm"><?= htmlspecialchars($pro['code']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($pro['title']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-emerald-600"><?= format_rupiah($pro['discount_amount']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-700"><?= $pro['quota'] ?> Kuota</td>
                        <td class="py-3.5 px-4 text-slate-500"><?= htmlspecialchars($pro['valid_until']) ?></td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]"><?= htmlspecialchars($pro['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Promo -->
<div id="modalAddPromo" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-tag text-blue-400"></i> Buat Kode Promo Baru
            </h3>
            <button onclick="document.getElementById('modalAddPromo').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_promo">
            <input type="hidden" name="redirect" value="crm/promo.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Kode Voucher / Promo</label>
                <input type="text" name="code" required placeholder="Contoh: MERDEKA50" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold uppercase text-indigo-600">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Campaign</label>
                <input type="text" name="title" required placeholder="Promo Diskon 50% Bulan Pertama" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Nilai Potongan (Rp)</label>
                    <input type="number" name="discount_amount" required placeholder="50000" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-blue-600">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kuota Kupon</label>
                    <input type="number" name="quota" value="100" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                </div>
            </div>
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-2.5 rounded-xl shadow-lg shadow-brand-950/30 transition">Terbitkan Kode Promo</button>
        </form>
    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
