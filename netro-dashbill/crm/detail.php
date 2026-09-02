<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Detail & Profil 360° Pelanggan";
$page_subtitle = "Profil lengkap pelanggan, telemetri GPON/OLT, status sesi MikroTik RADIUS, dan riwayat tagihan.";
$active_menu = "m-crm";
$breadcrumbs = [
    'CRM & Pelanggan' => 'crm/daftar.php',
    '360° Profil & Telemetri' => ''
];
require_once __DIR__ . '/../includes/header.php';

$custId = intval($_GET['id'] ?? 0);
$customer = $custId > 0 ? Customer::find($custId) : null;
if (!$customer) {
    $allCust = Customer::all();
    $customer = !empty($allCust) ? $allCust[0] : [
        'id' => 0,
        'cid' => '-',
        'name' => '-',
        'nik' => '-',
        'phone' => '-',
        'email' => '-',
        'address' => '-',
        'gps_lat' => 0,
        'gps_lng' => 0,
        'package_name' => '-',
        'speed_mbps' => 0,
        'package_price' => 0,
        'ppn_scheme' => '-',
        'auth_method' => '-',
        'status' => '-',
        'created_at' => date('Y-m-d H:i:s')
    ];
}

$invoices = Invoice::all();
$custInvoices = [];
$unpaidInvoices = [];
$latestPaidInvoice = null;
foreach ($invoices as $inv) {
    if (intval($inv['customer_id'] ?? 0) === intval($customer['id'])) {
        $custInvoices[] = $inv;
        if (strtolower($inv['status']) === 'unpaid' || strtolower($inv['status']) === 'belum bayar') {
            $unpaidInvoices[] = $inv;
        } else {
            $latestPaidInvoice = $inv;
        }
    }
}

$tickets = Ticket::all();
$custTickets = [];
foreach ($tickets as $t) {
    if (intval($t['customer_id'] ?? 0) === intval($customer['id'])) {
        $custTickets[] = $t;
    }
}

$allWo = WorkOrder::all();
$custWo = null;
foreach ($allWo as $w) {
    if (trim(strtolower($w['customer_name'])) === trim(strtolower($customer['name']))) {
        $custWo = $w;
        break;
    }
}

$custStatus = strtolower($customer['status'] ?? 'inactive');
$isOnline = ($custStatus === 'active');
$isIsolated = ($custStatus === 'isolated');
$isInactive = ($custStatus === 'inactive' || !$isOnline && !$isIsolated);
?>

