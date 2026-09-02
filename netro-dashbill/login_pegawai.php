<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/models.php';

$companyName = Setting::get('company_name', 'PT NETPRO TELEKOMUNIKASI INDONESIA');
$companyBrand = Setting::get('company_brand', 'NETPRO FIBER BROADBAND');
$appVersion = Setting::get('app_version', 'v3.2.0-STABLE');

$error = $_GET['error'] ?? '';
$msg = $_GET['msg'] ?? '';
$isStep2Fa = (($_GET['step'] ?? '') === '2fa' && !empty($_SESSION['2fa_pending_user']));
$pendingUser = $_SESSION['2fa_pending_user'] ?? null;

// Jika sudah login dan tidak dalam proses 2FA, arahkan langsung ke dashboard
if (is_logged_in() && !$isStep2Fa) {
    header('Location: ' . base_url('dashboard/utama.php'));
    exit;
}

// Fetch active employees for quick-switch demo
$employees = Employee::all();
$appFavicon = Setting::get('app_favicon_url', '');
$appLoginLogo = Setting::get('app_login_logo_url', Setting::get('app_logo_url', ''));
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isStep2Fa ? 'Verifikasi 2FA OTP' : 'Login Pegawai & Staff Portal' ?> | <?= APP_NAME ?></title>
    
    <?php if (!empty($appFavicon)): ?>
    <link rel="icon" href="<?= htmlspecialchars($appFavicon) ?>">
    <?php endif; ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #060911;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .bg-mesh {
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.18) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(99, 102, 241, 0.15) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(14, 165, 233, 0.12) 0px, transparent 50%);
        }
    </style>
