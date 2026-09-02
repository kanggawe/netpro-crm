<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Backup & Pemeliharaan Database";
$page_subtitle = "Pencadangan snapshot database sistem, riwayat backup, dan prosedur pemulihan data.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

$backups = Backup::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'backup_created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-4xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Snapshot database berhasil dibuat dan disimpan!
    </div>
<?php elseif ($msg === 'backup_deleted'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-4xl mx-auto">
        <i class="fa-solid fa-trash-can text-rose-600 text-sm"></i>
        File snapshot database telah berhasil dihapus.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-4xl mx-auto">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-database text-blue-600"></i> Snapshot & Cadangan Database
                </h3>
                <p class="text-slate-400">Total <?= count($backups) ?> Snapshot database tersimpan aman.</p>
            </div>
            <form action="<?= base_url('api/handler.php') ?>" method="POST">
                <input type="hidden" name="action" value="create_backup">
                <input type="hidden" name="redirect" value="pengaturan/backup.php">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl shadow transition flex items-center gap-2">
                    <i class="fa-solid fa-cloud-arrow-down"></i> + Buat Snapshot Backup Sekarang
                </button>
            </form>
        </div>

        <div class="space-y-4">
            <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Riwayat File Backup Database</h4>
            <div class="overflow-x-auto border border-slate-200 rounded-xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                            <th class="py-3 px-4">Nama File Snapshot</th>
                            <th class="py-3 px-4 font-mono text-center">Ukuran File</th>
                            <th class="py-3 px-4">Waktu Pembuatan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($backups as $bk): ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900"><?= htmlspecialchars($bk['filename']) ?></td>
                            <td class="py-3.5 px-4 font-mono text-center text-slate-600"><?= htmlspecialchars($bk['filesize'] ?? '1.4 MB') ?></td>
                            <td class="py-3.5 px-4 text-slate-500 font-mono"><?= htmlspecialchars($bk['created_at']) ?></td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <a href="<?= base_url('database/' . $bk['filename']) ?>" download class="text-blue-600 font-bold hover:underline">Unduh File</a>
                                <form action="<?= base_url('api/handler.php') ?>" method="POST" onsubmit="return confirm('Hapus file snapshot ini?')" class="inline">
                                    <input type="hidden" name="action" value="delete_backup">
                                    <input type="hidden" name="id" value="<?= $bk['id'] ?>">
                                    <input type="hidden" name="redirect" value="pengaturan/backup.php">
                                    <button type="submit" class="text-rose-600 font-bold hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
