<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Formulir Registrasi Pelanggan Baru (3-Step Wizard)";
$page_subtitle = "Alur registrasi 3 tahap: Data Identitas, Konfigurasi Paket & PPN, serta Penentuan Lokasi GPS & ODP.";
$active_menu = "m-crm";
require_once __DIR__ . '/../includes/header.php';

$packages = Package::all();
?>

<div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-md max-w-4xl mx-auto space-y-6 text-xs">
    <!-- Wizard Header & Progress Bar -->
    <div class="space-y-4 border-b border-slate-100 pb-5">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-blue-600"></i> Registrasi Pelanggan Baru & Onboarding ISP
                </h3>
                <p class="text-slate-400 text-xs">Ikuti 3 tahapan registrasi untuk aktivasi akun dan penerbitan faktur tagihan.</p>
            </div>
            <span id="stepBadge" class="px-3 py-1 bg-blue-50 text-blue-700 font-bold rounded-full border border-blue-200 text-[11px] shrink-0">
                Langkah 1 dari 3
            </span>
        </div>

        <!-- Visual Step Stepper (Chevron Arrow Style) -->
        <div class="pt-2">
            <div class="arrow-breadcrumb-wrapper">
                <div class="arrow-breadcrumb w-full flex">
                    <div id="stepTab1" class="arrow-breadcrumb-item is-active flex-1 cursor-pointer justify-center" onclick="goToStep(1)">
                        <span class="arrow-breadcrumb-badge">1</span>
                        <span class="truncate"><span class="sm:hidden">1. Identitas</span><span class="hidden sm:inline">1. Data Identitas KTP</span></span>
                    </div>

                    <div id="stepTab2" class="arrow-breadcrumb-item is-inactive flex-1 cursor-pointer justify-center" onclick="goToStep(2)">
                        <span class="arrow-breadcrumb-badge">2</span>
                        <span class="truncate"><span class="sm:hidden">2. Paket PPN</span><span class="hidden sm:inline">2. Paket & Pajak PPN</span></span>
                    </div>

                    <div id="stepTab3" class="arrow-breadcrumb-item is-inactive flex-1 cursor-pointer justify-center" onclick="goToStep(3)">
                        <span class="arrow-breadcrumb-badge">3</span>
                        <span class="truncate"><span class="sm:hidden">3. Aktivasi</span><span class="hidden sm:inline">3. Lokasi & Aktivasi</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="formWizardReg" action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-6">
        <input type="hidden" name="action" value="create_customer">
        <input type="hidden" name="redirect" value="crm/daftar.php">

        <!-- ==================== STEP 1: DATA IDENTITAS ==================== -->
        <div id="stepContent1" class="space-y-4">
            <div class="p-4 bg-brand-50/70 border border-brand-100 rounded-2xl text-brand-900 flex items-center gap-3">
                <i class="fa-solid fa-id-card text-brand-600 text-lg shrink-0"></i>
                <div>
                    <strong class="font-bold block">Tahap 1: Identitas Legal Calon Pelanggan</strong>
                    <span class="text-[11px] text-brand-700">Pastikan nomor KTP dan WhatsApp sesuai untuk keperluan kontrak & invoice digital.</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span></label>
                    <input type="text" id="inputName" name="name" placeholder="Contoh: Budi Santoso" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium focus:bg-white focus:border-brand-500 transition">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span></label>
                    <input type="text" id="inputNik" name="nik" placeholder="3275xxxxxxxxxxxx" maxlength="16" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono focus:bg-white focus:border-brand-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">No. WhatsApp / Telepon <span class="text-rose-500">*</span></label>
                    <input type="tel" id="inputPhone" name="phone" placeholder="081234567890" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium focus:bg-white focus:border-brand-500 transition">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Alamat Email Aktif</label>
                    <input type="email" id="inputEmail" name="email" placeholder="budi.santoso@gmail.com" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium focus:bg-white focus:border-brand-500 transition">
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Alamat Lengkap Domisili / Pemasangan <span class="text-rose-500">*</span></label>
                <textarea id="inputAddress" name="address" rows="3" placeholder="Jl. Jatiwaringin Raya No. 45, RT 02/RW 05, Kel. Jaticempaka, Kec. Pondok Gede, Kota Bekasi..." required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:bg-white focus:border-brand-500 transition"></textarea>
            </div>

            <div class="flex justify-end pt-2">
                <button type="button" onclick="nextStep(2)" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-6 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 transition flex items-center gap-2">
                    <span>Lanjut ke Tahap 2 (Paket & PPN)</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- ==================== STEP 2: PAKET & PPN ==================== -->
        <div id="stepContent2" class="space-y-4 hidden">
            <div class="p-3.5 bg-indigo-50/70 border border-indigo-100 rounded-xl text-indigo-800 flex items-center gap-2.5">
                <i class="fa-solid fa-file-invoice-dollar text-indigo-600 text-base shrink-0"></i>
                <div>
                    <strong class="font-bold block">Tahap 2: Pemilihan Paket Bandwidth & Skema Perpajakan PPN</strong>
                    <span class="text-[11px] text-indigo-600">Sistem otomatis menghitung simulasi DPP & PPN 11% sesuai regulasi Dirjen Pajak.</span>
                </div>
            </div>

            <!-- Tipe Billing & Skema Paket -->
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                <label class="font-bold text-slate-800 block text-xs flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-600"></i> Tipe Model Penagihan (Billing Mode) <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label id="lblBillingPostpaid" class="p-3 bg-white rounded-xl border-2 border-blue-500 cursor-pointer flex items-start gap-3 transition shadow-xs">
                        <input type="radio" name="billing_type" value="postpaid" checked onchange="updateBillingTypeUI('postpaid')" class="accent-blue-600 mt-1">
                        <div>
                            <strong class="block text-slate-900 font-bold text-xs">Pascabayar (Postpaid Fixed Date)</strong>
                            <span class="text-[10px] text-slate-500 leading-tight block mt-0.5">Tagihan rutin terbit tgl 1, jatuh tempo serentak tanggal 20.</span>
                        </div>
                    </label>
                    <label id="lblBillingPrepaid" class="p-3 bg-white rounded-xl border border-slate-200 cursor-pointer flex items-start gap-3 transition shadow-xs hover:border-purple-400">
                        <input type="radio" name="billing_type" value="prepaid" onchange="updateBillingTypeUI('prepaid')" class="accent-purple-600 mt-1">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <strong class="block text-slate-900 font-bold text-xs">Prabayar (Prepaid FTTH)</strong>
                                <span class="px-1.5 py-0.2 bg-purple-100 text-purple-700 font-bold text-[9px] rounded">Grace 30 Mnt</span>
                            </div>
                            <span class="text-[10px] text-slate-500 leading-tight block mt-0.5">Bayar / Top-up di awal. Mendukung Rolling 30 Hari & Fixed Date.</span>
                        </div>
                    </label>
                </div>

                <!-- Sub-options for Prepaid: Rolling 30 Days vs Fixed Date -->
                <div id="prepaidCycleContainer" class="hidden p-3 bg-purple-50/70 border border-purple-200 rounded-xl space-y-2">
                    <label class="font-bold text-purple-900 block text-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-arrows-spin text-purple-600"></i> Pilihan Siklus Masa Aktif Prabayar:
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <label id="lblCycleAnniversary" class="p-2.5 bg-white rounded-lg border-2 border-purple-500 cursor-pointer flex items-start gap-2 text-xs transition">
                            <input type="radio" name="billing_cycle_type" value="anniversary" checked onchange="updatePpnCalcPreview()" class="accent-purple-600 mt-0.5">
                            <div>
                                <strong class="text-purple-950 font-bold block text-[11px]">Billing Cycle (Rolling 30 Hari)</strong>
                                <span class="text-[10px] text-slate-500 block">Aktif 30 hari penuh sejak tanggal top-up / aktivasi.</span>
                            </div>
                        </label>
                        <label id="lblCycleFixed" class="p-2.5 bg-white rounded-lg border border-slate-200 cursor-pointer flex items-start gap-2 text-xs transition hover:border-purple-300">
                            <input type="radio" name="billing_cycle_type" value="fixed_date" onchange="updatePpnCalcPreview()" class="accent-purple-600 mt-0.5">
                            <div>
                                <strong class="text-slate-900 font-bold block text-[11px]">Fixed Date (Reset Tanggal 1)</strong>
                                <span class="text-[10px] text-slate-500 block">Masa aktif diselaraskan berakhir di akhir bulan (Prorata).</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Pilihan Paket Layanan Internet</label>
                    <select id="regPackageSelect" name="package_id" onchange="updatePpnCalcPreview()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 focus:bg-white focus:border-blue-500 transition">
                        <?php foreach ($packages as $pkg): ?>
                            <option value="<?= $pkg['id'] ?>" data-price="<?= $pkg['price'] ?>" <?= ($pkg['id'] == 2) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pkg['name']) ?> (<?= $pkg['speed_mbps'] ?? 50 ?> Mbps) - <?= format_rupiah($pkg['price']) ?>/bln
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Pilih Skema PPN Tagihan Invoice</label>
                    <div class="grid grid-cols-2 gap-2 pt-0.5">
                        <label id="lblInclude" class="p-2.5 bg-white rounded-lg border-2 border-blue-500 cursor-pointer flex items-center gap-2 font-bold text-xs text-blue-900 transition">
                            <input type="radio" name="ppn_scheme" value="include" checked onchange="updatePpnCalcPreview()" class="accent-blue-600">
                            <span>Include PPN</span>
                        </label>
                        <label id="lblExclude" class="p-2.5 bg-white rounded-lg border border-slate-200 cursor-pointer flex items-center gap-2 font-bold text-xs text-slate-700 hover:border-blue-500 transition">
                            <input type="radio" name="ppn_scheme" value="exclude" onchange="updatePpnCalcPreview()" class="accent-blue-600">
                            <span>Exclude PPN (+11%)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Skema Tagihan Awal: Prorata vs Non-Prorata -->
            <?php 
            $totDays = (int)date('t');
            $curDay = (int)date('j');
            $remDays = max(1, $totDays - $curDay + 1);
            ?>
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                <label class="font-bold text-slate-800 block text-xs flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-emerald-600"></i> Skema Tagihan Bulan Pertama (Aktivasi Baru)
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label id="lblNonProrata" class="p-2.5 bg-white rounded-lg border-2 border-blue-500 cursor-pointer flex items-center gap-2 font-bold text-xs text-blue-900 transition shadow-xs">
                        <input type="radio" name="is_prorata" value="0" checked onchange="updatePpnCalcPreview()" class="accent-blue-600">
                        <span>Non-Prorata (Tagihan Penuh 1 Bulan)</span>
                    </label>
                    <label id="lblProrata" class="p-2.5 bg-white rounded-lg border border-slate-200 cursor-pointer flex items-center gap-2 font-bold text-xs text-slate-700 hover:border-blue-500 transition shadow-xs">
                        <input type="radio" name="is_prorata" value="1" onchange="updatePpnCalcPreview()" class="accent-blue-600">
                        <span>Prorata (Sisa <?= $remDays ?>/<?= $totDays ?> Hari Bulan Ini)</span>
                    </label>
                </div>
            </div>

            <!-- Real-time Invoice Simulation Preview Card -->
            <div class="p-4 bg-slate-900 text-white rounded-2xl border border-slate-800 space-y-3">
                <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                    <span class="text-[11px] font-bold text-blue-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-calculator"></i> Pratinjau Tagihan Awal (Invoice Preview)
                    </span>
                    <span class="text-[10px] text-slate-400 font-mono" id="simDueLabel">Jatuh Tempo: Tgl 20</span>
                </div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <p class="font-extrabold text-white text-sm" id="ppnSimDescription">Paket Rp 250.000 (Include PPN 11%)</p>
                        <span class="text-[10px] text-slate-400 block" id="simSubNote">Siklus otomatis dibuat oleh NETPRO Billing Engine</span>
                    </div>
                    <div class="flex gap-4 text-right">
                        <div><span class="text-slate-400 block text-[10px]">DPP</span><strong class="font-mono text-slate-200" id="simDpp">Rp 225.225</strong></div>
                        <div><span class="text-slate-400 block text-[10px]">PPN 11%</span><strong class="font-mono text-blue-400" id="simPpn">Rp 24.775</strong></div>
                        <div><span class="text-slate-400 block text-[10px]">Total Bayar</span><strong class="font-mono text-emerald-400 text-base" id="simTotal">Rp 250.000</strong></div>
                    </div>
                </div>
            </div>

            <div class="flex justify-between pt-2">
                <button type="button" onclick="goToStep(1)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Tahap 1</span>
                </button>
                <button type="button" onclick="nextStep(3)" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-6 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 transition flex items-center gap-2">
                    <span>Lanjut ke Tahap 3 (Lokasi GPS & Aktivasi)</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <!-- ==================== STEP 3: LOKASI GPS & AKTIVASI ==================== -->
        <div id="stepContent3" class="space-y-4 hidden">
            <div class="p-3.5 bg-emerald-50/70 border border-emerald-100 rounded-xl text-emerald-800 flex items-center gap-2.5">
                <i class="fa-solid fa-map-pin text-emerald-600 text-base shrink-0"></i>
                <div>
                    <strong class="font-bold block">Tahap 3: Pemetaan Koordinat GPS & Penugasan ODP Port</strong>
                    <span class="text-[11px] text-emerald-600">Geser pin marker pada peta untuk menentukan koordinat presisi tarikan kabel dropcore.</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="font-semibold text-slate-700">Koordinat GPS Pemasangan</label>
                        <span class="text-[10px] text-blue-600 font-bold">📍 Drag & Drop Marker Peta</span>
                    </div>
                    <input type="text" id="gpsInputReg" name="gps_coords" value="-6.2891, 106.9182" required class="w-full bg-slate-50 border border-blue-500 rounded-lg p-2.5 font-mono font-bold text-blue-700 focus:bg-white transition shadow-xs">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Metode Otentikasi & Tipe Koneksi Router</label>
                    <select id="authMethodSelect" name="auth_method" onchange="toggleAuthFields()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 focus:bg-white focus:border-blue-500 transition">
                        <option value="pppoe" selected>PPPoE Client (Mikrotik RADIUS Authentication)</option>
                        <option value="dhcp">DHCP Lease / IPoE (MAC Binding & IP Pools)</option>
                        <option value="hotspot">Hotspot Voucher (RADIUS Captive Portal)</option>
                        <option value="static">Static IP (Routed Gateway Enterprise)</option>
                    </select>
                </div>
            </div>

            <!-- Dedicated PPPoE Credentials Configuration Box -->
            <div id="boxPppoeAuth" class="p-4 bg-blue-50/60 border border-blue-200 rounded-xl space-y-3">
                <div class="flex justify-between items-center border-b border-blue-200/60 pb-2">
                    <span class="font-bold text-blue-900 flex items-center gap-1.5 text-xs">
                        <i class="fa-solid fa-key text-blue-600"></i> Akun Otentikasi PPPoE Pelanggan (Dialer ONT / Router)
                    </span>
                    <button type="button" onclick="generateRandomPppoe()" class="text-[10px] text-blue-700 hover:text-blue-900 font-bold bg-white px-2 py-0.5 rounded border border-blue-300 shadow-xs flex items-center gap-1">
                        <i class="fa-solid fa-arrows-rotate"></i> Auto-Generate
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Username PPPoE <span class="text-[10px] text-blue-600 font-normal">(Prefix: 8 Digit KTP-NAMA KAPITAL)</span></label>
                        <input type="text" id="inputPppoeUser" name="pppoe_user" placeholder="32122725-SUSI" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 font-mono font-bold text-blue-700 uppercase focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Password PPPoE</label>
                        <div class="relative">
                            <input type="password" id="inputPppoePass" name="pppoe_password" placeholder="Min. 6 Karakter / Angka" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 pr-10 font-mono font-bold text-slate-800 focus:border-blue-500 transition">
                            <button type="button" onclick="togglePassVisibility('inputPppoePass', 'eyeIconPppoe')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700">
                                <i id="eyeIconPppoe" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <p class="text-[10.5px] text-slate-500 leading-normal">
                    <i class="fa-solid fa-circle-info text-blue-500"></i> Format otomatis: <strong>8 digit awal NIK KTP - NAMA KAPITAL</strong> (contoh: KTP <code>3212272511900002</code> atas nama <code>Susi Susanti</code> ➔ <code>32122725-SUSI</code>). Kredensial akan otomatis didaftarkan ke RADIUS / MikroTik NAS.
                </p>
            </div>

            <!-- Leaflet Interactive Map -->
            <div class="space-y-1.5">
                <span class="font-bold text-slate-800 text-xs">Peta Lokasi Pemasangan (Leaflet CartoDB Voyager)</span>
                <div id="leaflet-reg-map" class="h-64 rounded-xl border border-slate-300 shadow-inner z-10"></div>
            </div>

            <!-- Review Summary Before Final Submit -->
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Ringkasan Konfirmasi Registrasi</span>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-[11px]">
                    <div>
                        <span class="text-slate-400 block">Calon Pelanggan:</span>
                        <strong id="sumName" class="text-slate-800 font-bold">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Paket Internet:</span>
                        <strong id="sumPackage" class="text-blue-600 font-bold">-</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Tipe Koneksi:</span>
                        <strong id="sumAuth" class="text-indigo-600 font-bold">PPPoE Client</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Estimasi Tagihan Pertama:</span>
                        <strong id="sumTotal" class="text-emerald-600 font-bold font-mono">-</strong>
                    </div>
                </div>
            </div>

            <div class="flex justify-between pt-2">
                <button type="button" onclick="goToStep(2)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali ke Tahap 2</span>
                </button>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-emerald-500/25 transition flex items-center gap-2 text-xs">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Selesaikan Registrasi & Terbitkan Invoice Pertama</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
