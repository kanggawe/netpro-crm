<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Manajemen OLT GPON & PON Ports";
$page_subtitle = "Monitoring chassis OLT ZTE, Huawei, & Fiberhome, telemetri daya laser optik, dan registrasi ONU ONT.";
$active_menu = "m-noc";
require_once __DIR__ . '/../includes/header.php';

$oltChassis = [];
$ponPorts = [];
$unregisteredOnts = [];
?>

<div class="space-y-6 text-xs pb-8">
    <!-- Top KPI Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total OLT Chassis</span>
                <strong class="font-extrabold text-slate-900 text-xl block mt-0.5"><?= count($oltChassis) ?> Unit OLT</strong>
                <span class="text-emerald-600 text-[10px] font-semibold">100% Online UP</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-server"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total PON Optical Ports</span>
                <strong class="font-extrabold text-slate-900 text-xl font-mono block mt-0.5">0 Port</strong>
                <span class="text-slate-400 text-[10px] font-semibold">SFP Class C+ / C++ Transceivers</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-tower-broadcast"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">ONT Terdaftar (Active)</span>
                <strong class="font-extrabold text-emerald-600 text-xl font-mono block mt-0.5">0 ONT</strong>
                <span class="text-slate-500 text-[10px]">0 ONT Offline</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-network-wired"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Unregistered Discovery</span>
                <strong class="font-extrabold text-amber-600 text-xl font-mono block mt-0.5"><?= count($unregisteredOnts) ?> ONT Baru</strong>
                <span class="text-amber-600 text-[10px] font-semibold">Menunggu Aktivasi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-satellite-dish"></i>
            </div>
        </div>
    </div>

    <!-- Auto-Discovery Unconfigured ONT Alert Box -->
    <?php if (!empty($unregisteredOnts)): ?>
    <div class="bg-amber-500/10 border-2 border-amber-500/30 rounded-2xl p-5 text-slate-800 space-y-3">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
                <h4 class="font-bold text-amber-900 text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-bolt text-amber-600"></i> Auto-Discovery: Terdeteksi <?= count($unregisteredOnts) ?> Perangkat ONT Baru di Jaringan OLT!
                </h4>
            </div>
            <span class="text-[10px] text-amber-700 font-bold">Plug & Play Auto-Registration</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <?php foreach ($unregisteredOnts as $un): ?>
            <div class="bg-white p-3.5 rounded-xl border border-amber-200 shadow-xs flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2">
                        <strong class="font-mono text-slate-900 text-xs"><?= htmlspecialchars($un['sn']) ?></strong>
                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-700 font-bold rounded text-[9.5px]"><?= htmlspecialchars($un['vendor']) ?></span>
                    </div>
                    <span class="text-[10px] text-slate-500 block"><?= htmlspecialchars($un['olt']) ?> &bull; <strong class="text-blue-600"><?= htmlspecialchars($un['pon']) ?></strong> &bull; Redaman: <strong class="text-emerald-600"><?= htmlspecialchars($un['rx_power']) ?></strong></span>
                </div>
                <a href="<?= base_url('crm/registrasi.php') ?>" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-3 py-1.5 rounded-lg shadow-xs transition text-[11px] flex items-center gap-1">
                    <i class="fa-solid fa-plus-circle"></i> Aktivasi Akun
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- OLT Chassis Hardware Monitoring Cards -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-microchip text-blue-600"></i> Chassis Hardware OLT & Telemetri Lingkungan
                </h3>
                <p class="text-slate-400 text-xs">Informasi CPU load, suhu controller board, status catu daya PSU, dan firmware.</p>
            </div>
            <button onclick="triggerToast('Koneksi OLT', 'Sinkronisasi CLI & SNMP OLT berhasil diperbarui.')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-xl border border-slate-300 transition flex items-center gap-1 text-xs">
                <i class="fa-solid fa-arrows-rotate"></i> Refresh SNMP Telemetri
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($oltChassis as $olt): ?>
            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 hover:border-blue-400 transition shadow-xs">
                <div class="flex justify-between items-start">
                    <div>
                        <strong class="font-bold text-slate-900 text-xs block"><?= htmlspecialchars($olt['name']) ?></strong>
                        <span class="text-[10px] text-slate-500 font-mono"><?= htmlspecialchars($olt['model']) ?></span>
                    </div>
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold rounded text-[9px]">ONLINE</span>
                </div>

                <div class="space-y-1.5 text-[11px] text-slate-700">
                    <div class="flex justify-between"><span class="text-slate-400">IP Management:</span><strong class="font-mono text-blue-600"><?= htmlspecialchars($olt['ip']) ?></strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">Lokasi Rack:</span><span class="text-slate-600"><?= htmlspecialchars($olt['location']) ?></span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Slot PON Aktif:</span><strong class="text-purple-700 font-mono"><?= $olt['pon_active'] ?> / <?= $olt['pon_slots'] ?> Port</strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">ONT Terhubung:</span><strong class="text-emerald-600 font-bold"><?= $olt['ont_online'] ?> Online</strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">CPU & Suhu:</span><strong class="text-slate-800 font-mono"><?= $olt['cpu'] ?> &bull; <?= $olt['temp'] ?></strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">Power PSU:</span><span class="text-[10px] text-slate-600 font-medium"><?= htmlspecialchars($olt['power_status']) ?></span></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- PON Ports Optical Matrix Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-table-cells text-emerald-600"></i> Matriks Port PON & Daya Laser Transceiver Optik
                </h3>
                <p class="text-slate-400 text-xs">Monitoring per-port PON GPON, alokasi kapasitas ONT, dan daya transmit/receive (Tx/Rx).</p>
            </div>
            <a href="<?= base_url('noc/odp.php') ?>" class="bg-blue-50 text-blue-700 font-bold px-3 py-1.5 rounded-xl border border-blue-200 text-xs hover:bg-blue-600 hover:text-white transition flex items-center gap-1.5">
                <i class="fa-solid fa-map-location-dot"></i> Peta ODP Distribusi
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-[11px]">
                        <th class="py-3 px-4">Port PON</th>
                        <th class="py-3 px-4">Induk OLT</th>
                        <th class="py-3 px-4">Tipe Transceiver SFP</th>
                        <th class="py-3 px-4">Jumlah ONT / Max</th>
                        <th class="py-3 px-4 font-mono">Daya Laser Tx</th>
                        <th class="py-3 px-4 font-mono">Rata-rata Rx Power</th>
                        <th class="py-3 px-4 text-center">Status PON</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($ponPorts as $pon): 
                        $pct = round(($pon['ont_count'] / $pon['ont_max']) * 100);
                    ?>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($pon['port']) ?></td>
                        <td class="py-3.5 px-4 font-semibold text-slate-800"><?= htmlspecialchars($pon['olt']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-600"><?= htmlspecialchars($pon['sfp_type']) ?></td>
                        <td class="py-3.5 px-4">
                            <div class="space-y-1 w-28">
                                <div class="flex justify-between text-[10px]">
                                    <strong class="text-slate-800"><?= $pon['ont_count'] ?> / <?= $pon['ont_max'] ?></strong>
                                    <span class="text-slate-400 font-mono"><?= $pct ?>%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full <?= $pct >= 95 ? 'bg-rose-500' : 'bg-emerald-500' ?>" style="width: <?= $pct ?>%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($pon['tx_power']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-600"><?= htmlspecialchars($pon['avg_rx']) ?></td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="px-2 py-0.5 <?= $pon['status'] === 'FULL' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200' ?> border font-bold rounded text-[9.5px]">
                                <?= htmlspecialchars($pon['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
