<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Manajemen ODC & Kabinet Fiber (FDT)";
$page_subtitle = "Optical Distribution Cabinet (ODC) outdoor, alokasi splitter tahap pertama (1st Stage), dan distribusi kabel feeder ke ODP.";
$active_menu = "m-noc";
require_once __DIR__ . '/../includes/header.php';

$odcList = [];
?>

<div class="space-y-6 text-xs pb-8">
    <!-- Top Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Kabinet ODC (FDT)</span>
                <strong class="font-extrabold text-slate-900 text-xl block mt-0.5"><?= count($odcList) ?> Unit Kabinet</strong>
                <span class="text-emerald-600 text-[10px] font-semibold">Semua Node Normal & Terkunci</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-cube"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Feeder Splicing</span>
                <strong class="font-extrabold text-slate-900 text-xl font-mono block mt-0.5">0 Cores</strong>
                <span class="text-slate-400 text-[10px] font-semibold">144C & 96C Pedestal Cabinets</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-diagram-project"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Splitter Tahap-1 Aktif</span>
                <strong class="font-extrabold text-emerald-600 text-xl font-mono block mt-0.5">0 Unit Splitter</strong>
                <span class="text-slate-400 text-[10px] font-semibold">PLC 1:4 & 1:8 First Stage</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-network-wired"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">ODP Downstream Terhubung</span>
                <strong class="font-extrabold text-indigo-600 text-xl font-mono block mt-0.5">0 Box ODP</strong>
                <span class="text-slate-400 text-[10px] font-semibold">Siap Melayani Pelanggan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>
    </div>

    <!-- ODC Master Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-blue-600"></i> Daftar Optical Distribution Cabinet (ODC) Lapangan
                </h3>
                <p class="text-slate-400 text-xs">Arsip titik kabinet distribusi utama outdoor, pembagian kabel feeder, dan status fisik.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('noc/odp.php') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-2 rounded-xl transition flex items-center gap-1 text-xs">
                    <i class="fa-solid fa-map-location-dot"></i> Peta ODP
                </a>
                <button onclick="document.getElementById('modalAddOdc').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl shadow-lg shadow-blue-500/20 transition flex items-center gap-1.5 text-xs">
                    <i class="fa-solid fa-plus"></i> + Tambah Kabinet ODC
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-[11px]">
                        <th class="py-3 px-4">Kode Kabinet ODC</th>
                        <th class="py-3 px-4">Nama & Lokasi Pedestal</th>
                        <th class="py-3 px-4">Kapasitas Feeder In</th>
                        <th class="py-3 px-4">Splitter Tahap-1 (1st Stage)</th>
                        <th class="py-3 px-4 font-mono">ODP Terhubung</th>
                        <th class="py-3 px-4 font-mono">Redaman Sinyal</th>
                        <th class="py-3 px-4 text-center">Status Fisik</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($odcList as $odc): ?>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                            <?= htmlspecialchars($odc['code']) ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <strong class="text-slate-900 block text-xs"><?= htmlspecialchars($odc['name']) ?></strong>
                            <span class="text-[10px] text-slate-400"><?= htmlspecialchars($odc['location']) ?></span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700">
                            <strong class="font-mono text-purple-700 block"><?= $odc['capacity_cores'] ?> Cores Tray</strong>
                            <span class="text-[10px] text-slate-500"><?= htmlspecialchars($odc['feeder_in']) ?></span>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-800">
                            <?= htmlspecialchars($odc['first_stage_splitters']) ?>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                            <?= $odc['connected_odp'] ?> Box ODP
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-600">
                            <?= htmlspecialchars($odc['attenuation_avg']) ?>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold rounded-full text-[9.5px]">
                                <?= htmlspecialchars($odc['lock_status']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-2">
                            <button onclick="triggerToast('Detail Splicing ODC', 'Skema Splicing Tray <?= $odc['code'] ?> dibuka.')" class="text-blue-600 hover:text-blue-800 font-bold hover:underline">
                                <i class="fa-solid fa-sitemap"></i> Tray Splicing
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah ODC Baru -->
<div id="modalAddOdc" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-blue-600"></i> Tambah Kabinet ODC Baru
            </h3>
            <button onclick="document.getElementById('modalAddOdc').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        
        <form onsubmit="event.preventDefault(); document.getElementById('modalAddOdc').classList.add('hidden'); triggerToast('ODC Berhasil Ditambahkan', 'Kabinet ODC baru telah tersimpan di GIS database.');" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kode Kabinet ODC <span class="text-rose-500">*</span></label>
                    <input type="text" required placeholder="ODC-BKS-01/144C" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-blue-600">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kapasitas Core Kabinet</label>
                    <select class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                        <option value="144 Core Pedestal">144 Core Outdoor Pedestal</option>
                        <option value="96 Core Pedestal">96 Core Outdoor Pedestal</option>
                        <option value="288 Core High Capacity">288 Core High Capacity</option>
                        <option value="48 Core Pole Mount">48 Core Pole Mount</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Kabinet & Lokasi Titik <span class="text-rose-500">*</span></label>
                <input type="text" required placeholder="ODC Bekasi Timur Terminal Hub" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Splitter Tahap-1 (1st Stage)</label>
                    <select class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                        <option value="PLC 1:4 (4 Way Output)">PLC 1:4 (4 Way Output)</option>
                        <option value="PLC 1:8 (8 Way Output)">PLC 1:8 (8 Way Output)</option>
                        <option value="PLC 1:2 (2 Way Output)">PLC 1:2 (2 Way Output)</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Koordinat GPS (Lat, Lng)</label>
                    <input type="text" value="-6.2870, 106.9160" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-blue-600">
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Kabel Feeder Induk (Dari POP/OTB)</label>
                <input type="text" placeholder="Feeder 48C dari OTB Rack A Core #01-48..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-800">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow-lg shadow-blue-500/25 transition">
                Simpan & Daftarkan ODC
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
