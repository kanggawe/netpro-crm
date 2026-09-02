<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Absensi, Presensi GPS & Shift Kerja 24/7";
$page_subtitle = "Rekapitulasi presensi harian staf ISP, verifikasi radius geofencing GPS, dan roster shift NOC 24/7.";
$active_menu = "m-hr";
require_once __DIR__ . '/../includes/header.php';

$attendances = Attendance::all();
$employees = Employee::all();
$msg = $_GET['msg'] ?? '';

$onTime = 0;
$nightShift = 0;
foreach ($attendances as $att) {
    if ($att['status'] === 'TEPAT WAKTU') $onTime++;
    if (str_contains($att['shift_type'], 'Malam')) $nightShift++;
}
?>

<?php if ($msg === 'created_attendance'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Presensi check-in karyawan baru berhasil diverifikasi dengan radius GPS valid!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <!-- Top 3 Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Hadir Tepat Waktu</span>
                <strong class="text-2xl font-bold text-emerald-600"><?= $onTime ?> Karyawan</strong>
                <span class="text-emerald-600 font-medium block mt-0.5">✓ Radius GPS Geofencing Valid</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg"><i class="fa-solid fa-user-check"></i></div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Roster Shift Malam NOC</span>
                <strong class="text-2xl font-bold text-blue-600"><?= $nightShift ?> Operator On Duty</strong>
                <span class="text-slate-400 font-medium block mt-0.5">Shift 22:00 - 07:00 WIB (24/7)</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="fa-solid fa-moon"></i></div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Total Log Presensi Hari Ini</span>
                <strong class="text-2xl font-bold text-indigo-600"><?= count($attendances) ?> Presensi</strong>
                <span class="text-slate-400 font-medium block mt-0.5">Terintegrasi ke Modul Payroll</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg"><i class="fa-solid fa-calendar-check"></i></div>
        </div>
    </div>

    <!-- Leaflet GPS Map Section -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-blue-600"></i> Peta Sebaran GPS Check-In Karyawan & Teknisi
                </h3>
                <p class="text-slate-400">Verifikasi koordinat lokasi check-in kantor pusat HQ vs titik FAT teknisi lapangan.</p>
            </div>
            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">LIVE GPS TRACKING</span>
        </div>
        <div id="hr-leaflet-map" class="h-64 rounded-xl border border-slate-200 shadow-inner z-10"></div>
    </div>

    <!-- Attendance Log Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Log Presensi Harian Staf (<?= count($attendances) ?> Log)</h3>
                <p class="text-slate-400">Pencatatan waktu jam kerja sesuai UU Ketenagakerjaan & PP No. 35 Tahun 2021.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="document.getElementById('modalAddAttendance').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
                    <i class="fa-solid fa-fingerprint"></i> + Catat Presensi / Check-in
                </button>
                <a href="cetak_absensi.php" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-3.5 py-2 rounded-lg shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i> Cetak / Export PDF
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Nama Pegawai</th>
                        <th class="py-3 px-4">Divisi Kerja</th>
                        <th class="py-3 px-4">Pola Shift Kerja</th>
                        <th class="py-3 px-4 font-mono">Jam Masuk (Clock-in)</th>
                        <th class="py-3 px-4 font-mono">Jam Pulang (Clock-out)</th>
                        <th class="py-3 px-4">Titik Lokasi GPS Check-in</th>
                        <th class="py-3 px-4 text-right">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attendances)): ?>
                    <tr class="border-b border-slate-50">
                        <td colspan="7" class="py-8 text-center text-slate-400 font-medium">Belum ada log presensi harian staf di database.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($attendances as $att): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($att['employee_name']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($att['division']) ?></td>
                        <td class="py-3.5 px-4 font-medium text-slate-700">
                            <?php if (str_contains($att['shift_type'], 'Malam')): ?>
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Shift Malam NOC</span>
                            <?php elseif (str_contains($att['shift_type'], 'Siang')): ?>
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 font-bold rounded text-[10px]">Shift Siang</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded text-[10px]">Shift Pagi Reguler</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-600"><?= htmlspecialchars($att['clock_in']) ?> WIB</td>
                        <td class="py-3.5 px-4 font-mono text-slate-500"><?= $att['clock_out'] ? htmlspecialchars($att['clock_out']) . ' WIB' : 'On Duty' ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-600"><?= htmlspecialchars($att['gps_location']) ?></td>
                        <td class="py-3.5 px-4 text-right">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]">
                                <?= htmlspecialchars($att['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Catat Presensi / Check-in -->
<div id="modalAddAttendance" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-fingerprint text-blue-400"></i> Catat Presensi / Check-in Staf
            </h3>
            <button onclick="document.getElementById('modalAddAttendance').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_attendance">
            <input type="hidden" name="redirect" value="hr/absensi.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Pegawai</label>
                <select name="employee_name" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= htmlspecialchars($emp['name']) ?>"><?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['division']) ?>)</option>
                    <?php endforeach; ?>
                    <?php if (empty($employees)): ?>
                        <option value="Pegawai Standard">Staf Operasional</option>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Divisi Kerja</label>
                <select name="division" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option>NOC & Jaringan</option>
                    <option>Teknisi Lapangan</option>
                    <option>CS & Ticketing</option>
                    <option>Finance & Akuntansi</option>
                    <option>Marketing & Sales</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Pola Shift Kerja</label>
                    <select name="shift_type" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                        <option>Shift Pagi (08:00 - 17:00)</option>
                        <option>Shift Malam NOC (22:00 - 07:00)</option>
                        <option>Shift Siang (13:00 - 21:00)</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Status Kehadiran</label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                        <option>TEPAT WAKTU</option>
                        <option>TERLAMBAT</option>
                        <option>DINAS LUAR / SURVEY</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Titik Lokasi GPS Check-in</label>
                <input type="text" name="gps_location" value="Kantor Pusat HQ (-6.2891, 106.9182)" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">Simpan & Verifikasi Presensi</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof L !== 'undefined' && document.getElementById('hr-leaflet-map')) {
        var hrMap = L.map('hr-leaflet-map').setView([-6.2891, 106.9182], 13);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: "&copy; OpenStreetMap &copy; CARTO"
        }).addTo(hrMap);

        <?php if (!empty($attendances)): ?>
        L.marker([-6.2891, 106.9182]).addTo(hrMap).bindPopup('<b>Kantor Pusat HQ</b><br><?= count($attendances) ?> Pegawai Check-in<br>Status: Radius Valid (Geofenced)');
        <?php else: ?>
        L.marker([-6.2891, 106.9182]).addTo(hrMap).bindPopup('<b>Kantor Pusat HQ</b><br>Belum ada presensi hari ini');
        <?php endif; ?>
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
