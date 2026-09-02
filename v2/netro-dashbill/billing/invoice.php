<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Cetak Invoice & Faktur Pajak";
$page_subtitle = "Salinan tagihan resmi dengan rincian DPP dan PPN 11% Include/Exclude.";
$active_menu = "m-billing";
require_once __DIR__ . '/../includes/header.php';

$invId = 0;
if (!empty($_GET['token'])) {
    $invId = intval(url_decrypt($_GET['token'], 'invoice') ?: url_decrypt($_GET['token']));
} elseif (!empty($_GET['ref'])) {
    $invId = intval(url_decrypt($_GET['ref'], 'invoice') ?: url_decrypt($_GET['ref']));
} elseif (!empty($_GET['mid'])) {
    $invId = unmask_id($_GET['mid']);
} elseif (!empty($_GET['id']) || !empty($_GET['invoice_id'])) {
    $raw = $_GET['id'] ?? $_GET['invoice_id'];
    $invId = is_numeric($raw) ? intval($raw) : (unmask_id($raw) ?: intval(url_decrypt($raw, 'invoice')));
}
$invoice = $invId > 0 ? Invoice::find($invId) : null;
if (!$invoice) {
    $allInvoices = Invoice::all();
    $invoice = !empty($allInvoices) ? $allInvoices[0] : null;
}
$customer = ($invoice && !empty($invoice['customer_id'])) ? Customer::find($invoice['customer_id']) : null;

$companyName = Setting::get('company_name', 'PT NETPRO TELEKOMUNIKASI INDONESIA');
$companyNpwp = Setting::get('company_npwp', '01.234.567.8-901.000');
$companyAddress = Setting::get('company_address', 'Gedung Cyber Lt. 5, Jl. Kuningan Barat No. 8, Jakarta Selatan 12710');
$isPaid = (($invoice['status'] ?? 'paid') === 'paid');
?>

<style>
/* Watermark Stamp Style for Paid/Unpaid status */
.invoice-watermark {
    position: absolute !important;
    top: 52% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) rotate(-12deg) !important;
    z-index: 9999 !important;
    pointer-events: none !important;
    font-size: 4rem !important;
    font-weight: 900 !important;
    text-transform: uppercase !important;
    letter-spacing: 6px !important;
    border: 4px solid !important;
    padding: 30px 24px !important;
    border-radius: 12px !important;
    opacity: 0.35 !important;
    white-space: nowrap !important;
    user-select: none !important;
}
.watermark-paid {
    color: rgba(16, 185, 129, 0.35) !important;
    border-color: rgba(16, 185, 129, 0.35) !important;
}
.watermark-unpaid {
    color: rgba(239, 68, 68, 0.35) !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
}

