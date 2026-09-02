<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "WhatsApp & Server Notifikasi";
$page_subtitle = "Integrasi bot WhatsApp otomatis untuk pengiriman invoice, reminder jatuh tempo, dan BAST.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

$waVendor = Setting::get('wa_vendor', 'fonnte');
$waToken = Setting::get('wa_token', 'FONNTE-API-TOKEN-9912088214');
$waSender = Setting::get('wa_sender', '081298765432');
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'saved'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-4xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Konfigurasi WhatsApp Gateway berhasil disimpan!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-4xl mx-auto">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-600"></i> Integrasi Bot WhatsApp Gateway
                </h3>
                <p class="text-slate-400">Pemicu otomatisasi pesan tagihan, kwitansi lunas, dan tiket gangguan.</p>
            </div>
            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">DEVICE CONNECTED ✓</span>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="save_settings">
            <input type="hidden" name="redirect" value="pengaturan/wa_gateway.php">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Provider WhatsApp Gateway</label>
                    <select name="wa_vendor" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                        <option value="fonnte" <?= $waVendor === 'fonnte' ? 'selected' : '' ?>>Fonnte.com API Service</option>
                        <option value="wablas" <?= $waVendor === 'wablas' ? 'selected' : '' ?>>Wablas Official Gateway</option>
                        <option value="whacenter" <?= $waVendor === 'whacenter' ? 'selected' : '' ?>>WhaCenter Multi-device</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Nomor Pengirim (Sender Number)</label>
                    <input type="text" name="wa_sender" value="<?= htmlspecialchars($waSender) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-900">
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">API Token / Secret Key</label>
                <input type="password" name="wa_token" value="<?= htmlspecialchars($waToken) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-emerald-600">
            </div>

            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                <span class="text-slate-700 font-bold block">Format Template Notifikasi Tagihan Bulanan:</span>
                <p class="text-slate-600 font-mono text-[11px] bg-white p-3 rounded-lg border border-slate-200 leading-relaxed">
                    Halo *{nama_pelanggan}*, tagihan internet *{nama_paket}* periode *{periode}* sebesar *{total_tagihan}* telah terbit. Jatuh tempo: *{due_date}*. Bayar instan via QRIS: {link_qris}. Terima kasih.
                </p>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Konfigurasi WhatsApp
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
