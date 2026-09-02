<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Profil Kecepatan & Bandwidth Limiter";
$page_subtitle = "Manajemen profil rate-limit upload/download, burst bandwidth, dan IP pool.";
$active_menu = "m-radius";
require_once __DIR__ . '/../includes/header.php';
?>


<?php
$profiles = RadiusProfile::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Profil bandwidth baru berhasil dibuat di FreeRadius!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Profil Kecepatan RADIUS (<?= count($profiles) ?> Profil)</h3>
                <p class="text-slate-400">Parameter Simple Queue & CoA Rate-Limit pada Mikrotik.</p>
            </div>
            <button onclick="document.getElementById('modalAddProfile').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-gauge-simple-high"></i> + Buat Profil Bandwidth
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Nama Profil RADIUS</th>
                        <th class="py-3 px-4">Rate Limit (Rx / Tx)</th>
                        <th class="py-3 px-4">Burst Limit</th>
                        <th class="py-3 px-4">Address Pool</th>
                        <th class="py-3 px-4">Total Pelanggan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($profiles as $pro): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($pro['name']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900"><?= htmlspecialchars($pro['rate_limit']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-emerald-600"><?= htmlspecialchars($pro['burst_limit']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-600"><?= htmlspecialchars($pro['pool_name']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= $pro['user_count'] ?> User</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Profil Bandwidth -->
<div id="modalAddProfile" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-gauge-simple-high text-blue-400"></i> Buat Profil Bandwidth Baru
            </h3>
            <button onclick="document.getElementById('modalAddProfile').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_radius_profile">
            <input type="hidden" name="redirect" value="radius/profiles.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Profil</label>
                <input type="text" name="name" required placeholder="Contoh: PROFILE_100M_DEDICATED" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold uppercase text-blue-600">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Upload / Download Rate</label>
                    <input type="text" name="rate_limit" required placeholder="100M/100M" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Burst Limit</label>
                    <input type="text" name="burst_limit" placeholder="150M/150M (20s)" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">IP Pool Mikrotik</label>
                <input type="text" name="pool_name" value="pool_pppoe_home" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">Simpan Profil Bandwidth</button>
        </form>
    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