<div class="space-y-6 text-xs">
    <!-- Header Profile Bar (Mobile Responsive & RedDash Theme) -->
    <div class="bg-white p-4 sm:p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div class="flex items-start sm:items-center gap-3.5 sm:gap-4 w-full lg:w-auto">
            <div class="w-13 h-13 sm:w-16 sm:h-16 bg-gradient-to-br from-brand-600 to-rose-700 text-white rounded-2xl flex items-center justify-center font-black text-xl sm:text-2xl shadow-md shrink-0">
                <?= strtoupper(substr($customer['name'] ?? 'BW', 0, 2)) ?>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="font-extrabold text-slate-900 text-base sm:text-lg break-words leading-tight"><?= htmlspecialchars($customer['name']) ?></h2>
                    <?php if ($isOnline): ?>
                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[10px] flex items-center gap-1 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> ONLINE / AKTIF
                        </span>
                    <?php elseif ($isIsolated): ?>
                        <span class="px-2.5 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-full font-bold text-[10px] flex items-center gap-1 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> ISOLIR / TUNGGAKAN
                        </span>
                    <?php else: ?>
                        <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-full font-bold text-[10px] flex items-center gap-1 shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> INACTIVE / BELUM AKTIF
                        </span>
                    <?php endif; ?>
                    <span class="px-2 py-0.5 bg-brand-50 text-brand-700 border border-brand-100 rounded-lg font-mono text-[10px] font-bold shrink-0">
                        <?= htmlspecialchars($customer['cid'] ?? 'CID-991201') ?>
                    </span>
                </div>
                <p class="text-slate-400 text-[11px] sm:text-xs mt-1 leading-snug">
                    Paket: <strong class="text-slate-700 font-semibold"><?= htmlspecialchars($customer['package_name'] ?? 'Home Premium 50M') ?> (<?= $customer['speed_mbps'] ?? 50 ?> Mbps)</strong> • Terdaftar sejak <?= date('d M Y', strtotime($customer['created_at'] ?? '2024-01-14')) ?>
                </p>
            </div>
        </div>

        <!-- Quick Actions Buttons -->
        <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto pt-2 lg:pt-0 border-t border-slate-100 lg:border-t-0">
            <button onclick="triggerToast('Kirim WhatsApp', 'Pesan rincian tagihan WhatsApp terkirim ke <?= htmlspecialchars($customer['phone']) ?>')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-2 rounded-xl shadow transition flex items-center gap-1.5">
                <i class="fa-brands fa-whatsapp text-sm"></i> Kirim WA
            </button>
            <button onclick="document.getElementById('modalResetPass').classList.remove('hidden')" class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-3 py-2 rounded-xl shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-key"></i> Reset Password
            </button>
            <?php if ($isOnline): ?>
            <button onclick="triggerToast('PPPoE Disconnect', 'Sesi PPPoE di-kick dari MikroTik NAS & dial ulang otomatis.')" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-3 py-2 rounded-xl shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-rotate"></i> Re-Koneksi
            </button>
            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mengisolir akun ini?');">
                <input type="hidden" name="action" value="toggle_isolate_customer">
                <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                <input type="hidden" name="redirect" value="crm/detail.php?id=<?= $customer['id'] ?>">
                <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold px-3 py-2 rounded-xl shadow-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-ban"></i> Isolir Akun
                </button>
            </form>
            <?php else: ?>
            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline">
                <input type="hidden" name="action" value="toggle_isolate_customer">
                <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                <input type="hidden" name="redirect" value="crm/detail.php?id=<?= $customer['id'] ?>">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3.5 py-2 rounded-xl shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check"></i> Aktivasi & Set Online
                </button>
            </form>
            <?php endif; ?>
            <a href="<?= base_url('tickets/list.php?customer_id=' . $customer['id']) ?>" class="bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-bold px-3 py-2 rounded-xl shadow-sm transition flex items-center gap-1.5">
                <i class="fa-solid fa-ticket"></i> Buka Tiket
            </a>
            <a href="<?= base_url('crm/instalasi.php?customer_name=' . urlencode($customer['name']) . '&package_name=' . urlencode($customer['package_name'] ?? '')) ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-2 rounded-xl transition flex items-center gap-1.5">
                <i class="fa-solid fa-screwdriver-wrench"></i> Terbitkan WO
            </a>
        </div>
    </div>

    <!-- 4 Telemetry & Status Indicator Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Connection & Auth Method -->
        <?php 
        $authMode = strtolower($customer['auth_method'] ?? 'pppoe');
        ?>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-network-wired text-blue-600"></i> Otentikasi Jaringan
                </h4>
                <?php if ($isOnline): ?>
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded text-[10px]">CONNECTED</span>
                <?php elseif ($isIsolated): ?>
                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 font-bold rounded text-[10px]">SUSPENDED</span>
                <?php else: ?>
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold rounded text-[10px]">BELUM AKTIF</span>
                <?php endif; ?>
            </div>
            <div class="space-y-1.5">
                <?php if ($authMode === 'dhcp'): ?>
                    <div class="flex justify-between"><span class="text-slate-400">Tipe Koneksi:</span><span class="px-1.5 py-0.2 bg-amber-50 text-amber-700 font-bold rounded text-[10px]">DHCP Lease / IPoE</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">MAC Binding:</span><strong class="font-mono text-slate-900"><?= $isOnline ? ('48:8F:5A:' . strtoupper(substr(md5($customer['id']), 0, 6))) : '-' ?></strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">IP Lease:</span><strong class="font-mono <?= $isOnline ? 'text-blue-600' : 'text-slate-400' ?>"><?= $isOnline ? ('10.100.30.' . (10 + ($customer['id'] % 200))) : 'Belum Ada Lease' ?></strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">DHCP Server:</span><strong class="font-mono text-slate-700"><?= $isOnline ? 'dhcp-pool-client' : '-' ?></strong></div>
                <?php elseif ($authMode === 'hotspot'): ?>
                    <div class="flex justify-between"><span class="text-slate-400">Tipe Koneksi:</span><span class="px-1.5 py-0.2 bg-pink-50 text-pink-700 font-bold rounded text-[10px]">Hotspot Captive</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">User Voucher:</span><strong class="font-mono text-slate-900">hs_<?= strtolower(str_replace(' ', '', $customer['name'])) ?></strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">IP Terpasang:</span><strong class="font-mono <?= $isOnline ? 'text-blue-600' : 'text-slate-400' ?>"><?= $isOnline ? ('192.168.88.' . (20 + ($customer['id'] % 200))) : 'Belum Ada IP' ?></strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">Profil:</span><strong class="text-slate-700"><?= htmlspecialchars($customer['package_name'] ?? 'HS-VIP') ?></strong></div>
                <?php elseif ($authMode === 'static'): ?>
                    <div class="flex justify-between"><span class="text-slate-400">Tipe Koneksi:</span><span class="px-1.5 py-0.2 bg-cyan-50 text-cyan-700 font-bold rounded text-[10px]">Static IP Gateway</span></div>
                    <div class="flex justify-between"><span class="text-slate-400">IP Public:</span><strong class="font-mono text-slate-900"><?= $isOnline ? ('103.144.20.' . (5 + ($customer['id'] % 250)) . '/30') : '-' ?></strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">Gateway:</span><strong class="font-mono text-blue-600"><?= $isOnline ? '103.144.20.1' : '-' ?></strong></div>
                    <div class="flex justify-between"><span class="text-slate-400">Interface:</span><strong class="font-mono text-slate-700"><?= $isOnline ? 'sfp-sfpplus1' : '-' ?></strong></div>
                <?php else: 
                    $pUser = !empty($customer['pppoe_user']) ? $customer['pppoe_user'] : '-';
                    $pPass = !empty($customer['pppoe_password']) ? $customer['pppoe_password'] : '-';
                ?>
                    <div class="flex justify-between"><span class="text-slate-400">Tipe Koneksi:</span><span class="px-1.5 py-0.2 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">PPPoE Client</span></div>
                    <div class="flex justify-between items-center"><span class="text-slate-400">Username:</span><strong class="font-mono text-slate-900 font-bold"><?= htmlspecialchars($pUser) ?></strong></div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Password:</span>
                        <div class="flex items-center gap-1.5 font-mono">
                            <span id="detailPassMask" class="font-bold text-slate-800 tracking-widest">••••••</span>
                            <span id="detailPassPlain" class="font-bold text-blue-600 hidden"><?= htmlspecialchars($pPass) ?></span>
                            <button type="button" onclick="toggleDetailPass()" class="text-slate-400 hover:text-blue-600 text-xs">
                                <i id="detailEyeIco" class="fa-solid fa-eye text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between"><span class="text-slate-400">IP Terpasang:</span>
                        <?php if ($isOnline): ?>
                            <strong class="font-mono text-blue-600">10.100.20.<?= 10 + ($customer['id'] % 200) ?></strong>
                        <?php elseif ($isIsolated): ?>
                            <strong class="font-mono text-rose-600">10.200.0.<?= 10 + ($customer['id'] % 200) ?> (Pool Isolir)</strong>
                        <?php else: ?>
                            <strong class="font-mono text-slate-400">Belum Terkoneksi (N/A)</strong>
                        <?php endif; ?>
                    </div>
                    <div class="pt-1 flex justify-end">
                        <button type="button" onclick="document.getElementById('modalResetPass').classList.remove('hidden')" class="text-[10px] text-blue-600 hover:text-blue-800 font-bold hover:underline">
                            <i class="fa-solid fa-key"></i> Reset Password
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card 2: Billing & PPN -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-file-invoice-dollar text-emerald-600"></i> Billing & Skema PPN
                </h4>
                <?php if (empty($custInvoices)): ?>
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold rounded text-[10px]">BELUM ADA INVOICE</span>
                <?php elseif (!empty($unpaidInvoices)): ?>
                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 font-bold rounded text-[10px]">MENUNGGAK (<?= count($unpaidInvoices) ?>)</span>
                <?php else: ?>
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded text-[10px]">LUNAS (<?= htmlspecialchars($latestPaidInvoice['billing_period'] ?? 'TERKINI') ?>)</span>
                <?php endif; ?>
            </div>
            <div class="space-y-1.5">
                <div class="flex justify-between"><span class="text-slate-400">Tarif Bulanan:</span><strong class="font-mono text-slate-900"><?= format_rupiah($customer['package_price'] ?? 250000) ?></strong></div>
                <div class="flex justify-between"><span class="text-slate-400">Skema PPN 11%:</span><span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px] uppercase"><?= htmlspecialchars($customer['ppn_scheme'] ?? 'include') ?></span></div>
                <div class="flex justify-between"><span class="text-slate-400">Siklus Billing:</span><strong class="text-slate-700"><?= (strtolower($customer['billing_type'] ?? 'postpaid') === 'prepaid') ? 'Prabayar (Rolling 30 Hari)' : 'Pascabayar (Tgl 1 - 20)' ?></strong></div>
                <div class="flex justify-between"><span class="text-slate-400">Metode Bayar:</span><strong class="text-slate-700"><?= htmlspecialchars($latestPaidInvoice['payment_method'] ?? 'QRIS / VA Bank BCA') ?></strong></div>
            </div>
        </div>

        <!-- Card 3: GPON & Optical Fiber -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-tower-broadcast text-indigo-600"></i> GPON & Optical Loss
                </h4>
                <?php if ($custWo): ?>
                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded text-[10px]">TERPASANG</span>
                <?php else: ?>
                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 font-bold rounded text-[10px]">BELUM INSTALASI</span>
                <?php endif; ?>
            </div>
            <div class="space-y-1.5">
                <div class="flex justify-between"><span class="text-slate-400">Redaman OPM:</span>
                    <?php if ($custWo && !empty($custWo['attenuation'])): ?>
                        <strong class="font-mono text-emerald-600 font-bold"><?= htmlspecialchars($custWo['attenuation']) ?></strong>
                    <?php else: ?>
                        <span class="text-slate-400 font-mono">Belum Diukur (N/A)</span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between"><span class="text-slate-400">ODP Box / Port:</span>
                    <?php if ($custWo && !empty($custWo['odp_port'])): ?>
                        <strong class="font-mono text-slate-900"><?= htmlspecialchars($custWo['odp_port']) ?></strong>
                    <?php else: ?>
                        <span class="text-slate-400">Belum Ditugaskan</span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between"><span class="text-slate-400">SN Modem ONT:</span>
                    <?php if ($custWo && !empty($custWo['ont_sn'])): ?>
                        <strong class="font-mono text-slate-700"><?= htmlspecialchars($custWo['ont_sn']) ?></strong>
                    <?php else: ?>
                        <span class="text-slate-400">Belum Dipasang</span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between"><span class="text-slate-400">Status SPK / WO:</span>
                    <strong class="text-slate-700"><?= $custWo ? htmlspecialchars($custWo['wo_no'] . ' (' . $custWo['status'] . ')') : '<span class="text-amber-600 font-normal">Perlu Terbitkan WO</span>' ?></strong>
                </div>
            </div>
        </div>

        <!-- Card 4: Bandwidth & Trafik -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                    <i class="fa-solid fa-chart-line text-purple-600"></i> Trafik Real-time
                </h4>
                <?php if ($isOnline): ?>
                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 font-bold rounded text-[10px]">UNLIMITED FUP</span>
                <?php elseif ($isIsolated): ?>
                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 font-bold rounded text-[10px]">ISOLATED (0 MB)</span>
                <?php else: ?>
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold rounded text-[10px]">OFFLINE (0 MB)</span>
                <?php endif; ?>
            </div>
            <div class="space-y-1.5">
                <div class="flex justify-between"><span class="text-slate-400">Download Rate:</span>
                    <?php if ($isOnline): ?>
                        <strong class="font-mono text-purple-600 font-bold">24.5 Mbps / <?= $customer['speed_mbps'] ?? 50 ?>M</strong>
                    <?php else: ?>
                        <span class="font-mono text-slate-400">0.0 Mbps / <?= $customer['speed_mbps'] ?? 50 ?>M</span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between"><span class="text-slate-400">Upload Rate:</span>
                    <?php if ($isOnline): ?>
                        <strong class="font-mono text-purple-600 font-bold">8.2 Mbps / <?= $customer['speed_mbps'] ?? 50 ?>M</strong>
                    <?php else: ?>
                        <span class="font-mono text-slate-400">0.0 Mbps / <?= $customer['speed_mbps'] ?? 50 ?>M</span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between"><span class="text-slate-400">Akumulasi Bulan Ini:</span>
                    <?php if ($isOnline): ?>
                        <strong class="text-slate-700 font-mono">312.4 GB</strong>
                    <?php else: ?>
                        <span class="text-slate-400 font-mono">0.0 GB</span>
                    <?php endif; ?>
                </div>
                <div class="flex justify-between"><span class="text-slate-400">MTU Jaringan:</span><strong class="font-mono text-slate-700">1492 (PPPoE)</strong></div>
            </div>
        </div>
    </div>

    <!-- Main Content 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Column: Identity & Map Location (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Customer Biodata Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-id-card text-blue-600"></i> Identitas & Kontak Pelanggan
                    </h3>
                    <button onclick="triggerToast('Edit Profil', 'Modal edit profil dibuka.')" class="text-blue-600 font-bold hover:underline">Edit</button>
                </div>
                <div class="space-y-2.5">
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Nomor Induk Kependudukan (NIK)</span>
                        <strong class="font-mono text-slate-900 text-xs"><?= htmlspecialchars($customer['nik'] ?? '3275041401920002') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Nomor WhatsApp Aktif</span>
                        <strong class="font-mono text-blue-600 text-xs"><?= htmlspecialchars($customer['phone']) ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Alamat Email</span>
                        <strong class="text-slate-800 text-xs"><?= htmlspecialchars($customer['email'] ?? 'budi.wijaya@gmail.com') ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Alamat Lengkap Pemasangan</span>
                        <p class="text-slate-700 text-xs leading-relaxed"><?= htmlspecialchars($customer['address']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Leaflet Interactive GPS Location Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-map-location-dot text-emerald-600"></i> Titik Koordinat GPS Pemasangan
                        </h3>
                        <p class="text-slate-400 font-mono text-[10px]"><?= $customer['gps_lat'] ?? -6.2891 ?>, <?= $customer['gps_lng'] ?? 106.9182 ?></p>
                    </div>
                    <a href="https://maps.google.com/?q=<?= $customer['gps_lat'] ?? -6.2891 ?>,<?= $customer['gps_lng'] ?? 106.9182 ?>" target="_blank" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-2.5 py-1 rounded-lg flex items-center gap-1 text-[11px]">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Google Maps
                    </a>
                </div>
                <!-- Leaflet Map Container -->
                <div id="custDetailMap" class="w-full h-48 rounded-xl border border-slate-200 z-10"></div>
            </div>
        </div>

        <!-- Right Column: Billing History & Support Tickets (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Billing History Table -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-receipt text-blue-600"></i> Riwayat Tagihan & Kwitansi Invoice
                        </h3>
                        <p class="text-slate-400">Daftar penerbitan invoice bulanan dan status pelunasan.</p>
                    </div>
                    <a href="<?= base_url('billing/daftar.php') ?>" class="text-blue-600 font-bold hover:underline">Lihat Semua Tagihan</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                                <th class="py-2.5 px-3">No. Invoice</th>
                                <th class="py-2.5 px-3">Periode</th>
                                <th class="py-2.5 px-3 font-mono text-right">Total Tagihan</th>
                                <th class="py-2.5 px-3 text-center">Status</th>
                                <th class="py-2.5 px-3 text-right">Kwitansi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($custInvoices)): ?>
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">Belum ada riwayat tagihan invoice untuk pelanggan ini.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($custInvoices as $inv): ?>
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 font-mono font-bold text-blue-600">
                                    <a href="<?= base_url('billing/invoice.php?id=' . $inv['id']) ?>" class="hover:underline"><?= htmlspecialchars($inv['invoice_no']) ?></a>
                                </td>
                                <td class="py-2.5 px-3 text-slate-600"><?= htmlspecialchars($inv['billing_period']) ?></td>
                                <td class="py-2.5 px-3 font-mono font-bold text-slate-900 text-right"><?= format_rupiah($inv['total_amount']) ?></td>
                                <td class="py-2.5 px-3 text-center">
                                    <?php if (strtolower($inv['status']) === 'lunas' || strtolower($inv['status']) === 'paid'): ?>
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[9px]">LUNAS</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 bg-red-50 text-red-700 font-bold rounded-full text-[9px]">BELUM BAYAR</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2.5 px-3 text-right">
                                    <a href="<?= base_url('billing/invoice.php?id=' . $inv['id']) ?>" class="text-blue-600 font-bold hover:underline">📄 Kwitansi</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Support Ticket History Table -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                            <i class="fa-solid fa-ticket text-amber-600"></i> Riwayat Tiket Komplain & Bantuan Teknis
                        </h3>
                        <p class="text-slate-400">Catatan gangguan, penugasan teknisi, dan penyelesaian masalah.</p>
                    </div>
                    <button onclick="triggerToast('Buat Tiket', 'Form pembukaan tiket keluhan dibuka.')" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-3 py-1.5 rounded-xl text-[11px] shadow-sm transition">
                        + Buat Tiket Baru
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                                <th class="py-2.5 px-3">No. Tiket</th>
                                <th class="py-2.5 px-3">Keluhan Masalah</th>
                                <th class="py-2.5 px-3">Teknisi Bertugas</th>
                                <th class="py-2.5 px-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($custTickets)): ?>
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400">Belum ada riwayat tiket gangguan untuk pelanggan ini.</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($custTickets as $t): ?>
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                                <td class="py-2.5 px-3 font-mono font-bold text-blue-600"><?= htmlspecialchars($t['ticket_no'] ?? 'TCK-001') ?></td>
                                <td class="py-2.5 px-3 text-slate-700 font-medium"><?= htmlspecialchars($t['category'] ?? ($t['subject'] ?? 'Gangguan Layanan')) ?></td>
                                <td class="py-2.5 px-3 text-slate-600"><?= htmlspecialchars($t['assigned_tech'] ?? ($t['technician_name'] ?? 'Tim Lapangan')) ?></td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 <?= (strtolower($t['status'] ?? '') === 'closed' || strtolower($t['status'] ?? '') === 'solved') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' ?> border font-bold rounded-full text-[9px]"><?= htmlspecialchars($t['status'] ?? 'OPEN') ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password PPPoE -->
<div id="modalResetPass" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm">Reset Password RADIUS PPPoE</h3>
            <button onclick="document.getElementById('modalResetPass').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        <form onsubmit="event.preventDefault(); document.getElementById('modalResetPass').classList.add('hidden'); triggerToast('Password Berhasil Direset', 'Password baru telah disinkronkan ke RADIUS & dikirim ke WA pelanggan!');" class="space-y-3">
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Username PPPoE</label>
                <input type="text" readonly value="pppoe_<?= strtolower(str_replace(' ', '_', $customer['name'])) ?>" class="w-full bg-slate-100 border border-slate-200 rounded-lg p-2 font-mono font-bold text-slate-600">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Password Baru</label>
                <input type="text" required value="NetProPass#<?= rand(100, 999) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold text-blue-600">
            </div>
            <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl text-blue-900 text-[11px]">
                Password baru akan otomatis terkirim via bot WhatsApp ke nomor <strong><?= htmlspecialchars($customer['phone']) ?></strong>.
            </div>
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-2.5 rounded-xl shadow-lg shadow-brand-950/30 transition">Simpan & Kirim ke WhatsApp</button>
        </form>
    </div>
</div>

<!-- Leaflet Map Initialization Script -->
<script>
function toggleDetailPass() {
    var mask = document.getElementById('detailPassMask');
    var plain = document.getElementById('detailPassPlain');
    var ico = document.getElementById('detailEyeIco');
    if (!mask || !plain || !ico) return;

    if (plain.classList.contains('hidden')) {
        plain.classList.remove('hidden');
        mask.classList.add('hidden');
        ico.className = 'fa-solid fa-eye-slash text-[10px] text-blue-600';
    } else {
        plain.classList.add('hidden');
        mask.classList.remove('hidden');
        ico.className = 'fa-solid fa-eye text-[10px] text-slate-400';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var lat = <?= $customer['gps_lat'] ?? -6.2891 ?>;
    var lng = <?= $customer['gps_lng'] ?? 106.9182 ?>;
    var custName = "<?= addslashes($customer['name']) ?>";
    
    if (typeof L !== 'undefined') {
        var map = L.map('custDetailMap').setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup("<b>" + custName + "</b><br><?= addslashes($customer['address']) ?>").openPopup();
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
