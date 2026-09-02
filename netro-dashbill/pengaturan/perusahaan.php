<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Identitas Perusahaan & Cabang Operasional";
$page_subtitle = "Profil legalitas korporasi ISP, izin Kominfo, keanggotaan APJII, ASN BGP, dan manajemen kantor cabang.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

// Corporate Identity Settings
$companyName = Setting::get('company_name', 'PT NETPRO TELEKOMUNIKASI INDONESIA');
$companyBrand = Setting::get('company_brand', 'NETPRO FIBER BROADBAND');
$companyNib = Setting::get('company_nib', '9120003418821');
$companyKbli = Setting::get('company_kbli', '61100 (Jaringan Telekomunikasi Kabel) & 61999');
$companyNpwp = Setting::get('company_npwp', '01.234.567.8-901.000');
$companyNppkp = Setting::get('company_nppkp', 'PEM-0912/WPJ.06/KP.0303/2021');
$companyIzinIsp = Setting::get('company_izin_isp', 'KEPMENKOMINFO NO. 412/TEL.02.02/2021');
$companyIzinJartaplok = Setting::get('company_izin_jartaplok', 'IZIN-JARTAPLOK-NETPRO-2022-09');
$companyApjii = Setting::get('company_apjii', 'ANGGOTA APJII NO. 428/REG-2022');
$companyAsn = Setting::get('company_asn', 'AS139981 (NETPRO-AS-ID)');
$companyIpv4 = Setting::get('company_ipv4', '103.145.220.0/22 (1.024 IP Address)');
$companyIpv6 = Setting::get('company_ipv6', '2001:df7:5100::/48');

// Contact & Location
$companyAddress = Setting::get('company_address', 'Gedung Cyber Lt. 5, Jl. Kuningan Barat No. 8, Jakarta Selatan 12710');
$companyPhone = Setting::get('company_phone', '021-52908812');
$companyCallCenter = Setting::get('company_call_center', '1500-988 (24 Jam)');
$companyWa = Setting::get('company_wa', '0812-9876-5432');
$companyEmail = Setting::get('company_email', 'billing@netpro.co.id');
$companyWebsite = Setting::get('company_website', 'https://netpro.co.id');
$companyGps = Setting::get('company_gps', '-6.2384, 106.8245');

// Signatory
$companyDirector = Setting::get('company_director', 'Muhammad Ibrahim, S.Kom., M.T.');
$companyDirectorTitle = Setting::get('company_director_title', 'Direktur Utama (President Director)');

$branches = Branch::all();
$totalSubs = 0;
foreach ($branches as $b) {
    $totalSubs += $b['subs_count'];
}
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'saved_identity'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Identitas & Profil Legalitas Perusahaan berhasil diperbarui ke database!
    </div>
