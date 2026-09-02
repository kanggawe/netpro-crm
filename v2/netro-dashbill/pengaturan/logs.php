<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Audit Logs & Rekam Jejak Sistem";
$page_subtitle = "Catatan kronologis seluruh aktivitas pengguna, perubahan tarif, dan eksekusi isolir.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

$logs = AuditLog::all(50);
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'logs_cleared'): ?>
    <div class="p-4 bg-amber-50 text-amber-800 border border-amber-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-broom text-amber-600 text-sm"></i>
        Seluruh rekaman audit log sistem telah berhasil dibersihkan!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-5xl mx-auto">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-blue-600"></i> Rekam Jejak Audit Trail Sistem (Live Database)
                </h3>
                <p class="text-slate-400">Total <?= count($logs) ?> Entri aktivitas terekam.</p>
            </div>
            <div class="flex gap-2">
                <form action="<?= base_url('api/handler.php') ?>" method="POST" onsubmit="return confirm('Kosongkan seluruh riwayat audit log sistem?')">
                    <input type="hidden" name="action" value="clear_audit_logs">
                    <input type="hidden" name="redirect" value="pengaturan/logs.php">
                    <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold px-3 py-1.5 rounded-lg border border-rose-200 flex items-center gap-1">
                        <i class="fa-solid fa-trash-can"></i> Bersihkan Log
                    </button>
                </form>
                <button onclick="window.print()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg flex items-center gap-1">
                    <i class="fa-solid fa-print"></i> Cetak Log
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Waktu (WIB)</th>
                        <th class="py-3 px-4">User Admin</th>
                        <th class="py-3 px-4">Jenis Aktivitas</th>
                        <th class="py-3 px-4">IP Address</th>
                        <th class="py-3 px-4 text-right">Rincian Operasional</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada riwayat audit log.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($logs as $l): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono text-slate-500"><?= htmlspecialchars($l['timestamp']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-900"><?= htmlspecialchars($l['username']) ?></td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-mono font-bold rounded text-[10px]"><?= htmlspecialchars($l['action']) ?></span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-500"><?= htmlspecialchars($l['ip_address'] ?? '127.0.0.1') ?></td>
                        <td class="py-3.5 px-4 text-right text-slate-700"><?= htmlspecialchars($l['details']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
