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
<header class="h-20 bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 text-white shadow-xl flex items-center justify-between px-3.5 sm:px-8 z-20 shrink-0 border-b border-brand-900/40 gap-2 sm:gap-4 relative overflow-hidden select-none">
    <!-- Glowing Ambient Lights for RedDash Theme -->
    <div class="absolute top-0 right-1/4 w-32 h-16 bg-brand-600/15 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/3 w-32 h-16 bg-rose-600/10 rounded-full blur-2xl pointer-events-none"></div>

    <!-- Left: Mobile Toggle & Page Title with Subtitle -->
    <div class="flex items-center gap-2.5 sm:gap-3.5 relative z-10 flex-1 min-w-0 pr-1">
        <button onclick="toggleMobileSidebar()" class="md:hidden p-2 text-slate-300 hover:text-white rounded-xl border border-white/10 hover:bg-white/10 transition shrink-0">
            <i class="fa-solid fa-bars text-base sm:text-lg"></i>
        </button>
        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2 max-w-full">
                <h2 id="page-title" class="text-sm sm:text-lg font-extrabold text-white tracking-tight leading-tight truncate"><?= htmlspecialchars($curPageTitle) ?></h2>
                <?php if (isset($page_badge)): ?>
                    <span class="px-2 py-0.5 bg-brand-900/80 text-brand-200 rounded-full font-bold text-[9px] sm:text-[10px] border border-brand-700/60 shadow-xs shrink-0"><?= htmlspecialchars($page_badge) ?></span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-brand-300/80 font-medium mt-0.5 hidden sm:block truncate"><?= htmlspecialchars($pageSubtitle) ?></p>
        </div>
    </div>
    
    <!-- Right: Quick Action Controls & Status -->
    <div class="flex items-center justify-end gap-3 relative z-10">
        <!-- Status Badge Radius Server -->
        <div class="hidden sm:flex items-center gap-2 bg-emerald-950/70 text-emerald-300 px-3.5 py-1.5 rounded-full border border-emerald-500/30 text-xs font-mono font-semibold shadow-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
            <span>Radius: <strong class="text-emerald-200 font-bold">ONLINE</strong></span>
        </div>
        
        <!-- Quick Notification Bell with Counter -->
        <div class="relative">
            <button onclick="toggleNotificationSim()" class="p-2.5 text-slate-300 hover:text-white hover:bg-white/10 rounded-xl border border-white/10 transition shadow-xs" title="Notifikasi Sistem">
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-brand-500 rounded-full animate-ping"></span>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-brand-500 rounded-full shadow-[0_0_6px_rgba(239,68,68,0.8)]"></span>
                <i class="fa-regular fa-bell text-base"></i>
            </button>
        </div>

        <div class="h-6 w-px bg-white/15 mx-0.5 hidden sm:block"></div>

        <!-- Employee Profile & Logout Action -->
        <?php $curUser = auth_user(); ?>
        <div class="flex items-center pl-1 gap-2.5">
            <a href="<?= base_url('pengaturan/profile.php') ?>" class="flex items-center gap-2.5 text-slate-200 hover:text-brand-300 transition group" title="Detail Akun User">
                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-brand-600 to-rose-600 text-white font-bold text-xs flex items-center justify-center shadow-lg ring-2 ring-brand-500/40 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-user-shield text-xs"></i>
                </div>
                <div class="hidden lg:block text-left">
                    <p class="text-xs font-bold text-slate-100 leading-tight group-hover:text-brand-300 transition-colors"><?= htmlspecialchars($curUser['full_name'] ?? 'Admin Utama') ?></p>
                    <span class="text-[10px] text-brand-300/80 font-medium block leading-tight"><?= htmlspecialchars($curUser['role'] ?? 'Superadministrator') ?></span>
                </div>
            </a>
            <a href="<?= base_url('logout.php') ?>" class="p-2 text-slate-400 hover:text-rose-300 hover:bg-rose-950/50 rounded-xl border border-transparent hover:border-rose-900/40 transition" title="Logout Pegawai">
                <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
            </a>
        </div>
    </div>
</header>
