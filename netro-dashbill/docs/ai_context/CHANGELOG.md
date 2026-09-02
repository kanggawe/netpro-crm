# 📝 CHANGELOG — NETPRO CRM

Semua perubahan penting pada proyek **NETPRO CRM** dicatat dalam berkas ini.  
Format berpedoman pada [Keep a Changelog](https://keepachangelog.com/).

---

## [Unreleased]
### Added
- Standardisasi sistem konteks dan memori AI di `docs/ai_context/`.
- Perbaikan keterhubungan dan alur kerja lintas halaman (*End-to-End Business Flow*):
  - Integrasi tombol aktivasi instan dari lembar BAST (`crm/berita_acara.php`) ke status online dan penerbitan faktur tagihan pertama.
  - Shortcut terintegrasi dari daftar pelanggan (`crm/daftar.php`) ke WO Instalasi prefilled, survey, dan tiket gangguan.
  - Auto-select invoice dan integrasi kasir di `billing/pembayaran.php` dari tautan daftar tagihan.
  - Sinkronisasi otomatis Jurnal Umum PSAK 72 seimbang (Debit Kas/Bank, Kredit Pendapatan & PPN) setiap kali pembayaran invoice dikonfirmasi.
- Standardisasi konsistensi navigasi, action buttons, dan sistem breadcrumbs:
  - Penambahan Dynamic Enterprise Breadcrumbs di `includes/navbar.php` dengan pemetaan modul otomatis dan dukungan custom `$breadcrumbs`.
  - Auto-expand menu accordion dan highlighting aktif (`sidebar-glow-active`) di `includes/sidebar.php` pada seluruh 77 rute subpage.
  - Verifikasi otomatis 77 rute navigasi tanpa broken link di `scratch/test_navigation_and_breadcrumbs.php` (77/77 Valid).
- Standardisasi Parameter URL & Foreign Key (FK) Integrity:
  - Sinkronisasi lookup `customer_id`, `invoice_id`, `username` di `billing/invoice.php`, `billing/kwitansi.php`, `billing/daftar.php`, `tickets/list.php`, `crm/survey.php`, dan `radius/users.php`.
  - Filter isolasi data pelanggan pada daftar tagihan dan antrean tiket insiden.
  - Verifikasi relasi foreign key dan parameter handshake di `scratch/test_param_and_fk_relations.php` (7/7 Valid).
- Penyelarasan Hak Akses (RBAC) per Halaman & Page-Level Guard:
  - Penambahan Page-Level 403 Forbidden Access Shield di `includes/header.php`.
  - Matriks RBAC komprehensif 10 role operasional di `config/app.php`.
  - Pengujian simulasi hak akses multi-role di `scratch/test_rbac_access_control.php` (27/27 Valid).
- Automated Background Billing Scheduler & Leap-Year Safe Daemon (TASK-02):
  - Daemon CLI `cron/billing_scheduler.php` (`--all`, `--generate`, `--isolir`, `--reminder`).
  - Proteksi *Month-Safe Clamping* untuk anomali Februari (28/29 hari kabisat) & tanggal 29-31.
  - Auto-isolir pelanggan melewati cut-off tanggal jatuh tempo + grace period.
  - Antrean notifikasi tagihan WhatsApp Gateway (H-3, H-1, H+1).
- Pemisahan Arsitektur Dual Database Multi-Server:
  - Dual PDO Connection Engine di `config/database.php` (`$pdo` / `get_db()` vs `$pdoRadius` / `get_radius_db()`).
  - Skema resmi PostgreSQL FreeRADIUS 3.0 (`database/radius_database_schema.sql`).
  - Skema database aplikasi mandiri (`database/app_database_schema.sql`).
  - Model `RadCheck`, `RadAcct`, `RadiusUser`, `RadiusNas` tersinkronisasi lintas database.
- Perbaikan Dinamisasi Telemetri Profil 360° Pelanggan:
  - `crm/detail.php`: Menghilangkan data dummy statis pada 4 kartu telemetri. Status Otentikasi Jaringan, GPON Loss, Billing PPN, dan Trafik Real-time kini 100% dinamis dan akurat membaca status riil pelanggan (`inactive` / `active` / `isolated`), data SPK Work Order, dan riwayat invoice.
- Perbaikan CSS Backdrop Modal & Navbar Blur (`BUG-006`):
  - `assets/css/style.css`: Menambahkan aturan CSS reset `.space-y-6 > .fixed { margin-top: 0 !important; }` untuk mengatasi selector Tailwind yang menambahkan margin-top pada modal fixed backdrop.
  - `includes/navbar.php`: Mengatur z-index header ke `z-20` dan modal backdrop ke `z-index: 9999` sehingga backdrop blur menutup seluruh layar viewport (termasuk navbar atas) secara merata tanpa celah.
- Modernisasi Desain Modal Dialog Global (*Next-Gen Modern SaaS UI*):
  - Mengganti header hitam kaku dengan *smooth gradient header* dan *icon badge pill*.
  - Menambahkan animasi pop-in spring (`modalPop`) dengan radius `rounded-3xl` (24px) dan shadow `shadow-2xl`.
  - Input form modern dengan border halus, state hover/focus dengan *blue glowing focus ring* (`ring-4 ring-blue-500/12`).
  - Action footer terpadu: tombol Batal sekunder + tombol Simpan gradien biru-indigo dengan efek hover-lift.
- Auto-Center Scrolling Menu Sidebar Aktif:
  - `includes/sidebar.php` & `assets/js/app.js`: Menambahkan mekanisme auto-scroll otomatis yang menghitung posisi elemen menu yang sedang aktif (`.sidebar-glow-active`) dan memosisikannya tepat di tengah vertikal viewport scrollbar sidebar saat halaman dibuka.
- Standardisasi Desain Invoice & Faktur Pajak Resmi (Screen & Print A4):
  - `billing/invoice.php` & `billing/cetak_invoice.php`: Menyeragamkan tata letak kop perusahaan (`PT MITRAXCON SYNERGY UTAMA`), kotak data pelanggan & skema PPN, rincian DPP/PPN/Total bayar final, QR code & Barcode Code-128 berdampingan, stempel watermark `LUNAS / PAID` (mint emerald), serta tanda tangan resmi Finance & Billing Department yang presisi 100% pada tampilan web maupun pratinjau cetak A4.
- Desain Ulang Halaman Login Modern (*Next-Gen Split-Screen Auth*):
  - `login.php`: Mengadaptasi arsitektur split-screen berkelas dari template `authentication_page.html` yang disesuaikan dengan identitas NETPRO CRM. Sisi kiri menampilkan lengkungan *left-curve gradient* dengan 4 kartu pilar fitur (Pelanggan, Billing, GPON/RADIUS, Keamanan) dan telemetri node online; sisi kanan menampilkan *floating white card* elegan dengan pola *dot-pattern*, input form modern ber-ikon, toggle password, proteksi 2FA OTP, serta tombol demo quick-switch 1-klik yang muat sempurna dalam 1 viewport tanpa scrollbar.
- Penerapan Menyeluruh Tema & Desain RedDash ke Seluruh Proyek (*Full Project RedDash Theme Deployment*):
  - `includes/header.php`: Konfigurasi token Tailwind global untuk spektrum palet `brand` lengkap (`brand-50` s/d `brand-950`), lebar `w-76`, font `Plus Jakarta Sans`, dan breadcrumbs dengan aksen `hover:text-brand-600`.
  - `includes/sidebar.php`: Desain RedDash terintegrasi penuh ke 77 rute berkas modul (CRM, Billing, RADIUS, Keuangan, NOC, Ticketing, HR, Payroll, Aset, Marketing, Kalkulator, Kinerja, Laporan, Pengaturan).
  - `includes/navbar.php`: Header navbar modern ber-backdrop blur (`bg-white/85 backdrop-blur-xl`) dengan penataan judul halaman bersih, badge RADIUS online, bel notifikasi ping, dan avatar user modern.
  - `dashboard/utama.php`: RedDash alert pulse banner, 4 kartu KPI bergaris aksen marun di kiri (`w-2 bg-brand-600`), kartu analitik `rounded-3xl`, dan widget status node topologi jaringan.
  - `assets/css/style.css`: Scrollbar ultra-ramping `4px` - `5px` beraksen merah transparan dengan efek glow saat hover, serta fokus form input modal dengan pendar `rgba(220, 38, 38, 0.15)`.
- Telemetri Live & Heartbeat Real-Time Halaman Login:
  - `login.php`: Mengaktifkan jam server WIB berdetik secara *real-time* per detik (`1000ms`) dan simulator heartbeat latensi node aktif (`0.10ms - 0.15ms`) yang berdenyut setiap 3 detik.
  - Memulihkan kartu 3-baris indikator telemetri (Node Status, Waktu Server, Lisensi Korporat) dan badge footer bawah (Versi & Enkripsi TLS 1.3).

### In Progress
- Integrasi live socket polling status port OLT GPON SNMP.
- Verifikasi ketahanan socket CoA UDP 3799 saat Router NAS unreachable.

---

## [v2.4.0] - 2026-08-25
### Added
- **FreeRADIUS & MikroTik Router NAS Integration**:
  - Dukungan AAA untuk PPPoE dan Hotspot Voucher.
  - Implementasi Dynamic CoA Disconnect Request (UDP Port 3799 / RFC 3576).
  - Rate-Limit Queue Profile dinamis (`Mikrotik-Rate-Limit`).
- **Dual Billing Engine & Pajak**:
  - Skema Prabayar (Rolling 30 Hari) & Pascabayar (Fixed Date Jatuh Tempo tgl 20).
  - Kalkulasi tagihan awal Prorata dan Non-Prorata.
  - Perhitungan PPN 11% Residensial & Korporat (Include/Exclude).
- **Akuntansi PSAK & Kepatuhan Regulasi**:
  - Chart of Accounts (COA) 34 Akun Standar PSAK (72/73/16).
  - e-Bupot PPh 23 Unifikasi & validasi kode NTPN.
  - Perhitungan iuran PNBP Kominfo (USO 1.25% & BHP Telekomunikasi 0.50%).
- **Onboarding FTTH & BAST Digital**:
  - Validasi NIK 16 digit & koordinat GPS Leaflet GIS.
  - Work Order Teknisi (SPK) dengan pencatatan redaman OPM (dBm).
  - Auto-generate BAST Digital resmi bertanda tangan digital.
- **HRD & Payroll**:
  - Presensi GPS teknisi dan insentif BAST per titik instalasi.
  - Penggajian THP dengan potongan BPJS dan PPh 21.

---

## [v1.0.0] - 2026-08-01
### Added
- Rilis baseline database 33 tabel PostgreSQL & dashboard telemetri dasar.
