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
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-xs">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
        Kode promo diskon baru berhasil diterbitkan!
    </div>
<?php elseif ($msg === 'updated'): ?>
    <div class="p-4 bg-blue-50 text-blue-800 border border-blue-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-xs">
        <i class="fa-solid fa-pen-to-square text-blue-600 text-base"></i>
        Data kode promo berhasil diperbarui!
    </div>
<?php elseif ($msg === 'deleted'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-xs">
        <i class="fa-solid fa-trash-can text-rose-600 text-base"></i>
        Kode promo berhasil dihapus dari sistem!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-tags text-brand-600"></i> Daftar Kode Promo & Diskon Aktif (<?= count($promos) ?> Voucher)
                </h3>
                <p class="text-slate-400 mt-0.5">Kode promo yang dapat diaplikasikan saat registrasi pelanggan.</p>
            </div>
            <button onclick="document.getElementById('modalAddPromo').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/20 flex items-center gap-1.5 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i class="fa-solid fa-plus text-xs"></i> + Buat Kode Promo
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-bold text-[11px]">
                        <th class="py-3 px-4">Kode Promo</th>
                        <th class="py-3 px-4">Judul Campaign</th>
                        <th class="py-3 px-4">Nilai Diskon</th>
                        <th class="py-3 px-4">Kuota Maksimal</th>
                        <th class="py-3 px-4">Masa Berlaku</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($promos)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data kode promo diskon.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($promos as $pro): ?>
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="py-3.5 px-4 font-mono font-black text-brand-600 text-sm"><?= htmlspecialchars($pro['code']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($pro['title']) ?></td>
                        <td class="py-3.5 px-4 font-black text-emerald-600"><?= format_rupiah($pro['discount_amount']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-700 font-bold"><?= $pro['quota'] ?> Kuota</td>
                        <td class="py-3.5 px-4 text-slate-500 font-medium"><?= htmlspecialchars($pro['valid_until']) ?></td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-extrabold rounded-full text-[10px]">
                                <?= htmlspecialchars($pro['status']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick='openEditPromoModal(<?= json_encode($pro, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="p-2 rounded-xl bg-slate-100 hover:bg-brand-50 hover:text-brand-600 text-slate-600 border border-slate-200 transition" title="Edit Promo">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus promo <?= htmlspecialchars($pro['code']) ?>?');">
                                    <input type="hidden" name="action" value="delete_promo">
                                    <input type="hidden" name="id" value="<?= $pro['id'] ?>">
                                    <input type="hidden" name="redirect" value="crm/promo.php">
                                    <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 transition" title="Hapus Promo">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== MODAL TAMBAH PROMO ==================== -->
<div id="modalAddPromo" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden animate-fadeIn">
        <div class="h-16 bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 px-6 flex items-center justify-between text-white border-b border-brand-900/40">
            <h3 class="font-extrabold text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-tag text-brand-400"></i> Terbitkan Kode Promo Baru
            </h3>
            <button onclick="document.getElementById('modalAddPromo').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-xl p-1">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <input type="hidden" name="action" value="create_promo">
            <input type="hidden" name="redirect" value="crm/promo.php">

            <div>
                <label class="font-bold text-slate-700 block mb-1">Kode Voucher / Promo</label>
                <input type="text" name="code" required placeholder="Contoh: MERDEKA50" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-mono font-black uppercase text-brand-600 focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Nama Campaign Promo</label>
                <input type="text" name="title" required placeholder="Promo Diskon 50% Bulan Pertama" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nilai Potongan (Rp)</label>
                    <input type="number" name="discount_amount" required placeholder="50000" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-emerald-600 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kuota Kupon</label>
                    <input type="number" name="quota" value="100" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Masa Berlaku Hingga</label>
                <input type="date" name="valid_until" value="<?= date('Y-12-31') ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div class="pt-2 flex gap-2">
                <button type="button" onclick="document.getElementById('modalAddPromo').classList.add('hidden')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl transition">Batal</button>
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-2.5 rounded-xl shadow-lg shadow-brand-950/20 transition">Terbitkan Promo</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== MODAL EDIT PROMO ==================== -->
<div id="modalEditPromo" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden animate-fadeIn">
        <div class="h-16 bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 px-6 flex items-center justify-between text-white border-b border-brand-900/40">
            <h3 class="font-extrabold text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-brand-400"></i> Edit Kode Promo & Diskon
            </h3>
            <button onclick="document.getElementById('modalEditPromo').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-xl p-1">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <input type="hidden" name="action" value="update_promo">
            <input type="hidden" name="id" id="editPromoId" value="">
            <input type="hidden" name="redirect" value="crm/promo.php">

            <div>
                <label class="font-bold text-slate-700 block mb-1">Kode Voucher / Promo</label>
                <input type="text" name="code" id="editPromoCode" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-mono font-black uppercase text-brand-600 focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Nama Campaign Promo</label>
                <input type="text" name="title" id="editPromoTitle" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Nilai Potongan (Rp)</label>
                    <input type="number" name="discount_amount" id="editPromoDiscount" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-emerald-600 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kuota Kupon</label>
                    <input type="number" name="quota" id="editPromoQuota" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Masa Berlaku Hingga</label>
                    <input type="date" name="valid_until" id="editPromoValid" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Status Voucher</label>
                    <select name="status" id="editPromoStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="AKTIF">AKTIF</option>
                        <option value="EXPIRED">EXPIRED</option>
                        <option value="HABIS">HABIS</option>
                    </select>
                </div>
            </div>
            <div class="pt-2 flex gap-2">
                <button type="button" onclick="document.getElementById('modalEditPromo').classList.add('hidden')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl transition">Batal</button>
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-2.5 rounded-xl shadow-lg shadow-brand-950/20 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPromoModal(pro) {
    if (!pro) return;
    document.getElementById('editPromoId').value = pro.id || '';
    document.getElementById('editPromoCode').value = pro.code || '';
    document.getElementById('editPromoTitle').value = pro.title || '';
    document.getElementById('editPromoDiscount').value = pro.discount_amount || '';
    document.getElementById('editPromoQuota').value = pro.quota || 100;
    document.getElementById('editPromoValid').value = pro.valid_until || '';
    document.getElementById('editPromoStatus').value = pro.status || 'AKTIF';
    document.getElementById('modalEditPromo').classList.remove('hidden');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
