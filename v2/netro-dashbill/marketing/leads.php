<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Prospek & Leads Penjualan Sales";
$page_subtitle = "Pipeline calon pelanggan potensial, riwayat follow-up WhatsApp, dan konversi closing.";
$active_menu = "m-marketing";
require_once __DIR__ . '/../includes/header.php';
?>


<?php
$leads = Lead::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Data prospek calon pelanggan baru berhasil dicatat!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Pipeline Calon Pelanggan (<?= count($leads) ?> Leads Aktif)</h3>
                <p class="text-slate-400">Monitoring status penawaran paket dari calon pelanggan baru.</p>
            </div>
            <button onclick="document.getElementById('modalAddLead').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-user-plus"></i> + Tambah Prospek Baru
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Nama Calon Klien</th>
                        <th class="py-3 px-4">No WhatsApp</th>
                        <th class="py-3 px-4">Wilayah / Alamat</th>
                        <th class="py-3 px-4">Minat Paket</th>
                        <th class="py-3 px-4">Sales Agent</th>
                        <th class="py-3 px-4">Status Prospek</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leads as $ld): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($ld['name']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-600"><?= htmlspecialchars($ld['phone']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($ld['address']) ?></td>
                        <td class="py-3.5 px-4 font-medium text-slate-700"><?= htmlspecialchars($ld['package_interest']) ?></td>
                        <td class="py-3.5 px-4 font-semibold text-indigo-600"><?= htmlspecialchars($ld['sales_agent']) ?></td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 font-bold rounded-full text-[10px]"><?= htmlspecialchars($ld['status']) ?></span></td>
                        <td class="py-3.5 px-4 text-right"><a href="../crm/survey.php" class="text-blue-600 font-bold hover:underline">Jadwal Survey</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Lead -->
<div id="modalAddLead" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-400"></i> Tambah Prospek / Leads Baru
            </h3>
            <button onclick="document.getElementById('modalAddLead').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_lead">
            <input type="hidden" name="redirect" value="marketing/leads.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Calon Klien</label>
                <input type="text" name="name" required placeholder="Contoh: Agus Supriyadi" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">No WhatsApp</label>
                    <input type="tel" name="phone" required placeholder="081234567890" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Minat Paket</label>
                    <select name="package_interest" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                        <option>Home Basic 20M</option>
                        <option selected>Home Premium 50M</option>
                        <option>SOHO Platinum 100M</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Alamat / Lokasi Pemasangan</label>
                <textarea name="address" rows="2" required placeholder="Nama Jalan, Blok, Kelurahan..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2"></textarea>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Sales Agent</label>
                <input type="text" name="sales_agent" value="Sales Doni" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">Simpan Prospek Sales</button>
        </form>
    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
