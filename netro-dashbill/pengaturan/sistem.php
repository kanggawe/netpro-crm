<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Konfigurasi Sistem & Server Engine";
$page_subtitle = "Pengaturan lingkungan aplikasi, server SMTP email, zona waktu, keamanan sesi, dan branding antarmuka.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

// App Environment
$appName = Setting::get('app_name', 'NETPRO ISP MANAGEMENT SUITE');
$appDesc = Setting::get('app_desc', 'Enterprise Fiber & Billing Suite');
$appVersion = Setting::get('app_version', 'v3.2.0-STABLE');
$appEnv = Setting::get('app_env', 'production');
$appMaintenance = Setting::get('app_maintenance', '0');

// Localization
$appTimezone = Setting::get('app_timezone', 'Asia/Jakarta');
$appCurrency = Setting::get('app_currency', 'IDR');
$appDateFormat = Setting::get('app_date_format', 'd M Y H:i');

// SMTP Mail Server
$smtpHost = Setting::get('smtp_host', 'mail.netpro.co.id');
$smtpPort = Setting::get('smtp_port', '587');
$smtpUser = Setting::get('smtp_user', 'no-reply@netpro.co.id');
$smtpPass = Setting::get('smtp_pass', 'SmtpNetProSec#991');
$smtpCrypto = Setting::get('smtp_crypto', 'tls');
$smtpSenderName = Setting::get('smtp_sender_name', 'NETPRO Notification Engine');

// Security & Session
$sessionLifetime = Setting::get('session_lifetime', '120'); // minutes
$maxLoginAttempts = Setting::get('max_login_attempts', '5');
$ipWhitelistOnly = Setting::get('ip_whitelist_only', '0');

// Branding & Visual Assets
$appLogoUrl = Setting::get('app_logo_url', '');
$appFaviconUrl = Setting::get('app_favicon_url', '');
$appInvoiceLogoUrl = Setting::get('app_invoice_logo_url', '');
$appLoginLogoUrl = Setting::get('app_login_logo_url', '');
$appBrandColor = Setting::get('app_brand_color', '#2563eb');
$appCopyrightText = Setting::get('app_copyright_text', '© ' . date('Y') . ' PT NETPRO TELEKOMUNIKASI INDONESIA. All rights reserved.');

