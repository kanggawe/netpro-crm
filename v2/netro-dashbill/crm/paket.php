<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Katalog Paket Internet Broadband";
$page_subtitle = "Konfigurasi paket internet, batas kecepatan, rasio bandwidth, dan skema tarif PPN.";
$active_menu = "m-crm";
require_once __DIR__ . '/../includes/header.php';
?>


<?php
$packages = Package::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-xs">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
        Paket internet baru berhasil ditambahkan ke katalog layanan!
    </div>
<?php elseif ($msg === 'updated'): ?>
    <div class="p-4 bg-blue-50 text-blue-800 border border-blue-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-xs">
        <i class="fa-solid fa-pen-to-square text-blue-600 text-base"></i>
        Data paket internet berhasil diperbarui!
    </div>
<?php elseif ($msg === 'deleted'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-xs">
        <i class="fa-solid fa-trash-can text-rose-600 text-base"></i>
        Paket internet berhasil dihapus dari katalog layanan.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <h3 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-box-open text-brand-600"></i> Katalog Layanan Paket Internet Resmi (<?= count($packages) ?> Paket)
            </h3>
            <p class="text-slate-400 mt-0.5">Pilihan paket langganan untuk segmen Home, SOHO, dan Dedicated Corporate.</p>
        </div>
        <button onclick="document.getElementById('modalAddPackage').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/20 flex items-center gap-2 transition-all hover:scale-[1.02] active:scale-[0.98]">
            <i class="fa-solid fa-plus text-xs"></i> + Buat Paket Baru
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if (empty($packages)): ?>
        <div class="col-span-3 bg-white p-12 rounded-3xl border border-slate-100 text-center space-y-3 shadow-sm">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <p class="text-slate-500 font-bold">Belum ada paket internet di katalog layanan.</p>
            <p class="text-slate-400 text-xs">Klik tombol "Buat Paket Baru" di atas untuk menambahkan paket.</p>
        </div>
        <?php else: ?>
        <?php foreach ($packages as $pkg): 
            $isBest = ($pkg['id'] == 2);
            $catLabel = strtoupper($pkg['category'] ?? 'HOME');
        ?>
        <div class="bg-white rounded-3xl border <?= $isBest ? 'border-2 border-brand-500 shadow-xl ring-4 ring-brand-500/10' : 'border-slate-100 shadow-sm hover:shadow-xl' ?> p-6 space-y-4 relative flex flex-col justify-between transition-all duration-300">
            <?php if ($isBest): ?>
                <span class="absolute -top-3 right-6 px-3 py-1 bg-gradient-to-r from-brand-600 to-rose-600 text-white font-black rounded-full text-[10px] shadow-md uppercase tracking-wider">
                    <i class="fa-solid fa-fire mr-1 text-[9px]"></i> BEST SELLER
                </span>
            <?php endif; ?>

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="px-3 py-1 bg-brand-50 text-brand-700 border border-brand-100 font-extrabold rounded-full text-[10px] uppercase tracking-wider">
                        <?= htmlspecialchars($catLabel) ?>
                    </span>
                    <span class="text-slate-400 font-mono text-[10px] font-bold">ID: #<?= $pkg['id'] ?></span>
                </div>

                <div>
                    <h4 class="text-lg font-black text-slate-900 leading-tight"><?= htmlspecialchars($pkg['name']) ?></h4>
                    <p class="text-slate-400 mt-1">Kecepatan dedicated broadband up to <strong class="text-slate-700"><?= $pkg['speed_mbps'] ?> Mbps</strong>.</p>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl space-y-1.5 border border-slate-100">
                    <div>
                        <span class="text-2xl font-black text-brand-600"><?= format_rupiah($pkg['price']) ?></span>
                        <span class="text-slate-400 text-xs font-semibold"> / bulan</span>
                    </div>
                    <span class="block text-[10px] text-emerald-600 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-circle-check text-[10px]"></i> Skema PPN <?= ucfirst($pkg['default_ppn_mode'] ?? 'include') ?> (11%)
                    </span>
                </div>

                <ul class="space-y-2.5 text-slate-600 font-semibold text-[11px] pt-1">
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-bolt text-brand-600 w-4 text-center"></i> 
                        <span>Kecepatan simetris hingga <strong><?= $pkg['speed_mbps'] ?> Mbps</strong></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-infinity text-blue-600 w-4 text-center"></i> 
                        <span>Unlimited Kuota Tanpa Batas FUP</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fa-solid fa-headset text-indigo-600 w-4 text-center"></i> 
                        <span>24/7 Priority Support & Monitoring NOC</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons: EDIT & DELETE -->
            <div class="pt-4 border-t border-slate-100 flex items-center gap-2 mt-4">
                <button type="button" onclick='openEditPackageModal(<?= json_encode($pkg, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="flex-1 bg-slate-100 hover:bg-brand-50 hover:text-brand-700 text-slate-700 font-bold py-2 px-3 rounded-xl transition flex items-center justify-center gap-1.5 border border-slate-200 hover:border-brand-200">
                    <i class="fa-solid fa-pen-to-square text-xs"></i> Edit Paket
                </button>
                <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus paket <?= htmlspecialchars($pkg['name']) ?> dari katalog?');">
                    <input type="hidden" name="action" value="delete_package">
                    <input type="hidden" name="id" value="<?= $pkg['id'] ?>">
                    <input type="hidden" name="redirect" value="crm/paket.php">
                    <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold py-2 px-3 rounded-xl transition border border-rose-200 flex items-center justify-center" title="Hapus Paket">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ==================== MODAL TAMBAH PAKET BARU ==================== -->
<div id="modalAddPackage" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden animate-fadeIn">
        <div class="h-16 bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 px-6 flex items-center justify-between text-white border-b border-brand-900/40">
            <h3 class="font-extrabold text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-box text-brand-400"></i> Buat Paket Internet Baru
            </h3>
            <button onclick="document.getElementById('modalAddPackage').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-xl p-1">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <input type="hidden" name="action" value="create_package">
            <input type="hidden" name="redirect" value="crm/paket.php">

            <div>
                <label class="font-bold text-slate-700 block mb-1">Nama Paket Layanan</label>
                <input type="text" name="name" required placeholder="Contoh: Gamer Ultra 150M" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kecepatan (Mbps)</label>
                    <input type="number" name="speed_mbps" required placeholder="150" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-brand-600 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kategori Segmen</label>
                    <select name="category" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="home">Home / Perumahan</option>
                        <option value="soho">SOHO / Ruko</option>
                        <option value="corporate">Corporate Dedicated</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Tarif Bulanan (Rp)</label>
                <input type="number" name="price" required placeholder="350000" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-emerald-600 focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Skema PPN Default</label>
                <select name="default_ppn_mode" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
                    <option value="include">Include PPN (Harga Bersih Termasuk Pajak 11%)</option>
                    <option value="exclude">Exclude PPN (Ditambahkan +11% pada Invoice)</option>
                </select>
            </div>
            <div class="pt-2 flex gap-2">
                <button type="button" onclick="document.getElementById('modalAddPackage').classList.add('hidden')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl transition">Batal</button>
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-2.5 rounded-xl shadow-lg shadow-brand-950/20 transition">Simpan Paket</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== MODAL EDIT PAKET ==================== -->
<div id="modalEditPackage" class="fixed inset-0 bg-slate-950/70 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-md overflow-hidden animate-fadeIn">
        <div class="h-16 bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 px-6 flex items-center justify-between text-white border-b border-brand-900/40">
            <h3 class="font-extrabold text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-brand-400"></i> Edit Konfigurasi Paket Internet
            </h3>
            <button onclick="document.getElementById('modalEditPackage').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-xl p-1">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <input type="hidden" name="action" value="update_package">
            <input type="hidden" name="id" id="editPkgId" value="">
            <input type="hidden" name="redirect" value="crm/paket.php">

            <div>
                <label class="font-bold text-slate-700 block mb-1">Nama Paket Layanan</label>
                <input type="text" name="name" id="editPkgName" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kecepatan (Mbps)</label>
                    <input type="number" name="speed_mbps" id="editPkgSpeed" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-brand-600 focus:ring-2 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1">Kategori Segmen</label>
                    <select name="category" id="editPkgCategory" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
                        <option value="home">Home / Perumahan</option>
                        <option value="soho">SOHO / Ruko</option>
                        <option value="corporate">Corporate Dedicated</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Tarif Bulanan (Rp)</label>
                <input type="number" name="price" id="editPkgPrice" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-emerald-600 focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div>
                <label class="font-bold text-slate-700 block mb-1">Skema PPN Default</label>
                <select name="default_ppn_mode" id="editPkgPpn" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold focus:ring-2 focus:ring-brand-500 outline-none">
                    <option value="include">Include PPN (Harga Bersih Termasuk Pajak 11%)</option>
                    <option value="exclude">Exclude PPN (Ditambahkan +11% pada Invoice)</option>
                </select>
            </div>
            <div class="pt-2 flex gap-2">
                <button type="button" onclick="document.getElementById('modalEditPackage').classList.add('hidden')" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl transition">Batal</button>
                <button type="submit" class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-2.5 rounded-xl shadow-lg shadow-brand-950/20 transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPackageModal(pkg) {
    if (!pkg) return;
    document.getElementById('editPkgId').value = pkg.id || '';
    document.getElementById('editPkgName').value = pkg.name || '';
    document.getElementById('editPkgSpeed').value = pkg.speed_mbps || '';
    document.getElementById('editPkgPrice').value = pkg.price || '';
    document.getElementById('editPkgCategory').value = (pkg.category || 'home').toLowerCase();
    document.getElementById('editPkgPpn').value = (pkg.default_ppn_mode || 'include').toLowerCase();

    document.getElementById('modalEditPackage').classList.remove('hidden');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

