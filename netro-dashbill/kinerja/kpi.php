<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Master Bobot KPI per Divisi ISP";
$page_subtitle = "Konfigurasi Key Performance Indicators (KPI) dan indikator keberhasilan operasional per departemen.";
$active_menu = "m-kinerja";
require_once __DIR__ . '/../includes/header.php';

$kpis = KpiIndicator::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created_kpi'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Parameter KPI baru berhasil ditambahkan ke database!
    </div>
<?php elseif ($msg === 'deleted_kpi'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-trash-can text-rose-600 text-sm"></i>
        Parameter KPI telah berhasil dihapus.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <!-- Top 4 Division KPI Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h4 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-screwdriver-wrench text-blue-600"></i> Teknisi Lapangan
                </h4>
                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Bobot 100%</span>
            </div>
            <div class="space-y-2 text-slate-700">
                <div class="flex justify-between"><span>SLA Pasang Baru (< 24 Jam)</span><strong class="font-bold text-slate-900">35%</strong></div>
                <div class="flex justify-between"><span>Redaman Optik (< -20dBm)</span><strong class="font-bold text-slate-900">35%</strong></div>
                <div class="flex justify-between"><span>Rating CSAT Pelanggan</span><strong class="font-bold text-slate-900">20%</strong></div>
                <div class="flex justify-between"><span>Kepatuhan APD / K3</span><strong class="font-bold text-slate-900">10%</strong></div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h4 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-server text-indigo-600"></i> NOC & Core Network
                </h4>
                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded text-[10px]">Bobot 100%</span>
            </div>
            <div class="space-y-2 text-slate-700">
                <div class="flex justify-between"><span>Network Uptime (> 99.9%)</span><strong class="font-bold text-slate-900">45%</strong></div>
                <div class="flex justify-between"><span>MTTR Resolusi Outage</span><strong class="font-bold text-slate-900">30%</strong></div>
                <div class="flex justify-between"><span>Auto-Backup Rutin</span><strong class="font-bold text-slate-900">15%</strong></div>
                <div class="flex justify-between"><span>Laporan BGP Peering</span><strong class="font-bold text-slate-900">10%</strong></div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h4 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-headset text-rose-600"></i> Customer Service
                </h4>
                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 font-bold rounded text-[10px]">Bobot 100%</span>
            </div>
            <div class="space-y-2 text-slate-700">
                <div class="flex justify-between"><span>First Response (< 3 Min)</span><strong class="font-bold text-slate-900">40%</strong></div>
                <div class="flex justify-between"><span>First Contact Res. (FCR)</span><strong class="font-bold text-slate-900">35%</strong></div>
                <div class="flex justify-between"><span>Rating CSAT Bintang</span><strong class="font-bold text-slate-900">15%</strong></div>
                <div class="flex justify-between"><span>Eskalasi Tepat Waktu</span><strong class="font-bold text-slate-900">10%</strong></div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h4 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                    <i class="fa-solid fa-bullhorn text-amber-600"></i> Sales & Marketing
                </h4>
                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 font-bold rounded text-[10px]">Bobot 100%</span>
            </div>
            <div class="space-y-2 text-slate-700">
                <div class="flex justify-between"><span>Akuisisi Pelanggan FTTH</span><strong class="font-bold text-slate-900">50%</strong></div>
                <div class="flex justify-between"><span>Konversi Leads (> 65%)</span><strong class="font-bold text-slate-900">25%</strong></div>
                <div class="flex justify-between"><span>Retensi & Low Churn</span><strong class="font-bold text-slate-900">15%</strong></div>
                <div class="flex justify-between"><span>Broadcast Promo Baru</span><strong class="font-bold text-slate-900">10%</strong></div>
            </div>
        </div>
    </div>

    <!-- Live KPI Database Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Database Parameter KPI Aktif</h3>
                <p class="text-slate-400">Total <?= count($kpis) ?> Parameter KPI terdaftar untuk evaluasi bonus & review 360°.</p>
            </div>
            <button onclick="document.getElementById('modalAddKpi').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Tambah Parameter KPI
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Divisi Kerja</th>
                        <th class="py-3 px-4">Indikator Kinerja</th>
                        <th class="py-3 px-4 text-center">Target Standar</th>
                        <th class="py-3 px-4 font-mono text-center">Bobot</th>
                        <th class="py-3 px-4">Metode Pengukuran</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kpis as $k): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($k['division']) ?></td>
                        <td class="py-3.5 px-4 font-medium text-slate-700"><?= htmlspecialchars($k['name']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-center text-blue-600"><?= htmlspecialchars($k['target']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-center text-slate-900"><?= $k['weight'] ?>%</td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($k['method']) ?></td>
                        <td class="py-3.5 px-4 text-right">
                            <form action="<?= base_url('api/handler.php') ?>" method="POST" onsubmit="return confirm('Hapus parameter KPI ini?')" class="inline">
                                <input type="hidden" name="action" value="delete_kpi">
                                <input type="hidden" name="id" value="<?= $k['id'] ?>">
                                <input type="hidden" name="redirect" value="kinerja/kpi.php">
                                <button type="submit" class="text-rose-600 font-bold hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah KPI -->
<div id="modalAddKpi" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm">Tambah Parameter KPI Baru</h3>
            <button onclick="document.getElementById('modalAddKpi').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-3">
            <input type="hidden" name="action" value="create_kpi">
            <input type="hidden" name="redirect" value="kinerja/kpi.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Divisi Kerja</label>
                <select name="division" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                    <option>Teknisi Lapangan</option>
                    <option>NOC & Jaringan</option>
                    <option>Customer Support</option>
                    <option>Sales & Marketing</option>
                </select>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Indikator KPI</label>
                <input type="text" name="name" required placeholder="Contoh: Kualitas Sambungan OTDR" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Target Standar</label>
                    <input type="text" name="target" required placeholder="< -20 dBm" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Bobot (%)</label>
                    <input type="number" name="weight" required value="25" min="1" max="100" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold font-mono">
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Metode Pengukuran</label>
                <input type="text" name="method" placeholder="Log Sistem / BAST" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow">Simpan Parameter KPI</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
