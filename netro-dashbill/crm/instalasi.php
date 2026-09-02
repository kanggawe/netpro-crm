<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Manajemen Pemasangan Baru (Work Order)";
$page_subtitle = "Penugasan teknisi lapangan, alokasi material kabel & ONT, dan aktivasi PPPoE port.";
$active_menu = "m-crm";
require_once __DIR__ . '/../includes/header.php';
?>


<?php
$wos = WorkOrder::all();
$employees = Employee::all();
$msg = $_GET['msg'] ?? '';
$prefillCust = $_GET['customer_name'] ?? '';
$prefillPkg = $_GET['package_name'] ?? 'Home Premium 50M';
$showModal = !empty($prefillCust);
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Surat Perintah Kerja (WO) baru berhasil diterbitkan untuk teknisi!
    </div>
<?php elseif ($msg === 'deleted'): ?>
    <div class="p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-trash-can text-red-600 text-sm"></i>
        Data Work Order berhasil dihapus.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Surat Perintah Kerja (<?= count($wos) ?> WO Aktif)</h3>
                <p class="text-slate-400">Monitoring instalasi perangkat ONT & penarikan kabel drop optik.</p>
            </div>
            <button onclick="document.getElementById('modalAddWO').classList.remove('hidden')" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 flex items-center gap-1.5 transition">
                <i class="fa-solid fa-plus text-xs"></i> + Terbitkan WO Baru
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">No WO</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Paket Dipasang</th>
                        <th class="py-3 px-4">Perangkat ONT</th>
                        <th class="py-3 px-4">Tim Teknisi</th>
                        <th class="py-3 px-4">Redaman Sinyal</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($wos)): ?>
                    <tr><td colspan="8" class="py-6 text-center text-slate-400">Belum ada data Work Order di database.</td></tr>
                    <?php else: ?>
                    <?php foreach ($wos as $wo): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($wo['wo_no']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($wo['customer_name']) ?></td>
                        <td class="py-3.5 px-4 font-medium text-slate-700"><?= htmlspecialchars($wo['package_name']) ?></td>
                        <td class="py-3.5 px-4 font-mono text-slate-600"><?= htmlspecialchars($wo['ont_type']) ?> (SN: <?= htmlspecialchars($wo['ont_sn']) ?>)</td>
                        <td class="py-3.5 px-4 font-semibold text-slate-700"><?= htmlspecialchars($wo['tech_name']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-emerald-600"><?= htmlspecialchars($wo['attenuation']) ?></td>
                        <td class="py-3.5 px-4"><span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px]"><?= htmlspecialchars($wo['status']) ?></span></td>
                        <td class="py-3.5 px-4 text-right space-x-2">
                            <a href="<?= base_url('crm/berita_acara.php?id=' . $wo['id']) ?>" class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded text-[10px] inline-flex items-center gap-1">
                                <i class="fa-solid fa-file-signature text-[9px]"></i> BAST
                            </a>
                            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline" onsubmit="return confirm('Hapus WO <?= addslashes($wo['wo_no']) ?>?');">
                                <input type="hidden" name="action" value="delete_wo">
                                <input type="hidden" name="id" value="<?= $wo['id'] ?>">
                                <input type="hidden" name="redirect" value="crm/instalasi.php">
                                <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Terbitkan WO Baru -->
<div id="modalAddWO" class="fixed inset-0 bg-slate-950/65 z-[9999] flex items-center justify-center p-4 <?= $showModal ? '' : 'hidden' ?> backdrop-blur-md transition-all duration-300">
    <div class="bg-white rounded-3xl shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)] border border-slate-100 w-full max-w-lg overflow-hidden transform transition-all">
        
        <!-- Premium Header with Soft Accent Pill -->
        <div class="p-6 pb-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-b from-slate-50/80 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm tracking-tight">Terbitkan Work Order (WO) Baru</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Penugasan teknisi lapangan untuk instalasi & aktivasi GPON</p>
                </div>
            </div>
            <button onclick="document.getElementById('modalAddWO').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 transition flex items-center justify-center text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Form Body -->
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-4 text-xs">
            <input type="hidden" name="action" value="create_wo">
            <input type="hidden" name="redirect" value="crm/instalasi.php">

            <div>
                <label class="font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-user text-slate-400 text-[10px]"></i> Nama Pelanggan
                </label>
                <input type="text" name="customer_name" value="<?= htmlspecialchars($prefillCust) ?>" required placeholder="Contoh: Budi Wijaya" class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl p-3 font-semibold text-slate-900 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition shadow-inner">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-box-open text-slate-400 text-[10px]"></i> Paket Layanan
                    </label>
                    <select name="package_name" class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl p-3 font-bold text-slate-800 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition">
                        <option <?= $prefillPkg === 'Home Basic 20M' ? 'selected' : '' ?>>Home Basic 20M</option>
                        <option <?= ($prefillPkg === 'Home Premium 50M' || empty($prefillPkg)) ? 'selected' : '' ?>>Home Premium 50M</option>
                        <option <?= $prefillPkg === 'SOHO Platinum 100M' ? 'selected' : '' ?>>SOHO Platinum 100M</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-microchip text-slate-400 text-[10px]"></i> Tipe Modem ONT
                    </label>
                    <select name="ont_type" class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl p-3 font-bold text-slate-800 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition">
                        <option>ZTE F660 Dualband</option>
                        <option>Huawei HG8245H5</option>
                        <option>Fiberhome GPON</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-barcode text-slate-400 text-[10px]"></i> Serial Number (SN ONT)
                    </label>
                    <input type="text" name="ont_sn" placeholder="ZTEG12345678" value="ZTEG<?= rand(10000000, 99999999) ?>" required class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl p-3 font-mono font-bold text-slate-800 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition shadow-inner">
                </div>
                <div>
                    <label class="font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-gear text-slate-400 text-[10px]"></i> Teknisi Lapangan
                    </label>
                    <select name="tech_name" class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl p-3 font-bold text-slate-800 focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition">
                        <?php if (empty($employees)): ?>
                            <option value="Teknisi Lapangan">Teknisi Lapangan</option>
                        <?php else: ?>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['position']) ?>)">
                                    <?= htmlspecialchars($emp['name']) ?> - <?= htmlspecialchars($emp['position']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="font-bold text-slate-700 block mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-network-wired text-slate-400 text-[10px]"></i> Alokasi Port ODP
                </label>
                <input type="text" name="odp_port" value="ODP-JTW-04/16 (Port 3)" class="w-full bg-slate-50/70 hover:bg-slate-50 focus:bg-white border border-slate-200 rounded-xl p-3 font-mono text-blue-600 font-bold focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition shadow-inner">
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalAddWO').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                    Batal
                </button>
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-5 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-paper-plane text-xs"></i> Simpan & Terbitkan WO
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
