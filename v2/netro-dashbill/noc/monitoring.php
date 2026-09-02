<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Router & OLT Monitoring";
$page_subtitle = "Visualisasi utilisasi API Mikrotik, load CPU, ping, dan traffic OLT.";
$active_menu = "m-noc";
require_once __DIR__ . '/../includes/header.php';

$outages = NocOutage::all();
$activeOutageCount = 0;
foreach ($outages as $o) {
    if (strtoupper($o['status'] ?? '') !== 'RESOLVED') {
        $activeOutageCount++;
    }
}
?>

<div id="view-noc-monitoring" class="view-panel space-y-6" data-title="Status Jaringan Global" data-subtitle="Overview kesehatan per jalur backbone optik dengan Leaflet GIS Map.">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 text-xs">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div><span class="text-xs font-semibold text-slate-500 block uppercase">Jalur Fiber Optik Ring</span><p class="text-2xl font-bold text-slate-900">0 KM</p><span class="text-[11px] text-emerald-600 font-medium">Standby Mode</span></div>
                <div class="w-10 h-10 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center font-bold text-lg"><i class="fa-solid fa-route"></i></div>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div><span class="text-xs font-semibold text-slate-500 block uppercase">Node POP OLT Active</span><p class="text-2xl font-bold text-emerald-600">0 OLT Node</p><span class="text-[11px] text-emerald-600 font-medium">Standby</span></div>
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-lg"><i class="fa-solid fa-server"></i></div>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div><span class="text-xs font-semibold text-slate-500 block uppercase">Kotak ODP Terpasang</span><p class="text-2xl font-bold text-blue-600">0 ODP</p><span class="text-[11px] text-slate-400 font-medium">Kapasitas 8 Port / ODP</span></div>
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-bold text-lg"><i class="fa-solid fa-box-archive"></i></div>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div><span class="text-xs font-semibold text-slate-500 block uppercase">Insiden Outage Fiber</span><p class="text-2xl font-bold text-red-600"><?= $activeOutageCount ?> Insiden</p><span class="text-[11px] text-emerald-600 font-medium"><?= $activeOutageCount > 0 ? 'Sedang Ditangani NOC' : 'Jaringan Normal 100%' ?></span></div>
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center font-bold text-lg"><i class="fa-solid fa-scissors"></i></div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Peta GIS Topologi Jaringan Optik (Leaflet Map)</h3>
                    <p class="text-[11px] text-slate-400">Peta rute kabel fiber optik backbone, POP OLT, dan titik insiden fiber cut.</p>
                </div>
                <div class="flex gap-2">
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">🔵 POP OLT</span>
                    <span class="px-2.5 py-1 bg-red-50 text-red-700 font-bold rounded text-[10px]">🔴 Outage Cut</span>
                </div>
            </div>
            
            <!-- Leaflet NOC Map Container -->
            <div id="noc-leaflet-map" class="h-96 rounded-xl border border-slate-300 shadow-inner z-10"></div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof L !== 'undefined' && document.getElementById('noc-leaflet-map')) {
                    var nocMap = L.map('noc-leaflet-map').setView([-6.2891, 106.9182], 13);

                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                        maxZoom: 19,
                        attribution: "&copy; OpenStreetMap &copy; CARTO"
                    }).addTo(nocMap);

                    L.marker([-6.2891, 106.9182]).addTo(nocMap)
                        .bindPopup('<b>Kantor Pusat HQ</b><br>NOC Core Server Submodule');
                }
            });
        </script>
    </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
