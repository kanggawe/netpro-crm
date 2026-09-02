# 🏛️ ARCHITECTURAL & TECHNICAL DECISIONS (ADR) — NETPRO CRM

---

## [ADR-001] Integrasi Skema Native FreeRADIUS 3.0 & Database PostgreSQL 16
- **Tanggal:** 2026-08-20
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Sistem ISP membutuhkan otentikasi dial-up PPPoE & Hotspot skala ribuan pengguna dengan performa tinggi dan kompatibilitas standar AAA.
- **Pilihan yang Dipertimbangkan:**
  1. *MikroTik API Direct Polling*: Membuat script PHP yang memanggil MikroTik API RouterOS secara polling.
  2. *FreeRADIUS Native Database Schema*: Menggunakan skema standar FreeRADIUS (`radcheck`, `radreply`, `radacct`, `nas`) yang terhubung langsung ke PostgreSQL.
- **Keputusan Terpilih:** Opsi 2 (FreeRADIUS Native Database Schema).
- **Alasan & Dampak:**
  - Menghilangkan beban CPU router MikroTik karena otentikasi ditangani oleh server RADIUS terpusat.
  - Kompatibel dengan multi-vendor router NAS (MikroTik, Cisco, Huawei, ZTE).

---

## [ADR-002] Dynamic CoA (Change of Authorization) via UDP Port 3799 (RFC 3576)
- **Tanggal:** 2026-08-22
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Ketika pelanggan menunggak pembayaran (*overdue*) atau melakukan upgrade paket, sistem harus dapat memutus sesi atau mengubah kecepatan secara instan tanpa reboot router atau meminta input kredensial ulang.
- **Pilihan yang Dipertimbangkan:**
  1. *RouterOS API `/interface/pppoe-server/remove`*: Butuh akses port API MikroTik (8728) dan kredensial admin router.
  2. *RFC 3576 RADIUS Disconnect-Request (CoA UDP 3799)*: Mengirim paket disconnect standar ke port incoming RADIUS MikroTik.
- **Keputusan Terpilih:** Opsi 2 (RFC 3576 CoA via UDP 3799).
- **Alasan & Dampak:**
  - Standar industri telekomunikasi yang sangat cepat, aman, dan tidak mengekspos akun admin MikroTik ke layer aplikasi billing.

---

## [ADR-003] Dual Billing Engine (Prabayar Rolling & Pascabayar Fixed Date) dengan Skema Prorata
- **Tanggal:** 2026-08-23
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Model bisnis ISP di Indonesia melayani dua segmen: pelanggan residensial (cenderung Prabayar / Rolling 30 Hari) dan korporat/SME (Pascabayar tanggal 1-20).
- **Pilihan yang Dipertimbangkan:**
  1. *Single Billing Engine*: Memaksa seluruh pelanggan ke skema pascabayar tanggal 1.
  2. *Dual Billing Engine*: Mendukung mode Prabayar (30 hari dari tanggal aktivasi) dan Pascabayar (Jatuh tempo tanggal 20), dengan opsi kalkulasi Prorata proporsional hari.
- **Keputusan Terpilih:** Opsi 2 (Dual Billing Engine).
- **Alasan & Dampak:**
  - Fleksibilitas operasional bisnis ISP maksimal, mengakomodasi pelanggan rumahan maupun korporat.

---

## [ADR-004] Standardisasi 34 Akun COA Berbasis PSAK & Otomasi Jurnal Pelunasan
- **Tanggal:** 2026-08-24
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Pembukuan ISP harus memenuhi kepatuhan audit PSAK 72 (Pendapatan Kontrak), PSAK 73 (Sewa Fiber Optik), PSAK 16 (Aset Tetap OLT/ODP), serta pelaporan PPh 23 dan PNBP Kominfo.
- **Keputusan Terpilih:**
  - Menyusun 34 Akun Standar COA ISP dan memicu pembuatan Jurnal Umum secara otomatis setiap invoice berstatus `Lunas`.
- **Dampak:**
  - Laporan Neraca, Laba Rugi, dan Arus Kas selalu akurat secara real-time tanpa input manual bagian akunting.

---

