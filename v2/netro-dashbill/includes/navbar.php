<?php
/**
 * Top Navbar Header Component with Dynamic Enterprise Breadcrumbs
 */
$moduleMap = [
    'dashboard' => ['title' => 'Dashboard', 'url' => 'dashboard/utama.php', 'icon' => 'fa-chart-pie'],
    'crm' => ['title' => 'CRM & Pelanggan', 'url' => 'crm/daftar.php', 'icon' => 'fa-users-gear'],
    'billing' => ['title' => 'Billing & Tagihan', 'url' => 'billing/daftar.php', 'icon' => 'fa-credit-card'],
    'radius' => ['title' => 'RADIUS Engine', 'url' => 'radius/users.php', 'icon' => 'fa-network-wired'],
    'finance' => ['title' => 'Keuangan & Akuntansi', 'url' => 'finance/kas.php', 'icon' => 'fa-wallet'],
    'noc' => ['title' => 'NOC & Network', 'url' => 'noc/monitoring.php', 'icon' => 'fa-microchip'],
    'tickets' => ['title' => 'Ticketing & CSAT', 'url' => 'tickets/list.php', 'icon' => 'fa-headset'],
    'hr' => ['title' => 'HR & Staf', 'url' => 'hr/karyawan.php', 'icon' => 'fa-user-tie'],
    'payroll' => ['title' => 'Payroll & Gaji', 'url' => 'payroll/master.php', 'icon' => 'fa-money-bill-wave'],
    'inventory' => ['title' => 'Inventory & Aset', 'url' => 'inventory/barang.php', 'icon' => 'fa-boxes-stacked'],
    'marketing' => ['title' => 'Marketing & Leads', 'url' => 'marketing/leads.php', 'icon' => 'fa-bullhorn'],
    'kalkulator' => ['title' => 'Kalkulator Tools', 'url' => 'kalkulator/bandwidth.php', 'icon' => 'fa-calculator'],
    'kinerja' => ['title' => 'Penilaian Kinerja', 'url' => 'kinerja/kpi.php', 'icon' => 'fa-award'],
    'laporan' => ['title' => 'Laporan Eksekutif', 'url' => 'laporan/summary.php', 'icon' => 'fa-file-lines'],
    'pengaturan' => ['title' => 'Pengaturan Sistem', 'url' => 'pengaturan/sistem.php', 'icon' => 'fa-gear']
];

$scriptPath = trim($_SERVER['PHP_SELF'] ?? '', '/');
$pathParts = explode('/', $scriptPath);
$currentDir = $pathParts[0] ?? 'dashboard';
// If in root (e.g. login, profile, index)
if (count($pathParts) <= 1 || !isset($moduleMap[$currentDir])) {
    $currentDir = 'dashboard';
}