@media print {
    .invoice-watermark {
        visibility: visible !important;
    }
    @page {
        /* size: landscape; */
        margin: 5mm 8mm 5mm 8mm;
    }
    
    body * {
        visibility: hidden !important;
    }
    
    .invoice-paper,
    .invoice-paper * {
        visibility: visible !important;
    }
    
    .invoice-paper {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        background: #ffffff !important;
        page-break-before: avoid !important;
        page-break-after: avoid !important;
        page-break-inside: avoid !important;
        break-before: avoid !important;
        break-after: avoid !important;
        break-inside: avoid !important;
    }

    /* Override global .flex { display: block } dari style.css */
    /* Semua flex layout di dalam invoice harus tetap flex agar kiri-kanan rapi */
    .invoice-paper .flex {
        display: flex !important;
    }
    .invoice-paper .justify-between {
        justify-content: space-between !important;
    }
    .invoice-paper .justify-center {
        justify-content: center !important;
    }
    .invoice-paper .items-start {
        align-items: flex-start !important;
    }
    .invoice-paper .items-end {
        align-items: flex-end !important;
    }
    .invoice-paper .items-center {
        align-items: center !important;
    }
    .invoice-paper .gap-2 {
        gap: 0.5rem !important;
    }
    .invoice-paper .gap-4 {
        gap: 1rem !important;
    }
    .invoice-paper .text-right {
        text-align: right !important;
    }
    .invoice-paper .text-center {
        text-align: center !important;
    }
    .invoice-paper .text-left {
        text-align: left !important;
    }
    
    /* Ruang vertikal super rapat untuk format Landscape */
    .invoice-paper.space-y-6 > * + *,
    .invoice-paper .space-y-6 > * + * {
        margin-top: 0.35rem !important;
    }
    .invoice-paper .space-y-1 > * + * {
        margin-top: 0.1rem !important;
    }
    .invoice-paper .pb-5 {
        padding-bottom: 0.35rem !important;
    }
    .invoice-paper .pb-4 {
        padding-bottom: 0.35rem !important;
    }
    .invoice-paper .pt-6 {
        padding-top: 0.35rem !important;
    }
    .invoice-paper .mt-6 {
        margin-top: 0.35rem !important;
    }
    .invoice-paper .pt-2 {
        padding-top: 0.15rem !important;
    }
    .invoice-paper .pt-1.5 {
        padding-top: 0.15rem !important;
    }
    .invoice-paper table {
        margin-top: 0.2rem !important;
    }
    .invoice-paper td, .invoice-paper th {
        padding-top: 0.3rem !important;
        padding-bottom: 0.3rem !important;
    }
    
    .invoice-paper .grid {
        display: flex !important;
        justify-content: space-between !important;
    }
    .invoice-paper .w-72 {
        width: 18rem !important;
    }

    .invoice-paper .print-only {
        display: block !important;
    }

    .no-print,
    .no-print * {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>

<!-- Invoice Container with invoice-paper class for clean single-page printing -->
<div class="invoice-paper bg-white p-8 sm:p-10 rounded-2xl border border-slate-200 shadow-lg max-w-3xl mx-auto space-y-6 text-xs text-slate-800" style="position: relative !important;">
    <!-- Top Action Bar (Hanya tampil di layar, tersembunyi saat cetak) -->
    <div class="no-print flex justify-between items-center pb-4 border-b border-slate-100">
        <a href="<?= base_url('billing/daftar.php') ?>" class="text-blue-600 font-bold hover:underline flex items-center gap-1.5 text-xs">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Tagihan
        </a>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('billing/cetak_invoice.php?id=' . ($invoice['id'] ?? 1)) ?>" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl shadow-md transition flex items-center gap-2 text-xs">
                <i class="fa-solid fa-file-arrow-down"></i> Format Cetak Resmi A4 / Unduh PDF
            </a>
            <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white font-bold px-3.5 py-2 rounded-xl shadow transition flex items-center gap-1.5 text-xs">
                <i class="fa-solid fa-print"></i> Cetak Cepat
            </button>
        </div>
    </div>

    <!-- Official Invoice Header -->
    <div class="flex justify-between items-start border-b-2 border-slate-900 pb-5" style="display:flex!important;justify-content:space-between!important;">
        <div class="space-y-1" style="flex:0 0 60%!important;border:none!important;">
            <!-- Screen-only Header with Icon -->
            <div class="no-print flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-brand-600 to-rose-600 text-white flex items-center justify-center font-black text-sm shadow">
                    <i class="fa-solid fa-network-wired"></i>
                </div>
                <h1 class="font-black text-slate-900 text-lg uppercase tracking-wide"><?= htmlspecialchars($companyName) ?></h1>
            </div>
            <!-- Print-only Header (Simple Left-Aligned Text) -->
            <h1 class="print-only hidden font-black text-slate-900 uppercase tracking-wide" style="font-size:16px!important;line-height:1.3!important;text-align:left!important;margin:0 0 4px 0!important;display:none;"><?= htmlspecialchars($companyName) ?></h1>
            <p class="text-slate-600 font-mono text-[11px]">NPWP / NPPKP: <strong><?= htmlspecialchars($companyNpwp) ?></strong></p>
            <p class="text-slate-500 text-[11px] leading-relaxed"><?= htmlspecialchars($companyAddress) ?></p>
            <p class="text-slate-400 text-[10px]">Izin Kominfo ISP: 0220108921882 | Call Center: (021) 5088-9900</p>
        </div>

        <div class="text-right space-y-1" style="flex:0 0 40%!important;border:none!important;text-align:right!important;">
            <h2 class="font-black text-brand-600 text-lg tracking-wider uppercase" style="font-size:15px!important;">INVOICE TAGIHAN</h2>
            <!-- <div class="pt-1">
                <?php //if ($isPaid): ?>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 border border-emerald-300 font-black rounded-lg text-xs uppercase tracking-wider inline-block">
                        ✓ LUNAS (PAID)
                    </span>
                <?php //else: ?>
                    <span class="px-3 py-1 bg-rose-100 text-rose-800 border border-rose-300 font-black rounded-lg text-xs uppercase tracking-wider inline-block">
                        BELUM DIBAYAR (UNPAID)
                    </span>
                <?php //endif; ?>
            </div> -->
            <p class="font-mono font-bold text-slate-900 text-sm mt-1"><?= htmlspecialchars($invoice['invoice_no'] ?? 'INV-2026-06-9901') ?></p>
            <p class="text-slate-500 text-[10px] font-mono">Periode: <strong><?= htmlspecialchars($invoice['billing_period'] ?? 'Juni 2026') ?></strong></p>
            <p class="text-slate-400 text-[10px] font-mono">Tanggal Terbit: <?= date('d/m/Y') ?></p>
        </div>
    </div>

    <!-- Customer Details & Tax Scheme Box -->
    <div class="grid grid-cols-2 gap-4 bg-slate-50/80 p-4 rounded-xl border border-slate-200" style="display: flex !important; justify-content: space-between !important;">
        <div class="space-y-0.5" style="flex: 1 !important; border: none !important;">
            <span class="text-slate-400 block text-[9px] uppercase font-bold tracking-wider">Ditujukan Kepada Pelanggan:</span>
            <strong class="font-extrabold text-slate-900 text-sm block"><?= htmlspecialchars($invoice['customer_name'] ?? 'Budi Wijaya') ?></strong>
            <p class="text-blue-600 font-mono font-bold text-xs">CID: <?= htmlspecialchars($invoice['cid'] ?? 'CID-991201') ?></p>
            <p class="text-slate-600 text-[11px] leading-tight"><?= htmlspecialchars($invoice['customer_address'] ?? 'Jl. Jatiwaringin Raya No. 12, Bekasi') ?></p>
        </div>
        <div class="text-right space-y-1" style="flex: 1 !important; border: none !important;">
            <span class="text-slate-400 block text-[9px] uppercase font-bold tracking-wider">Skema Perpajakan:</span>
            <span class="px-2.5 py-0.5 bg-blue-100 text-blue-900 border border-blue-200 font-bold rounded text-[10px] inline-block uppercase">
                MODE: <?= htmlspecialchars($invoice['ppn_mode'] ?? 'include') ?> PPN (11%)
            </span>
            <p class="text-slate-500 font-mono text-[10px]">No. Seri Faktur: <strong>010.000-26.<?= str_pad($invoice['id'] ?? 1, 7, '0', STR_PAD_LEFT) ?></strong></p>
            <p class="text-slate-500 font-mono text-[10px]">Jatuh Tempo: <?= date('20/m/Y') ?></p>
        </div>
    </div>

    <!-- Item Breakdown Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-100 text-slate-700 font-bold border-y border-slate-300">
                    <th class="py-2.5 px-3">No</th>
                    <th class="py-2.5 px-3">Deskripsi Layanan Internet</th>
                    <th class="py-2.5 px-3">Periode</th>
                    <th class="py-2.5 px-3 text-right">Dasar Pengenaan Pajak (DPP)</th>
                    <th class="py-2.5 px-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-slate-200">
                    <td class="py-3 px-3 font-mono text-center">1</td>
                    <td class="py-3 px-3">
                        <strong class="text-slate-900 block text-xs"><?= htmlspecialchars($invoice['package_name'] ?? 'Home Premium 50M') ?></strong>
                        <span class="text-[10px] text-slate-500">Sewa Bandwidth Internet Broadband FTTH Unlimited Fiber Optic</span>
                    </td>
                    <td class="py-3 px-3 text-slate-600 font-mono"><?= htmlspecialchars($invoice['billing_period'] ?? 'Juni 2026') ?></td>
                    <td class="py-3 px-3 text-right font-mono font-medium"><?= format_rupiah($invoice['dpp_amount'] ?? 225225) ?></td>
                    <td class="py-3 px-3 text-right font-bold text-slate-900 font-mono"><?= format_rupiah($invoice['dpp_amount'] ?? 225225) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Calculation Breakdown & Payment QR Validation -->
    <div class="flex justify-between items-end pt-2" style="display: flex !important; justify-content: space-between !important;">
        <!-- Left: Payment Instructions & Validation Notice -->
        <div class="space-y-1 text-[11px] text-slate-500" style="max-width: 320px;">
            <p class="font-bold text-slate-800">Catatan & Validasi Pembayaran:</p>
            <p class="text-[10px] leading-relaxed">
                Pembayaran via Virtual Account Bank BCA, Mandiri, atau QRIS otomatis terverifikasi oleh payment gateway resmi NETPRO.
            </p>
            <div class="pt-2">
                <span class="px-2 py-0.5 bg-slate-100 rounded border border-slate-200 font-mono text-[9px] text-slate-600 font-bold" style="border:1px solid #cbd5e1!important;">
                    VALIDATED BY SYSTEM: <?= date('Ymd-His') ?>
                </span>
            </div>
        </div>

        <!-- Right: Tax & Final Calculation -->
        <div class="w-72 space-y-1.5 border-t border-slate-300 pt-2 text-xs">
            <div class="flex justify-between text-slate-600">
                <span>Dasar Pengenaan Pajak (DPP):</span>
                <span class="font-mono font-bold text-slate-800"><?= format_rupiah($invoice['dpp_amount'] ?? 225225) ?></span>
            </div>
            <div class="flex justify-between text-blue-600">
                <span>PPN 11% (<?= ucfirst($invoice['ppn_mode'] ?? 'include') ?>):</span>
                <span class="font-mono font-bold"><?= format_rupiah($invoice['ppn_amount'] ?? 24775) ?></span>
            </div>
            <div class="flex justify-between text-slate-900 font-extrabold text-sm border-t-2 border-slate-900 pt-2">
                <span>TOTAL BAYAR FINAL:</span>
                <span class="font-mono text-emerald-600 text-base"><?= format_rupiah($invoice['total_amount'] ?? 250000) ?></span>
            </div>
        </div>
    </div>

    <!-- Authorized Sign-Off for Invoice Document -->
    <div class="pt-6 border-t border-slate-200 mt-6" style="border-top:1px solid #cbd5e1!important;">
        <!-- Row 1: QR & Barcode (Left) and Signature Date/Role (Right) -->
        <div style="display:block!important;clear:both!important;width:100%!important;margin-bottom:10px!important;border:none!important;">
            <!-- Left: QR Code & Barcode -->
            <div style="float:left!important;display:flex!important;align-items:center!important;gap:12px!important;border:none!important;">
                <!-- QR Code -->
                <div style="border:none!important;display:block!important;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode(base_url('billing/invoice.php?id=' . ($invoice['id'] ?? 1))) ?>" alt="Verification QR" style="width:60px!important;height:60px!important;border:1px solid #cbd5e1!important;padding:2px!important;background:#ffffff!important;border-radius:4px!important;display:block!important;">
                </div>
                <!-- Barcode -->
                <div style="border:none!important;display:block!important;">
                    <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text=<?= urlencode($invoice['invoice_no'] ?? 'INV-2026-06-9901') ?>&scale=2&height=12&rotate=N&includetext=true" alt="Barcode" style="height:44px!important;width:150px!important;object-contain:fit!important;display:block!important;">
                </div>
            </div>

            <!-- Right: Signature Header -->
            <div style="float:right!important;width:220px!important;text-align:center!important;border:none!important;font-size:11px!important;color:#64748b!important;line-height:1.3!important;">
                Jakarta, <?= date('d F Y') ?><br><strong>Bagian Billing & Keuangan</strong>
            </div>
            
            <div style="clear:both!important;"></div>
        </div>

        <!-- Row 2: Legal notice (Left) and Signature Line (Right) -->
        <div style="display:block!important;clear:both!important;width:100%!important;border:none!important;">
            <!-- Left Column: Legal Notice -->
            <div class="text-left text-[10px] text-slate-400 font-mono" style="float:left!important;text-align:left!important;border:none!important;line-height:15px!important;margin:0!important;padding:0!important;">
                Lembar ini sah & diakui sebagai bukti tagihan dan faktur pajak resmi.
            </div>

            <!-- Right Column: Signature Footer -->
            <div style="float:right!important;width:220px!important;text-align:center!important;border:none!important;">
                <div style="height:15px!important;border:none!important;"></div> <!-- Signature Space -->
                <strong class="text-slate-900 font-bold block underline" style="font-size:11px!important;display:block!important;text-decoration:underline!important;">Finance & Billing Department</strong>
                <span class="text-[9px] text-slate-500 font-mono" style="font-size:9px!important;display:block!important;"><?= htmlspecialchars($companyName) ?></span>
            </div>
            
            <div style="clear:both!important;"></div>
        </div>
    </div>

    <!-- Watermark Stamp -->
    <?php if ($isPaid): ?>
        <div class="invoice-watermark watermark-paid">LUNAS / PAID</div>
    <?php else: ?>
        <div class="invoice-watermark watermark-unpaid">BELUM BAYAR</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
