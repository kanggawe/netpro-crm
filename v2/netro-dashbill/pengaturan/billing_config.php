<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Otomatisasi Billing, Denda & Isolir";
$page_subtitle = "Konfigurasi siklus penagihan, toleransi keterlambatan (Grace Period), dan trigger isolir MikroTik.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

$dueDay = Setting::get('billing_due_day', '10');
$graceDays = Setting::get('billing_grace_days', '5');
$dendaAmount = Setting::get('billing_denda_amount', '25000');
$autoIsolir = Setting::get('billing_auto_isolir', '1');
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'saved'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-4xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Konfigurasi otomatisasi penagihan dan kebijakan denda berhasil diperbarui!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-4xl mx-auto">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-calendar-check text-emerald-600"></i> Kebijakan Siklus Penagihan & Isolir Pelanggan
            </h3>
            <p class="text-slate-400">Aturan pemotongan layanan secara otomatis oleh daemon sistem saat jatuh tempo terlewati.</p>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="save_settings">
            <input type="hidden" name="redirect" value="pengaturan/billing_config.php">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Tanggal Jatuh Tempo Bulanan</label>
                    <input type="number" name="billing_due_day" value="<?= htmlspecialchars($dueDay) ?>" min="1" max="28" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-slate-900">
                    <span class="text-[11px] text-slate-400 block mt-1">Setiap tanggal <?= $dueDay ?> pukul 23:59 WIB.</span>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Masa Tenggang Toleransi (Grace Period)</label>
                    <input type="number" name="billing_grace_days" value="<?= htmlspecialchars($graceDays) ?>" min="0" max="15" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-blue-600">
                    <span class="text-[11px] text-slate-400 block mt-1">Jumlah hari toleransi sebelum tindakan isolir aktif.</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Nominal Denda Keterlambatan (Rp)</label>
                    <input type="number" name="billing_denda_amount" value="<?= htmlspecialchars($dendaAmount) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-rose-600">
                    <span class="text-[11px] text-slate-400 block mt-1">Biaya administrasi flat ditambahkan ke tagihan berikutnya.</span>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Otomatisasi Isolir MikroTik</label>
                    <select name="billing_auto_isolir" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                        <option value="1" <?= $autoIsolir === '1' ? 'selected' : '' ?>>AKTIF (Pindahkan ke Pool Isolir Otomatis)</option>
                        <option value="0" <?= $autoIsolir === '0' ? 'selected' : '' ?>>NONAKTIF (Hanya Kirim Notifikasi Pengingat)</option>
                    </select>
                </div>
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 space-y-1">
                <strong class="block font-bold">⚠️ Mekanisme Redirect Halaman Isolir</strong>
                <p class="text-[11px] text-amber-800">Pelanggan yang terisolir akan otomatis diarahkan ke URL Landing Page Notifikasi Pembayaran (*http://isolir.netpro.co.id*) saat mencoba browsing internet.</p>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Kebijakan Billing & Denda
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
