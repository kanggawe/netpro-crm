<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Manajemen RouterOS Mikrotik API";
$page_subtitle = "Resource hardware CPU load, RAM usage, Simple Queues, dan status port interface.";
$active_menu = "m-noc";
require_once __DIR__ . '/../includes/header.php';
?>


<div class="space-y-6 text-xs">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block font-semibold uppercase">CPU Load Core</span>
            <strong class="text-2xl font-bold text-emerald-600">0%</strong>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden"><div class="bg-emerald-500 h-full w-[0%]"></div></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block font-semibold uppercase">Free Memory RAM</span>
            <strong class="text-2xl font-bold text-blue-600">0 GB</strong>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden"><div class="bg-blue-500 h-full w-[0%]"></div></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block font-semibold uppercase">Total Traffic Upstream</span>
            <strong class="text-2xl font-bold text-purple-600">0 Mbps</strong>
            <span class="text-[10px] text-slate-400 block mt-1">SFP+ 10G Port 1</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block font-semibold uppercase">Uptime RouterOS</span>
            <strong class="text-2xl font-bold text-slate-800">0 Hari</strong>
            <span class="text-[10px] text-slate-400 block mt-1">RouterOS API Disconnected</span>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <h3 class="font-bold text-slate-900 text-sm">Daftar Interface & Port Router Core</h3>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                    <th class="py-3 px-4">Interface Name</th>
                    <th class="py-3 px-4">Type</th>
                    <th class="py-3 px-4">IP Address Binding</th>
                    <th class="py-3 px-4">RX (Inbound)</th>
                    <th class="py-3 px-4">TX (Outbound)</th>
                    <th class="py-3 px-4">Link Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="py-6 text-center text-slate-400">Belum ada interface MikroTik terhubung via API.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>
