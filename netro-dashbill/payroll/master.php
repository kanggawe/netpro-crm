<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Master Komponen Gaji, BPJS & PPh 21 TER";
$page_subtitle = "Standarisasi struktur gaji pokok, tunjangan shift NOC, insentif pasang baru, BPJS TK, dan PPh 21.";
$active_menu = "m-payroll";
require_once __DIR__ . '/../includes/header.php';

$components = SalaryComponent::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created_component'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Komponen gaji baru berhasil ditambahkan ke database!
    </div>
<?php elseif ($msg === 'deleted_component'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-trash-can text-rose-600 text-sm"></i>
        Komponen gaji telah berhasil dihapus.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <!-- Top 3 Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Dasar Pengupahan</span>
                <strong class="text-2xl font-bold text-slate-900">PP No. 36 / 2021</strong>
                <span class="text-slate-400 block mt-0.5">Struktur & Skala Upah ISP</span>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-scale-balanced"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Program Jaminan Sosial</span>
                <strong class="text-2xl font-bold text-emerald-600">BPJS TK & Kes</strong>
                <span class="text-emerald-600 font-medium block mt-0.5">JKK, JKM, JHT, JP, BPJS Kes</span>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-shield-heart"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Metode Pajak Penghasilan</span>
                <strong class="text-2xl font-bold text-indigo-600">PPh 21 TER</strong>
                <span class="text-indigo-600 font-medium block mt-0.5">PMK 168/2023 & PP 58/2023</span>
            </div>
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-file-invoice-dollar"></i></div>
        </div>
    </div>

    <!-- Master Components Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Master Komponen Penghasilan & Formula Potongan</h3>
                <p class="text-slate-400">Total <?= count($components) ?> Komponen gaji aktif tersimpan.</p>
            </div>
            <button onclick="document.getElementById('modalAddComponent').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Tambah Komponen Gaji
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Nama Komponen</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Formula / Aturan Perhitungan</th>
                        <th class="py-3 px-4">Ditanggung Oleh</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($components as $c): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold <?= strpos($c['category'], 'POTONGAN') !== false ? 'text-rose-600' : 'text-blue-600' ?>"><?= htmlspecialchars($c['code']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($c['name']) ?></td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 <?= strpos($c['category'], 'POTONGAN') !== false ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' ?> font-bold rounded text-[10px]"><?= htmlspecialchars($c['category']) ?></span>
                        </td>
                        <td class="py-3.5 px-4 font-mono text-slate-700"><?= htmlspecialchars($c['formula']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($c['borne_by']) ?></td>
                        <td class="py-3.5 px-4 text-right">
                            <form action="<?= base_url('api/handler.php') ?>" method="POST" onsubmit="return confirm('Hapus komponen gaji ini?')" class="inline">
                                <input type="hidden" name="action" value="delete_salary_component">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <input type="hidden" name="redirect" value="payroll/master.php">
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

<!-- Modal Tambah Komponen -->
<div id="modalAddComponent" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm">Tambah Komponen Gaji Baru</h3>
            <button onclick="document.getElementById('modalAddComponent').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-3">
            <input type="hidden" name="action" value="create_salary_component">
            <input type="hidden" name="redirect" value="payroll/master.php">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kode Komponen</label>
                    <input type="text" name="code" required placeholder="COMP-TJ-LEMBUR" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kategori</label>
                    <select name="category" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                        <option>PENDAPATAN TETAP</option>
                        <option>TUNJANGAN SHIFT</option>
                        <option>VARIABEL BONUS</option>
                        <option>POTONGAN RESMI</option>
                        <option>POTONGAN PAJAK</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Komponen</label>
                <input type="text" name="name" required placeholder="Tunjangan Lembur Lapangan" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Formula / Nominal</label>
                <input type="text" name="formula" required placeholder="Rp 50.000 / Jam" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Ditanggung Oleh</label>
                <select name="borne_by" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option>Perusahaan</option>
                    <option>Karyawan (Payroll)</option>
                    <option>Karyawan (DJP)</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow">Simpan Komponen Gaji</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
