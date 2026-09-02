<?php
/**
 * Common Header Template
 */
if (!defined('APP_NAME')) {
    require_once __DIR__ . '/../config/app.php';
}

// Proteksi Auth Guard: Pengguna wajib login terlebih dahulu
require_login();

$pageTitle = isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME . ' - ' . APP_DESC;
$pageSubtitle = isset($page_subtitle) ? $page_subtitle : 'Sistem Operasi Manajemen ISP & Billing.';
$activeMenu = isset($active_menu) ? $active_menu : 'm-dashboard';
$currentFile = basename($_SERVER['PHP_SELF']);
$appFavicon = Setting::get('app_favicon_url', '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <?php if (!empty($appFavicon)): ?>
    <!-- Custom Brand Favicon -->
    <link rel="icon" href="<?= htmlspecialchars($appFavicon) ?>">
    <?php endif; ?>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            200: '#fecaca',
                            300: '#fca5a5',
                            400: '#f87171',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                            950: '#450a0a',
                        }
                    },
                    width: {
                        '76': '19rem',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    boxShadow: {
                        'soft': '0 20px 40px -15px rgba(0,0,0,0.05)',
                    }
                }
            }
        }
    </script>
    
    <!-- Google Fonts Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet CSS & JS CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Chart.js 4.x CDN for Rich Interactive Dashboards -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom Style -->
    <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
</head>
<body class="bg-slate-50 text-slate-800 antialiased overflow-hidden">
    <div class="flex h-screen w-screen overflow-hidden">
        
        <!-- Sidebar PHP Modular Include -->
        <?php require_once __DIR__ . '/sidebar.php'; ?>

        <!-- Main Content Area Wrapper -->
        <main class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50">
            
            <!-- Navbar Header Include -->
            <?php require_once __DIR__ . '/navbar.php'; ?>

            <!-- Dynamic Page Content Viewport -->
            <div id="content-viewport" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 sm:space-y-6">
                
                <?php if (!in_array($currentFile, ['invoice.php', 'kwitansi.php', 'cetak_invoice.php'])): ?>
                <!-- Dynamic Chevron / Arrow Breadcrumb Stepper Navigation (Touch Scrollable on Mobile) -->
                <div class="arrow-breadcrumb-wrapper w-full select-none">
                    <nav class="arrow-breadcrumb" aria-label="Breadcrumb">
                        <a href="<?= base_url('dashboard/utama.php') ?>" class="arrow-breadcrumb-item is-completed">
                            <span class="arrow-breadcrumb-badge"><i class="fa-solid fa-house text-[9px]"></i></span>
                            <span class="hidden sm:inline">Home</span>
                        </a>

                        <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                            <?php 
                            $totalB = count($breadcrumbs);
                            $idxB = 0;
                            foreach ($breadcrumbs as $bLabel => $bUrl): 
                                $idxB++;
                                $isLast = ($idxB === $totalB);
                            ?>
                                <?php if (!empty($bUrl) && !$isLast): ?>
                                    <a href="<?= base_url($bUrl) ?>" class="arrow-breadcrumb-item is-completed">
                                        <span class="arrow-breadcrumb-badge"><i class="fa-solid fa-check text-[9px]"></i></span>
                                        <span class="truncate max-w-[100px] sm:max-w-xs"><?= htmlspecialchars($bLabel) ?></span>
                                    </a>
                                <?php else: ?>
                                    <span class="arrow-breadcrumb-item is-active">
                                        <span class="arrow-breadcrumb-badge"><i class="fa-solid fa-check text-[9px]"></i></span>
                                        <span class="truncate max-w-[130px] sm:max-w-xs"><?= htmlspecialchars($bLabel) ?></span>
                                    </span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php if ($currentFile !== 'utama.php'): ?>
                                <a href="<?= base_url($currentModule['url']) ?>" class="arrow-breadcrumb-item is-completed">
                                    <span class="arrow-breadcrumb-badge"><i class="fa-solid <?= $currentModule['icon'] ?> text-[9px]"></i></span>
                                    <span class="truncate max-w-[100px] sm:max-w-xs"><?= $currentModule['title'] ?></span>
                                </a>
                                <span class="arrow-breadcrumb-item is-active">
                                    <span class="arrow-breadcrumb-badge"><i class="fa-solid fa-check text-[9px]"></i></span>
                                    <span class="truncate max-w-[130px] sm:max-w-xs"><?= htmlspecialchars($curPageTitle) ?></span>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </nav>
                </div>
                <?php endif; ?>

                <?php if (!in_array($currentFile, ['invoice.php', 'kwitansi.php', 'cetak_invoice.php'])): ?>
                <!-- Official Enterprise Print Letterhead (Hanya muncul saat dicetak/print) -->
                <div class="global-print-header print-only hidden mb-6 pb-4 border-b-2 border-slate-900">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-xl font-extrabold text-slate-900 uppercase tracking-wide">PT NETPRO TELEKOMUNIKASI INDONESIA</h1>
                            <p class="text-xs text-slate-700 font-medium">Izin Penyelenggara Jasa Internet (ISP) Kominfo No: 0220108921882 | KBLI 61100</p>
                            <p class="text-[11px] text-slate-500">Kantor Pusat: Cyber 2 Tower Lt. 18, Jl. H.R. Rasuna Said, Jakarta Selatan 12950 | Telp: (021) 5088-9900</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-slate-900 block uppercase"><?= htmlspecialchars($page_title ?? 'DOKUMEN LAPORAN RESMI') ?></span>
                            <span class="text-[10px] text-slate-500 font-mono block">Dicetak: <?= date('d/m/Y H:i') ?> WIB</span>
                            <span class="text-[10px] text-slate-500 font-mono block">Operator: <?= htmlspecialchars(auth_user()['full_name'] ?? 'Admin') ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php
                // Enforce RBAC Permission Guard: Check if the logged-in user can access this module
                if (!can_access($activeMenu)) {
                ?>
                <div class="max-w-xl mx-auto my-12 bg-white p-8 rounded-3xl border border-rose-200 shadow-xl text-center space-y-4">
                    <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-3xl mx-auto shadow-sm">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="space-y-1">
                        <span class="px-3 py-1 bg-rose-100 text-rose-800 font-black rounded-full text-[10px] uppercase tracking-wider">403 ACCESS FORBIDDEN</span>
                        <h2 class="text-lg font-extrabold text-slate-900">Akses Modul Terbatas (RBAC)</h2>
                        <p class="text-xs text-slate-500">Akun Anda dengan role <strong><?= htmlspecialchars(auth_user()['role'] ?? 'User') ?></strong> tidak memiliki otorisasi untuk mengakses modul <strong><?= htmlspecialchars($activeMenu) ?></strong>.</p>
                    </div>
                    <div class="pt-2 flex justify-center gap-3">
                        <a href="<?= base_url('dashboard/utama.php') ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5">
                            <i class="fa-solid fa-house"></i> Kembali ke Dashboard
                        </a>
                        <a href="<?= base_url('logout.php') ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition">
                            Ganti Akun Lain
                        </a>
                    </div>
                </div>
                <?php
                    require_once __DIR__ . '/footer.php';
                    exit;
                }
                ?>