## [ADR-005] Automated Background Billing Scheduler & Leap-Year Safe Clamping Engine
- **Tanggal:** 2026-08-25
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Penagihan massal bulanan, auto-isolir jatuh tempo, dan pengingat WhatsApp harus berjalan otomatis via CLI Daemon/Cronjob tanpa human intervention. Selain itu, kalkulasi tanggal pada Februari (28/29 hari) dan tanggal 29-31 tidak boleh mengalami lonjakan kalender (*calendar overflow*).
- **Keputusan Terpilih:**
  - Membuat daemon `cron/billing_scheduler.php` dengan argumen CLI (`--all`, `--generate`, `--isolir`, `--reminder`) dan proteksi *Month-Safe Clamping* (`Invoice::addMonthSafe`, `Invoice::getDaysInMonth`).
- **Dampak:**
  - Tagihan terbit tepat tanggal 1 jam 00:05 WIB, auto-isolir berjalan sesuai *grace period*, antrean WhatsApp terdistribusi rapi, dan sistem 100% kebal anomali tahun kabisat (*Leap-Year Proof*).

---

## [ADR-006] Pemisahan Database Multi-Server (App DB & Dedicated Official FreeRADIUS PostgreSQL DB)
- **Tanggal:** 2026-08-25
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Beban transaksi frekuensi tinggi otentikasi dial-up PPPoE dan accounting data radius (`radacct`, `radcheck`, `radreply`) harus diisolasi dari database utama aplikasi (CRM, Billing, PSAK, HR) agar tidak terjadi perebutan resource I/O CPU/Disk saat ribuan router NAS melakukan handshake serentak.
- **Keputusan Terpilih:**
  - Memisahkan arsitektur menjadi 2 database terpisah dengan Dual PDO Engine di `config/database.php` (`$pdo` / `get_db()` vs `$pdoRadius` / `get_radius_db()`) dan mengadopsi skema resmi PostgreSQL FreeRADIUS (`database/radius_database_schema.sql` vs `database/app_database_schema.sql`).
- **Dampak:**
  - Sistem dapat di-deploy pada server terpisah (*multi-node topology*) dengan throughput otentikasi tinggi dan keamanan terisolasi.

---

## [ADR-007] Real-Time Socket Probing Fisik Hardware & Strict FreeRADIUS Detection
- **Tanggal:** 2026-08-26
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Status hardware (Router MikroTik NAS, OLT GPON, dan Server FreeRADIUS) sebelumnya bersifat statis atau menggunakan fallback status database lokal, sehingga menampilkan status `ONLINE` palsu padahal perangkat fisik sedang mati atau belum terhubung kabel jaringan.
- **Keputusan Terpilih:**
  - Menghapus semua fallback database pada indikator hardware. Mengimplementasikan fungsi socket probing non-blocking `is_hardware_node_online($host, $port, $timeout = 0.15 - 0.25)` pada port MikroTik API (8728), OLT Telnet (23/80), dan FreeRADIUS daemon (1812/1813).
- **Dampak:**
  - Indikator status di dashboard eksekutif dan NOC 100% mencerminkan kondisi fisik riil perangkat di lapangan tanpa *false positive*.

---

## [ADR-008] Solid Flat Overlay System untuk Breadcrumb & Multi-Step Chevron Ribbon
- **Tanggal:** 2026-08-26
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Pada layar ponsel resolusi tinggi (*High-DPI / Retina*), poligon chevron yang saling bertaut menggunakan celah `clip-path` ganda menghasilkan garis putih / *sub-pixel anti-aliasing background bleed*.
- **Keputusan Terpilih:**
  - Menggunakan teknik **Solid Flat Left Edge Overlay**: Setiap segmen memiliki ujung panah runcing di sisi kanan dan sisi kiri rata (`clip-path: polygon(0% 0%, calc(100% - 10px) 0%, 100% 50%, calc(100% - 10px) 100%, 0% 100%)`). Segmen sebelumnya bertumpuk di atas segmen berikutnya dengan `-10px margin-left` dan layer `z-index` menurun (10, 9, 8...).
- **Dampak:**
  - Sambungan antar panah ribbon 100% rapat, solid, tanpa celah putih di segala tingkat zoom dan resolusi perangkat, serta mendukung horizontal touch-momentum swipe pada smartphone.

