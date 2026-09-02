<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Kebijakan Denda & Masa Tenggang (Grace Period)";
$page_subtitle = "Pengaturan denda keterlambatan pembayaran dan aturan isolir otomatis MikroTik.";
$active_menu = "m-billing";
require_once __DIR__ . '/../includes/header.php';
?>

<?php
$gracePeriod = Setting::get('grace_period', '0');
$dendaNominal = floatval(Setting::get('denda_nominal', 0));
$isolirDate = Setting::get('isolir_date', '-');
?>

<div class="space-y-6 text-xs max-w-4xl mx-auto">
    <!-- Top 3 Denda Settings -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Masa Tenggang (Grace Period)</span>
            <strong class="text-2xl font-bold text-slate-900"><?= htmlspecialchars($gracePeriod) ?> Hari</strong>
            <span class="text-slate-400 block">Tenggang waktu sebelum denda aktif</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Nominal Denda Keterlambatan</span>
            <strong class="text-2xl font-bold text-rose-600"><?= format_rupiah($dendaNominal) ?></strong>
            <span class="text-rose-600 font-bold block">Flat per invoice menunggak</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Batas Auto-Isolir MikroTik</span>
            <strong class="text-2xl font-bold text-amber-600"><?= is_numeric($isolirDate) ? 'Tanggal ' . htmlspecialchars($isolirDate) : htmlspecialchars($isolirDate) ?></strong>
            <span class="text-amber-600 font-bold block">Setiap bulan pukul 23:59 WIB</span>
        </div>
    </div>

    <!-- Denda Configuration Panel -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Konfigurasi Otomatisasi Penagihan & Denda
            </h3>
            <p class="text-slate-400">Aturan pemutusan sementara (isolir) dan pengenaan biaya tambahan administrasi keterlambatan.</p>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="update_settings">
            <input type="hidden" name="redirect" value="billing/denda.php">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Skema Denda</label>
                    <select name="denda_type" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                        <option value="flat" selected>Nominal Tetap (<?= format_rupiah($dendaNominal) ?> / Bulan)</option>
                        <option value="percent">Persentase (5% dari Nilai Tagihan)</option>
                        <option value="none">Tanpa Denda (Hanya Isolir Jaringan)</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Toleransi Hari (Grace Period)</label>
                    <input type="number" name="grace_period" value="<?= htmlspecialchars($gracePeriod) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono">
                </div>
            </div>

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 space-y-1">
                <strong class="block font-bold">⚠️ Mekanisme Isolir MikroTik & RADIUS</strong>
                <p class="text-[11px] text-amber-800">Pelanggan yang melewati batas tanggal jatuh tempo + grace period akan otomatis dialihkan ke IP Pool Isolir dengan halaman pemberitahuan pembayaran (*Landing Page Pembayaran Tagihan*).</p>
            </div>

            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-3 rounded-xl shadow-lg shadow-brand-950/30 transition">
                Simpan Perubahan Kebijakan Denda
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
