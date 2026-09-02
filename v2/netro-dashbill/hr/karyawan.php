<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Data & Kontrak Karyawan";
$page_subtitle = "Direktori pegawai ISP Management, jabatan, divisi, dan dokumen masa kerja.";
$active_menu = "m-hr";
require_once __DIR__ . '/../includes/header.php';

$employees = Employee::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'employee_created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Karyawan baru berhasil ditambahkan ke database HR!
    </div>
<?php elseif ($msg === 'employee_deleted'): ?>
    <div class="p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-trash-can text-red-600 text-sm"></i>
        Data karyawan berhasil dihapus.
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
    <!-- Left 2/3 Employees Table -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Direktori Pegawai Terdaftar (<?= count($employees) ?> Staf)</h3>
                <p class="text-slate-400">Database staf operasional kantor & teknisi lapangan.</p>
            </div>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                    <th class="py-3 px-4">NIK</th>
                    <th class="py-3 px-4">Nama Staf</th>
                    <th class="py-3 px-4">Divisi</th>
                    <th class="py-3 px-4">Jabatan</th>
                    <th class="py-3 px-4">Status Kontrak</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($employees)): ?>
                <tr class="border-b border-slate-50">
                    <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada data pegawai terdaftar di database HR.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($employees as $e): ?>
                <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                    <td class="py-3.5 px-4 font-mono font-bold text-slate-600"><?= htmlspecialchars($e['nik']) ?></td>
                    <td class="py-3.5 px-4">
                        <strong class="font-bold text-slate-800 block"><?= htmlspecialchars($e['name']) ?></strong>
                        <span class="text-[10px] text-slate-400 font-normal"><?= htmlspecialchars($e['email']) ?></span>
                    </td>
                    <td class="py-3.5 px-4 font-medium text-slate-700"><?= htmlspecialchars($e['division']) ?></td>
                    <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($e['position']) ?></td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]">
                            <?= htmlspecialchars($e['contract_status']) ?>
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-right">
                        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline" onsubmit="return confirm('Hapus karyawan <?= addslashes($e['name']) ?>?');">
                            <input type="hidden" name="action" value="delete_employee">
                            <input type="hidden" name="id" value="<?= $e['id'] ?>">
                            <input type="hidden" name="redirect" value="hr/karyawan.php">
                            <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Right 1/3 Add Employee Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-blue-600"></i> Tambah Karyawan Baru
        </h3>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-3">
            <input type="hidden" name="action" value="create_employee">
            <input type="hidden" name="redirect" value="hr/karyawan.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Lengkap</label>
                <input type="text" name="name" placeholder="Nama Pegawai" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Email Perusahaan</label>
                <input type="email" name="email" placeholder="nama@netpro.co.id" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Divisi Kerja</label>
                <select name="division" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option>NOC & Jaringan</option>
                    <option>Teknisi Lapangan</option>
                    <option>CS & Ticketing</option>
                    <option>Billing & Finance</option>
                    <option>Marketing & Sales</option>
                </select>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Jabatan</label>
                <input type="text" name="position" placeholder="Contoh: Network Engineer" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium">
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Status Kontrak</label>
                <select name="contract_status" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                    <option value="TETAP">Karyawan Tetap</option>
                    <option value="PKWT">Kontrak PKWT</option>
                    <option value="MAGANG">Magang / Internship</option>
                </select>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition">
                + Daftarkan Karyawan
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
