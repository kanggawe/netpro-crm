<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Manajemen OTB & ODF Fiber Optik";
$page_subtitle = "Manajemen Optical Termination Box (OTB), frame terminasi ODF server room, alokasi core feeder, dan kode warna kabel.";
$active_menu = "m-noc";
require_once __DIR__ . '/../includes/header.php';

// Color coding standards (TIA/EIA-598 12 Colors)
$fiberColors = [
    1 => ['name' => 'Biru', 'bg' => 'bg-blue-600', 'text' => 'text-white'],
    2 => ['name' => 'Oranye', 'bg' => 'bg-amber-600', 'text' => 'text-white'],
    3 => ['name' => 'Hijau', 'bg' => 'bg-emerald-600', 'text' => 'text-white'],
    4 => ['name' => 'Cokelat', 'bg' => 'bg-amber-900', 'text' => 'text-white'],
    5 => ['name' => 'Abu-abu', 'bg' => 'bg-slate-500', 'text' => 'text-white'],
    6 => ['name' => 'Putih', 'bg' => 'bg-slate-100 border border-slate-300', 'text' => 'text-slate-800'],
    7 => ['name' => 'Merah', 'bg' => 'bg-rose-600', 'text' => 'text-white'],
    8 => ['name' => 'Hitam', 'bg' => 'bg-slate-900', 'text' => 'text-white'],
    9 => ['name' => 'Kuning', 'bg' => 'bg-yellow-400', 'text' => 'text-slate-900'],
    10 => ['name' => 'Ungu', 'bg' => 'bg-purple-600', 'text' => 'text-white'],
    11 => ['name' => 'Pink', 'bg' => 'bg-pink-500', 'text' => 'text-white'],
    12 => ['name' => 'Toska', 'bg' => 'bg-teal-500', 'text' => 'text-white'],
];

$otbList = [];

$totalOtbCores = 0;
$totalOtbUsed = 0;
foreach ($otbList as $o) {
    $totalOtbCores += $o['total_cores'];
    $totalOtbUsed += $o['used_cores'];
}
?>

