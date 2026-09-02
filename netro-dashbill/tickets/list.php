<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Daftar Tiket Gangguan & Incident Management";
$page_subtitle = "Pusat komando pemantauan tiket insiden teknis pelanggan, penugasan teknisi lapangan, dan update hasil kerja perbaikan.";
$active_menu = "m-tickets";
require_once __DIR__ . '/../includes/header.php';

$tickets = Ticket::all();
$customers = Customer::all();
$employees = Employee::all();
$filterCustId = intval($_GET['customer_id'] ?? ($_GET['id'] ?? 0));
$filterCustomer = $filterCustId > 0 ? Customer::find($filterCustId) : null;
if ($filterCustId > 0) {
    $filteredTickets = [];
    foreach ($tickets as $t) {
        if (($t['customer_id'] ?? 0) == $filterCustId) {
            $filteredTickets[] = $t;
        }
    }
    $tickets = $filteredTickets;
}
$msg = $_GET['msg'] ?? '';

// Calculate stats
$totalTickets = count($tickets);
$openTickets = 0;
$inProgressTickets = 0;
$closedTickets = 0;
$highPriorityTickets = 0;

foreach ($tickets as $t) {
    $st = strtoupper($t['status'] ?? 'OPEN');
    if ($st === 'CLOSED' || $st === 'SELESAI') {
        $closedTickets++;
    } elseif ($st === 'IN_PROGRESS' || $st === 'PROSES') {
        $inProgressTickets++;
    } else {
        $openTickets++;
    }
    if (strtoupper($t['priority'] ?? '') === 'HIGH') {
        $highPriorityTickets++;
    }
}
?>

<?php if ($filterCustomer): ?>
    <div class="p-3.5 bg-blue-50 text-blue-900 border border-blue-200 rounded-2xl flex items-center justify-between text-xs font-bold shadow-sm mb-6">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-filter text-blue-600"></i>
            <span>Menampilkan tiket gangguan khusus pelanggan: <strong><?= htmlspecialchars($filterCustomer['name']) ?></strong> (<?= htmlspecialchars($filterCustomer['cid'] ?? '') ?>)</span>
        </div>
        <a href="<?= base_url('tickets/list.php') ?>" class="px-2.5 py-1 bg-white border border-blue-300 text-blue-700 hover:bg-blue-100 rounded-lg text-[11px] font-semibold">
            ✕ Tampilkan Semua Tiket
        </a>
    </div>
<?php endif; ?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-sm mb-6">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
        <span>Tiket gangguan baru berhasil diterbitkan & teknisi lapangan telah ditugaskan secara real-time!</span>
    </div>
