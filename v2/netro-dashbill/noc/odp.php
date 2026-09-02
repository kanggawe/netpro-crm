<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Peta Sebaran ODP & Kapasitas Fiber Optik";
$page_subtitle = "GIS Mapping sebaran Optical Distribution Point (ODP), utilisasi port splitter, dan telemetri redaman sinyal.";
$active_menu = "m-noc";
require_once __DIR__ . '/../includes/header.php';

// ODP Master Data
$odpList = [];

$totalPorts = 0;
$totalUsed = 0;
foreach ($odpList as $odp) {
    $totalPorts += $odp['total_ports'];
    $totalUsed += $odp['used_ports'];
}
$totalFree = $totalPorts - $totalUsed;
$utilizationPct = round(($totalUsed / max(1, $totalPorts)) * 100, 1);
?>

<div class="space-y-6 text-xs pb-8">
    <!-- Top KPI Statistic Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Box ODP Terpasang</span>
                <strong class="font-extrabold text-slate-900 text-xl block mt-0.5"><?= count($odpList) ?> Box ODP</strong>
                <span class="text-blue-600 text-[10px] font-semibold">Tersambung ke 3 OLT GPON</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Kapasitas Port Splitter</span>
                <strong class="font-extrabold text-slate-900 text-xl font-mono block mt-0.5"><?= $totalPorts ?> Port</strong>
                <span class="text-slate-500 text-[10px]">Splitter 1:8 & 1:16 PLC</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-diagram-next"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Port Terpakai (Pelanggan)</span>
                <strong class="font-extrabold text-emerald-600 text-xl font-mono block mt-0.5"><?= $totalUsed ?> / <?= $totalPorts ?> Port</strong>
                <span class="text-emerald-600 text-[10px] font-semibold"><?= $utilizationPct ?>% Utilisasi Jaringan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Port Kosong (Siap Pasang)</span>
                <strong class="font-extrabold text-blue-600 text-xl font-mono block mt-0.5"><?= $totalFree ?> Port Free</strong>
                <span class="text-blue-600 text-[10px] font-semibold">Tersedia untuk survey baru</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-plug-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- GIS Interactive Map Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-emerald-600"></i> Peta GIS Sebaran Box ODP & Jalur Distribusi Fiber
                </h3>
                <p class="text-slate-400 text-xs">Klik marker ODP pada peta untuk melihat detail kapasitas port dan status redaman sinyal optik.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-3 text-[11px] font-bold">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Tersedia (&lt;80%)</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Menipis (80-90%)</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Penuh (100%)</span>
                </div>
                <button onclick="document.getElementById('modalAddOdp').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl shadow-lg shadow-blue-500/20 transition flex items-center gap-1.5 text-xs">
                    <i class="fa-solid fa-plus"></i> + Tambah Box ODP Baru
                </button>
            </div>
        </div>

        <!-- Leaflet Map Canvas -->
        <div id="leaflet-odp-map" class="h-96 rounded-2xl border border-slate-200 shadow-inner z-10"></div>
    </div>

    <!-- ODP Master Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-blue-600"></i> Matriks Kapasitas & Redaman Box ODP
                </h3>
                <p class="text-slate-400 text-xs">Data teknis seluruh titik ODP, splitter ratio, port terpakai, dan kabel drop optik.</p>
            </div>
            <div class="relative w-full sm:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="searchOdpInput" onkeyup="filterOdpTable()" placeholder="Cari Kode ODP, Lokasi..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs focus:bg-white focus:border-blue-500 transition">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="odpTable">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-[11px]">
                        <th class="py-3 px-4">Kode Box ODP</th>
                        <th class="py-3 px-4">Induk OLT / PON Port</th>
                        <th class="py-3 px-4">Lokasi / Titik Tiang</th>
                        <th class="py-3 px-4 font-mono">Splitter</th>
                        <th class="py-3 px-4">Utilisasi Port</th>
                        <th class="py-3 px-4 font-mono">Redaman Sinyal</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($odpList as $odp): 
                        $used = $odp['used_ports'];
                        $total = $odp['total_ports'];
                        $free = $total - $used;
                        $pct = round(($used / $total) * 100);
                    ?>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                            <span class="cursor-pointer hover:underline" onclick="focusOdpMap(<?= $odp['lat'] ?>, <?= $odp['lng'] ?>, '<?= addslashes($odp['code']) ?>')">
                                📍 <?= htmlspecialchars($odp['code']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium">
                            <?= htmlspecialchars($odp['olt_source']) ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <strong class="text-slate-900 block text-xs"><?= htmlspecialchars($odp['location']) ?></strong>
                            <span class="text-[10px] text-slate-400 font-mono"><?= $odp['lat'] ?>, <?= $odp['lng'] ?></span>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-purple-700">
                            <?= htmlspecialchars($odp['ratio']) ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="space-y-1 w-32">
                                <div class="flex justify-between text-[10px]">
                                    <strong class="text-slate-800"><?= $used ?>/<?= $total ?> Port</strong>
                                    <span class="text-slate-400 font-mono"><?= $free ?> Free</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full <?= $pct >= 95 ? 'bg-rose-500' : ($pct >= 80 ? 'bg-amber-500' : 'bg-emerald-500') ?>" style="width: <?= $pct ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-600">
                            <?= htmlspecialchars($odp['attenuation']) ?>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <?php if ($odp['status'] === 'PENUH'): ?>
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 font-bold rounded-full text-[10px]">PENUH (FULL)</span>
                            <?php elseif ($odp['status'] === 'MENIPIS'): ?>
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 font-bold rounded-full text-[10px]">MENIPIS</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold rounded-full text-[10px]">TERSEDIA</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-2">
                            <button onclick="focusOdpMap(<?= $odp['lat'] ?>, <?= $odp['lng'] ?>, '<?= addslashes($odp['code']) ?>')" class="text-blue-600 hover:text-blue-800 font-bold hover:underline">
                                <i class="fa-solid fa-location-crosshairs"></i> Peta
                            </button>
                            <a href="<?= base_url('crm/registrasi.php') ?>" class="text-emerald-600 hover:text-emerald-800 font-bold hover:underline">
                                + Pasang
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah ODP Baru -->
<div id="modalAddOdp" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-blue-600"></i> Tambah Titik Box ODP Baru
            </h3>
            <button onclick="document.getElementById('modalAddOdp').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        
        <form onsubmit="event.preventDefault(); document.getElementById('modalAddOdp').classList.add('hidden'); triggerToast('ODP Berhasil Ditambahkan', 'Titik ODP baru telah tersimpan di GIS database!');" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kode Box ODP <span class="text-rose-500">*</span></label>
                    <input type="text" required placeholder="ODP-JTW-09/16" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-blue-600">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Induk OLT / PON Port <span class="text-rose-500">*</span></label>
                    <select class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                        <option value="OLT-ZTE-C320 / PON 1/1/1">OLT-ZTE-C320 / PON 1/1/1</option>
                        <option value="OLT-ZTE-C320 / PON 1/1/2">OLT-ZTE-C320 / PON 1/1/2</option>
                        <option value="OLT-HUAWEI-MA5608T / PON 1/1/1">OLT-HUAWEI-MA5608T / PON 1/1/1</option>
                        <option value="OLT-FIBERHOME / PON 1/1/1">OLT-FIBERHOME / PON 1/1/1</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Splitter Ratio <span class="text-rose-500">*</span></label>
                    <select class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                        <option value="1:16">1:16 PLC Splitter (16 Port)</option>
                        <option value="1:8">1:8 PLC Splitter (8 Port)</option>
                        <option value="1:4">1:4 PLC Splitter (4 Port)</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Redaman Rata-Rata OPM</label>
                    <input type="text" value="-18.2 dBm" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-emerald-600 font-bold">
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Koordinat GPS (Lat, Lng) <span class="text-rose-500">*</span></label>
                <input type="text" required value="-6.2891, 106.9182" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-blue-700 font-bold">
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Alamat / Patokan Tiang Lokasi <span class="text-rose-500">*</span></label>
                <textarea rows="2" required placeholder="Jl. Jatiwaringin Raya No. 88 (Tiang PLN #14)..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow-lg shadow-blue-500/25 transition">
                Simpan & Plot ke Peta GIS
            </button>
        </form>
    </div>
</div>

<script>
var odpMapInstance = null;
var odpMarkers = {};

document.addEventListener('DOMContentLoaded', function() {
    if (typeof L !== 'undefined' && document.getElementById('leaflet-odp-map')) {
        odpMapInstance = L.map('leaflet-odp-map').setView([-6.2750, 106.9180], 13);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: "&copy; OpenStreetMap &copy; CARTO"
        }).addTo(odpMapInstance);

        // Core POP HQ Marker
        var hqIcon = L.divIcon({
            className: 'custom-hq-marker',
            html: '<div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-xl border-2 border-white"><i class="fa-solid fa-tower-broadcast"></i></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });
        L.marker([-6.2800, 106.9200], {icon: hqIcon}).addTo(odpMapInstance)
            .bindPopup('<b>POP SENTRAL HQ FIBER</b><br>Core OLT ZTE C320 & Huawei MA5608T');

        // Plot ODP Nodes
        var odpData = <?= json_encode($odpList) ?>;
        odpData.forEach(function(odp) {
            var colorClass = odp.status === 'PENUH' ? 'bg-rose-500' : (odp.status === 'MENIPIS' ? 'bg-amber-500' : 'bg-emerald-500');
            var customIcon = L.divIcon({
                className: 'custom-odp-pin',
                html: '<div class="w-7 h-7 rounded-xl ' + colorClass + ' text-white flex items-center justify-center font-black text-[10px] shadow-lg border-2 border-white"><i class="fa-solid fa-box"></i></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });

            var m = L.marker([odp.lat, odp.lng], {icon: customIcon}).addTo(odpMapInstance);
            m.bindPopup(
                '<b>' + odp.code + '</b><br>' +
                odp.location + '<br>' +
                'Kapasitas: <b>' + odp.used_ports + '/' + odp.total_ports + ' Port (' + odp.ratio + ')</b><br>' +
                'Redaman: <b style="color:green">' + odp.attenuation + '</b><br>' +
                'Induk: ' + odp.olt_source
            );
            odpMarkers[odp.code] = m;
        });
    }
});

function focusOdpMap(lat, lng, code) {
    if (odpMapInstance) {
        odpMapInstance.setView([lat, lng], 17);
        if (odpMarkers[code]) {
            odpMarkers[code].openPopup();
        }
        document.getElementById('leaflet-odp-map').scrollIntoView({behavior: 'smooth', block: 'center'});
    }
}

function filterOdpTable() {
    var input = document.getElementById("searchOdpInput");
    var filter = input.value.toLowerCase();
    var table = document.getElementById("odpTable");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        var text = tr[i].textContent || tr[i].innerText;
        if (text.toLowerCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
