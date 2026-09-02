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
/* Sleek Custom Scrollbar for Ultra-Elegant Sidebar */
#sidebarMenuScroll::-webkit-scrollbar {
    width: 4px;
}
#sidebarMenuScroll::-webkit-scrollbar-track {
    background: transparent;
}
#sidebarMenuScroll::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.15);
    border-radius: 9999px;
}
#sidebarMenuScroll::-webkit-scrollbar-thumb:hover {
    background: rgba(59, 130, 246, 0.5);
}
.sidebar-glow-active {
    background: linear-gradient(90deg, rgba(37, 99, 235, 0.18) 0%, rgba(30, 58, 138, 0.08) 60%, transparent 100%);
    border-left: 3px solid #3b82f6;
    color: #60a5fa !important;
    font-weight: 700;
}
</style>

<aside id="sidebar" class="w-68 bg-[#090d16] border-r border-slate-800/70 flex flex-col h-full z-40 transition-all duration-300 shrink-0 md:relative absolute -translate-x-full md:translate-x-0 select-none">
    <?php 
    $sidebarLogo = Setting::get('app_logo_url', '');
    ?>
    <!-- Brand Header -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800/80 bg-[#060911]/90 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-400 flex items-center justify-center font-black text-white shadow-lg shadow-blue-600/30 ring-1 ring-white/20 overflow-hidden">
                <?php if (!empty($sidebarLogo)): ?>
                    <img src="<?= htmlspecialchars($sidebarLogo) ?>" alt="Logo" class="w-full h-full object-contain p-1" onerror="this.outerHTML='<i class=\'fa-solid fa-tower-cell text-sm\'></i>'">
                <?php else: ?>
                    <i class="fa-solid fa-tower-cell text-sm"></i>
                <?php endif; ?>
            </div>
            <div>
                <div class="flex items-center gap-1.5">
                    <h1 class="font-extrabold text-white text-xs tracking-wider uppercase"><?= APP_NAME ?></h1>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                </div>
                <span class="text-[9px] text-slate-400 font-semibold tracking-widest block uppercase"><?= APP_DESC ?></span>
            </div>
        </div>
        <button onclick="toggleMobileSidebar()" class="md:hidden text-slate-400 hover:text-white text-lg p-1.5 rounded-lg hover:bg-slate-800/60 transition">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Sleek Spotlight Search Box -->
    <div class="px-3 pt-3 pb-2 bg-[#090d16]">
        <div class="relative group">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-500 group-focus-within:text-blue-400 text-xs transition-colors"></i>
            <input type="text" id="sidebarSearch" onkeyup="filterSidebarMenu()" placeholder="Cari modul atau menu..." class="w-full bg-[#0e1424] border border-slate-800/90 rounded-xl py-1.5 pl-8 pr-9 text-[11px] text-slate-200 placeholder-slate-500 focus:outline-none focus:border-blue-500/80 focus:ring-2 focus:ring-blue-500/15 transition">
            <span class="absolute right-2.5 top-2 px-1.5 py-0.5 rounded bg-slate-800/80 border border-slate-700/50 text-[9px] font-mono text-slate-400 font-semibold pointer-events-none">/</span>
        </div>
    </div>

    <!-- Navigation Menu List -->
    <div id="sidebarMenuScroll" class="flex-1 overflow-y-auto px-3 py-2 space-y-4">
        <?php foreach ($menuSections as $sec): 
            $allowedMenus = array_filter($sec['menus'], function($m) {
                return can_access($m['id']);
            });
            if (empty($allowedMenus)) continue;
        ?>
        <div class="space-y-1">
            <!-- Micro Category Label -->
            <div class="px-2.5 pt-2 pb-1 text-[9px] font-bold tracking-widest text-slate-500 uppercase flex items-center justify-between">
                <span><?= $sec['category'] ?></span>
            </div>

            <?php foreach ($allowedMenus as $grp): 
                $isOpen = ($activeMenu === $grp['id']);
                $rotate = $isOpen ? 'style="transform: rotate(180deg);"' : '';
                $openClass = $isOpen ? 'open' : '';
            ?>
            <div class="menu-group">
                <button onclick="toggleSidebarMenu('<?= $grp['id'] ?>')" class="w-full flex justify-between items-center px-2.5 py-2 text-slate-300 hover:text-white hover:bg-slate-800/40 rounded-xl text-xs font-medium tracking-normal transition-all duration-200 group">
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-lg bg-slate-800/50 flex items-center justify-center group-hover:bg-slate-800 transition">
                            <i class="fa-solid <?= $grp['icon'] ?> <?= $grp['color'] ?> text-xs group-hover:scale-110 transition-transform"></i>
                        </div>
                        <span class="font-medium text-slate-200 text-xs"><?= $grp['title'] ?></span>
                    </div>
                    <i id="arrow-<?= $grp['id'] ?>" class="fa-solid fa-chevron-down text-[9px] text-slate-500 transition-transform duration-200" <?= $rotate ?>></i>
                </button>

                <!-- Submenu Accordion Container -->
                <div id="<?= $grp['id'] ?>" class="submenu-container <?= $openClass ?> mt-1 pl-3.5 space-y-0.5 border-l border-slate-800/70 ml-3.5">
                    <?php foreach ($grp['items'] as $item): 
                        $targetUrl = base_url($item['url']);
                        $isActive = strpos($currentUri, $item['url']) !== false;
                        $itemClass = $isActive 
                            ? 'sidebar-glow-active' 
                            : 'text-slate-400 hover:text-slate-100 hover:bg-slate-800/30 hover:translate-x-0.5';
                    ?>
                    <a href="<?= $targetUrl ?>" class="submenu-item flex items-center py-1.5 px-2.5 text-[11px] rounded-lg transition-all duration-150 <?= $itemClass ?>">
                        <i class="fa-solid <?= $item['icon'] ?> text-[10px] w-4 opacity-70"></i>
                        <span class="truncate"><?= $item['title'] ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Refined User Profile Footer Card -->
    <?php $authUser = auth_user(); ?>
    <div class="p-3 bg-[#060911]/95 border-t border-slate-800/80">
        <div class="p-2 bg-[#0e1424]/80 border border-slate-800/70 rounded-xl flex items-center justify-between shadow-inner">
            <a href="<?= base_url('pengaturan/profile.php') ?>" class="flex items-center gap-2.5 overflow-hidden flex-1 hover:opacity-90 transition group" title="Buka Detail Akun User">
                <div class="relative shrink-0">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center font-bold text-xs shadow-md group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 border-2 border-[#0e1424] rounded-full"></span>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-slate-100 truncate group-hover:text-blue-300 transition-colors"><?= htmlspecialchars($authUser['full_name'] ?? 'Admin NOC Core') ?></p>
                    <span class="text-[9px] text-blue-400 font-semibold block truncate"><?= htmlspecialchars($authUser['role'] ?? 'Super Administrator') ?></span>
                </div>
            </a>
            <a href="<?= base_url('logout.php') ?>" class="text-slate-400 hover:text-rose-400 p-1.5 rounded-lg hover:bg-slate-800/60 transition" title="Log Out Pegawai">
                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
            </a>
        </div>
    </div>
</aside>
