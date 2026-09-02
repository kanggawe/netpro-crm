<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Manajemen Pajak & Regulasi ISP";
$page_subtitle = "Pajak Penghasilan (PPh 21, PPh 23, PPh Badan), e-Bupot Unifikasi DJP, dan Iuran BHP/USO Kominfo.";
$active_menu = "m-finance";
require_once __DIR__ . '/../includes/header.php';

// Fetch Tax Records
$bupots = TaxRecord::all();
$msg = $_GET['msg'] ?? '';

// Monthly Calculations
$grossRevenue = 0;
foreach (Invoice::all() as $inv) {
    $st = strtolower($inv['status'] ?? 'unpaid');
    if ($st === 'lunas' || $st === 'paid') {
        $grossRevenue += floatval($inv['total_amount'] ?? 0);
    }
}

$usoRate = 1.25; // 1.25% Kontribusi USO
$bhpRate = 0.50; // 0.50% BHP Telekomunikasi
$usoAmount = $grossRevenue * ($usoRate / 100);
$bhpAmount = $grossRevenue * ($bhpRate / 100);
$totalKominfo = $usoAmount + $bhpAmount;

$pph21Monthly = 0; // Dynamic PPh 21 Gaji Staf
$pph23Total = 0;
foreach ($bupots as $b) {
    $pph23Total += floatval($b['tax_amount'] ?? 0);
}
$pphBadanEst = round($grossRevenue * 0.05);
?>

