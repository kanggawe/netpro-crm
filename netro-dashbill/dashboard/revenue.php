<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Dashboard Billing & Pendapatan (MRR Intelligence)";
$page_subtitle = "Analisis arus kas masuk, rasio penagihan tagihan lunas vs tunggakan, dan distribusi kanal pembayaran.";
$active_menu = "m-dashboard";
require_once __DIR__ . '/../includes/header.php';

$invoices = Invoice::all();
$totalInv = count($invoices);
$paidCount = 0;
$unpaidCount = 0;
$paidAmount = 0;
$unpaidAmount = 0;

foreach ($invoices as $inv) {
    $invVal = floatval($inv['total_amount'] ?? ($inv['amount'] ?? 0));
    $status = strtolower($inv['status'] ?? 'unpaid');
    if ($status === 'lunas' || $status === 'paid') {
        $paidCount++;
        $paidAmount += $invVal;
    } else {
        $unpaidCount++;
        $unpaidAmount += $invVal;
    }
}
$displayPaid = $paidAmount;
$displayUnpaid = $unpaidAmount;
$collectionRate = ($displayPaid + $displayUnpaid > 0) ? round(($displayPaid / ($displayPaid + $displayUnpaid)) * 100, 1) : 0;
$arpu = $paidCount > 0 ? round($paidAmount / $paidCount) : ($totalInv > 0 ? round(($paidAmount + $unpaidAmount) / $totalInv) : 0);
$arr = $displayPaid * 12;
?>

