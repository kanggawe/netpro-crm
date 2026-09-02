<?php
require_once __DIR__ . '/../config/app.php';
$page_title = "REPORTS";
$page_subtitle = "Halaman modul radius/reports.html";
$active_menu = "m-radius";
require_once __DIR__ . '/../includes/header.php';
?>

<div id="view-radius-reports" class="view-panel space-y-6" data-title="REPORTS" data-subtitle="Halaman modul radius/reports.php">
                    
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 text-xs">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 block uppercase">Total Metric REPORTS</span>
                <p class="text-2xl font-bold text-slate-900">0</p>
                <span class="text-[11px] text-emerald-600 font-medium">▲ Standby Mode</span>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 block uppercase">Efisiensi Sync</span>
                <p class="text-2xl font-bold text-emerald-600">0%</p>
                <span class="text-[11px] text-slate-400 font-medium">Realtime Engine</span>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 block uppercase">Pending Review</span>
                <p class="text-2xl font-bold text-amber-600">0 Items</p>
                <span class="text-[11px] text-emerald-600 font-medium">Normal</span>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-1">
                <span class="text-xs font-semibold text-slate-500 block uppercase">Status Service</span>
                <p class="text-2xl font-bold text-blue-600">STANDBY</p>
                <span class="text-[11px] text-slate-400 font-medium">Tidak ada laporan aktif</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center text-xs">
            <input type="text" placeholder="Cari data REPORTS..." class="bg-slate-50 border border-slate-200 rounded-lg py-2 px-3 w-80">
            <button onclick="triggerToast('Tambah Data', 'Form tambah data dibuka.')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg shadow">+ Tambah Entri REPORTS</button>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden text-xs">
            <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-bold text-sm text-slate-900">Database Module: RADIUS / REPORTS</h3>
                <span class="text-[10px] text-slate-400 font-mono">Modul radius/reports.php</span>
            </div>
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3.5 px-5">ID Referensi</th>
                        <th class="py-3.5 px-5">Deskripsi Entri REPORTS</th>
                        <th class="py-3.5 px-5">Modul Kategori</th>
                        <th class="py-3.5 px-5">Waktu Update</th>
                        <th class="py-3.5 px-5">Status Badge</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-50">
                        <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada data laporan RADIUS di database.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    
                </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