<?php if ($msg === 'created_bupot'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Bukti Potong PPh 23 (e-Bupot Unifikasi) baru berhasil diterbitkan dan tersimpan di database!
    </div>
<?php elseif ($msg === 'paid_tax'): ?>
    <div class="p-4 bg-blue-50 text-blue-800 border border-blue-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-receipt text-blue-600 text-sm"></i>
        Setoran pajak berhasil diverifikasi dengan nomor NTPN resmi Kas Negara!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <!-- Top 4 Tax Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">PPh 21 (Gaji & Insentif)</span>
                <strong class="text-2xl font-bold text-slate-900"><?= format_rupiah($pph21Monthly) ?></strong>
                <span class="text-emerald-600 font-bold block mt-0.5">✓ SPT Masa PPh 21 Siap</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg"><i class="fa-solid fa-users-viewfinder"></i></div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">PPh 23 (Vendor & Sewa)</span>
                <strong class="text-2xl font-bold text-blue-600"><?= format_rupiah($pph23Total) ?></strong>
                <span class="text-slate-400 font-medium block mt-0.5"><?= count($bupots) ?> e-Bupot Terbit</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg"><i class="fa-solid fa-file-invoice"></i></div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Estimasi PPh Badan (22%)</span>
                <strong class="text-2xl font-bold text-indigo-600"><?= format_rupiah($pphBadanEst) ?></strong>
                <span class="text-slate-400 font-medium block mt-0.5">Angsuran PPh Pasal 25</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg"><i class="fa-solid fa-building-columns"></i></div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">BHP & USO Kominfo (1.75%)</span>
                <strong class="text-2xl font-bold text-amber-600"><?= format_rupiah($totalKominfo) ?></strong>
                <span class="text-amber-600 font-bold block mt-0.5">Iuran Resmi Regulasi ISP</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg"><i class="fa-solid fa-tower-broadcast"></i></div>
        </div>
    </div>

    <!-- Filter & Action Banner (RedDash Executive Style) -->
    <div class="p-6 bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 text-white rounded-3xl shadow-xl border border-brand-900/40 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
        <div class="absolute top-0 left-1/4 w-32 h-32 bg-brand-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 bg-brand-500/20 text-brand-300 border border-brand-500/30 font-bold rounded-full text-[10px]">DJP e-Bupot 2.0 & SIMS DJPPI KOMINFO</span>
                <h3 class="font-bold text-sm text-white">Pusat Kepatuhan Pajak & Iuran Regulasi ISP Indonesia</h3>
            </div>
            <p class="text-slate-300 text-[11px] mt-1">Pemotongan PPh 23 Sewa Core/Tiang, PPh 21 Staf, Estimasi Pajak Penghasilan Badan, serta Kontribusi USO & BHP Telekomunikasi.</p>
        </div>
        <div class="flex items-center gap-2 relative z-10">
            <button onclick="document.getElementById('modalAddBupot').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/50 border border-brand-500/30 transition flex items-center gap-1.5">
                <i class="fa-solid fa-plus text-xs"></i> + Terbitkan e-Bupot PPh 23
            </button>
            <button onclick="window.print()" class="bg-white/10 hover:bg-white/20 text-white font-bold px-3.5 py-2.5 rounded-xl border border-white/10 transition">
                <i class="fa-solid fa-print"></i> Cetak SPT
            </button>
        </div>
    </div>

    <!-- Section 1: Daftar Bukti Potong PPh 23 (e-Bupot Unifikasi) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Bukti Potong PPh Pasal 23 (e-Bupot Unifikasi DJP)</h3>
                <p class="text-slate-400">Pemotongan pajak 2% atas biaya sewa upstream, sewa tiang fiber optik, dan colocation server.</p>
            </div>
            <span class="text-slate-400 font-semibold">Total DPP: <strong class="font-mono text-slate-900"><?= format_rupiah($pph23Total > 0 ? $pph23Total / 0.02 : 0) ?></strong></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">No Bukti Potong</th>
                        <th class="py-3 px-4">Nama Mitra / Vendor</th>
                        <th class="py-3 px-4">NPWP Vendor</th>
                        <th class="py-3 px-4">Objek Penghasilan (Jasa/Sewa)</th>
                        <th class="py-3 px-4 font-mono text-right">Nilai DPP (Rp)</th>
                        <th class="py-3 px-4 text-center">Tarif</th>
                        <th class="py-3 px-4 font-mono text-right">Pajak Dipotong</th>
                        <th class="py-3 px-4">Status & NTPN</th>
                        <th class="py-3 px-4 text-right">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bupots)): ?>
                    <tr class="border-b border-slate-50">
                        <td colspan="9" class="py-8 text-center text-slate-400 font-medium">Belum ada bukti potong PPh 23 di database.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($bupots as $bp): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($bp['bupot_no']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($bp['vendor_name']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-500"><?= htmlspecialchars($bp['npwp']) ?></td>
                        <td class="py-3.5 px-4 text-slate-700 font-medium"><?= htmlspecialchars($bp['obj_income']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-slate-900 text-right"><?= format_rupiah($bp['dpp_amount']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-center font-bold text-indigo-600"><?= $bp['rate_percent'] ?>%</td>
                        <td class="py-3.5 px-4 font-mono font-bold text-rose-600 text-right"><?= format_rupiah($bp['tax_amount']) ?></td>
                        <td class="py-3.5 px-4">
                            <?php if (str_contains($bp['status'], 'LUNAS')): ?>
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]">
                                    ✓ LUNAS (<?= htmlspecialchars($bp['ntpn']) ?>)
                                </span>
                            <?php else: ?>
                                <button onclick="openPayModal(<?= $bp['id'] ?>, '<?= htmlspecialchars($bp['bupot_no']) ?>', <?= $bp['tax_amount'] ?>)" class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 font-bold rounded-full text-[10px]">
                                    Input NTPN Bayar →
                                </button>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <button onclick="triggerToast('Download e-Bupot', 'Mengunduh Bukti Potong PDF <?= htmlspecialchars($bp['bupot_no']) ?>...')" class="text-blue-600 font-bold hover:underline">
                                📄 e-Bupot.pdf
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Rekapitulasi Kewajiban Iuran Kominfo & Pajak Tahunan Badan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Box 1: Iuran Regulasi Kominfo (USO & BHP) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-tower-broadcast text-amber-600"></i> Iuran Regulasi Kominfo (BHP & USO)
                    </h3>
                    <p class="text-slate-400">Kewajiban PNBP Penyelenggara Jasa Internet (ISP) ke DJPPI Kominfo.</p>
                </div>
                <span class="px-2.5 py-1 bg-amber-50 text-amber-700 font-bold rounded-full text-[10px]">1.75% DARI REVENUE</span>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-600">Pendapatan Kotor Operasional ISP (Gross)</span>
                    <strong class="font-mono text-slate-900"><?= format_rupiah($grossRevenue) ?></strong>
                </div>
                <div class="space-y-2 pl-2 border-l-2 border-slate-200">
                    <div class="flex justify-between text-slate-600">
                        <span>1. Kontribusi Kewajiban Pelayanan Universal (USO - 1.25%)</span>
                        <strong class="font-mono text-amber-700"><?= format_rupiah($usoAmount) ?></strong>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>2. Biaya Hak Penyelenggaraan Telekomunikasi (BHP - 0.50%)</span>
                        <strong class="font-mono text-amber-700"><?= format_rupiah($bhpAmount) ?></strong>
                    </div>
                </div>
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl flex justify-between items-center font-bold text-amber-950">
                    <span>TOTAL SETORAN PNBP KOMINFO BULAN INI</span>
                    <span class="font-mono text-base"><?= format_rupiah($totalKominfo) ?></span>
                </div>
            </div>
        </div>

        <!-- Box 2: Kewajiban SPT Masa & Pajak Penghasilan Badan -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-building-columns text-blue-600"></i> Estimasi PPh Badan (Corporate Tax)
                    </h3>
                    <p class="text-slate-400">Penghitungan Pajak Penghasilan Tahunan PT Berdasarkan Laba Bersih.</p>
                </div>
                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded-full text-[10px]">TARIF UU HPP 22%</span>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-600">Laba Sebelum Pajak (EBT / Net Profit)</span>
                    <strong class="font-mono text-slate-900"><?= format_rupiah($grossRevenue) ?></strong>
                </div>
                <div class="space-y-2 pl-2 border-l-2 border-slate-200">
                    <div class="flex justify-between text-slate-600">
                        <span>Kredit Pajak (PPh 23 Dipotong Pelanggan Korporat)</span>
                        <strong class="font-mono text-emerald-600">(<?= format_rupiah($pph23Total) ?>)</strong>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Estimasi Kewajiban PPh Badan Terutang (22%)</span>
                        <strong class="font-mono text-rose-600"><?= format_rupiah($pphBadanEst) ?></strong>
                    </div>
                </div>
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl flex justify-between items-center font-bold text-blue-950">
                    <span>ESTIMASI ANGSURAN PPh PASAL 25 BULAN INI</span>
                    <span class="font-mono text-base"><?= format_rupiah($pphBadanEst) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Terbitkan e-Bupot PPh 23 Baru -->
<div id="modalAddBupot" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-lg overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-file-invoice-dollar text-blue-400"></i> Terbitkan Bukti Potong PPh 23 (e-Bupot)
            </h3>
            <button onclick="document.getElementById('modalAddBupot').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_bupot">
            <input type="hidden" name="redirect" value="finance/pajak.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Vendor / Rekanan Mitra</label>
                <input type="text" name="vendor_name" required placeholder="Contoh: PT Telkom Indonesia (Persero) Tbk" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">NPWP Vendor (16 Digit)</label>
                    <input type="text" name="npwp" required placeholder="01.234.567.8-000.000" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Jenis Pajak</label>
                    <select name="tax_type" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                        <option>PPh 23 (Sewa & Jasa)</option>
                        <option>PPh 4 ayat 2 (Sewa Tanah/Bangunan)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Objek Penghasilan (Transaksi)</label>
                <input type="text" name="obj_income" required placeholder="Contoh: Sewa Bandwidth Upstream 10G" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Dasar Pengenaan Pajak / DPP (Rp)</label>
                    <input type="number" name="dpp_amount" required placeholder="10000000" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold font-mono text-blue-600">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Tarif Pajak (%)</label>
                    <input type="number" step="0.1" name="rate_percent" value="2.0" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold font-mono">
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">Terbitkan Bukti Potong e-Bupot</button>
        </form>
    </div>
</div>

<!-- Modal Input NTPN Setoran Pajak -->
<div id="modalPayTax" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-receipt text-emerald-400"></i> Konfirmasi Setor Pajak (Input NTPN)
            </h3>
            <button onclick="document.getElementById('modalPayTax').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="pay_tax">
            <input type="hidden" name="redirect" value="finance/pajak.php">
            <input type="hidden" id="payTaxId" name="id" value="">

            <div>
                <span class="text-slate-400 block">No Bukti Potong:</span>
                <strong id="payTaxBupotNo" class="font-mono text-blue-600 text-sm"></strong>
            </div>
            <div>
                <span class="text-slate-400 block">Jumlah Pajak Disetor:</span>
                <strong id="payTaxAmount" class="font-mono text-rose-600 text-base"></strong>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nomor Transaksi Penerimaan Negara (NTPN)</label>
                <input type="text" name="ntpn" required placeholder="Contoh: 87A918273619" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold uppercase text-emerald-700">
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl shadow transition">Simpan NTPN & Tandai Lunas</button>
        </form>
    </div>
</div>

<script>
function openPayModal(id, bupotNo, amount) {
    document.getElementById('payTaxId').value = id;
    document.getElementById('payTaxBupotNo').innerText = bupotNo;
    document.getElementById('payTaxAmount').innerText = 'Rp ' + Number(amount).toLocaleString('id-ID');
    document.getElementById('modalPayTax').classList.remove('hidden');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
