<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$viewId = isset($_GET['id']) ? intval($_GET['id']) : null;
$wos = WorkOrder::all();
$employees = Employee::all();
$allCustomers = Customer::all();

// If viewing a specific BAST
$selectedBast = null;
$matchedCustomer = null;
if ($viewId !== null) {
    foreach ($wos as $wo) {
        if (intval($wo['id']) === $viewId) {
            $selectedBast = $wo;
            break;
        }
    }
    // Fallback if ID not found in WO list
    if (!$selectedBast) {
        $selectedBast = [
            'id' => $viewId,
            'wo_no' => '-',
            'customer_name' => '-',
            'package_name' => '-',
            'ont_type' => '-',
            'ont_sn' => '-',
            'tech_name' => '-',
            'odp_port' => '-',
            'attenuation' => '-',
            'status' => '-'
        ];
    }
    // Try to match customer
    if ($selectedBast) {
        foreach ($allCustomers as $c) {
            if (strcasecmp(trim($c['name']), trim($selectedBast['customer_name'])) === 0) {
                $matchedCustomer = $c;
                break;
            }
        }
    }
}

$page_title = $selectedBast ? "Dokumen Berita Acara Serah Terima (BAST)" : "Daftar Berita Acara Serah Terima (BAST)";
$page_subtitle = $selectedBast ? "Hasil uji terima kecepatan, telemetri redaman optik, dan persetujuan serah terima layanan." : "Daftar arsip dokumen Berita Acara Serah Terima & Uji Kelayakan Aktivasi Pelanggan.";
$active_menu = "m-crm";
$breadcrumbs = [
    'CRM & Pelanggan' => 'crm/daftar.php',
    'Work Order (WO)' => 'crm/instalasi.php',
    'Berita Acara (BAST)' => ($selectedBast ? 'crm/berita_acara.php' : '')
];
if ($selectedBast) {
    $breadcrumbs['Lembar BAST #' . $selectedBast['id']] = '';
}
require_once __DIR__ . '/../includes/header.php';
?>