<div class="space-y-6 text-xs pb-8">
    <!-- Top Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Unit OTB & ODF</span>
                <strong class="font-extrabold text-slate-900 text-xl block mt-0.5"><?= count($otbList) ?> Unit Frame</strong>
                <span class="text-blue-600 text-[10px] font-semibold">Rackmount & Wallmount</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-server"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Kapasitas Core Feeder</span>
                <strong class="font-extrabold text-slate-900 text-xl font-mono block mt-0.5"><?= $totalOtbCores ?> Cores</strong>
                <span class="text-purple-600 text-[10px] font-semibold">Single Mode G.652D</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-circle-nodes"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Core Terterminasi (Patch)</span>
                <strong class="font-extrabold text-emerald-600 text-xl font-mono block mt-0.5"><?= $totalOtbUsed ?> Cores</strong>
                <span class="text-emerald-600 text-[10px] font-semibold"><?= round(($totalOtbUsed / max(1, $totalOtbCores)) * 100) ?>% Terpakai</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-plug"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Core Cadangan (Spare)</span>
                <strong class="font-extrabold text-cyan-600 text-xl font-mono block mt-0.5"><?= $totalOtbCores - $totalOtbUsed ?> Cores</strong>
                <span class="text-cyan-600 text-[10px] font-semibold">Ready for Expansion</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-network-wired"></i>
            </div>
        </div>
    </div>

    <!-- TIA/EIA-598 Standard 12-Color Fiber Visual Guide -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-palette text-blue-600"></i> Panduan Standar Kode Warna Tube & Core Fiber Optik (TIA/EIA-598)
                </h3>
                <p class="text-slate-400 text-xs">Urutan nomor core 1 s/d 12 untuk penyambungan kabel feeder & distribusi (Splicing Guide).</p>
            </div>
            <span class="text-[10px] text-slate-400 font-mono">12-Color Fiber Code Standard</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-2.5">
            <?php foreach ($fiberColors as $num => $col): ?>
            <div class="p-2.5 rounded-xl border border-slate-200 flex items-center gap-2.5 bg-slate-50/50">
                <div class="w-6 h-6 rounded-lg <?= $col['bg'] ?> <?= $col['text'] ?> font-black flex items-center justify-center text-[10px] shadow-xs shrink-0">
                    <?= $num ?>
                </div>
                <div class="overflow-hidden">
                    <strong class="text-slate-800 text-xs block font-bold truncate"><?= $col['name'] ?></strong>
                    <span class="text-[9.5px] text-slate-400 font-mono block">Core #<?= str_pad($num, 2, '0', STR_PAD_LEFT) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- OTB & ODF Master List Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-boxes-packing text-emerald-600"></i> Daftar Frame OTB & ODF Server Room
                </h3>
                <p class="text-slate-400 text-xs">Arsip panel terminasi kabel optik, konektor adaptor, dan jalur kabel feeder.</p>
            </div>
            <button onclick="document.getElementById('modalAddOtb').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl shadow-lg shadow-blue-500/20 transition flex items-center gap-1.5 text-xs">
                <i class="fa-solid fa-plus"></i> + Tambah Unit OTB
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-[11px]">
                        <th class="py-3 px-4">Kode Frame OTB</th>
                        <th class="py-3 px-4">Nama & Lokasi OTB</th>
                        <th class="py-3 px-4">Tipe & Adaptor</th>
                        <th class="py-3 px-4">Jalur Kabel Feeder Target</th>
                        <th class="py-3 px-4">Alokasi Core</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($otbList as $otb): 
                        $pct = round(($otb['used_cores'] / $otb['total_cores']) * 100);
                    ?>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                            <?= htmlspecialchars($otb['code']) ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <strong class="text-slate-900 block text-xs"><?= htmlspecialchars($otb['name']) ?></strong>
                            <span class="text-[10px] text-slate-400"><?= htmlspecialchars($otb['location']) ?></span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-800 block text-[11px]"><?= htmlspecialchars($otb['type']) ?></span>
                            <span class="text-[10px] text-purple-700 font-mono font-semibold"><?= htmlspecialchars($otb['connector']) ?></span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-600 font-medium text-xs">
                            <i class="fa-solid fa-arrow-right-arrow-left text-slate-400 mr-1 text-[10px]"></i>
                            <?= htmlspecialchars($otb['target_cable']) ?>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="space-y-1 w-32">
                                <div class="flex justify-between text-[10px]">
                                    <strong class="text-slate-800"><?= $otb['used_cores'] ?> / <?= $otb['total_cores'] ?> Core</strong>
                                    <span class="text-slate-400 font-mono"><?= $otb['spare_cores'] ?> Spare</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full <?= $pct >= 90 ? 'bg-amber-500' : 'bg-emerald-500' ?>" style="width: <?= $pct ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold rounded-full text-[10px]">
                                <?= htmlspecialchars($otb['status']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-2">
                            <button onclick="triggerToast('Detail Core Tray', 'Visualisasi 96-Tray Core Splicing <?= $otb['code'] ?> dibuka.')" class="text-blue-600 hover:text-blue-800 font-bold hover:underline">
                                <i class="fa-solid fa-list-check"></i> Tray Splicing
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah OTB Baru -->
<div id="modalAddOtb" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-blue-600"></i> Tambah Frame OTB / ODF Baru
            </h3>
            <button onclick="document.getElementById('modalAddOtb').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        
        <form onsubmit="event.preventDefault(); document.getElementById('modalAddOtb').classList.add('hidden'); triggerToast('OTB Berhasil Ditambahkan', 'Frame OTB baru telah terdaftar di database.');" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kode Frame OTB <span class="text-rose-500">*</span></label>
                    <input type="text" required placeholder="OTB-RACK-C01" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-blue-600">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Tipe Frame / Enclosure</label>
                    <select class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                        <option value="Rackmount 19 Inch 1U (24 Core)">Rackmount 19" 1U (24 Core)</option>
                        <option value="Rackmount 19 Inch 2U (48 Core)">Rackmount 19" 2U (48 Core)</option>
                        <option value="Rackmount 19 Inch 3U (96 Core)" selected>Rackmount 19" 3U (96 Core)</option>
                        <option value="Rackmount 19 Inch 4U (144 Core)">Rackmount 19" 4U (144 Core)</option>
                        <option value="Wallmount Box Outdoor">Wallmount Box Outdoor</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama ODF & Lokasi Rack <span class="text-rose-500">*</span></label>
                <input type="text" required placeholder="ODF Feeder Timur Rack C-01" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Tipe Konektor Adaptor</label>
                    <select class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-purple-700">
                        <option value="SC/UPC Duplex (Blue)">SC/UPC Duplex (Blue)</option>
                        <option value="SC/APC Simplex (Green)">SC/APC Simplex (Green)</option>
                        <option value="LC/UPC Duplex">LC/UPC Duplex</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Total Kapasitas Core</label>
                    <input type="number" value="96" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Jalur Kabel Feeder Tujuan</label>
                <input type="text" placeholder="Kabel Feeder 96C ke ODC Cluster Timur..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5">
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow-lg shadow-blue-500/25 transition">
                Simpan & Daftarkan OTB
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
