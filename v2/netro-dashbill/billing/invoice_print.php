<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$invId = intval($_GET['id'] ?? 1);
$invoice = Invoice::find($invId) ?? Invoice::find(1);

$companyName = Setting::get('company_name', 'PT NETPRO TELEKOMUNIKASI INDONESIA');
$companyNpwp = Setting::get('company_npwp', '01.234.567.8-901.000');
$companyAddress = Setting::get('company_address', 'Gedung Cyber 2 Tower Lt. 18, Jl. H.R. Rasuna Said, Jakarta Selatan 12950');
$isPaid = (($invoice['status'] ?? 'paid') === 'paid');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice_<?= htmlspecialchars($invoice['invoice_no'] ?? 'INV-2026-06-9901') ?>_NETPRO</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- html2pdf.js CDN for 100% Vector PDF Generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        /* A4 Canvas Screen Simulation */
        .a4-page {
            width: 210mm;
            min-height: 297mm;
            padding: 18mm 20mm;
            margin: 20px auto;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            box-sizing: border-box;
            position: relative;
        }

        /* Official Print Stylesheet */
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
                visibility: hidden !important;
            }
            .a4-page {
                width: 100% !important;
                min-height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body class="py-6">

    <!-- Floating Top Control Bar (RedDash Theme) -->
    <div class="no-print fixed top-4 left-1/2 transform -translate-x-1/2 z-50 bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 backdrop-blur-md text-white px-6 py-3.5 rounded-2xl shadow-2xl border border-brand-900/40 flex items-center gap-4 text-xs">
        <div class="flex items-center gap-2 pr-3 border-r border-brand-900/50">
            <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
            <span class="font-bold text-white">Pratinjau Cetak Invoice Resmi (A4)</span>
        </div>
        <button onclick="window.print()" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2 rounded-xl shadow-lg shadow-brand-950/40 transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i> Cetak Dokumen
        </button>
        <button onclick="downloadPdf()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl shadow transition flex items-center gap-2">
            <i class="fa-solid fa-file-arrow-down"></i> Unduh PDF Asli
        </button>
        <button onclick="window.close(); history.back();" class="text-slate-400 hover:text-white font-bold px-2 py-1 transition">
            ✕ Tutup
        </button>
    </div>

    <!-- Standalone Pure A4 Invoice Document Canvas -->
    <article id="invoice-doc" class="a4-page text-slate-800 flex flex-col justify-between">
        
        <div class="space-y-6">
            <!-- 1. Enterprise ISP Header -->
            <header class="flex justify-between items-start border-b-2 border-slate-900 pb-5">
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-rose-600 text-white flex items-center justify-center font-black text-base shadow-sm">
                            <i class="fa-solid fa-network-wired text-sm"></i>
                        </div>
                        <div>
                            <h1 class="font-black text-slate-900 text-lg uppercase tracking-wide"><?= htmlspecialchars($companyName) ?></h1>
                            <span class="text-[10px] text-brand-600 font-bold block uppercase tracking-widest">ISP & FIBER BROADBAND NETWORK</span>
                        </div>
                    </div>
                    <p class="text-slate-600 font-mono text-[11px] pt-1">NPWP / NPPKP: <strong><?= htmlspecialchars($companyNpwp) ?></strong></p>
                    <p class="text-slate-500 text-[11px] max-w-sm leading-relaxed"><?= htmlspecialchars($companyAddress) ?></p>
                    <p class="text-slate-400 text-[10px]">SK Izin Kominfo No: 0220108921882 | APJII ID: 892-NETPRO | Call Center: (021) 5088-9900</p>
                </div>

                <div class="text-right space-y-1.5">
                    <h2 class="font-black text-blue-600 text-2xl tracking-wider uppercase">INVOICE</h2>
                    <div class="pt-0.5">
                        <?php if ($isPaid): ?>
                            <span class="px-3.5 py-1 bg-emerald-100 text-emerald-800 border-2 border-emerald-500 font-black rounded-lg text-xs uppercase tracking-wider inline-block">
                                ✓ LUNAS (PAID)
                            </span>
                        <?php else: ?>
                            <span class="px-3.5 py-1 bg-rose-100 text-rose-800 border-2 border-rose-500 font-black rounded-lg text-xs uppercase tracking-wider inline-block">
                                UNPAID (BELUM BAYAR)
                            </span>
                        <?php endif; ?>
                    </div>
                    <p class="font-mono font-bold text-slate-900 text-sm mt-1"><?= htmlspecialchars($invoice['invoice_no'] ?? 'INV-2026-06-9901') ?></p>
                    <p class="text-slate-500 text-[11px] font-mono">Periode: <strong><?= htmlspecialchars($invoice['billing_period'] ?? 'Juni 2026') ?></strong></p>
                    <p class="text-slate-400 text-[10px] font-mono">Tanggal Terbit: <?= date('d/m/Y') ?></p>
                </div>
            </header>

            <!-- 2. Customer Information & Tax Details (Grid 2 Kolom) -->
            <section class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="space-y-1">
                    <span class="text-slate-400 block text-[9px] uppercase font-bold tracking-wider">Ditujukan Kepada Pelanggan (Bill To):</span>
                    <strong class="font-extrabold text-slate-900 text-sm block"><?= htmlspecialchars($invoice['customer_name'] ?? 'Budi Wijaya') ?></strong>
                    <p class="text-blue-600 font-mono font-bold text-xs">Customer ID: <?= htmlspecialchars($invoice['cid'] ?? 'CID-991201') ?></p>
                    <p class="text-slate-600 text-[11px] leading-relaxed"><?= htmlspecialchars($invoice['customer_address'] ?? 'Jl. Jatiwaringin Raya No. 12, Pondok Gede, Bekasi') ?></p>
                    <p class="text-slate-500 text-[10px] font-mono">WhatsApp: 0812-3456-7890</p>
                </div>

                <div class="text-right space-y-1">
                    <span class="text-slate-400 block text-[9px] uppercase font-bold tracking-wider">Informasi Pajak & Jatuh Tempo:</span>
                    <span class="px-2.5 py-0.5 bg-blue-100 text-blue-900 border border-blue-200 font-bold rounded text-[10px] inline-block uppercase">
                        MODE: <?= htmlspecialchars($invoice['ppn_mode'] ?? 'include') ?> PPN (11%)
                    </span>
                    <p class="text-slate-600 font-mono text-[11px]">No. Seri Faktur: <strong>010.000-26.<?= str_pad($invoice['id'] ?? 1, 7, '0', STR_PAD_LEFT) ?></strong></p>
                    <p class="text-slate-600 font-mono text-[11px]">Jatuh Tempo: <strong>20/<?= date('m/Y') ?></strong></p>
                    <p class="text-slate-400 text-[10px]">Status Akun: AKTIF / NORMAL</p>
                </div>
            </section>

            <!-- 3. Line Items Table -->
            <section>
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-bold border-y-2 border-slate-300 text-xs">
                            <th class="py-2.5 px-3 text-center w-12">No</th>
                            <th class="py-2.5 px-3">Deskripsi Layanan & Bandwidth</th>
                            <th class="py-2.5 px-3 text-center">Periode Tagihan</th>
                            <th class="py-2.5 px-3 text-right">Dasar Pengenaan Pajak (DPP)</th>
                            <th class="py-2.5 px-3 text-right">Jumlah (Subtotal)</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        <tr class="border-b border-slate-200">
                            <td class="py-4 px-3 font-mono text-center text-slate-500">1</td>
                            <td class="py-4 px-3">
                                <strong class="text-slate-900 block text-xs"><?= htmlspecialchars($invoice['package_name'] ?? 'Home Premium 50M') ?></strong>
                                <span class="text-[11px] text-slate-500 block leading-tight">Langganan Dedicated Broadband Internet FTTH Fiber Optic Unlimited (Simetris 1:1)</span>
                            </td>
                            <td class="py-4 px-3 text-center text-slate-600 font-mono"><?= htmlspecialchars($invoice['billing_period'] ?? 'Juni 2026') ?></td>
                            <td class="py-4 px-3 text-right font-mono font-medium"><?= format_rupiah($invoice['dpp_amount'] ?? 225225) ?></td>
                            <td class="py-4 px-3 text-right font-bold text-slate-900 font-mono"><?= format_rupiah($invoice['dpp_amount'] ?? 225225) ?></td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- 4. Calculation Breakdown & Payment Channel (Grid 12 Kolom Bebas Tabrakan) -->
            <section class="grid grid-cols-12 gap-6 pt-4 items-start">
                <!-- Left Column (Span 7): Payment Instructions -->
                <div class="col-span-7 space-y-1.5 text-[10.5px] text-slate-600">
                    <strong class="text-slate-900 block font-bold text-xs">Instruksi Pembayaran Resmi:</strong>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-1 font-mono text-[10px]">
                        <p>• Bank BCA: <strong>872-009-1234</strong> a.n PT NETPRO</p>
                        <p>• Bank Mandiri: <strong>124-000-8889</strong> a.n PT NETPRO</p>
                        <p>• QRIS Nasional: Scan melalui GoPay, OVO, Dana, ShopeePay</p>
                    </div>
                    <p class="text-[9px] text-slate-400 leading-tight">
                        Pembayaran via Virtual Account & QRIS otomatis diverifikasi sistem secara real-time tanpa perlu konfirmasi manual.
                    </p>
                </div>

                <!-- Right Column (Span 5): Totals Box -->
                <div class="col-span-5 space-y-2 border-t-2 border-slate-300 pt-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Dasar Pajak (DPP):</span>
                        <span class="font-mono font-bold text-slate-800"><?= format_rupiah($invoice['dpp_amount'] ?? 225225) ?></span>
                    </div>
                    <div class="flex justify-between text-blue-600">
                        <span>PPN 11% (<?= ucfirst($invoice['ppn_mode'] ?? 'include') ?>):</span>
                        <span class="font-mono font-bold"><?= format_rupiah($invoice['ppn_amount'] ?? 24775) ?></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-900 font-extrabold text-sm border-t-2 border-slate-900 pt-2">
                        <span class="text-xs uppercase">TOTAL FINAL:</span>
                        <span class="font-mono text-emerald-600 text-base font-black"><?= format_rupiah($invoice['total_amount'] ?? 250000) ?></span>
                    </div>
                </div>
            </section>
        </div>

        <!-- 5. Legal Footer & Authorized Stamp -->
        <footer class="pt-6 border-t border-slate-200 mt-6">
            <div class="grid grid-cols-12 gap-6 items-end">
                <div class="col-span-7 space-y-1.5 text-left">
                    <div class="p-2 bg-slate-50 border border-slate-200 rounded-lg inline-block font-mono text-[9px] text-slate-500">
                        SECURITY TOKEN: <?= strtoupper(md5(($invoice['invoice_no'] ?? 'INV') . 'NETPRO_AUTH_2026')) ?>
                    </div>
                    <p class="text-[9px] text-slate-400 font-mono leading-relaxed">
                        Dokumen ini adalah salinan faktur tagihan dan perpajakan sah yang diterbitkan otomatis oleh sistem billing NETPRO.
                    </p>
                </div>

                <div class="col-span-5 text-center">
                    <span class="text-slate-500 block mb-10 text-[10.5px]">Jakarta, <?= date('d F Y') ?><br><strong>Finance & Billing Department</strong></span>
                    <strong class="text-slate-900 font-bold block underline text-xs">PT NETPRO TELEKOMUNIKASI INDONESIA</strong>
                    <span class="text-[9px] text-slate-400 font-mono block">Authorized Billing Gateway</span>
                </div>
            </div>
        </footer>

    </article>

    <script>
        // High-Quality Client-Side PDF Generator
        function downloadPdf() {
            var element = document.getElementById('invoice-doc');
            var opt = {
                margin:       [10, 15, 10, 15],
                filename:     'Invoice_<?= htmlspecialchars($invoice['invoice_no'] ?? 'INV-9901') ?>.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
