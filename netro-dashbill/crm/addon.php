<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Add-on & Layanan Tambahan";
$page_subtitle = "Katalog penambahan fitur IP Publik Statis, Mesh WiFi Router, CCTV Cloud, dan Booster.";
$active_menu = "m-crm";
require_once __DIR__ . '/../includes/header.php';
?>


<?php
$addons = Addon::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Layanan Add-on baru berhasil ditambahkan ke database!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="font-bold text-slate-900 text-sm">Daftar Add-on & Layanan Opsional (<?= count($addons) ?> Layanan)</h3>
            <p class="text-slate-400">Layanan bernilai tambah untuk meningkatkan ARPU pelanggan.</p>
        </div>
        <button onclick="document.getElementById('modalAddAddon').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-puzzle-piece text-xs"></i> + Tambah Add-on Baru
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($addons as $ad): ?>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <span class="px-2.5 py-1 bg-cyan-50 text-cyan-700 font-bold rounded-full text-[10px]"><?= htmlspecialchars($ad['category']) ?></span>
            <h4 class="text-base font-bold text-slate-900"><?= htmlspecialchars($ad['name']) ?></h4>
            <p class="text-slate-500"><?= htmlspecialchars($ad['description']) ?></p>
            <p class="text-lg font-bold text-blue-600"><?= format_rupiah($ad['price']) ?> <span class="text-xs text-slate-400 font-normal">/ bln</span></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Tambah Addon -->
<div id="modalAddAddon" class="fixed inset-0 bg-slate-950/65 z-[9999] flex items-center justify-center p-4 hidden backdrop-blur-md transition-all duration-300">
    <div class="bg-white rounded-3xl shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)] border border-slate-100 w-full max-w-md overflow-hidden transform transition-all">
        
        <!-- Premium Header with Soft Accent Pill -->
        <div class="p-6 pb-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-b from-slate-50/80 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-puzzle-piece"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm tracking-tight">Tambah Add-on Baru</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Buat paket layanan nilai tambah baru untuk pelanggan</p>
                </div>
            </div>
            <button onclick="document.getElementById('modalAddAddon').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 transition flex items-center justify-center text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Form Body -->
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <input type="hidden" name="action" value="create_addon">
            <input type="hidden" name="redirect" value="crm/addon.php">

            <div>
                <label class="font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-cube text-slate-400 text-[10px]"></i> Nama Layanan Add-on
                </label>
                <input type="text" name="name" required placeholder="Contoh: Cloud Storage CCTV 30 Hari" class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl p-3 font-semibold text-slate-900 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition shadow-inner">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-tag text-slate-400 text-[10px]"></i> Kategori
                    </label>
                    <select name="category" class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl p-3 font-bold text-slate-800 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition">
                        <option value="CLOUD & VALUE ADD">Cloud & Value Add</option>
                        <option value="DEVICE & HARDWARE">Perangkat / Device</option>
                        <option value="STATIC IP">IP Publik Statis</option>
                        <option value="ENTERTAINMENT & TV">IPTV & Entertainment</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-rupiah-sign text-slate-400 text-[10px]"></i> Harga / Bulan
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-slate-400 font-bold text-xs">Rp</span>
                        <input type="number" name="price" required placeholder="75000" class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl py-3 pl-9 pr-3 font-extrabold text-blue-600 font-mono focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition shadow-inner">
                    </div>
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-align-left text-slate-400 text-[10px]"></i> Deskripsi Manfaat Layanan
                </label>
                <textarea name="description" rows="2" required placeholder="Jelaskan fitur dan benefit layanan add-on ini..." class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl p-3 font-medium text-slate-800 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition shadow-inner resize-none"></textarea>
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalAddAddon').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                    Batal
                </button>
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-5 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Layanan Add-on
                </button>
            </div>
        </form>
    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
