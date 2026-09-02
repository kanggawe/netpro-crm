<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Dashboard Tiket Gangguan";
$page_subtitle = "Pemantauan beban tiket CS/NOC, SLA breach, dan waktu penyelesaian rata-rata.";
$active_menu = "m-dashboard";
require_once __DIR__ . '/../includes/header.php';

$tickets = Ticket::all();

// Dynamic Calculations from Database
$totalCount = count($tickets);
$openCount = 0;
$inProgressCount = 0;
$closedCount = 0;
$urgentTickets = [];
$openIncidentList = [];

foreach ($tickets as $t) {
    $st = strtoupper($t['status'] ?? 'OPEN');
    $pr = strtoupper($t['priority'] ?? 'MEDIUM');

    if ($st === 'CLOSED' || $st === 'SELESAI') {
        $closedCount++;
    } elseif ($st === 'IN_PROGRESS' || $st === 'PROSES') {
        $inProgressCount++;
        $openIncidentList[] = $t;
    } else {
        $openCount++;
        $openIncidentList[] = $t;
    }

    if ($pr === 'HIGH' && $st !== 'CLOSED' && $st !== 'SELESAI') {
        $urgentTickets[] = $t;
    }
}

$slaComplianceRate = $totalCount > 0 ? round((($closedCount + $inProgressCount) / $totalCount) * 100, 1) : 0.0;
?>