<?php if ($selectedBast): ?>
<!-- ==================== TAMPILAN DOKUMEN HASIL BAST (DETAIL VIEW) ==================== -->
<div class="space-y-6 text-xs max-w-4xl mx-auto pb-8">
    <!-- Action Top Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm print:hidden">
        <a href="<?= base_url('crm/berita_acara.php') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar BAST</span>
        </a>
        <div class="flex items-center gap-2 flex-wrap">
            <?php if ($matchedCustomer && ($matchedCustomer['status'] ?? '') === 'inactive'): ?>
                <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline">
                    <input type="hidden" name="action" value="set_customer_online">
                    <input type="hidden" name="id" value="<?= $matchedCustomer['id'] ?>">
                    <input type="hidden" name="redirect" value="crm/berita_acara.php?id=<?= $selectedBast['id'] ?>">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl shadow transition flex items-center gap-1.5 animate-pulse">
                        <i class="fa-solid fa-play"></i> Aktivasi & Set Online
                    </button>
                </form>
            <?php endif; ?>
            <?php if ($matchedCustomer): ?>
                <a href="<?= base_url('crm/detail.php?id=' . $matchedCustomer['id']) ?>" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold px-3 py-2 rounded-xl border border-indigo-200 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-id-card"></i> 360° Profil
                </a>
            <?php endif; ?>
            <button onclick="triggerToast('Kirim WhatsApp', 'Salinan dokumen BAST digital telah dikirim via WhatsApp bot!')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-2 rounded-xl shadow transition flex items-center gap-1.5">
                <i class="fa-brands fa-whatsapp"></i> Kirim WA
            </button>
            <a href="cetak_bast.php?id=<?= $selectedBast['id'] ?? 1 ?>" target="_blank" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-print text-xs"></i> Cetak / PDF BAST
            </a>
        </div>
    </div>

    <!-- Official BAST Paper Document -->
    <div class="bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-xl space-y-6 text-xs text-slate-800 print:border-none print:shadow-none print:p-0">
        <!-- Letterhead Header -->
        <div class="border-b-2 border-slate-900 pb-5 flex justify-between items-start">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-600 to-rose-600 text-white flex items-center justify-center font-black text-base shadow">
                        <i class="fa-solid fa-network-wired text-sm"></i>
                    </div>
                    <span class="font-extrabold text-slate-900 text-base tracking-wider uppercase">PT NETPRO TELEKOMUNIKASI INDONESIA</span>
                </div>
                <p class="text-slate-500 text-[11px]">ISP License No. 492/TEL.01.02/2024 &bull; Graha Network Lt. 5, Jl. Rasuna Said Kav. 8, Jakarta Selatan</p>
                <p class="text-slate-400 text-[10px]">Layanan NOC & Helpdesk: (021) 555-8899 &bull; Email: noc@netpro.co.id &bull; Website: www.netpro.co.id</p>
            </div>
            <div class="text-right space-y-1">
                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 font-extrabold rounded-full border border-emerald-200 text-[11px] uppercase tracking-wide inline-block">
                    ✓ STATUS: DITANDATANGANI
                </span>
                <p class="font-mono text-slate-500 text-[10px] block">No. Dokumen: <strong class="text-slate-900">BAST-NETPRO/<?= date('Y/m') ?>/<?= str_pad($selectedBast['id'] ?? 1, 4, '0', STR_PAD_LEFT) ?></strong></p>
            </div>
        </div>

        <div class="text-center space-y-1 py-2">
            <h2 class="font-black text-slate-900 text-base uppercase tracking-wide">BERITA ACARA SERAH TERIMA & UJI KELAYAKAN LAYANAN (BAST)</h2>
            <p class="text-slate-500 text-xs">Pemasangan Baru, Uji Terima Bandwidth Fiber Optik, dan Serah Terima Perangkat ONT</p>
        </div>

        <div class="space-y-4 leading-relaxed">
            <p class="text-slate-700 text-xs">
                Pada hari ini, tanggal <strong><?= date('d F Y') ?></strong>, bertempat di lokasi pelanggan, telah diselesaikan pekerjaan instalasi, konfigurasi jaringan, dan pengujian kualitas layanan internet broadband fiber optik dengan rincian sebagai berikut:
            </p>

            <!-- 1. Identitas Pelanggan -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-blue-600 flex items-center gap-1.5">
                    <i class="fa-solid fa-user-check"></i> 1. Informasi Calon Pelanggan & Layanan
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-[11px]">
                    <div><span class="text-slate-400 block text-[10px]">Nama Pelanggan:</span><strong class="text-slate-900 text-xs"><?= htmlspecialchars($selectedBast['customer_name']) ?></strong></div>
                    <div><span class="text-slate-400 block text-[10px]">Nomor Referensi WO:</span><strong class="font-mono text-blue-600"><?= htmlspecialchars($selectedBast['wo_no']) ?></strong></div>
                    <div><span class="text-slate-400 block text-[10px]">Paket Internet Terpasang:</span><strong class="text-slate-800"><?= htmlspecialchars($selectedBast['package_name']) ?></strong></div>
                    <div><span class="text-slate-400 block text-[10px]">Tipe Otentikasi Jaringan:</span><strong class="text-indigo-600 font-mono">PPPoE Client (Mikrotik RADIUS)</strong></div>
                    <div class="sm:col-span-2"><span class="text-slate-400 block text-[10px]">Alamat Pemasangan:</span><span class="text-slate-700">Jl. Jatiwaringin Raya No. 45, RT 02/RW 05, Kec. Pondok Gede, Kota Bekasi</span></div>
                </div>
            </div>

            <!-- 2. Hardware ONT & Fiber Optic Telemetry -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-emerald-600 flex items-center gap-1.5">
                    <i class="fa-solid fa-tower-broadcast"></i> 2. Spesifikasi Perangkat & Hasil Ukur Redaman Optik (OPM)
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-[11px]">
                    <div><span class="text-slate-400 block text-[10px]">Tipe Modem ONT:</span><strong class="text-slate-900"><?= htmlspecialchars($selectedBast['ont_type']) ?></strong></div>
                    <div><span class="text-slate-400 block text-[10px]">Serial Number (SN):</span><strong class="font-mono text-slate-800"><?= htmlspecialchars($selectedBast['ont_sn']) ?></strong></div>
                    <div><span class="text-slate-400 block text-[10px]">Distribusi ODP / Port:</span><strong class="font-mono text-slate-900"><?= htmlspecialchars($selectedBast['odp_port'] ?? 'ODP-JTW-04/16 (Port 3)') ?></strong></div>
                    <div><span class="text-slate-400 block text-[10px]">Redaman Daya Optik:</span><strong class="font-mono text-emerald-600 font-bold"><?= htmlspecialchars($selectedBast['attenuation'] ?? '-18.4 dBm') ?> (EXCELLENT)</strong></div>
                    <div><span class="text-slate-400 block text-[10px]">Panjang Kabel Dropcore:</span><strong class="text-slate-700 font-mono">125 Meter (1-Core G.657A)</strong></div>
                    <div><span class="text-slate-400 block text-[10px]">Status Koneksi OLT:</span><span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold rounded text-[10px]">ONLINE & SYNCHRONIZED</span></div>
                </div>
            </div>

            <!-- 3. Speedtest Uji Kelayakan -->
            <div class="p-4 bg-gradient-to-r from-blue-50/70 via-indigo-50/50 to-blue-50/70 rounded-2xl border border-blue-200/80 space-y-3">
                <div class="flex justify-between items-center">
                    <h4 class="font-bold text-blue-900 text-xs uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-gauge-high text-blue-600"></i> 3. Hasil Uji Kecepatan Akses (Live Speedtest Uji Terima)
                    </h4>
                    <span class="text-[10px] text-blue-700 font-bold">Server: Jakarta OpenIXP (10G Node)</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <div class="bg-white p-3 rounded-xl border border-blue-100 shadow-xs">
                        <span class="text-[10px] text-slate-400 block font-semibold">Download Rate</span>
                        <strong class="text-emerald-600 font-mono text-base font-black">51.4 Mbps</strong>
                        <span class="text-[9px] text-emerald-600 block">Target: 50 Mbps (102%)</span>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-blue-100 shadow-xs">
                        <span class="text-[10px] text-slate-400 block font-semibold">Upload Rate</span>
                        <strong class="text-blue-600 font-mono text-base font-black">50.8 Mbps</strong>
                        <span class="text-[9px] text-blue-600 block">Simetris 1:1</span>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-blue-100 shadow-xs">
                        <span class="text-[10px] text-slate-400 block font-semibold">Latency / Ping</span>
                        <strong class="text-indigo-600 font-mono text-base font-black">3.8 ms</strong>
                        <span class="text-[9px] text-slate-500 block">Jitter: 0.6 ms</span>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-blue-100 shadow-xs">
                        <span class="text-[10px] text-slate-400 block font-semibold">Packet Loss</span>
                        <strong class="text-emerald-600 font-mono text-base font-black">0.0 %</strong>
                        <span class="text-[9px] text-emerald-600 block">Kondisi Prima</span>
                    </div>
                </div>
            </div>

            <!-- Pernyataan & Klausul -->
            <p class="text-slate-600 text-[11px] leading-relaxed pt-2">
                Dengan ditandatanganinya Berita Acara Serah Terima ini, pihak <strong>Pelanggan</strong> menyatakan telah menerima instalasi perangkat dalam kondisi baik, berfungsi normal, dan hasil pengujian kecepatan internet telah sesuai dengan spesifikasi paket layanan yang disepakati.
            </p>

            <!-- Tanda Tangan Kedua Belah Pihak -->
            <div class="grid grid-cols-2 gap-8 pt-8 text-center">
                <div class="space-y-16">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Pihak Teknisi Pelaksana,</span>
                        <span class="text-[10px] text-slate-400">PT Netpro Telekomunikasi Indonesia</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-6 block w-fit mx-auto text-xs">( <?= htmlspecialchars($selectedBast['tech_name']) ?> )</strong>
                        <span class="text-[10px] text-slate-400 block mt-1">NIK: EMP-NETPRO-2024</span>
                    </div>
                </div>
                <div class="space-y-16">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Pihak Pelanggan Penerima,</span>
                        <span class="text-[10px] text-slate-400">Persetujuan Serah Terima Layanan</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-6 block w-fit mx-auto text-xs">( <?= htmlspecialchars($selectedBast['customer_name']) ?> )</strong>
                        <span class="text-[10px] text-slate-400 block mt-1">Pelanggan / Kuasa Penerima</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- ==================== TAMPILAN DAFTAR MASTER BAST (LIST VIEW) ==================== -->