$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'saved_system'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Konfigurasi server engine, SMTP mailer, dan parameter sistem berhasil diperbarui!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-5xl mx-auto">
    <!-- Top 4 System Health Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Versi Platform</span>
                <strong class="text-lg font-bold text-slate-900"><?= htmlspecialchars($appVersion) ?></strong>
                <span class="text-emerald-600 font-bold block mt-0.5">● Engine Running</span>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-code-commit"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Zona Waktu Server</span>
                <strong class="text-lg font-bold text-blue-600">WIB (UTC+7)</strong>
                <span class="text-slate-400 block mt-0.5">Asia/Jakarta NTP Sync</span>
            </div>
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-clock"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">SMTP Mail Dispatcher</span>
                <strong class="text-lg font-bold text-emerald-600">Port 587 TLS</strong>
                <span class="text-emerald-600 font-medium block mt-0.5">Connected (mail.netpro)</span>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-envelope-circle-check"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Mode Operasi</span>
                <strong class="text-lg font-bold text-purple-600"><?= strtoupper($appEnv) ?></strong>
                <span class="text-slate-400 block mt-0.5">PHP <?= phpversion() ?></span>
            </div>
            <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-server"></i></div>
        </div>
    </div>

    <!-- Main System Configuration Form -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-gears text-blue-600"></i> Pengaturan Teknis Server & Parameter Aplikasi
                </h3>
                <p class="text-slate-400">Konfigurasi mendasar untuk lingkungan server, notifikasi email otomatis, dan kebijakan keamanan.</p>
            </div>
            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">ALL DAEMONS ACTIVE ✓</span>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="save_settings">
            <input type="hidden" name="redirect" value="pengaturan/sistem.php?msg=saved_system">

            <!-- 1. Parameter Aplikasi & Lingkungan -->
            <div class="space-y-3">
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider text-blue-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                    <i class="fa-solid fa-laptop-code"></i> 1. Parameter Aplikasi & Lingkungan Operasional
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Judul / Nama Platform Sistem</label>
                        <input type="text" name="app_name" value="<?= htmlspecialchars($appName) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Sub-judul / Tagline Singkat</label>
                        <input type="text" name="app_desc" value="<?= htmlspecialchars($appDesc) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Environment Mode</label>
                        <select name="app_env" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="production" <?= $appEnv === 'production' ? 'selected' : '' ?>>Production (Live System)</option>
                            <option value="staging" <?= $appEnv === 'staging' ? 'selected' : '' ?>>Staging / Quality Assurance</option>
                            <option value="development" <?= $appEnv === 'development' ? 'selected' : '' ?>>Development (Debug Active)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Status Maintenance Mode</label>
                        <select name="app_maintenance" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="0" <?= $appMaintenance === '0' ? 'selected' : '' ?>>NONAKTIF (Sistem Beroperasi Normal)</option>
                            <option value="1" <?= $appMaintenance === '1' ? 'selected' : '' ?>>AKTIF (Halaman Pemeliharaan Server)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Build Version String</label>
                        <input type="text" name="app_version" value="<?= htmlspecialchars($appVersion) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-purple-600">
                    </div>
                </div>
            </div>

            <!-- 2. Lokalisasi & Waktu -->
            <div class="space-y-3 pt-2">
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider text-indigo-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                    <i class="fa-solid fa-globe"></i> 2. Lokalisasi, Zona Waktu & Format Angka
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Server Timezone</label>
                        <select name="app_timezone" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="Asia/Jakarta" <?= $appTimezone === 'Asia/Jakarta' ? 'selected' : '' ?>>Asia/Jakarta (WIB - UTC+7)</option>
                            <option value="Asia/Makassar" <?= $appTimezone === 'Asia/Makassar' ? 'selected' : '' ?>>Asia/Makassar (WITA - UTC+8)</option>
                            <option value="Asia/Jayapura" <?= $appTimezone === 'Asia/Jayapura' ? 'selected' : '' ?>>Asia/Jayapura (WIT - UTC+9)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Simbol Mata Uang Utama</label>
                        <input type="text" name="app_currency" value="<?= htmlspecialchars($appCurrency) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-emerald-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Format Tanggal Tampilan</label>
                        <input type="text" name="app_date_format" value="<?= htmlspecialchars($appDateFormat) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-700">
                    </div>
                </div>
            </div>

            <!-- 3. Server SMTP Email Notifikasi -->
            <div class="space-y-3 pt-2">
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider text-emerald-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                    <i class="fa-solid fa-envelope-open-text"></i> 3. Konfigurasi SMTP Mail Server (Pengiriman Invoice PDF Otomatis)
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">SMTP Host / Server</label>
                        <input type="text" name="smtp_host" value="<?= htmlspecialchars($smtpHost) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">SMTP Port</label>
                        <input type="number" name="smtp_port" value="<?= htmlspecialchars($smtpPort) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Protokol Enkripsi</label>
                        <select name="smtp_crypto" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="tls" <?= $smtpCrypto === 'tls' ? 'selected' : '' ?>>TLS (Recommended - Port 587)</option>
                            <option value="ssl" <?= $smtpCrypto === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
                            <option value="none" <?= $smtpCrypto === 'none' ? 'selected' : '' ?>>None (Plain Text)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">SMTP Username (Email Pengirim)</label>
                        <input type="text" name="smtp_user" value="<?= htmlspecialchars($smtpUser) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">SMTP Password</label>
                        <input type="password" name="smtp_pass" value="<?= htmlspecialchars($smtpPass) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-emerald-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Tampilan Pengirim (Sender Name)</label>
                        <input type="text" name="smtp_sender_name" value="<?= htmlspecialchars($smtpSenderName) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                    </div>
                </div>
            </div>

            <!-- 4. Keamanan Sesi & Pembatasan Login -->
            <div class="space-y-3 pt-2">
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider text-purple-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                    <i class="fa-solid fa-shield-halved"></i> 4. Kebijakan Keamanan Sesi & Pembatasan Akses
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Durasi Sesi Login (Menit)</label>
                        <input type="number" name="session_lifetime" value="<?= htmlspecialchars($sessionLifetime) ?>" min="15" max="1440" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Batas Percobaan Login Salah</label>
                        <input type="number" name="max_login_attempts" value="<?= htmlspecialchars($maxLoginAttempts) ?>" min="3" max="10" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-rose-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Pembatasan IP Whitelist Admin</label>
                        <select name="ip_whitelist_only" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                            <option value="0" <?= $ipWhitelistOnly === '0' ? 'selected' : '' ?>>NONAKTIF (Akses dari Mana Saja)</option>
                            <option value="1" <?= $ipWhitelistOnly === '1' ? 'selected' : '' ?>>AKTIF (Hanya IP Kantor Terdaftar)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 5. Identitas Visual, Logo & Favicon Antarmuka -->
            <div class="space-y-3 pt-2">
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider text-pink-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                    <i class="fa-solid fa-paintbrush"></i> 5. Identitas Visual, Logo & Favicon Antarmuka (Branding Suite)
                </h4>
                <p class="text-slate-400 text-[11px]">Masukkan link/URL logo (PNG, SVG, ICO) atau path aset lokal. Biarkan kosong untuk menggunakan ikon default ISP.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1 flex items-center justify-between">
                            <span>URL Logo Utama (Sidebar & Header)</span>
                            <span class="text-[10px] text-slate-400 font-mono">PNG / SVG Transparan</span>
                        </label>
                        <input type="text" id="inputAppLogo" name="app_logo_url" value="<?= htmlspecialchars($appLogoUrl) ?>" placeholder="https://example.com/logo.png atau assets/img/logo.png" oninput="updateBrandingPreview()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-800">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1 flex items-center justify-between">
                            <span>URL Favicon Browser (Tab Icon)</span>
                            <span class="text-[10px] text-slate-400 font-mono">.ICO / .PNG / .SVG (32x32)</span>
                        </label>
                        <input type="text" id="inputAppFavicon" name="app_favicon_url" value="<?= htmlspecialchars($appFaviconUrl) ?>" placeholder="https://example.com/favicon.ico atau assets/img/favicon.ico" oninput="updateBrandingPreview()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1 flex items-center justify-between">
                            <span>URL Logo Faktur / Invoice PDF</span>
                            <span class="text-[10px] text-slate-400 font-mono">Kop Surat & Cetak Dokumen</span>
                        </label>
                        <input type="text" id="inputInvoiceLogo" name="app_invoice_logo_url" value="<?= htmlspecialchars($appInvoiceLogoUrl) ?>" placeholder="https://example.com/logo-invoice.png" oninput="updateBrandingPreview()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-800">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1 flex items-center justify-between">
                            <span>URL Logo Portal Login Pegawai</span>
                            <span class="text-[10px] text-slate-400 font-mono">Halaman Login ESS & Staff</span>
                        </label>
                        <input type="text" id="inputLoginLogo" name="app_login_logo_url" value="<?= htmlspecialchars($appLoginLogoUrl) ?>" placeholder="https://example.com/logo-white.png" oninput="updateBrandingPreview()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Aksen Warna Brand</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="app_brand_color" id="inputBrandColor" value="<?= htmlspecialchars($appBrandColor) ?>" oninput="document.getElementById('brandColorHex').value = this.value" class="w-10 h-10 rounded-lg border border-slate-200 p-1 cursor-pointer bg-white">
                            <input type="text" id="brandColorHex" value="<?= htmlspecialchars($appBrandColor) ?>" oninput="document.getElementById('inputBrandColor').value = this.value" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold text-slate-700">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="font-semibold text-slate-700 block mb-1">Teks Hak Cipta / Footer Resmi</label>
                        <input type="text" name="app_copyright_text" value="<?= htmlspecialchars($appCopyrightText) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-slate-700">
                    </div>
                </div>

                <!-- Real-time Live Branding Preview Card -->
                <div class="p-4 bg-slate-900 text-white rounded-2xl border border-slate-800 space-y-3 mt-3">
                    <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                        <span class="text-xs font-bold text-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-eye text-blue-400"></i> Pratinjau Langsung (Live Asset Preview)
                        </span>
                        <span class="text-[10px] text-slate-400 font-mono">Real-time Simulation</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <!-- Preview 1: Browser Tab (Favicon) -->
                        <div class="p-3 bg-[#0e1424] border border-slate-800 rounded-xl space-y-1.5">
                            <span class="text-[10px] text-slate-400 font-semibold block uppercase">1. Tab Browser Favicon</span>
                            <div class="flex items-center gap-2 bg-[#1a2234] p-2 rounded-lg border border-slate-700">
                                <div id="previewFaviconBox" class="w-4 h-4 flex items-center justify-center shrink-0">
                                    <?php if ($appFaviconUrl): ?>
                                        <img src="<?= htmlspecialchars($appFaviconUrl) ?>" class="w-4 h-4 object-contain">
                                    <?php else: ?>
                                        <i class="fa-solid fa-tower-cell text-blue-400 text-xs"></i>
                                    <?php endif; ?>
                                </div>
                                <span class="font-bold text-slate-200 text-[11px] truncate"><?= htmlspecialchars($appName) ?></span>
                            </div>
                        </div>

                        <!-- Preview 2: Sidebar Header Logo -->
                        <div class="p-3 bg-[#0e1424] border border-slate-800 rounded-xl space-y-1.5">
                            <span class="text-[10px] text-slate-400 font-semibold block uppercase">2. Sidebar Header Logo</span>
                            <div class="flex items-center gap-2.5 bg-[#060911] p-2 rounded-lg border border-slate-800">
                                <div id="previewLogoBox" class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center font-bold text-white shadow text-xs overflow-hidden shrink-0">
                                    <?php if ($appLogoUrl): ?>
                                        <img src="<?= htmlspecialchars($appLogoUrl) ?>" class="w-full h-full object-contain p-0.5">
                                    <?php else: ?>
                                        <i class="fa-solid fa-tower-cell"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="overflow-hidden">
                                    <strong class="font-extrabold text-white text-[10px] block truncate"><?= htmlspecialchars($appName) ?></strong>
                                    <span class="text-[8px] text-slate-400 font-semibold block truncate uppercase"><?= htmlspecialchars($appDesc) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Preview 3: Invoice Print Letterhead -->
                        <div class="p-3 bg-white text-slate-900 border border-slate-200 rounded-xl space-y-1.5">
                            <span class="text-[10px] text-slate-500 font-semibold block uppercase">3. Kop Cetak Invoice PDF</span>
                            <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-lg border border-slate-200">
                                <div id="previewInvoiceLogoBox" class="w-6 h-6 flex items-center justify-center shrink-0 text-blue-600 font-bold text-xs">
                                    <?php if ($appInvoiceLogoUrl || $appLogoUrl): ?>
                                        <img src="<?= htmlspecialchars($appInvoiceLogoUrl ?: $appLogoUrl) ?>" class="w-6 h-6 object-contain">
                                    <?php else: ?>
                                        <i class="fa-solid fa-file-invoice-dollar text-sm text-blue-600"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="overflow-hidden leading-tight">
                                    <strong class="font-bold text-[10px] block truncate text-slate-900">PT NETPRO TELEKOMUNIKASI</strong>
                                    <span class="text-[8px] text-slate-500 block truncate">Official ISP Letterhead</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Konfigurasi Sistem, Logo & Server Engine
            </button>
        </form>
    </div>