var currentStep = 1;
var regMap = null;
var regMarker = null;

function goToStep(step) {
    // Validate Step 1 if moving forward
    if (step > 1 && currentStep === 1) {
        if (!validateStep1()) return;
    }

    // Hide all step contents
    document.getElementById('stepContent1').classList.add('hidden');
    document.getElementById('stepContent2').classList.add('hidden');
    document.getElementById('stepContent3').classList.add('hidden');

    // Show target content
    document.getElementById('stepContent' + step).classList.remove('hidden');

    // Update Badge Text
    document.getElementById('stepBadge').innerText = 'Langkah ' + step + ' dari 3';

    // Update Tabs Styling
    for (var i = 1; i <= 3; i++) {
        var tab = document.getElementById('stepTab' + i);
        var badgeNum = tab.querySelector('.arrow-breadcrumb-badge');
        if (i === step) {
            tab.className = 'arrow-breadcrumb-item is-active flex-1 cursor-pointer justify-center';
            badgeNum.innerText = i;
        } else if (i < step) {
            tab.className = 'arrow-breadcrumb-item is-completed flex-1 cursor-pointer justify-center';
            badgeNum.innerHTML = '<i class="fa-solid fa-check text-[10px]"></i>';
        } else {
            tab.className = 'arrow-breadcrumb-item is-inactive flex-1 cursor-pointer justify-center';
            badgeNum.innerText = i;
        }
    }

    currentStep = step;

    // Refresh map if Step 3
    if (step === 3) {
        updateSummaryReview();
        setTimeout(function() {
            if (regMap) {
                regMap.invalidateSize();
            } else {
                initRegMap();
            }
        }, 100);
    }
}

