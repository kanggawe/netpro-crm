<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Kalkulator Bandwidth & Kapasitas Jaringan ISP";
$page_subtitle = "Perhitungan kebutuhan IP Transit/Upstream, rasio contention (CIR/MIR), redaman optik GPON, dan formula baku Burst Limit MikroTik.";
$active_menu = "m-kalkulator";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="space-y-6 text-xs">
    <!-- Top 4 Quick Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Rasio Retail FTTH</span>
                <strong class="text-2xl font-bold text-blue-600">1:4 s/d 1:8</strong>
                <span class="text-slate-400 block mt-0.5">Contention Ratio Berbagi</span>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-users-viewfinder"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Dedicated Corporate</span>
                <strong class="text-2xl font-bold text-emerald-600">1:1 CIR 100%</strong>
                <span class="text-emerald-600 font-medium block mt-0.5">SLA Guaranteed Bandwidth</span>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-building-shield"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Formula Baku Burst</span>
                <strong class="text-2xl font-bold text-amber-500">1.5x - 2.0x</strong>
                <span class="text-amber-600 font-medium block mt-0.5">Threshold 75% • Time 16s</span>
            </div>
            <div class="w-11 h-11 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-bolt"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Standar Redaman OPM</span>
                <strong class="text-2xl font-bold text-purple-600">-15 s/d -24 dBm</strong>
                <span class="text-purple-600 font-medium block mt-0.5">Class B+ / C+ GPON ONU</span>
            </div>
            <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-tower-broadcast"></i></div>
        </div>
    </div>

    <!-- Section 1 & 2: IP Transit & Optical Budget Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kalkulator 1: Kebutuhan Bandwidth Upstream / IP Transit -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-gauge-high text-blue-600"></i> 1. Kalkulator Kebutuhan Bandwidth Upstream
                    </h3>
                    <p class="text-slate-400">Estimasi total kapasitas pipa IP Transit berdasarkan jumlah user dan rasio.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Jumlah Pelanggan (User)</label>
                        <input type="number" id="bwUserCount" value="1245" min="1" oninput="calculateBw()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-slate-900">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Rata-rata Paket (Mbps)</label>
                        <input type="number" id="bwAvgSpeed" value="35" min="5" oninput="calculateBw()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Contention Ratio (1:N)</label>
                        <select id="bwRatio" onchange="calculateBw()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="1">1:1 (Dedicated 100%)</option>
                            <option value="4">1:4 (SOHO / Bisnis)</option>
                            <option value="8" selected>1:8 (Home Broadband Standard)</option>
                            <option value="10">1:10 (Ekonomis Residential)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Peak Concurrency (Jam Sibuk)</label>
                        <select id="bwConcurrency" onchange="calculateBw()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="0.70">70% User Online Bersamaan</option>
                            <option value="0.80" selected>80% User Online Bersamaan</option>
                            <option value="0.90">90% User Online Bersamaan</option>
                            <option value="1.00">100% Full Load</option>
                        </select>
                    </div>
                </div>

                <!-- Result Box -->
                <div class="p-5 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-semibold">Total Akumulasi Kecepatan Paket:</span>
                        <strong id="bwTotalSubscribed" class="font-mono text-slate-900 text-sm">43.575 Mbps</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-semibold">Kapasitas Minimum IP Transit (CIR):</span>
                        <strong id="bwMinTransit" class="font-mono text-blue-600 text-sm">4.358 Mbps (4.36 Gbps)</strong>
                    </div>
                    <div class="border-t border-blue-200/80 pt-2 flex justify-between items-center">
                        <span class="text-slate-800 font-extrabold">Rekomendasi Pipa Uplink Upstream:</span>
                        <span id="bwRecommendedPipe" class="px-3 py-1 bg-blue-600 text-white font-mono font-bold rounded-xl text-base shadow">5 Gbps (LAG Bundle)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kalkulator 2: Optical Power Budget GPON (Redaman) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-tower-broadcast text-purple-600"></i> 2. Kalkulator Redaman Optik (Power Budget)
                    </h3>
                    <p class="text-slate-400">Hitung estimasi redaman dBm dari port OLT ke ONT pelanggan.</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">TX Output Power OLT (dBm)</label>
                        <input type="number" id="optOltPower" value="4.5" step="0.1" oninput="calculateOpt()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-purple-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Jarak Kabel Fiber (KM)</label>
                        <input type="number" id="optDistance" value="1.8" step="0.1" min="0" oninput="calculateOpt()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-slate-900">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Splitter Utama (ODC / FDT)</label>
                        <select id="optSplitter1" onchange="calculateOpt()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="0">Tanpa Splitter (0 dB)</option>
                            <option value="3.5">1:2 (-3.5 dB)</option>
                            <option value="7.2">1:4 (-7.2 dB)</option>
                            <option value="10.5" selected>1:8 (-10.5 dB)</option>
                            <option value="13.8">1:16 (-13.8 dB)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Splitter Distribusi (ODP / FAT)</label>
                        <select id="optSplitter2" onchange="calculateOpt()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="0">Tanpa Splitter (0 dB)</option>
                            <option value="7.2">1:4 (-7.2 dB)</option>
                            <option value="10.5" selected>1:8 (-10.5 dB)</option>
                            <option value="13.8">1:16 (-13.8 dB)</option>
                        </select>
                    </div>
                </div>

                <!-- Result Box -->
                <div class="p-5 bg-gradient-to-br from-purple-50 to-fuchsia-50 border border-purple-200 rounded-2xl space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-semibold">Total Redaman Redaman Fiber (0.35 dB/km):</span>
                        <strong id="optFiberLoss" class="font-mono text-slate-900 text-sm">-0.63 dB</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-semibold">Total Redaman Splitter (ODC + ODP):</span>
                        <strong id="optSplitterLoss" class="font-mono text-purple-600 text-sm">-21.00 dB</strong>
                    </div>
                    <div class="border-t border-purple-200/80 pt-2 flex justify-between items-center">
                        <span class="text-slate-800 font-extrabold">Estimasi RX Optical Power di ONT:</span>
                        <span id="optRxResult" class="px-3 py-1 bg-emerald-600 text-white font-mono font-bold rounded-xl text-base shadow">-17.63 dBm (EXCELLENT)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Kalkulator Burst Limit MikroTik (Standar Baku ISP) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-amber-500"></i> 3. Kalkulator Burst Limit MikroTik Sesuai Aturan Baku ISP
                </h3>
                <p class="text-slate-400">Formula baku: <code>Burst-Limit (1.5x - 2.0x)</code>, <code>Burst-Threshold (75% Max-Limit)</code>, dan <code>Durasi Akselerasi = Burst-Time × (Threshold / Burst-Limit)</code>.</p>
            </div>
            <span class="px-3 py-1 bg-amber-50 text-amber-700 font-bold rounded-full border border-amber-200 text-[10px]">
                MIKROTIK ROUTEROS STANDARD
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Controls: Parameters (5 Cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Max Download (MIR Mbps)</label>
                        <input type="number" id="burstMaxDown" value="20" min="1" oninput="calculateBurst()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-blue-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Max Upload (MIR Mbps)</label>
                        <input type="number" id="burstMaxUp" value="20" min="1" oninput="calculateBurst()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-indigo-600">
                    </div>
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Faktor Pengali Burst-Limit (Puncak Sesaat)</label>
                    <select id="burstMultiplier" onchange="calculateBurst()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                        <option value="1.5">1.50x Max-Limit (Standar Konservatif / Stabil)</option>
                        <option value="1.75" selected>1.75x Max-Limit (Standar Rekomendasi ISP)</option>
                        <option value="2.0">2.00x Max-Limit (Ultra Web-Boost / Super Cepat)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Burst Threshold (%)</label>
                        <select id="burstThresholdPct" onchange="calculateBurst()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="0.70">70% dari Max-Limit</option>
                            <option value="0.75" selected>75% dari Max-Limit (Baku)</option>
                            <option value="0.80">80% dari Max-Limit</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Burst-Time (Detik)</label>
                        <select id="burstTimeVal" onchange="calculateBurst()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="8">8 Detik (Fast Webpage)</option>
                            <option value="16" selected>16 Detik (Rekomendasi ISP)</option>
                            <option value="32">32 Detik (Long Burst)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Right Controls: Live Calculation & Script Output (7 Cols) -->
            <div class="lg:col-span-7 space-y-4">
                <!-- Result Breakdown -->
                <div class="p-5 bg-gradient-to-br from-amber-50/70 to-orange-50/50 border border-amber-200 rounded-2xl space-y-3">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="bg-white p-2.5 rounded-xl border border-amber-200">
                            <span class="text-slate-400 block text-[10px]">Max-Limit (MIR)</span>
                            <strong id="resMaxLimit" class="font-mono text-slate-900 font-bold text-xs">20M/20M</strong>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-amber-200">
                            <span class="text-slate-400 block text-[10px]">Burst-Limit</span>
                            <strong id="resBurstLimit" class="font-mono text-amber-600 font-bold text-xs">35M/35M</strong>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-amber-200">
                            <span class="text-slate-400 block text-[10px]">Threshold</span>
                            <strong id="resThreshold" class="font-mono text-blue-600 font-bold text-xs">15M/15M</strong>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-amber-200">
                            <span class="text-slate-400 block text-[10px]">Durasi Efektif</span>
                            <strong id="resDuration" class="font-mono text-emerald-600 font-bold text-xs">6.86 Detik</strong>
                        </div>
                    </div>

                    <div class="text-[11px] text-slate-600 bg-white/70 p-3 rounded-xl border border-amber-100 leading-relaxed">
                        💡 <strong>Cara Kerja Burst:</strong> Saat pelanggan membuka website/sosmed, sistem memberikan kecepatan akselerasi hingga <strong id="descBurstSpd" class="text-amber-700">35 Mbps</strong> selama <strong id="descDuration" class="text-emerald-700">6.9 detik</strong> (halaman langsung terbuka instan). Jika pelanggan melanjutkan download file besar secara non-stop, kecepatan diturunkan secara halus kembali ke <strong id="descMaxSpd" class="text-slate-900">20 Mbps</strong>.
                    </div>
                </div>

                <!-- MikroTik Simple Queue Script Output -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="font-bold text-slate-700 text-[11px]">Command MikroTik Simple Queue (Siap Copy):</label>
                        <button onclick="copyToClipboard('scriptMikrotikQueue')" class="text-blue-600 font-bold hover:underline text-[10px]">
                            <i class="fa-solid fa-copy"></i> Salin Script
                        </button>
                    </div>
                    <pre id="scriptMikrotikQueue" class="bg-slate-900 text-amber-400 p-3 rounded-xl font-mono text-[11px] overflow-x-auto border border-slate-800 select-all leading-tight"></pre>
                </div>

                <!-- RADIUS Mikrotik-Rate-Limit Attribute -->
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="font-bold text-slate-700 text-[11px]">Format RADIUS Attribute <code>Mikrotik-Rate-Limit</code>:</label>
                        <button onclick="copyToClipboard('scriptRadiusRate')" class="text-blue-600 font-bold hover:underline text-[10px]">
                            <i class="fa-solid fa-copy"></i> Salin RADIUS String
                        </button>
                    </div>
                    <pre id="scriptRadiusRate" class="bg-slate-900 text-emerald-400 p-3 rounded-xl font-mono text-[11px] overflow-x-auto border border-slate-800 select-all leading-tight"></pre>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateBw() {
    var users = parseFloat(document.getElementById('bwUserCount').value) || 0;
    var speed = parseFloat(document.getElementById('bwAvgSpeed').value) || 0;
    var ratio = parseFloat(document.getElementById('bwRatio').value) || 1;
    var conc = parseFloat(document.getElementById('bwConcurrency').value) || 0.8;

    var totalSub = users * speed;
    var cir = (totalSub / ratio) * conc;
    var gbps = cir / 1000;

    document.getElementById('bwTotalSubscribed').innerText = totalSub.toLocaleString('id-ID') + ' Mbps (' + (totalSub/1000).toFixed(2) + ' Gbps)';
    document.getElementById('bwMinTransit').innerText = Math.round(cir).toLocaleString('id-ID') + ' Mbps (' + gbps.toFixed(2) + ' Gbps)';

    var pipe = "1 Gbps";
    if (gbps > 10) pipe = Math.ceil(gbps / 10) * 10 + " Gbps (Multiple 10G Link)";
    else if (gbps > 5) pipe = "10 Gbps (10G SFP+)";
    else if (gbps > 2.5) pipe = "5 Gbps (LAG Bundle)";
    else if (gbps > 1) pipe = "2.5 Gbps / 5 Gbps";
    else pipe = "1 Gbps SFP";

    document.getElementById('bwRecommendedPipe').innerText = pipe;
}

function calculateOpt() {
    var olt = parseFloat(document.getElementById('optOltPower').value) || 4.5;
    var dist = parseFloat(document.getElementById('optDistance').value) || 0;
    var sp1 = parseFloat(document.getElementById('optSplitter1').value) || 0;
    var sp2 = parseFloat(document.getElementById('optSplitter2').value) || 0;

    var fiberLoss = dist * 0.35;
    var spliceLoss = 0.5;
    var totalLoss = fiberLoss + sp1 + sp2 + spliceLoss;
    var rx = olt - totalLoss;

    document.getElementById('optFiberLoss').innerText = '-' + fiberLoss.toFixed(2) + ' dB (termasuk sambungan)';
    document.getElementById('optSplitterLoss').innerText = '-' + (sp1 + sp2).toFixed(2) + ' dB';

    var badge = document.getElementById('optRxResult');
    badge.innerText = rx.toFixed(2) + ' dBm';
    
    if (rx > -22 && rx < -12) {
        badge.className = "px-3 py-1 bg-emerald-600 text-white font-mono font-bold rounded-xl text-base shadow";
        badge.innerText = rx.toFixed(2) + ' dBm (EXCELLENT)';
    } else if (rx >= -25 && rx <= -22) {
        badge.className = "px-3 py-1 bg-blue-600 text-white font-mono font-bold rounded-xl text-base shadow";
        badge.innerText = rx.toFixed(2) + ' dBm (GOOD)';
    } else if (rx >= -28 && rx < -25) {
        badge.className = "px-3 py-1 bg-amber-500 text-white font-mono font-bold rounded-xl text-base shadow";
        badge.innerText = rx.toFixed(2) + ' dBm (WARNING MARGINAL)';
    } else {
        badge.className = "px-3 py-1 bg-rose-600 text-white font-mono font-bold rounded-xl text-base shadow";
        badge.innerText = rx.toFixed(2) + ' dBm (LOS / UNFEASIBLE)';
    }
}

function calculateBurst() {
    var maxDown = parseFloat(document.getElementById('burstMaxDown').value) || 20;
    var maxUp = parseFloat(document.getElementById('burstMaxUp').value) || 20;
    var multiplier = parseFloat(document.getElementById('burstMultiplier').value) || 1.75;
    var threshPct = parseFloat(document.getElementById('burstThresholdPct').value) || 0.75;
    var burstTime = parseFloat(document.getElementById('burstTimeVal').value) || 16;

    var burstDown = Math.round(maxDown * multiplier);
    var burstUp = Math.round(maxUp * multiplier);

    var threshDown = Math.round(maxDown * threshPct);
    var threshUp = Math.round(maxUp * threshPct);

    // Formula Baku MikroTik: Duration = Burst-Time * (Burst-Threshold / Burst-Limit)
    var duration = burstTime * (threshDown / burstDown);

    document.getElementById('resMaxLimit').innerText = maxUp + 'M/' + maxDown + 'M';
    document.getElementById('resBurstLimit').innerText = burstUp + 'M/' + burstDown + 'M';
    document.getElementById('resThreshold').innerText = threshUp + 'M/' + threshDown + 'M';
    document.getElementById('resDuration').innerText = duration.toFixed(1) + ' Detik';

    document.getElementById('descBurstSpd').innerText = burstDown + ' Mbps';
    document.getElementById('descDuration').innerText = duration.toFixed(1) + ' detik';
    document.getElementById('descMaxSpd').innerText = maxDown + ' Mbps';

    var queueCmd = `/queue simple add name="PPPoE-User" target=10.100.20.0/24 \\\n  max-limit=${maxUp}M/${maxDown}M \\\n  burst-limit=${burstUp}M/${burstDown}M \\\n  burst-threshold=${threshUp}M/${threshDown}M \\\n  burst-time=${burstTime}s/${burstTime}s \\\n  limit-at=${Math.round(maxUp*0.25)}M/${Math.round(maxDown*0.25)}M priority=8/8`;
    document.getElementById('scriptMikrotikQueue').innerText = queueCmd;

    var radiusStr = `${maxUp}M/${maxDown}M ${burstUp}M/${burstDown}M ${threshUp}M/${threshDown}M ${burstTime}/${burstTime} 8 ${Math.round(maxUp*0.25)}M/${Math.round(maxDown*0.25)}M`;
    document.getElementById('scriptRadiusRate').innerText = radiusStr;
}

function copyToClipboard(elemId) {
    var text = document.getElementById(elemId).innerText;
    navigator.clipboard.writeText(text).then(function() {
        triggerToast('Tersalin', 'Script konfigurasi berhasil disalin ke clipboard!');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    calculateBw();
    calculateOpt();
    calculateBurst();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
