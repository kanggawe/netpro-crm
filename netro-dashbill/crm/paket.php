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
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Paket internet baru berhasil ditambahkan ke katalog layanan!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="font-bold text-slate-900 text-sm">Katalog Layanan Paket Internet Resmi (<?= count($packages) ?> Paket)</h3>
            <p class="text-slate-400">Pilihan paket langganan untuk segmen Home, SOHO, dan Dedicated Corporate.</p>
        </div>
        <button onclick="document.getElementById('modalAddPackage').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-plus text-xs"></i> + Buat Paket Baru
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if (empty($packages)): ?>
        <div class="col-span-3 bg-white p-8 rounded-2xl border border-slate-200 text-center text-slate-400 font-medium">
            Belum ada paket internet di katalog layanan.
        </div>
        <?php else: ?>
        <?php foreach ($packages as $pkg): ?>
        <div class="bg-white rounded-2xl border <?= ($pkg['id'] == 2) ? 'border-2 border-blue-500 shadow-lg' : 'border-slate-200 shadow-sm' ?> p-6 space-y-4 relative">
            <?php if ($pkg['id'] == 2): ?>
                <span class="absolute -top-3 right-6 px-3 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-full text-[10px] shadow">BEST SELLER</span>
            <?php endif; ?>
            <span class="px-3 py-1 bg-blue-50 text-blue-700 font-bold rounded-full text-[10px] uppercase"><?= htmlspecialchars($pkg['category'] ?? 'HOME') ?></span>
            <div>
                <h4 class="text-lg font-bold text-slate-900"><?= htmlspecialchars($pkg['name']) ?></h4>
                <p class="text-slate-400">Kecepatan dedicated broadband up to <?= $pkg['speed_mbps'] ?> Mbps.</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl space-y-1">
                <span class="text-2xl font-extrabold text-blue-600"><?= format_rupiah($pkg['price']) ?></span><span class="text-slate-400"> / bulan</span>
                <span class="block text-[10px] text-emerald-600 font-semibold">
                    ✓ Skema PPN <?= ucfirst($pkg['default_ppn_mode'] ?? 'include') ?> PPN (11%)
                </span>
            </div>
            <ul class="space-y-2 text-slate-600 font-medium">
                <li><i class="fa-solid fa-bolt text-blue-500 mr-2"></i> Kecepatan hingga <?= $pkg['speed_mbps'] ?> Mbps</li>
                <li><i class="fa-solid fa-infinity text-blue-500 mr-2"></i> Unlimited Tanpa Batas FUP</li>
                <li><i class="fa-solid fa-headset text-blue-500 mr-2"></i> 24/7 Priority Support NOC</li>
            </ul>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Tambah Paket Baru -->
<div id="modalAddPackage" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-box text-blue-400"></i> Buat Paket Internet Baru
            </h3>
            <button onclick="document.getElementById('modalAddPackage').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_package">
            <input type="hidden" name="redirect" value="crm/paket.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Paket</label>
                <input type="text" name="name" required placeholder="Contoh: Gamer Ultra 150M" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kecepatan (Mbps)</label>
                    <input type="number" name="speed_mbps" required placeholder="150" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-blue-600">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kategori</label>
                    <select name="category" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                        <option value="home">Home / Perumahan</option>
                        <option value="soho">SOHO / Ruko</option>
                        <option value="corporate">Corporate Dedicated</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Harga Bulanan (Rp)</label>
                <input type="number" name="price" required placeholder="350000" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-emerald-600">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Skema PPN Default</label>
                <select name="default_ppn_mode" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option value="include">Include PPN (Harga Bersih Termasuk Pajak)</option>
                    <option value="exclude">Exclude PPN (Ditambahkan +11% pada Invoice)</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-2.5 rounded-xl shadow-lg shadow-brand-950/30 transition">Simpan Paket Baru</button>
        </form>
    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