---

## [ADR-009] Native RFC 6238 TOTP Engine & Multi-Factor Authentication Architecture
- **Tanggal:** 2026-08-27
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Kebutuhan otentikasi dua langkah (2FA) berstandar industri dengan kompatibilitas aplikasi seluler terkemuka (Google Authenticator, Microsoft Authenticator, Authy, 1Password) tanpa ketergantungan library pihak ketiga eksternal yang berat.
- **Pilihan yang Dipertimbangkan:**
  1. *Composer Heavy Dependencies (`pragmarx/google2fa`)*: Membutuhkan package manager dan puluhan sub-dependensi.
  2. *Native PHP RFC 6238 TOTP Engine (`class TOTP`)*: Menggunakan fungsi kriptografi bawaan PHP (`hash_hmac('sha1')`, `pack()`, `random_bytes()`, dan konversi Base32 manual RFC 4648).
- **Keputusan Terpilih:** Opsi 2 (Native PHP RFC 6238 TOTP Engine).
- **Alasan & Dampak:**
  - Performa sangat cepat (zero runtime overhead), 100% kompatibel dengan standar RFC 6238 (30-second window, ±30s clock drift tolerance), mandiri tanpa dependensi composer, dan mendukung aktivasi/deaktivasi per-user serta live interactive QR code scanning.

---

## [ADR-010] OAuth 2.0 / SSO Social Authentication & Self-Registration Engine
- **Tanggal:** 2026-08-27
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Kemudahan onboarding dan login staf ISP menggunakan akun media sosial / corporate identity (Google Workspace, GitHub, Facebook, dan X / Twitter) serta pendaftaran mandiri (*Self-Registration*) yang aman dengan integrasi proteksi 2FA.
- **Keputusan Terpilih:**
  - Membangun handler `oauth_login` dan `register` di `api/handler.php` yang mendukung 4 platform (Google, GitHub, Facebook, X) dengan mekanisme provisioning otomatis akun baru jika email belum terdaftar di database, auto-hashing `PASSWORD_BCRYPT`, audit trail log, dan pemeriksaan 2FA otomatis.
- **Dampak:**
  - Pengguna dapat masuk dan mendaftar dengan 1 klik via 4 platform media sosial utama atau formulir pendaftaran mandiri, tanpa mengurangi standar keamanan enterprise.

---

## [ADR-011] Cryptographic URL Parameter Protection & Anti-IDOR Engine with Native .php Compatibility
- **Tanggal:** 2026-08-27
- **Status:** `Accepted`
- **Konteks & Masalah:**
  - Kebutuhan proteksi parameter URL sensitif (seperti ID invoice, ID pelanggan) dari serangan manipulasi dan enumerasi IDOR (*Insecure Direct Object Reference* / OWASP Top 10), sekaligus menjamin kompatibilitas pemanggilan native `.php` di berbagai web server tanpa memerlukan router eksternal.
- **Keputusan Terpilih:**
  - **Native .php Navigation**: Seluruh tautan aplikasi memanggil langsung file fisik `.php` (misal: `/hr/karyawan.php`, `/billing/daftar.php`), menjamin 100% kompatibilitas instan di semua jenis web server (`php -S`, XAMPP, Apache, Nginx) tanpa risiko 404.
  - **Cryptographic Parameter Suite (`class UrlCrypto`)**:
    1. *AES-256-CBC URL Encryption (`url_encrypt`, `url_decrypt`)*: Enkripsi payload token dengan integritas HMAC-SHA256 URL-safe Base64.
    2. *Fast Numeric ID Masking (`mask_id`, `unmask_id`)*: Obfuscation ID numerik bolak-balik dengan Feistel bit-mixing.
    3. *HMAC Signed URLs (`signed_url`, `verify_signed_url`)*: Validasi integritas tautan dengan pembatasan waktu kedaluwarsa (*expiration timestamp*).
- **Dampak:**
  - Sistem memiliki proteksi IDOR level enterprise pada modul penagihan & pelanggan, serta berjalan lancar dan instan di seluruh lingkungan server tanpa ketergantungan konfigurasi routing tambahan.





