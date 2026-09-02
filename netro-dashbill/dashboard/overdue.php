<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Tagihan Jatuh Tempo (Overdue & Aging Piutang)";
$page_subtitle = "Daftar piutang pelanggan melebihi tanggal jatuh tempo & aksi pengiriman notifikasi.";
$active_menu = "m-dashboard";
require_once __DIR__ . '/../includes/header.php';

$invoices = Invoice::all();
$customers = Customer::all();

$overdueList = [];
$totalOverdueNominal = 0;
$gracePeriodCount = 0;
$gracePeriodNominal = 0;
$isolirCount = 0;
$isolirNominal = 0;
$dismantleCount = 0;
$dismantleNominal = 0;

$today = strtotime(date('Y-m-d'));

foreach ($invoices as $inv) {
    $st = strtolower($inv['status'] ?? 'unpaid');
    if ($st !== 'paid' && $st !== 'lunas') {
        $amt = floatval($inv['total_amount'] ?? ($inv['amount'] ?? 0));
        $due = !empty($inv['due_date']) ? strtotime($inv['due_date']) : ($today - 86400 * 5);
        $daysLate = max(1, floor(($today - $due) / 86400));

        $inv['days_late'] = $daysLate;
        $inv['overdue_amt'] = $amt;
        $overdueList[] = $inv;
        $totalOverdueNominal += $amt;

        if ($daysLate <= 7) {
            $gracePeriodCount++;
            $gracePeriodNominal += $amt;
        } elseif ($daysLate <= 30) {
            $isolirCount++;
            $isolirNominal += $amt;
        } else {
            $dismantleCount++;
            $dismantleNominal += $amt;
        }
    }
}
$totalOverdueCount = count($overdueList);
?>

