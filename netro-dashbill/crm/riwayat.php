<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Riwayat Langganan & Aktivitas Pelanggan";
$page_subtitle = "Log histori upgrade/downgrade paket, penggantian modem ONT, mutasi alamat, dan rekam isolir.";
$active_menu = "m-crm";
require_once __DIR__ . '/../includes/header.php';

$customers = Customer::all();
?>

<div class="space-y-6 text-xs">
    <!-- Top 4 Summary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Aktivitas Bulan Ini</span>
                <strong class="text-2xl font-bold text-slate-900">0 Event</strong>
                <span class="text-blue-600 font-bold block mt-0.5">▲ Log Sistem Terpusat</span>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-clock-rotate-left"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Upgrade Bandwidth</span>
                <strong class="text-2xl font-bold text-emerald-600">0 Akun</strong>
                <span class="text-slate-400 font-medium block mt-0.5">Perubahan Paket</span>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-arrow-trend-up"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Rotasi ONT / Drop Wire</span>
                <strong class="text-2xl font-bold text-indigo-600">0 Unit</strong>
                <span class="text-slate-400 block mt-0.5">Pemeliharaan Lapangan</span>
            </div>
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-arrows-rotate"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Otomatisasi Isolir</span>
                <strong class="text-2xl font-bold text-rose-600">0 Event</strong>
                <span class="text-slate-400 font-medium block mt-0.5">MikroTik Dunning Trigger</span>
            </div>
            <div class="w-11 h-11 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-user-slash"></i></div>
        </div>
    </div>

    <!-- Subscription History Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Log Riwayat Langganan & Modifikasi Layanan Pelanggan</h3>
                <p class="text-slate-400">Rekam jejak setiap perubahan status akun, upgrade kecepatan, dan pergantian perangkat.</p>
            </div>
            <div class="flex gap-2">
                <input type="text" placeholder="Cari ID Pelanggan / Nama..." class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 font-medium">
                <button onclick="window.print()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i> Cetak Log
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Waktu Kejadian</th>
                        <th class="py-3 px-4">Pelanggan (CID)</th>
                        <th class="py-3 px-4">Kategori Event</th>
                        <th class="py-3 px-4">Rincian Perubahan Layanan</th>
                        <th class="py-3 px-4">Eksekutor / Sistem</th>
                        <th class="py-3 px-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-slate-50">
                        <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada riwayat aktivitas pelanggan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