<div class="space-y-8 text-xs pb-6">
    <!-- Top 4 Financial Metrics (RedDash KPI Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Monthly Recurring Revenue</span>
                    <strong class="text-2xl font-black text-slate-900 block mt-1"><?= format_rupiah($displayPaid) ?></strong>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-arrow-trend-up mr-1 text-[10px]"></i> <?= $collectionRate ?>%
                </span>
                <span class="text-slate-400 ml-2">Collection Rate</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Piutang Berjalan (Unpaid)</span>
                    <strong class="text-2xl font-black text-rose-600 block mt-1"><?= format_rupiah($displayUnpaid) ?></strong>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-rose-600 bg-rose-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-triangle-exclamation mr-1 text-[10px]"></i> <?= $unpaidCount ?> Invoice
                </span>
                <span class="text-slate-400 ml-2">belum bayar</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Rata-rata ARPU Pelanggan</span>
                    <strong class="text-2xl font-black text-slate-900 block mt-1"><?= format_rupiah($arpu) ?></strong>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-chart-line text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Optimal
                </span>
                <span class="text-slate-400 ml-2">per user / bulan</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm p-6 border border-slate-100 hover:shadow-xl transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block">Annual Run Rate (ARR)</span>
                    <strong class="text-2xl font-black text-slate-900 block mt-1"><?= format_rupiah($arr) ?></strong>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300 shadow-sm">
                    <i class="fa-solid fa-award text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs font-semibold">
                <span class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-xl font-bold flex items-center">
                    <i class="fa-solid fa-trophy mr-1 text-[10px]"></i> Proyeksi
                </span>
                <span class="text-slate-400 ml-2">tahunan berjalan</span>
            </div>
        </div>
    </div>

    <!-- Revenue Visual Chart Section -->
    <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-4">
            <div>
                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                    <i class="fa-solid fa-chart-area text-brand-600"></i> Analisis Performa Pendapatan Bulanan vs Target ISP
                </h3>
                <p class="text-slate-400 text-xs mt-0.5">Komparasi target tagihan bulanan terhadap realisasi pembayaran masuk.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-brand-50 text-brand-700 font-bold rounded-xl text-[10px] border border-brand-200 shadow-xs">
                    <i class="fa-solid fa-chart-line text-brand-600"></i> <?= $displayPaid > 0 ? 'TARGET TERCAPAI' : 'TARGET REVENUE' ?>
                </span>
            </div>
        </div>
        <div class="relative h-80 w-full pt-2">
            <canvas id="revAnalysisChart"></canvas>
        </div>
    </div>

    <!-- 2 Columns: Payment Channel Matrix & Invoice Aging -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Payment Channels Card -->
        <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-4">
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                            <i class="fa-solid fa-credit-card text-brand-600"></i> Distribusi Kanal Pembayaran Tagihan
                        </h3>
                        <p class="text-slate-400 text-xs mt-0.5">Rincian volume transaksi per metode pembayaran.</p>
                    </div>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-bold rounded-xl text-[10px]">
                        OTOMATIS SINKRON
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                    <div class="sm:col-span-5 relative h-48 flex items-center justify-center">
                        <canvas id="paymentPieChart"></canvas>
                    </div>
                    <div class="sm:col-span-7 space-y-2">
                        <div class="flex justify-between items-center p-2.5 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100/60 transition">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center font-black text-[10px] shrink-0">QRIS</div>
                                <div>
                                    <strong class="text-slate-900 block font-bold text-xs">QRIS Dinamis</strong>
                                    <span class="text-slate-400 text-[10px]">0 Transaksi Lunas</span>
                                </div>
                            </div>
                            <strong class="font-mono text-slate-900 text-xs">Rp 0 (0%)</strong>
                        </div>

                        <div class="flex justify-between items-center p-2.5 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100/60 transition">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center font-black text-[10px] shrink-0">BCA</div>
                                <div>
                                    <strong class="text-slate-900 block font-bold text-xs">Virtual Account BCA</strong>
                                    <span class="text-slate-400 text-[10px]">0 Transaksi Lunas</span>
                                </div>
                            </div>
                            <strong class="font-mono text-slate-900 text-xs">Rp 0 (0%)</strong>
                        </div>

                        <div class="flex justify-between items-center p-2.5 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100/60 transition">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center font-black text-[10px] shrink-0">MDR</div>
                                <div>
                                    <strong class="text-slate-900 block font-bold text-xs">Virtual Account Mandiri</strong>
                                    <span class="text-slate-400 text-[10px]">0 Transaksi Lunas</span>
                                </div>
                            </div>
                            <strong class="font-mono text-slate-900 text-xs">Rp 0 (0%)</strong>
                        </div>

                        <div class="flex justify-between items-center p-2.5 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100/60 transition">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center font-black text-[10px] shrink-0">CASH</div>
                                <div>
                                    <strong class="text-slate-900 block font-bold text-xs">Kasir Tunai & Kolektor</strong>
                                    <span class="text-slate-400 text-[10px]">0 Transaksi Lunas</span>
                                </div>
                            </div>
                            <strong class="font-mono text-slate-900 text-xs">Rp 0 (0%)</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-[11px] text-slate-400">
                <span>Total <?= $totalInv ?> Transaksi Masuk</span>
                <span class="font-mono font-bold text-slate-700">Total: <?= format_rupiah($displayPaid) ?></span>
            </div>
        </div>

        <!-- Aging Matrix Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-hourglass-half text-amber-600"></i> Matriks Umur Piutang (Invoice Aging)
                        </h3>
                        <p class="text-slate-400 text-[11px]">Monitoring siklus dunning dan pencegahan piutang macet.</p>
                    </div>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 font-bold rounded-full text-[10px] border border-slate-200">
                        MONITORING DUNNING
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex justify-between items-center hover:bg-slate-100/60 transition">
                        <div>
                            <strong class="text-slate-800 block font-bold text-xs">0 - 7 Hari (Belum Jatuh Tempo)</strong>
                            <span class="text-slate-400 text-[10px]">0 Akun siap auto-debet & bayar tepat waktu</span>
                        </div>
                        <strong class="font-mono text-slate-700 text-sm">Rp 0</strong>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex justify-between items-center hover:bg-slate-100/60 transition">
                        <div>
                            <strong class="text-slate-800 block font-bold text-xs">8 - 14 Hari (Reminder WhatsApp 1)</strong>
                            <span class="text-slate-400 text-[10px]">0 Akun terkirim invoice reminder otomatis</span>
                        </div>
                        <strong class="font-mono text-slate-700 text-sm">Rp 0</strong>
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl flex justify-between items-center hover:bg-slate-100/60 transition">
                        <div>
                            <strong class="text-slate-800 block font-bold text-xs">> 15 Hari (Siap Auto-Isolir Sistem)</strong>
                            <span class="text-slate-400 text-[10px]">0 Akun masuk antrian isolir MikroTik NOC</span>
                        </div>
                        <strong class="font-mono text-slate-700 text-sm">Rp 0</strong>
                    </div>
                </div>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-between items-center text-[11px] text-slate-400">
                <span>Total <?= $unpaidCount ?> Akun dalam Dunning Cycle</span>
                <span class="font-mono font-bold text-slate-700">Total Piutang: <?= format_rupiah($displayUnpaid) ?></span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Revenue Performance vs Target Chart
    var ctx1 = document.getElementById('revAnalysisChart');
    if (ctx1) {
        var chartContext = ctx1.getContext('2d');
        
        // Gradient fill for bars
        var blueGradient = chartContext.createLinearGradient(0, 0, 0, 300);
        blueGradient.addColorStop(0, '#2563eb');
        blueGradient.addColorStop(1, '#60a5fa');

        new Chart(chartContext, {
            data: {
                labels: ['Jan 2026', 'Feb 2026', 'Mar 2026', 'Apr 2026', 'Mei 2026', 'Jun 2026'],
                datasets: [
                    {
                        type: 'line',
                        label: 'Target KPI Revenue (Juta Rp)',
                        data: [0, 0, 0, 0, 0, 0],
                        borderColor: '#f59e0b',
                        backgroundColor: '#f59e0b',
                        borderWidth: 2.5,
                        borderDash: [6, 4],
                        tension: 0.35,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        order: 1
                    },
                    {
                        type: 'bar',
                        label: 'Realisasi Pendapatan Masuk (Juta Rp)',
                        data: [0, 0, 0, 0, 0, 0],
                        backgroundColor: blueGradient,
                        hoverBackgroundColor: '#1d4ed8',
                        borderRadius: 8,
                        barPercentage: 0.5,
                        categoryPercentage: 0.65,
                        order: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { family: 'Inter', size: 11, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 11 },
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(ctx) { 
                                return ' ' + ctx.dataset.label + ': Rp ' + ctx.raw + ' Juta'; 
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11, weight: '500' } }
                    },
                    y: {
                        border: { dash: [4, 4] },
                        grid: { color: '#f1f5f9' },
                        ticks: { 
                            font: { family: 'Inter', size: 11 },
                            callback: function(val) { return 'Rp ' + val + 'M'; } 
                        }
                    }
                }
            }
        });
    }

    // 2. Payment Channel Doughnut Chart
    var ctx2 = document.getElementById('paymentPieChart');
    if (ctx2) {
        new Chart(ctx2.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['QRIS Dinamis', 'VA BCA', 'VA Mandiri', 'Kasir Tunai'],
                datasets: [{
                    data: [0, 0, 0, 0],
                    backgroundColor: ['#f43f5e', '#3b82f6', '#6366f1', '#10b981'],
                    hoverBackgroundColor: ['#e11d48', '#2563eb', '#4f46e5', '#059669'],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 11 },
                        padding: 10,
                        cornerRadius: 10,
                        callbacks: {
                            label: function(ctx) { return ' ' + ctx.label + ': ' + ctx.raw + '%'; }
                        }
                    }
                },
                cutout: '72%'
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
