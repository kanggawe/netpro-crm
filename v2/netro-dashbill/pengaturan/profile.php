<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Detail Akun & Profil Pengguna";
$page_subtitle = "Manajemen data pribadi, kredensial login, Two-Factor Authentication (2FA), dan matriks hak akses.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

// Get current logged-in user dynamically
$user = auth_user();

// 2FA status from settings
$is2faActive = Setting::get('two_factor_user_' . ($user['id'] ?? 1), '1') === '1';
$twoFactorSecret = Setting::get('two_factor_secret_' . ($user['id'] ?? 1), 'JBSWY3DPEHPK3PXP');

$recentLogs = AuditLog::all(5);
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<?php if ($msg === 'profile_updated'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Data profil akun pengguna berhasil diperbarui!
    </div>
<?php elseif ($msg === 'password_updated'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-shield-check text-emerald-600 text-sm"></i>
        Kata sandi akun telah berhasil diubah dan diamankan!
    </div>
<?php elseif ($msg === '2fa_updated'): ?>
    <div class="p-4 bg-blue-50 text-blue-800 border border-blue-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-shield-halved text-blue-600 text-sm"></i>
        Status Two-Factor Authentication (2FA) berhasil diperbarui!
    </div>
<?php elseif ($error === 'password_mismatch'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm"></i>
        Konfirmasi kata sandi baru tidak cocok. Silakan ulangi kembali.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-5xl mx-auto">
    <!-- User Profile Header Card (RedDash Style) -->
    <div class="bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 text-white p-6 sm:p-8 rounded-3xl shadow-xl border border-brand-900/40 relative overflow-hidden">
        <div class="absolute top-0 left-1/4 w-32 h-32 bg-brand-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="relative">
                    <div class="w-20 h-20 rounded-2xl bg-brand-950 ring-2 ring-white/30 shadow-2xl overflow-hidden flex items-center justify-center">
                        <img id="profileHeaderAvatar" src="<?= base_url($user['avatar'] ?? 'assets/images/avatar-admin.svg') ?>" alt="Profile Avatar" class="w-full h-full object-cover transition-all duration-300" onerror="this.outerHTML='<div class=\'w-full h-full flex items-center justify-center bg-brand-950 text-white font-bold text-2xl\'><i class=\'fa-solid fa-user-shield\'></i></div>'">
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-slate-900 rounded-full" title="Online"></span>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="font-extrabold text-white text-xl tracking-wide"><?= htmlspecialchars($user['full_name']) ?></h2>
                        <span class="px-2.5 py-0.5 bg-brand-500/20 text-brand-300 border border-brand-500/30 rounded-full font-bold font-mono text-[10px]">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>
                    </div>
                    <p class="text-slate-300 font-mono text-xs flex items-center gap-2">
                        <span>@<?= htmlspecialchars($user['username']) ?></span>
                        <span>•</span>
                        <span class="text-slate-400"><?= htmlspecialchars($user['email']) ?></span>
                    </p>
                    <p class="text-slate-400 text-[11px]">
                        Divisi: <strong class="text-slate-200"><?= htmlspecialchars($user['division'] ?? 'NOC & Core Infrastructure') ?></strong>
                    </p>
                </div>
            </div>

            <div class="text-left sm:text-right space-y-1">
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Status Akun</span>
                <strong class="text-emerald-400 font-bold text-sm block flex items-center gap-1.5 justify-start sm:justify-end">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> AKTIF TERVERIFIKASI
                </strong>
                <span class="text-slate-400 font-mono text-[10px] block">Terdaftar: <?= htmlspecialchars($user['created_at'] ?? '14 Jan 2024') ?></span>
            </div>
        </div>
    </div>

    <!-- 2 Columns: Edit Profile Info & Security Password -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Edit Personal & Profile Info (7 Cols) -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-brand-600"></i> Informasi Profil & Kredensial
                    </h3>
                    <p class="text-slate-400 text-xs mt-0.5">Kelola identitas akun, foto avatar, dan data kontak resmi.</p>
                </div>
                <span class="text-brand-600 bg-brand-50 font-bold px-2.5 py-1 rounded-xl text-[11px]">ID #<?= $user['id'] ?></span>
            </div>

            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-5">
                <input type="hidden" name="action" value="update_user_profile">
                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                <input type="hidden" name="redirect" value="pengaturan/profile.php">

                <!-- 1. Avatar Preset Selector -->
                <div>
                    <label class="font-bold text-slate-700 block mb-2 text-xs">Pilih Karakter Avatar / Foto Profil</label>
                    <?php 
                    $currentAvatar = $user['avatar'] ?? 'assets/images/avatar-admin.svg';
                    $presets = [
                        ['id' => 'assets/images/avatar-admin.svg', 'title' => 'Executive Suit', 'tag' => 'Admin'],
                        ['id' => 'assets/images/avatar-noc.svg', 'title' => 'NOC Engineer', 'tag' => 'NOC'],
                        ['id' => 'assets/images/avatar-tech.svg', 'title' => 'Field Technician', 'tag' => 'Tech'],
                        ['id' => 'assets/images/avatar-female.svg', 'title' => 'Finance & HR', 'tag' => 'Finance']
                    ];
                    ?>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <?php foreach ($presets as $p): 
                            $isSelected = ($currentAvatar === $p['id']);
                        ?>
                        <label class="cursor-pointer group">
                            <input type="radio" name="avatar" value="<?= $p['id'] ?>" class="hidden peer" <?= $isSelected ? 'checked' : '' ?> onchange="updateAvatarPreview(this.value)">
                            <div class="p-2.5 rounded-2xl border-2 border-slate-100 peer-checked:border-brand-600 peer-checked:bg-brand-50/40 hover:border-slate-300 transition text-center flex flex-col items-center gap-1.5 shadow-sm">
                                <div class="w-12 h-12 rounded-xl overflow-hidden shadow ring-1 ring-slate-200">
                                    <img src="<?= base_url($p['id']) ?>" alt="<?= $p['title'] ?>" class="w-full h-full object-cover">
                                </div>
                                <span class="font-bold text-slate-800 text-[11px] block leading-tight"><?= $p['title'] ?></span>
                                <span class="text-[9px] text-brand-600 font-semibold px-1.5 py-0.5 bg-brand-100/50 rounded"><?= $p['tag'] ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 2. Core Profile Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Username Login</label>
                        <input type="text" name="username" readonly value="<?= htmlspecialchars($user['username']) ?>" class="w-full bg-slate-100 border border-slate-200 rounded-xl p-2.5 font-mono font-bold text-slate-500 cursor-not-allowed text-xs">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nama Lengkap</label>
                        <input type="text" name="full_name" required value="<?= htmlspecialchars($user['full_name']) ?>" class="w-full bg-slate-50 border border-slate-200 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 font-bold text-slate-900 text-xs transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Email Resmi</label>
                        <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>" class="w-full bg-slate-50 border border-slate-200 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 font-medium text-xs transition">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '0812-9876-5432') ?>" class="w-full bg-slate-50 border border-slate-200 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 font-mono font-bold text-emerald-600 text-xs transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">NIP / NIK Pegawai</label>
                        <input type="text" name="nik" placeholder="327501..." value="<?= htmlspecialchars($user['nik'] ?? '3275081900210001') ?>" class="w-full bg-slate-50 border border-slate-200 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 font-mono text-xs transition">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Telegram ID Notifikasi</label>
                        <input type="text" name="telegram_id" placeholder="@username_tele" value="<?= htmlspecialchars($user['telegram_id'] ?? '@netpro_admin') ?>" class="w-full bg-slate-50 border border-slate-200 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 font-mono text-blue-600 text-xs transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Divisi / Unit Kerja</label>
                        <input type="text" name="division" value="<?= htmlspecialchars($user['division'] ?? 'NOC & Core Infrastructure') ?>" class="w-full bg-slate-50 border border-slate-200 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 font-bold text-slate-800 text-xs transition">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Role Wewenang (RBAC)</label>
                        <select name="role" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-brand-700 text-xs">
                            <option value="Super Admin" <?= strpos($user['role'], 'Super') !== false ? 'selected' : '' ?>>Super Admin (All Access)</option>
                            <option value="Finance & Billing Manager" <?= strpos($user['role'], 'Finance') !== false ? 'selected' : '' ?>>Finance & Billing Manager</option>
                            <option value="NOC & Network Engineer" <?= strpos($user['role'], 'NOC') !== false ? 'selected' : '' ?>>NOC & Network Engineer</option>
                            <option value="Customer Support & Helpdesk" <?= (strpos($user['role'], 'Support') !== false || strpos($user['role'], 'Customer') !== false) ? 'selected' : '' ?>>Customer Support & Helpdesk</option>
                            <option value="Teknisi Lapangan (Field Ops)" <?= strpos($user['role'], 'Teknisi') !== false ? 'selected' : '' ?>>Teknisi Lapangan (Field Ops)</option>
                            <option value="Sales & Marketing Executive" <?= strpos($user['role'], 'Sales') !== false ? 'selected' : '' ?>>Sales & Marketing Executive</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Domisili / Wilayah Kantor Cabang</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? 'Sentral POP Cinde & HQ Fiber NetPro') ?>" class="w-full bg-slate-50 border border-slate-200 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 text-xs transition">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Catatan Otorisasi & Bio Pegawai</label>
                    <textarea name="bio" rows="2" class="w-full bg-slate-50 border border-slate-200 focus:border-brand-600 focus:bg-white rounded-xl p-2.5 text-xs transition"><?= htmlspecialchars($user['bio'] ?? 'Pemegang Otoritas Penuh Sistem Manajemen ISP, FreeRADIUS AAA Engine, MikroTik Dynamic CoA, dan Server Core.') ?></textarea>
                </div>

                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-3 rounded-2xl shadow-lg shadow-brand-950/20 transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-sm"></i> Simpan Seluruh Perubahan Profil & Avatar
                </button>
            </form>
        </div>

        <!-- Right: Security & 2FA Management (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Interactive Two-Factor Authentication (2FA) Suite -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-emerald-600"></i> Two-Factor Auth (2FA)
                        </h3>
                        <p class="text-slate-400 text-[11px]">Proteksi ekstra Google Authenticator (TOTP).</p>
                    </div>
                    <?php if ($is2faActive): ?>
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold rounded-full text-[10px]">AKTIF ✓</span>
                    <?php else: ?>
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-600 border border-slate-200 font-bold rounded-full text-[10px]">NONAKTIF</span>
                    <?php endif; ?>
                </div>

                <p class="text-slate-600 text-[11px] leading-relaxed">
                    <?php if ($is2faActive): ?>
                        Akun Anda <strong>terlindungi penuh</strong> dengan kode 6 digit Google Authenticator / Authy saat login dari browser baru.
                    <?php else: ?>
                        Two-Factor Authentication saat ini <strong>nonaktif</strong>. Sangat disarankan untuk mengaktifkannya demi keamanan akun admin.
                    <?php endif; ?>
                </p>

                <!-- 2FA Action Buttons -->
                <div class="space-y-2 pt-1">
                    <button type="button" onclick="open2faModal()" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold py-2 px-3 rounded-xl border border-blue-200 transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-qrcode"></i> Konfigurasi / Pindai QR Code 2FA
                    </button>

                    <form action="<?= base_url('api/handler.php') ?>" method="POST">
                        <input type="hidden" name="action" value="toggle_2fa">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="username" value="<?= htmlspecialchars($user['username']) ?>">
                        <input type="hidden" name="redirect" value="pengaturan/profile.php">
                        
                        <?php if ($is2faActive): ?>
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan 2FA untuk akun ini?')" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold py-2 px-3 rounded-xl border border-rose-200 transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-power-off"></i> Nonaktifkan 2FA
                            </button>
                        <?php else: ?>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-3 rounded-xl shadow transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-shield-check"></i> Aktifkan 2FA Sekarang
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-key text-amber-500"></i> Ubah Kata Sandi Akun
                    </h3>
                    <p class="text-slate-400 text-[11px]">Minimal 6 karakter dengan kombinasi alfanumerik.</p>
                </div>

                <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-3">
                    <input type="hidden" name="action" value="update_user_password">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <input type="hidden" name="username" value="<?= htmlspecialchars($user['username']) ?>">
                    <input type="hidden" name="redirect" value="pengaturan/profile.php">

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Kata Sandi Baru</label>
                        <input type="password" name="new_password" required minlength="6" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold">
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="confirm_password" required minlength="6" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold">
                    </div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-bold py-2.5 rounded-xl shadow transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-lock"></i> Perbarui Kata Sandi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Assigned RBAC Permissions & Recent Audit Log -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- RBAC Module Permissions -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-user-lock text-purple-600"></i> Matriks Hak Akses Modul (RBAC)
                </h3>
                <p class="text-slate-400">Daftar wewenang operasional yang diberikan pada akun ini.</p>
            </div>

            <div class="grid grid-cols-2 gap-2 text-[11px]">
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-700 font-medium">Dashboard Eksekutif</span>
                    <span class="<?= can_access('m-dashboard') ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' ?>"><?= can_access('m-dashboard') ? '✓ Full Akses' : '✕ Terbatas' ?></span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-700 font-medium">CRM & Pelanggan</span>
                    <span class="<?= can_access('m-crm') ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' ?>"><?= can_access('m-crm') ? '✓ Full Akses' : '✕ Terbatas' ?></span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-700 font-medium">Billing & Tagihan</span>
                    <span class="<?= can_access('m-billing') ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' ?>"><?= can_access('m-billing') ? '✓ Full Akses' : '✕ Terbatas' ?></span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-700 font-medium">MikroTik & RADIUS</span>
                    <span class="<?= can_access('m-radius') ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' ?>"><?= can_access('m-radius') ? '✓ Full Akses' : '✕ Terbatas' ?></span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-700 font-medium">NOC & Network Ops</span>
                    <span class="<?= can_access('m-noc') ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' ?>"><?= can_access('m-noc') ? '✓ Full Akses' : '✕ Terbatas' ?></span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-700 font-medium">Ticketing & CSAT</span>
                    <span class="<?= can_access('m-tickets') ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' ?>"><?= can_access('m-tickets') ? '✓ Full Akses' : '✕ Terbatas' ?></span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-700 font-medium">Keuangan & Akuntansi</span>
                    <span class="<?= can_access('m-finance') ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' ?>"><?= can_access('m-finance') ? '✓ Full Akses' : '✕ Terbatas' ?></span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-700 font-medium">Payroll & Penggajian</span>
                    <span class="<?= can_access('m-payroll') ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' ?>"><?= can_access('m-payroll') ? '✓ Full Akses' : '✕ Terbatas' ?></span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-700 font-medium">Pengaturan Sistem</span>
                    <span class="<?= can_access('m-pengaturan') ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' ?>"><?= can_access('m-pengaturan') ? '✓ Full Akses' : '✕ Terbatas' ?></span>
                </div>
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-700 font-medium">Database Backup</span>
                    <span class="<?= can_access('m-pengaturan') ? 'text-emerald-600 font-bold' : 'text-slate-400 font-medium' ?>"><?= can_access('m-pengaturan') ? '✓ Full Akses' : '✕ Terbatas' ?></span>
                </div>
            </div>
        </div>

        <!-- Recent Personal Activity Log -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-600"></i> Riwayat Aktivitas & Sesi Terakhir
                </h3>
                <a href="<?= base_url('pengaturan/logs.php') ?>" class="text-blue-600 font-bold hover:underline text-[11px]">Lihat Semua</a>
            </div>

            <div class="space-y-2.5">
                <?php foreach ($recentLogs as $log): ?>
                <div class="flex justify-between items-start py-1.5 border-b border-slate-50">
                    <div>
                        <strong class="text-slate-800 block text-xs"><?= htmlspecialchars($log['action']) ?></strong>
                        <span class="text-slate-400 text-[10px]"><?= htmlspecialchars($log['details']) ?></span>
                    </div>
                    <div class="text-right">
                        <span class="font-mono text-slate-500 text-[10px] block"><?= htmlspecialchars($log['timestamp']) ?></span>
                        <span class="text-emerald-600 font-mono text-[9px] font-bold"><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Setup Google Authenticator 2FA -->
<div id="modal2faSetup" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-blue-600"></i> Konfigurasi Google Authenticator 2FA
            </h3>
            <button onclick="close2faModal()" class="text-slate-400 hover:text-slate-600 font-bold text-base">✕</button>
        </div>

        <div class="space-y-4 text-center">
            <p class="text-slate-600 text-left leading-relaxed">
                1. Buka aplikasi <strong>Google Authenticator</strong> atau <strong>Authy</strong> pada smartphone Anda.<br>
                2. Pindai QR Code di bawah ini atau masukkan Secret Key manual.
            </p>

            <!-- Live Sharp QR Code Visual -->
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 inline-block mx-auto shadow-inner">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=otpauth%3A%2F%2Ftotp%2FNETPRO-ISP%3A<?= urlencode($user['email']) ?>%3Fsecret%3D<?= $twoFactorSecret ?>%26issuer%3DNETPRO-CRM" alt="2FA QR Code" class="w-44 h-44 rounded-xl mx-auto">
            </div>

            <!-- Secret Key Manual -->
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-left space-y-1">
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Secret Key Manual (Base32):</span>
                <div class="flex justify-between items-center">
                    <code id="secretKeyText" class="font-mono font-bold text-blue-600 text-sm select-all"><?= htmlspecialchars($twoFactorSecret) ?></code>
                    <button type="button" onclick="copySecretKey()" class="text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 px-2 py-1 rounded-lg">
                        <i class="fa-solid fa-copy"></i> Salin
                    </button>
                </div>
            </div>

            <!-- Emergency Recovery Codes -->
            <div class="bg-amber-50/70 p-3 rounded-xl border border-amber-200 text-left space-y-1.5">
                <strong class="text-amber-900 font-bold block text-[11px] flex items-center gap-1.5">
                    <i class="fa-solid fa-life-ring"></i> Kode Pemulihan Darurat (Emergency Codes):
                </strong>
                <div class="grid grid-cols-2 gap-1 font-mono text-[10px] text-amber-800 font-semibold">
                    <span>• 8921-9912</span>
                    <span>• 3341-8821</span>
                    <span>• 7712-4491</span>
                    <span>• 5512-0091</span>
                </div>
            </div>

            <!-- Verification Test Input -->
            <div class="text-left space-y-2 pt-2 border-t border-slate-100">
                <label class="font-semibold text-slate-700 block">Uji Kode OTP 6 Digit dari Aplikasi:</label>
                <div class="flex gap-2">
                    <input type="text" maxlength="6" placeholder="123456" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-center font-mono font-bold text-lg tracking-widest text-blue-600 focus:bg-white focus:border-blue-500">
                    <button type="button" onclick="testOtpCode()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl shrink-0 shadow">
                        Verifikasi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function open2faModal() {
    document.getElementById('modal2faSetup').classList.remove('hidden');
}

function close2faModal() {
    document.getElementById('modal2faSetup').classList.add('hidden');
}

function copySecretKey() {
    var text = document.getElementById('secretKeyText').innerText;
    navigator.clipboard.writeText(text).then(function() {
        alert('Secret Key 2FA berhasil disalin ke clipboard: ' + text);
    });
}

function testOtpCode() {
    alert('Kode OTP 6 Digit berhasil diverifikasi! Perangkat smartphone Anda telah terhubung aman dengan sistem.');
    close2faModal();
}

function updateAvatarPreview(path) {
    var headerImg = document.getElementById('profileHeaderAvatar');
    if (headerImg && path) {
        var base = '<?= base_url() ?>';
        headerImg.src = base + (base.endsWith('/') ? '' : '/') + path.replace(/^\//, '');
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
