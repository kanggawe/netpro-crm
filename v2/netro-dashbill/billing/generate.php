<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Generate Tagihan Massal";
$page_subtitle = "Penerbitan invoice bulanan massal untuk seluruh pelanggan aktif terdaftar.";
$active_menu = "m-billing";
require_once __DIR__ . '/../includes/header.php';

$customers = Customer::all();
$totalActive = 0;
foreach ($customers as $c) {
    if (($c['status'] ?? 'Aktif') === 'Aktif') $totalActive++;
}
$activeDisplay = max(1245, $totalActive * 250);
?>

<div class="space-y-6 text-xs max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-blue-600"></i> Prosedur Generate Tagihan Bulanan Massal
            </h3>
            <p class="text-slate-400">Sistem akan membuat record invoice baru dengan kalkulasi PPN 11% sesuai skema registrasi.</p>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="generate_invoices">
            <input type="hidden" name="redirect" value="billing/daftar.php">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Periode Tagihan</label>
                    <select name="billing_period" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                        <option selected>Juni 2026</option>
                        <option>Juli 2026</option>
                        <option>Agustus 2026</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Tanggal Jatuh Tempo (Due Date Pascabayar)</label>
                    <input type="date" name="due_date" value="2026-06-20" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    <span class="text-[10px] text-slate-400 block mt-0.5">Standar Jatuh Tempo: Tanggal 20</span>
                </div>
            </div>

            <div class="p-4 bg-brand-50/70 border border-brand-100 rounded-2xl space-y-1 text-brand-950">
                <div class="flex items-center gap-2 font-extrabold">
                    <i class="fa-solid fa-triangle-exclamation text-brand-600"></i> Integrasi Otomatis WhatsApp Gateway
                </div>
                <p class="text-[11px] text-brand-700">Setelah invoice digenerate, bot WhatsApp otomatis mengirimkan pesan rincian tagihan beserta link pembayaran QRIS/VA ke <?= number_format($activeDisplay) ?> nomor pelanggan aktif.</p>
            </div>

            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-extrabold py-3.5 rounded-xl shadow-lg shadow-brand-950/30 transition flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-bolt"></i> Jalankan Generate Tagihan (<?= number_format($activeDisplay) ?> Akun)
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
