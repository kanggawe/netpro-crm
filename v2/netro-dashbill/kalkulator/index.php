<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Kalkulator Terpadu ISP: Bandwidth & Pajak";
$page_subtitle = "Alat hitung praktis estimasi kapasitas bandwidth, CIR contention ratio, PPN 11%/12%, dan iuran Kominfo.";
$active_menu = "m-kalkulator";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="space-y-6 text-xs">
    <!-- Selection Hub Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Bandwidth Calculator Hub -->
        <a href="<?= base_url('kalkulator/bandwidth.php') ?>" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-500 transition space-y-4 group block">
            <div class="flex justify-between items-center">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-gauge-high"></i>
                </div>
                <span class="text-blue-600 font-bold flex items-center gap-1 group-hover:translate-x-1 transition">
                    Buka Kalkulator <i class="fa-solid fa-arrow-right"></i>
                </span>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base group-hover:text-blue-600 transition">Kalkulator Bandwidth & Kapasitas Jaringan</h3>
                <p class="text-slate-500 mt-1">Hitung kebutuhan pipa IP Transit/Upstream, rasio berbagi (CIR 1:1 vs MIR 1:8), peak concurrency user, dan power budget redaman optik GPON.</p>
            </div>
            <div class="flex gap-2 pt-2 border-t border-slate-100">
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold rounded-lg text-[11px]">CIR / MIR</span>
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold rounded-lg text-[11px]">IP Transit Estimator</span>
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold rounded-lg text-[11px]">Optical Loss dBm</span>
            </div>
        </a>

        <!-- Card 2: Tax Calculator Hub -->
        <a href="<?= base_url('kalkulator/pajak.php') ?>" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md hover:border-indigo-500 transition space-y-4 group block">
            <div class="flex justify-between items-center">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl group-hover:scale-110 transition">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <span class="text-indigo-600 font-bold flex items-center gap-1 group-hover:translate-x-1 transition">
                    Buka Kalkulator <i class="fa-solid fa-arrow-right"></i>
                </span>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base group-hover:text-indigo-600 transition">Kalkulator Pajak ISP & Regulasi Kominfo</h3>
                <p class="text-slate-500 mt-1">Simulasi otomatis pemecahan DPP dan PPN 11%/12% (Include vs Exclude), perhitungan e-Bupot PPh 23, serta Iuran PNBP Kominfo (USO 1.25% & BHP 0.50%).</p>
            </div>
            <div class="flex gap-2 pt-2 border-t border-slate-100">
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold rounded-lg text-[11px]">PPN 11% / 12%</span>
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold rounded-lg text-[11px]">e-Bupot PPh 23</span>
                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold rounded-lg text-[11px]">USO 1.25% & BHP 0.50%</span>
            </div>
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
