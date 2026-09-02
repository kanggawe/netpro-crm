<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Manajemen ONU & Modem ONT Pelanggan";
$page_subtitle = "Monitoring armada perangkat Optical Network Unit (ONU/ONT), telemetri daya optik Rx/Tx, dan kontrol remote TR-069/OMCI.";
$active_menu = "m-noc";
require_once __DIR__ . '/../includes/header.php';

$onuList = [];
?>

<div class="space-y-6 text-xs pb-8">
    <!-- Top Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total ONU / ONT Terdaftar</span>
                <strong class="font-extrabold text-slate-900 text-xl font-mono block mt-0.5"><?= count($onuList) ?> Unit</strong>
                <span class="text-slate-400 text-[10px] font-semibold">ZTE, Huawei, Fiberhome</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-satellite-dish"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Status Online (Aktif)</span>
                <strong class="font-extrabold text-emerald-600 text-xl font-mono block mt-0.5">0 Online</strong>
                <span class="text-emerald-600 text-[10px] font-semibold">100% Service Reliability</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Offline / LOS Red</span>
                <strong class="font-extrabold text-rose-600 text-xl font-mono block mt-0.5">0 Offline</strong>
                <span class="text-slate-400 text-[10px] font-semibold">Mati Lampu / Kabel Putus</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Redaman Kritis (&gt; -24 dBm)</span>
                <strong class="font-extrabold text-amber-600 text-xl font-mono block mt-0.5">0 Perangkat</strong>
                <span class="text-slate-400 text-[10px] font-semibold">Perlu Maintenance Splicing</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-wave-square"></i>
            </div>
        </div>
    </div>

    <!-- ONU Registry Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-network-wired text-blue-600"></i> Master Armada Modem ONU & Telemetri Real-time
                </h3>
                <p class="text-slate-400 text-xs">Monitoring daya optik Rx/Tx dBm, temperatur modem, status Wi-Fi, dan kontrol remote OMCI.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="searchOnuInput" onkeyup="filterOnuTable()" placeholder="Cari SN, Pelanggan, MAC..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs focus:bg-white focus:border-blue-500 transition">
                </div>
                <button onclick="document.getElementById('modalAddOnu').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl shadow-lg shadow-blue-500/20 transition flex items-center gap-1.5 text-xs">
                    <i class="fa-solid fa-plus"></i> + Provisioning ONU
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="onuTable">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-[11px]">
                        <th class="py-3 px-4">Serial Number & MAC</th>
                        <th class="py-3 px-4">Pelanggan & CID</th>
                        <th class="py-3 px-4">Induk OLT & Port PON</th>
                        <th class="py-3 px-4">Tipe Hardware</th>
                        <th class="py-3 px-4 font-mono">Rx Optical Power</th>
                        <th class="py-3 px-4 font-mono">Tx / Suhu</th>
                        <th class="py-3 px-4 text-center">Status Link</th>
                        <th class="py-3 px-4 text-right">Kontrol Remote</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php foreach ($onuList as $onu): ?>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-3.5 px-4">
                            <strong class="font-mono text-blue-600 font-bold block text-xs"><?= htmlspecialchars($onu['sn']) ?></strong>
                            <span class="font-mono text-slate-400 text-[10px]"><?= htmlspecialchars($onu['mac']) ?></span>
                        </td>
                        <td class="py-3.5 px-4">
                            <strong class="text-slate-900 block text-xs"><?= htmlspecialchars($onu['customer_name']) ?></strong>
                            <span class="text-[10px] font-mono text-indigo-600 font-semibold"><?= htmlspecialchars($onu['cid']) ?></span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-800 block text-xs"><?= htmlspecialchars($onu['olt']) ?></span>
                            <span class="text-[10px] text-slate-500 font-mono"><?= htmlspecialchars($onu['pon']) ?> &bull; ONU #<?= $onu['onu_id'] ?></span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium">
                            <?= htmlspecialchars($onu['vendor']) ?>
                            <span class="text-[10px] text-slate-400 block font-mono">SSID: <?= htmlspecialchars($onu['wifi_ssid']) ?></span>
                        </td>
                        <td class="py-3.5 px-4 font-mono">
                            <?php if ($onu['status'] === 'OFFLINE'): ?>
                                <span class="text-rose-600 font-bold">0.0 dBm (LOS)</span>
                            <?php elseif ($onu['rx_power'] <= -24.0): ?>
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded font-bold"><?= $onu['rx_power'] ?> dBm</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded font-bold"><?= $onu['rx_power'] ?> dBm</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-[11px] text-slate-600">
                            <?= $onu['status'] === 'OFFLINE' ? '―' : '+' . $onu['tx_power'] . ' dBm' ?>
                            <span class="block text-[10px] text-slate-400"><?= $onu['temp'] ?></span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <?php if ($onu['status'] === 'ONLINE'): ?>
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold rounded-full text-[9.5px]">ONLINE</span>
                            <?php elseif ($onu['status'] === 'WARNING'): ?>
                                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 font-bold rounded-full text-[9.5px]">HIGH REDAMAN</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 font-bold rounded-full text-[9.5px]">OFFLINE (LOS)</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1.5">
                            <button onclick="triggerToast('Reboot ONT', 'Sinyal OMCI Remote Reboot berhasil dikirim ke <?= $onu['sn'] ?>!')" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition" title="Reboot Remote">
                                <i class="fa-solid fa-rotate text-xs"></i>
                            </button>
                            <button onclick="triggerToast('Wi-Fi Setup', 'Dialog TR-069 konfigurasi Wi-Fi SSID untuk <?= $onu['customer_name'] ?> dibuka.')" class="p-1.5 bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-600 rounded-lg border border-blue-200 transition" title="Atur Wi-Fi">
                                <i class="fa-solid fa-wifi text-xs"></i>
                            </button>
                            <a href="<?= base_url('crm/detail.php') ?>" class="p-1.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-600 rounded-lg border border-indigo-200 transition inline-block" title="Profil 360">
                                <i class="fa-solid fa-id-card text-xs"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Provisioning ONU Baru -->
