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

