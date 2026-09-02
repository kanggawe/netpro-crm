<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Kalkulator Pajak ISP, PPN & Regulasi Kominfo";
$page_subtitle = "Simulasi perhitungan PPN 11%/12% (Include vs Exclude), e-Bupot PPh 23, PPh 21 TER, dan Iuran Kominfo USO/BHP.";
$active_menu = "m-kalkulator";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="space-y-6 text-xs">
    <!-- Top 3 Regulatory Standard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Tarif PPN UU HPP</span>
                <strong class="text-2xl font-bold text-blue-600">11% & 12%</strong>
                <span class="text-slate-400 block mt-0.5">Include & Exclude DPP</span>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-percent"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Potongan Jasa PPh 23</span>
                <strong class="text-2xl font-bold text-emerald-600">2.0% (NPWP)</strong>
                <span class="text-emerald-600 font-medium block mt-0.5">e-Bupot 23 Unifikasi</span>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-file-invoice-dollar"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Iuran PNBP Kominfo</span>
                <strong class="text-2xl font-bold text-indigo-600">1.75% Total</strong>
                <span class="text-indigo-600 font-medium block mt-0.5">USO 1.25% + BHP 0.50%</span>
            </div>
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-scale-balanced"></i></div>
        </div>
    </div>

    <!-- Main 2 Tax Calculators Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kalkulator 1: PPN 11% / 12% Breakdown -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-blue-600"></i> Kalkulator PPN Tagihan & Faktur Pajak
                </h3>
                <p class="text-slate-400">Pemisahan otomatis Dasar Pengenaan Pajak (DPP) dan Pajak Pertambahan Nilai.</p>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nominal Nilai Transaksi (Rp)</label>
                        <input type="number" id="taxTransAmount" value="250000" step="1000" oninput="calculateTax()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-slate-900">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Tarif PPN Berlaku</label>
                        <select id="taxPpnRate" onchange="calculateTax()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="0.11" selected>PPN 11% (UU HPP Berlaku)</option>
                            <option value="0.12">PPN 12% (Proyeksi 2025/2026)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Skema Input Tarif</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="p-3 border border-slate-200 rounded-xl flex items-center gap-2 cursor-pointer has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="taxScheme" value="include" checked onchange="calculateTax()" class="text-blue-600">
                            <div>
                                <strong class="block font-bold text-slate-900">Include PPN</strong>
                                <span class="text-[10px] text-slate-400">Harga sudah termasuk PPN</span>
                            </div>
                        </label>
                        <label class="p-3 border border-slate-200 rounded-xl flex items-center gap-2 cursor-pointer has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                            <input type="radio" name="taxScheme" value="exclude" onchange="calculateTax()" class="text-blue-600">
                            <div>
                                <strong class="block font-bold text-slate-900">Exclude PPN</strong>
                                <span class="text-[10px] text-slate-400">PPN ditambahkan di atas tarif</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Result Box -->
                <div class="p-5 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-semibold">Dasar Pengenaan Pajak (DPP):</span>
                        <strong id="resDpp" class="font-mono text-slate-900 text-sm">Rp 225.225</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-semibold">Pajak Pertambahan Nilai (PPN):</span>
                        <strong id="resPpn" class="font-mono text-emerald-600 text-sm">Rp 24.775</strong>
                    </div>
                    <div class="border-t border-blue-200/80 pt-2 flex justify-between items-center">
                        <span class="text-slate-800 font-extrabold">Total Tagihan Final (Invoice):</span>
                        <span id="resTotal" class="px-3 py-1 bg-blue-600 text-white font-mono font-bold rounded-xl text-base shadow">Rp 250.000</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kalkulator 2: Iuran Kominfo PNBP & e-Bupot PPh 23 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-scale-balanced text-indigo-600"></i> Kalkulator PNBP Kominfo (USO & BHP)
                </h3>
                <p class="text-slate-400">Kalkulasi kewajiban iuran pendapatan kotor ISP sesuai PP No. 46 / 2021.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Pendapatan Kotor Bruto ISP Bulan/Tahun (Rp)</label>
                    <input type="number" id="pnbpGross" value="128450000" step="1000000" oninput="calculatePnbp()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-slate-900">
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Biaya Pengurang Sah (Beban Interkoneksi / Sewa Jaringan IP Transit)</label>
                    <input type="number" id="pnbpDeduction" value="28450000" step="500000" oninput="calculatePnbp()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-rose-600">
                    <span class="text-[10px] text-slate-400 block mt-1">Sesuai aturan PM Kominfo: Biaya interkoneksi dapat mengurangi dasar pengenaan PNBP.</span>
                </div>

                <!-- Result Box -->
                <div class="p-5 bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-2xl space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-semibold">Dasar Pengenaan PNBP Bersih:</span>
                        <strong id="resPnbpNet" class="font-mono text-slate-900 text-sm">Rp 100.000.000</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-semibold">Iuran Kontribusi USO (1.25%):</span>
                        <strong id="resUso" class="font-mono text-indigo-600 text-sm">Rp 1.250.000</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600 font-semibold">Iuran BHP Telekomunikasi (0.50%):</span>
                        <strong id="resBhp" class="font-mono text-indigo-600 text-sm">Rp 500.000</strong>
                    </div>
                    <div class="border-t border-indigo-200/80 pt-2 flex justify-between items-center">
                        <span class="text-slate-800 font-extrabold">Total Setoran PNBP ke Kas Negara:</span>
                        <span id="resTotalPnbp" class="px-3 py-1 bg-indigo-600 text-white font-mono font-bold rounded-xl text-base shadow">Rp 1.750.000</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateTax() {
    var amount = parseFloat(document.getElementById('taxTransAmount').value) || 0;
    var rate = parseFloat(document.getElementById('taxPpnRate').value) || 0.11;
    var isInclude = document.querySelector('input[name="taxScheme"]:checked').value === 'include';

    var dpp = 0;
    var ppn = 0;
    var total = 0;

    if (isInclude) {
        dpp = Math.round(amount / (1 + rate));
        ppn = amount - dpp;
        total = amount;
    } else {
        dpp = amount;
        ppn = Math.round(amount * rate);
        total = dpp + ppn;
    }

    document.getElementById('resDpp').innerText = 'Rp ' + dpp.toLocaleString('id-ID');
    document.getElementById('resPpn').innerText = 'Rp ' + ppn.toLocaleString('id-ID');
    document.getElementById('resTotal').innerText = 'Rp ' + total.toLocaleString('id-ID');
}

function calculatePnbp() {
    var gross = parseFloat(document.getElementById('pnbpGross').value) || 0;
    var ded = parseFloat(document.getElementById('pnbpDeduction').value) || 0;

    var net = Math.max(0, gross - ded);
    var uso = Math.round(net * 0.0125);
    var bhp = Math.round(net * 0.0050);
    var total = uso + bhp;

    document.getElementById('resPnbpNet').innerText = 'Rp ' + net.toLocaleString('id-ID');
    document.getElementById('resUso').innerText = 'Rp ' + uso.toLocaleString('id-ID');
    document.getElementById('resBhp').innerText = 'Rp ' + bhp.toLocaleString('id-ID');
    document.getElementById('resTotalPnbp').innerText = 'Rp ' + total.toLocaleString('id-ID');
}

document.addEventListener('DOMContentLoaded', function() {
    calculateTax();
    calculatePnbp();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
