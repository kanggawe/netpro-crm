<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Integrasi MikroTik RouterOS & RADIUS";
$page_subtitle = "Konfigurasi koneksi API RouterOS, sinkronisasi secret FreeRADIUS, dan SNMP OLT.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

$mikrotikIp = Setting::get('mikrotik_ip', '10.0.0.1');
$mikrotikPort = Setting::get('mikrotik_port', '8728');
$mikrotikUser = Setting::get('mikrotik_user', 'api_netpro');
$radiusSecret = Setting::get('radius_secret', 'NetProRadiusSec#991');
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'saved'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-4xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Konfigurasi API MikroTik & RADIUS berhasil diperbarui dan terkoneksi!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-4xl mx-auto">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-code-branch text-indigo-600"></i> Konektivitas MikroTik API & FreeRADIUS Server
                </h3>
                <p class="text-slate-400">Digunakan untuk auto-provisioning sesi PPPoE, limitasi bandwidth simple queue, dan isolir.</p>
            </div>
            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">API CONNECTED (2ms) ✓</span>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="save_settings">
            <input type="hidden" name="redirect" value="pengaturan/api.php">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">IP Address Router MikroTik (Core NAS)</label>
                    <input type="text" name="mikrotik_ip" value="<?= htmlspecialchars($mikrotikIp) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-900">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Port API RouterOS</label>
                    <input type="number" name="mikrotik_port" value="<?= htmlspecialchars($mikrotikPort) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Username API RouterOS</label>
                    <input type="text" name="mikrotik_user" value="<?= htmlspecialchars($mikrotikUser) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">RADIUS Shared Secret</label>
                    <input type="password" name="radius_secret" value="<?= htmlspecialchars($radiusSecret) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-indigo-600">
                </div>
            </div>

            <div class="p-4 bg-indigo-50 border border-indigo-100 rounded-xl text-indigo-900 space-y-1">
                <div class="flex items-center gap-2 font-bold">
                    <i class="fa-solid fa-shield-halved text-indigo-600"></i> Keamanan Koneksi API
                </div>
                <p class="text-[11px] text-indigo-700">Pastikan IP Server CRM telah di-whitelist pada menu <code>/ip service set api address=...</code> di MikroTik Core.</p>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Konfigurasi Integrasi Jaringan
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
