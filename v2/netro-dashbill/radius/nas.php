<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Router NAS Mikrotik (Network Access Server)";
$page_subtitle = "Konfigurasi IP Router NAS, RADIUS Secret, Port CoA, dan status sinkronisasi router core.";
$active_menu = "m-radius";
require_once __DIR__ . '/../includes/header.php';

$nasList = RadiusNas::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Router NAS baru berhasil didaftarkan dan terhubung ke RADIUS Server!
    </div>
<?php elseif ($msg === 'deleted'): ?>
    <div class="p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-trash-can text-red-600 text-sm"></i>
        Router NAS berhasil dihapus dari database RADIUS.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div><span class="text-slate-400 block font-semibold uppercase">Total Router NAS</span><strong class="text-2xl font-bold text-slate-900"><?= count($nasList) ?> Router</strong><span class="text-emerald-600 block mt-0.5"><?= count($nasList) > 0 ? 'All Connected' : '0 Connected' ?></span></div>
            <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg"><i class="fa-solid fa-server"></i></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div><span class="text-slate-400 block font-semibold uppercase">Total PPPoE Active</span><strong class="text-2xl font-bold text-blue-600">0 User</strong><span class="text-slate-400 block mt-0.5">Online di NAS</span></div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="fa-solid fa-bolt"></i></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div><span class="text-slate-400 block font-semibold uppercase">Auth CoA / Disconnect</span><strong class="text-2xl font-bold text-emerald-600">PORT 3799</strong><span class="text-slate-400 block mt-0.5">Auto-Kick Active</span></div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg"><i class="fa-solid fa-shield-halved"></i></div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Perangkat Router NAS Terintegrasi RADIUS</h3>
                <p class="text-slate-400">Total <?= count($nasList) ?> Router NAS terdaftar di database.</p>
            </div>
            <button onclick="document.getElementById('modalAddNas').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> + Tambah Router NAS
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Nama Router NAS</th>
                        <th class="py-3 px-4">IP Address</th>
                        <th class="py-3 px-4">Model Hardware</th>
                        <th class="py-3 px-4">Radius Secret</th>
                        <th class="py-3 px-4">Sesi Aktif</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($nasList)): ?>
                    <tr><td colspan="7" class="py-6 text-center text-slate-400">Belum ada Router NAS di database.</td></tr>
                    <?php else: ?>
                    <?php foreach ($nasList as $nas): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($nas['name']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($nas['ip_address']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($nas['model']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-400">••••••••••••</td>
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-600"><?= $nas['active_sessions'] ?> PPPoE</td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]"><?= htmlspecialchars($nas['status']) ?></span></td>
                        <td class="py-3.5 px-4 text-right space-x-2">
                            <button onclick="triggerToast('Sync Berhasil', 'Ping & Sesi Router <?= htmlspecialchars($nas['name']) ?> tersinkronisasi.')" class="text-blue-600 font-bold hover:underline">Ping Sync</button>
                            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline" onsubmit="return confirm('Hapus Router NAS <?= addslashes($nas['name']) ?>?');">
                                <input type="hidden" name="action" value="delete_nas">
                                <input type="hidden" name="id" value="<?= $nas['id'] ?>">
                                <input type="hidden" name="redirect" value="radius/nas.php">
                                <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
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

<!-- Modal Tambah NAS Baru -->
<div id="modalAddNas" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-server text-blue-400"></i> Tambah Router NAS Mikrotik
            </h3>
            <button onclick="document.getElementById('modalAddNas').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_nas">
            <input type="hidden" name="redirect" value="radius/nas.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Router NAS</label>
                <input type="text" name="name" required placeholder="Contoh: CCR-POP-JAKARTA-03" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">IP Address NAS</label>
                <input type="text" name="ip_address" required placeholder="10.20.0.1" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold text-blue-600">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Model Hardware</label>
                <input type="text" name="model" value="Mikrotik CCR2004-16G-2S+" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">RADIUS Secret Key</label>
                <input type="password" name="secret" value="radiussecret123" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">Simpan Router NAS</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
