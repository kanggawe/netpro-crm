<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Evaluasi Kinerja 360° Karyawan";
$page_subtitle = "Penilaian berkala aspek teknis, kedisiplinan, dan koordinasi tim oleh supervisor.";
$active_menu = "m-kinerja";
require_once __DIR__ . '/../includes/header.php';

$employees = Employee::all();
$reviews = PerformanceReview::all();
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'created_review'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-2xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Evaluasi performa 360° berhasil disimpan ke database! Skor terakumulasi otomatis ke Leaderboard.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-3xl mx-auto">
    <!-- Form Input Review -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
        <div class="border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-blue-600"></i> Form Penilaian Review Kinerja 360°
            </h3>
            <p class="text-slate-400">Hasil review akan terakumulasi ke dalam skor leaderboard dan insentif bonus bulanan.</p>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create_review">
            <input type="hidden" name="redirect" value="kinerja/review.php">

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Pilih Karyawan yang Dinilai</label>
                <select name="employee_name" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= htmlspecialchars($emp['name']) ?>"><?= htmlspecialchars($emp['nik']) ?> - <?= htmlspecialchars($emp['name']) ?> (<?= htmlspecialchars($emp['position']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Skor Keahlian Teknis & SOP (0-100)</label>
                    <input type="number" name="tech_score" min="0" max="100" value="95" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-blue-600">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Skor Kedisiplinan & Presensi (0-100)</label>
                    <input type="number" name="discipline_score" min="0" max="100" value="92" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-blue-600">
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Catatan Evaluasi & Rekomendasi Supervisor</label>
                <textarea name="notes" rows="3" placeholder="Sangat responsif dan memiliki inisiatif tinggi dalam penanganan perbaikan kabel optik..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Hasil Review Evaluasi
            </button>
        </form>
    </div>

    <!-- Recent Reviews Feed -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="font-bold text-slate-900 text-sm">Riwayat Penilaian Review Terbaru</h3>
        <div class="space-y-3">
            <?php foreach ($reviews as $rev): ?>
            <div class="p-4 rounded-xl border border-slate-100 bg-slate-50 space-y-2">
                <div class="flex justify-between items-center">
                    <div>
                        <strong class="text-slate-900 font-bold"><?= htmlspecialchars($rev['employee_name']) ?></strong>
                        <span class="text-slate-400 block text-[11px]"><?= htmlspecialchars($rev['position'] ?? 'Staf Operasional') ?></span>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded-lg text-xs">Total Skor: <?= $rev['total_score'] ?> / 100</span>
                    </div>
                </div>
                <p class="text-slate-600 text-[11px] italic">"<?= htmlspecialchars($rev['notes'] ?: 'Kinerja memenuhi standar operasional perusahaan.') ?>"</p>
                <span class="text-slate-400 text-[10px] block">Penilai: <?= htmlspecialchars($rev['supervisor_name'] ?? 'Supervisor') ?> • <?= htmlspecialchars($rev['created_at']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
