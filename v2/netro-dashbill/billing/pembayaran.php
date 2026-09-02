<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Input & Verifikasi Pembayaran Tagihan";
$page_subtitle = "Pencatatan pembayaran manual kasir, konfirmasi mutasi bank & payment gateway.";
$active_menu = "m-billing";
$breadcrumbs = [
    'Billing & Tagihan' => 'billing/daftar.php',
    'Input Pembayaran Kasir' => ''
];
require_once __DIR__ . '/../includes/header.php';

$invoices = Invoice::all();
$selectedInvId = intval($_GET['invoice_id'] ?? ($_GET['id'] ?? 0));
$unpaidInvoices = [];
foreach ($invoices as $inv) {
    if (strtolower($inv['status'] ?? 'unpaid') !== 'lunas' && strtolower($inv['status'] ?? 'unpaid') !== 'paid') {
        $unpaidInvoices[] = $inv;
    }
}
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'paid' || $msg === 'invoice_paid'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center justify-between text-xs font-bold max-w-xl mx-auto">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>Pembayaran berhasil dikonfirmasi & jurnal umum otomatis diterbitkan!</span>
        </div>
        <a href="<?= base_url('billing/daftar.php') ?>" class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-[11px]">
            Lihat Daftar Tagihan →
        </a>
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-xl mx-auto">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-money-bill-transfer text-emerald-600"></i> Form Penerimaan Kasir & Mutasi Bank
            </h3>
            <p class="text-slate-400">Verifikasi pelunasan invoice, pencatatan jurnal akuntansi, dan re-aktivasi status isolir pelanggan.</p>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-3">
            <input type="hidden" name="action" value="pay_invoice">
            <input type="hidden" name="redirect" value="billing/pembayaran.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Pilih Nomor Invoice / Pelanggan</label>
                <select name="id" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-800">
                    <?php if (empty($unpaidInvoices)): ?>
                        <?php foreach ($invoices as $inv): ?>
                            <option value="<?= $inv['id'] ?>" <?= ($inv['id'] == $selectedInvId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($inv['invoice_no']) ?> - <?= htmlspecialchars($inv['customer_name'] ?? '') ?> - <?= format_rupiah($inv['total_amount']) ?> (<?= htmlspecialchars($inv['status']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($unpaidInvoices as $inv): ?>
                            <option value="<?= $inv['id'] ?>" <?= ($inv['id'] == $selectedInvId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($inv['invoice_no']) ?> - <?= htmlspecialchars($inv['customer_name'] ?? '') ?> - <?= format_rupiah($inv['total_amount']) ?> (UNPAID)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Metode Pembayaran</label>
                    <select name="payment_method" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                        <option>Kasir Tunai (Cash HQ)</option>
                        <option selected>Transfer Bank BCA</option>
                        <option>Transfer Bank Mandiri</option>
                        <option>QRIS Dinamis</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">No. Referensi / Mutasi Bank</label>
                    <input type="text" name="ref_no" placeholder="Contoh: TRX-BCA-88291" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono">
                </div>
            </div>

            <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-900 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span class="text-[11px] font-medium">Sistem otomatis mencatat penerimaan ke menu <strong>Arus Kas & Bank</strong>.</span>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl shadow transition">
                Konfirmasi Pembayaran & Cetak Kwitansi
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