<div id="modalAddOnu" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 text-xs max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-plus-circle text-blue-600"></i> Provisioning / Registrasi ONU Baru
            </h3>
            <button onclick="document.getElementById('modalAddOnu').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        
        <form onsubmit="event.preventDefault(); document.getElementById('modalAddOnu').classList.add('hidden'); triggerToast('ONU Berhasil Di-provision', 'Konfigurasi OMCI profile & VLAN telah terkirim ke OLT!');" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Serial Number (SN) ONU <span class="text-rose-500">*</span></label>
                    <input type="text" required placeholder="ZTEGC4129899" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-blue-600">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Pilih Induk OLT & PON Port <span class="text-rose-500">*</span></label>
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
                    <label class="font-semibold text-slate-700 block mb-1">Nama Pelanggan Terdaftar <span class="text-rose-500">*</span></label>
                    <input type="text" required placeholder="Budi Wijaya" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Tipe / Model Hardware</label>
                    <select class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                        <option value="ZTE F660 Dualband">ZTE F660 Dualband</option>
                        <option value="ZTE F670L AC1200">ZTE F670L AC1200</option>
                        <option value="Huawei HG8245H5">Huawei HG8245H5 Dualband</option>
                        <option value="Fiberhome AN5506-04">Fiberhome AN5506-04</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">VLAN Internet ID</label>
                    <input type="number" value="100" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Nama Wi-Fi SSID Bawaan</label>
                    <input type="text" value="NetPro_SuperFast_WiFi" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono">
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow-lg shadow-blue-500/25 transition">
                Kirim Konfigurasi OMCI & Aktivasi
            </button>
        </form>
    </div>
</div>

<script>
function filterOnuTable() {
    var input = document.getElementById("searchOnuInput");
    var filter = input.value.toLowerCase();
    var table = document.getElementById("onuTable");
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
