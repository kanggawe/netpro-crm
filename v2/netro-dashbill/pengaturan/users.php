<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "User Admin & Manajemen Hak Akses";
$page_subtitle = "Pengaturan akun administrator sistem, hak akses modul (RBAC), dan log login.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

$users = User::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created_user'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        User Administrator baru berhasil dibuat!
    </div>
<?php elseif ($msg === 'deleted_user'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-trash-can text-rose-600 text-sm"></i>
        Akun administrator telah dihapus dari sistem.
    </div>
<?php elseif ($msg === 'updated_user'): ?>
    <div class="p-4 bg-blue-50 text-blue-800 border border-blue-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-user-check text-blue-600 text-sm"></i>
        Status akses administrator berhasil diubah!
    </div>
<?php elseif ($msg === 'updated_rbac'): ?>
    <div class="p-4 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-circle-check text-indigo-600 text-sm"></i>
        Matriks hak akses role (RBAC) berhasil diperbarui dan disimpan!
    </div>
<?php elseif ($msg === 'reset_rbac'): ?>
    <div class="p-4 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-rotate-left text-amber-600 text-sm"></i>
        Matriks hak akses role (RBAC) telah dikembalikan ke pengaturan default sistem.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-5xl mx-auto">
    <!-- User Management Table -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-users-gear text-blue-600"></i> Akun Administrator & Hak Akses
                </h3>
                <p class="text-slate-400">Total <?= count($users) ?> Akun Pengguna terdaftar dalam database.</p>
            </div>
            <button onclick="document.getElementById('modalAddUser').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg shadow flex items-center gap-1.5 transition">
                <i class="fa-solid fa-user-plus"></i> + Tambah User Admin
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Username</th>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Email Resmi</th>
                        <th class="py-3 px-4">Role Akses</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($u['username']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-900"><?= htmlspecialchars($u['full_name']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="py-3.5 px-4">
                            <?php
                            $roleLower = strtolower($u['role']);
                            $badgeClass = 'bg-blue-50 text-blue-700 border-blue-100';
                            if (strpos($roleLower, 'super') !== false || $roleLower === 'super admin (all)') {
                                $badgeClass = 'bg-purple-50 text-purple-700 border-purple-200';
                            } elseif (strpos($roleLower, 'admin') !== false) {
                                $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                            } elseif (strpos($roleLower, 'teknisi') !== false) {
                                $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                            } elseif (strpos($roleLower, 'finance') !== false) {
                                $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } elseif (strpos($roleLower, 'noc') !== false) {
                                $badgeClass = 'bg-cyan-50 text-cyan-700 border-cyan-200';
                            }
                            ?>
                            <span class="px-2.5 py-1 <?= $badgeClass ?> border font-bold rounded-full text-[10px] inline-flex items-center gap-1">
                                <?php if (strpos($roleLower, 'teknisi') !== false): ?>
                                    <i class="fa-solid fa-wrench text-[9px]"></i>
                                <?php elseif (strpos($roleLower, 'admin') !== false || strpos($roleLower, 'super') !== false): ?>
                                    <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                <?php endif; ?>
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <?php if ($u['status'] === 'active'): ?>
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">AKTIF</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 font-bold rounded-full text-[10px]">NONAKTIF</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-2">
                            <?php if ($u['username'] === 'superadmin' || $u['id'] == 1): ?>
                                <span class="text-slate-400 font-semibold italic text-[11px] inline-flex items-center gap-1.5 bg-slate-100 px-2.5 py-1 rounded-md">
                                    <i class="fa-solid fa-lock text-[10px] text-purple-600"></i> Akun Utama (Terkunci)
                                </span>
                            <?php else: ?>
                                <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline">
                                    <input type="hidden" name="action" value="toggle_user_status">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="redirect" value="pengaturan/users.php">
                                    <button type="submit" class="text-blue-600 font-bold hover:underline">
                                        <?= $u['status'] === 'active' ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>
                                <form action="<?= base_url('api/handler.php') ?>" method="POST" onsubmit="return confirm('Hapus akun administrator ini?')" class="inline">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="redirect" value="pengaturan/users.php">
                                    <button type="submit" class="text-rose-600 font-bold hover:underline">Hapus</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Matriks Hak Akses Modul (RBAC Matrix Table) -->
    <?php
    $systemModules = [
        'm-dashboard' => ['name' => 'Dashboard', 'icon' => 'fa-chart-pie'],
        'm-crm'       => ['name' => 'CRM Pelanggan', 'icon' => 'fa-users-gear'],
        'm-noc'       => ['name' => 'NOC Network', 'icon' => 'fa-microchip'],
        'm-tickets'   => ['name' => 'Tiket', 'icon' => 'fa-headset'],
        'm-billing'   => ['name' => 'Billing', 'icon' => 'fa-credit-card'],
        'm-radius'    => ['name' => 'RADIUS', 'icon' => 'fa-network-wired'],
        'm-finance'   => ['name' => 'Keuangan', 'icon' => 'fa-receipt'],
        'm-inventory' => ['name' => 'Stok / Aset', 'icon' => 'fa-boxes-stacked'],
        'm-hr'        => ['name' => 'HR SDM', 'icon' => 'fa-user-tie'],
        'm-payroll'   => ['name' => 'Payroll', 'icon' => 'fa-money-bill-wave'],
        'm-marketing' => ['name' => 'Marketing', 'icon' => 'fa-bullhorn'],
        'm-kalkulator'=> ['name' => 'Kalkulator', 'icon' => 'fa-calculator'],
        'm-laporan'   => ['name' => 'Laporan', 'icon' => 'fa-file-lines'],
        'm-pengaturan'=> ['name' => 'Pengaturan', 'icon' => 'fa-gear'],
    ];

    $activeRbacPermissions = get_rbac_matrix_permissions();

    $rbacRoles = [
        [
            'key' => 'super admin',
            'role' => 'Super Admin',
            'desc' => 'Akses mutlak & tak terbatas ke seluruh modul dan inti sistem.',
            'badge' => 'bg-purple-50 text-purple-700 border-purple-200',
            'icon' => 'fa-crown',
            'default' => array_keys($systemModules)
        ],
        [
            'key' => 'administrator',
            'role' => 'Administrator',
            'desc' => 'Operasional penuh sistem, manajemen user, billing & data bisnis.',
            'badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'icon' => 'fa-shield-halved',
            'default' => array_keys($systemModules)
        ],
        [
            'key' => 'teknisi',
            'role' => 'Teknisi / Field Engineer',
            'desc' => 'Instalasi baru, penanganan tiket gangguan, inventaris perangkat & absensi.',
            'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
            'icon' => 'fa-wrench',
            'default' => ['m-dashboard', 'm-crm', 'm-noc', 'm-tickets', 'm-inventory', 'm-hr', 'm-payroll', 'm-kalkulator']
        ],
        [
            'key' => 'finance',
            'role' => 'Finance & Billing',
            'desc' => 'Penerbitan invoice, kas & bank, rekonsiliasi denda, pajak & penggajian.',
            'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'icon' => 'fa-money-bill-wave',
            'default' => ['m-dashboard', 'm-billing', 'm-finance', 'm-payroll', 'm-kalkulator', 'm-laporan']
        ],
        [
            'key' => 'noc',
            'role' => 'NOC & Network Ops',
            'desc' => 'Monitoring uptime perangkat, router RADIUS PPPoE, dan mitigasi incident.',
            'badge' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            'icon' => 'fa-network-wired',
            'default' => ['m-dashboard', 'm-noc', 'm-radius', 'm-tickets', 'm-kalkulator', 'm-laporan']
        ],
        [
            'key' => 'support',
            'role' => 'Customer Support (CS)',
            'desc' => 'Layanan pelanggan, pembuatan tiket keluhan, cek status billing & radius.',
            'badge' => 'bg-rose-50 text-rose-700 border-rose-200',
            'icon' => 'fa-headset',
            'default' => ['m-dashboard', 'm-crm', 'm-billing', 'm-tickets', 'm-radius']
        ],
        [
            'key' => 'sales',
            'role' => 'Sales & Marketing',
            'desc' => 'Pencatatan calon pelanggan (leads), promo paket, dan kalkulator biaya.',
            'badge' => 'bg-blue-50 text-blue-700 border-blue-200',
            'icon' => 'fa-bullhorn',
            'default' => ['m-dashboard', 'm-crm', 'm-marketing', 'm-kalkulator']
        ],
    ];
    ?>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-table-cells text-purple-600"></i> Matriks Hak Akses Role (RBAC Matrix List)
                </h3>
                <p class="text-slate-400">Kelola dan sesuaikan hak akses modul untuk masing-masing role pengguna.</p>
            </div>
            <div class="flex items-center gap-2">
                <form action="<?= base_url('api/handler.php') ?>" method="POST" onsubmit="return confirm('Kembalikan seluruh matriks hak akses ke pengaturan default sistem?')" class="inline">
                    <input type="hidden" name="action" value="reset_rbac_matrix">
                    <input type="hidden" name="redirect" value="pengaturan/users.php">
                    <button type="submit" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg font-bold text-[10px] flex items-center gap-1 border border-slate-200 transition">
                        <i class="fa-solid fa-rotate-left text-[9px]"></i> Reset Default
                    </button>
                </form>
                <span class="px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded-full font-bold text-[10px] flex items-center gap-1.5">
                    <i class="fa-solid fa-lock"></i> RBAC Enforced
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse text-[11px]">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th class="py-3 px-3 text-left font-bold min-w-[170px]">Role / Jabatan</th>
                        <?php foreach ($systemModules as $modKey => $mod): ?>
                        <th class="py-3 px-1.5 font-semibold text-[10px] whitespace-nowrap" title="<?= htmlspecialchars($mod['name']) ?>">
                            <i class="fa-solid <?= $mod['icon'] ?> block mb-1 text-slate-400 text-xs"></i>
                            <span><?= htmlspecialchars($mod['name']) ?></span>
                        </th>
                        <?php endforeach; ?>
                        <th class="py-3 px-3 text-right font-bold min-w-[90px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($rbacRoles as $r): ?>
                    <?php
                        $roleKey = $r['key'];
                        $rawAllowed = $activeRbacPermissions[$roleKey] ?? $r['default'];
                        $isAll = in_array('all', $rawAllowed);
                        $allowedModules = $isAll ? array_keys($systemModules) : $rawAllowed;
                    ?>
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-3 px-3 text-left">
                            <span class="px-2 py-0.5 <?= $r['badge'] ?> border font-bold rounded-full text-[10px] inline-flex items-center gap-1 mb-1">
                                <i class="fa-solid <?= $r['icon'] ?> text-[9px]"></i>
                                <?= htmlspecialchars($r['role']) ?>
                            </span>
                            <p class="text-[10px] text-slate-400 leading-tight"><?= htmlspecialchars($r['desc']) ?></p>
                        </td>
                        <?php foreach ($systemModules as $modKey => $mod): ?>
                            <?php $hasAccess = $isAll || in_array($modKey, $allowedModules); ?>
                            <td class="py-3 px-1.5">
                                <?php if ($hasAccess): ?>
                                    <span class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs" title="Akses Diizinkan">
                                        <i class="fa-solid fa-check text-[10px]"></i>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-300 font-mono text-xs select-none" title="Tidak Memiliki Akses">―</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="py-3 px-3 text-right">
                            <?php if ($roleKey === 'super admin'): ?>
                                <span class="text-slate-400 font-semibold italic text-[10px] inline-flex items-center gap-1">
                                    <i class="fa-solid fa-lock text-[9px] text-purple-400"></i> Full
                                </span>
                            <?php else: ?>
                                <button type="button" 
                                    onclick='openEditRbacModal(<?= json_encode($roleKey) ?>, <?= json_encode($r["role"]) ?>, <?= json_encode($allowedModules) ?>)'
                                    class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg font-bold text-[10px] inline-flex items-center gap-1 transition shadow-xs">
                                    <i class="fa-solid fa-pen-to-square text-[9px]"></i> Edit
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-100 text-[11px] text-slate-500">
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center text-[9px]"><i class="fa-solid fa-check"></i></span> = Memiliki Hak Akses Modul
            </span>
            <span class="flex items-center gap-2">
                <span class="text-slate-400 font-mono font-bold">―</span> = Dibatasi (Akses Ditolak)
            </span>
        </div>
    </div>
</div>

<!-- Modal Edit Matriks Hak Akses Role -->
<div id="modalEditRbac" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 text-xs max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-indigo-600"></i> Edit Hak Akses Role: <span id="editRbacRoleName" class="text-blue-600 font-extrabold"></span>
                </h3>
                <p class="text-slate-400 text-[11px]">Centang modul yang diizinkan untuk diakses oleh pengguna dengan role ini.</p>
            </div>
            <button onclick="document.getElementById('modalEditRbac').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold text-base">✕</button>
        </div>
        
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4 flex-1 overflow-y-auto pr-1">
            <input type="hidden" name="action" value="update_rbac_matrix">
            <input type="hidden" name="redirect" value="pengaturan/users.php">
            <input type="hidden" name="role_key" id="editRbacRoleKey" value="">

            <div class="flex justify-between items-center bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-[11px]">
                <span class="text-slate-600 font-semibold">Tindakan Cepat:</span>
                <div class="space-x-2">
                    <button type="button" onclick="toggleAllRbacCheckboxes(true)" class="px-2.5 py-1 bg-white hover:bg-emerald-50 text-emerald-700 border border-slate-200 rounded-lg font-bold text-[10px] transition shadow-2xs">
                        <i class="fa-solid fa-check-double"></i> Pilih Semua
                    </button>
                    <button type="button" onclick="toggleAllRbacCheckboxes(false)" class="px-2.5 py-1 bg-white hover:bg-rose-50 text-rose-700 border border-slate-200 rounded-lg font-bold text-[10px] transition shadow-2xs">
                        <i class="fa-solid fa-xmark"></i> Batal Semua
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <?php foreach ($systemModules as $modKey => $mod): ?>
                <label class="flex items-center gap-3 p-2.5 bg-slate-50 hover:bg-indigo-50/60 border border-slate-200 hover:border-indigo-300 rounded-xl cursor-pointer transition select-none">
                    <input type="checkbox" name="modules[]" value="<?= $modKey ?>" class="rbac-mod-checkbox w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 text-xs shadow-2xs shrink-0">
                            <i class="fa-solid <?= $mod['icon'] ?>"></i>
                        </div>
                        <div class="overflow-hidden">
                            <span class="font-bold text-slate-800 block truncate"><?= htmlspecialchars($mod['name']) ?></span>
                            <span class="font-mono text-[9px] text-slate-400 block truncate"><?= $modKey ?></span>
                        </div>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>

            <div class="pt-3 border-t border-slate-100 flex gap-2">
                <button type="button" onclick="document.getElementById('modalEditRbac').classList.add('hidden')" class="w-1/3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl transition">Batal</button>
                <button type="submit" class="w-2/3 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Matriks Hak Akses
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah User -->
<div id="modalAddUser" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm">Tambah Administrator Baru</h3>
            <button onclick="document.getElementById('modalAddUser').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-3">
            <input type="hidden" name="action" value="create_user">
            <input type="hidden" name="redirect" value="pengaturan/users.php">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Username Login</label>
                    <input type="text" name="username" required placeholder="admin_teknisi1" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Password Awal</label>
                    <input type="password" name="password" required value="admin123" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold">
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" required placeholder="Nama Administrator / Teknisi" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Email Resmi</label>
                <input type="email" name="email" required placeholder="user@netpro.co.id" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Role Hak Akses</label>
                <select name="role" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option value="Super Admin (All)">Super Admin (All Access)</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Teknisi">Teknisi / Field Engineer</option>
                    <option value="Finance & Billing">Finance & Billing</option>
                    <option value="NOC & Network">NOC & Network</option>
                    <option value="Customer Support">Customer Support</option>
                    <option value="Sales Manager">Sales Manager</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow">Simpan User Admin</button>
        </form>
    </div>
</div>

<script>
function openEditRbacModal(roleKey, roleName, allowedModules) {
    document.getElementById('editRbacRoleKey').value = roleKey;
    document.getElementById('editRbacRoleName').innerText = roleName;
    
    const checkboxes = document.querySelectorAll('.rbac-mod-checkbox');
    const isAll = Array.isArray(allowedModules) && allowedModules.includes('all');
    
    checkboxes.forEach(cb => {
        cb.checked = isAll || (Array.isArray(allowedModules) && allowedModules.includes(cb.value));
    });
    
    document.getElementById('modalEditRbac').classList.remove('hidden');
}

function toggleAllRbacCheckboxes(checked) {
    const checkboxes = document.querySelectorAll('.rbac-mod-checkbox');
    checkboxes.forEach(cb => cb.checked = checked);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
