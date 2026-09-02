<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Pengajuan Cuti & Izin Pegawai";
$page_subtitle = "Formulir permohonan cuti tahunan, sakit, izin dinas luar, dan persetujuan atasan.";
$active_menu = "m-hr";
require_once __DIR__ . '/../includes/header.php';
?>


<?php
$leaves = Leave::all();
$employees = Employee::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Permohonan cuti pegawai berhasil diajukan dan disimpan ke database!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Pengajuan Cuti Staf (<?= count($leaves) ?> Pengajuan)</h3>
                <p class="text-slate-400">Monitoring sisa kuota cuti tahunan dan persetujuan HRD.</p>
            </div>
            <button onclick="document.getElementById('modalAddCuti').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-plane-departure"></i> + Ajukan Cuti Pegawai
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Nama Karyawan</th>
                        <th class="py-3 px-4">Divisi</th>
                        <th class="py-3 px-4">Jenis Cuti</th>
                        <th class="py-3 px-4">Durasi Hari</th>
                        <th class="py-3 px-4">Alasan</th>
                        <th class="py-3 px-4">Status Approval</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($leaves as $lv): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($lv['employee_name']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($lv['division']) ?></td>
                        <td class="py-3.5 px-4 font-semibold text-indigo-600"><?= htmlspecialchars($lv['leave_type']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900"><?= $lv['duration_days'] ?> Hari (<?= htmlspecialchars($lv['start_date']) ?>)</td>
                        <td class="py-3.5 px-4 text-slate-500"><?= htmlspecialchars($lv['reason']) ?></td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]"><?= htmlspecialchars($lv['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Ajukan Cuti -->
<div id="modalAddCuti" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-plane-departure text-blue-400"></i> Ajukan Permohonan Cuti
            </h3>
            <button onclick="document.getElementById('modalAddCuti').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_leave">
            <input type="hidden" name="redirect" value="hr/cuti.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Karyawan</label>
                <select name="employee_name" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                    <?php if (empty($employees)): ?>
                        <option value="Karyawan">Karyawan</option>
                    <?php else: ?>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= htmlspecialchars($emp['name']) ?>">
                                <?= htmlspecialchars($emp['nik']) ?> - <?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['position']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Jenis Cuti</label>
                <select name="leave_type" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option>Cuti Tahunan</option>
                    <option>Izin Sakit (Surat Dokter)</option>
                    <option>Cuti Melahirkan / Keperluan Khusus</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="2026-08-25" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="2026-08-27" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Alasan Pengajuan Cuti</label>
                <textarea name="reason" rows="2" required placeholder="Keterangan keperluan cuti..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">Kirim Permohonan Cuti</button>
        </form>
    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
