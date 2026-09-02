# 📝 CHANGELOG — NETPRO CRM

Semua perubahan penting pada proyek **NETPRO CRM** dicatat dalam berkas ini.  
Format berpedoman pada [Keep a Changelog](https://keepachangelog.com/).

---

## [Unreleased]

---

## [v4.0.0-ENTERPRISE] - 2026-08-26
### Added
- **Penerapan Menyeluruh Tema RedDash & Dark Obsidian Executive UI**:
  - `includes/header.php`, `includes/sidebar.php`, `includes/navbar.php`: Spektrum palet brand komprehensif, ambient glow, backdrop blur, dan konsistensi RedDash di seluruh 77 rute subpage aplikasi.
- **Komponen Breadcrumb Chevron Dinamis & Multi-Step Wizard Ribbon**:
  - `assets/css/style.css`: Menambahkan komponen `.arrow-breadcrumb` dengan sistem chevron polygon bersambung presisi (`clip-path`), badge status ikonik/angka (`.arrow-breadcrumb-badge`), dan 3 status visual (`is-completed`, `is-active`, `is-inactive`).
  - `includes/header.php`: Mengintegrasikan breadcrumb chevron di seluruh halaman aplikasi di luar navbar.
  - `crm/registrasi.php`: Mengubah wizard pendaftaran 3 tahap (`1. Identitas KTP` ➔ `2. Paket & Pajak PPN` ➔ `3. Lokasi & Aktivasi`) menjadi stepper chevron interaktif dengan centang `✓` otomatis.
- **Probe Telemetri Hardware & Pemeriksaan Fisik Jaringan Nyata**:
  - `config/app.php`: Menambahkan fungsi `is_hardware_node_online($host, $port, $timeout)` untuk menguji konektivitas fisik soket non-blocking perangkat secara langsung (MikroTik Port 8728, OLT Telnet 23/80, dan FreeRADIUS Port 1812/1813).
  - `dashboard/utama.php`: Matriks 4 node hardware (Core Router, OLT ZTE, OLT Huawei, FreeRADIUS) kini 100% dinamis mendeteksi kondisi fisik perangkat di lapangan dan mengubah status badge secara real-time (`ONLINE` / `OFFLINE`) beserta diagnosis RTO.
  - `dashboard/noc.php`: Indikator ringkasan perangkat inti OLT/NAS menyesuaikan jumlah router yang benar-benar tersambung secara fisik.
- **Penyelarasan Desain Navbar & Sidebar ke Tema Eksekutif Crimson Red**:
  - `includes/navbar.php`: Mengubah tampilan header navbar menjadi warna merah gradien mewah (`bg-gradient-to-r from-brand-800 via-brand-700 to-brand-900`), teks kontras putih, dan aksen lencana status modern.
  - `includes/sidebar.php`: Mengubah latar sidebar menjadi merah ruby eksekutif (`bg-gradient-to-b from-brand-900 via-brand-950 to-brand-950`), menu accordion aktif putih kontras, submenu glow transparan elegan, dan pencarian berlatar gelap transparan.
- **Manajemen CRUD Lengkap (Create, Read, Update, Delete) Katalog CRM**:
  - `crm/paket.php`: Menambahkan tombol **Edit Paket** (modal form terisi otomatis) dan **Hapus Paket** (konfirmasi hapus) pada setiap kartu katalog paket internet.
  - `crm/addon.php`: Menambahkan tombol aksi Edit & Hapus pada kartu layanan bernilai tambah (Add-on) beserta modal edit dialog.
  - `crm/promo.php`: Menambahkan kolom Aksi Edit & Hapus pada tabel daftar voucher diskon/promo beserta modal edit dialog.
  - `config/models.php` & `api/handler.php`: Menambahkan method model `update()` dan `delete()` serta controller handler pada entitas `Package`, `Addon`, dan `Promo`.
- **Modul Entry Index Router**:
  - Menambahkan file `index.php` pada folder `dashboard/`, `crm/`, `billing/`, `finance/`, `noc/`, dan `radius/` untuk memastikan akses URL direktori langsung berjalan mulus tanpa error 403 / 404.
- **Dual Database Architecture (Multi-Server)**:
  - Pemisahan Dual PDO Engine di `config/database.php` (`$pdo` / `get_db()` vs `$pdoRadius` / `get_radius_db()`) dan skema resmi PostgreSQL FreeRADIUS 3.0.
- **Two-Factor Authentication (2FA TOTP) Challenge UI & Toast System**:
  - `login.php`: Mengintegrasikan Step-2 Challenge Screen interaktif untuk verifikasi OTP 6-digit saat akun mengaktifkan 2FA, dilengkapi User Identity Card, validasi input berjarak lebar, kode darurat *master backup codes*, alur pembatalan sesi pending ke `logout.php`, serta auto-toast notification listener untuk `invalid`, `invalid_otp`, `logged_out`, dan `2fa_updated`.
- **OAuth 2.0 Social Media Authentication & Self-Registration Engine**:
  - `login.php` & `api/handler.php`: Menghubungkan tombol 4 platform (**Google Workspace**, **GitHub**, **Facebook**, dan **X / Twitter**) ke backend OAuth SSO handler dengan provisioning akun otomatis, auto-hashing `PASSWORD_BCRYPT`, seamless 2FA challenge check, formulir pendaftaran akun mandiri (*Self-Registration*), dan integrasi notifikasi toast terpadu (`registered`, `reg_exists`, `reg_empty`, `reg_short_pass`).
- **Self-Service Lupa Password & Reset Kata Sandi**:
  - `login.php` & `api/handler.php`: Mengintegrasikan transisi layar interaktif `forgotForm` untuk pemulihan kata sandi mandiri dengan validasi identitas akun, enkripsi sandi baru `PASSWORD_BCRYPT`, audit logging, dan notifikasi konfirmasi instan.
- **Modern Spline Gradient Customer Growth Chart & 7 Dashboards Visual Overhaul**:
  - `dashboard/customers.php`: Mengubah bar chart flat kaku menjadi **Smooth Spline Gradient Area Chart** bernuansa *Crimson Red Glow* dengan label bulan kalender dinamis, kurva pertumbuhan kumulatif, doughnut chart komposisi paket 70% cutout, dan custom dark glassmorphism tooltips.
  - `dashboard/revenue.php`: Menghubungkan visual chart ke dynamic 6-month revenue timeline, Ruby Crimson gradient bar, target spline, dan empty-safe payment donut.
  - `dashboard/utama.php`: Menghubungkan dual-axis revenue & subscriber growth chart ke live database metrics dan package breakdown donut.
  - `dashboard/noc.php`: Mengganti flatline zero bandwidth chart dengan dynamic 24-hour telemetry curve bergradien merah/hijau.
  - `dashboard/tickets.php`, `dashboard/overdue.php`, `dashboard/hr.php`: Penyelarasan tooltip dark glassmorphism, rounded bars, dan border dash gridlines.
- **Full System & Database Pre-MVC Snapshot Backup**:
  - `backups/netpro_crm_pre_mvc_backup_2026_08_27.zip`: Backup arsip lengkap seluruh source code, assets, konfigurasi, dan dokumentasi AI Context.
  - `backups/app_pre_mvc.sqlite` & `backups/backup_app_db_2026_08_27_151018.sqlite`: Snapshot database terenkripsi & data riil pelanggan, invoice, serta log sistem.
- **Cryptographic URL Parameter & Anti-IDOR Suite with Native .php Server Compatibility**:
  - `config/app.php` (`class UrlCrypto`): Enkripsi parameter URL AES-256-CBC dengan verifikasi integritas HMAC-SHA256 (`url_encrypt`, `url_decrypt`), obfuscation ID numerik cepat (`mask_id`, `unmask_id`), dan penandatanganan URL bertenggat waktu (`signed_url`, `verify_signed_url`) untuk mencegah serangan IDOR (*Insecure Direct Object Reference*).
  - `config/app.php` & `includes/sidebar.php`: Menyelaraskan seluruh tautan aplikasi menggunakan format native `.php` langsung agar berjalan 100% kompatibel di server apa pun (`php -S localhost:8000`, Apache, XAMPP, Nginx) tanpa menghasilkan error 404.
  - `crm/detail.php` & `billing/invoice.php`: Dukungan dual-mode pembacaan parameter pelanggan & invoice dari plain numeric ID, masked ID, maupun encrypted token.
- **Rich Profile Management & Multi-Avatar Preset Suite**:
  - `assets/images/`: Menambahkan 4 preset karakter avatar resmi: Executive Suit (`avatar-admin.svg`), NOC Specialist (`avatar-noc.svg`), Field Tech (`avatar-tech.svg`), dan Corporate Finance (`avatar-female.svg`).
  - `pengaturan/profile.php`: Menambahkan pemilih avatar visual radio dengan JavaScript dynamic live preview instan ke header kartu profil.
  - `pengaturan/profile.php` & `config/models.php`: Menambahkan formulir data personal & korporat mendalam mencakup NIP/NIK Pegawai, Telegram ID Notifikasi NOC, Nomor WhatsApp, Wilayah Kantor Cabang, Bio Otoritas, serta sinkronisasi instan ke sesi login aktif (`$_SESSION['user']`).
- **Official Brand Logo Vector & Executive Profile Avatar**:
  - `assets/images/netpro-logo.svg`: Vektor logo resmi NETPRO CRM berbasis Ruby Crimson Gradient dengan node matriks fiber optik.
  - `assets/images/avatar-admin.svg`: Avatar profil eksekutif korporat dengan cincin badge verified administrator.
  - `includes/sidebar.php`, `includes/navbar.php`, `login.php`, `pengaturan/profile.php`: Menghubungkan seluruh penampil logo dan foto profil ke aset SVG resmi.
- **Interactive Notification Center Dropdown & Live Telemetry Alerts**:
  - `includes/navbar.php` & `assets/js/app.js`: Mengembangkan **Pusat Notifikasi Dropdown** interaktif berdesain glassmorphism terapung pada tombol lonceng navbar. Menampilkan status real-time FreeRADIUS & Dynamic CoA UDP 3799, agregasi invoice belum dibayar (*unpaid alerts*), antrean tiket gangguan aktif, telemetri fiber OLT GPON, tombol tandai dibaca (*mark as read*), serta penutupan otomatis saat klik di luar area (*click outside listener*) atau menekan tombol `Esc`.
- **Automated Leap-Year Proof Billing Scheduler**:
  - Daemon CLI `cron/billing_scheduler.php` dengan proteksi Month-Safe Clamping kalender kabisat.

### Fixed
- **Perbaikan Un-clipping Menu Dropdown Notifikasi Navbar**:
  - `includes/navbar.php`: Menghapus atribut `overflow-hidden` pada elemen `<header>` dan memindahkannya ke layer khusus lampu ambient, sehingga menu dropdown notifikasi dapat mengambang (*floating*) sempurna ke bawah tanpa terpotong batas navbar.
- **Responsivitas Breadcrumb & Navbar pada Layar Mobile / Smartphone**:
  - `includes/navbar.php`: Mencegah judul halaman bertumpuk vertikal keluar dari navbar dengan menerapkan `flex-1 min-w-0` dan `truncate` responsif (`text-sm sm:text-lg`).
  - `assets/css/style.css`: Mengatasi celah putih (*sub-pixel background bleed*) pada sambungan chevron dengan teknik **Solid Flat Overlay System** dan penataan layer `z-index` menurun (10, 9, 8...).
  - `assets/css/style.css`: Menambahkan wrapper `.arrow-breadcrumb-wrapper` dengan dukungan horizontal touch momentum scrolling (`-webkit-overflow-scrolling: touch; overflow-x: auto; flex-nowrap;`).
  - `crm/detail.php`: Memperbaiki header kartu profil pelanggan (`eling semesta alam`) agar menggunakan `flex-wrap` dan `shrink-0` pada badge sehingga nama tidak terhimpit per kata pada ponsel sempit.
  - Penyesuaian kebersihan visual: Menghapus aksen garis vertikal tebal (`w-2 bg-brand-600`) pada seluruh kartu KPI dashboard.
- **Strict FreeRADIUS Daemon Reachability Probe**:
  - Menghapus fallback status database lokal (`$pdo !== null`) pada pengecekan FreeRADIUS di `dashboard/utama.php`, sehingga status FreeRADIUS murni hanya bernilai `ONLINE` jika service/daemon FreeRADIUS fisik benar-benar berjalan dan listening di port `1812`/`1813`.


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
