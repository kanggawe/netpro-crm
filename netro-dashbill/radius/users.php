<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Manajemen Pengguna PPPoE & Hotspot";
$page_subtitle = "Kredensial login secret PPPoE, alokasi IP Static/Dynamic pool, dan masa aktif.";
$active_menu = "m-radius";
require_once __DIR__ . '/../includes/header.php';

$users = RadiusUser::all();
$filterUser = $_GET['username'] ?? ($_GET['search'] ?? '');
if (!empty($filterUser)) {
    $filteredUsers = [];
    foreach ($users as $u) {
        if (stripos($u['username'] ?? '', $filterUser) !== false || stripos($u['customer_name'] ?? '', $filterUser) !== false) {
            $filteredUsers[] = $u;
        }
    }
    $users = $filteredUsers;
}
$msg = $_GET['msg'] ?? '';
?>

<?php if (!empty($filterUser)): ?>
    <div class="p-3.5 bg-blue-50 text-blue-900 border border-blue-200 rounded-xl flex items-center justify-between text-xs font-bold mb-4">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-filter text-blue-600"></i>
            <span>Menampilkan kredensial secret untuk pencarian: <strong><?= htmlspecialchars($filterUser) ?></strong></span>
        </div>
        <a href="<?= base_url('radius/users.php') ?>" class="px-2.5 py-1 bg-white border border-blue-300 text-blue-700 hover:bg-blue-100 rounded-lg text-[11px] font-semibold">
            ✕ Tampilkan Semua Pengguna
        </a>
    </div>
<?php endif; ?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Akun PPPoE baru berhasil dibuat dan disinkronkan ke FreeRadius!
    </div>
<?php elseif ($msg === 'deleted'): ?>
    <div class="p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-trash-can text-red-600 text-sm"></i>
        Akun PPPoE berhasil dihapus.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Akun PPPoE & Hotspot Radius (<?= count($users) ?> Akun)</h3>
                <p class="text-slate-400">Sinkronisasi langsung dengan database FreeRadius & Mikrotik RouterOS.</p>
            </div>
            <button onclick="document.getElementById('modalAddRadiusUser').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> + Tambah Akun Secret
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Username PPPoE</th>
                        <th class="py-3 px-4">Nama Pelanggan</th>
                        <th class="py-3 px-4">Profil Kecepatan</th>
                        <th class="py-3 px-4">IP Pool / Static</th>
                        <th class="py-3 px-4">Router NAS</th>
                        <th class="py-3 px-4">Status Akun</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="7" class="py-6 text-center text-slate-400">Belum ada akun PPPoE di database.</td></tr>
                    <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($u['username']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800">
                            <?php if (!empty($u['customer_id'])): ?>
                                <a href="<?= base_url('crm/detail.php?id=' . $u['customer_id']) ?>" class="hover:text-blue-600 hover:underline">
                                    <?= htmlspecialchars($u['customer_name']) ?>
                                </a>
                            <?php else: ?>
                                <?= htmlspecialchars($u['customer_name']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 font-medium text-slate-700"><?= htmlspecialchars($u['profile_name']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-600"><?= htmlspecialchars($u['ip_address']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($u['nas_name']) ?></td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]"><?= htmlspecialchars($u['status']) ?></span></td>
                        <td class="py-3.5 px-4 text-right space-x-2">
                            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline" onsubmit="return confirm('Kick / Hapus user <?= addslashes($u['username']) ?>?');">
                                <input type="hidden" name="action" value="delete_radius_user">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="redirect" value="radius/users.php">
                                <button type="submit" class="text-rose-600 font-bold hover:underline">Hapus / Kick</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Akun PPPoE -->
<div id="modalAddRadiusUser" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-400"></i> Buat Akun Secret PPPoE
            </h3>
            <button onclick="document.getElementById('modalAddRadiusUser').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_radius_user">
            <input type="hidden" name="redirect" value="radius/users.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Username PPPoE</label>
                <input type="text" name="username" required placeholder="user_pppoe_123" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold text-blue-600">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Password Secret</label>
                <input type="text" name="password" value="netpro123" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Pelanggan</label>
                <input type="text" name="customer_name" required placeholder="Nama Lengkap" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Profil Bandwidth</label>
                <select name="profile_name" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option value="PROFILE_HOME_20M">PROFILE_HOME_20M (20 Mbps)</option>
                    <option value="PROFILE_HOME_50M" selected>PROFILE_HOME_50M (50 Mbps)</option>
                    <option value="PROFILE_SOHO_100M">PROFILE_SOHO_100M (100 Mbps)</option>
                </select>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Router NAS Tujuan</label>
                <select name="nas_name" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option value="CCR-CORE-HQ-01">CCR-CORE-HQ-01 (10.0.0.1)</option>
                    <option value="CCR-POP-BEKASI-02">CCR-POP-BEKASI-02 (10.10.0.1)</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">Simpan Akun RADIUS</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