function nextStep(step) {
    goToStep(step);
}

function validateStep1() {
    var name = document.getElementById('inputName').value.trim();
    var nik = document.getElementById('inputNik').value.trim();
    var phone = document.getElementById('inputPhone').value.trim();
    var addr = document.getElementById('inputAddress').value.trim();

    if (!name || !nik || !phone || !addr) {
        alert('Mohon lengkapi Nama, NIK, No WhatsApp, dan Alamat Lengkap terlebih dahulu.');
        return false;
    }
    return true;
}

function toggleAuthFields() {
    var authSelect = document.getElementById('authMethodSelect');
    var boxPppoe = document.getElementById('boxPppoeAuth');
    var val = authSelect ? authSelect.value : 'pppoe';

    if (boxPppoe) {
        if (val === 'pppoe') {
            boxPppoe.classList.remove('hidden');
            // Auto fill PPPoE fields if empty
            var userField = document.getElementById('inputPppoeUser');
            var passField = document.getElementById('inputPppoePass');
            if (userField && !userField.value) {
                generateRandomPppoe();
            }
        } else {
            boxPppoe.classList.add('hidden');
        }
    }
}

function generateRandomPppoe(force) {
    var rawName = document.getElementById('inputName').value.trim();
    var rawNik = document.getElementById('inputNik').value.trim().replace(/[^0-9]/g, '');
    
    // Format Prefix: 8 Digit KTP - NAMA KAPITAL (misal: 32122725-SUSI)
    var nikPrefix = rawNik.length >= 8 ? rawNik.substring(0, 8) : (rawNik || '32122725');
    var firstName = rawName.split(' ')[0].toUpperCase().replace(/[^A-Z0-9]/g, '') || 'SUSI';
    var pppoeUsername = nikPrefix + '-' + firstName;

    var rndPass = Math.floor(100000 + Math.random() * 900000);

    var userEl = document.getElementById('inputPppoeUser');
    var passEl = document.getElementById('inputPppoePass');
    if (userEl && (force || !userEl.value || userEl.value.indexOf('-') !== -1 || userEl.value.startsWith('pppoe_'))) {
        userEl.value = pppoeUsername;
    }
    if (passEl && (force || !passEl.value)) {
        passEl.value = rndPass;
    }
}

