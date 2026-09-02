<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Daftar Pelanggan Terdaftar";
$page_subtitle = "Database master pelanggan aktif, suspend, dan riwayat paket.";
$active_menu = "m-crm";
require_once __DIR__ . '/../includes/header.php';

$customers = Customer::all();
$packages = Package::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'customer_created' || $msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Pelanggan baru berhasil didaftarkan dan invoice pertama telah diterbitkan!
    </div>
<?php elseif ($msg === 'customer_deleted' || $msg === 'deleted'): ?>
    <div class="p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-trash-can text-red-600 text-sm"></i>
        Data pelanggan berhasil dihapus dari database.
    </div>
<?php elseif ($msg === 'customer_updated'): ?>
    <div class="p-4 bg-blue-50 text-blue-800 border border-blue-200 rounded-xl flex items-center gap-2 text-xs font-bold">
        <i class="fa-solid fa-pen-to-square text-blue-600 text-sm"></i>
        Perubahan data pelanggan berhasil disimpan.
    </div>
<?php endif; ?>

<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4 text-xs">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
        <div>
            <h3 class="font-bold text-slate-900 text-sm">Master Database Pelanggan (<?= count($customers) ?> Terdaftar)</h3>
            <p class="text-slate-400">Kelola data akun pelanggan, koordinat GPS, paket langganan, dan status isolir.</p>
        </div>
        <a href="<?= base_url('crm/registrasi.php') ?>" class="bg-brand-600 hover:bg-brand-700 text-white font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-brand-950/30 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-user-plus text-xs"></i> + Tambah Pelanggan Baru
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                    <th class="py-3 px-4">CID</th>
                    <th class="py-3 px-4">Nama Pelanggan</th>
                    <th class="py-3 px-4">No WhatsApp</th>
                    <th class="py-3 px-4">Paket & Bandwidth</th>
                    <th class="py-3 px-4">Tipe Billing & Masa Aktif</th>
                    <th class="py-3 px-4">Skema PPN</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="8" class="py-6 text-center text-slate-400">Belum ada data pelanggan di database.</td>
                </tr>
                <?php else: ?>
                <?php foreach ($customers as $c): 
                    $bType = strtolower($c['billing_type'] ?? 'postpaid');
                    $isPrepaid = ($bType === 'prepaid');
                    $expiredAt = !empty($c['expired_at']) ? strtotime($c['expired_at']) : null;
                    $daysLeft = $expiredAt ? ceil(($expiredAt - time()) / 86400) : 0;
                ?>
                <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                    <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($c['cid']) ?></td>
                    <td class="py-3.5 px-4">
                        <strong class="font-bold text-slate-900 block"><?= htmlspecialchars($c['name']) ?></strong>
                        <span class="text-[10px] text-slate-400 font-normal"><?= htmlspecialchars($c['address']) ?></span>
                    </td>
                    <td class="py-3.5 px-4 text-slate-600 font-mono"><?= htmlspecialchars($c['phone']) ?></td>
                    <td class="py-3.5 px-4 font-medium text-slate-800">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="font-bold text-slate-900"><?= htmlspecialchars($c['package_name'] ?? 'Custom Package') ?></span>
                            <?php 
                            $authM = strtolower($c['auth_method'] ?? 'pppoe');
                            if ($authM === 'dhcp'): ?>
                                <span class="px-1.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 font-bold rounded text-[8.5px]">DHCP</span>
                            <?php elseif ($authM === 'hotspot'): ?>
                                <span class="px-1.5 py-0.5 bg-pink-50 text-pink-700 border border-pink-200 font-bold rounded text-[8.5px]">HOTSPOT</span>
                            <?php elseif ($authM === 'static'): ?>
                                <span class="px-1.5 py-0.5 bg-cyan-50 text-cyan-700 border border-cyan-200 font-bold rounded text-[8.5px]">STATIC</span>
                            <?php else: ?>
                                <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 font-bold rounded text-[8.5px]">PPPoE</span>
                            <?php endif; ?>
                        </div>
                        <span class="block text-[10px] text-slate-400 font-mono"><?= format_rupiah($c['package_price'] ?? 0) ?>/bln</span>
                    </td>
                    <td class="py-3.5 px-4">
                        <?php if ($isPrepaid): 
                            $secondsLeft = $expiredAt ? ($expiredAt - time()) : 0;
                            $minutesLeft = ceil($secondsLeft / 60);
                            $isInactive = ($c['status'] === 'inactive');
                        ?>
                            <div class="space-y-1">
                                <div class="flex items-center gap-1">
                                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 border border-purple-200 font-bold rounded-full text-[9px] inline-flex items-center gap-1">
                                        <i class="fa-solid fa-bolt text-[8px]"></i> PRABAYAR
                                    </span>
                                    <?php if (($c['billing_cycle_type'] ?? 'anniversary') === 'fixed_date'): ?>
                                        <span class="px-1.5 py-0.2 bg-slate-100 text-slate-700 border border-slate-200 font-bold rounded text-[8px]">FIXED DATE</span>
                                    <?php else: ?>
                                        <span class="px-1.5 py-0.2 bg-purple-100 text-purple-700 border border-purple-200 font-bold rounded text-[8px]">ROLLING 30D</span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($isInactive): ?>
                                    <span class="block text-[10px] text-amber-600 font-medium">
                                        <i class="fa-solid fa-hourglass-start text-[9px]"></i> Belum Online (Grace Belum Jalan)
                                    </span>
                                <?php elseif ($expiredAt): ?>
                                    <?php if ($secondsLeft <= 0): ?>
                                        <span class="block text-[10px] text-rose-600 font-bold">
                                            <i class="fa-solid fa-triangle-exclamation"></i> Masa Aktif Habis (Isolir)
                                        </span>
                                    <?php elseif ($daysLeft <= 1): ?>
                                        <span class="block text-[10px] text-amber-600 font-bold animate-pulse">
                                            <i class="fa-solid fa-clock"></i> Sisa <?= $minutesLeft ?> Menit (Grace Bayar)
                                        </span>
                                    <?php else: ?>
                                        <span class="block text-[10px] text-slate-600 font-medium">
                                            Exp: <?= date('d M Y', $expiredAt) ?> (<?= $daysLeft ?> hari lagi)
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="block text-[10px] text-slate-400">Aktif 30 Hari</span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="space-y-1">
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 font-bold rounded-full text-[9px] inline-flex items-center gap-1">
                                    <i class="fa-solid fa-calendar text-[8px]"></i> PASCABAYAR
                                </span>
                                <?php if ($c['status'] === 'inactive'): ?>
                                    <span class="block text-[10px] text-amber-600 font-medium"><i class="fa-solid fa-hourglass-start text-[9px]"></i> Belum Online</span>
                                <?php else: ?>
                                    <span class="block text-[10px] text-slate-400">Jatuh Tempo: Tgl 20</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="py-3.5 px-4">
                        <?php if (($c['ppn_scheme'] ?? 'include') === 'include'): ?>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 font-bold rounded text-[9px]">INC PPN</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 font-bold rounded text-[9px]">EXC PPN</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3.5 px-4">
                        <?php if ($c['status'] === 'active'): ?>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold rounded-full text-[10px] inline-flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> AKTIF
                            </span>
                        <?php elseif ($c['status'] === 'inactive'): ?>
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-700 border border-slate-200 font-bold rounded-full text-[10px] inline-flex items-center gap-1">
                                <i class="fa-solid fa-power-off text-slate-400"></i> INACTIVE
                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 font-bold rounded-full text-[10px] inline-flex items-center gap-1">
                                <i class="fa-solid fa-ban text-rose-600"></i> ISOLIR
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3.5 px-4 text-right space-x-1.5 whitespace-nowrap">
                        <?php if ($c['status'] === 'inactive'): ?>
                            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline">
                                <input type="hidden" name="action" value="set_customer_online">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <input type="hidden" name="redirect" value="crm/daftar.php">
                                <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded text-[10px] shadow-xs inline-flex items-center gap-1">
                                    <i class="fa-solid fa-play text-[8px]"></i> Aktivasi Online
                                </button>
                            </form>
                        <?php endif; ?>
                        <?php if ($isPrepaid): ?>
                            <button onclick="openTopupModal(<?= $c['id'] ?>, '<?= addslashes($c['name']) ?>', '<?= addslashes($c['package_name'] ?? '') ?>', <?= $c['package_price'] ?? 0 ?>)" class="px-2.5 py-1 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded text-[10px] shadow-xs">
                                + Top-up
                            </button>
                        <?php endif; ?>
                        <a href="<?= base_url('crm/instalasi.php?customer_name=' . urlencode($c['name']) . '&package_name=' . urlencode($c['package_name'] ?? 'Home Premium 50M')) ?>" title="Terbitkan Work Order Instalasi" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded text-[10px] inline-flex items-center gap-1">
                            <i class="fa-solid fa-screwdriver-wrench text-[9px]"></i> WO
                        </a>
                        <a href="<?= base_url('crm/detail.php?id=' . $c['id']) ?>" class="px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold rounded text-[10px] inline-flex items-center gap-1">
                            <i class="fa-solid fa-id-card text-[9px]"></i> Detail
                        </a>
                        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan <?= addslashes($c['name']) ?>?');">
                            <input type="hidden" name="action" value="delete_customer">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <input type="hidden" name="redirect" value="crm/daftar.php">
                            <button type="submit" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded text-[10px]">
                                <i class="fa-solid fa-trash-can text-[9px]"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Top-up Perpanjangan Masa Aktif Prabayar -->
