<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

require_login();

// Corporate Settings
$companyName = Setting::get('company_name', 'PT NETPRO TELEKOMUNIKASI INDONESIA');
$companyIzin = Setting::get('company_izin_isp', 'KEPMENKOMINFO NO. 412/TEL.02.02/2021');
$companyAddress = Setting::get('company_address', 'Gedung Cyber Lt. 5, Jl. Kuningan Barat No. 8, Jakarta Selatan 12710');
$companyPhone = Setting::get('company_phone', '(021) 5290-8812');

$payrollList = PayrollRecord::all();
$selectedId = intval($_GET['id'] ?? 0);
$payroll = null;

if ($selectedId > 0) {
    foreach ($payrollList as $p) {
        if (intval($p['id']) === $selectedId) {
            $payroll = $p;
            break;
        }
    }
}
if (!$payroll && !empty($payrollList)) {
    $payroll = $payrollList[0];
}
if (!$payroll) {
    $payroll = [
        'slip_no' => '-',
        'employee_name' => '-',
        'employee_nik' => '-',
        'division' => '-',
        'position' => '-',
        'bank_account' => '-',
        'basic_salary' => 0,
        'allowance_pos' => 0,
        'allowance_trans' => 0,
        'bonus_overtime' => 0,
        'deduction_bpjs' => 0,
        'deduction_tax' => 0,
        'deduction_other' => 0,
        'thp' => 0,
        'period' => date('F Y'),
        'payment_date' => date('Y-m-d')
    ];
}

$basic = floatval($payroll['basic_salary'] ?? 5000000);
$allowPos = floatval($payroll['allowance_pos'] ?? 500000);
$allowTrans = floatval($payroll['allowance_trans'] ?? 400000);
$bonus = floatval($payroll['bonus_overtime'] ?? 300000);
$totalEarnings = $basic + $allowPos + $allowTrans + $bonus;

$bpjs = floatval($payroll['deduction_bpjs'] ?? 150000);
$tax = floatval($payroll['deduction_tax'] ?? 100000);
$other = floatval($payroll['deduction_other'] ?? 0);
$totalDeductions = $bpjs + $tax + $other;

