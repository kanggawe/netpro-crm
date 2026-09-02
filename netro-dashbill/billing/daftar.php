<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Daftar Tagihan & Skema PPN";
$page_subtitle = "Tabel invoice tagihan dengan rincian skema PPN Include/Exclude saat registrasi.";
$active_menu = "m-billing";
require_once __DIR__ . '/../includes/header.php';

$invoices = Invoice::all();
$filterCustId = intval($_GET['customer_id'] ?? ($_GET['id'] ?? 0));
$filterCustomer = $filterCustId > 0 ? Customer::find($filterCustId) : null;
if ($filterCustId > 0) {
    $filteredInvoices = [];
    foreach ($invoices as $inv) {
        if (($inv['customer_id'] ?? 0) == $filterCustId) {
            $filteredInvoices[] = $inv;
        }
    }
    $invoices = $filteredInvoices;
}
$msg = $_GET['msg'] ?? '';
?>

<?php if ($filterCustomer): ?>
    <div class="p-3.5 bg-blue-50 text-blue-900 border border-blue-200 rounded-xl flex items-center justify-between text-xs font-bold">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-filter text-blue-600"></i>
            <span>Menampilkan tagihan khusus pelanggan: <strong><?= htmlspecialchars($filterCustomer['name']) ?></strong> (<?= htmlspecialchars($filterCustomer['cid'] ?? '') ?>)</span>
        </div>
        <a href="<?= base_url('billing/daftar.php') ?>" class="px-2.5 py-1 bg-white border border-blue-300 text-blue-700 hover:bg-blue-100 rounded-lg text-[11px] font-semibold">
            ✕ Tampilkan Semua Tagihan
        </a>
    </div>
<?php endif; ?>

<?php if ($msg === 'paid'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Pembayaran berhasil diproses! Status invoice telah diubah menjadi LUNAS.
    </div>
<?php elseif ($msg === 'generated'): ?>
    <div class="p-4 bg-blue-50 text-blue-800 border border-blue-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-file-invoice-dollar text-blue-600 text-sm"></i>
        Berhasil men-generate <?= htmlspecialchars($_GET['count'] ?? '0') ?> invoice tagihan bulanan baru!
    </div>
<?php elseif ($msg === 'deleted_invoice'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-trash-can text-rose-600 text-sm"></i>
        Invoice tagihan telah berhasil dihapus/dibatalkan.
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4 text-xs">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
        <div>
            <h3 class="font-bold text-slate-900 text-sm">Daftar Tagihan & Status Pembayaran</h3>
            <p class="text-slate-400">Total <?= count($invoices) ?> Invoice ditampilkan di sistem.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="cetak_tagihan.php" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-3.5 py-2 rounded-lg shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak / Export PDF
            </a>
            <form action="<?= base_url('api/handler.php') ?>" method="POST">
                <input type="hidden" name="action" value="generate_invoices">
                <input type="hidden" name="redirect" value="billing/daftar.php">
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-rotate text-xs"></i> Generate Tagihan Massal
                </button>
            </form>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                    <th class="py-3 px-4">No. Invoice</th>
                    <th class="py-3 px-4">Nama Pelanggan</th>
                    <th class="py-3 px-4">Tipe & Periode</th>
                    <th class="py-3 px-4 font-mono text-right">DPP (Rp)</th>
                    <th class="py-3 px-4 font-mono text-right">PPN 11%</th>
                    <th class="py-3 px-4 font-mono text-right">Total Tagihan</th>
                    <th class="py-3 px-4">Status Tagihan</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoices as $inv): 
                    $bType = strtolower($inv['billing_type'] ?? ($inv['cust_billing_type'] ?? 'postpaid'));
                    $isPaid = (strtolower($inv['status']) === 'lunas' || strtolower($inv['status']) === 'paid');
                ?>
                <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                    <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                        <a href="<?= base_url('billing/invoice.php?id=' . $inv['id']) ?>" class="hover:underline"><?= htmlspecialchars($inv['invoice_no']) ?></a>
                    </td>
                    <td class="py-3.5 px-4 font-bold text-slate-800">
                        <a href="<?= base_url('crm/detail.php?id=' . $inv['customer_id']) ?>" class="hover:text-blue-600 hover:underline">
                            <?= htmlspecialchars($inv['customer_name'] ?? 'Pelanggan') ?>
                        </a>
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="block text-slate-700 font-medium"><?= htmlspecialchars($inv['billing_period']) ?></span>
                        <?php if ($bType === 'prepaid'): ?>
                            <span class="px-1.5 py-0.2 bg-purple-50 text-purple-700 border border-purple-200 font-bold rounded text-[8.5px] inline-flex items-center gap-1">
                                <i class="fa-solid fa-bolt text-[7px]"></i> PRABAYAR
                            </span>
                        <?php else: ?>
                            <span class="px-1.5 py-0.2 bg-blue-50 text-blue-700 border border-blue-200 font-bold rounded text-[8.5px] inline-flex items-center gap-1">
                                <i class="fa-solid fa-calendar text-[7px]"></i> PASCABAYAR
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3.5 px-4 font-mono text-right text-slate-600"><?= format_rupiah($inv['dpp_amount']) ?></td>
                    <td class="py-3.5 px-4 font-mono text-right text-emerald-600"><?= format_rupiah($inv['ppn_amount']) ?></td>
                    <td class="py-3.5 px-4 font-mono font-bold text-slate-900 text-right"><?= format_rupiah($inv['total_amount']) ?></td>
                    <td class="py-3.5 px-4">
                        <?php if ($isPaid): ?>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">LUNAS</span>
                        <?php else: ?>
                            <span class="px-2.5 py-1 bg-red-50 text-red-700 font-bold rounded-full text-[10px]">BELUM BAYAR</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                        <?php if (!$isPaid): ?>
                            <a href="<?= base_url('billing/pembayaran.php?invoice_id=' . $inv['id']) ?>" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded text-[10px] shadow-xs inline-flex items-center gap-1">
                                <i class="fa-solid fa-cash-register text-[9px]"></i> Bayar Kasir
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('billing/kwitansi.php?id=' . $inv['id']) ?>" target="_blank" class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded text-[10px] inline-flex items-center gap-1 border border-emerald-200">
                                <i class="fa-solid fa-receipt text-[9px]"></i> Kwitansi
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('billing/cetak_invoice.php?id=' . $inv['id']) ?>" target="_blank" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded text-[10px] inline-flex items-center gap-1">
                            <i class="fa-solid fa-print text-[9px]"></i> PDF
                        </a>
                        <form action="<?= base_url('api/handler.php') ?>" method="POST" onsubmit="return confirm('Batalkan/Hapus invoice ini?')" class="inline">
                            <input type="hidden" name="action" value="delete_invoice">
                            <input type="hidden" name="id" value="<?= $inv['id'] ?>">
                            <input type="hidden" name="redirect" value="billing/daftar.php">
                            <button type="submit" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded text-[10px]">
                                <i class="fa-solid fa-trash-can text-[9px]"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
