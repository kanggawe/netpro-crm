<?php
/**
 * Ultra-Elegant Enterprise Modular Sidebar Component
 * Sleek Dark Obsidian Theme with Neon Glassmorphism & Custom Minimal Scrollbar
 */
$currentUri = $_SERVER['PHP_SELF'];

$menuSections = [
    [
        'category' => 'MAIN MONITORING',
        'menus' => [
            [
                'id' => 'm-dashboard',
                'title' => 'Executive Dashboard',
                'icon' => 'fa-chart-pie',
                'color' => 'text-blue-400',
                'items' => [
                    ['title' => 'Ringkasan Bisnis Utama', 'url' => 'dashboard/utama.php', 'icon' => 'fa-gauge-high'],
                    ['title' => 'Pendapatan Bulan Berjalan', 'url' => 'dashboard/revenue.php', 'icon' => 'fa-arrow-trend-up'],
                    ['title' => 'Tagihan Jatuh Tempo', 'url' => 'dashboard/overdue.php', 'icon' => 'fa-clock-rotate-left'],
                    ['title' => 'Pelanggan Aktif / Suspend', 'url' => 'dashboard/customers.php', 'icon' => 'fa-users-line'],
                    ['title' => 'Tiket & Antrean Gangguan', 'url' => 'dashboard/tickets.php', 'icon' => 'fa-triangle-exclamation'],
                    ['title' => 'Status Jaringan & POP NOC', 'url' => 'dashboard/noc.php', 'icon' => 'fa-network-wired'],
                    ['title' => 'Statistik Karyawan & Absensi', 'url' => 'dashboard/hr.php', 'icon' => 'fa-user-check']
                ]
            ]
        ]
    ],
    [
        'category' => 'OPERATIONAL & CRM',
        'menus' => [
            [
                'id' => 'm-crm',
                'title' => 'CRM & Pelanggan',
                'icon' => 'fa-users-gear',
                'color' => 'text-indigo-400',
                'items' => [
                    ['title' => 'Daftar Pelanggan', 'url' => 'crm/daftar.php', 'icon' => 'fa-address-book'],
                    ['title' => 'Registrasi Akun Baru', 'url' => 'crm/registrasi.php', 'icon' => 'fa-user-plus'],
                    ['title' => '360° Profil & Telemetri', 'url' => 'crm/detail.php', 'icon' => 'fa-id-card'],
                    ['title' => 'Riwayat Berlangganan', 'url' => 'crm/riwayat.php', 'icon' => 'fa-history'],
                    ['title' => 'Katalog Paket Internet', 'url' => 'crm/paket.php', 'icon' => 'fa-box-open'],
                    ['title' => 'Add-on & Vas Layanan', 'url' => 'crm/addon.php', 'icon' => 'fa-puzzle-piece'],
                    ['title' => 'Promo & Voucher Diskon', 'url' => 'crm/promo.php', 'icon' => 'fa-tags'],
                    ['title' => 'Jadwal Survey Lokasi', 'url' => 'crm/survey.php', 'icon' => 'fa-map-location-dot'],
                    ['title' => 'Pemasangan Baru (WO)', 'url' => 'crm/instalasi.php', 'icon' => 'fa-tools'],
                    ['title' => 'Berita Acara (BAST)', 'url' => 'crm/berita_acara.php', 'icon' => 'fa-file-signature']
                ]
            ],
            [
                'id' => 'm-noc',
                'title' => 'NOC & Network Ops',
                'icon' => 'fa-microchip',
                'color' => 'text-amber-400',
                'items' => [
                    ['title' => 'Status Jaringan & POP', 'url' => 'noc/monitoring.php', 'icon' => 'fa-heart-pulse'],
                    ['title' => 'Topologi Backbone ISP', 'url' => 'noc/topologi.php', 'icon' => 'fa-diagram-project'],
                    ['title' => 'Manajemen OLT GPON', 'url' => 'noc/olt.php', 'icon' => 'fa-tower-broadcast'],
                    ['title' => 'Frame OTB & ODF Server', 'url' => 'noc/otb.php', 'icon' => 'fa-circle-nodes'],
                    ['title' => 'Kabinet ODC (FDT)', 'url' => 'noc/odc.php', 'icon' => 'fa-layer-group'],
                    ['title' => 'Peta Sebaran ODP', 'url' => 'noc/odp.php', 'icon' => 'fa-map-location-dot'],
                    ['title' => 'Armada Modem ONU/ONT', 'url' => 'noc/onu.php', 'icon' => 'fa-satellite-dish'],
                    ['title' => 'Manajemen MikroTik API', 'url' => 'noc/mikrotik.php', 'icon' => 'fa-sliders'],
                    ['title' => 'Insiden & Outage Fiber', 'url' => 'noc/outage.php', 'icon' => 'fa-scissors']
                ]
            ],
            [
                'id' => 'm-tickets',
                'title' => 'Ticketing & CSAT',
                'icon' => 'fa-headset',
                'color' => 'text-rose-400',
                'items' => [
                    ['title' => 'Daftar Tiket Gangguan', 'url' => 'tickets/list.php', 'icon' => 'fa-ticket-simple'],
                    ['title' => 'Komplain & Survey CSAT', 'url' => 'tickets/complaints.php', 'icon' => 'fa-comments']
                ]
            ]
        ]
    ],
    [
        'category' => 'COMMERCE & BILLING',
        'menus' => [
            [
                'id' => 'm-billing',
                'title' => 'Billing & Tagihan',
                'icon' => 'fa-credit-card',
                'color' => 'text-emerald-400',
                'items' => [
                    ['title' => 'Generate Tagihan Massal', 'url' => 'billing/generate.php', 'icon' => 'fa-file-invoice-dollar'],
                    ['title' => 'Daftar Tagihan Pelanggan', 'url' => 'billing/daftar.php', 'icon' => 'fa-list-check'],
                    ['title' => 'Input Pembayaran Kasir', 'url' => 'billing/pembayaran.php', 'icon' => 'fa-money-bill-wave'],
                    ['title' => 'Riwayat Transaksi', 'url' => 'billing/riwayat.php', 'icon' => 'fa-receipt'],
                    ['title' => 'Cetak Invoice PDF', 'url' => 'billing/invoice.php', 'icon' => 'fa-print'],
                    ['title' => 'Pengaturan Denda', 'url' => 'billing/denda.php', 'icon' => 'fa-triangle-exclamation']
                ]
            ],
            [
                'id' => 'm-radius',
                'title' => 'RADIUS Server Engine',
                'icon' => 'fa-network-wired',
                'color' => 'text-cyan-400',
                'items' => [
                    ['title' => 'Router NAS MikroTik', 'url' => 'radius/nas.php', 'icon' => 'fa-server'],
                    ['title' => 'Pengguna PPPoE & Hotspot', 'url' => 'radius/users.php', 'icon' => 'fa-users-gear'],
                    ['title' => 'Sesi Aktif Real-time', 'url' => 'radius/sessions.php', 'icon' => 'fa-bolt'],
                    ['title' => 'Profil Bandwidth (Queue)', 'url' => 'radius/profiles.php', 'icon' => 'fa-gauge-simple-high'],
                    ['title' => 'Manajemen Voucher Hotspot', 'url' => 'radius/vouchers.php', 'icon' => 'fa-ticket'],
                    ['title' => 'Laporan Penggunaan Data', 'url' => 'radius/reports.php', 'icon' => 'fa-chart-area']
                ]
            ],
            [
                'id' => 'm-marketing',
                'title' => 'Marketing & Sales',
                'icon' => 'fa-bullhorn',
                'color' => 'text-pink-400',
                'items' => [
                    ['title' => 'Prospek & Leads Sales', 'url' => 'marketing/leads.php', 'icon' => 'fa-filter-circle-dollar'],
                    ['title' => 'Broadcast Promo WhatsApp', 'url' => 'marketing/campaign.php', 'icon' => 'fa-paper-plane'],
                    ['title' => 'Target & Komisi Sales', 'url' => 'marketing/sales.php', 'icon' => 'fa-chart-pie']
                ]
            ],
            [
                'id' => 'm-kalkulator',
                'title' => 'Kalkulator ISP Tools',
                'icon' => 'fa-calculator',
                'color' => 'text-amber-400',
                'items' => [
                    ['title' => 'Kalkulator Bandwidth & CIR', 'url' => 'kalkulator/bandwidth.php', 'icon' => 'fa-gauge-high'],
                    ['title' => 'Kalkulator Pajak & PPN', 'url' => 'kalkulator/pajak.php', 'icon' => 'fa-percent']
                ]
            ]
        ]
    ],
    [
        'category' => 'ENTERPRISE & FINANCE',
        'menus' => [
            [
                'id' => 'm-finance',
                'title' => 'Keuangan & Akuntansi',
                'icon' => 'fa-wallet',
                'color' => 'text-teal-400',
                'items' => [
                    ['title' => 'Arus Kas & Bank Rekening', 'url' => 'finance/kas.php', 'icon' => 'fa-money-bill-transfer'],
                    ['title' => 'Pengeluaran Biaya OPEX', 'url' => 'finance/pengeluaran.php', 'icon' => 'fa-receipt'],
                    ['title' => 'Buku Besar & COA Akun', 'url' => 'finance/akuntansi.php', 'icon' => 'fa-book-bookmark'],
                    ['title' => 'Laporan Laba Rugi & Neraca', 'url' => 'finance/laporan.php', 'icon' => 'fa-scale-balanced'],
                    ['title' => 'Kewajiban Pajak & PNBP', 'url' => 'finance/pajak.php', 'icon' => 'fa-file-invoice-dollar']
                ]
            ],
            [
                'id' => 'm-inventory',
                'title' => 'Inventory & Asset Tools',
                'icon' => 'fa-boxes-stacked',
                'color' => 'text-orange-400',
                'items' => [
                    ['title' => 'Stok Perangkat & Material', 'url' => 'inventory/barang.php', 'icon' => 'fa-box'],
                    ['title' => 'Mutasi Keluar & Masuk', 'url' => 'inventory/stok.php', 'icon' => 'fa-right-left'],
                    ['title' => 'Aset Kantor & Toolkit Fiber', 'url' => 'inventory/asset.php', 'icon' => 'fa-screwdriver-wrench'],
                    ['title' => 'Supplier & Purchase Order', 'url' => 'inventory/supplier.php', 'icon' => 'fa-truck-field']
                ]
            ],
            [
                'id' => 'm-hr',
                'title' => 'HR & Manajemen Staf',
                'icon' => 'fa-user-tie',
                'color' => 'text-violet-400',
                'items' => [
                    ['title' => 'Master Data Karyawan', 'url' => 'hr/karyawan.php', 'icon' => 'fa-id-badge'],
                    ['title' => 'Absensi & Presensi GPS', 'url' => 'hr/absensi.php', 'icon' => 'fa-calendar-check'],
                    ['title' => 'Pengajuan Cuti & Izin', 'url' => 'hr/cuti.php', 'icon' => 'fa-plane-departure']
                ]
            ],
            [
                'id' => 'm-kinerja',
                'title' => 'Penilaian Kinerja Tim',
                'icon' => 'fa-award',
                'color' => 'text-yellow-400',
                'items' => [
                    ['title' => 'Master KPI Divisi & SLA', 'url' => 'kinerja/kpi.php', 'icon' => 'fa-list-ol'],
                    ['title' => 'Target Kerja & Okr Bulanan', 'url' => 'kinerja/target.php', 'icon' => 'fa-bullseye'],
                    ['title' => 'Review Evaluasi Karyawan', 'url' => 'kinerja/review.php', 'icon' => 'fa-user-pen'],
                    ['title' => 'Leaderboard & Prestasi', 'url' => 'kinerja/laporan.php', 'icon' => 'fa-trophy']
                ]
            ],
            [
                'id' => 'm-payroll',
                'title' => 'Payroll & Penggajian',
                'icon' => 'fa-money-bill-wave',
                'color' => 'text-emerald-400',
                'items' => [
                    ['title' => 'Master Komponen Gaji', 'url' => 'payroll/master.php', 'icon' => 'fa-coins'],
                    ['title' => 'Generate & Slip Gaji', 'url' => 'payroll/generate.php', 'icon' => 'fa-file-invoice'],
                    ['title' => 'Insentif & Bonus Pasang', 'url' => 'payroll/bonus.php', 'icon' => 'fa-hand-holding-dollar'],
                    ['title' => 'Rekapitulasi Gaji & BPJS', 'url' => 'payroll/laporan.php', 'icon' => 'fa-scale-balanced']
                ]
            ]
        ]
    ],
    [
        'category' => 'SYSTEM & CONTROLS',
        'menus' => [
            [
                'id' => 'm-laporan',
                'title' => 'Laporan Eksekutif',
                'icon' => 'fa-file-lines',
                'color' => 'text-sky-400',
                'items' => [
                    ['title' => 'Laporan Summary Eksekutif', 'url' => 'laporan/summary.php', 'icon' => 'fa-file-export']
                ]
            ],
            [
                'id' => 'm-pengaturan',
                'title' => 'Pengaturan Sistem',
                'icon' => 'fa-gear',
                'color' => 'text-slate-400',
                'items' => [
                    ['title' => 'Konfigurasi Sistem & Server', 'url' => 'pengaturan/sistem.php', 'icon' => 'fa-gears'],
                    ['title' => 'Identitas & Cabang ISP', 'url' => 'pengaturan/perusahaan.php', 'icon' => 'fa-building'],
                    ['title' => 'Otomatisasi Billing & Denda', 'url' => 'pengaturan/billing_config.php', 'icon' => 'fa-calendar-check'],
                    ['title' => 'Payment Gateways & QRIS', 'url' => 'pengaturan/payment_gateway.php', 'icon' => 'fa-credit-card'],
                    ['title' => 'WhatsApp & Server Notifikasi', 'url' => 'pengaturan/wa_gateway.php', 'icon' => 'fa-comments'],
                    ['title' => 'Integrasi MikroTik & RADIUS', 'url' => 'pengaturan/api.php', 'icon' => 'fa-code-branch'],
                    ['title' => 'Backup & Database Restore', 'url' => 'pengaturan/backup.php', 'icon' => 'fa-database'],
                    ['title' => 'User Admin & Roles RBAC', 'url' => 'pengaturan/users.php', 'icon' => 'fa-user-lock'],
                    ['title' => 'Audit Logs System Trail', 'url' => 'pengaturan/logs.php', 'icon' => 'fa-clock-rotate-left'],
                    ['title' => 'Lisensi & Aktivasi Sistem', 'url' => 'pengaturan/lisensi.php', 'icon' => 'fa-certificate'],
                    ['title' => 'Tentang Aplikasi Platform', 'url' => 'pengaturan/about.php', 'icon' => 'fa-circle-info']
                ]
            ]
        ]
    ]
];
?>