$currentModule = $moduleMap[$currentDir] ?? $moduleMap['dashboard'];
$curPageTitle = $page_title ?? 'Dashboard';
?>
<header class="h-20 bg-gradient-to-r from-brand-800 via-brand-700 to-brand-900 text-white shadow-xl flex items-center justify-between px-3.5 sm:px-8 z-30 shrink-0 border-b border-brand-600/40 gap-2 sm:gap-4 relative select-none">
    <!-- Glowing Ambient Lights for Red Theme (Contained in dedicated overflow-hidden layer) -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-1/4 w-32 h-16 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-1/3 w-32 h-16 bg-rose-400/20 rounded-full blur-2xl"></div>
    </div>
    
    <!-- Left: Mobile Toggle & Page Title with Subtitle -->
    <div class="flex items-center gap-2.5 sm:gap-3.5 relative z-10 flex-1 min-w-0 pr-1">
        <button onclick="toggleMobileSidebar()" class="md:hidden p-2 text-red-100 hover:text-white rounded-xl border border-white/20 hover:bg-white/10 transition shrink-0">
            <i class="fa-solid fa-bars text-base sm:text-lg"></i>
        </button>
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 max-w-full">
                <h2 id="page-title" class="text-sm sm:text-lg font-extrabold text-white tracking-tight leading-tight truncate"><?= htmlspecialchars($curPageTitle) ?></h2>
                <?php if (isset($page_badge)): ?>
                    <span class="px-2 py-0.5 bg-white/20 text-white rounded-full font-bold text-[9px] sm:text-[10px] border border-white/30 shadow-xs shrink-0"><?= htmlspecialchars($page_badge) ?></span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-red-100/90 font-medium mt-0.5 hidden sm:block truncate"><?= htmlspecialchars($pageSubtitle) ?></p>
        </div>
    </div>
    
    <!-- Right: Quick Action Controls & Status -->
    <div class="flex items-center justify-end gap-3 relative z-10">
        <!-- Status Badge Radius Server -->
        <div class="hidden sm:flex items-center gap-2 bg-black/30 text-emerald-300 px-3.5 py-1.5 rounded-full border border-emerald-400/30 text-xs font-mono font-semibold shadow-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
            <span>Radius: <strong class="text-emerald-200 font-bold">ONLINE</strong></span>
        </div>
        
        <?php
        // Dynamic Notification Aggregation
        $unpaidInvCount = 0;
        try {
            foreach (Invoice::all() as $inv) {
                $st = strtolower($inv['status'] ?? 'unpaid');
                if ($st !== 'lunas' && $st !== 'paid') {
                    $unpaidInvCount++;
                }
            }
        } catch (Throwable $e) {}

        $openTicketCount = 0;
        try {
            foreach (Ticket::all() as $tkt) {
                $tst = strtolower($tkt['status'] ?? 'open');
                if ($tst !== 'closed' && $tst !== 'selesai') {
                    $openTicketCount++;
                }
            }
        } catch (Throwable $e) {}
        ?>

        <!-- Quick Notification Bell with Dropdown Menu -->
        <div class="relative" id="notificationMenuContainer">
            <button id="notifBellBtn" onclick="toggleNotificationDropdown(event)" class="p-2.5 text-red-100 hover:text-white hover:bg-white/15 rounded-xl border border-white/20 transition shadow-xs relative" title="Pusat Notifikasi Sistem">
                <span id="notif-ping-dot" class="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-400 rounded-full animate-ping"></span>
                <span id="notif-static-dot" class="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-400 rounded-full shadow-[0_0_6px_rgba(251,191,36,0.9)]"></span>
                <i class="fa-regular fa-bell text-base"></i>
            </button>

            <!-- Dropdown Notification Center -->
            <div id="notificationDropdown" class="hidden absolute top-full right-0 mt-2.5 w-80 sm:w-96 bg-white text-slate-800 rounded-3xl shadow-2xl border border-slate-100 z-50 overflow-hidden transform origin-top-right transition-all ring-1 ring-black/5">
                <!-- Dropdown Header -->
                <div class="p-4 bg-gradient-to-r from-brand-900 to-brand-800 text-white flex items-center justify-between border-b border-brand-700">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-xs shadow-inner">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-sm leading-tight">Pusat Notifikasi</h4>
                            <p class="text-[10px] text-red-200">Alert & Aktivitas Sistem Real-Time</p>
                        </div>
                    </div>
                    <button onclick="markAllNotificationsRead(event)" class="text-[10px] bg-white/15 hover:bg-white/25 px-2.5 py-1 rounded-lg font-bold transition flex items-center gap-1 text-red-100">
                        <i class="fa-solid fa-check-double text-[9px]"></i> Tandai Dibaca
                    </button>
                </div>

                <!-- Notification Items List -->
                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 text-xs">
                    <!-- Item 1: RADIUS Heartbeat -->
                    <a href="<?= base_url('radius/users.php') ?>" class="p-3.5 flex items-start gap-3 hover:bg-slate-50 transition block group">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-network-wired text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <strong class="text-slate-900 font-bold truncate text-[11px]">FreeRADIUS & MikroTik CoA</strong>
                                <span class="text-[9px] text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded">ONLINE</span>
                            </div>
                            <p class="text-slate-500 text-[11px] mt-0.5 leading-snug">Layanan autentikasi PPPoE aktif & sinkronisasi Dynamic CoA port UDP 3799 normal.</p>
                            <span class="text-[9px] text-slate-400 mt-1 block font-mono"><i class="fa-regular fa-clock mr-1"></i> Baru saja</span>
                        </div>
                    </a>

                    <!-- Item 2: Billing & Isolir Alert -->
                    <a href="<?= base_url('billing/daftar.php') ?>" class="p-3.5 flex items-start gap-3 hover:bg-slate-50 transition block group">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <strong class="text-slate-900 font-bold truncate text-[11px]">Penagihan & Invoice Jatuh Tempo</strong>
                                <span class="text-[9px] text-rose-600 font-bold bg-rose-50 px-1.5 py-0.5 rounded"><?= $unpaidInvCount ?> Unpaid</span>
                            </div>
                            <p class="text-slate-500 text-[11px] mt-0.5 leading-snug"><?= $unpaidInvCount > 0 ? "Terdapat $unpaidInvCount invoice pelanggan menunggu pembayaran & auto-isolir." : "Seluruh invoice tagihan pelanggan dalam status lunas." ?></p>
                            <span class="text-[9px] text-slate-400 mt-1 block font-mono"><i class="fa-regular fa-clock mr-1"></i> Hari ini</span>
                        </div>
                    </a>

                    <!-- Item 3: CSAT / Tiket SLA -->
                    <a href="<?= base_url('tickets/list.php') ?>" class="p-3.5 flex items-start gap-3 hover:bg-slate-50 transition block group">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-headset text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <strong class="text-slate-900 font-bold truncate text-[11px]">Tiket Gangguan Pelanggan</strong>
                                <span class="text-[9px] text-blue-600 font-bold bg-blue-50 px-1.5 py-0.5 rounded"><?= $openTicketCount ?> Tiket</span>
                            </div>
                            <p class="text-slate-500 text-[11px] mt-0.5 leading-snug"><?= $openTicketCount > 0 ? "Ada $openTicketCount tiket eskalasi teknisi yang sedang ditangani." : "Tidak ada antrean gangguan aktif. SLA 99.9% optimal." ?></p>
                            <span class="text-[9px] text-slate-400 mt-1 block font-mono"><i class="fa-regular fa-clock mr-1"></i> Real-time</span>
                        </div>
                    </a>

                    <!-- Item 4: OLT GPON & Fiber Telemetry -->
                    <a href="<?= base_url('noc/monitoring.php') ?>" class="p-3.5 flex items-start gap-3 hover:bg-slate-50 transition block group">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-tower-broadcast text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <strong class="text-slate-900 font-bold truncate text-[11px]">OLT GPON & Fiber Optical</strong>
                                <span class="text-[9px] text-amber-600 font-bold bg-amber-50 px-1.5 py-0.5 rounded">NORMAL</span>
                            </div>
                            <p class="text-slate-500 text-[11px] mt-0.5 leading-snug">Distribusi 24 Core 2 Tube, ODC Cinde, dan OTB 96 Port terhubung normal.</p>
                            <span class="text-[9px] text-slate-400 mt-1 block font-mono"><i class="fa-regular fa-clock mr-1"></i> Terpantau</span>
                        </div>
                    </a>
                </div>

                <!-- Dropdown Footer -->
                <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-slate-500">
                    <span class="text-[10px] font-medium">Sistem Monitoring Terpadu</span>
                    <a href="<?= base_url('pengaturan/logs.php') ?>" class="text-[11px] font-bold text-brand-600 hover:text-brand-700 transition flex items-center gap-1">
                        Lihat Log Audit <i class="fa-solid fa-arrow-right text-[9px]"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="h-6 w-px bg-white/20 mx-0.5 hidden sm:block"></div>

        <!-- Employee Profile & Logout Action -->
        <?php $curUser = auth_user(); ?>
        <div class="flex items-center pl-1 gap-2.5">
            <a href="<?= base_url('pengaturan/profile.php') ?>" class="flex items-center gap-2.5 text-white hover:text-red-100 transition group" title="Detail Akun User">
                <div class="w-9 h-9 rounded-full ring-2 ring-white/40 shadow-lg group-hover:scale-105 transition-transform overflow-hidden shrink-0 bg-brand-950 flex items-center justify-center">
                    <img src="<?= base_url($curUser['avatar'] ?? 'assets/images/avatar-admin.svg') ?>" alt="<?= htmlspecialchars($curUser['full_name'] ?? 'User') ?>" class="w-full h-full object-cover" onerror="this.outerHTML='<div class=\'w-full h-full flex items-center justify-center bg-brand-950 text-white font-bold text-xs\'><i class=\'fa-solid fa-user-shield\'></i></div>'">
                </div>
                <div class="hidden lg:block text-left">
                    <p class="text-xs font-bold text-white leading-tight group-hover:text-red-100 transition-colors"><?= htmlspecialchars($curUser['full_name'] ?? 'Admin Utama') ?></p>
                    <span class="text-[10px] text-red-200 font-medium block leading-tight"><?= htmlspecialchars($curUser['role'] ?? 'Superadministrator') ?></span>
                </div>
            </a>
            <a href="<?= base_url('logout.php') ?>" class="p-2 text-red-200 hover:text-white hover:bg-white/15 rounded-xl border border-transparent hover:border-white/20 transition" title="Logout Pegawai">
                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
            </a>
        </div>
    </div>
</header>