<?php elseif ($msg === 'ticket_resolved'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-sm mb-6">
        <i class="fa-solid fa-clipboard-check text-emerald-600 text-base"></i>
        <span>Laporan hasil kerjaan teknisi & status resolusi tiket berhasil disimpan ke sistem!</span>
    </div>
<?php elseif ($msg === 'updated'): ?>
    <div class="p-4 bg-blue-50 text-blue-800 border border-blue-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-sm mb-6">
        <i class="fa-solid fa-check-double text-blue-600 text-base"></i>
        <span>Status tiket insiden berhasil diperbarui!</span>
    </div>
<?php elseif ($msg === 'tech_assigned'): ?>
    <div class="p-4 bg-indigo-50 text-indigo-800 border border-indigo-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-sm mb-6">
        <i class="fa-solid fa-user-gear text-indigo-600 text-base"></i>
        <span>Teknisi lapangan berhasil ditugaskan ke tiket insiden!</span>
    </div>
<?php elseif ($msg === 'deleted'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-sm mb-6">
        <i class="fa-solid fa-trash-can text-rose-600 text-base"></i>
        <span>Tiket insiden telah berhasil dihapus dari sistem.</span>
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <!-- Top 4 Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block tracking-wider">Total Tiket Masuk</span>
                <strong class="text-2xl font-bold text-slate-900"><?= $totalTickets ?></strong>
                <span class="text-blue-600 block text-[11px] font-semibold">Semua Riwayat Insiden</span>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-ticket"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block tracking-wider">Antrean Terbuka (Open)</span>
                <strong class="text-2xl font-bold text-rose-600"><?= $openTickets ?></strong>
                <span class="text-rose-600 block text-[11px] font-bold">● Butuh Penanganan NOC</span>
            </div>
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block tracking-wider">Dalam Penanganan</span>
                <strong class="text-2xl font-bold text-amber-600"><?= $inProgressTickets ?></strong>
                <span class="text-amber-600 block text-[11px] font-semibold">Teknisi di Lapangan</span>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block tracking-wider">Terselesaikan (Closed)</span>
                <strong class="text-2xl font-bold text-emerald-600"><?= $closedTickets ?></strong>
                <span class="text-emerald-600 block text-[11px] font-semibold">SLA 98.4% Compliance</span>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2/3: Ticket Master Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-blue-600"></i> Matriks Tiket Insiden & Pemeliharaan
                    </h3>
                    <p class="text-slate-400 text-xs">Daftar keluhan gangguan jaringan aktif dan riwayat perbaikan teknis.</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" id="searchTicketInput" onkeyup="filterTicketTable()" placeholder="Cari No Tiket / Pelanggan..." class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 w-48 sm:w-56">
                </div>
            </div>

            <!-- Filter Badges -->
            <div class="flex flex-wrap gap-2 pb-1">
                <button onclick="filterTicketStatus('ALL')" class="ticket-tab-btn active px-3 py-1 bg-slate-900 text-white rounded-lg font-bold transition">Semua (<?= $totalTickets ?>)</button>
                <button onclick="filterTicketStatus('OPEN')" class="ticket-tab-btn px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold transition">Open (<?= $openTickets ?>)</button>
                <button onclick="filterTicketStatus('IN_PROGRESS')" class="ticket-tab-btn px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold transition">In-Progress (<?= $inProgressTickets ?>)</button>
                <button onclick="filterTicketStatus('CLOSED')" class="ticket-tab-btn px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-bold transition">Closed (<?= $closedTickets ?>)</button>
            </div>

            <div class="overflow-x-auto border border-slate-100 rounded-xl">
                <table class="w-full text-left border-collapse" id="ticketTable">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-[11px]">
                            <th class="py-3 px-4">No Tiket</th>
                            <th class="py-3 px-4">Pelanggan & Paket</th>
                            <th class="py-3 px-4">Kategori & Prioritas</th>
                            <th class="py-3 px-4">Teknisi / SLA</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi & Hasil Kerja</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($tickets)): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-inbox text-3xl mb-2 block opacity-40"></i>
                                    Belum ada tiket gangguan yang terdaftar.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tickets as $t): 
                                $status = strtoupper($t['status'] ?? 'OPEN');
                                $priority = strtoupper($t['priority'] ?? 'MEDIUM');
                                $cleanPhone = preg_replace('/[^0-9]/', '', $t['customer_phone'] ?? '');
                                if (substr($cleanPhone, 0, 1) === '0') {
                                    $cleanPhone = '62' . substr($cleanPhone, 1);
                                }
                            ?>
                            <tr class="hover:bg-slate-50/70 transition ticket-row" data-status="<?= $status ?>" data-search="<?= strtolower(($t['ticket_no'] ?? '') . ' ' . ($t['customer_name'] ?? '') . ' ' . ($t['category'] ?? '') . ' ' . ($t['assigned_tech'] ?? '')) ?>">
                                <td class="py-3.5 px-4">
                                    <span class="font-mono font-bold text-blue-600 block"><?= htmlspecialchars($t['ticket_no'] ?? 'TCK-000') ?></span>
                                    <span class="text-[10px] text-slate-400 font-mono"><?= date('d M Y H:i', strtotime($t['created_at'] ?? 'now')) ?></span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php if (!empty($t['customer_id'])): ?>
                                        <a href="<?= base_url('crm/detail.php?id=' . $t['customer_id']) ?>" class="font-bold text-slate-900 block hover:text-blue-600 hover:underline">
                                            <?= htmlspecialchars($t['customer_name'] ?? 'Pelanggan') ?>
                                        </a>
                                    <?php else: ?>
                                        <strong class="font-bold text-slate-900 block"><?= htmlspecialchars($t['customer_name'] ?? 'Pelanggan') ?></strong>
                                    <?php endif; ?>
                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-400">
                                        <span class="font-mono"><?= htmlspecialchars($t['cid'] ?? '-') ?></span>
                                        <span>•</span>
                                        <span class="text-indigo-600 font-semibold"><?= htmlspecialchars($t['package_name'] ?? 'Paket Internet') ?></span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-medium text-slate-800 block"><?= htmlspecialchars($t['category'] ?? 'Gangguan Koneksi') ?></span>
                                    <?php if ($priority === 'HIGH'): ?>
                                        <span class="inline-block px-2 py-0.5 bg-rose-100 text-rose-700 font-bold rounded text-[9px] mt-0.5">HIGH PRIORITY</span>
                                    <?php elseif ($priority === 'LOW'): ?>
                                        <span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-600 font-bold rounded text-[9px] mt-0.5">LOW</span>
                                    <?php else: ?>
                                        <span class="inline-block px-2 py-0.5 bg-amber-100 text-amber-700 font-bold rounded text-[9px] mt-0.5">MEDIUM</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[9px] font-bold">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                        <span class="font-semibold text-slate-700"><?= htmlspecialchars($t['assigned_tech'] ?? 'Teknisi Standby') ?></span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">SLA: <?= intval($t['sla_minutes'] ?? 120) ?> Menit Target</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php if ($status === 'CLOSED' || $status === 'SELESAI'): ?>
                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold rounded-full text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> CLOSED
                                        </span>
                                    <?php elseif ($status === 'IN_PROGRESS' || $status === 'PROSES'): ?>
                                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 font-bold rounded-full text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-arrows-rotate fa-spin text-[9px]"></i> IN PROGRESS
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 font-bold rounded-full text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-clock text-[9px]"></i> OPEN
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- WhatsApp Direct Link -->
                                        <?php if (!empty($cleanPhone)): ?>
                                        <a href="https://wa.me/<?= $cleanPhone ?>?text=Halo%20<?= urlencode($t['customer_name'] ?? '') ?>,%20terkait%20tiket%20insiden%20<?= $t['ticket_no'] ?>" target="_blank" title="WhatsApp Pelanggan" class="w-7 h-7 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg flex items-center justify-center transition">
                                            <i class="fa-brands fa-whatsapp"></i>
                                        </a>
                                        <?php endif; ?>

                                        <!-- Update Hasil Kerja Modal Trigger -->
                                        <button onclick='openResolveModal(<?= json_encode($t) ?>)' title="Input / Perbarui Hasil Kerja Teknisi" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg font-bold flex items-center gap-1 transition">
                                            <i class="fa-solid fa-clipboard-check text-[11px]"></i>
                                            <span>Hasil Kerja</span>
                                        </button>

                                        <!-- Delete Button -->
                                        <form action="<?= base_url('api/handler.php') ?>" method="POST" onsubmit="return confirm('Hapus tiket ini secara permanen?')" class="inline">
                                            <input type="hidden" name="action" value="delete_ticket">
                                            <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                            <input type="hidden" name="redirect" value="tickets/list.php">
                                            <button type="submit" title="Hapus Tiket" class="w-7 h-7 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg flex items-center justify-center transition">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right 1/3: Form Buka Tiket Baru -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-headset text-rose-600"></i> Buka Tiket Insiden Baru
                </h3>
                <p class="text-slate-400 text-xs">Penerbitan tiket gangguan teknis dan penugasan langsung ke teknisi lapangan.</p>
            </div>
            
            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create_ticket">
                <input type="hidden" name="redirect" value="tickets/list.php">

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Pilih Pelanggan Pelapor</label>
                    <select name="customer_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-blue-500">
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($c['id'] == $filterCustId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['cid']) ?> - <?= htmlspecialchars($c['address']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kategori Masalah Jaringan</label>
                    <select name="category" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-medium text-slate-800 focus:outline-none focus:border-blue-500">
                        <option value="Redaman Optik Loss / High dBm">Redaman Optik Loss / High dBm (> -25 dBm)</option>
                        <option value="Kabel Dropcore Putus / Terjepit">Kabel Dropcore Putus / Terjepit Tiang</option>
                        <option value="Lampu Indikator LOS Merah (Modem ONT)">Lampu Indikator LOS Merah (Modem ONT)</option>
                        <option value="Internet Lambat / Bandwidth Drop">Internet Lambat / Bandwidth Drop</option>
                        <option value="Router WiFi Error / Reset Pabrik">Router WiFi Error / Reset Pabrik</option>
                        <option value="Billing & Pembayaran Belum Sync">Billing & Pembayaran Belum Sync</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Priority SLA</label>
                        <select name="priority" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-blue-500">
                            <option value="HIGH">HIGH (Urgent)</option>
                            <option value="MEDIUM" selected>MEDIUM (Standard)</option>
                            <option value="LOW">LOW (Minor)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Target SLA (Menit)</label>
                        <input type="number" name="sla_minutes" value="120" min="30" step="15" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold font-mono text-blue-600 focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Penugasan Teknisi Lapangan</label>
                    <select name="assigned_tech" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-blue-500">
                        <?php if (empty($employees)): ?>
                            <option value="Teknisi Standby NOC">Teknisi Standby NOC</option>
                        <?php else: ?>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['position']) ?>)">
                                    <?= htmlspecialchars($emp['name']) ?> - <?= htmlspecialchars($emp['position']) ?> (<?= htmlspecialchars($emp['division']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Terbitkan Tiket Insiden
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================= -->
<!-- MODAL: UPDATE LAPORAN HASIL KERJA TEKNISI & RESOLUSI   -->
<!-- ======================================================= -->
<div id="modalResolveTicket" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-xl w-full p-6 space-y-5 text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-check text-blue-600"></i> Laporan Hasil Kerja & Penyelesaian Tiket
                </h3>
                <p class="text-slate-400">Pencatatan tindakan teknis lapangan, perbaikan redaman optik, dan status akhir.</p>
            </div>
            <button type="button" onclick="closeResolveModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center font-bold">
                ✕
            </button>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="resolve_ticket">
            <input type="hidden" name="id" id="modalTicketId" value="">
            <input type="hidden" name="redirect" value="tickets/list.php">

            <!-- Ticket Info Pill -->
            <div class="p-3.5 bg-blue-50/70 border border-blue-100 rounded-2xl flex justify-between items-center">
                <div>
                    <span class="text-[10px] text-blue-600 font-bold uppercase tracking-wider block">Nomor & Pelanggan:</span>
                    <strong id="modalTicketTitle" class="text-sm font-bold text-slate-900 block">-</strong>
                </div>
                <span id="modalTicketCategory" class="px-2.5 py-1 bg-blue-600 text-white font-bold rounded-lg text-[10px]">-</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Status Akhir Tiket</label>
                    <select name="status" id="modalTicketStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-blue-500">
                        <option value="CLOSED">CLOSED (Selesai / Pulih Normal)</option>
                        <option value="IN_PROGRESS">IN_PROGRESS (Perlu Kunjungan Ulang)</option>
                        <option value="OPEN">OPEN (Eskalasi ke Core NOC)</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Redaman Optik Akhir (dBm)</label>
                    <input type="text" name="final_attenuation" id="modalFinalAttenuation" placeholder="Contoh: -18.2 dBm" value="-18.5 dBm" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold font-mono text-emerald-600 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Akar Masalah Gangguan (Root Cause)</label>
                <select name="root_cause" id="modalRootCause" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-medium text-slate-800 focus:outline-none focus:border-blue-500">
                    <option value="Kabel Dropcore Terjepit / Tertekuk">Kabel Dropcore Terjepit / Tertekuk</option>
                    <option value="Kabel Dropcore Putus Tertimpa Ranting">Kabel Dropcore Putus Tertimpa Ranting Pohon</option>
                    <option value="Konektor Fast Connector / Adaptor ODP Kotor">Konektor Fast Connector / Adaptor ODP Kotor</option>
                    <option value="Modem ONT Hang / Port Power Drop">Modem ONT Hang / Port Power Drop</option>
                    <option value="Konfigurasi SSID & Password WiFi Reset">Konfigurasi SSID & Password WiFi Reset</option>
                    <option value="Splicing Core Feeder / Distribusi ODC">Splicing Core Feeder / Distribusi ODC</option>
                </select>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Tindakan Perbaikan yang Dilakukan (Action Taken)</label>
                <textarea name="action_taken" id="modalActionTaken" rows="2" placeholder="Contoh: Pemotongan kabel dropcore rusak 2 meter, pasang fast connector baru, redaman turun menjadi -18.2 dBm..." required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Teknisi Pelaksana</label>
                    <select name="assigned_tech" id="modalAssignedTech" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-blue-500">
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= htmlspecialchars($emp['name']) ?>">
                                <?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['position']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Catatan Tambahan Pelanggan</label>
                    <input type="text" name="resolution_notes" id="modalResolutionNotes" placeholder="Pelanggan sudah verifikasi speedtest" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-medium text-slate-800 focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeResolveModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition flex items-center gap-1.5">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Hasil Kerja & Selesaikan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openResolveModal(ticket) {
    document.getElementById('modalTicketId').value = ticket.id;
    document.getElementById('modalTicketTitle').innerText = ticket.ticket_no + ' - ' + (ticket.customer_name || 'Pelanggan');
    document.getElementById('modalTicketCategory').innerText = ticket.category || 'Insiden';
    
    if (ticket.status) {
        document.getElementById('modalTicketStatus').value = ticket.status.toUpperCase() === 'CLOSED' ? 'CLOSED' : 'CLOSED';
    }
    if (ticket.final_attenuation) {
        document.getElementById('modalFinalAttenuation').value = ticket.final_attenuation;
    }
    if (ticket.root_cause) {
        document.getElementById('modalRootCause').value = ticket.root_cause;
    }
    if (ticket.action_taken) {
        document.getElementById('modalActionTaken').value = ticket.action_taken;
    } else {
        document.getElementById('modalActionTaken').value = 'Pemeriksaan lapangan, penanganan kabel optik & pengujian speedtest.';
    }
    if (ticket.resolution_notes) {
        document.getElementById('modalResolutionNotes').value = ticket.resolution_notes;
    }

    document.getElementById('modalResolveTicket').classList.remove('hidden');
}

function closeResolveModal() {
    document.getElementById('modalResolveTicket').classList.add('hidden');
}

function filterTicketTable() {
    let input = document.getElementById('searchTicketInput').value.toLowerCase();
    let rows = document.querySelectorAll('.ticket-row');
    rows.forEach(row => {
        let text = row.getAttribute('data-search');
        if (text.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function filterTicketStatus(status) {
    document.querySelectorAll('.ticket-tab-btn').forEach(b => {
        b.classList.remove('bg-slate-900', 'text-white', 'active');
        b.classList.add('bg-slate-100', 'text-slate-700');
    });
    event.target.classList.remove('bg-slate-100', 'text-slate-700');
    event.target.classList.add('bg-slate-900', 'text-white', 'active');

    let rows = document.querySelectorAll('.ticket-row');
    rows.forEach(row => {
        let st = row.getAttribute('data-status');
        if (status === 'ALL' || st === status || (status === 'OPEN' && st === 'OPEN') || (status === 'IN_PROGRESS' && st === 'IN_PROGRESS') || (status === 'CLOSED' && (st === 'CLOSED' || st === 'SELESAI'))) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