<style>
/* Sleek Custom Scrollbar for RedDash Sidebar */
#sidebarMenuScroll {
    scrollbar-width: thin;
    scrollbar-color: rgba(220, 38, 38, 0.3) transparent;
    scroll-behavior: smooth;
}
#sidebarMenuScroll::-webkit-scrollbar {
    width: 4px;
}
#sidebarMenuScroll::-webkit-scrollbar-track {
    background: transparent;
}
#sidebarMenuScroll::-webkit-scrollbar-thumb {
    background: rgba(220, 38, 38, 0.3);
    border-radius: 9999px;
    transition: background 0.2s ease-in-out;
}
#sidebarMenuScroll::-webkit-scrollbar-thumb:hover {
    background: rgba(220, 38, 38, 0.7);
    box-shadow: 0 0 6px rgba(220, 38, 38, 0.5);
}
.sidebar-glow-active {
    background: rgba(255, 255, 255, 0.2) !important;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.35) !important;
    color: #ffffff !important;
    font-weight: 700;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}
</style>

<aside id="sidebar" class="bg-gradient-to-b from-brand-900 via-brand-950 to-brand-950 text-white w-72 md:w-76 flex-shrink-0 fixed md:static inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-40 flex flex-col shadow-2xl border-r border-brand-700/40 select-none overflow-hidden h-full">
    
    <!-- Glowing Ambient Lighting Effects -->
    <div class="absolute top-0 left-1/4 w-32 h-32 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 right-0 w-32 h-32 bg-rose-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <?php 
    $sidebarLogo = Setting::get('app_logo_url', base_url('assets/images/netpro-logo.svg'));
    if (empty($sidebarLogo)) $sidebarLogo = base_url('assets/images/netpro-logo.svg');
    ?>
    <!-- Logo Header Component -->
    <div class="h-20 flex items-center px-5 border-b border-white/10 bg-black/10 relative z-10 shrink-0">
        <div class="relative group cursor-pointer shrink-0">
            <div class="absolute -inset-1 bg-gradient-to-r from-white to-red-300 rounded-2xl blur opacity-50 group-hover:opacity-100 transition duration-300"></div>
            <div class="relative h-11 w-11 rounded-xl bg-gradient-to-br from-white to-red-100 flex items-center justify-center text-brand-700 shadow-xl ring-2 ring-white/30 overflow-hidden">
                <img src="<?= htmlspecialchars($sidebarLogo) ?>" alt="NETPRO Logo" class="w-full h-full object-contain p-0.5 group-hover:scale-105 transition-transform" onerror="this.src='<?= base_url('assets/images/netpro-logo.svg') ?>'">
            </div>
        </div>
        <div class="ml-3.5 overflow-hidden">
            <div class="flex items-center space-x-2">
                <span class="text-sm font-extrabold tracking-wider bg-gradient-to-r from-white via-red-100 to-red-200 bg-clip-text text-transparent uppercase truncate"><?= APP_NAME ?></span>
                <span class="h-2 w-2 rounded-full bg-emerald-400 inline-block shadow-[0_0_8px_rgba(52,211,153,0.8)] shrink-0"></span>
            </div>
            <p class="text-[9px] text-red-200/90 font-bold uppercase tracking-widest mt-0.5 truncate"><?= APP_DESC ?></p>
        </div>
        <button onclick="toggleMobileSidebar()" class="md:hidden ml-auto text-red-200 hover:text-white p-1.5 rounded-lg hover:bg-white/10">
            <i class="fa-solid fa-xmark text-base"></i>
        </button>
    </div>

    <!-- Interactive Search Input -->
    <div class="p-3.5 relative z-10 shrink-0">
        <div class="flex items-center bg-black/20 hover:bg-black/30 focus-within:bg-black/30 transition-all duration-200 rounded-xl px-3 py-2 border border-white/15 focus-within:border-white/40 shadow-inner group">
            <i class="fa-solid fa-magnifying-glass text-red-200 mr-2.5 text-xs group-focus-within:text-white transition"></i>
            <input type="text" id="sidebarSearch" onkeyup="filterSidebarMenu()" placeholder="Cari modul atau menu..." class="bg-transparent border-none outline-none text-white w-full placeholder-red-200/70 text-xs font-medium">
            <span class="bg-white/15 text-red-100 px-1.5 py-0.5 rounded text-[9px] font-mono border border-white/10 shadow-sm pointer-events-none">/</span>
        </div>
    </div>

    <!-- Navigation Menu Items -->
    <div id="sidebarMenuScroll" class="flex-1 overflow-y-auto px-3.5 py-2 space-y-5 relative z-10">
        <?php foreach ($menuSections as $sec): 
            $allowedMenus = array_filter($sec['menus'], function($m) {
                return can_access($m['id']);
            });
            if (empty($allowedMenus)) continue;
        ?>
        <div>
            <!-- Section Header Category -->
            <p class="px-3 text-[10px] font-extrabold text-red-200/90 uppercase tracking-widest mb-2.5 flex items-center">
                <span class="w-1.5 h-1.5 rounded-full bg-red-400 mr-2 shadow-[0_0_6px_rgba(248,113,113,0.9)]"></span>
                <span><?= $sec['category'] ?></span>
            </p>

            <div class="space-y-1.5">
                <?php foreach ($allowedMenus as $grp): 
                    $hasActiveItem = false;
                    foreach ($grp['items'] as $it) {
                        if (strpos($currentUri, $it['url']) !== false) {
                            $hasActiveItem = true;
                            break;
                        }
                    }
                    $isOpen = ($activeMenu === $grp['id']) || $hasActiveItem;
                    $rotate = $isOpen ? 'style="transform: rotate(180deg);"' : '';
                    $openClass = $isOpen ? 'open' : '';
                    $parentActive = $isOpen 
                        ? 'bg-white text-brand-900 font-bold shadow-lg shadow-black/20 border border-white' 
                        : 'text-red-100/90 hover:bg-white/10 hover:text-white';
                    $iconBox = $isOpen
                        ? 'bg-brand-900/10 text-brand-700 shadow-sm'
                        : 'bg-white/10 text-red-200 group-hover:bg-white/20 group-hover:text-white';
                ?>
                <div class="menu-group">
                    <button onclick="toggleSidebarMenu('<?= $grp['id'] ?>')" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group <?= $parentActive ?>">
                        <div class="flex items-center">
                            <div class="h-7 w-7 rounded-lg flex items-center justify-center mr-2.5 transition <?= $iconBox ?>">
                                <i class="fa-solid <?= $grp['icon'] ?> text-xs"></i>
                            </div>
                            <span class="text-xs font-semibold tracking-wide"><?= $grp['title'] ?></span>
                        </div>
                        <i id="arrow-<?= $grp['id'] ?>" class="fa-solid fa-chevron-down text-[9px] <?= $isOpen ? 'text-brand-900' : 'text-red-300 group-hover:text-white' ?> transition-transform duration-200" <?= $rotate ?>></i>
                    </button>

                    <!-- Submenu Links Container -->
                    <div id="<?= $grp['id'] ?>" class="submenu-container <?= $openClass ?> pl-3.5 pt-1.5 space-y-1 border-l border-white/20 ml-4 mt-1">
                        <?php foreach ($grp['items'] as $item): 
                            $targetUrl = base_url($item['url']);
                            $isActive = strpos($currentUri, $item['url']) !== false;
                            $itemClass = $isActive 
                                ? 'sidebar-glow-active' 
                                : 'text-red-100/80 hover:text-white hover:bg-white/10';
                            $bulletColor = $isActive ? 'text-white' : 'text-red-300 group-hover/sub:text-white';
                        ?>
                        <a href="<?= $targetUrl ?>" class="submenu-item flex items-center px-3 py-2 rounded-lg text-xs transition duration-150 group/sub <?= $itemClass ?>">
                            <i class="fa-solid <?= $item['icon'] ?> text-[10px] mr-2.5 <?= $bulletColor ?> transition"></i>
                            <span class="truncate"><?= $item['title'] ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- User Profile Footer Component -->
    <?php $authUser = auth_user(); ?>
    <div class="p-3.5 border-t border-white/10 bg-black/20 backdrop-blur-md relative z-10 shrink-0">
        <div class="flex items-center justify-between bg-white/10 hover:bg-white/15 transition p-2.5 rounded-2xl border border-white/10 shadow-lg">
            <a href="<?= base_url('pengaturan/profile.php') ?>" class="flex items-center space-x-2.5 overflow-hidden flex-1 group" title="Buka Detail Akun User">
                <div class="relative shrink-0">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-brand-950 to-brand-700 flex items-center justify-center font-bold text-white shadow-md ring-2 ring-white/30 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-user-shield text-xs"></i>
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 bg-emerald-500 rounded-full border-2 border-brand-950 shadow"></span>
                </div>
                <div class="overflow-hidden">
                    <h4 class="text-xs font-bold text-white leading-tight truncate group-hover:text-red-100 transition-colors"><?= htmlspecialchars($authUser['full_name'] ?? 'Super Administrator') ?></h4>
                    <p class="text-[10px] text-red-200/90 font-mono mt-0.5 truncate"><?= htmlspecialchars($authUser['role'] ?? 'Super Administrator') ?></p>
                </div>
            </a>
            <a href="<?= base_url('logout.php') ?>" class="text-red-300 hover:text-white transition p-1.5 rounded-xl hover:bg-white/10" title="Log Out Pegawai">
                <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
            </a>
        </div>
    </div>
</aside>

<script>
// Auto-center active menu in sidebar view immediately on render
(function() {
    function centerActiveMenu() {
        var activeEl = document.querySelector('.sidebar-glow-active') || document.querySelector('.submenu-item.sidebar-glow-active');
        var scrollBox = document.getElementById('sidebarMenuScroll');
        if (activeEl && scrollBox) {
            var elTop = activeEl.offsetTop;
            var elHeight = activeEl.offsetHeight;
            var boxHeight = scrollBox.clientHeight;
            scrollBox.scrollTop = elTop - (boxHeight / 2) + (elHeight / 2);
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', centerActiveMenu);
    } else {
        centerActiveMenu();
    }
    setTimeout(centerActiveMenu, 100);
})();
</script>