<div id="modalTopupPrepaid" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-bolt text-purple-600"></i> Top-Up Perpanjangan Masa Aktif Prabayar
            </h4>
            <button onclick="closeTopupModal()" class="text-slate-400 hover:text-slate-600 text-base font-bold">&times;</button>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="renew_prepaid_customer">
            <input type="hidden" name="redirect" value="crm/daftar.php">
            <input type="hidden" id="topupCustId" name="id" value="">

            <div class="p-3 bg-purple-50 border border-purple-100 rounded-xl space-y-1 text-purple-900">
                <span class="text-[10px] text-purple-600 block uppercase font-bold">Pelanggan Prabayar</span>
                <strong id="topupCustName" class="text-sm font-bold block text-purple-950">-</strong>
                <span id="topupCustPackage" class="text-xs text-purple-800 block">-</span>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Pilih Durasi Perpanjangan Masa Aktif</label>
                <select name="days" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                    <option value="30" selected>+ 30 Hari (1 Bulan Kalender)</option>
                    <option value="60">+ 60 Hari (2 Bulan Kalender)</option>
                    <option value="90">+ 90 Hari (3 Bulan Kalender - Kuartal)</option>
                </select>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Metode Pembayaran Top-up</label>
                <select name="payment_method" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                    <option value="QRIS Dinamis" selected>QRIS Dinamis (Real-time)</option>
                    <option value="Kasir Tunai HQ">Kasir Tunai HQ (Offline)</option>
                    <option value="VA BCA">Virtual Account BCA</option>
                    <option value="VA Mandiri">Virtual Account Mandiri</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeTopupModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 shadow-md transition flex items-center gap-1.5">
                    <i class="fa-solid fa-check"></i> Proses Top-up & Terbitkan Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openTopupModal(id, name, pkgName, price) {
    document.getElementById('topupCustId').value = id;
    document.getElementById('topupCustName').innerText = name;
    document.getElementById('topupCustPackage').innerText = pkgName + " - Rp " + Number(price).toLocaleString('id-ID');
    document.getElementById('modalTopupPrepaid').classList.remove('hidden');
}
function closeTopupModal() {
    document.getElementById('modalTopupPrepaid').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
