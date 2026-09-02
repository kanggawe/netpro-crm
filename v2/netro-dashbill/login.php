<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/models.php';

$companyName = Setting::get('company_name', 'PT MITRAXCON SYNERGY UTAMA');
$companyBrand = Setting::get('company_brand', 'NETPRO CRM');
$appVersion = Setting::get('app_version', 'v4.0.0-ENTERPRISE');

$isStep2Fa = (($_GET['step'] ?? '') === '2fa' && !empty($_SESSION['2fa_pending_user']));
$pendingUser = $_SESSION['2fa_pending_user'] ?? null;
$error = $_GET['error'] ?? '';
$msg = $_GET['msg'] ?? '';

// Jika sudah login dan tidak dalam proses verifikasi 2FA, arahkan langsung ke dashboard
if (is_logged_in() && !$isStep2Fa) {
    header('Location: ' . base_url('dashboard/index.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isStep2Fa ? 'Verifikasi 2FA OTP' : 'NETPRO CRM - Authentication' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 20px 40px -15px rgba(0,0,0,0.05)',
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* Very light slate background for the right side */
        }

        /* The signature curved background for the left section */
        .left-curve-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
            /* Complex clip-path to create the smooth wave/curve effect on the right edge */
            clip-path: polygon(0 0, 100% 0, 85% 50%, 100% 100%, 0 100%);
            z-index: -1;
        }

        /* Subtle dot pattern for the right background to match the reference */
        .dot-pattern {
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 20px 20px;
        }

        /* Form transition animations */
        .form-container {
            position: relative;
            min-height: 480px; /* Ensure container doesn't shrink during transition */
            overflow: hidden;
        }

        .form-section {
            width: 100%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: absolute;
            top: 0;
            left: 0;
        }

        .form-enter-right { transform: translateX(120%); opacity: 0; visibility: hidden; }
        .form-enter-left { transform: translateX(-120%); opacity: 0; visibility: hidden; }
        .form-active { transform: translateX(0); opacity: 1; visibility: visible; position: relative; }

        /* Custom Input Styling */
        .input-group {
            position: relative;
        }
        .input-group input {
            padding-left: 2.75rem; /* Space for the icon */
        }
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            transition: color 0.2s;
        }
        .input-group input:focus + .input-icon,
        .input-group input:not(:placeholder-shown) + .input-icon {
            color: #dc2626; /* Brand color on focus */
        }

        /* Custom Checkbox */
        .custom-checkbox {
            appearance: none;
            background-color: #fff;
            margin: 0;
            font: inherit;
            color: currentColor;
            width: 1.15em;
            height: 1.15em;
            border: 2px solid #cbd5e1;
            border-radius: 0.25em;
            display: grid;
            place-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .custom-checkbox::before {
            content: "";
            width: 0.65em;
            height: 0.65em;
            transform: scale(0);
            transition: 120ms transform ease-in-out;
            box-shadow: inset 1em 1em white;
            background-color: white;
            transform-origin: center;
            clip-path: polygon(14% 44%, 0 65%, 50% 100%, 100% 16%, 80% 0%, 43% 62%);
        }
        .custom-checkbox:checked {
            background-color: #dc2626;
            border-color: #dc2626;
        }
        .custom-checkbox:checked::before {
            transform: scale(1);
        }
    </style>
</head>
<body class="min-h-screen relative dot-pattern flex selection:bg-brand-500 selection:text-white">

    <div class="flex w-full min-h-screen relative z-10">
        
        <!-- ================= LEFT COLUMN: BRANDING & FITUR NETPRO CRM ================= -->
        <div class="hidden lg:flex w-[55%] relative flex-col justify-center py-12 pl-16 pr-32 text-white">
            <div class="left-curve-bg"></div>

            <div class="relative z-10 max-w-xl space-y-6">
                <!-- Header / Brand -->
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center p-1 shadow-xl transform -rotate-3 shrink-0 ring-2 ring-white/30">
                        <img src="assets/images/netpro-logo.svg" alt="NETPRO Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">NETPRO<span class="font-light"> CRM OS</span></h1>
                        <p class="text-brand-100 text-xs tracking-wider uppercase font-semibold">ISP BILLING & NETWORK MANAGEMENT</p>
                    </div>
                </div>

                <!-- Main Headline -->
                <div>
                    <h2 class="text-3xl xl:text-4xl font-bold leading-tight mb-2">
                        Platform Otomasi Bisnis ISP <br/>
                        <span class="text-yellow-400">Modern & Terlengkap</span>
                    </h2>
                    <p class="text-brand-100 text-sm max-w-lg leading-relaxed">
                        Kelola pelanggan FTTH/Hotspot, tagihan PPN 11%, operasional teknisi NOC, dan laporan keuangan dalam satu platform terintegrasi.
                    </p>
                </div>

                <!-- Features Grid -->
                <div class="grid grid-cols-2 gap-x-6 gap-y-5 pt-2">
                    <!-- Feature 1 -->
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/10 flex-shrink-0 flex items-center justify-center backdrop-blur-sm border border-white/20 text-yellow-300">
                            <i class="fa-solid fa-users text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm mb-0.5">Kelola Pelanggan</h3>
                            <p class="text-brand-200 text-xs leading-snug">Profil 360°, isolir otomatis, dan manajemen tiket gangguan.</p>
                        </div>
                    </div>
                    
                    <!-- Feature 2 -->
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/10 flex-shrink-0 flex items-center justify-center backdrop-blur-sm border border-white/20 text-yellow-300">
                            <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm mb-0.5">Automasi Billing</h3>
                            <p class="text-brand-200 text-xs leading-snug">Faktur pajak PPN 11%, QRIS, dan VA Bank otomatis.</p>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/10 flex-shrink-0 flex items-center justify-center backdrop-blur-sm border border-white/20 text-yellow-300">
                            <i class="fa-solid fa-tower-cell text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm mb-0.5">GPON & RADIUS AAA</h3>
                            <p class="text-brand-200 text-xs leading-snug">Otentikasi PPPoE, MikroTik NAS, dan monitoring OLT.</p>
                        </div>
                    </div>

                    <!-- Feature 4 -->
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/10 flex-shrink-0 flex items-center justify-center backdrop-blur-sm border border-white/20 text-yellow-300">
                            <i class="fa-solid fa-shield-halved text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-sm mb-0.5">Keamanan Enterprise</h3>
                            <p class="text-brand-200 text-xs leading-snug">Enkripsi TLS 1.3, audit trail log, dan proteksi 2FA OTP.</p>
                        </div>
                    </div>
                </div>

                <!-- Live System Indicator Card -->
                <div class="p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 space-y-2.5 text-xs font-mono">
                    <div class="flex justify-between items-center">
                        <span class="text-brand-100 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Server Node Status:</span>
                        </span>
                        <strong id="nodeLatency" class="text-emerald-300 font-bold">ONLINE (0.12ms)</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-brand-100">Waktu Server (WIB):</span>
                        <strong id="liveClock" class="text-yellow-300 font-bold"><?= date('H:i:s') ?> WIB</strong>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-brand-100">Lisensi Korporat:</span>
                        <strong class="text-white truncate max-w-[200px]"><?= htmlspecialchars($companyName) ?></strong>
                    </div>
                </div>
            </div>

            <!-- Bottom Badges -->
            <div class="absolute bottom-6 left-16 right-32 flex items-center justify-between text-xs text-brand-200/90 font-mono">
                <span>Versi: <strong class="text-white"><?= htmlspecialchars($appVersion) ?></strong></span>
                <span>Enkripsi: <strong class="text-emerald-300">TLS 1.3 AES-256</strong></span>
            </div>
        </div>

        <!-- ================= RIGHT COLUMN: AUTH FORMS ================= -->
        <div class="w-full lg:w-[45%] flex flex-col justify-center items-center p-6 sm:p-12 relative z-10">
            
            <!-- Mobile Brand (Visible only on small screens) -->
            <div class="flex lg:hidden items-center gap-3 mb-8 w-full max-w-[420px]">
                <div class="w-11 h-11 bg-white rounded-xl flex items-center justify-center p-1 shadow-md ring-1 ring-black/5 shrink-0">
                    <img src="assets/images/netpro-logo.svg" alt="NETPRO Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight">NETPRO CRM OS</h1>
                </div>
            </div>

            <!-- The Floating Form Card -->
            <div class="w-full max-w-[420px] bg-white rounded-3xl shadow-soft p-8 sm:p-10 border border-gray-100">
                
                <div class="form-container" id="formContainer">
                    
                    <?php if ($isStep2Fa && $pendingUser): ?>
                    <!-- ================= 2FA OTP CHALLENGE SCREEN ================= -->
                    <div id="twoFactorForm" class="form-section form-active">
                        <div class="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fa-solid fa-shield-halved text-brand-600"></i> Verifikasi 2FA
                                </h2>
                                <p class="text-xs text-gray-500 mt-0.5">Dua langkah otentikasi akun aman</p>
                            </div>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono font-bold text-[10px] rounded-full flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                2FA ACTIVE
                            </span>
                        </div>

                        <!-- User Profile Identity Badge -->
                        <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-brand-500 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>
                            <div class="overflow-hidden min-w-0">
                                <strong class="text-gray-900 text-xs block truncate"><?= htmlspecialchars($pendingUser['full_name'] ?? $pendingUser['username']) ?></strong>
                                <span class="text-gray-500 font-mono text-[11px] block truncate"><?= htmlspecialchars($pendingUser['email'] ?? $pendingUser['username']) ?> (<?= htmlspecialchars($pendingUser['role'] ?? 'User') ?>)</span>
                            </div>
                        </div>

                        <?php if ($error === 'invalid_otp'): ?>
                            <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2 text-xs font-semibold mb-4">
                                <i class="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                                <span>Kode OTP 6 Digit tidak valid atau telah kedaluwarsa.</span>
                            </div>
                        <?php endif; ?>

                        <form action="api/handler.php" method="POST" class="space-y-5">
                            <input type="hidden" name="action" value="verify_2fa_otp">

                            <div>
                                <label for="otpCodeInput" class="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide text-center">
                                    Masukkan Kode OTP 6-Digit:
                                </label>
                                <div class="relative">
                                    <input type="text" id="otpCodeInput" name="otp_code" autofocus required maxlength="9" 
                                        placeholder="123456" 
                                        class="w-full py-3.5 px-4 text-center font-mono font-bold text-2xl tracking-[0.35em] text-brand-700 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-brand-600 focus:bg-white focus:ring-4 focus:ring-brand-500/15 outline-none transition-all placeholder-gray-300">
                                </div>
                                <span class="text-[11px] text-gray-500 block text-center mt-2 leading-relaxed">
                                    Buka aplikasi <strong>Google Authenticator</strong> atau gunakan kode darurat: <code class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-800 font-mono font-bold text-[10px]">8921-9912</code>
                                </span>
                            </div>

                            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3.5 rounded-xl shadow-lg shadow-brand-500/25 transition-all duration-200 flex justify-center items-center gap-2 group">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Verifikasi & Masuk Dashboard</span>
                                <i class="fa-solid fa-arrow-right text-sm transform group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </form>

                        <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                            <a href="logout.php" class="text-xs font-medium text-gray-500 hover:text-brand-600 inline-flex items-center gap-1.5 transition-colors">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Batalkan & Masuk dengan Akun Lain</span>
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- ================= LOGIN FORM ================= -->
                    <div id="loginForm" class="form-section form-active">
                        <div class="text-center mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang Kembali!</h2>
                            <p class="text-sm text-gray-500">Masuk untuk melanjutkan ke akun Anda</p>
                        </div>

                        <?php if ($error === 'invalid'): ?>
                            <div class="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2 text-xs font-semibold mb-4">
                                <i class="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                                <span>Email/Username atau Password salah. Silakan coba lagi.</span>
                            </div>
                        <?php elseif ($msg === 'logged_out'): ?>
                            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-2 text-xs font-semibold mb-4">
                                <i class="fa-solid fa-circle-check text-sm shrink-0"></i>
                                <span>Anda telah berhasil logout dengan aman.</span>
                            </div>
                        <?php endif; ?>

                        <form action="api/handler.php" method="POST" class="space-y-5">
                            <input type="hidden" name="action" value="login">

                            <!-- Email Input -->
                            <div>
                                <label for="loginIdentifier" class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Email atau Username</label>
                                <div class="input-group">
                                    <input type="text" id="loginIdentifier" name="username" required 
                                        class="w-full py-3 pr-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="superadmin" value="superadmin">
                                    <i class="fa-regular fa-user input-icon"></i>
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div>
                                <label for="loginPassword" class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Password</label>
                                <div class="input-group">
                                    <input type="password" id="loginPassword" name="password" required 
                                        class="w-full py-3 pr-10 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="••••••••" value="admin123">
                                    <i class="fa-solid fa-lock input-icon"></i>
                                    <button type="button" onclick="togglePassword('loginPassword', 'loginEye')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                                        <i id="loginEye" class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Options Row -->
                            <div class="flex items-center justify-between pt-1">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="checkbox" id="rememberMe" checked class="custom-checkbox">
                                    <span class="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Ingat saya</span>
                                </label>
                                <button type="button" onclick="switchForm('forgot')" class="text-sm font-medium text-brand-600 hover:text-brand-700 hover:underline transition-all">Lupa password?</button>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl shadow-lg shadow-brand-500/25 transition-all duration-200 mt-2 flex justify-center items-center gap-2 group">
                                Masuk 
                                <i class="fa-solid fa-arrow-right text-sm transform group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="relative mt-8 mb-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-xs">
                                <span class="px-3 bg-white text-gray-400">atau masuk dengan</span>
                            </div>
                        </div>

                        <!-- Social Buttons (Google, GitHub, Facebook, X / Twitter OAuth 2.0 SSO) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <a href="api/handler.php?action=oauth_login&provider=google" title="Masuk dengan Google" class="flex items-center justify-center gap-1.5 py-2 px-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-xs font-semibold text-gray-700 shadow-sm hover:border-gray-300">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-3.5 h-3.5">
                                <span>Google</span>
                            </a>
                            <a href="api/handler.php?action=oauth_login&provider=github" title="Masuk dengan GitHub" class="flex items-center justify-center gap-1.5 py-2 px-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-xs font-semibold text-gray-700 shadow-sm hover:border-gray-300">
                                <i class="fa-brands fa-github text-sm text-gray-900"></i>
                                <span>GitHub</span>
                            </a>
                            <a href="api/handler.php?action=oauth_login&provider=facebook" title="Masuk dengan Facebook" class="flex items-center justify-center gap-1.5 py-2 px-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-xs font-semibold text-gray-700 shadow-sm hover:border-gray-300">
                                <i class="fa-brands fa-facebook text-sm text-[#1877F2]"></i>
                                <span>Facebook</span>
                            </a>
                            <a href="api/handler.php?action=oauth_login&provider=twitter" title="Masuk dengan X (Twitter)" class="flex items-center justify-center gap-1.5 py-2 px-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-xs font-semibold text-gray-700 shadow-sm hover:border-gray-300">
                                <i class="fa-brands fa-x-twitter text-sm text-gray-900"></i>
                                <span>X / Twitter</span>
                            </a>
                        </div>

                        <div class="mt-8 text-center">
                            <p class="text-sm text-gray-600">
                                Belum punya akun? 
                                <button onclick="switchForm('register')" class="font-bold text-brand-600 hover:text-brand-700 transition-colors">Daftar Sekarang</button>
                            </p>
                        </div>
                    </div>

                    <!-- ================= REGISTER FORM ================= -->
                    <div id="registerForm" class="form-section form-enter-right">
                        
                        <div class="mb-5 flex items-center justify-between">
                             <button type="button" onclick="switchForm('login')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 transition-colors">
                                <i class="fa-solid fa-arrow-left"></i>
                            </button>
                            <h2 class="text-xl font-bold text-gray-900 absolute left-1/2 -translate-x-1/2">Buat Akun</h2>
                            <div class="w-8"></div> <!-- Spacer for centering -->
                        </div>
                        <p class="text-xs text-gray-500 text-center mb-4">Daftar akun baru untuk mengakses portal NETPRO CRM.</p>

                        <!-- Social Quick Registration (Google, GitHub, Facebook, X) -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
                            <a href="api/handler.php?action=oauth_login&provider=google" title="Daftar dengan Google" class="flex items-center justify-center gap-1.5 py-2 px-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-[11px] font-semibold text-gray-700 shadow-sm">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-3.5 h-3.5">
                                <span>Google</span>
                            </a>
                            <a href="api/handler.php?action=oauth_login&provider=github" title="Daftar dengan GitHub" class="flex items-center justify-center gap-1.5 py-2 px-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-[11px] font-semibold text-gray-700 shadow-sm">
                                <i class="fa-brands fa-github text-sm text-gray-900"></i>
                                <span>GitHub</span>
                            </a>
                            <a href="api/handler.php?action=oauth_login&provider=facebook" title="Daftar dengan Facebook" class="flex items-center justify-center gap-1.5 py-2 px-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-[11px] font-semibold text-gray-700 shadow-sm">
                                <i class="fa-brands fa-facebook text-sm text-[#1877F2]"></i>
                                <span>Facebook</span>
                            </a>
                            <a href="api/handler.php?action=oauth_login&provider=twitter" title="Daftar dengan X" class="flex items-center justify-center gap-1.5 py-2 px-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-[11px] font-semibold text-gray-700 shadow-sm">
                                <i class="fa-brands fa-x-twitter text-sm text-gray-900"></i>
                                <span>X / Twitter</span>
                            </a>
                        </div>

                        <div class="relative mb-4">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                            <div class="relative flex justify-center text-[10px]"><span class="px-2 bg-white text-gray-400 uppercase font-semibold">atau formulir manual</span></div>
                        </div>

                        <form action="api/handler.php" method="POST" class="space-y-3.5">
                            <input type="hidden" name="action" value="register">

                            <!-- Name Input -->
                            <div>
                                <label for="regName" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Nama Lengkap</label>
                                <div class="input-group">
                                    <input type="text" id="regName" name="name" required 
                                        class="w-full py-2.5 pr-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="Jhon Doe">
                                    <i class="fa-regular fa-id-card input-icon"></i>
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div>
                                <label for="regEmail" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Alamat Email</label>
                                <div class="input-group">
                                    <input type="email" id="regEmail" name="email" required 
                                        class="w-full py-2.5 pr-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="nama@perusahaan.com">
                                    <i class="fa-regular fa-envelope input-icon"></i>
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div>
                                <label for="regPassword" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Buat Password</label>
                                <div class="input-group">
                                    <input type="password" id="regPassword" name="password" required minlength="6"
                                        class="w-full py-2.5 pr-10 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="Min. 6 Karakter">
                                    <i class="fa-solid fa-lock input-icon"></i>
                                    <button type="button" onclick="togglePassword('regPassword', 'regEye')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                                        <i id="regEye" class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Terms Checkbox -->
                            <div class="pt-1">
                                <label class="flex items-start gap-2.5 cursor-pointer group">
                                    <input type="checkbox" required checked class="custom-checkbox mt-0.5">
                                    <span class="text-[11px] text-gray-600 leading-snug">Saya menyetujui <a href="#" class="font-semibold text-brand-600 hover:underline">Ketentuan</a> dan <a href="#" class="font-semibold text-brand-600 hover:underline">Privasi</a>.</span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-medium py-2.5 rounded-xl shadow-lg transition-all duration-200 mt-2 flex justify-center items-center gap-2 group">
                                <i class="fa-solid fa-user-plus text-xs"></i>
                                <span>Daftar Sekarang</span>
                            </button>
                        </form>
                    </div>

                    <!-- ================= FORGOT PASSWORD FORM ================= -->
                    <div id="forgotForm" class="form-section form-enter-right">
                        <div class="mb-5 flex items-center justify-between">
                            <button type="button" onclick="switchForm('login')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 transition-colors">
                                <i class="fa-solid fa-arrow-left"></i>
                            </button>
                            <h2 class="text-xl font-bold text-gray-900 absolute left-1/2 -translate-x-1/2">Lupa Password</h2>
                            <div class="w-8"></div>
                        </div>
                        <p class="text-xs text-gray-500 text-center mb-5">Atur ulang kata sandi akun NETPRO CRM Anda.</p>

                        <form action="api/handler.php" method="POST" class="space-y-4">
                            <input type="hidden" name="action" value="reset_password">

                            <!-- Identifier Input -->
                            <div>
                                <label for="forgotIdentifier" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Email atau Username</label>
                                <div class="input-group">
                                    <input type="text" id="forgotIdentifier" name="username" required 
                                        class="w-full py-2.5 pr-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="nama@perusahaan.com atau username">
                                    <i class="fa-regular fa-user input-icon"></i>
                                </div>
                            </div>

                            <!-- New Password Input -->
                            <div>
                                <label for="forgotNewPass" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Kata Sandi Baru</label>
                                <div class="input-group">
                                    <input type="password" id="forgotNewPass" name="new_password" required minlength="6"
                                        class="w-full py-2.5 pr-10 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="Min. 6 Karakter">
                                    <i class="fa-solid fa-key input-icon"></i>
                                    <button type="button" onclick="togglePassword('forgotNewPass', 'forgotEye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                                        <i id="forgotEye1" class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password Input -->
                            <div>
                                <label for="forgotConfirmPass" class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Konfirmasi Kata Sandi Baru</label>
                                <div class="input-group">
                                    <input type="password" id="forgotConfirmPass" name="confirm_password" required minlength="6"
                                        class="w-full py-2.5 pr-10 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="Ulangi kata sandi baru">
                                    <i class="fa-solid fa-lock input-icon"></i>
                                    <button type="button" onclick="togglePassword('forgotConfirmPass', 'forgotEye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                                        <i id="forgotEye2" class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-3 rounded-xl shadow-lg shadow-brand-500/25 transition-all duration-200 mt-3 flex justify-center items-center gap-2 group">
                                <i class="fa-solid fa-rotate-right text-xs"></i>
                                <span>Simpan Kata Sandi Baru</span>
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Footer Security Note -->
            <div class="absolute bottom-6 w-full text-center lg:text-right lg:right-12 text-xs text-gray-400 flex justify-center lg:justify-end items-center gap-1.5">
                <i class="fa-solid fa-shield-halved"></i>
                Keamanan terjamin dengan enkripsi data tingkat enterprise
            </div>

        </div>
    </div>

    <!-- Custom Toast Notification -->
    <div id="toast" class="fixed top-6 right-6 transform translate-x-[150%] transition-transform duration-300 z-50 flex items-start gap-3 p-4 bg-white border border-gray-100 rounded-2xl shadow-xl max-w-sm">
        <div id="toastIconWrapper" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center">
            <i id="toastIcon" class="fa-solid fa-check text-sm"></i>
        </div>
        <div class="pt-0.5">
            <h4 id="toastTitle" class="text-sm font-bold text-gray-900 mb-0.5">Notification</h4>
            <p id="toastMessage" class="text-sm text-gray-500 leading-snug">Message goes here.</p>
        </div>
        <button onclick="hideToast()" class="ml-auto text-gray-400 hover:text-gray-600 mt-0.5">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <script>
        // --- Form Switcher Logic ---
        function switchForm(target) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const forgotForm = document.getElementById('forgotForm');

            // Reset all
            if (loginForm) {
                loginForm.classList.remove('form-active', 'form-enter-left', 'form-enter-right');
            }
            if (registerForm) {
                registerForm.classList.remove('form-active', 'form-enter-left', 'form-enter-right');
            }
            if (forgotForm) {
                forgotForm.classList.remove('form-active', 'form-enter-left', 'form-enter-right');
            }

            if (target === 'register') {
                if (loginForm) loginForm.classList.add('form-enter-left');
                if (forgotForm) forgotForm.classList.add('form-enter-right');
                if (registerForm) registerForm.classList.add('form-active');
            } else if (target === 'forgot') {
                if (loginForm) loginForm.classList.add('form-enter-left');
                if (registerForm) registerForm.classList.add('form-enter-right');
                if (forgotForm) forgotForm.classList.add('form-active');
            } else {
                if (registerForm) registerForm.classList.add('form-enter-right');
                if (forgotForm) forgotForm.classList.add('form-enter-right');
                if (loginForm) loginForm.classList.add('form-active');
            }
        }

        // --- Password Visibility Toggle ---
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // --- Toast Notification System ---
        let toastTimeout;
        function showToast(title, message, type = 'success') {
            const toast = document.getElementById('toast');
            document.getElementById('toastTitle').textContent = title;
            document.getElementById('toastMessage').textContent = message;
            
            const iconWrapper = document.getElementById('toastIconWrapper');
            const icon = document.getElementById('toastIcon');

            // Set styling based on type
            if (type === 'success') {
                iconWrapper.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-green-100 text-green-600';
                icon.className = 'fa-solid fa-check text-sm';
            } else if (type === 'error') {
                iconWrapper.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-red-100 text-red-600';
                icon.className = 'fa-solid fa-triangle-exclamation text-sm';
            } else {
                iconWrapper.className = 'flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-blue-100 text-blue-600';
                icon.className = 'fa-solid fa-info text-sm';
            }

            // Show toast
            toast.classList.remove('translate-x-[150%]');
            
            // Auto hide
            clearTimeout(toastTimeout);
            toastTimeout = setTimeout(hideToast, 4000);
        }

        function hideToast() {
            document.getElementById('toast').classList.add('translate-x-[150%]');
        }

        // --- Real-time Live Clock (WIB) ---
        function updateLiveClock() {
            var now = new Date();
            var h = String(now.getHours()).padStart(2, '0');
            var m = String(now.getMinutes()).padStart(2, '0');
            var s = String(now.getSeconds()).padStart(2, '0');
            var clockEl = document.getElementById('liveClock');
            if (clockEl) {
                clockEl.innerText = h + ':' + m + ':' + s + ' WIB';
            }
        }
        setInterval(updateLiveClock, 1000);
        updateLiveClock();

        // --- Real-time Node Latency Simulator (Real-time Heartbeat) ---
        setInterval(function() {
            var latencyEl = document.getElementById('nodeLatency');
            if (latencyEl) {
                var ms = (0.10 + Math.random() * 0.05).toFixed(2);
                latencyEl.innerText = 'ONLINE (' + ms + 'ms)';
            }
        }, 3000);

        // --- Automatic Toast Notifications based on URL Query params ---
        window.addEventListener('DOMContentLoaded', function() {
            <?php if ($error === 'invalid'): ?>
                showToast('Login Gagal', 'Email/Username atau Password tidak valid. Silakan coba lagi.', 'error');
            <?php elseif ($error === 'invalid_otp'): ?>
                showToast('Verifikasi OTP Gagal', 'Kode 6 digit OTP atau kode pemulihan salah.', 'error');
            <?php elseif ($error === 'user_not_found'): ?>
                showToast('Reset Password Gagal', 'Pengguna atau alamat email tidak ditemukan.', 'error');
                switchForm('forgot');
            <?php elseif ($error === 'pass_mismatch'): ?>
                showToast('Reset Password Gagal', 'Konfirmasi kata sandi baru tidak cocok.', 'error');
                switchForm('forgot');
            <?php elseif ($error === 'empty_identifier'): ?>
                showToast('Reset Password Gagal', 'Email atau username wajib diisi.', 'error');
                switchForm('forgot');
            <?php elseif ($error === 'reg_exists'): ?>
                showToast('Pendaftaran Gagal', 'Alamat email atau username sudah terdaftar. Silakan login.', 'error');
                switchForm('register');
            <?php elseif ($error === 'reg_empty'): ?>
                showToast('Pendaftaran Gagal', 'Semua field pendaftaran wajib diisi lengkap.', 'error');
                switchForm('register');
            <?php elseif ($error === 'reg_short_pass'): ?>
                showToast('Gagal', 'Kata sandi minimal 6 karakter.', 'error');
            <?php elseif ($msg === 'password_reset'): ?>
                showToast('Password Berhasil Diubah', 'Kata sandi baru berhasil disimpan. Silakan masuk.', 'success');
            <?php elseif ($msg === 'registered'): ?>
                showToast('Pendaftaran Berhasil', 'Akun Anda berhasil dibuat! Silakan masuk dengan akun baru.', 'success');
            <?php elseif ($msg === 'logged_out'): ?>
                showToast('Logout Berhasil', 'Anda telah keluar dari sesi dengan aman.', 'success');
            <?php elseif ($msg === '2fa_updated'): ?>
                showToast('2FA Diperbarui', 'Status keamanan Two-Factor Authentication berhasil disimpan.', 'success');
            <?php endif; ?>
        });
    </script>
</body>
</html>
