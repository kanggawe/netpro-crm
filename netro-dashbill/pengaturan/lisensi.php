<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Manajemen Lisensi & Aktivasi Sistem";
$page_subtitle = "Status sertifikat lisensi enterprise, kuota kapasitas pelanggan, dan pembaharuan kunci produk.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

$licenseKey = Setting::get('license_key', 'NETPRO-ENT-99120-88129-BC99A-2026-PRO');
$licenseType = Setting::get('license_type', 'ENTERPRISE PERPETUAL (UNLIMITED)');
$licenseHolder = Setting::get('license_holder', 'PT NETPRO TELEKOMUNIKASI INDONESIA');
$licenseHwid = Setting::get('license_hwid', 'HWID-7F89-9A01-BC22-E981');
$licenseIssued = Setting::get('license_issued', '14 Jan 2024');
$licenseExpiry = Setting::get('license_expiry', 'LIFETIME (Dukungan Prioritas s/d 2029)');
$licenseStatus = Setting::get('license_status', 'ACTIVE');

$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'license_updated'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-4xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Kunci lisensi baru berhasil diverifikasi & diaktifkan pada server lokal!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-4xl mx-auto">
    <!-- License Certificate Banner Card (RedDash Style) -->
    <div class="bg-gradient-to-r from-brand-950 via-slate-950 to-brand-950 text-white p-8 rounded-3xl shadow-xl border border-brand-900/40 relative overflow-hidden">
        <div class="absolute top-0 left-1/4 w-32 h-32 bg-brand-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-5">
                <div class="w-18 h-18 p-4 bg-gradient-to-tr from-brand-600 to-rose-600 text-white rounded-2xl flex items-center justify-center font-black text-3xl shadow-xl shadow-brand-950/50 ring-1 ring-white/20">
                    <i class="fa-solid fa-certificate text-2xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-400/40 rounded-full font-bold font-mono text-[10px] flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> STATUS: <?= $licenseStatus ?>
                        </span>
                        <span class="text-brand-300/80 font-mono text-[10px]">On-Premise Node</span>
                    </div>
                    <h2 class="font-extrabold text-white text-xl tracking-wide mt-1"><?= htmlspecialchars($licenseType) ?></h2>
                    <p class="text-slate-300 text-xs mt-0.5">Pemilik Lisensi: <strong class="text-white"><?= htmlspecialchars($licenseHolder) ?></strong></p>
                </div>
            </div>
            <div class="text-left md:text-right space-y-1">
                <span class="text-slate-400 block text-[10px] uppercase font-semibold">Masa Berlaku</span>
                <strong class="text-emerald-400 font-bold text-sm block"><?= htmlspecialchars($licenseExpiry) ?></strong>
                <span class="text-slate-400 font-mono text-[10px] block">Aktivasi: <?= htmlspecialchars($licenseIssued) ?></span>
            </div>
        </div>
    </div>

    <!-- 4 License Limits & Entitlements -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Kapasitas Pelanggan (Subscribers)</span>
            <strong class="text-2xl font-bold text-blue-600">UNLIMITED</strong>
            <span class="text-slate-400 block mt-0.5">Saat Ini: <strong>1,245 Akun</strong> (Tanpa Batas)</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Router NAS & OLT Nodes</span>
            <strong class="text-2xl font-bold text-emerald-600">UNLIMITED</strong>
            <span class="text-emerald-600 font-medium block mt-0.5">Multi-Router MikroTik Core</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">Kantor Cabang / POP</span>
            <strong class="text-2xl font-bold text-indigo-600">UNLIMITED</strong>
            <span class="text-indigo-600 font-medium block mt-0.5">Multi-Branch Regional</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-slate-400 font-semibold uppercase text-[10px] block">User Admin & Operator</span>
            <strong class="text-2xl font-bold text-purple-600">UNLIMITED</strong>
            <span class="text-slate-400 block mt-0.5">Role-Based Access Control</span>
        </div>
    </div>

    <!-- 2 Columns: License Details & Form Activation -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left: Certificate Data Details -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-file-contract text-blue-600"></i> Rincian Kunci Sertifikat Lisensi
                </h3>
                <button onclick="triggerToast('Cloud Sync', 'Sertifikat lisensi tersinkronisasi valid dengan Cloud Licensing Server.')" class="text-blue-600 font-bold hover:underline text-[11px]">
                    <i class="fa-solid fa-arrows-rotate"></i> Cek Online
                </button>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="text-slate-400 block text-[10px] uppercase font-semibold">Kunci Lisensi Terpasang (Serial Product Key)</label>
                    <p class="font-mono text-slate-900 font-bold text-xs bg-slate-50 p-2.5 rounded-lg border border-slate-200 select-all"><?= htmlspecialchars($licenseKey) ?></p>
                </div>

                <div>
                    <label class="text-slate-400 block text-[10px] uppercase font-semibold">Hardware Machine Fingerprint (HWID)</label>
                    <p class="font-mono text-slate-700 font-bold text-xs bg-slate-50 p-2.5 rounded-lg border border-slate-200 select-all"><?= htmlspecialchars($licenseHwid) ?></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-slate-400 block text-[10px] uppercase font-semibold">Tipe Deployment</label>
                        <strong class="text-slate-800 text-xs block mt-0.5">Dedicated On-Premise</strong>
                    </div>
                    <div>
                        <label class="text-slate-400 block text-[10px] uppercase font-semibold">Level Support</label>
                        <strong class="text-emerald-600 text-xs block mt-0.5">24/7 Priority Engineer SLA</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Update License Key Form -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-key text-emerald-600"></i> Pembaharuan / Re-Aktivasi Kunci Lisensi
                </h3>
                <p class="text-slate-400">Masukkan product key lisensi baru untuk memperpanjang masa dukungan atau upgrade paket.</p>
            </div>

            <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-4">
                <input type="hidden" name="action" value="save_settings">
                <input type="hidden" name="redirect" value="pengaturan/lisensi.php?msg=license_updated">

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Product License Key Baru</label>
                    <input type="text" name="license_key" placeholder="XXXXX-XXXXX-XXXXX-XXXXX-XXXXX" required value="<?= htmlspecialchars($licenseKey) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-blue-600 focus:bg-white focus:border-blue-500">
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Atas Nama Pemilik Lisensi</label>
                    <input type="text" name="license_holder" value="<?= htmlspecialchars($licenseHolder) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> Validasi & Simpan Lisensi
                </button>
            </form>
        </div>
    </div>

    <!-- EULA Agreement Info Box -->
    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-slate-600 space-y-1 leading-relaxed">
        <strong class="text-slate-900 font-bold block flex items-center gap-1.5">
            <i class="fa-solid fa-shield-halved text-blue-600"></i> Perjanjian Penggunaan Lisensi Perangkat Lunak (EULA):
        </strong>
        <p class="text-[11px]">
            Lisensi ini memberikan hak eksklusif kepada <strong><?= htmlspecialchars($licenseHolder) ?></strong> untuk mengoperasikan sistem billing dan manajemen ISP ini tanpa batasan jumlah pelanggan pada infrastruktur server milik perusahaan. Seluruh database, data pelanggan, dan kunci enkripsi tersimpan 100% lokal secara mandiri.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
