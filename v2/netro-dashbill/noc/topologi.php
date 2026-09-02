<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Topologi Jaringan & Backbone ISP";
$page_subtitle = "Visualisasi arsitektur routing BGP, Core Aggregation, OLT GPON, dan distribusi Optical Network.";
$active_menu = "m-noc";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="space-y-6 text-xs pb-8">
    <!-- Top Telemetry KPI Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Bandwidth Upstream</span>
                <strong class="font-extrabold text-slate-900 text-xl block mt-0.5">0.00 Gbps</strong>
                <span class="text-slate-400 text-[10px] font-semibold flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                    0 Gbps Terpakai (0%)
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">BGP Peering Sessions</span>
                <strong class="font-extrabold text-emerald-600 text-xl block mt-0.5">0 ESTABLISHED</strong>
                <span class="text-slate-500 text-[10px]">OpenIXP, CDIX, Telkom, Indosat</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-globe"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Perangkat Backbone Aktif</span>
                <strong class="font-extrabold text-slate-900 text-xl block mt-0.5">0 Nodes Online</strong>
                <span class="text-emerald-600 text-[10px] font-semibold">0 Node Down (100% Health)</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-server"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Rata-Rata Latensi Core</span>
                <strong class="font-extrabold text-indigo-600 text-xl font-mono block mt-0.5">0 ms</strong>
                <span class="text-slate-500 text-[10px]">Jitter 0ms &bull; 0% Packet Loss</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                <i class="fa-solid fa-bolt"></i>
            </div>
        </div>
    </div>

    <!-- Interactive Visual Network Topology Map Canvas -->
    <div class="bg-slate-950 p-6 rounded-3xl border border-slate-800 shadow-2xl text-white space-y-6 relative overflow-hidden">
        <!-- Background Grid Pattern -->
        <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:16px_16px] opacity-40 pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-800 pb-4 relative z-10">
            <div>
                <h3 class="font-bold text-white text-base flex items-center gap-2">
                    <i class="fa-solid fa-diagram-project text-blue-400"></i> Diagram Arsitektur Topologi End-to-End Jaringan ISP
                </h3>
                <p class="text-slate-400 text-xs">Hirarki koneksi dari Upstream Tier-1, Core BGP Router, Aggregation Switch, OLT GPON, hingga Box ODP Pelanggan.</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold rounded-full text-[11px] flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Live Backbone Synced
                </span>
                <button onclick="triggerToast('Refresh Topologi', 'Status link topologi backbone berhasil diperbarui secara real-time.')" class="bg-slate-800 hover:bg-slate-700 text-white font-bold px-3 py-1.5 rounded-xl border border-slate-700 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrows-rotate"></i> Ping All Nodes
                </button>
            </div>
        </div>

        <!-- Visual Hierarchy Topology Tree -->
        <div class="space-y-8 relative z-10 py-2">
            <!-- LEVEL 1: UPSTREAM INTERNET & PEERING EXCHANGE -->
            <div class="text-center space-y-3">
                <span class="text-[10px] text-slate-400 uppercase tracking-widest font-black bg-slate-900 px-3 py-1 rounded-full border border-slate-800">
                    LEVEL 1: UPSTREAM TRANSIT & INTERNET EXCHANGE (10G UPLINK)
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 max-w-4xl mx-auto">
                    <div class="p-3 bg-slate-900/90 rounded-2xl border border-slate-700/80 text-left hover:border-blue-500 transition shadow-md">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-bold text-blue-400 text-[11px]">OpenIXP (IDC 3D)</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        </div>
                        <span class="text-[10px] text-slate-400 block font-mono">10G SFP+ &bull; AS1313</span>
                        <strong class="text-white text-xs font-mono">0 Gbps / 0ms</strong>
                    </div>

                    <div class="p-3 bg-slate-900/90 rounded-2xl border border-slate-700/80 text-left hover:border-blue-500 transition shadow-md">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-bold text-blue-400 text-[11px]">CDIX Cyber 1</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        </div>
                        <span class="text-[10px] text-slate-400 block font-mono">10G SFP+ &bull; AS7713</span>
                        <strong class="text-white text-xs font-mono">0 Gbps / 0ms</strong>
                    </div>

                    <div class="p-3 bg-slate-900/90 rounded-2xl border border-slate-700/80 text-left hover:border-emerald-500 transition shadow-md">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-bold text-emerald-400 text-[11px]">Transit Telkom Tier-1</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        </div>
                        <span class="text-[10px] text-slate-400 block font-mono">10G Dedicated STM-64</span>
                        <strong class="text-white text-xs font-mono">0 Gbps / 0ms</strong>
                    </div>

                    <div class="p-3 bg-slate-900/90 rounded-2xl border border-slate-700/80 text-left hover:border-emerald-500 transition shadow-md">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-bold text-emerald-400 text-[11px]">Transit Indosat Ooredoo</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        </div>
                        <span class="text-[10px] text-slate-400 block font-mono">10G Dark Fiber Link</span>
                        <strong class="text-white text-xs font-mono">0 Gbps / 0ms</strong>
                    </div>
                </div>
            </div>

            <!-- Flow Arrow -->
            <div class="flex justify-center items-center text-blue-400">
                <i class="fa-solid fa-angles-down text-lg animate-bounce"></i>
            </div>

            <!-- LEVEL 2: BGP EDGE & CORE ROUTER HQ -->
            <div class="text-center space-y-3">
                <span class="text-[10px] text-slate-400 uppercase tracking-widest font-black bg-slate-900 px-3 py-1 rounded-full border border-slate-800">
                    LEVEL 2: CORE ROUTING & SUBSCRIBER MANAGEMENT
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-4xl mx-auto">
                    <!-- Router 1: BGP Gateway -->
                    <div class="p-4 bg-slate-900 rounded-2xl border-2 border-blue-500/80 text-left space-y-2 shadow-lg shadow-blue-500/10">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-shield-halved text-blue-400 text-base"></i>
                                <div>
                                    <strong class="block text-white font-bold text-xs">RT-BGP-EDGE-01</strong>
                                    <span class="text-[10px] text-slate-400">MikroTik CCR2216-1G-12XS-2XQ</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 font-bold rounded text-[9px]">ONLINE</span>
                        </div>
                        <div class="space-y-1 text-[10px] text-slate-300 font-mono">
                            <div class="flex justify-between"><span>IP Core:</span><strong>103.144.20.1</strong></div>
                            <div class="flex justify-between"><span>CPU Load:</span><strong class="text-emerald-400">0%</strong></div>
                            <div class="flex justify-between"><span>Throughput:</span><strong class="text-blue-400">0 Gbps</strong></div>
                        </div>
                    </div>

                    <!-- Router 2: PPPoE / Hotspot Core -->
                    <div class="p-4 bg-slate-900 rounded-2xl border-2 border-indigo-500/80 text-left space-y-2 shadow-lg shadow-indigo-500/10">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-server text-indigo-400 text-base"></i>
                                <div>
                                    <strong class="block text-white font-bold text-xs">RT-PPPOE-CORE-HQ</strong>
                                    <span class="text-[10px] text-slate-400">MikroTik CCR2004-1G-12S+2XS</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 font-bold rounded text-[9px]">ONLINE</span>
                        </div>
                        <div class="space-y-1 text-[10px] text-slate-300 font-mono">
                            <div class="flex justify-between"><span>Active Sesi:</span><strong class="text-indigo-400">0 PPPoE</strong></div>
                            <div class="flex justify-between"><span>CPU Load:</span><strong class="text-emerald-400">22%</strong></div>
                            <div class="flex justify-between"><span>RAM Bebas:</span><strong>3.2 GB / 4 GB</strong></div>
                        </div>
                    </div>

                    <!-- FreeRADIUS Engine -->
                    <div class="p-4 bg-slate-900 rounded-2xl border border-slate-700 text-left space-y-2 shadow-md">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-database text-purple-400 text-base"></i>
                                <div>
                                    <strong class="block text-white font-bold text-xs">SRV-RADIUS-AUTH</strong>
                                    <span class="text-[10px] text-slate-400">FreeRADIUS 3.2 + SQLite Engine</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-400 font-bold rounded text-[9px]">ONLINE</span>
                        </div>
                        <div class="space-y-1 text-[10px] text-slate-300 font-mono">
                            <div class="flex justify-between"><span>Auth Latency:</span><strong class="text-purple-400">1.8 ms</strong></div>
                            <div class="flex justify-between"><span>CoA Requests:</span><strong>0 Failures</strong></div>
                            <div class="flex justify-between"><span>Uptime:</span><strong>48 Hari 12 Jam</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flow Arrow -->
            <div class="flex justify-center items-center text-blue-400">
                <i class="fa-solid fa-angles-down text-lg animate-bounce"></i>
            </div>

            <!-- LEVEL 3: OLT GPON OPTICAL DISTRIBUTION -->
            <div class="text-center space-y-3">
                <span class="text-[10px] text-slate-400 uppercase tracking-widest font-black bg-slate-900 px-3 py-1 rounded-full border border-slate-800">
                    LEVEL 3: OLT GPON CHASSIS & OPTICAL LINE TERMINAL
                </span>
                <div class="p-6 bg-slate-900/60 rounded-2xl border border-slate-800 text-center text-slate-500 font-medium max-w-5xl mx-auto">
                    Belum ada perangkat OLT GPON terdaftar dalam topologi.
                </div>
            </div>

            <!-- Flow Arrow -->
            <div class="flex justify-center items-center text-blue-400">
                <i class="fa-solid fa-angles-down text-lg animate-bounce"></i>
            </div>

            <!-- LEVEL 4: ODC & ODP FIBER ACCESS NODES -->
            <div class="text-center space-y-3">
                <span class="text-[10px] text-slate-400 uppercase tracking-widest font-black bg-slate-900 px-3 py-1 rounded-full border border-slate-800">
                    LEVEL 4: DISTRIBUSI OPTICAL DISTRIBUTION CABINET (ODC) & BOX ODP
                </span>
                <div class="p-4 bg-slate-900/60 rounded-2xl border border-slate-800 text-center text-slate-500 font-medium max-w-4xl mx-auto">
                    Belum ada node ODC / ODP terhubung.
                </div>
            </div>
        </div>
    </div>

    <!-- Node Diagnostic & Interface Matrix Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-network-wired text-blue-600"></i> Matriks Port Trunking & Telemetri Antar Node
                </h3>
                <p class="text-slate-400 text-xs">Monitoring antarmuka SFP+ 10G dan optical transceiver backbone.</p>
            </div>
            <a href="<?= base_url('noc/odp.php') ?>" class="bg-blue-50 text-blue-700 font-bold px-3 py-1.5 rounded-xl border border-blue-200 text-xs hover:bg-blue-600 hover:text-white transition flex items-center gap-1.5">
                <i class="fa-solid fa-map-location-dot"></i> Buka Peta Sebaran ODP
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-[11px]">
                        <th class="py-3 px-4">Interface Port</th>
                        <th class="py-3 px-4">Dari Node (Source)</th>
                        <th class="py-3 px-4">Ke Node (Destination)</th>
                        <th class="py-3 px-4">Media Fisik</th>
                        <th class="py-3 px-4 font-mono">Throughput In / Out</th>
                        <th class="py-3 px-4 font-mono">Tx / Rx Power</th>
                        <th class="py-3 px-4 text-center">Status Link</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <tr>
                        <td colspan="7" class="py-6 text-center text-slate-400">Belum ada data port trunking & telemetri node terdaftar.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
