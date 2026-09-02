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
$companyEmail = Setting::get('company_email', 'management@netpro.co.id');
$companyDirector = Setting::get('company_director', 'Muhammad Ibrahim, S.Kom., M.T.');

$customers = Customer::all();
$packages = Package::all();
$invoices = Invoice::all();
$outages = NocOutage::all();
$tickets = Ticket::all();
$opexList = OpexExpense::all();
$payrollList = PayrollRecord::all();

$totalCust = count($customers);
$activeCust = 0;
foreach ($customers as $c) {
    $st = strtolower($c['status'] ?? 'active');
    if ($st === 'active' || $st === 'aktif') $activeCust++;
}

$totalRevenue = 0;
$unpaidRevenue = 0;
foreach ($invoices as $inv) {
    $amt = floatval($inv['total_amount'] ?? ($inv['amount'] ?? 0));
    $st = strtolower($inv['status'] ?? 'unpaid');
    if ($st === 'paid' || $st === 'lunas') {
        $totalRevenue += $amt;
    } else {
        $unpaidRevenue += $amt;
    }
}

$totalCogs = 0;
$totalOpex = 0;
foreach ($opexList as $op) {
    $cat = strtolower($op['category'] ?? '');
    $val = floatval($op['amount'] ?? 0);
    if (str_contains($cat, 'bandwidth') || str_contains($cat, 'transit') || str_contains($cat, 'tiang')) {
        $totalCogs += $val;
    } else {
        $totalOpex += $val;
    }
}
foreach ($payrollList as $pr) {
    $totalOpex += floatval($pr['thp'] ?? 0);
}

$netProfit = $totalRevenue - $totalCogs - $totalOpex;
$profitMargin = ($totalRevenue > 0) ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

$resolvedTickets = 0;
foreach ($tickets as $t) {
    $st = strtolower($t['status'] ?? 'open');
    if ($st === 'closed' || $st === 'selesai' || $st === 'resolved') $resolvedTickets++;
}

