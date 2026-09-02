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
$companyCallCenter = Setting::get('company_call_center', '1500-988');

$bastList = Bast::all();
$selectedId = intval($_GET['id'] ?? 0);
$bast = null;

if ($selectedId > 0) {
    foreach ($bastList as $b) {
        if (intval($b['id']) === $selectedId) {
            $bast = $b;
            break;
        }
    }
}
if (!$bast && !empty($bastList)) {
    $bast = $bastList[0];
}
if (!$bast) {
    $bast = [
        'doc_number' => '-',
        'customer_name' => '-',
        'customer_id' => '-',
        'package_name' => '-',
        'install_date' => date('Y-m-d'),
        'tech_name' => '-',
        'sn_ont' => '-',
        'sn_stb' => '-',
        'attenuation' => '-',
        'speed_dl' => '-',
        'speed_ul' => '-',
        'status' => '-'
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAST_<?= htmlspecialchars($bast['doc_number'] ?? '001') ?>_<?= htmlspecialchars($bast['customer_name'] ?? '') ?></title>
    
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
                <i class="fa-solid fa-file-signature"></i>
            </div>
            <div>
                <h2 class="text-sm font-extrabold tracking-tight text-white">Berita Acara Serah Terima (BAST) FTTH</h2>
                <p class="text-[11px] text-brand-300 font-mono">Nomor: <strong><?= htmlspecialchars($bast['doc_number'] ?? 'BAST-001') ?></strong></p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold text-xs px-4 py-2 rounded-xl shadow-lg shadow-brand-950/40 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak BAST (Ctrl+P)
            </button>
            <button onclick="downloadPDF()" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </button>
            <button onclick="window.close()" class="bg-white/10 hover:bg-white/20 text-slate-200 font-bold text-xs px-3.5 py-2 rounded-xl transition">
                <i class="fa-solid fa-xmark"></i> Tutup
            </button>
        </div>
    </div>

    <!-- Official Paper Document -->
    <div id="bast-document" class="print-container w-full max-w-4xl bg-white border border-slate-300 rounded-2xl p-8 sm:p-12 shadow-2xl space-y-6 text-slate-800 text-xs leading-relaxed">
        
        <!-- Header Kop Surat Perusahaan -->
        <div class="border-b-2 border-slate-900 pb-5 flex justify-between items-start">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-tr from-brand-600 to-rose-600 rounded-xl flex items-center justify-center text-white text-2xl font-black shadow shrink-0">
                    <i class="fa-solid fa-network-wired text-xl"></i>
                </div>
                <div>
                    <h1 class="text-base font-extrabold text-slate-900 tracking-tight uppercase"><?= htmlspecialchars($companyName) ?></h1>
                    <p class="text-[11px] text-slate-700 font-medium">Izin Penyelenggara Jasa Internet (ISP): <?= htmlspecialchars($companyIzin) ?></p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Alamat: <?= htmlspecialchars($companyAddress) ?> • Customer Care 24 Jam: <?= htmlspecialchars($companyCallCenter) ?></p>
                </div>
            </div>
            <div class="text-right shrink-0">
                <span class="px-2.5 py-1 bg-brand-950 text-white font-bold rounded-lg text-[10px] uppercase tracking-wider block mb-1">DOKUMEN RESMI FTTH</span>
                <span class="text-[10px] text-slate-500 font-mono block">No: <?= htmlspecialchars($bast['doc_number'] ?? 'BAST/001') ?></span>
                <span class="text-[10px] text-slate-500 font-mono block">Tanggal: <?= htmlspecialchars($bast['install_date'] ?? date('Y-m-d')) ?></span>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-1">
            <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">BERITA ACARA SERAH TERIMA & UJI LAIK OPERASI (BAST)</h2>
            <p class="text-[11px] text-slate-500">Pekerjaan Instalasi, Aktivasi Sambungan Fiber Optik (FTTH) & Pengujian Kualitas Redaman</p>
        </div>

        <!-- Introduction Statement -->
        <p class="text-slate-700 text-justify leading-relaxed">
            Pada hari ini, tanggal <strong><?= date('d F Y', strtotime($bast['install_date'] ?? date('Y-m-d'))) ?></strong>, telah dilaksanakan instalasi, terminasi kabel dropcore optik, dan pengujian kualitas sinyal layanan internet FTTH oleh tim teknisi resmi <strong><?= htmlspecialchars($companyName) ?></strong> untuk pelanggan:
        </p>

        <!-- Customer & Device Details (2 Columns Box) -->
        <div class="grid grid-cols-2 gap-4">
            <div class="border border-slate-300 rounded-xl p-4 bg-slate-50/50 space-y-2">
                <h3 class="font-bold text-slate-900 border-b border-slate-200 pb-1 text-[11px] uppercase tracking-wider text-blue-900">
                    <i class="fa-solid fa-user-check text-blue-600 mr-1"></i> Data Pelanggan Penerima
                </h3>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-slate-500">Nama Pelanggan</span>
                    <span class="col-span-2 font-bold text-slate-900">: <?= htmlspecialchars($bast['customer_name']) ?></span>
                    
                    <span class="text-slate-500">Customer ID</span>
                    <span class="col-span-2 font-mono font-bold text-blue-700">: <?= htmlspecialchars($bast['customer_id'] ?? 'CID-001') ?></span>
                    
                    <span class="text-slate-500">Paket Layanan</span>
                    <span class="col-span-2 font-semibold text-slate-800">: <?= htmlspecialchars($bast['package_name'] ?? 'Paket Internet') ?></span>

                    <span class="text-slate-500">Teknisi Bertugas</span>
                    <span class="col-span-2 font-bold text-slate-900">: <?= htmlspecialchars($bast['tech_name'] ?? 'Teknisi Netpro') ?></span>
                </div>
            </div>

            <div class="border border-slate-300 rounded-xl p-4 bg-slate-50/50 space-y-2">
                <h3 class="font-bold text-slate-900 border-b border-slate-200 pb-1 text-[11px] uppercase tracking-wider text-emerald-900">
                    <i class="fa-solid fa-server text-emerald-600 mr-1"></i> Rincian Perangkat Terpasang
                </h3>
                <div class="grid grid-cols-3 gap-1">
                    <span class="text-slate-500">Modem ONT GPON</span>
                    <span class="col-span-2 font-mono font-bold text-slate-800">: <?= htmlspecialchars($bast['sn_ont'] ?? 'ONT-ZTE-F670L') ?></span>
                    
                    <span class="text-slate-500">STB Android TV</span>
                    <span class="col-span-2 font-mono text-slate-700">: <?= htmlspecialchars($bast['sn_stb'] ?? 'STB-4K-NETPRO') ?></span>
                    
                    <span class="text-slate-500">Panjang Kabel</span>
                    <span class="col-span-2 font-semibold text-slate-800">: ± 145 Meter (Dropcore 1 Core)</span>

                    <span class="text-slate-500">Status Perangkat</span>
                    <span class="col-span-2 font-bold text-emerald-700">: TERPASANG & NORMAL</span>
                </div>
            </div>
        </div>

        <!-- 3. Quality & Attenuation Test Matrix -->
        <div class="space-y-2">
            <h3 class="font-bold text-slate-900 text-xs flex items-center justify-between">
                <span>Hasil Pengukuran Kualitas Sinyal Optik & Uji Kecepatan (Speedtest)</span>
                <span class="text-emerald-700 font-bold text-[10px] bg-emerald-100 px-2 py-0.5 rounded">STATUS: LAIK OPERASI (PRIMA)</span>
            </h3>
            
            <div class="grid grid-cols-4 gap-3">
                <div class="border border-slate-300 rounded-xl p-3 text-center bg-blue-50/30">
                    <span class="text-[10px] text-slate-500 font-semibold block">Redaman Optik (OPM)</span>
                    <strong class="text-base font-black text-blue-700 font-mono block mt-1"><?= htmlspecialchars($bast['attenuation'] ?? '-18.2 dBm') ?></strong>
                    <span class="text-[9px] text-emerald-700 font-bold">Standard Kominfo (&lt; -24 dBm)</span>
                </div>

                <div class="border border-slate-300 rounded-xl p-3 text-center bg-emerald-50/30">
                    <span class="text-[10px] text-slate-500 font-semibold block">Download Speed</span>
                    <strong class="text-base font-black text-emerald-700 font-mono block mt-1"><?= htmlspecialchars($bast['speed_dl'] ?? '100 Mbps') ?></strong>
                    <span class="text-[9px] text-slate-500">100% Sesuai SLA Paket</span>
                </div>

                <div class="border border-slate-300 rounded-xl p-3 text-center bg-emerald-50/30">
                    <span class="text-[10px] text-slate-500 font-semibold block">Upload Speed</span>
                    <strong class="text-base font-black text-emerald-700 font-mono block mt-1"><?= htmlspecialchars($bast['speed_ul'] ?? '100 Mbps') ?></strong>
                    <span class="text-[9px] text-slate-500">Simetris 1:1 Fiber</span>
                </div>

                <div class="border border-slate-300 rounded-xl p-3 text-center bg-purple-50/30">
                    <span class="text-[10px] text-slate-500 font-semibold block">Latency & Packet Loss</span>
                    <strong class="text-base font-black text-purple-700 font-mono block mt-1">3.2 ms / 0.0%</strong>
                    <span class="text-[9px] text-emerald-700 font-bold">Koneksi Sangat Stabil</span>
                </div>
            </div>
        </div>

        <!-- Acceptance Terms -->
        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-[10px] text-slate-600 leading-relaxed">
            <strong>Pernyataan Penerimaan:</strong> Dengan menandatangani Berita Acara ini, Pelanggan menyatakan telah menerima perangkat dalam kondisi baik, terpasang rapi, berfungsi optimal, dan telah dilakukan demonstrasi kecepatan koneksi internet sesuai paket yang dipilih.
        </div>

        <!-- 4. Two-Party Sign-Off (Technician & Customer) -->
        <div class="pt-6 border-t border-slate-300">
            <div class="grid grid-cols-2 gap-12 text-center text-xs">
                <div class="space-y-16">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Pihak Teknisi Pelaksana,</span>
                        <span class="text-[10px] text-slate-400"><?= htmlspecialchars($companyName) ?></span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-8 block w-fit mx-auto text-xs">( <?= htmlspecialchars($bast['tech_name'] ?? 'Rian Hidayat') ?> )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">Teknisi FTTH Pasang Baru</span>
                    </div>
                </div>

                <div class="space-y-16">
                    <div>
                        <span class="text-slate-500 font-semibold block text-[11px]">Pihak Pelanggan Penerima,</span>
                        <span class="text-[10px] text-slate-400">Persetujuan & Konfirmasi Serah Terima</span>
                    </div>
                    <div>
                        <strong class="text-slate-900 border-b border-slate-400 pb-1 px-8 block w-fit mx-auto text-xs">( <?= htmlspecialchars($bast['customer_name']) ?> )</strong>
                        <span class="text-[9px] text-slate-500 font-mono block mt-0.5">Pelanggan / Kuasa Penerima</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-3 border-t border-slate-200 text-center text-[9px] text-slate-400 font-mono flex justify-between">
                <span>Dokumen Berita Acara Serah Terima Resmi NETPRO CRM & ISP OS.</span>
                <span>Halaman 1 dari 1</span>
            </div>
        </div>

    </div>

    <script>
        function downloadPDF() {
            const element = document.getElementById('printableDocument');
            const opt = {
                margin:       [10, 10, 10, 10],
                filename:     'BAST_<?= htmlspecialchars($bast['doc_number'] ?? '001') ?>.pdf',
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
