<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Buku Besar & Bagan Akun Standar (COA)";
$page_subtitle = "Master Chart of Accounts (COA) PSAK 72/115 & Buku Besar Umum (General Ledger) ISP.";
$active_menu = "m-finance";
$breadcrumbs = [
    'Keuangan & Akuntansi' => 'finance/kas.php',
    'Buku Besar & COA PSAK' => ''
];
require_once __DIR__ . '/../includes/header.php';

// COA Data grouped by Classification
$coaAccounts = CoaAccount::all();
$journalEntries = JournalEntry::all();
?>

<div class="space-y-6 text-xs">
    <!-- Header Info Banner (RedDash Executive Style) -->
    <div class="p-6 bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 text-white rounded-3xl shadow-xl border border-brand-900/40 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
        <div class="absolute top-0 left-1/4 w-32 h-32 bg-brand-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 bg-brand-500/20 text-brand-300 border border-brand-500/30 font-bold rounded-full text-[10px]">PSAK 72 / 115 & PSAK 73 COMPLIANT</span>
                <h3 class="font-bold text-sm text-white">Struktur Bagan Akun Standar (COA) Industri ISP Telekomunikasi</h3>
            </div>
            <p class="text-slate-300 text-[11px] mt-1">Standarisasi 34 akun akuntansi keuangan ISP: Aset Infrastruktur FO, Liabilitas Kontrak, Pendapatan FTTH, COGS Upstream & Beban OPEX.</p>
        </div>
        <div class="flex items-center gap-2 relative z-10">
            <button onclick="switchTab('tab-coa')" id="btn-tab-coa" class="bg-brand-600 text-white font-bold px-4 py-2 rounded-xl shadow-lg shadow-brand-950/50 border border-brand-500/30 transition">Bagan Akun (COA)</button>
            <button onclick="switchTab('tab-ledger')" id="btn-tab-ledger" class="bg-white/10 text-slate-300 hover:text-white font-bold px-4 py-2 rounded-xl border border-white/10 transition">Buku Besar (General Ledger)</button>
        </div>
    </div>

    <!-- TAB 1: COA Master Directory -->
    <div id="panel-tab-coa" class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Daftar Akun Bagan (Chart of Accounts) - <?= count($coaAccounts) ?> Akun Terdaftar</h3>
                    <p class="text-slate-400">Klasifikasi hierarki akun akuntansi terpadu sesuai standar PSAK ISP.</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="document.getElementById('modalAddCoa').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-bold px-4 py-2 rounded-xl shadow-md flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-plus text-xs"></i> + Tambah Akun COA
                    </button>
                    <button onclick="window.print()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-2 rounded-lg">
                        <i class="fa-solid fa-print"></i> Cetak COA
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                            <th class="py-3 px-4">Kode Akun</th>
                            <th class="py-3 px-4">Nama Akun Akuntansi</th>
                            <th class="py-3 px-4">Kategori / Klasifikasi</th>
                            <th class="py-3 px-4">Saldo Normal</th>
                            <th class="py-3 px-4 text-right">Saldo Terkini</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coaAccounts as $acc): ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                            <td class="py-3 px-4 font-mono font-bold text-blue-600 text-xs"><?= $acc['code'] ?></td>
                            <td class="py-3 px-4 font-bold text-slate-800"><?= htmlspecialchars($acc['name']) ?></td>
                            <td class="py-3 px-4">
                                <?php if (str_contains($acc['category'], 'Aset Tetap')): ?>
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-bold rounded text-[10px]">Aset Tetap FO/Server</span>
                                <?php elseif (str_contains($acc['category'], 'Aset')): ?>
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Aset Lancar</span>
                                <?php elseif (str_contains($acc['category'], 'Liabilitas')): ?>
                                    <span class="px-2 py-0.5 bg-amber-50 text-amber-700 font-bold rounded text-[10px]">Kewajiban</span>
                                <?php elseif (str_contains($acc['category'], 'Ekuitas')): ?>
                                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 font-bold rounded text-[10px]">Ekuitas</span>
                                <?php elseif (str_contains($acc['category'], 'Pendapatan')): ?>
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded text-[10px]">Pendapatan</span>
                                <?php elseif (str_contains($acc['category'], 'COGS')): ?>
                                    <span class="px-2 py-0.5 bg-rose-50 text-rose-700 font-bold rounded text-[10px]">Beban Pokok</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 font-bold rounded text-[10px]">Beban OPEX</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-600"><?= $acc['normal_balance'] ?? ($acc['normal'] ?? 'Debit') ?></td>
                            <td class="py-3 px-4 font-mono font-bold text-right <?= ($acc['balance'] < 0) ? 'text-rose-600' : 'text-slate-900' ?>">
                                <?= ($acc['balance'] < 0) ? '(' . format_rupiah(abs($acc['balance'])) . ')' : format_rupiah($acc['balance']) ?>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button onclick="viewLedger('<?= $acc['code'] ?>', '<?= addslashes($acc['name']) ?>')" class="text-blue-600 font-bold hover:underline">Buku Besar →</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: General Ledger (Buku Besar Rincian Mutasi) -->
    <div id="panel-tab-ledger" class="space-y-6 hidden">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
                <div>
                    <span class="text-slate-400 font-semibold block uppercase">Buku Besar Akun (General Ledger)</span>
                    <h3 id="ledgerAccountTitle" class="font-bold text-slate-900 text-base">Semua Mutasi Jurnal Umum</h3>
                </div>
                <div class="flex items-center gap-2">
                    <select id="ledgerSelect" onchange="changeLedgerAccount(this.value)" class="bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                        <option value="ALL">Semua Akun (Jurnal Umum)</option>
                        <?php foreach ($coaAccounts as $acc): ?>
                            <option value="<?= $acc['code'] ?>"><?= $acc['code'] ?> - <?= htmlspecialchars($acc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button onclick="window.print()" class="bg-slate-900 text-white font-bold px-3 py-2 rounded-lg">🖨️ Cetak</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">No Jurnal</th>
                            <th class="py-3 px-4">Kode & Nama Akun</th>
                            <th class="py-3 px-4">Uraian Transaksi</th>
                            <th class="py-3 px-4 text-right">Debit</th>
                            <th class="py-3 px-4 text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody id="ledgerTableBody">
                        <?php if (empty($journalEntries)): ?>
                        <tr class="border-b border-slate-50">
                            <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada mutasi jurnal buku besar di database.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($journalEntries as $j): ?>
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50" data-account="<?= $j['account_code'] ?>">
                            <td class="py-3 px-4 font-mono text-slate-600"><?= $j['trans_date'] ?></td>
                            <td class="py-3 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($j['journal_no']) ?></td>
                            <td class="py-3 px-4 font-medium text-slate-800">
                                <span class="font-mono font-bold text-slate-900"><?= $j['account_code'] ?></span> - <?= htmlspecialchars($j['account_name'] ?? '') ?>
                            </td>
                            <td class="py-3 px-4 text-slate-700"><?= htmlspecialchars($j['description']) ?></td>
                            <td class="py-3 px-4 font-mono text-right text-emerald-600 font-bold"><?= format_rupiah($j['debit']) ?></td>
                            <td class="py-3 px-4 font-mono text-right text-slate-700"><?= format_rupiah($j['credit']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Akun COA -->
<div id="modalAddCoa" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden">
        <div class="h-14 bg-slate-950 px-6 flex items-center justify-between">
            <h3 class="font-bold text-slate-50 text-xs tracking-wider uppercase flex items-center gap-2">
                <i class="fa-solid fa-book text-blue-400"></i> Tambah Akun Bagan Standar (COA)
            </h3>
            <button onclick="document.getElementById('modalAddCoa').classList.add('hidden')" class="text-slate-400 hover:text-white font-bold text-lg">×</button>
        </div>
        <form onsubmit="triggerToast('Sukses', 'Akun COA baru berhasil didaftarkan.'); document.getElementById('modalAddCoa').classList.add('hidden'); return false;" class="p-6 space-y-3 text-xs">
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Kode Akun (Bagan Akun)</label>
                <input type="text" required placeholder="Contoh: 1104" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold text-blue-600">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Akun Akuntansi</label>
                <input type="text" required placeholder="Contoh: Bank BNI Corporate" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Klasifikasi Kategori</label>
                <select class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option>1-xxxx Aset Lancar</option>
                    <option>1-xxxx Aset Tetap (Infrastruktur/Alat)</option>
                    <option>2-xxxx Liabilitas Jangka Pendek (Hutang/PPN)</option>
                    <option>2-xxxx Liabilitas Kontrak (PSAK 72)</option>
                    <option>3-xxxx Ekuitas / Modal</option>
                    <option>4-xxxx Pendapatan Usaha</option>
                    <option>5-xxxx Beban Pokok Pendapatan (COGS)</option>
                    <option>6-xxxx Beban Operasional (OPEX)</option>
                </select>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Posisi Saldo Normal</label>
                <select class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option>Debit</option>
                    <option>Kredit</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">Simpan Akun COA Baru</button>
        </form>
    </div>
</div>

<script>
function switchTab(tabId) {
    if (tabId === 'tab-coa') {
        document.getElementById('panel-tab-coa').classList.remove('hidden');
        document.getElementById('panel-tab-ledger').classList.add('hidden');
        document.getElementById('btn-tab-coa').className = 'bg-brand-600 text-white font-bold px-4 py-2 rounded-xl shadow-lg shadow-brand-950/50 border border-brand-500/30 transition';
        document.getElementById('btn-tab-ledger').className = 'bg-white/10 text-slate-300 hover:text-white font-bold px-4 py-2 rounded-xl border border-white/10 transition';
    } else {
        document.getElementById('panel-tab-coa').classList.add('hidden');
        document.getElementById('panel-tab-ledger').classList.remove('hidden');
        document.getElementById('btn-tab-ledger').className = 'bg-brand-600 text-white font-bold px-4 py-2 rounded-xl shadow-lg shadow-brand-950/50 border border-brand-500/30 transition';
        document.getElementById('btn-tab-coa').className = 'bg-white/10 text-slate-300 hover:text-white font-bold px-4 py-2 rounded-xl border border-white/10 transition';
    }
}

function viewLedger(code, name) {
    const sel = document.getElementById('ledgerSelect');
    if (sel) sel.value = code;
    document.getElementById('ledgerAccountTitle').innerText = code + ' - ' + name;
    filterLedgerRows(code);
    switchTab('tab-ledger');
}

function changeLedgerAccount(code) {
    const sel = document.getElementById('ledgerSelect');
    const txt = sel.options[sel.selectedIndex].text;
    document.getElementById('ledgerAccountTitle').innerText = (code === 'ALL') ? 'Semua Mutasi Jurnal Umum' : txt;
    filterLedgerRows(code);
}

function filterLedgerRows(code) {
    const rows = document.querySelectorAll('#ledgerTableBody tr[data-account]');
    rows.forEach(r => {
        if (code === 'ALL' || r.getAttribute('data-account') === code) {
            r.style.display = '';
        } else {
            r.style.display = 'none';
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