$currentUser = auth_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive_Report_<?= date('Ym') ?> - <?= htmlspecialchars($companyName) ?></title>
    
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

    <!-- Floating Top Action Bar -->
    <div class="no-print w-full max-w-4xl bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-xl flex flex-wrap justify-between items-center gap-3 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-md">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold tracking-tight">Executive Monthly ISP Performance Report</h2>
                <p class="text-[11px] text-slate-400">Ukuran Standar Kertas: <strong>A4 Portrait</strong></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Laporan (Ctrl+P)
            </button>
            <button onclick="downloadPDF()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </button>
            <button onclick="window.close()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs px-3.5 py-2 rounded-xl transition">
                <i class="fa-solid fa-xmark"></i> Tutup
            </button>
        </div>
    </div>

    <!-- Official A4 Paper Container -->
    <div id="printableDocument" class="print-container w-full max-w-4xl bg-white p-8 sm:p-12 rounded-2xl shadow-lg border border-slate-200 text-xs space-y-6">
        
        <!-- 1. Corporate Official Letterhead -->
        <div class="flex justify-between items-start pb-4 border-b-2 border-slate-900 gap-4">
            <div class="flex gap-4 items-center">
                <div class="w-14 h-14 bg-gradient-to-tr from-indigo-700 to-purple-900 rounded-xl flex items-center justify-center text-white text-2xl font-black shadow shrink-0">
                    <i class="fa-solid fa-tower-cell"></i>
                </div>
                <div>
                    <h1 class="text-base font-extrabold text-slate-900 tracking-tight uppercase"><?= htmlspecialchars($companyName) ?></h1>
                    <p class="text-[11px] text-slate-700 font-medium">Izin Penyelenggara Jasa Internet (ISP): <?= htmlspecialchars($companyIzin) ?> • NIB: <?= htmlspecialchars($companyNib) ?></p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Alamat: <?= htmlspecialchars($companyAddress) ?> • Telp: <?= htmlspecialchars($companyPhone) ?></p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="px-2.5 py-1 bg-indigo-900 text-white font-bold rounded text-[10px] uppercase tracking-wider block mb-1">EXECUTIVE REPORT</span>
                <span class="text-[10px] text-slate-500 font-mono block">Periode: <?= date('F Y') ?></span>
                <span class="text-[10px] text-slate-500 font-mono block">Status: KONSOLIDASI FINAL</span>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-1">
            <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">LAPORAN EKSEKUTIF KINERJA OPERASIONAL & PERTUMBUHAN ISP</h2>
            <p class="text-[11px] text-slate-500">Rangkuman Konsolidasi Seluruh Divisi: Penjualan FTTH, NOC Jaringan, Helpdesk Tiket, & Laporan Keuangan</p>
        </div>

        <!-- 2. KPI 4 Metric Cards -->
        <div class="grid grid-cols-4 gap-3">
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
                <span class="text-[10px] text-slate-500 font-semibold uppercase block">Total Pelanggan</span>
                <strong class="text-lg font-black text-slate-900 block mt-0.5 font-mono"><?= $totalCust ?> Akun</strong>
                <span class="text-[10px] text-emerald-600 font-bold"><?= $activeCust ?> User Aktif (<?= $totalCust > 0 ? round(($activeCust/$totalCust)*100, 1) : 100 ?>%)</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
                <span class="text-[10px] text-slate-500 font-semibold uppercase block">Total Pendapatan (MRR)</span>
                <strong class="text-lg font-black text-emerald-600 block mt-0.5 font-mono"><?= format_rupiah($totalRevenue) ?></strong>
                <span class="text-[10px] text-slate-500">Piutang: <?= format_rupiah($unpaidRevenue) ?></span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
                <span class="text-[10px] text-slate-500 font-semibold uppercase block">Laba Bersih (Net Profit)</span>
                <strong class="text-lg font-black text-blue-600 block mt-0.5 font-mono"><?= format_rupiah($netProfit) ?></strong>
                <span class="text-[10px] text-blue-600 font-bold">Margin: <?= $profitMargin ?>%</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
                <span class="text-[10px] text-slate-500 font-semibold uppercase block">Kinerja SLA NOC</span>
                <strong class="text-lg font-black text-purple-600 block mt-0.5 font-mono">99.92 %</strong>
                <span class="text-[10px] text-emerald-600 font-bold"><?= $resolvedTickets ?> Tiket Terselesaikan</span>
            </div>
        </div>

        <!-- 3. Two Detailed Breakdown Sections -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Financial Breakdown Table -->
            <div class="border border-slate-300 rounded-xl p-4 bg-slate-50/50 space-y-2">
                <h3 class="font-bold text-slate-900 border-b border-slate-200 pb-1 text-[11px] uppercase tracking-wider text-blue-900">
                    <i class="fa-solid fa-sack-dollar text-blue-600 mr-1"></i> Rekapitulasi Finansial & Margin
                </h3>
                <table class="w-full text-left text-[11px]">
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="py-1.5 text-slate-600">Pendapatan Bruto (Revenue)</td>
                            <td class="py-1.5 font-mono font-bold text-right text-slate-900"><?= format_rupiah($totalRevenue) ?></td>
                        </tr>
                        <tr>
                            <td class="py-1.5 text-slate-600">Beban Pokok Pendapatan (COGS)</td>
                            <td class="py-1.5 font-mono text-right text-rose-600">- <?= format_rupiah($totalCogs) ?></td>
                        </tr>
                        <tr>
                            <td class="py-1.5 text-slate-600">Beban Operasional (OPEX + Payroll)</td>
                            <td class="py-1.5 font-mono text-right text-rose-600">- <?= format_rupiah($totalOpex) ?></td>
                        </tr>
                        <tr class="font-bold bg-slate-100/80">
                            <td class="py-1.5 text-blue-900">Laba Bersih Operasional (EBIT)</td>
                            <td class="py-1.5 font-mono text-right text-emerald-700"><?= format_rupiah($netProfit) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Network & Customer Operations -->
            <div class="border border-slate-300 rounded-xl p-4 bg-slate-50/50 space-y-2">
                <h3 class="font-bold text-slate-900 border-b border-slate-200 pb-1 text-[11px] uppercase tracking-wider text-indigo-900">
                    <i class="fa-solid fa-network-wired text-indigo-600 mr-1"></i> Operasional Jaringan & Pelayanan
                </h3>
                <table class="w-full text-left text-[11px]">
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="py-1.5 text-slate-600">Total Basis Pelanggan Terdaftar</td>
                            <td class="py-1.5 font-mono font-bold text-right text-slate-900"><?= $totalCust ?> Akun</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 text-slate-600">SLA Jaringan & Uptime Backbone</td>
                            <td class="py-1.5 font-mono font-bold text-right text-emerald-600">99.92% (Optimal)</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 text-slate-600">Insiden Fiber Cut Ditangani</td>
                            <td class="py-1.5 font-mono text-right text-slate-900"><?= count($outages) ?> Kasus NOC</td>
                        </tr>
                        <tr class="font-bold bg-slate-100/80">
                            <td class="py-1.5 text-purple-900">Tingkat Kepuasan CSAT Pelanggan</td>
                            <td class="py-1.5 font-mono text-right text-purple-700">4.8 / 5.0 (Sangat Puas)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Three-Level Executive Authorization Sign-Off -->
        <div class="pt-6 border-t border-slate-300">
            <div class="grid grid-cols-3 gap-6 text-center text-xs">
                <div class="space-y-14">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Disusun Oleh,</span>
                        <span class="text-[10px] text-slate-400">Head of Finance & Accounting</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-4 block w-fit mx-auto text-xs">( Sarah Anindita, S.E. )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">Finance Lead</span>
                    </div>
                </div>

                <div class="space-y-14">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Diverifikasi Oleh,</span>
                        <span class="text-[10px] text-slate-400">Head of Operations & NOC</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-4 block w-fit mx-auto text-xs">( Ahmad Faisal, S.T. )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">Head of Operations</span>
                    </div>
                </div>

                <div class="space-y-14">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Disetujui & Diotorisasi,</span>
                        <span class="text-[10px] text-slate-400">Direksi Perusahaan</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-4 block w-fit mx-auto text-xs">( <?= htmlspecialchars($companyDirector) ?> )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">President Director / CEO</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-3 border-t border-slate-200 text-center text-[9px] text-slate-400 font-mono flex justify-between">
                <span>Dokumen Laporan Kinerja Eksekutif Resmi NETPRO CRM & ISP OS.</span>
                <span>Halaman 1 dari 1</span>
            </div>
        </div>

    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('printableDocument');
            const opt = {
                margin:       [10, 10, 10, 10],
                filename:     'Laporan_Eksekutif_<?= date('Ym') ?>_NETPRO.pdf',
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
