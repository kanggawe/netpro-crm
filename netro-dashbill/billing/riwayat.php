<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Riwayat Transaksi & Mutasi Tagihan";
$page_subtitle = "Daftar rekonsiliasi seluruh pembayaran tagihan yang telah diverifikasi lunas.";
$active_menu = "m-billing";
require_once __DIR__ . '/../includes/header.php';

$invoices = Invoice::all();
$paidInvoices = [];
$totalPaid = 0;
foreach ($invoices as $inv) {
    if (strtolower($inv['status'] ?? '') === 'lunas' || strtolower($inv['status'] ?? '') === 'paid') {
        $paidInvoices[] = $inv;
        $totalPaid += floatval($inv['total_amount'] ?? 0);
    }
}
$displayPaid = $totalPaid;
?>

<div class="space-y-6 text-xs">
    <!-- Top 3 Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Total Pembayaran Terverifikasi</span>
                <strong class="text-2xl font-bold text-emerald-600"><?= format_rupiah($displayPaid) ?></strong>
                <span class="text-emerald-600 font-bold block mt-0.5">✓ 100% Masuk ke Rekening Kas</span>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-receipt"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Total Transaksi Lunas</span>
                <strong class="text-2xl font-bold text-blue-600"><?= count($paidInvoices) ?> Transaksi</strong>
                <span class="text-slate-400 block mt-0.5">Bulan <?= date('F Y') ?></span>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-file-circle-check"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Status Gateway Real-time</span>
                <strong class="text-2xl font-bold text-purple-600">CONNECTED</strong>
                <span class="text-purple-600 font-bold block mt-0.5">BCA, Mandiri, QRIS Auto-callback</span>
            </div>
            <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-tower-broadcast"></i></div>
        </div>
    </div>

    <!-- Paid Invoices Log Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Riwayat Pembayaran Tagihan Lunas</h3>
                <p class="text-slate-400">Seluruh penerimaan dana pembayaran yang telah disinkronkan ke buku kas.</p>
            </div>
            <button onclick="window.print()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg">
                <i class="fa-solid fa-print"></i> Cetak Riwayat
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">No. Invoice</th>
                        <th class="py-3 px-4">ID Pelanggan</th>
                        <th class="py-3 px-4">Periode</th>
                        <th class="py-3 px-4 font-mono text-right">DPP (Rp)</th>
                        <th class="py-3 px-4 font-mono text-right">PPN 11%</th>
                        <th class="py-3 px-4 font-mono text-right">Total Dibayar</th>
                        <th class="py-3 px-4">Waktu Pembayaran</th>
                        <th class="py-3 px-4 text-right">Kwitansi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($paidInvoices)): ?>
                    <tr class="border-b border-slate-50">
                        <td colspan="8" class="py-8 text-center text-slate-400 font-medium">Belum ada data riwayat pembayaran lunas.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($paidInvoices as $p): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($p['invoice_no']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800">CUST-<?= str_pad($p['customer_id'], 3, '0', STR_PAD_LEFT) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($p['billing_period']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-right text-slate-600"><?= format_rupiah($p['dpp_amount']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-right text-emerald-600"><?= format_rupiah($p['ppn_amount']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900 text-right"><?= format_rupiah($p['total_amount']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-500"><?= htmlspecialchars($p['paid_date'] ?? date('Y-m-d H:i')) ?></td>
                        <td class="py-3.5 px-4 text-right">
                            <a href="<?= base_url('billing/cetak_invoice.php?id=' . $p['id']) ?>" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                <i class="fa-solid fa-file-pdf text-red-500"></i> Kwitansi.pdf
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
