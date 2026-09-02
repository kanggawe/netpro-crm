<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Data & Kategori Barang Gudang";
$page_subtitle = "Katalog stok material optik, ONT, SFP transceiver, Drop Cable, ODB.";
$active_menu = "m-inventory";
require_once __DIR__ . '/../includes/header.php';

$items = Inventory::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'inventory_restocked'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Stok barang berhasil diperbarui di database gudang!
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
    <!-- Left 2/3 Table -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Master Stok Material Gudang (<?= count($items) ?> SKU)</h3>
                <p class="text-slate-400">Pemantauan kuantitas persediaan perangkat keras & kabel optik.</p>
            </div>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                    <th class="py-3 px-4">SKU</th>
                    <th class="py-3 px-4">Nama Barang</th>
                    <th class="py-3 px-4">Stok Gudang</th>
                    <th class="py-3 px-4">Satuan</th>
                    <th class="py-3 px-4">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                    <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($it['sku']) ?></td>
                    <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($it['name']) ?></td>
                    <td class="py-3.5 px-4 font-mono font-bold text-slate-900 text-sm"><?= $it['stock'] ?></td>
                    <td class="py-3.5 px-4 text-slate-500"><?= htmlspecialchars($it['unit']) ?></td>
                    <td class="py-3.5 px-4">
                        <?php if ($it['status'] === 'AMAN'): ?>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]">AMAN</span>
                        <?php else: ?>
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 font-bold rounded-full text-[10px]">MENIPIS</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Right 1/3 Restock Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
            <i class="fa-solid fa-box text-orange-500"></i> Restock Material Gudang
        </h3>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-3">
            <input type="hidden" name="action" value="restock_inventory">
            <input type="hidden" name="redirect" value="inventory/barang.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Pilih Barang</label>
                <select name="id" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <?php foreach ($items as $it): ?>
                        <option value="<?= $it['id'] ?>"><?= htmlspecialchars($it['name']) ?> (Stok: <?= $it['stock'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Jumlah Tambahan (Qty)</label>
                <input type="number" name="qty" value="10" min="1" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-blue-600">
            </div>

            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2.5 rounded-xl shadow transition">
                + Tambahkan Stok Masuk
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
