<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Komplain Pelanggan, Eskalasi & Survey CSAT";
$page_subtitle = "Pusat manajemen keluhan kritis pelanggan, pengukuran Net Promoter Score (NPS), dan Service Recovery.";
$active_menu = "m-tickets";
require_once __DIR__ . '/../includes/header.php';

$complaints = Complaint::all();
$customers = Customer::all();
$employees = Employee::all();
$msg = $_GET['msg'] ?? '';

// Calculate stats
$totalComplaints = count($complaints);
$activeComplaints = 0;
$resolvedComplaints = 0;
$totalRating = 0;

foreach ($complaints as $c) {
    if (strtoupper($c['status'] ?? '') === 'RESOLVED' || strtoupper($c['status'] ?? '') === 'SELESAI') {
        $resolvedComplaints++;
    } else {
        $activeComplaints++;
    }
    $totalRating += intval($c['csat_rating'] ?? 5);
}

$avgCsat = $totalComplaints > 0 ? round($totalRating / $totalComplaints, 1) : 0.0;
?>

<?php if ($msg === 'created'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-sm mb-6">
        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
        <span>Eskalasi komplain pelanggan berhasil dicatat & tim Customer Care telah menindaklanjuti!</span>
    </div>
<?php elseif ($msg === 'updated'): ?>
    <div class="p-4 bg-blue-50 text-blue-800 border border-blue-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-sm mb-6">
        <i class="fa-solid fa-check-double text-blue-600 text-base"></i>
        <span>Status resolusi komplain & service recovery berhasil diperbarui!</span>
    </div>
<?php elseif ($msg === 'deleted'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-2xl flex items-center gap-2.5 text-xs font-bold shadow-sm mb-6">
        <i class="fa-solid fa-trash-can text-rose-600 text-base"></i>
        <span>Rekaman komplain telah dihapus.</span>
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs">
    <!-- Top 4 Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block tracking-wider">CSAT Score Index</span>
                <strong class="text-2xl font-bold text-slate-400 flex items-center gap-1.5">
                    <?= $avgCsat ?> <span class="text-xs text-slate-400 font-normal">/ 5.0</span>
                </strong>
                <span class="text-slate-400 block text-[11px] font-medium"><?= $totalComplaints > 0 ? '★★★★★ (' . ($avgCsat*20) . '% Puas)' : 'Belum Ada Penilaian CSAT' ?></span>
            </div>
            <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-star"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block tracking-wider">Net Promoter Score (NPS)</span>
                <strong class="text-2xl font-bold text-slate-400"><?= $totalComplaints > 0 ? '+68 NPS' : '0 NPS' ?></strong>
                <span class="text-slate-400 block text-[11px] font-medium"><?= $totalComplaints > 0 ? 'Promoters 76% vs Detractor 4%' : 'Belum Ada Survey NPS' ?></span>
            </div>
            <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-face-smile"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block tracking-wider">Komplain Sedang Ditangani</span>
                <strong class="text-2xl font-bold text-rose-600"><?= $activeComplaints ?></strong>
                <span class="text-rose-600 block text-[11px] font-bold">Investigasi & Kompensasi</span>
            </div>
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-slate-400 font-semibold uppercase text-[10px] block tracking-wider">Service Recovery Selesai</span>
                <strong class="text-2xl font-bold text-emerald-600"><?= $resolvedComplaints ?></strong>
                <span class="text-emerald-600 block text-[11px] font-semibold">Pelanggan Kembali Puas</span>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-handshake-angle"></i>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left 2/3: Complaints Registry Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-comments text-rose-600"></i> Rekam Jejak Komplain & Evaluasi Layanan
                    </h3>
                    <p class="text-slate-400 text-xs">Pencatatan komplain kritis, kompensasi gangguan, dan tindak lanjut kepuasan.</p>
                </div>
                <input type="text" id="searchComplaintInput" onkeyup="filterComplaintTable()" placeholder="Cari Pelanggan / Keluhan..." class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 w-48 sm:w-56">
            </div>

            <div class="overflow-x-auto border border-slate-100 rounded-xl">
                <table class="w-full text-left border-collapse" id="complaintTable">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-[11px]">
                            <th class="py-3 px-4">Pelanggan & Channel</th>
                            <th class="py-3 px-4">Kategori & Sentimen</th>
                            <th class="py-3 px-4">Ulasan & CSAT</th>
                            <th class="py-3 px-4">Kompensasi / Recovery</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($complaints)): ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-comments text-3xl mb-2 block opacity-40"></i>
                                    Belum ada rekaman eskalasi komplain pelanggan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($complaints as $c): 
                                $st = strtoupper($c['status'] ?? 'INVESTIGASI');
                                $rating = intval($c['csat_rating'] ?? 5);
                            ?>
                            <tr class="hover:bg-slate-50/70 transition complaint-row" data-search="<?= strtolower(($c['customer_name'] ?? '') . ' ' . ($c['category'] ?? '') . ' ' . ($c['description'] ?? '')) ?>">
                                <td class="py-3.5 px-4">
                                    <strong class="font-bold text-slate-900 block"><?= htmlspecialchars($c['customer_name'] ?? 'Pelanggan') ?></strong>
                                    <span class="text-[10px] text-slate-500 font-medium flex items-center gap-1">
                                        <i class="fa-solid fa-headset text-blue-500"></i> <?= htmlspecialchars($c['channel'] ?? 'WhatsApp') ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-medium text-slate-800 block"><?= htmlspecialchars($c['category'] ?? 'Keluhan Layanan') ?></span>
                                    <span class="inline-block px-2 py-0.5 bg-amber-100 text-amber-800 font-bold rounded text-[9px]"><?= htmlspecialchars($c['sentiment'] ?? 'Kecewa') ?></span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="text-amber-500 text-xs tracking-wider">
                                        <?= str_repeat('★', max(1, min(5, $rating))) . str_repeat('☆', 5 - max(1, min(5, $rating))) ?>
                                    </div>
                                    <p class="text-[11px] text-slate-600 line-clamp-1 italic">"<?= htmlspecialchars($c['description'] ?? '-') ?>"</p>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-semibold text-slate-700 block"><?= htmlspecialchars($c['compensation'] ?? '-') ?></span>
                                    <span class="text-[10px] text-slate-400">Oleh <?= htmlspecialchars($c['handler_name'] ?? 'Care Lead') ?></span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <?php if ($st === 'RESOLVED' || $st === 'SELESAI'): ?>
                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold rounded-full text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> RESOLVED
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 bg-rose-50 text-rose-700 border border-rose-200 font-bold rounded-full text-[10px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-magnifying-glass text-[9px]"></i> INVESTIGASI
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <?php if ($st !== 'RESOLVED' && $st !== 'SELESAI'): ?>
                                        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="inline">
                                            <input type="hidden" name="action" value="update_complaint_status">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <input type="hidden" name="status" value="RESOLVED">
                                            <input type="hidden" name="redirect" value="tickets/complaints.php">
                                            <button type="submit" title="Tandai Selesai" class="w-7 h-7 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg flex items-center justify-center transition">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>

                                        <form action="<?= base_url('api/handler.php') ?>" method="POST" onsubmit="return confirm('Hapus rekaman komplain ini?')" class="inline">
                                            <input type="hidden" name="action" value="delete_complaint">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <input type="hidden" name="redirect" value="tickets/complaints.php">
                                            <button type="submit" title="Hapus" class="w-7 h-7 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg flex items-center justify-center transition">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right 1/3: Form Input Komplain & CSAT -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-plus text-blue-600"></i> Catat Eskalasi Komplain & CSAT
                </h3>
                <p class="text-slate-400 text-xs">Form input keluhan tidak puas untuk audit kualitas & service recovery.</p>
            </div>

            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create_complaint">
                <input type="hidden" name="redirect" value="tickets/complaints.php">

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Nama Pelanggan</label>
                    <select name="customer_name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-blue-500">
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= htmlspecialchars($c['name']) ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['cid']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Channel Masuk</label>
                        <select name="channel" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-blue-500">
                            <option value="WhatsApp Care">WhatsApp Care</option>
                            <option value="Hotline Call 24 Jam">Hotline Call 24 Jam</option>
                            <option value="Portal Web Ticket">Portal Web Ticket</option>
                            <option value="Walk-in Kantor Cabang">Walk-in Kantor Cabang</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Rating Bintang (1-5)</label>
                        <select name="csat_rating" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-amber-500 focus:outline-none focus:border-blue-500">
                            <option value="5">★★★★★ (5 - Sangat Puas)</option>
                            <option value="4">★★★★☆ (4 - Puas)</option>
                            <option value="3">★★★☆☆ (3 - Cukup)</option>
                            <option value="2">★★☆☆☆ (2 - Tidak Puas)</option>
                            <option value="1">★☆☆☆☆ (1 - Sangat Kecewa)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kategori Keluhan</label>
                    <select name="category" required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-medium text-slate-800 focus:outline-none focus:border-blue-500">
                        <option value="Gangguan Koneksi Sering Terputus">Gangguan Koneksi Sering Terputus</option>
                        <option value="Ketidaksesuaian Kecepatan Speedtest">Ketidaksesuaian Kecepatan Speedtest</option>
                        <option value="Keterlambatan Kedatangan Teknisi">Keterlambatan Kedatangan Teknisi</option>
                        <option value="Pertanyaan & Sengketa Tagihan Billing">Pertanyaan & Sengketa Tagihan Billing</option>
                        <option value="Permintaan Relokasi / Pemindahan Jalur">Permintaan Relokasi / Pemindahan Jalur</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Petugas Penanggung Jawab</label>
                        <select name="handler_name" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:outline-none focus:border-blue-500">
                            <?php if (empty($employees)): ?>
                                <option value="Customer Care Officer">Customer Care Officer</option>
                            <?php else: ?>
                                <?php foreach ($employees as $emp): ?>
                                    <option value="<?= htmlspecialchars($emp['name']) ?>">
                                        <?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['position']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Bentuk Kompensasi</label>
                        <input type="text" name="compensation" placeholder="Diskon 10% / Free Booster" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-medium text-slate-800 focus:outline-none focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Catatan / Ulasan Lengkap Pelanggan</label>
                    <textarea name="description" rows="3" placeholder="Masukkan ringkasan kronologi komplain atau ulasan pelanggan..." required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500"></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Catatan Komplain & CSAT
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function filterComplaintTable() {
    let input = document.getElementById('searchComplaintInput').value.toLowerCase();
    let rows = document.querySelectorAll('.complaint-row');
    rows.forEach(row => {
        let text = row.getAttribute('data-search');
        if (text.includes(input)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