$thp = floatval($payroll['thp'] ?? ($totalEarnings - $totalDeductions));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip_Gaji_<?= htmlspecialchars($payroll['slip_no'] ?? 'SLIP001') ?>_<?= htmlspecialchars($payroll['employee_name'] ?? '') ?></title>
    
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
    <div class="no-print w-full max-w-3xl bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-xl flex flex-wrap justify-between items-center gap-3 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white text-sm font-bold shadow-md">
                <i class="fa-solid fa-money-check-dollar"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold tracking-tight">Dokumen Slip Gaji Karyawan (Official Payroll)</h2>
                <p class="text-[11px] text-slate-400">Pegawai: <strong><?= htmlspecialchars($payroll['employee_name']) ?></strong></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Slip (Ctrl+P)
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
    <div id="printableDocument" class="print-container w-full max-w-3xl bg-white p-8 sm:p-10 rounded-2xl shadow-lg border border-slate-200 text-xs space-y-5">
        
        <!-- 1. Corporate Official Letterhead -->
        <div class="flex justify-between items-start pb-4 border-b-2 border-slate-900 gap-4">
            <div class="flex gap-3.5 items-center">
                <div class="w-12 h-12 bg-gradient-to-tr from-emerald-700 to-teal-900 rounded-xl flex items-center justify-center text-white text-xl font-black shadow shrink-0">
                    <i class="fa-solid fa-tower-cell"></i>
                </div>
                <div>
                    <h1 class="text-sm font-extrabold text-slate-900 tracking-tight uppercase"><?= htmlspecialchars($companyName) ?></h1>
                    <p class="text-[10px] text-slate-600">Izin ISP Kominfo No: <?= htmlspecialchars($companyIzin) ?></p>
                    <p class="text-[9.5px] text-slate-500">Divisi Human Capital & Payroll Department • Telp: <?= htmlspecialchars($companyPhone) ?></p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="px-2.5 py-0.5 bg-emerald-900 text-white font-bold rounded text-[9px] uppercase tracking-wider block mb-1">RAHASIA / CONFIDENTIAL</span>
                <span class="text-[9.5px] text-slate-500 font-mono block">No: <?= htmlspecialchars($payroll['slip_no'] ?? 'SLIP-001') ?></span>
                <span class="text-[9.5px] text-slate-500 font-mono block">Periode: <?= date('F Y') ?></span>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-0.5">
            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">SLIP GAJI & RINCIAN REMUNERASI KARYAWAN</h2>
        </div>

        <!-- Employee Info Box -->
        <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
            <div class="grid grid-cols-2 gap-4">
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-slate-500">Nama Pegawai</span>
                    <span class="col-span-2 font-bold text-slate-900">: <?= htmlspecialchars($payroll['employee_name']) ?></span>
                    
                    <span class="text-slate-500">NIK / ID Staf</span>
                    <span class="col-span-2 font-mono font-bold text-blue-700">: <?= htmlspecialchars($payroll['employee_nik'] ?? 'EMP-001') ?></span>
                    
                    <span class="text-slate-500">Divisi Kerja</span>
                    <span class="col-span-2 font-semibold text-slate-800">: <?= htmlspecialchars($payroll['division'] ?? 'Operasional') ?></span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-slate-500">Jabatan</span>
                    <span class="col-span-2 font-bold text-slate-900">: <?= htmlspecialchars($payroll['position'] ?? 'Staf') ?></span>
                    
                    <span class="text-slate-500">Rekening Transfer</span>
                    <span class="col-span-2 font-mono font-bold text-slate-800">: <?= htmlspecialchars($payroll['bank_account'] ?? 'BCA') ?></span>
                    
                    <span class="text-slate-500">Tanggal Transfer</span>
                    <span class="col-span-2 font-semibold text-slate-800">: <?= date('d F Y') ?></span>
                </div>
            </div>
        </div>

        <!-- Earnings & Deductions (2 Columns Breakdown) -->
        <div class="grid grid-cols-2 gap-4">
            <!-- Earnings -->
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/30 space-y-2">
                <h3 class="font-bold text-slate-900 border-b border-slate-200 pb-1 text-[11px] uppercase tracking-wider text-emerald-900">
                    <i class="fa-solid fa-circle-plus text-emerald-600 mr-1"></i> Komponen Pendapatan (+)
                </h3>
                <table class="w-full text-left text-[11px]">
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="py-1.5 text-slate-600">Gaji Pokok (Basic Salary)</td>
                            <td class="py-1.5 font-mono font-bold text-right text-slate-900"><?= format_rupiah($basic) ?></td>
                        </tr>
                        <tr>
                            <td class="py-1.5 text-slate-600">Tunjangan Jabatan & Keahlian</td>
                            <td class="py-1.5 font-mono text-right text-slate-900"><?= format_rupiah($allowPos) ?></td>
                        </tr>
                        <tr>
                            <td class="py-1.5 text-slate-600">Tunjangan Transport & BBM</td>
                            <td class="py-1.5 font-mono text-right text-slate-900"><?= format_rupiah($allowTrans) ?></td>
                        </tr>
                        <tr>
                            <td class="py-1.5 text-slate-600">Insentif Pasang Baru / Lembur</td>
                            <td class="py-1.5 font-mono text-right text-slate-900"><?= format_rupiah($bonus) ?></td>
                        </tr>
                        <tr class="font-bold bg-emerald-50/80">
                            <td class="py-1.5 text-emerald-900">Total Pendapatan Bruto</td>
                            <td class="py-1.5 font-mono text-right text-emerald-700"><?= format_rupiah($totalEarnings) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Deductions -->
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/30 space-y-2">
                <h3 class="font-bold text-slate-900 border-b border-slate-200 pb-1 text-[11px] uppercase tracking-wider text-rose-900">
                    <i class="fa-solid fa-circle-minus text-rose-600 mr-1"></i> Komponen Potongan (-)
                </h3>
                <table class="w-full text-left text-[11px]">
                    <tbody class="divide-y divide-slate-200">
                        <tr>
                            <td class="py-1.5 text-slate-600">Iuran BPJS Ketenagakerjaan (2%)</td>
                            <td class="py-1.5 font-mono text-right text-rose-600"><?= format_rupiah($bpjs) ?></td>
                        </tr>
                        <tr>
                            <td class="py-1.5 text-slate-600">Pajak Penghasilan PPh 21 (TER)</td>
                            <td class="py-1.5 font-mono text-right text-rose-600"><?= format_rupiah($tax) ?></td>
                        </tr>
                        <tr>
                            <td class="py-1.5 text-slate-600">Potongan Keterlambatan / Kasbon</td>
                            <td class="py-1.5 font-mono text-right text-rose-600"><?= format_rupiah($other) ?></td>
                        </tr>
                        <tr>
                            <td class="py-1.5 text-slate-400">-</td>
                            <td class="py-1.5 text-right text-slate-400 font-mono">-</td>
                        </tr>
                        <tr class="font-bold bg-rose-50/80">
                            <td class="py-1.5 text-rose-900">Total Potongan</td>
                            <td class="py-1.5 font-mono text-right text-rose-700"><?= format_rupiah($totalDeductions) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Net Take Home Pay (THP Box) -->
        <div class="border-2 border-emerald-500 bg-emerald-50/40 rounded-xl p-4 flex justify-between items-center">
            <div>
                <span class="text-slate-500 font-bold uppercase text-[10px] block">TOTAL GAJI BERSIH (TAKE HOME PAY)</span>
                <span class="text-[11px] text-slate-600 italic">Ditransfer otomatis via Virtual Account Payroll</span>
            </div>
            <div class="text-right">
                <strong class="text-2xl font-black text-emerald-700 font-mono block"><?= format_rupiah($thp) ?></strong>
                <span class="text-[9.5px] text-emerald-800 font-semibold uppercase">● STATUS: LUNAS DITRANSFER</span>
            </div>
        </div>

        <!-- Two-Party Sign-Off -->
        <div class="pt-5 border-t border-slate-300">
            <div class="grid grid-cols-2 gap-12 text-center text-xs">
                <div class="space-y-12">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Human Capital & Payroll,</span>
                        <span class="text-[10px] text-slate-400"><?= htmlspecialchars($companyName) ?></span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-8 block w-fit mx-auto text-xs">( Sarah Anindita, S.Psi. )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">Manager HC & Remunerasi</span>
                    </div>
                </div>

                <div class="space-y-12">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Karyawan Penerima,</span>
                        <span class="text-[10px] text-slate-400">Konfirmasi Tanda Terima Slip Gaji</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-8 block w-fit mx-auto text-xs">( <?= htmlspecialchars($payroll['employee_name']) ?> )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">Pegawai Bersangkutan</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-2 border-t border-slate-200 text-center text-[8.5px] text-slate-400 font-mono">
                Slip gaji ini diterbitkan secara elektronik dan sah tanpa memerlukan cap basah.
            </div>
        </div>

    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('printableDocument');
            const opt = {
                margin:       [10, 10, 10, 10],
                filename:     'Slip_Gaji_<?= htmlspecialchars($payroll['slip_no'] ?? 'SLIP001') ?>.pdf',
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
