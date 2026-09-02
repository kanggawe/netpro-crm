<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Statistik Karyawan & Presensi";
$page_subtitle = "Ringkasan kehadiran harian staf kantor, teknisi lapangan, dan distribusi shift.";
$active_menu = "m-dashboard";
require_once __DIR__ . '/../includes/header.php';

$employees = Employee::all();
$attendances = Attendance::all();
$leaves = Leave::all();

$totalEmployees = count($employees);
$totalAttendances = count($attendances);
$totalLeaves = count($leaves);

// Division breakdown
$divisionCounts = [];
foreach ($employees as $emp) {
    $div = $emp['division'] ?: 'Operasional & Lapangan';
    $divisionCounts[$div] = ($divisionCounts[$div] ?? 0) + 1;
}

$onTimeCount = 0;
foreach ($attendances as $att) {
    if (strtoupper($att['status'] ?? '') === 'TEPAT WAKTU') {
        $onTimeCount++;
    }
}
$attendanceRate = $totalEmployees > 0 ? round(($onTimeCount / max(1, $totalEmployees)) * 100, 1) : 95.0;
?>

<div id="view-dashboard-hr" class="view-panel space-y-8 text-xs pb-6" data-title="Statistik Karyawan & Presensi" data-subtitle="Ringkasan kehadiran harian staf kantor, teknisi lapangan, dan distribusi shift.">
    <!-- Top 4 HR Metrics (RedDash KPI Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Total Karyawan Aktif</span>
                    <p class="text-2xl font-black text-slate-900 mt-1"><?= $totalEmployees ?> Orang</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-users-gear text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-layer-group mr-1 text-[10px]"></i> <?= count($divisionCounts) ?> Divisi
                </span>
                <span class="text-slate-400 ml-2">terdaftar</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Presensi Hadir Tepat Waktu</span>
                    <p class="text-2xl font-black text-emerald-600 mt-1"><?= $onTimeCount ?> Orang</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-user-check text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> <?= $attendanceRate ?>%
                </span>
                <span class="text-slate-400 ml-2">kehadiran</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Pengajuan Cuti & Izin</span>
                    <p class="text-2xl font-black text-amber-600 mt-1"><?= $totalLeaves ?> Orang</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-plane-departure text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-calendar mr-1 text-[10px]"></i> Cuti
                </span>
                <span class="text-slate-400 ml-2">bulan berjalan</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Shift Kerja Lapangan</span>
                    <p class="text-2xl font-black text-brand-600 mt-1">Active</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-brand-600 bg-brand-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-location-dot mr-1 text-[10px]"></i> GPS
                </span>
                <span class="text-slate-400 ml-2">Geofencing Valid</span>
            </div>
        </div>
    </div>

    <!-- Middle Section: 2 Columns Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-xs">
        <!-- Left 2/3 Column: Attendance Bar Chart -->
        <div class="lg:col-span-2 bg-white p-7 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-chart-simple text-brand-600"></i> Distribusi Staf per Divisi Operasional
                    </h3>
                    <p class="text-slate-400 text-xs mt-0.5">Proporsi jumlah personel dan teknisi pendukung operasional ISP.</p>
                </div>
                <span class="px-3 py-1 bg-brand-50 text-brand-700 font-bold rounded-xl text-[10px] border border-brand-200">TOTAL: <?= $totalEmployees ?> PERSONEL</span>
            </div>
            <div class="relative h-56 w-full pt-1">
                <canvas id="hrAttendanceChart"></canvas>
            </div>
        </div>

        <!-- Right 1/3 Column: Active Technicians On Duty -->
        <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-user-clock text-brand-600"></i> Teknisi & Tim Bertugas (On Duty)
                </h3>
                <p class="text-slate-400 text-xs mt-0.5">Personel aktif dari database karyawan.</p>
            </div>
            <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                <?php if (empty($employees)): ?>
                    <p class="text-slate-400 text-center py-4">Belum ada data staf.</p>
                <?php else: ?>
                    <?php foreach ($employees as $emp): ?>
                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-between">
                        <div>
                            <p class="font-bold text-slate-900"><?= htmlspecialchars($emp['name']) ?></p>
                            <span class="text-slate-500 text-[10px]"><?= htmlspecialchars($emp['position']) ?> (<?= htmlspecialchars($emp['division']) ?>)</span>
                        </div>
                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded font-bold text-[9px] uppercase"><?= htmlspecialchars($emp['status'] ?? 'AKTIF') ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="pt-2 border-t border-slate-100">
                <a href="<?= base_url('hr/karyawan.php') ?>" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 rounded-xl text-center block transition">
                    Kelola Database Karyawan →
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Section: Sebaran Staf Grid -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 text-xs">
        <h3 class="font-bold text-slate-900 text-sm">Rekapitulasi Divisi & Struktur Personel</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach ($divisionCounts as $divName => $count): ?>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                <p class="font-bold text-slate-800 line-clamp-1"><?= htmlspecialchars($divName) ?></p>
                <span class="text-2xl font-bold text-blue-600 block my-1"><?= $count ?></span>
                <span class="text-[10px] text-slate-400">Personel Aktif</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('hrAttendanceChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_keys($divisionCounts)) ?>,
                datasets: [
                    {
                        label: 'Jumlah Personel',
                        data: <?= json_encode(array_values($divisionCounts)) ?>,
                        backgroundColor: ['#dc2626', '#10b981', '#f59e0b', '#6366f1', '#ec4899'],
                        hoverBackgroundColor: ['#b91c1c', '#059669', '#d97706', '#4f46e5', '#db2777'],
                        borderRadius: 8,
                        barPercentage: 0.45
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 11 },
                        padding: 12,
                        cornerRadius: 12,
                        borderColor: 'rgba(220, 38, 38, 0.25)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(c) { return ' Total: ' + c.raw + ' Personel'; }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11, weight: '600' }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        border: { dash: [4, 4] },
                        grid: { color: 'rgba(226, 232, 240, 0.8)' },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#94a3b8',
                            stepSize: 1,
                            callback: function(v) { return v + ' Org'; }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