<?php elseif ($msg === 'created_branch'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Kantor Cabang baru berhasil didaftarkan ke sistem!
    </div>
<?php elseif ($msg === 'deleted_branch'): ?>
    <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-trash-can text-rose-600 text-sm"></i>
        Kantor Cabang telah berhasil dihapus.
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-5xl mx-auto">
    <!-- Top 4 Identity Quick Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Perizinan Kominfo</span>
                <strong class="text-lg font-bold text-slate-900">ISP & Jartaplok</strong>
                <span class="text-emerald-600 font-bold block mt-0.5">✓ Terverifikasi Legal</span>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-certificate"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">BGP ASN Number</span>
                <strong class="text-lg font-bold text-blue-600">AS139981</strong>
                <span class="text-blue-600 font-medium block mt-0.5">Anggota Resmi APJII</span>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-network-wired"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Alokasi IP Publik IDNIC</span>
                <strong class="text-lg font-bold text-indigo-600">/22 IPv4 • /48 IPv6</strong>
                <span class="text-slate-400 block mt-0.5">1.024 Public IPv4</span>
            </div>
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-server"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Jaringan Cabang Ops</span>
                <strong class="text-lg font-bold text-purple-600"><?= count($branches) ?> Cabang</strong>
                <span class="text-purple-600 font-medium block mt-0.5"><?= number_format($totalSubs, 0, ',', '.') ?> Pelanggan</span>
            </div>
            <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-building"></i></div>
        </div>
    </div>

    <!-- Main Corporate Identity Form -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-id-card-clip text-blue-600"></i> Identitas & Data Legalitas Resmi Perusahaan ISP
                </h3>
                <p class="text-slate-400">Data identitas ini digunakan pada Kop Surat, Invoice Tagihan, Faktur Pajak, Kwitansi, dan BAST.</p>
            </div>
            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded-full text-[10px]">VERIFIED CORPORATE PROFILE</span>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="save_settings">
            <input type="hidden" name="redirect" value="pengaturan/perusahaan.php?msg=saved_identity">

            <!-- 1. Identitas Badan Hukum & Pajak -->
            <div class="space-y-3">
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider text-blue-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                    <i class="fa-solid fa-scale-balanced"></i> 1. Identitas Badan Hukum & Perpajakan (DJP)
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Resmi Badan Usaha (PT/CV)</label>
                        <input type="text" name="company_name" value="<?= htmlspecialchars($companyName) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Dagang / Commercial Brand</label>
                        <input type="text" name="company_brand" value="<?= htmlspecialchars($companyBrand) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nomor Induk Berusaha (NIB 13 Digit)</label>
                        <input type="text" name="company_nib" value="<?= htmlspecialchars($companyNib) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">NPWP 16 Digit / 15 Digit</label>
                        <input type="text" name="company_npwp" value="<?= htmlspecialchars($companyNpwp) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">No. Pengukuhan PKP (NPPKP / SPPKP)</label>
                        <input type="text" name="company_nppkp" value="<?= htmlspecialchars($companyNppkp) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-emerald-600 font-bold">
                    </div>
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Klasifikasi Baku Lapangan Usaha Indonesia (KBLI)</label>
                    <input type="text" name="company_kbli" value="<?= htmlspecialchars($companyKbli) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-slate-800">
                </div>
            </div>

            <!-- 2. Perizinan Telekomunikasi & Alokasi Jaringan -->
            <div class="space-y-3 pt-2">
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider text-indigo-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                    <i class="fa-solid fa-tower-broadcast"></i> 2. Perizinan Kominfo, APJII & Alokasi BGP IP Transit
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">No. SK Izin Penyelenggaraan ISP (Kominfo)</label>
                        <input type="text" name="company_izin_isp" value="<?= htmlspecialchars($companyIzinIsp) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-blue-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">No. Izin Jaringan Lokal (Jartaplok/Jartup)</label>
                        <input type="text" name="company_izin_jartaplok" value="<?= htmlspecialchars($companyIzinJartaplok) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nomor Anggota APJII</label>
                        <input type="text" name="company_apjii" value="<?= htmlspecialchars($companyApjii) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Autonomous System Number (ASN)</label>
                        <input type="text" name="company_asn" value="<?= htmlspecialchars($companyAsn) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-indigo-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Prefix Alokasi IPv4 Publik IDNIC</label>
                        <input type="text" name="company_ipv4" value="<?= htmlspecialchars($companyIpv4) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-purple-600">
                    </div>
                </div>
            </div>

            <!-- 3. Alamat Kantor Pusat, Helpdesk & Kontak Resmi -->
            <div class="space-y-3 pt-2">
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider text-emerald-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                    <i class="fa-solid fa-headset"></i> 3. Lokasi Kantor Pusat (HQ) & Kontak Layanan 24 Jam
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">No. Telepon Kantor (Hunting)</label>
                        <input type="text" name="company_phone" value="<?= htmlspecialchars($companyPhone) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-slate-900">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Call Center 24 Jam (Toll-Free/Hotline)</label>
                        <input type="text" name="company_call_center" value="<?= htmlspecialchars($companyCallCenter) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-emerald-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">WhatsApp Official Billing & CS</label>
                        <input type="text" name="company_wa" value="<?= htmlspecialchars($companyWa) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-emerald-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Email Resmi Billing & Info</label>
                        <input type="email" name="company_email" value="<?= htmlspecialchars($companyEmail) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Website Perusahaan</label>
                        <input type="url" name="company_website" value="<?= htmlspecialchars($companyWebsite) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-blue-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Koordinat GPS Kantor Pusat</label>
                        <input type="text" name="company_gps" value="<?= htmlspecialchars($companyGps) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                </div>

                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Alamat Gedung & Kantor Pusat Lengkap</label>
                    <textarea name="company_address" rows="2" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 leading-relaxed"><?= htmlspecialchars($companyAddress) ?></textarea>
                </div>
            </div>

            <!-- 4. Penanggung Jawab & Penandatangan Dokumen -->
            <div class="space-y-3 pt-2">
                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider text-purple-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                    <i class="fa-solid fa-signature"></i> 4. Penanggung Jawab & Otorisasi Dokumen Resmi
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Direktur Utama / Penanggung Jawab</label>
                        <input type="text" name="company_director" value="<?= htmlspecialchars($companyDirector) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Jabatan Penandatangan</label>
                        <input type="text" name="company_director_title" value="<?= htmlspecialchars($companyDirectorTitle) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Lengkap Identitas Perusahaan
            </button>
        </form>
    </div>

    <!-- Branch Offices Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <div>
                <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-network-wired text-indigo-600"></i> Daftar Kantor Cabang & Area Coverage ISP
                </h3>
                <p class="text-slate-400">Total <?= count($branches) ?> Kantor Cabang & POP Regional terhubung ke sistem terpusat.</p>
            </div>
            <button onclick="document.getElementById('modalAddBranch').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg shadow flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Tambah Cabang Baru
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th class="py-3 px-4">Kode Cabang</th>
                        <th class="py-3 px-4">Nama Kantor Cabang</th>
                        <th class="py-3 px-4">Alamat Operasional</th>
                        <th class="py-3 px-4">Kepala Cabang</th>
                        <th class="py-3 px-4 font-mono text-center">Total Subs</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($branches)): ?>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-400">Belum ada kantor cabang didaftarkan.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($branches as $b): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50">
                        <td class="py-3.5 px-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($b['code']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($b['name']) ?></td>
                        <td class="py-3.5 px-4 text-slate-600"><?= htmlspecialchars($b['address']) ?></td>
                        <td class="py-3.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($b['manager']) ?></td>
                        <td class="py-3.5 px-4 font-mono font-bold text-center text-indigo-600"><?= number_format($b['subs_count'], 0, ',', '.') ?> Akun</td>
                        <td class="py-3.5 px-4 text-right">
                            <form action="<?= base_url('api/handler.php') ?>" method="POST" onsubmit="return confirm('Hapus cabang ini?')" class="inline">
                                <input type="hidden" name="action" value="delete_branch">
                                <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                <input type="hidden" name="redirect" value="pengaturan/perusahaan.php">
                                <button type="submit" class="text-rose-600 font-bold hover:underline">Hapus</button>
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

<!-- Modal Tambah Cabang -->
<div id="modalAddBranch" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm">Tambah Kantor Cabang Baru</h3>
            <button onclick="document.getElementById('modalAddBranch').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold">✕</button>
        </div>
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="space-y-3">
            <input type="hidden" name="action" value="create_branch">
            <input type="hidden" name="redirect" value="pengaturan/perusahaan.php">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Kode Cabang</label>
                    <input type="text" name="code" required placeholder="CBG-TGR-04" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Target Pelanggan</label>
                    <input type="number" name="subs_count" value="150" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold text-indigo-600">
                </div>
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Nama Kantor Cabang</label>
                <input type="text" name="name" required placeholder="Cabang Tangerang BSD" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Kepala Cabang (Branch Manager)</label>
                <input type="text" name="manager" required placeholder="Nama Penanggung Jawab" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
            </div>
            <div>
                <label class="font-semibold text-slate-700 block mb-1">Alamat Kantor Cabang</label>
                <textarea name="address" rows="2" required placeholder="Alamat jalan lengkap..." class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2"></textarea>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow">Simpan Cabang Baru</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