function togglePassVisibility(inputId, iconId) {
    var inp = document.getElementById(inputId);
    var ico = document.getElementById(iconId);
    if (!inp || !ico) return;

    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'fa-solid fa-eye-slash text-xs text-blue-600';
    } else {
        inp.type = 'password';
        ico.className = 'fa-solid fa-eye text-xs';
    }
}

function formatRupiahJs(num) {
    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
}

function updateBillingTypeUI(type) {
    var lblPost = document.getElementById('lblBillingPostpaid');
    var lblPre = document.getElementById('lblBillingPrepaid');
    var cycleContainer = document.getElementById('prepaidCycleContainer');
    if (lblPost && lblPre) {
        if (type === 'prepaid') {
            lblPre.className = 'p-3 bg-purple-50/60 rounded-xl border-2 border-purple-500 cursor-pointer flex items-start gap-3 transition shadow-xs';
            lblPost.className = 'p-3 bg-white rounded-xl border border-slate-200 cursor-pointer flex items-start gap-3 transition shadow-xs hover:border-blue-400';
            if (cycleContainer) cycleContainer.classList.remove('hidden');
        } else {
            lblPost.className = 'p-3 bg-blue-50/60 rounded-xl border-2 border-blue-500 cursor-pointer flex items-start gap-3 transition shadow-xs';
            lblPre.className = 'p-3 bg-white rounded-xl border border-slate-200 cursor-pointer flex items-start gap-3 transition shadow-xs hover:border-purple-400';
            if (cycleContainer) cycleContainer.classList.add('hidden');
        }
    }
    updatePpnCalcPreview();
}