<div class="space-y-6 text-xs">
    <!-- Top KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total BAST Terbit</span>
                <strong class="font-extrabold text-slate-900 text-xl block mt-0.5"><?= count($wos) ?> Dokumen</strong>
                <span class="text-blue-600 text-[10px] font-semibold">Tercatat di sistem</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-file-signature"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Ditandatangani (Selesai)</span>
                <strong class="font-extrabold text-emerald-600 text-xl block mt-0.5"><?= count($wos) ?> Selesai</strong>
                <span class="text-emerald-600 text-[10px] font-semibold"><?= count($wos) > 0 ? '100% Uji Lulus' : 'Belum Ada BAST' ?></span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Rata-Rata Redaman OPM</span>
                <strong class="font-extrabold text-slate-400 text-xl font-mono block mt-0.5"><?= count($wos) > 0 ? '-18.42 dBm' : '0.00 dBm' ?></strong>
                <span class="text-slate-400 text-[10px] font-semibold"><?= count($wos) > 0 ? 'Kategori Sangat Baik' : 'Tidak Ada Data' ?></span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-base">
                <i class="fa-solid fa-tower-broadcast"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Menunggu Validasi TTD</span>
                <strong class="font-extrabold text-slate-400 text-xl block mt-0.5">0 Dokumen</strong>
                <span class="text-slate-400 text-[10px] font-semibold">Proses Instalasi</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-base">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>
    </div>

    <!-- Main BAST Master List Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-file-contract text-blue-600"></i> Arsip Dokumen Berita Acara Serah Terima (BAST)
                </h3>
                <p class="text-slate-400 text-xs">Klik pada baris atau tombol BAST untuk melihat dokumen hasil uji terima dan pencetakan sertifikat.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= base_url('crm/instalasi.php') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-2 rounded-xl transition flex items-center gap-1.5 text-xs">
                    <i class="fa-solid fa-wrench"></i> Kelola Work Order
                </a>
                <button onclick="document.getElementById('modalAddBast').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 transition flex items-center gap-1.5 text-xs">
                    <i class="fa-solid fa-plus text-xs"></i> + Terbitkan BAST Baru
                </button>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
            <div class="relative w-full sm:w-72">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="searchBastInput" onkeyup="filterBastTable()" placeholder="Cari No BAST, Nama Pelanggan..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs focus:bg-white focus:border-blue-500 transition">
            </div>
            <div class="text-slate-400 text-[11px]">
                Menampilkan <strong><?= count($wos) ?></strong> Berita Acara Terdaftar
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="bastTable">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">No. BAST</th>
                        <th class="py-3 px-4">Ref. Work Order</th>
                        <th class="py-3 px-4">Nama Pelanggan</th>
                        <th class="py-3 px-4">Paket & Speed</th>
                        <th class="py-3 px-4">Modem ONT & SN</th>
                        <th class="py-3 px-4">Teknisi Bertugas</th>
                        <th class="py-3 px-4">Redaman Sinyal</th>
                        <th class="py-3 px-4">Status BAST</th>
                        <th class="py-3 px-4 text-right">Aksi Hasil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($wos)): ?>
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400">Belum ada dokumen Berita Acara Serah Terima.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($wos as $wo): 
                        $bastNo = 'BAST-NETPRO/' . date('Y/m') . '/' . str_pad($wo['id'], 4, '0', STR_PAD_LEFT);
                    ?>
                    <tr class="border-b border-slate-50 hover:bg-blue-50/40 transition group">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                            <a href="<?= base_url('crm/berita_acara.php?id=' . $wo['id']) ?>" class="hover:underline flex items-center gap-1.5">
                                <i class="fa-solid fa-file-lines text-blue-400 group-hover:text-blue-600"></i>
                                <?= $bastNo ?>
                            </a>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-600"><?= htmlspecialchars($wo['wo_no']) ?></td>
                        <td class="py-3.5 px-4">
                            <strong class="font-bold text-slate-900 block"><?= htmlspecialchars($wo['customer_name']) ?></strong>
                            <span class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($wo['odp_port'] ?? 'ODP Sentral') ?></span>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-800">
                            <?= htmlspecialchars($wo['package_name']) ?>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-700">
                            <?= htmlspecialchars($wo['ont_type']) ?>
                            <span class="block text-[10px] text-slate-400">SN: <?= htmlspecialchars($wo['ont_sn']) ?></span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-slate-700">
                            <i class="fa-solid fa-user-gear text-slate-400 mr-1"></i>
                            <?= htmlspecialchars($wo['tech_name']) ?>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-600">
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded">
                                <?= htmlspecialchars($wo['attenuation']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[9.5px]">
                                DITANDATANGANI
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1.5">
                            <a href="<?= base_url('crm/berita_acara.php?id=' . $wo['id']) ?>" class="bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-700 font-bold px-3 py-1.5 rounded-lg border border-blue-200 transition inline-flex items-center gap-1 text-[11px]">
                                <i class="fa-solid fa-eye"></i> Lihat Hasil BAST
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Terbitkan BAST Baru -->
<div id="modalAddBast" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 text-xs max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                <i class="fa-solid fa-file-circle-plus text-blue-600"></i> Terbitkan Berita Acara Serah Terima (BAST) Baru
            </h3>
            <button onclick="document.getElementById('modalAddBast').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create_wo">
            <input type="hidden" name="redirect" value="crm/berita_acara.php">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Nama Pelanggan <span class="text-rose-500">*</span></label>
                    <input type="text" name="customer_name" required placeholder="Budi Santoso" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium focus:bg-white focus:border-blue-500">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Paket Layanan <span class="text-rose-500">*</span></label>
                    <input type="text" name="package_name" required value="Home Premium 50M" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium focus:bg-white focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Tipe Modem ONT</label>
                    <select name="ont_type" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                        <option value="ZTE F660 Dualband">ZTE F660 Dualband</option>
                        <option value="Huawei HG8245H5">Huawei HG8245H5 Dualband</option>
                        <option value="Fiberhome AN5506">Fiberhome AN5506-04</option>
                        <option value="MikroTik hEX S + SFP">MikroTik Router SFP</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Serial Number (SN) ONT <span class="text-rose-500">*</span></label>
                    <input type="text" name="ont_sn" required value="ZTEG<?= rand(10000000, 99999999) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Teknisi Pelaksana Uji <span class="text-rose-500">*</span></label>
                    <select name="tech_name" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-slate-800">
                        <?php if (empty($employees)): ?>
                            <option value="Teknisi Lapangan">Teknisi Lapangan</option>
                        <?php else: ?>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= htmlspecialchars($emp['name']) ?>">
                                    <?= htmlspecialchars($emp['name']) ?> - <?= htmlspecialchars($emp['position']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Hasil Redaman OPM <span class="text-rose-500">*</span></label>
                    <input type="text" name="attenuation" required value="-18.4 dBm" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-emerald-600">
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Titik Distribusi ODP / Port <span class="text-rose-500">*</span></label>
                <input type="text" name="odp_port" required value="ODP-JTW-04/16 (Port 3)" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-medium">
            </div>

            <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl text-blue-900 text-[11px] leading-relaxed">
                <i class="fa-solid fa-circle-info text-blue-600"></i> Dokumen BAST akan otomatis dibuat dengan hasil uji redaman & speedtest sesuai profil paket yang dipilih.
            </div>

            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-2.5 rounded-xl shadow-lg shadow-brand-950/30 transition">
                Simpan & Terbitkan Lembar BAST
            </button>
        </form>
    </div>
</div>

<script>
function filterBastTable() {
    var input = document.getElementById("searchBastInput");
    var filter = input.value.toLowerCase();
    var table = document.getElementById("bastTable");
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
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
