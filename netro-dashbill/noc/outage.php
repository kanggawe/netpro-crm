<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Insiden & Outage Jaringan Fiber";
$page_subtitle = "Pencatatan insiden kabel FO putus, OLT power loss, tiket NOC darurat, dan estimasi MTTR.";
$active_menu = "m-noc";
require_once __DIR__ . '/../includes/header.php';
?>


<?php
$outages = NocOutage::all();
$employees = Employee::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Laporan insiden outage baru berhasil diterbitkan untuk tim teknisi NOC!
    </div>
<?php elseif ($msg === 'resolved'): ?>
    <div class="p-4 bg-blue-50 text-blue-800 border border-blue-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-check-double text-blue-600 text-sm"></i>
        Insiden telah ditandai PULIH / RESOLVED.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Pusat Laporan Insiden & Outage Lapangan (<?= count($outages) ?> Insiden)</h3>
                <p class="text-slate-400">Monitoring eskalasi fiber cut & estimasi waktu perbaikan (MTTR).</p>
            </div>
            <button onclick="document.getElementById('modalAddOutage').classList.remove('hidden')" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-triangle-exclamation"></i> + Lapor Insiden Baru
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">No Insiden</th>
                        <th class="py-3 px-4">Lokasi / Segmen Node</th>
                        <th class="py-3 px-4">Tipe Masalah</th>
                        <th class="py-3 px-4">Dampak Pelanggan</th>
                        <th class="py-3 px-4">Tim Penanganan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($outages as $o): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-rose-600"><?= htmlspecialchars($o['outage_no']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($o['location']) ?></td>
                        <td class="py-3.5 px-4 font-medium text-rose-700"><?= htmlspecialchars($o['issue_type']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900"><?= $o['affected_users'] ?> Pelanggan</td>
                        <td class="py-3.5 px-4 font-semibold text-slate-700"><?= htmlspecialchars($o['tech_name']) ?></td>
                        <td class="py-3.5 px-4">
                            <?php if ($o['status'] === 'ON PROGRESS'): ?>
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 font-bold rounded-full text-[10px]">ON PROGRESS</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]">PULIH / RESOLVED</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <?php if ($o['status'] === 'ON PROGRESS'): ?>
                            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline">
                                <input type="hidden" name="action" value="resolve_outage">
                                <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                <input type="hidden" name="redirect" value="noc/outage.php">
                                <button type="submit" class="text-emerald-600 font-bold hover:underline">Tandai Pulih</button>
                            </form>
                            <?php else: ?>
                                <span class="text-slate-400">Closed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Lapor Outage -->
<div id="modalAddOutage" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-rose-400"></i> Lapor Insiden Outage Baru
            </h3>
            <button onclick="document.getElementById('modalAddOutage').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_outage">
            <input type="hidden" name="redirect" value="noc/outage.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Lokasi Node / Segmen FO</label>
                <input type="text" name="location" required placeholder="Contoh: Segmen Ring Kalibata - Cawang" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Tipe Gangguan</label>
                <select name="issue_type" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option>Kabel Fiber Putus (Fiber Cut)</option>
                    <option>OLT Power Down / PLN Mati</option>
                    <option>Redaman Kritis Global (Core Degraded)</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Dampak (User)</label>
                    <input type="number" name="affected_users" value="100" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Teknisi Bertugas</label>
                    <select name="tech_name" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                        <?php if (empty($employees)): ?>
                            <option value="Tim Splicer NOC">Tim Splicer NOC</option>
                        <?php else: ?>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= htmlspecialchars($emp['name']) ?>">
                                    <?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['position']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-2.5 rounded-xl shadow transition">Terbitkan Laporan Outage</button>
        </form>
    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