function updatePpnCalcPreview() {
    var selectEl = document.getElementById('regPackageSelect');
    if (!selectEl) return;
    var selectedOpt = selectEl.options[selectEl.selectedIndex];
    var originalPkgVal = parseFloat(selectedOpt.getAttribute('data-price')) || 250000;
    
    // Check Prorata
    var prorataRadio = document.querySelector('input[name="is_prorata"]:checked');
    var isProrata = prorataRadio ? prorataRadio.value === '1' : false;
    var remDays = <?= $remDays ?>;
    var totDays = <?= $totDays ?>;
    
    var lblNon = document.getElementById('lblNonProrata');
    var lblPro = document.getElementById('lblProrata');
    if (lblNon && lblPro) {
        if (isProrata) {
            lblPro.className = 'p-2.5 bg-white rounded-lg border-2 border-emerald-500 cursor-pointer flex items-center gap-2 font-bold text-xs text-emerald-900 transition shadow-xs';
            lblNon.className = 'p-2.5 bg-white rounded-lg border border-slate-200 cursor-pointer flex items-center gap-2 font-bold text-xs text-slate-700 hover:border-blue-500 transition shadow-xs';
        } else {
            lblNon.className = 'p-2.5 bg-white rounded-lg border-2 border-blue-500 cursor-pointer flex items-center gap-2 font-bold text-xs text-blue-900 transition shadow-xs';
            lblPro.className = 'p-2.5 bg-white rounded-lg border border-slate-200 cursor-pointer flex items-center gap-2 font-bold text-xs text-slate-700 hover:border-blue-500 transition shadow-xs';
        }
    }

    var pkgVal = originalPkgVal;
    var prorataNote = '';
    if (isProrata && remDays < totDays) {
        pkgVal = Math.round(originalPkgVal * (remDays / totDays));
        prorataNote = ' [Prorata ' + remDays + '/' + totDays + ' Hari]';
    }

    // Check PPN Scheme
    var ppnRadio = document.querySelector('input[name="ppn_scheme"]:checked');
    var isInclude = ppnRadio ? ppnRadio.value === 'include' : true;

    var lblInc = document.getElementById('lblInclude');
    var lblExc = document.getElementById('lblExclude');
    if (lblInc && lblExc) {
        if (isInclude) {
            lblInc.className = 'p-2.5 bg-white rounded-lg border-2 border-blue-500 cursor-pointer flex items-center gap-2 font-bold text-xs text-blue-900 transition';
            lblExc.className = 'p-2.5 bg-white rounded-lg border border-slate-200 cursor-pointer flex items-center gap-2 font-bold text-xs text-slate-700 hover:border-blue-500 transition';
        } else {
            lblInc.className = 'p-2.5 bg-white rounded-lg border border-slate-200 cursor-pointer flex items-center gap-2 font-bold text-xs text-slate-700 hover:border-blue-500 transition';
            lblExc.className = 'p-2.5 bg-white rounded-lg border-2 border-blue-500 cursor-pointer flex items-center gap-2 font-bold text-xs text-blue-900 transition';
        }
    }

    // Check Billing Type & Cycle Type
    var bTypeRadio = document.querySelector('input[name="billing_type"]:checked');
    var isPrepaid = bTypeRadio ? bTypeRadio.value === 'prepaid' : false;
    var cycleRadio = document.querySelector('input[name="billing_cycle_type"]:checked');
    var isCycleFixed = cycleRadio ? cycleRadio.value === 'fixed_date' : false;

    // Style cycle labels
    var lblAnn = document.getElementById('lblCycleAnniversary');
    var lblFix = document.getElementById('lblCycleFixed');
    if (lblAnn && lblFix) {
        if (isCycleFixed) {
            lblFix.className = 'p-2.5 bg-white rounded-lg border-2 border-purple-500 cursor-pointer flex items-start gap-2 text-xs transition';
            lblAnn.className = 'p-2.5 bg-white rounded-lg border border-slate-200 cursor-pointer flex items-start gap-2 text-xs transition hover:border-purple-300';
        } else {
            lblAnn.className = 'p-2.5 bg-white rounded-lg border-2 border-purple-500 cursor-pointer flex items-start gap-2 text-xs transition';
            lblFix.className = 'p-2.5 bg-white rounded-lg border border-slate-200 cursor-pointer flex items-start gap-2 text-xs transition hover:border-purple-300';
        }
    }

    var dueLabel = document.getElementById('simDueLabel');
    var subNote = document.getElementById('simSubNote');
    if (dueLabel && subNote) {
        if (isPrepaid) {
            if (isCycleFixed) {
                dueLabel.innerText = "Prabayar: Fixed Date (Reset Tgl 1)";
                dueLabel.className = "text-[10px] text-purple-400 font-mono font-bold";
                subNote.innerText = "Bayar di awal, masa aktif diselaraskan tgl 1 tiap bulan";
            } else {
                dueLabel.innerText = "Prabayar: Billing Cycle (Rolling 30 Hari)";
                dueLabel.className = "text-[10px] text-purple-400 font-mono font-bold";
                subNote.innerText = "Bayar di awal, masa aktif berjalan penuh 30 hari";
            }
        } else {
            dueLabel.innerText = "Pascabayar: Jatuh Tempo Tgl 20";
            dueLabel.className = "text-[10px] text-blue-400 font-mono font-bold";
            subNote.innerText = "Tagihan Fixed Date terbit tgl 1, jatuh tempo tgl 20";
        }
    }

    var dpp = 0;
    var ppn = 0;
    var total = 0;
    var modeText = '';

    if (isInclude) {
        dpp = Math.round(pkgVal / 1.11);
        ppn = pkgVal - dpp;
        total = pkgVal;
        modeText = 'Include PPN 11%';
    } else {
        dpp = pkgVal;
        ppn = Math.round(pkgVal * 0.11);
        total = dpp + ppn;
        modeText = 'Exclude PPN 11%';
    }

    var descEl = document.getElementById('ppnSimDescription');
    if (descEl) descEl.innerText = "Tagihan " + formatRupiahJs(pkgVal) + " (" + modeText + ")" + prorataNote;
    
    var dppEl = document.getElementById('simDpp');
    if (dppEl) dppEl.innerText = formatRupiahJs(dpp);

    var ppnEl = document.getElementById('simPpn');
    if (ppnEl) ppnEl.innerText = formatRupiahJs(ppn);

    var totalEl = document.getElementById('simTotal');
    if (totalEl) totalEl.innerText = formatRupiahJs(total);

    updateSummaryReview();
}