<div id="view-dashboard-tickets" class="view-panel space-y-8 text-xs pb-6" data-title="Dashboard Tiket Gangguan" data-subtitle="Pemantauan beban tiket CS/NOC, SLA breach, dan waktu penyelesaian rata-rata.">
    <!-- Top 4 Dynamic KPI Cards (RedDash Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Tiket Terbuka (Open)</span>
                    <p class="text-2xl font-black text-brand-600 mt-1"><?= $openCount ?> Tiket</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-ticket text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-rose-600 bg-rose-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-triangle-exclamation mr-1 text-[10px]"></i> Perlu Aksi
                </span>
                <span class="text-slate-400 ml-2">antrean CS</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">In-Progress Teknisi</span>
                    <p class="text-2xl font-black text-amber-600 mt-1"><?= $inProgressCount ?> Tiket</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-screwdriver-wrench text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-person-digging mr-1 text-[10px]"></i> Lapangan
                </span>
                <span class="text-slate-400 ml-2">dalam proses</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Tiket Selesai (Closed)</span>
                    <p class="text-2xl font-black text-emerald-600 mt-1"><?= $closedCount ?> Tiket</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-check-double mr-1 text-[10px]"></i> Closed
                </span>
                <span class="text-slate-400 ml-2">MTTR optimal</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">SLA Compliance</span>
                    <p class="text-2xl font-black text-slate-900 mt-1"><?= $slaComplianceRate ?>%</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-award text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-brand-600 bg-brand-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-shield mr-1 text-[10px]"></i> Standar SLA
                </span>
                <span class="text-slate-400 ml-2">kualitas layanan</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 text-xs">
        <!-- Weekly Resolution Chart -->
        <div class="lg:col-span-2 bg-white p-7 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-brand-600"></i> Tren Resolusi Tiket Insiden Mingguan
                    </h3>
                    <p class="text-slate-400 text-xs mt-0.5">Komparasi tiket terselesaikan terhadap rata-rata waktu pemulihan MTTR (Jam).</p>
                </div>
                <span class="px-3 py-1 bg-brand-50 text-brand-700 font-bold rounded-xl text-[10px] border border-brand-200">LIVE TELEMETRI</span>
            </div>
            <div class="relative h-56 w-full pt-1">
                <canvas id="ticketsWeeklyChart"></canvas>
            </div>
        </div>

        <!-- High Priority Urgent Tickets Box -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-600"></i> Tiket Prioritas Urgent
                </h3>
                <span class="px-2 py-0.5 bg-red-100 text-red-800 font-bold rounded text-[10px]"><?= count($urgentTickets) ?> URGENT</span>
            </div>

            <div class="space-y-2.5 max-h-64 overflow-y-auto pr-1">
                <?php if (empty($urgentTickets)): ?>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100 text-center text-emerald-700 font-medium">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-lg mb-1 block"></i>
                        Tidak ada tiket prioritas tinggi yang tertunda.
                    </div>
                <?php else: ?>
                    <?php foreach ($urgentTickets as $ut): ?>
                    <div class="p-3 bg-red-50/80 rounded-xl border border-red-100 space-y-1">
                        <div class="flex justify-between font-bold text-red-800">
                            <span><?= htmlspecialchars($ut['ticket_no']) ?> (<?= htmlspecialchars($ut['customer_name'] ?? 'Pelanggan') ?>)</span>
                            <span class="px-1.5 py-0.5 bg-red-200 text-red-900 rounded text-[9px]">HIGH</span>
                        </div>
                        <p class="text-red-700 text-xs font-medium"><?= htmlspecialchars($ut['category'] ?? 'Gangguan') ?></p>
                        <div class="flex justify-between items-center text-[10px] text-red-600 pt-0.5">
                            <span>PJ: <strong><?= htmlspecialchars($ut['assigned_tech'] ?? 'Teknisi') ?></strong></span>
                            <span class="font-bold font-mono">SLA: <?= intval($ut['sla_minutes'] ?? 120) ?> Menit</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="pt-2 border-t border-slate-100">
                <a href="<?= base_url('tickets/list.php') ?>" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 rounded-xl text-center block transition">
                    Buka Halaman Manajemen Tiket →
                </a>
            </div>
        </div>
    </div>

    <!-- Active Open Incident Matrix -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden text-xs space-y-3 p-6">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-tower-broadcast text-indigo-600"></i> Matriks Open Incident Tickets & SLA Monitoring
                </h3>
                <p class="text-slate-400">Total <?= count($openIncidentList) ?> Tiket sedang dalam antrean atau penanganan aktif teknisi.</p>
            </div>
            <a href="<?= base_url('tickets/list.php') ?>" class="text-blue-600 font-bold hover:underline">Kelola Seluruh Tiket →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-[11px]">
                        <th class="py-3 px-4">No Tiket</th>
                        <th class="py-3 px-4">CID & Pelanggan</th>
                        <th class="py-3 px-4">Kategori Masalah</th>
                        <th class="py-3 px-4">Priority</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Teknisi Penanggung Jawab</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($openIncidentList)): ?>
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">
                                Seluruh tiket insiden telah terselesaikan (All Closed).
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($openIncidentList as $ot): ?>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($ot['ticket_no']) ?></td>
                            <td class="py-3 px-4">
                                <strong class="font-bold text-slate-800 block"><?= htmlspecialchars($ot['customer_name'] ?? 'Pelanggan') ?></strong>
                                <span class="font-mono text-slate-400 text-[10px]"><?= htmlspecialchars($ot['cid'] ?? '-') ?></span>
                            </td>
                            <td class="py-3 px-4 text-slate-700"><?= htmlspecialchars($ot['category'] ?? 'Gangguan') ?></td>
                            <td class="py-3 px-4">
                                <?php if (strtoupper($ot['priority'] ?? '') === 'HIGH'): ?>
                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 font-bold rounded text-[9px]">HIGH</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 font-bold rounded text-[9px]"><?= htmlspecialchars($ot['priority'] ?? 'MEDIUM') ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4">
                                <?php if (strtoupper($ot['status'] ?? '') === 'IN_PROGRESS'): ?>
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 font-bold rounded-full text-[10px]">IN PROGRESS</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 font-bold rounded-full text-[10px]">OPEN</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-700 text-right">
                                <?= htmlspecialchars($ot['assigned_tech'] ?? 'Teknisi Standby') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('ticketsWeeklyChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
                datasets: [
                    {
                        type: 'bar',
                        label: 'Tiket Resolved',
                        data: [<?= $closedCount ?>, 0, 0, 0],
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        yAxisID: 'yTickets',
                        barThickness: 32
                    },
                    {
                        type: 'line',
                        label: 'Rata-rata MTTR (Jam)',
                        data: [0, 0, 0, 0],
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 5,
                        yAxisID: 'yMttr'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end' },
                    tooltip: {
                        callbacks: {
                            label: function(c) {
                                if (c.dataset.yAxisID === 'yTickets') return ' Selesai: ' + c.raw + ' Tiket';
                                return ' MTTR: ' + c.raw + ' Jam';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    yTickets: {
                        position: 'left',
                        ticks: { callback: function(v) { return v + ' Tkt'; } }
                    },
                    yMttr: {
                        position: 'right',
                        grid: { display: false },
                        ticks: { callback: function(v) { return v + ' Jam'; } }
                    }
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
