<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Generator & Manajemen Voucher Hotspot";
$page_subtitle = "Cetak batch voucher internet, masa aktif jam/hari, kuota, dan harga jual mitra.";
$active_menu = "m-radius";
require_once __DIR__ . '/../includes/header.php';
?>


<?php
$vouchers = RadiusVoucher::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Batch voucher hotspot baru berhasil di-generate dan siap dicetak!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Generator Batch Voucher Hotspot (<?= count($vouchers) ?> Batch)</h3>
                <p class="text-slate-400">Generate tiket login voucher acak dengan template cetak thermal / PDF.</p>
            </div>
            <button onclick="document.getElementById('modalGenVoucher').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-ticket"></i> + Generate Batch Voucher
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Batch ID</th>
                        <th class="py-3 px-4">Paket Voucher</th>
                        <th class="py-3 px-4">Durasi Aktif</th>
                        <th class="py-3 px-4">Jumlah Voucher</th>
                        <th class="py-3 px-4">Tarif Satuan</th>
                        <th class="py-3 px-4">Status Cetak</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vouchers as $v): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($v['batch_code']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($v['plan_name']) ?></td>
                        <td class="py-3.5 px-4 font-medium text-slate-600"><?= htmlspecialchars($v['duration']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-indigo-600"><?= $v['qty'] ?> Voucher</td>
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900"><?= format_rupiah($v['price']) ?></td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]"><?= htmlspecialchars($v['status']) ?></span></td>
                        <td class="py-3.5 px-4 text-right"><button onclick="window.print()" class="text-blue-600 font-bold hover:underline">🖨️ Cetak</button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Generate Voucher -->
<div id="modalGenVoucher" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-ticket text-blue-400"></i> Generate Batch Voucher
            </h3>
            <button onclick="document.getElementById('modalGenVoucher').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_voucher">
            <input type="hidden" name="redirect" value="radius/vouchers.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Paket Voucher</label>
                <input type="text" name="plan_name" required placeholder="Contoh: Hotspot 2 Jam Unlimited" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Durasi</label>
                    <input type="text" name="duration" value="2 Jam / 5 Mbps" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Jumlah Voucher (Qty)</label>
                    <input type="number" name="qty" value="100" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Tarif Satuan (Rp)</label>
                <input type="number" name="price" value="3000" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-blue-600">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">Generate Voucher Sekarang</button>
        </form>
    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