function updateSummaryReview() {
    var nameInput = document.getElementById('inputName');
    var sumNameEl = document.getElementById('sumName');
    if (sumNameEl && nameInput) {
        sumNameEl.innerText = nameInput.value.trim() || '-';
    }
    
    var selectEl = document.getElementById('regPackageSelect');
    if (selectEl) {
        var selectedOpt = selectEl.options[selectEl.selectedIndex];
        var sumPkgEl = document.getElementById('sumPackage');
        if (sumPkgEl && selectedOpt) {
            sumPkgEl.innerText = selectedOpt.text;
        }
    }

    var simTotalEl = document.getElementById('simTotal');
    var sumTotalEl = document.getElementById('sumTotal');
    if (sumTotalEl && simTotalEl) {
        sumTotalEl.innerText = simTotalEl.innerText;
    }

    var authSel = document.getElementById('authMethodSelect');
    var authText = authSel ? authSel.options[authSel.selectedIndex].text.split('(')[0] : 'PPPoE Client';
    var sumAuthEl = document.getElementById('sumAuth');
    if (sumAuthEl) {
        sumAuthEl.innerText = authText;
    }
}

function initRegMap() {
    if (typeof L !== 'undefined' && document.getElementById('leaflet-reg-map')) {
        var initialLat = -6.2891;
        var initialLng = 106.9182;
        regMap = L.map('leaflet-reg-map').setView([initialLat, initialLng], 15);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            attribution: "&copy; OpenStreetMap &copy; CARTO"
        }).addTo(regMap);

        regMarker = L.marker([initialLat, initialLng], {draggable: true}).addTo(regMap);
        regMarker.bindPopup('<b>Lokasi Calon Pelanggan</b><br>Geser untuk sesuaikan titik pasang.').openPopup();

        function updateGpsField(lat, lng) {
            var gpsInput = document.getElementById('gpsInputReg');
            if (gpsInput) {
                gpsInput.value = lat.toFixed(6) + ', ' + lng.toFixed(6);
            }
        }

        regMarker.on('dragend', function(e) {
            var latlng = regMarker.getLatLng();
            updateGpsField(latlng.lat, latlng.lng);
        });

        regMap.on('click', function(e) {
            regMarker.setLatLng(e.latlng);
            updateGpsField(e.latlng.lat, e.latlng.lng);
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updatePpnCalcPreview();
    toggleAuthFields();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