</div>

<script>
function updateBrandingPreview() {
    const logoUrl = document.getElementById('inputAppLogo').value.trim();
    const faviconUrl = document.getElementById('inputAppFavicon').value.trim();
    const invoiceLogoUrl = document.getElementById('inputInvoiceLogo').value.trim();

    // Update Favicon Preview
    const favBox = document.getElementById('previewFaviconBox');
    if (faviconUrl) {
        favBox.innerHTML = `<img src="${faviconUrl}" class="w-4 h-4 object-contain" onerror="this.outerHTML='<i class=\\\'fa-solid fa-triangle-exclamation text-amber-400 text-xs\\\'></i>'">`;
    } else {
        favBox.innerHTML = `<i class="fa-solid fa-tower-cell text-blue-400 text-xs"></i>`;
    }

    // Update Sidebar Logo Preview
    const logoBox = document.getElementById('previewLogoBox');
    if (logoUrl) {
        logoBox.innerHTML = `<img src="${logoUrl}" class="w-full h-full object-contain p-0.5" onerror="this.outerHTML='<i class=\\\'fa-solid fa-tower-cell\\\'></i>'">`;
    } else {
        logoBox.innerHTML = `<i class="fa-solid fa-tower-cell"></i>`;
    }

    // Update Invoice Logo Preview
    const invBox = document.getElementById('previewInvoiceLogoBox');
    const targetInvLogo = invoiceLogoUrl || logoUrl;
    if (targetInvLogo) {
        invBox.innerHTML = `<img src="${targetInvLogo}" class="w-6 h-6 object-contain" onerror="this.outerHTML='<i class=\\\'fa-solid fa-file-invoice-dollar text-sm text-blue-600\\\'></i>'">`;
    } else {
        invBox.innerHTML = `<i class="fa-solid fa-file-invoice-dollar text-sm text-blue-600"></i>`;
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
