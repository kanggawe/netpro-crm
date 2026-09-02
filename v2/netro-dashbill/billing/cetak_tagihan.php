<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

require_login();

// Corporate Settings
$companyName = Setting::get('company_name', 'PT NETPRO TELEKOMUNIKASI INDONESIA');
$companyIzin = Setting::get('company_izin_isp', 'KEPMENKOMINFO NO. 412/TEL.02.02/2021');
$companyNib = Setting::get('company_nib', '9120003418821 | KBLI 61100');
$companyAddress = Setting::get('company_address', 'Gedung Cyber Lt. 5, Jl. Kuningan Barat No. 8, Jakarta Selatan 12710');
$companyPhone = Setting::get('company_phone', '(021) 5290-8812');
$companyDirector = Setting::get('company_director', 'Muhammad Ibrahim, S.Kom., M.T.');

$invoices = Invoice::all();
$statusFilter = strtoupper($_GET['status'] ?? '');

$filteredInvoices = [];
$totalInvoiced = 0;
$totalPaid = 0;
$totalUnpaid = 0;

foreach ($invoices as $inv) {
    $amt = floatval($inv['total_amount'] ?? ($inv['amount'] ?? 0));
    $st = strtoupper($inv['status'] ?? 'UNPAID');
    
    $totalInvoiced += $amt;
    if ($st === 'PAID' || $st === 'LUNAS') {
        $totalPaid += $amt;
    } else {
        $totalUnpaid += $amt;
    }

    if (!empty($statusFilter) && $statusFilter !== 'ALL') {
        if ($statusFilter === 'PAID' && ($st === 'PAID' || $st === 'LUNAS')) {
            $filteredInvoices[] = $inv;
        } elseif ($statusFilter === 'UNPAID' && ($st !== 'PAID' && $st !== 'LUNAS')) {
            $filteredInvoices[] = $inv;
        }
    } else {
        $filteredInvoices[] = $inv;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi_Tagihan_Billing_<?= date('Ym') ?> - <?= htmlspecialchars($companyName) ?></title>
    
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- html2pdf.js CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-8 flex flex-col items-center">

    <!-- Floating Top Action Bar (RedDash Style) -->
    <div class="no-print w-full max-w-4xl bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 border border-brand-900/40 text-white px-6 py-4 rounded-2xl shadow-xl flex flex-wrap justify-between items-center gap-3 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-rose-600 flex items-center justify-center text-white text-sm font-bold shadow-md">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h2 class="text-sm font-extrabold tracking-tight text-white">Dokumen Rekapitulasi Tagihan & Billing Pelanggan</h2>
                <p class="text-[11px] text-brand-300 font-mono">Ukuran Standar Kertas: <strong>A4 Portrait</strong></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-xs px-4 py-2 rounded-xl shadow-lg shadow-brand-950/40 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Rekap (Ctrl+P)
            </button>
            <button onclick="downloadPDF()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </button>
            <button onclick="window.close()" class="bg-white/10 hover:bg-white/20 text-slate-200 font-bold text-xs px-3.5 py-2 rounded-xl transition">
                <i class="fa-solid fa-xmark"></i> Tutup
            </button>
        </div>
    </div>

    <!-- Official A4 Paper Container -->
    <div id="printableDocument" class="print-container w-full max-w-4xl bg-white p-8 sm:p-12 rounded-2xl shadow-lg border border-slate-200 text-xs space-y-6">
        
        <!-- 1. Corporate Official Letterhead -->
        <div class="flex justify-between items-start pb-4 border-b-2 border-slate-900 gap-4">
            <div class="flex gap-4 items-center">
                <div class="w-14 h-14 bg-gradient-to-tr from-brand-600 to-rose-600 rounded-xl flex items-center justify-center text-white text-2xl font-black shadow shrink-0">
                    <i class="fa-solid fa-network-wired text-xl"></i>
                </div>
                <div>
                    <h1 class="text-base font-extrabold text-slate-900 tracking-tight uppercase"><?= htmlspecialchars($companyName) ?></h1>
                    <p class="text-[11px] text-slate-700 font-medium">Izin Penyelenggara Jasa Internet (ISP): <?= htmlspecialchars($companyIzin) ?> • NIB: <?= htmlspecialchars($companyNib) ?></p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Alamat: <?= htmlspecialchars($companyAddress) ?> • Telp: <?= htmlspecialchars($companyPhone) ?></p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="px-2.5 py-1 bg-brand-950 text-white font-bold rounded-lg text-[10px] uppercase tracking-wider block mb-1">REKAPITULASI BILLING</span>
                <span class="text-[10px] text-slate-500 font-mono block">Periode: <?= date('F Y') ?></span>
                <span class="text-[10px] text-slate-500 font-mono block">Tanggal Cetak: <?= date('d/m/Y H:i') ?></span>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-1">
            <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">REKAPITULASI DAFTAR TAGIHAN & PIUTANG PELANGGAN FTTH</h2>
            <p class="text-[11px] text-slate-500">Status Pembayaran: <?= !empty($statusFilter) ? htmlspecialchars($statusFilter) : 'SEMUA TAGIHAN' ?> • Total <?= count($filteredInvoices) ?> Tagihan</p>
        </div>

        <!-- 2. KPI Summary Cards -->
        <div class="grid grid-cols-3 gap-4">
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
                <span class="text-[10px] text-slate-500 font-semibold uppercase block">Total Tagihan Terbit</span>
                <strong class="text-lg font-black text-slate-900 block mt-0.5 font-mono"><?= format_rupiah($totalInvoiced) ?></strong>
                <span class="text-[10px] text-slate-500"><?= count($invoices) ?> Invoice Terdaftar</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
                <span class="text-[10px] text-slate-500 font-semibold uppercase block">Total Terbayar (Lunas)</span>
                <strong class="text-lg font-black text-emerald-600 block mt-0.5 font-mono"><?= format_rupiah($totalPaid) ?></strong>
                <span class="text-[10px] text-emerald-600 font-bold"><?= $totalInvoiced > 0 ? round(($totalPaid/$totalInvoiced)*100, 1) : 0 ?>% Collection Rate</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
                <span class="text-[10px] text-slate-500 font-semibold uppercase block">Piutang Berjalan (Unpaid)</span>
                <strong class="text-lg font-black text-rose-600 block mt-0.5 font-mono"><?= format_rupiah($totalUnpaid) ?></strong>
                <span class="text-[10px] text-rose-600 font-medium">Tertunggak / Jatuh Tempo</span>
            </div>
        </div>

        <!-- 3. Invoices Table -->
        <div class="space-y-2">
            <table class="w-full text-left border-collapse border border-slate-300 text-[11px]">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300 text-[10px] uppercase">
                        <th class="py-2.5 px-3 border-r border-slate-300 w-28">No. Invoice</th>
                        <th class="py-2.5 px-3 border-r border-slate-300">Nama Pelanggan</th>
                        <th class="py-2.5 px-3 border-r border-slate-300 w-36">Paket Layanan</th>
                        <th class="py-2.5 px-3 border-r border-slate-300 w-28 font-mono text-right">Nominal</th>
                        <th class="py-2.5 px-3 border-r border-slate-300 w-24 text-center">Jatuh Tempo</th>
                        <th class="py-2.5 px-3 text-center w-24">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($filteredInvoices)): ?>
                        <tr><td colspan="6" class="py-6 text-center text-slate-400">Tidak ada data tagihan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($filteredInvoices as $inv): 
                            $amt = floatval($inv['total_amount'] ?? ($inv['amount'] ?? 0));
                            $st = strtoupper($inv['status'] ?? 'UNPAID');
                            $isPaid = ($st === 'PAID' || $st === 'LUNAS');
                        ?>
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-2.5 px-3 border-r border-slate-200 font-mono font-bold text-blue-700"><?= htmlspecialchars($inv['inv_number'] ?? 'INV-000') ?></td>
                            <td class="py-2.5 px-3 border-r border-slate-200">
                                <strong class="font-bold text-slate-900 block"><?= htmlspecialchars($inv['customer_name'] ?? 'Pelanggan') ?></strong>
                                <span class="text-[10px] text-slate-500"><?= htmlspecialchars($inv['customer_id'] ?? '') ?></span>
                            </td>
                            <td class="py-2.5 px-3 border-r border-slate-200 font-medium text-slate-700"><?= htmlspecialchars($inv['package_name'] ?? 'Internet FTTH') ?></td>
                            <td class="py-2.5 px-3 border-r border-slate-200 font-mono font-bold text-right text-slate-900"><?= format_rupiah($amt) ?></td>
                            <td class="py-2.5 px-3 border-r border-slate-200 text-center font-mono text-[10px]"><?= htmlspecialchars($inv['due_date'] ?? date('Y-m-d')) ?></td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2 py-0.5 <?= $isPaid ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' ?> font-bold rounded text-[9px]">
                                    <?= htmlspecialchars($st) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- 4. Three-Level Authorization Sign-Off -->
        <div class="pt-6 border-t border-slate-300">
            <div class="grid grid-cols-3 gap-6 text-center text-xs">
                <div class="space-y-14">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Staff Billing & Kasir,</span>
                        <span class="text-[10px] text-slate-400">Verifikasi Penerimaan Kas</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-4 block w-fit mx-auto text-xs">( <?= htmlspecialchars(auth_user()['full_name'] ?? 'Admin Billing') ?> )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">Billing Officer</span>
                    </div>
                </div>

                <div class="space-y-14">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Supervisor Finance,</span>
                        <span class="text-[10px] text-slate-400">Pemeriksaan Rekonsiliasi Bank</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-4 block w-fit mx-auto text-xs">( Sarah Anindita, S.E. )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">Finance Lead</span>
                    </div>
                </div>

                <div class="space-y-14">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Disetujui Oleh,</span>
                        <span class="text-[10px] text-slate-400">Direksi Perusahaan</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-4 block w-fit mx-auto text-xs">( <?= htmlspecialchars($companyDirector) ?> )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">President Director / CEO</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-3 border-t border-slate-200 text-center text-[9px] text-slate-400 font-mono flex justify-between">
                <span>Dokumen Rekapitulasi Billing Resmi NETPRO Billing & ERP OS.</span>
                <span>Halaman 1 dari 1</span>
            </div>
        </div>

    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('printableDocument');
            const opt = {
                margin:       [10, 10, 10, 10],
                filename:     'Rekap_Tagihan_<?= date('Ym') ?>_NETPRO.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2, useCORS: true },
                jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            html2pdf().set(opt).from(element).save();
        }

        <?php if (isset($_GET['autoprint']) && $_GET['autoprint'] == '1'): ?>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => { window.print(); }, 600);
        });
        <?php endif; ?>
    </script>
</body>
</html>
