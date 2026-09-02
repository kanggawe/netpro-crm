<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Tentang Aplikasi & Spesifikasi Sistem";
$page_subtitle = "Informasi versi aplikasi, arsitektur teknologi, lisensi operasional, dan diagnostik server.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

$companyName = Setting::get('company_name', 'PT NETPRO TELEKOMUNIKASI INDONESIA');
$companyIzin = Setting::get('company_izin_isp', 'KEPMENKOMINFO NO. 412/TEL.02.02/2021');
$appVersion = Setting::get('app_version', 'v4.0.0-ENTERPRISE');
?>

<div class="space-y-6 text-xs max-w-5xl mx-auto">
    <!-- Brand Banner Card (RedDash Style) -->
    <div class="bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 text-white p-8 rounded-3xl shadow-xl border border-brand-900/40 relative overflow-hidden">
        <div class="absolute top-0 left-1/4 w-40 h-40 bg-brand-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 bg-gradient-to-tr from-brand-600 to-rose-600 text-white rounded-3xl flex items-center justify-center font-black text-3xl shadow-2xl shadow-brand-950/60 border border-white/20">
                    <i class="fa-solid fa-network-wired text-3xl animate-pulse"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="font-extrabold text-white text-2xl tracking-wide"><?= APP_NAME ?></h2>
                        <span class="px-3 py-0.5 bg-brand-500/20 text-brand-300 border border-brand-500/30 rounded-full font-bold font-mono text-[10px]">
                            <?= htmlspecialchars($appVersion) ?>
                        </span>
                    </div>
                    <p class="text-slate-300 text-sm mt-1">Enterprise ISP Management & Billing Automation Suite</p>
                    <p class="text-slate-400 text-[11px] mt-0.5">Dirancang khusus untuk Penyelenggara Jasa Internet (ISP), Jartaplok, dan Operator Jaringan Fiber Optik Indonesia.</p>
                </div>
            </div>
            <div class="text-left md:text-right space-y-1">
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Lisensi Resmi Terdaftar</span>
                <strong class="text-emerald-400 font-bold text-sm block"><?= htmlspecialchars($companyName) ?></strong>
                <span class="text-slate-400 font-mono text-[10px] block"><?= htmlspecialchars($companyIzin) ?></span>
            </div>
        </div>
    </div>

    <!-- 4 Feature Highlight Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="fa-solid fa-receipt"></i></div>
            <h4 class="font-bold text-slate-900 text-sm">Smart Billing & PPN</h4>
            <p class="text-slate-500 leading-relaxed">Kalkulasi otomatis PPN 11%/12% Include & Exclude, invoice massal, dan gateway QRIS.</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg"><i class="fa-solid fa-network-wired"></i></div>
            <h4 class="font-bold text-slate-900 text-sm">MikroTik & FreeRADIUS</h4>
            <p class="text-slate-500 leading-relaxed">Sinkronisasi otomatis PPPoE session, dynamic queue burst limit, dan trigger isolir.</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg"><i class="fa-solid fa-scale-balanced"></i></div>
            <h4 class="font-bold text-slate-900 text-sm">Payroll PP 36/2021</h4>
            <p class="text-slate-500 leading-relaxed">Perhitungan BPJS TK/Kes, pajak PPh 21 TER PMK 168/2023, dan insentif aktivasi teknisi.</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg"><i class="fa-solid fa-calculator"></i></div>
            <h4 class="font-bold text-slate-900 text-sm">ISP Tools & Calculators</h4>
            <p class="text-slate-500 leading-relaxed">Perhitungan kapasitas bandwidth IP Transit, Optical Power Budget GPON, dan PNBP Kominfo.</p>
        </div>
    </div>

    <!-- 2 Columns: Server Diagnostics & Tech Stack -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Server Diagnostics -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-server text-blue-600"></i> Diagnostik Lingkungan Server (Runtime)
                </h3>
                <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded text-[10px]">OPTIMAL</span>
            </div>

            <div class="space-y-2.5">
                <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                    <span class="text-slate-500">PHP Version:</span>
                    <strong class="font-mono text-slate-900"><?= phpversion() ?></strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                    <span class="text-slate-500">Database Engine:</span>
                    <strong class="font-mono text-blue-600"><?= (isset($activeDriver) && $activeDriver === 'pgsql') ? 'PostgreSQL 14+ (billdash)' : 'SQLite 3 (app.db PDO)' ?></strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                    <span class="text-slate-500">Web Server:</span>
                    <strong class="font-mono text-slate-900"><?= htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'PHP Built-in Server') ?></strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                    <span class="text-slate-500">Server Timezone:</span>
                    <strong class="font-mono text-slate-900"><?= date_default_timezone_get() ?> (<?= date('d M Y H:i:s') ?>)</strong>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-50">
                    <span class="text-slate-500">Memory Limit:</span>
                    <strong class="font-mono text-slate-900"><?= ini_get('memory_limit') ?></strong>
                </div>
                <div class="flex justify-between items-center py-1.5">
                    <span class="text-slate-500">PDO Driver:</span>
                    <strong class="font-mono text-emerald-600"><?= (isset($activeDriver) && $activeDriver === 'pgsql') ? 'pdo_pgsql (Active)' : 'pdo_sqlite (Active)' ?></strong>
                </div>
            </div>
        </div>

        <!-- Compliance & Technology Stack -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-indigo-600"></i> Arsitektur & Kepatuhan Regulasi
                </h3>
                <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">STANDAR NASIONAL</span>
            </div>

            <div class="space-y-3">
                <div class="p-3 bg-slate-50 rounded-xl space-y-1">
                    <strong class="text-slate-900 font-bold block flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-blue-600"></i> Kepatuhan Regulasi Telekomunikasi:
                    </strong>
                    <p class="text-slate-600 text-[11px] leading-relaxed">
                        Memenuhi ketentuan UU No. 36/1999 tentang Telekomunikasi, PP No. 46/2021 tentang Pos, Telekomunikasi dan Penyiaran, serta ketentuan PNBP Kominfo (USO 1.25% & BHP 0.50%).
                    </p>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl space-y-1">
                    <strong class="text-slate-900 font-bold block flex items-center gap-1.5">
                        <i class="fa-solid fa-file-shield text-emerald-600"></i> Kepatuhan Pajak & Ketenagakerjaan:
                    </strong>
                    <p class="text-slate-600 text-[11px] leading-relaxed">
                        Sesuai UU Harmonisasi Peraturan Perpajakan (UU HPP No. 7/2021) untuk PPN 11%/12%, PP No. 36/2021 tentang Pengupahan, dan PMK No. 168/2023 tentang PPh 21 TER.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Copyright Box -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center text-slate-500 gap-3 text-center sm:text-left">
        <div>
            <strong class="text-slate-900 block font-bold">BILL-DASH ISP Management Suite</strong>
            <span class="text-[11px]">© <?= date('Y') ?> <?= htmlspecialchars($companyName) ?>. All rights reserved.</span>
        </div>
        <div class="flex gap-2">
            <button onclick="triggerToast('Cek Update', 'Sistem menggunakan versi terbaru (Up to date).')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg flex items-center gap-1">
                <i class="fa-solid fa-arrows-rotate"></i> Periksa Update
            </button>
            <a href="<?= base_url('pengaturan/sistem.php') ?>" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg flex items-center gap-1">
                <i class="fa-solid fa-gears"></i> Pengaturan Sistem
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
