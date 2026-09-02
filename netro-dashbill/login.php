<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/models.php';

$companyName = Setting::get('company_name', 'PT MITRAXCON SYNERGY UTAMA');
$companyBrand = Setting::get('company_brand', 'NETPRO CRM');
$appVersion = Setting::get('app_version', 'v3.2.0-STABLE');

// Jika sudah login, arahkan langsung ke dashboard
if (is_logged_in()) {
    header('Location: ' . base_url('dashboard/utama.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IGNITE - Authentication</title>
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
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-brand-700 shadow-lg transform -rotate-6 shrink-0">
                        <i class="fa-solid fa-tower-broadcast text-2xl text-brand-600"></i>
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
                <div class="w-10 h-10 bg-brand-600 rounded-lg flex items-center justify-center text-white shadow-md">
                    <i class="fa-solid fa-fire-flame-curved"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight">IGNITE</h1>
                </div>
            </div>

            <!-- The Floating Form Card -->
            <div class="w-full max-w-[420px] bg-white rounded-3xl shadow-soft p-8 sm:p-10 border border-gray-100">
                
                <div class="form-container" id="formContainer">
                    
                    <!-- ================= LOGIN FORM ================= -->
                    <div id="loginForm" class="form-section form-active">
                        <div class="text-center mb-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang Kembali!</h2>
                            <p class="text-sm text-gray-500">Masuk untuk melanjutkan ke akun Anda</p>
                        </div>

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
                                <a href="#" onclick="showToast('Bantuan', 'Silakan hubungi IT Administrator untuk reset password.', 'info'); return false;" class="text-sm font-medium text-brand-600 hover:text-brand-700 hover:underline transition-all">Lupa password?</a>
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

                        <!-- Social Buttons -->
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="mockSocialLogin('Google')" class="flex items-center justify-center gap-2 py-2.5 px-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700">
                                <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-4 h-4">
                                Google
                            </button>
                            <button onclick="mockSocialLogin('GitHub')" class="flex items-center justify-center gap-2 py-2.5 px-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors text-sm font-medium text-gray-700">
                                <i class="fa-brands fa-github text-base"></i>
                                GitHub
                            </button>
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
                        
                        <div class="mb-6 flex items-center justify-between">
                             <button onclick="switchForm('login')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-500 transition-colors">
                                <i class="fa-solid fa-arrow-left"></i>
                            </button>
                            <h2 class="text-xl font-bold text-gray-900 absolute left-1/2 -translate-x-1/2">Buat Akun</h2>
                            <div class="w-8"></div> <!-- Spacer for centering -->
                        </div>
                        <p class="text-sm text-gray-500 text-center mb-6">Bergabunglah untuk mengelola bisnis Anda lebih baik.</p>

                        <form onsubmit="handleAuth(event, 'register')" class="space-y-4">
                            <!-- Name Input -->
                            <div>
                                <label for="regName" class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Nama Lengkap</label>
                                <div class="input-group">
                                    <input type="text" id="regName" required 
                                        class="w-full py-2.5 pr-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="Jhon Doe">
                                    <i class="fa-regular fa-id-card input-icon"></i>
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div>
                                <label for="regEmail" class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Alamat Email</label>
                                <div class="input-group">
                                    <input type="email" id="regEmail" required 
                                        class="w-full py-2.5 pr-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="nama@perusahaan.com">
                                    <i class="fa-regular fa-envelope input-icon"></i>
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div>
                                <label for="regPassword" class="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">Buat Password</label>
                                <div class="input-group">
                                    <input type="password" id="regPassword" required minlength="8"
                                        class="w-full py-2.5 pr-10 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 outline-none transition-all text-sm bg-gray-50/50 focus:bg-white"
                                        placeholder="Min. 8 Karakter">
                                    <i class="fa-solid fa-lock input-icon"></i>
                                    <button type="button" onclick="togglePassword('regPassword', 'regEye')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                                        <i id="regEye" class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Terms Checkbox -->
                            <div class="pt-2">
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <input type="checkbox" required class="custom-checkbox mt-0.5">
                                    <span class="text-xs text-gray-600 leading-snug">Saya menyetujui <a href="#" class="font-semibold text-brand-600 hover:underline">Syarat & Ketentuan</a> serta <a href="#" class="font-semibold text-brand-600 hover:underline">Kebijakan Privasi</a> yang berlaku.</span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-medium py-3 rounded-xl shadow-lg transition-all duration-200 mt-2 flex justify-center items-center gap-2 group">
                                Daftar Sekarang
                            </button>
                        </form>
                    </div>

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

            if (target === 'register') {
                // Slide login out to left, register in from right
                loginForm.classList.remove('form-active');
                loginForm.classList.add('form-enter-left');
                
                registerForm.classList.remove('form-enter-right');
                registerForm.classList.add('form-active');
            } else {
                // Slide register out to right, login in from left
                registerForm.classList.remove('form-active');
                registerForm.classList.add('form-enter-right');
                
                loginForm.classList.remove('form-enter-left');
                loginForm.classList.add('form-active');
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

        // --- Mock Authentication Submission ---
        function handleAuth(event, type) {
            event.preventDefault();
            
            if (type === 'register') {
                const name = document.getElementById('regName').value;
                showToast('Akun Dibuat', `Halo ${name}, akun Anda berhasil didaftarkan.`, 'success');
                // Switch back to login after short delay
                setTimeout(() => switchForm('login'), 1500);
            }
        }

        function mockSocialLogin(provider) {
            showToast('Otentikasi SSO', `Mengarahkan ke halaman login ${provider}...`, 'info');
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
    </script>
</body>
</html>
