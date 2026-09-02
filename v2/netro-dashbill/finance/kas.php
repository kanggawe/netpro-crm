<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Arus Kas & Mutasi Rekening Bank";
$page_subtitle = "Pencatatan mutasi kas operasional, rekening bank BCA, Mandiri, dan rekonsiliasi kasir.";
$active_menu = "m-finance";
require_once __DIR__ . '/../includes/header.php';

$cashes = Cash::all();
$msg = $_GET['msg'] ?? '';

// Calculate Balances dynamically
$bcaAcc = CoaAccount::find('1102');
$mandiriAcc = CoaAccount::find('1103');
$hqAcc = CoaAccount::find('1101');

$bcaBalance = $bcaAcc ? floatval($bcaAcc['balance']) : 0;
$mandiriBalance = $mandiriAcc ? floatval($mandiriAcc['balance']) : 0;
$hqBalance = $hqAcc ? floatval($hqAcc['balance']) : 0;
$totalLiquidity = $bcaBalance + $mandiriBalance + $hqBalance;
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Mutasi transaksi kas/bank baru berhasil dicatat dan disinkronkan ke database!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <!-- 4 Top Bank Liquidity Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block font-semibold uppercase">Saldo Bank BCA Bisnis</span>
            <strong class="text-2xl font-bold text-blue-600"><?= format_rupiah($bcaBalance) ?></strong>
            <span class="text-[10px] text-slate-400 block mt-1">Rek: 872-009-1234 (Giro)</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block font-semibold uppercase">Saldo Bank Mandiri</span>
            <strong class="text-2xl font-bold text-indigo-600"><?= format_rupiah($mandiriBalance) ?></strong>
            <span class="text-[10px] text-slate-400 block mt-1">Rek: 124-000-8889 (Corporate)</span>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-slate-400 block font-semibold uppercase">Kas Tunai Kantor</span>
            <strong class="text-2xl font-bold text-emerald-600"><?= format_rupiah($hqBalance) ?></strong>
            <span class="text-[10px] text-slate-400 block mt-1">Kasir Utama HQ (Petty Cash)</span>
        </div>
        <div class="p-5 rounded-3xl border border-brand-900/40 shadow-xl bg-gradient-to-br from-brand-950 via-slate-950 to-brand-950 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-brand-600/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <span class="text-brand-300/80 block font-bold uppercase text-[10px]">Total Likuiditas Kas</span>
                <strong class="text-2xl font-extrabold text-white block my-0.5"><?= format_rupiah($totalLiquidity) ?></strong>
                <span class="text-emerald-400 text-[10px] block font-mono">✓ Rekening Koran Reconciled</span>
            </div>
        </div>
    </div>

    <!-- Cash Mutations Table -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Riwayat Mutasi Arus Kas Real-time (<?= count($cashes) ?> Transaksi)</h3>
                <p class="text-slate-400">Pencatatan debit & kredit rekening perusahaan terintegrasi otomatis.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="document.getElementById('modalAddKas').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-bold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-money-bill-transfer text-xs"></i> + Catat Transaksi Kas
                </button>
                <a href="cetak_kas.php" target="_blank" class="bg-slate-800 hover:bg-slate-900 text-white font-bold px-3.5 py-2 rounded-lg shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-print"></i> Cetak / Export PDF
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Keterangan Transaksi</th>
                        <th class="py-3 px-4">Akun Bank / Kas</th>
                        <th class="py-3 px-4">Tipe Mutasi</th>
                        <th class="py-3 px-4 font-mono text-right">Nominal</th>
                        <th class="py-3 px-4">Rekonsiliasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cashes)): ?>
                    <tr><td colspan="6" class="py-6 text-center text-slate-400">Belum ada transaksi arus kas di database.</td></tr>
                    <?php else: ?>
                    <?php foreach ($cashes as $cs): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono text-slate-500"><?= htmlspecialchars($cs['trans_date']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($cs['description']) ?></td>
                        <td class="py-3.5 px-4 font-semibold text-blue-600"><?= htmlspecialchars($cs['bank_account']) ?></td>
                        <td class="py-3.5 px-4">
                            <?php if ($cs['type'] === 'in'): ?>
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded">Pemasukan (Debit)</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 font-bold rounded">Pengeluaran (Kredit)</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-4 font-mono font-bold text-right <?= ($cs['type'] === 'in') ? 'text-emerald-600' : 'text-rose-600' ?>">
                            <?= ($cs['type'] === 'in') ? '+ ' : '- ' ?><?= format_rupiah($cs['amount']) ?>
                        </td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]">VERIFIED ✓</span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Catat Kas -->
<div id="modalAddKas" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-money-bill-transfer text-blue-400"></i> Catat Transaksi Kas / Bank
            </h3>
            <button onclick="document.getElementById('modalAddKas').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-3 text-xs">
            <input type="hidden" name="action" value="create_cash">
            <input type="hidden" name="redirect" value="finance/kas.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Tipe Mutasi</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="p-2 bg-slate-50 border border-emerald-500 rounded-lg flex items-center gap-2 font-bold text-emerald-700 cursor-pointer">
                        <input type="radio" name="type" value="in" checked class="accent-emerald-600"> Pemasukan (Debit)
                    </label>
                    <label class="p-2 bg-slate-50 border border-slate-200 rounded-lg flex items-center gap-2 font-bold text-slate-700 cursor-pointer">
                        <input type="radio" name="type" value="out" class="accent-red-600"> Pengeluaran (Kredit)
                    </label>
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Akun Keuangan</label>
                <select name="bank_account" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option>Bank BCA Bisnis</option>
                    <option>Bank Mandiri</option>
                    <option>Kas Tunai Kantor</option>
                </select>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Jumlah Nominal (Rp)</label>
                <input type="number" name="amount" required placeholder="1500000" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-blue-600 font-mono">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Keterangan Transaksi</label>
                <input type="text" name="description" required placeholder="Uraian mutasi..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">Simpan Mutasi Kas</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
