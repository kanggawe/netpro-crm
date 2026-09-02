<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Leaderboard & Grafik Performa Staf";
$page_subtitle = "Peringkat performa staf terbaik, skor evaluasi kumulatif, dan kelayakan bonus insentif.";
$active_menu = "m-kinerja";
require_once __DIR__ . '/../includes/header.php';

$employees = Employee::all();
?>

<div class="space-y-6 text-xs">
    <!-- Top 3 Podium Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php if (empty($employees)): ?>
        <div class="col-span-3 bg-white p-8 rounded-2xl border border-slate-200 text-center text-slate-400 font-medium">
            Belum ada peringkat performa karyawan untuk periode ini.
        </div>
        <?php else: ?>
        <?php if (isset($employees[0])): ?>
        <div class="p-6 bg-gradient-to-br from-amber-400 via-amber-500 to-yellow-600 text-white rounded-2xl shadow-xl space-y-3 relative overflow-hidden">
            <div class="flex justify-between items-center">
                <span class="px-2.5 py-1 bg-white/20 backdrop-blur rounded-full font-bold text-[10px]">🏆 JUARA 1 LEADERBOARD</span>
                <i class="fa-solid fa-crown text-2xl text-yellow-200"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-xl"><?= htmlspecialchars($employees[0]['name']) ?></h4>
                <p class="text-amber-100 text-xs"><?= htmlspecialchars($employees[0]['position'] ?? 'Staf') ?></p>
            </div>
            <div class="border-t border-white/20 pt-2 flex justify-between items-center">
                <span class="text-xs font-semibold">Skor KPI Kumulatif:</span>
                <strong class="text-2xl font-bold font-mono">96.5 / 100</strong>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($employees[1])): ?>
        <div class="p-6 bg-gradient-to-br from-slate-400 via-slate-500 to-slate-600 text-white rounded-2xl shadow-lg space-y-3">
            <div class="flex justify-between items-center">
                <span class="px-2.5 py-1 bg-white/20 backdrop-blur rounded-full font-bold text-[10px]">🥈 JUARA 2 LEADERBOARD</span>
                <i class="fa-solid fa-medal text-2xl text-slate-200"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-xl"><?= htmlspecialchars($employees[1]['name']) ?></h4>
                <p class="text-slate-100 text-xs"><?= htmlspecialchars($employees[1]['position'] ?? 'Staf') ?></p>
            </div>
            <div class="border-t border-white/20 pt-2 flex justify-between items-center">
                <span class="text-xs font-semibold">Skor KPI Kumulatif:</span>
                <strong class="text-2xl font-bold font-mono">94.0 / 100</strong>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($employees[2])): ?>
        <div class="p-6 bg-gradient-to-br from-amber-700 via-amber-800 to-amber-900 text-white rounded-2xl shadow-lg space-y-3">
            <div class="flex justify-between items-center">
                <span class="px-2.5 py-1 bg-white/20 backdrop-blur rounded-full font-bold text-[10px]">🥉 JUARA 3 LEADERBOARD</span>
                <i class="fa-solid fa-award text-2xl text-amber-300"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-xl"><?= htmlspecialchars($employees[2]['name']) ?></h4>
                <p class="text-amber-100 text-xs"><?= htmlspecialchars($employees[2]['position'] ?? 'Staf') ?></p>
            </div>
            <div class="border-t border-white/20 pt-2 flex justify-between items-center">
                <span class="text-xs font-semibold">Skor KPI Kumulatif:</span>
                <strong class="text-2xl font-bold font-mono">92.8 / 100</strong>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Leaderboard Full Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Peringkat Performa Karyawan ISP Periode <?= date('F Y') ?></h3>
                <p class="text-slate-400">Evaluasi menyeluruh presensi, pencapaian target kerja, dan review teknis supervisor.</p>
            </div>
            <button onclick="window.print()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg shadow">
                <i class="fa-solid fa-print"></i> Cetak Leaderboard
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4 text-center">Rank</th>
                        <th class="py-3 px-4">Nama Staf</th>
                        <th class="py-3 px-4">Divisi Kerja</th>
                        <th class="py-3 px-4">Jabatan</th>
                        <th class="py-3 px-4 font-mono text-center">Skor Teknis</th>
                        <th class="py-3 px-4 font-mono text-center">Skor Disiplin</th>
                        <th class="py-3 px-4 font-mono text-center">Total Skor KPI</th>
                        <th class="py-3 px-4 text-right">Status Insentif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                    <tr>
                        <td colspan="8" class="py-6 text-center text-slate-400">Belum ada data evaluasi kinerja karyawan di database.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($employees as $idx => $emp): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-bold text-center text-amber-600 font-mono text-sm">#<?= $idx + 1 ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($emp['name']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($emp['division'] ?? '-') ?></td>
                        <td class="py-3.5 px-4 text-slate-700"><?= htmlspecialchars($emp['position'] ?? '-') ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-center text-blue-600">90.0</td>
                        <td class="py-3.5 px-4 font-mono font-bold text-center text-emerald-600">90.0</td>
                        <td class="py-3.5 px-4 font-mono font-bold text-center text-slate-900 text-sm">90.0</td>
                        <td class="py-3.5 px-4 text-right"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">BONUS STANDAR ✓</span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
