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
$companyEmail = Setting::get('company_email', 'hrd@netpro.co.id');
$companyDirector = Setting::get('company_director', 'Muhammad Ibrahim, S.Kom., M.T.');

$attendances = Attendance::all();
$employees = Employee::all();

$onTime = 0;
$late = 0;
$nightShift = 0;

foreach ($attendances as $att) {
    $st = strtoupper($att['status'] ?? '');
    if ($st === 'TEPAT WAKTU') $onTime++;
    elseif ($st === 'TERLAMBAT') $late++;
    if (str_contains($att['shift_type'] ?? '', 'Malam')) $nightShift++;
}

$currentUser = auth_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi_Presensi_Staf_<?= date('Ymd') ?> - <?= htmlspecialchars($companyName) ?></title>
    
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
            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white text-sm font-bold shadow-md">
                <i class="fa-solid fa-clipboard-user"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold tracking-tight">Dokumen Rekapitulasi Presensi Staf ISP</h2>
                <p class="text-[11px] text-slate-400">Ukuran Standar Kertas: <strong>A4 Portrait</strong></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak / Print (Ctrl+P)
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
                <div class="w-14 h-14 bg-gradient-to-tr from-emerald-600 to-teal-800 rounded-xl flex items-center justify-center text-white text-2xl font-black shadow shrink-0">
                    <i class="fa-solid fa-tower-cell"></i>
                </div>
                <div>
                    <h1 class="text-base font-extrabold text-slate-900 tracking-tight uppercase"><?= htmlspecialchars($companyName) ?></h1>
                    <p class="text-[11px] text-slate-700 font-medium">Izin Penyelenggara Jasa Internet (ISP): <?= htmlspecialchars($companyIzin) ?> • NIB: <?= htmlspecialchars($companyNib) ?></p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Divisi Human Capital & Operasional • Telp: <?= htmlspecialchars($companyPhone) ?> • Email: <?= htmlspecialchars($companyEmail) ?></p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="px-2.5 py-1 bg-emerald-900 text-white font-bold rounded text-[10px] uppercase tracking-wider block mb-1">LOG KEHADIRAN</span>
                <span class="text-[10px] text-slate-500 font-mono block">No. Dok: ATT-LOG/<?= date('Ymd') ?>/01</span>
                <span class="text-[10px] text-slate-500 font-mono block">Tanggal: <?= date('d F Y') ?></span>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-2">
            <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">REKAPITULASI PRESENSI, GPS GEOFENCING & SHIFT KERJA 24/7</h2>
            <p class="text-[11px] text-slate-500">Pencatatan Jam Kerja Pegawai & Teknisi Lapangan Sesuai PP No. 35 Tahun 2021</p>
        </div>

        <!-- 2. KPI Summary Cards (Full-Width Proportional Grid) -->
        <div class="grid grid-cols-3 gap-4">
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
                <span class="text-[10px] text-slate-500 font-semibold uppercase block">Hadir Tepat Waktu</span>
                <strong class="text-lg font-black text-emerald-600 block mt-0.5 font-mono"><?= $onTime ?> Karyawan</strong>
                <span class="text-[10px] text-slate-500">Radius GPS HQ Valid</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
                <span class="text-[10px] text-slate-500 font-semibold uppercase block">Roster Shift Malam NOC</span>
                <strong class="text-lg font-black text-blue-600 block mt-0.5 font-mono"><?= max(1, $nightShift) ?> Operator</strong>
                <span class="text-[10px] text-slate-500">Shift 22:00 - 07:00 (24/7)</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/50">
                <span class="text-[10px] text-slate-500 font-semibold uppercase block">Total Presensi Tercatat</span>
                <strong class="text-lg font-black text-slate-900 block mt-0.5 font-mono"><?= count($attendances) ?> Log</strong>
                <span class="text-[10px] text-slate-500">Sinkronisasi Modul Payroll</span>
            </div>
        </div>

        <!-- 3. Attendance Log Table -->
        <div class="space-y-2">
            <h3 class="font-bold text-slate-900 text-xs flex items-center justify-between">
                <span>Daftar Log Presensi Harian Staf ISP</span>
                <span class="text-[10px] text-slate-400 font-normal">Waktu Standar: WIB (UTC+7)</span>
            </h3>
            <table class="w-full text-left border-collapse border border-slate-300 text-[11px]">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 font-bold border-b border-slate-300 text-[10px] uppercase">
                        <th class="py-2.5 px-3 border-r border-slate-300 w-36">Nama Pegawai</th>
                        <th class="py-2.5 px-3 border-r border-slate-300 w-28">Divisi Kerja</th>
                        <th class="py-2.5 px-3 border-r border-slate-300 w-28">Pola Shift</th>
                        <th class="py-2.5 px-3 border-r border-slate-300 w-24 font-mono text-center">Clock-In</th>
                        <th class="py-2.5 px-3 border-r border-slate-300 w-24 font-mono text-center">Clock-Out</th>
                        <th class="py-2.5 px-3 border-r border-slate-300">Titik Koordinat GPS</th>
                        <th class="py-2.5 px-3 text-center w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($attendances)): ?>
                        <tr><td colspan="7" class="py-6 text-center text-slate-400">Belum ada catatan presensi hari ini.</td></tr>
                    <?php else: ?>
                        <?php foreach ($attendances as $att): ?>
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-2.5 px-3 border-r border-slate-200 font-bold text-slate-900"><?= htmlspecialchars($att['employee_name']) ?></td>
                            <td class="py-2.5 px-3 border-r border-slate-200 text-slate-600"><?= htmlspecialchars($att['division']) ?></td>
                            <td class="py-2.5 px-3 border-r border-slate-200 font-medium text-slate-700"><?= htmlspecialchars($att['shift_type']) ?></td>
                            <td class="py-2.5 px-3 border-r border-slate-200 font-mono font-bold text-emerald-700 text-center"><?= htmlspecialchars($att['clock_in']) ?> WIB</td>
                            <td class="py-2.5 px-3 border-r border-slate-200 font-mono text-slate-500 text-center"><?= $att['clock_out'] ? htmlspecialchars($att['clock_out']) . ' WIB' : 'On Duty' ?></td>
                            <td class="py-2.5 px-3 border-r border-slate-200 font-mono text-[10px] text-slate-600"><?= htmlspecialchars($att['gps_location']) ?></td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold rounded text-[9px]">
                                    <?= htmlspecialchars($att['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- 4. Three-Level HR & Management Sign-Off -->
        <div class="pt-6 border-t border-slate-300">
            <div class="grid grid-cols-3 gap-6 text-center text-xs">
                <div class="space-y-14">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Dicatat & Diverifikasi,</span>
                        <span class="text-[10px] text-slate-400">Staff Human Capital & GA</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-4 block w-fit mx-auto text-xs">( <?= htmlspecialchars($currentUser['full_name'] ?? 'Admin HR') ?> )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">HC Operations Staff</span>
                    </div>
                </div>

                <div class="space-y-14">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Diketahui Oleh,</span>
                        <span class="text-[10px] text-slate-400">Manager HR & Legal</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-4 block w-fit mx-auto text-xs">( Sarah Anindita, S.Psi. )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">Manager HR & Corporate Affairs</span>
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
                <span>Dokumen resmi absensi geofencing NETPRO Billing & ERP OS.</span>
                <span>Halaman 1 dari 1</span>
            </div>
        </div>

    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('printableDocument');
            const opt = {
                margin:       [10, 10, 10, 10],
                filename:     'Rekap_Presensi_<?= date('Ymd') ?>_NETPRO.pdf',
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