</head>
<body class="min-h-full flex items-center justify-center p-4 bg-mesh relative overflow-x-hidden">
    <!-- Ambient Glowing Orbs -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 gap-6 z-10 my-6">
        <!-- Left Column: Branding & Live Telemetry -->
        <div class="lg:col-span-5 flex flex-col justify-between p-6 sm:p-8 rounded-3xl glass-card text-white relative overflow-hidden shadow-2xl">
            <div class="space-y-6">
                <!-- Brand Header -->
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-400 flex items-center justify-center font-black text-xl shadow-lg shadow-blue-500/30 ring-1 ring-white/20 overflow-hidden">
                        <?php if (!empty($appLoginLogo)): ?>
                            <img src="<?= htmlspecialchars($appLoginLogo) ?>" alt="Logo" class="w-full h-full object-contain p-1" onerror="this.outerHTML='<i class=\'fa-solid fa-tower-cell\'></i>'">
                        <?php else: ?>
                            <i class="fa-solid fa-tower-cell"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-lg tracking-wider"><?= APP_NAME ?></h1>
                        <span class="text-[10px] text-blue-400 font-semibold tracking-widest block uppercase">EMPLOYEE & STAFF PORTAL (ESS)</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <h2 class="text-2xl font-bold text-white leading-tight">
                        Portal Layanan Mandiri & Operasional Pegawai
                    </h2>
                    <p class="text-slate-400 text-xs leading-relaxed">
                        Akses terpadu slip gaji, absensi presensi lapangan, klaim insentif pasang, tiket gangguan, dan penilaian KPI.
                    </p>
                </div>

                <!-- Live System Indicator -->
                <div class="p-4 bg-slate-900/80 rounded-2xl border border-slate-800/80 space-y-3 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Server Node Status:</span>
                        </span>
                        <strong class="text-emerald-400 font-bold font-mono">ONLINE (0.12ms)</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Waktu Server (WIB):</span>
                        <strong id="liveClock" class="font-mono text-blue-400 font-bold"><?= date('H:i:s') ?> WIB</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Lisensi Korporat:</span>
                        <strong class="text-slate-300 truncate max-w-[160px]"><?= htmlspecialchars($companyName) ?></strong>
                    </div>
                </div>
            </div>

            <!-- Bottom Badges -->
            <div class="pt-6 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-500">
                <span>Versi: <strong class="text-slate-400 font-mono"><?= htmlspecialchars($appVersion) ?></strong></span>
                <span>Enkripsi: <strong class="text-emerald-400 font-mono">TLS 1.3 AES-256</strong></span>
            </div>
        </div>

        <!-- Right Column: Login Form OR 2FA Verification Challenge -->
        <div class="lg:col-span-7 p-6 sm:p-8 rounded-3xl glass-card text-white shadow-2xl flex flex-col justify-between space-y-6">
            <?php if ($isStep2Fa && $pendingUser): ?>
                <!-- STEP 2: 2FA OTP CHALLENGE SCREEN -->
                <div class="space-y-6">
                    <div class="flex justify-between items-center border-b border-slate-800/80 pb-4">
                        <div>
                            <h3 class="font-bold text-white text-base flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved text-emerald-400"></i> Verifikasi Dua Langkah (2FA)
                            </h3>
                            <p class="text-slate-400 text-xs">Masukkan kode 6 digit dari aplikasi Authenticator Anda.</p>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full font-mono font-bold text-[10px]">
                            2FA ACTIVE
                        </span>
                    </div>

                    <!-- User Identity Pill -->
                    <div class="p-3.5 bg-slate-900/90 rounded-2xl border border-slate-800 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div class="overflow-hidden">
                            <strong class="text-white text-xs block truncate"><?= htmlspecialchars($pendingUser['full_name']) ?></strong>
                            <span class="text-slate-400 font-mono text-[10px] block truncate"><?= htmlspecialchars($pendingUser['email']) ?> (<?= htmlspecialchars($pendingUser['role']) ?>)</span>
                        </div>
                    </div>

                    <?php if ($error === 'invalid_otp'): ?>
                        <div class="p-3.5 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl flex items-center gap-2.5 text-xs font-bold">
                            <i class="fa-solid fa-circle-exclamation text-sm"></i>
                            <span>Kode OTP 6 Digit tidak valid atau telah kedaluwarsa. Silakan ulangi.</span>
                        </div>
                    <?php endif; ?>

                    <form action="api/handler.php" method="POST" class="space-y-4 text-xs">
                        <input type="hidden" name="action" value="verify_2fa_otp">

                        <div>
                            <label class="font-semibold text-slate-300 block mb-2 text-center">
                                Kode Verifikasi OTP 6-Digit:
                            </label>
                            <input type="text" name="otp_code" autofocus required maxlength="9" placeholder="123456" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-2xl py-3 px-4 text-center font-mono font-bold text-2xl tracking-[0.4em] text-emerald-400 placeholder-slate-600 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition">
                            <span class="text-[10px] text-slate-500 block text-center mt-1.5 font-mono">
                                Tips: Masukkan 6 angka dari Google Authenticator atau kode pemulihan (misal <code>8921-9912</code> / <code>123456</code>).
                            </span>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-500/25 transition duration-200 flex items-center justify-center gap-2 text-sm">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Verifikasi & Masuk Dashboard</span>
                        </button>
                    </form>

                    <div class="pt-2 text-center">
                        <a href="logout.php" class="text-slate-400 hover:text-slate-200 text-xs">
                            <i class="fa-solid fa-arrow-left"></i> Batalkan & Gunakan Akun Lain
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <!-- STEP 1: STANDARD USERNAME & PASSWORD LOGIN SCREEN -->
                <div>
                    <div class="flex justify-between items-center border-b border-slate-800/80 pb-4 mb-5">
                        <div>
                            <h3 class="font-bold text-white text-base flex items-center gap-2">
                                <i class="fa-solid fa-right-to-bracket text-blue-400"></i> Masuk Akun Pegawai / Staff
                            </h3>
                            <p class="text-slate-400 text-xs">Gunakan NIP, Email Perusahaan, atau Username Anda.</p>
                        </div>
                        <span class="px-2.5 py-1 bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-full font-mono font-bold text-[10px]">
                            SSO READY
                        </span>
                    </div>

                    <?php if ($error === 'invalid'): ?>
                        <div class="mb-4 p-3.5 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl flex items-center gap-2.5 text-xs font-bold">
                            <i class="fa-solid fa-circle-exclamation text-sm"></i>
                            <span>NIP, Username atau Password salah. Silakan coba kembali.</span>
                        </div>
                    <?php elseif ($msg === 'logged_out'): ?>
                        <div class="mb-4 p-3.5 bg-blue-500/10 border border-blue-500/30 text-blue-400 rounded-xl flex items-center gap-2.5 text-xs font-bold">
                            <i class="fa-solid fa-circle-check text-sm"></i>
                            <span>Anda telah berhasil logout dari portal pegawai.</span>
                        </div>
                    <?php elseif ($msg === 'auth_required' || $error === 'unauthorized'): ?>
                        <div class="mb-4 p-3.5 bg-amber-500/10 border border-amber-500/30 text-amber-400 rounded-xl flex items-center gap-2.5 text-xs font-bold">
                            <i class="fa-solid fa-lock text-sm"></i>
                            <span>Silakan login terlebih dahulu untuk mengakses sistem.</span>
                        </div>
                    <?php endif; ?>

                    <form action="api/handler.php" method="POST" class="space-y-4 text-xs">
                        <input type="hidden" name="action" value="login">
                        <div>
                            <label class="font-semibold text-slate-300 block mb-1.5 flex items-center justify-between">
                                <span>NIP / Email Perusahaan / Username</span>
                                <span class="text-[10px] text-slate-500 font-mono">Contoh: superadmin atau ahmad@netpro.co.id</span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-user absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                <input type="text" id="loginUsername" name="username" required placeholder="Masukkan NIP atau Email Pegawai" value="superadmin" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl py-2.5 pl-10 pr-4 text-white font-medium placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition">
                            </div>
                        </div>

                        <div>
                            <label class="font-semibold text-slate-300 block mb-1.5 flex items-center justify-between">
                                <span>Kata Sandi (Password)</span>
                                <a href="#" onclick="alert('Silakan hubungi HRD atau Admin IT untuk mereset kata sandi melalui WhatsApp.'); return false;" class="text-[10px] text-blue-400 hover:underline">Lupa Password?</a>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-lock absolute left-3.5 top-3 text-slate-500 text-xs"></i>
                                <input type="password" id="loginPassword" name="password" required value="admin123" placeholder="Masukkan password Anda" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-xl py-2.5 pl-10 pr-10 text-white font-medium placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition">
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 top-3 text-slate-500 hover:text-slate-300 text-xs">
                                    <i id="eyeIcon" class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" checked class="rounded bg-slate-900 border-slate-700 text-blue-600 focus:ring-blue-500">
                                <span class="text-slate-400 text-[11px]">Ingat sesi login saya</span>
                            </label>
                            <span class="text-[11px] text-emerald-400 font-medium flex items-center gap-1">
                                <i class="fa-solid fa-shield-halved"></i> Proteksi 2FA Siap
                            </span>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/25 transition duration-200 flex items-center justify-center gap-2 text-sm mt-2">
                            <span>Lanjut Masuk Portal</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </form>
                </div>

                <!-- Quick 1-Click Role Switcher Demo -->
                <div class="pt-4 border-t border-slate-800/80 space-y-2.5 text-xs">
                    <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider block flex items-center gap-1.5">
                        <i class="fa-solid fa-bolt text-amber-400"></i> Quick Demo Login Akun Pegawai (1-Click Switch):
                    </span>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <button type="button" onclick="setCredentials('superadmin', 'admin123', 'Super Admin')" class="p-2 bg-slate-900/80 hover:bg-purple-600/20 border border-slate-800 hover:border-purple-500/40 rounded-xl text-left transition flex items-center gap-2 group">
                            <div class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center font-bold text-xs">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <div class="overflow-hidden">
                                <strong class="text-[11px] text-slate-200 block truncate group-hover:text-purple-300">Super Admin</strong>
                                <span class="text-[9px] text-emerald-400 font-mono block truncate">2FA Aktif ✓</span>
                            </div>
                        </button>

                        <button type="button" onclick="setCredentials('ahmad@netpro.co.id', 'password123', 'NOC Engineer')" class="p-2 bg-slate-900/80 hover:bg-blue-600/20 border border-slate-800 hover:border-blue-500/40 rounded-xl text-left transition flex items-center gap-2 group">
                            <div class="w-7 h-7 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-xs">
                                <i class="fa-solid fa-network-wired"></i>
                            </div>
                            <div class="overflow-hidden">
                                <strong class="text-[11px] text-slate-200 block truncate group-hover:text-blue-300">Ahmad F.</strong>
                                <span class="text-[9px] text-slate-500 block truncate">NOC Engineer</span>
                            </div>
                        </button>

                        <button type="button" onclick="setCredentials('rian@netpro.co.id', 'password123', 'Leader Teknisi')" class="p-2 bg-slate-900/80 hover:bg-emerald-600/20 border border-slate-800 hover:border-emerald-500/40 rounded-xl text-left transition flex items-center gap-2 group">
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs">
                                <i class="fa-solid fa-tools"></i>
                            </div>
                            <div class="overflow-hidden">
                                <strong class="text-[11px] text-slate-200 block truncate group-hover:text-emerald-300">Rian H.</strong>
                                <span class="text-[9px] text-slate-500 block truncate">Teknisi Optik</span>
                            </div>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            var pwd = document.getElementById('loginPassword');
            var icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.className = 'fa-solid fa-eye-slash';
            } else {
                pwd.type = 'password';
                icon.className = 'fa-solid fa-eye';
            }
        }

        function setCredentials(user, pass, roleName) {
            document.getElementById('loginUsername').value = user;
            document.getElementById('loginPassword').value = pass;
        }

        // Live Clock
        setInterval(function() {
            var now = new Date();
            var h = String(now.getHours()).padStart(2, '0');
            var m = String(now.getMinutes()).padStart(2, '0');
            var s = String(now.getSeconds()).padStart(2, '0');
            var el = document.getElementById('liveClock');
            if (el) el.innerText = h + ':' + m + ':' + s + ' WIB';
        }, 1000);
    </script>
</body>
</html>
