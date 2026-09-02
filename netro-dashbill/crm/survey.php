<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Jadwal Survey Lokasi & Uji Kelayakan";
$page_subtitle = "Monitoring penugasan tim survey teknisi, pengecekan redaman ODP, dan jarak kabel optik.";
$active_menu = "m-crm";
require_once __DIR__ . '/../includes/header.php';

$surveys = Survey::all();
$employees = Employee::all();
$prefillCustId = intval($_GET['customer_id'] ?? ($_GET['id'] ?? 0));
$prefillCustomer = $prefillCustId > 0 ? Customer::find($prefillCustId) : null;
$prefillName = $prefillCustomer['name'] ?? ($_GET['customer_name'] ?? '');
$prefillPhone = $prefillCustomer['phone'] ?? ($_GET['phone'] ?? '');
$prefillAddress = $prefillCustomer['address'] ?? ($_GET['address'] ?? '');
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Penugasan survey lokasi baru berhasil disimpan ke database!
    </div>
<?php elseif ($msg === 'deleted'): ?>
    <div class="p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-trash-can text-red-600 text-sm"></i>
        Data survey berhasil dihapus.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div><span class="text-slate-400 block font-semibold uppercase">Total Jadwal Survey</span><strong class="text-2xl font-bold text-slate-900"><?= count($surveys) ?></strong><span class="text-blue-600 block mt-0.5">Minggu Ini</span></div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="fa-solid fa-map-location-dot"></i></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div><span class="text-slate-400 block font-semibold uppercase">Hasil Feasible (Layak)</span><strong class="text-2xl font-bold text-emerald-600"><?= count($surveys) ?></strong><span class="text-slate-400 block mt-0.5">ODP < 150m</span></div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg"><i class="fa-solid fa-circle-check"></i></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div><span class="text-slate-400 block font-semibold uppercase">Pending Survey</span><strong class="text-2xl font-bold text-amber-600">0</strong><span class="text-slate-400 block mt-0.5">Menunggu Teknisi</span></div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg"><i class="fa-solid fa-clock"></i></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div><span class="text-slate-400 block font-semibold uppercase">Unfeasible (Jauh)</span><strong class="text-2xl font-bold text-rose-600">0</strong><span class="text-slate-400 block mt-0.5">Butuh FAT Baru</span></div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg"><i class="fa-solid fa-triangle-exclamation"></i></div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Penugasan Survey Calon Pelanggan</h3>
                <p class="text-slate-400">Verifikasi kelayakan sinyal optik & estimasi tarikan kabel drop wire.</p>
            </div>
            <button onclick="document.getElementById('modalAddSurvey').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-bold px-4 py-2 rounded-xl shadow-lg shadow-brand-950/30 flex items-center gap-1.5 transition">
                <i class="fa-solid fa-calendar-plus"></i> + Tambah Jadwal Survey
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">No Survey</th>
                        <th class="py-3 px-4">Calon Pelanggan</th>
                        <th class="py-3 px-4">Alamat Lokasi</th>
                        <th class="py-3 px-4">ODP Terdekat</th>
                        <th class="py-3 px-4">Jarak Estimasi</th>
                        <th class="py-3 px-4">Teknisi Lapangan</th>
                        <th class="py-3 px-4">Hasil Kelayakan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($surveys)): ?>
                    <tr><td colspan="8" class="py-6 text-center text-slate-400">Belum ada data survey di database.</td></tr>
                    <?php else: ?>
                    <?php foreach ($surveys as $s): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($s['survey_no']) ?></td>
                        <td class="py-3.5 px-4"><strong class="font-bold text-slate-800 block"><?= htmlspecialchars($s['customer_name']) ?></strong><span class="text-[10px] text-slate-400"><?= htmlspecialchars($s['phone']) ?></span></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($s['address']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-indigo-600"><?= htmlspecialchars($s['nearest_odp']) ?></td>
                        <td class="py-3.5 px-4 font-mono"><?= $s['distance_m'] ?> Meter</td>
                        <td class="py-3.5 px-4 font-semibold text-slate-700"><?= htmlspecialchars($s['tech_name']) ?></td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]"><?= htmlspecialchars($s['status']) ?> (<?= htmlspecialchars($s['attenuation']) ?>)</span></td>
                        <td class="py-3.5 px-4 text-right space-x-2">
                            <a href="../crm/instalasi.php" class="text-blue-600 font-bold hover:underline">Terbitkan WO</a>
                            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline" onsubmit="return confirm('Hapus survey <?= addslashes($s['survey_no']) ?>?');">
                                <input type="hidden" name="action" value="delete_survey">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <input type="hidden" name="redirect" value="crm/survey.php">
                                <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Jadwal Survey -->
<div id="modalAddSurvey" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 <?= empty($prefillName) ? 'hidden' : '' ?> backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-calendar-plus text-blue-400"></i> Tambah Jadwal Survey Lokasi
            </h3>
            <button onclick="document.getElementById('modalAddSurvey').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_survey">
            <input type="hidden" name="redirect" value="crm/survey.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Calon Pelanggan</label>
                <input type="text" name="customer_name" required value="<?= htmlspecialchars($prefillName) ?>" placeholder="Contoh: Hendro Kurniawan" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">No WhatsApp</label>
                    <input type="tel" name="phone" required value="<?= htmlspecialchars($prefillPhone) ?>" placeholder="081234567890" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">ODP Terdekat</label>
                    <input type="text" name="nearest_odp" value="ODP-JTW-04/16" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-indigo-600">
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Alamat Lokasi Survey</label>
                <textarea name="address" rows="2" required placeholder="Jl. Mawar No. 12, RT 01/RW 03..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2"><?= htmlspecialchars($prefillAddress) ?></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Teknisi Survey</label>
                    <select name="tech_name" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                        <?php if (empty($employees)): ?>
                            <option value="Teknisi Lapangan">Teknisi Lapangan</option>
                        <?php else: ?>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['position']) ?>)">
                                    <?= htmlspecialchars($emp['name']) ?> - <?= htmlspecialchars($emp['position']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Estimasi Jarak (Meter)</label>
                    <input type="number" name="distance_m" value="75" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                </div>
            </div>
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-2.5 rounded-xl shadow-lg shadow-brand-950/30 transition">Simpan Jadwal Survey</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