<div id="view-dashboard-overdue" class="view-panel space-y-8 text-xs pb-6" data-title="Tagihan Jatuh Tempo (Overdue)" data-subtitle="Daftar piutang pelanggan melebihi tanggal jatuh tempo & aksi pengiriman notifikasi.">
    <!-- Top 4 Overdue KPI Cards (RedDash Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Total Piutang Overdue</span>
                    <p class="text-2xl font-black text-brand-600 mt-1"><?= format_rupiah($totalOverdueNominal) ?></p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-file-circle-exclamation text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-rose-600 bg-rose-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-users mr-1 text-[10px]"></i> <?= $totalOverdueCount ?> Akun
                </span>
                <span class="text-slate-400 ml-2">tertunggak</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">1 - 7 Hari (Grace Period)</span>
                    <p class="text-2xl font-black text-amber-600 mt-1"><?= $gracePeriodCount ?> Akun</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-user-clock text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-clock mr-1 text-[10px]"></i> Grace
                </span>
                <span class="text-slate-400 ml-2 font-mono"><?= format_rupiah($gracePeriodNominal) ?></span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">8 - 30 Hari (Isolir)</span>
                    <p class="text-2xl font-black text-rose-600 mt-1"><?= $isolirCount ?> Akun</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-user-slash text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-rose-600 bg-rose-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-ban mr-1 text-[10px]"></i> Terisolir
                </span>
                <span class="text-slate-400 ml-2 font-mono"><?= format_rupiah($isolirNominal) ?></span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">> 30 Hari (Dismantle)</span>
                    <p class="text-2xl font-black text-slate-900 mt-1"><?= $dismantleCount ?> Akun</p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-trash-can text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-slate-700 bg-slate-100 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-wrench mr-1 text-[10px]"></i> Dismantle
                </span>
                <span class="text-slate-400 ml-2 font-mono"><?= format_rupiah($dismantleNominal) ?></span>
            </div>
        </div>
    </div>

    <!-- Chart & WhatsApp Dispatcher Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white p-7 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-hourglass-half text-brand-600"></i> Matriks Aging Piutang Berjalan (Hari Keterlambatan)
                    </h3>
                    <p class="text-slate-400 text-xs mt-0.5">Distribusi akumulasi piutang & jumlah akun pelanggan tertunggak.</p>
                </div>
                <span class="px-3 py-1 bg-brand-50 text-brand-700 font-bold rounded-xl text-[10px] border border-brand-200">REAL DATABASE AGING</span>
            </div>
            <div class="relative h-56 w-full pt-1">
                <canvas id="overdueAgingChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-600"></i> Auto WhatsApp Dispatcher
                </h3>
                <p class="text-[11px] text-slate-400">Kirim reminder tagihan otomatis ke <?= $totalOverdueCount ?> akun yang belum bayar.</p>
            </div>
            <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 space-y-2">
                <p class="font-bold text-emerald-800 text-xs">Status Gateway WhatsApp</p>
                <p class="text-emerald-700 text-xs">Ready: <?= $totalOverdueCount ?> WhatsApp Messages siap diproses.</p>
                <a href="<?= base_url('billing/daftar.php?status=UNPAID') ?>" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl shadow block text-center transition">
                    💬 Kelola Tagihan & Blast Reminder WA
                </a>
            </div>
        </div>
    </div>

    <!-- Table of Overdue Invoices -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden space-y-3 p-6">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Tabel Tagihan Overdue & Status Pelanggan</h3>
                <p class="text-slate-400">Menampilkan seluruh tagihan aktif yang belum terselesaikan.</p>
            </div>
            <span class="px-3 py-1 bg-rose-50 text-rose-700 font-bold rounded-full text-[11px]">Total: <?= $totalOverdueCount ?> Tagihan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-[11px]">
                        <th class="py-3 px-4">No. Invoice</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Nominal</th>
                        <th class="py-3 px-4">Jatuh Tempo</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($overdueList)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-3xl mb-2 block"></i>
                                Tidak ada tagihan jatuh tempo. Seluruh pelanggan tertib bayar!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($overdueList as $ol): ?>
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($ol['inv_number'] ?? 'INV-000') ?></td>
                            <td class="py-3.5 px-4">
                                <strong class="font-bold text-slate-900 block"><?= htmlspecialchars($ol['customer_name'] ?? 'Pelanggan') ?></strong>
                                <span class="text-slate-400 text-[10px]"><?= htmlspecialchars($ol['package_name'] ?? '-') ?></span>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-red-600"><?= format_rupiah($ol['overdue_amt']) ?></td>
                            <td class="py-3.5 px-4 font-semibold text-amber-600"><?= htmlspecialchars($ol['due_date'] ?? 'Lewat') ?> (<?= $ol['days_late'] ?> Hari)</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-100 font-bold rounded-full text-[10px]">
                                    <?= htmlspecialchars($ol['status']) ?>
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <a href="<?= base_url('billing/pembayaran.php?inv_id=' . $ol['id']) ?>" class="text-emerald-600 font-bold hover:underline">Bayar Kasir</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('overdueAgingChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['1 - 7 Hari (Grace Period)', '8 - 30 Hari (Isolir MikroTik)', '> 30 Hari (Dismantle)'],
                datasets: [
                    {
                        type: 'bar',
                        label: 'Total Piutang (Rp)',
                        data: [<?= $gracePeriodNominal ?>, <?= $isolirNominal ?>, <?= $dismantleNominal ?>],
                        backgroundColor: ['#f59e0b', '#f43f5e', '#0f172a'],
                        borderRadius: 8,
                        yAxisID: 'yAmount',
                        barThickness: 36
                    },
                    {
                        type: 'line',
                        label: 'Jumlah Akun Pelanggan',
                        data: [<?= $gracePeriodCount ?>, <?= $isolirCount ?>, <?= $dismantleCount ?>],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 5,
                        yAxisID: 'yCount'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', align: 'end' }
                },
                scales: {
                    x: { grid: { display: false } },
                    yAmount: {
                        position: 'left',
                        ticks: { callback: function(v) { return 'Rp ' + (v/1000).toLocaleString('id-ID') + 'k'; } }
                    },
                    yCount: {
                        position: 'right',
                        grid: { display: false },
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
