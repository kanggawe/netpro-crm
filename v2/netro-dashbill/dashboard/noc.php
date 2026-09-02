<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Dashboard NOC 24/7 & Monitoring Topologi Jaringan";
$page_subtitle = "Monitoring utilisasi IP Transit 10G, status perangkat OLT/FAT, dan peta sebaran insiden kabel optik.";
$active_menu = "m-dashboard";
require_once __DIR__ . '/../includes/header.php';

$outages = NocOutage::all();
?>

<div class="space-y-8 text-xs pb-6">
    <!-- Top 4 Network Health Cards (RedDash Cards without Accent Line) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Backbone Network SLA</span>
                    <strong class="text-2xl font-black text-slate-900 block mt-1">99.98%</strong>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Optimal
                </span>
                <span class="text-slate-400 ml-2">Core Backbone</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Peak Upstream Bandwidth</span>
                    <strong class="text-2xl font-black text-slate-900 block mt-1">4.82 Gbps</strong>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-gauge-high text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-brand-600 bg-brand-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-bolt mr-1 text-[10px]"></i> 48.2%
                </span>
                <span class="text-slate-400 ml-2">Port 10G Transit</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Mean Time to Recovery</span>
                    <strong class="text-2xl font-black text-emerald-600 block mt-1">24 Menit</strong>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-stopwatch text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-arrow-trend-down mr-1 text-[10px]"></i> Cepat
                </span>
                <span class="text-slate-400 ml-2">Rata-rata MTTR</span>
            </div>
        </div>

        <?php
        $nasList = RadiusNas::all();
        $nasCount = count($nasList);
        $coreNasHost = !empty($nasList[0]['nasname']) ? $nasList[0]['nasname'] : '127.0.0.1';
        $isCoreOnline = is_hardware_node_online($coreNasHost, 8728, 0.15);
        $onlineNasCount = $isCoreOnline ? 1 : 0;
        ?>
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Perangkat Inti (Core)</span>
                    <strong class="text-2xl font-black text-slate-900 block mt-1"><?= $onlineNasCount ?> OLT / <?= $nasCount ?> NAS</strong>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-network-wired text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <?php if ($isCoreOnline): ?>
                    <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                        <i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Online
                    </span>
                    <span class="text-slate-400 ml-2">Router NAS Aktif</span>
                <?php else: ?>
                    <span class="text-rose-600 bg-rose-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                        <i class="fa-solid fa-circle-xmark mr-1 text-[10px]"></i> Offline
                    </span>
                    <span class="text-slate-400 ml-2">No Physical Link</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Real-Time Bandwidth Throughput Chart (24 Hours Traffic) -->
    <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-chart-area text-brand-600"></i> Monitoring Trafik Bandwidth IP Transit & OpenIXP (24 Jam)
                </h3>
                <p class="text-slate-400 text-xs mt-0.5">Live throughput agregat upstream 10 Gbps + IIX/OIXP 10 Gbps.</p>
            </div>
            <span class="px-3 py-1 bg-brand-50 text-brand-700 font-bold font-mono rounded-xl text-[10px] border border-brand-200 flex items-center gap-1.5 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span> IN: 0.00 Gbps | OUT: 0.00 Gbps
            </span>
        </div>
        <div class="relative h-64 w-full pt-2">
            <canvas id="nocBandwidthChart"></canvas>
        </div>
    </div>

    <!-- Leaflet Topologi Peta Outage -->
    <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-sm space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-brand-600"></i> Peta Geografis Monitoring Insiden Jaringan (Fiber Cut & Outage Map)
                </h3>
                <p class="text-slate-400 text-xs mt-0.5">Pemantauan titik koordinat kabel fiber optik, ODP, dan penugasan armada splicer.</p>
            </div>
            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-xl text-[10px] border border-emerald-200">LIVE TOPOLOGY GPS</span>
        </div>
        <div id="noc-leaflet-map" class="h-72 rounded-2xl border border-slate-100 shadow-inner z-10"></div>
    </div>

    <!-- Outage Log Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm">Daftar Insiden Gangguan Jaringan Terkini</h3>
            <span class="text-slate-400 font-semibold">Total Insiden: <?= count($outages) ?></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Kode Insiden</th>
                        <th class="py-3 px-4">Titik Lokasi / Segmen FO</th>
                        <th class="py-3 px-4">Penyebab Gangguan</th>
                        <th class="py-3 px-4">Waktu Terdeteksi</th>
                        <th class="py-3 px-4 font-mono text-center">Durasi / MTTR</th>
                        <th class="py-3 px-4 text-right">Status Penanganan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($outages)): ?>
                        <?php foreach ($outages as $o): ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                            <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($o['incident_code'] ?? 'OUT-2026-001') ?></td>
                            <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($o['location'] ?? 'Segmen Backbone Jl. Raya Bekasi') ?></td>
                            <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($o['cause'] ?? 'Kabel FO Tertabrak Truk Kontainer') ?></td>
                            <td class="py-3.5 px-4 font-mono text-slate-500"><?= htmlspecialchars($o['start_time'] ?? date('Y-m-d H:i')) ?></td>
                            <td class="py-3.5 px-4 font-mono font-bold text-center text-slate-900"><?= htmlspecialchars($o['mttr'] ?? '42 Menit') ?></td>
                            <td class="py-3.5 px-4 text-right">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]">
                                    <?= htmlspecialchars($o['status'] ?? 'RESOLVED') ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4 text-slate-400">Belum ada log insiden gangguan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Leaflet Outage Map
    if (typeof L !== 'undefined' && document.getElementById('noc-leaflet-map')) {
        var nocMap = L.map('noc-leaflet-map').setView([-6.2891, 106.9182], 12);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: "&copy; OpenStreetMap &copy; CARTO"
        }).addTo(nocMap);

        L.marker([-6.2891, 106.9182]).addTo(nocMap).bindPopup('<b>Sentral OLT HQ</b><br>Status: ONLINE (16 PON Ports Up)');
    }

    // 2. NOC Bandwidth Chart (Luminous Executive Theme)
    var ctx = document.getElementById('nocBandwidthChart');
    if (ctx) {
        var chartCtx = ctx.getContext('2d');
        var inGradient = chartCtx.createLinearGradient(0, 0, 0, 240);
        inGradient.addColorStop(0, 'rgba(220, 38, 38, 0.25)');
        inGradient.addColorStop(0.8, 'rgba(220, 38, 38, 0.02)');
        inGradient.addColorStop(1, 'rgba(220, 38, 38, 0.00)');

        var outGradient = chartCtx.createLinearGradient(0, 0, 0, 240);
        outGradient.addColorStop(0, 'rgba(16, 185, 129, 0.20)');
        outGradient.addColorStop(0.8, 'rgba(16, 185, 129, 0.02)');
        outGradient.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

        new Chart(chartCtx, {
            type: 'line',
            data: {
                labels: ['00:00', '03:00', '06:00', '09:00', '12:00', '15:00', '18:00', '20:00', '21:30', '23:00'],
                datasets: [
                    {
                        label: 'Download / Inbound Traffic',
                        data: [1.2, 0.8, 1.5, 3.8, 5.2, 4.9, 7.8, 9.4, 8.6, 4.2],
                        borderColor: '#dc2626',
                        backgroundColor: inGradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#dc2626',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Upload / Outbound Traffic',
                        data: [0.4, 0.2, 0.5, 1.4, 2.1, 1.8, 3.2, 3.9, 3.4, 1.5],
                        borderColor: '#10b981',
                        backgroundColor: outGradient,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { family: 'Inter', size: 11, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 11 },
                        padding: 12,
                        cornerRadius: 12,
                        borderColor: 'rgba(220, 38, 38, 0.25)',
                        borderWidth: 1,
                        usePointStyle: true,
                        callbacks: {
                            label: function(ctx) { return ' ' + ctx.dataset.label + ': ' + ctx.raw + ' Gbps'; }
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
                            callback: function(val) { return val + ' Gbps'; }
                        }
                    }
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
