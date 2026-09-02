# 📘 DOKUMENTASI TEKNIS PERBAIKAN, PENGEMBANGAN, MIGRASI DATABASE & PENGUATAN KEAMANAN SIBER
**Sistem**: NETPRO CRM / BILL-DASH (ISP Management Operating System)  
**Tanggal Rilis**: 22 Agustus 2026  
**Status**: Production Ready & Enterprise Hardened  
**Target Pembelajaran**: Referensi Standar Rekayasa Perangkat Lunak, DevOps & Cyber Security  

---

## 📑 DAFTAR ISI
1. [Ringkasan Eksekutif (Executive Summary)](#1-ringkasan-eksekutif)
2. [Migrasi Database: SQLite ke PostgreSQL Enterprise](#2-migrasi-database-sqlite-ke-postgresql-enterprise)
3. [Pengembangan Modul Baru](#3-pengembangan-modul-baru)
   - [A. Modul Berita Acara Serah Terima (BAST) 2-in-1](#a-modul-berita-acara-serah-terima-bast-2-in-1)
   - [B. Full Enterprise NOC FTTH Infrastructure Suite](#b-full-enterprise-noc-ftth-infrastructure-suite)
4. [Perbaikan Bug & Pembersihan Kode (*Bug Fixes & Refactoring*)](#4-perbaikan-bug--pembersihan-kode)
5. [Pengembangan Endpoint Webhook Payment Gateway](#5-pengembangan-endpoint-webhook-payment-gateway)
6. [Audit & Penguatan Keamanan Siber (OWASP Top 10 Hardening)](#6-audit--penguatan-keamanan-siber-owasp-top-10-hardening)
7. [Daftar Perubahan Berkas (*File Change Matrix*)](#7-daftar-perubahan-berkas-file-change-matrix)
8. [Panduan Pemeliharaan & SOP Pembelajaran Pengembang](#8-panduan-pemeliharaan--sop-pembelajaran-pengembang)

---

## 1. RINGKASAN EKSEKUTIF

Dokumentasi ini merangkum seluruh kegiatan rekayasa perangkat lunak, perbaikan bug kritis, refaktorisasi arsitektur, migrasi engine basis data, serta penguatan keamanan siber (*cybersecurity hardening*) yang dilakukan pada sistem **NETPRO CRM / BILL-DASH**.

Tujuan utama dari pembaruan ini adalah:
1. Menjadikan aplikasi siap beroperasi pada skala ISP komersial berskala besar (*enterprise-grade*) menggunakan basis data PostgreSQL.
2. Melengkapi rangkaian modul operasional jaringan (NOC FTTH suite) dan berita acara serah terima (BAST).
3. Menutup celah keamanan siber (Broken Access Control, SQL Injection, Webhook Forgery, Open Redirect, Session Hijacking, Exposure File Sensitif) sesuai standar OWASP Top 10.

---

## 2. MIGRASI DATABASE: SQLITE KE POSTGRESQL ENTERPRISE

### A. Arsitektur Dual-Driver Database Manager (`config/database.php`)
Sistem dilengkapi dengan pembaca variabel lingkungan (`.env`) yang secara otomatis mendeteksi koneksi PostgreSQL dan memiliki sistem proteksi *graceful fallback* ke SQLite lokal jika koneksi server database utama terputus.

```php
// Konfigurasi .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=billdash
DB_USER=postgres
DB_PASS=kanggawe123
```

### B. Standardisasi 33 Tabel Skema PostgreSQL (`database/postgresql_schema.sql`)
Seluruh 33 tabel telah dibuat secara lengkap mencakup seluruh entitas bisnis:
1. `packages` - Katalog paket internet & bandwidth.
2. `customers` - Master pelanggan, koordinat GPS, kredensial PPPoE.
3. `invoices` - Tagihan billing, pemisahan DPP, dan PPN 11%.
4. `surveys` - Hasil survey kelayakan optik & ODP terdekat.
5. `work_orders` - Surat perintah kerja & serial number ONT.
6. `addons` - Layanan tambahan (IP Publik, Mesh WiFi).
7. `promos` - Kode promo & voucher diskon.
8. `radius_nas` - Daftar Router NAS MikroTik.
9. `radius_users` - Akun PPPoE / Hotspot aktif.
10. `radius_profiles` - Limitasi bandwidth dan burst queue.
11. `radius_vouchers` - Batch voucher hotspot.
12. `noc_outages` - Catatan insiden fiber cut dan pemadaman.
13. `tickets` - Tiket keluhan teknis pelanggan & SLA.
14. `employees` - Master karyawan & staf operasional.
15. `leaves` - Pengajuan cuti pegawai.
16. `inventory_items` - Gudang material kabel optik & modem ONT.
17. `cash_transactions` - Buku kas masuk & kas keluar.
18. `leads` - Prospek calon pelanggan dari sales.
19. `settings` - Pengaturan global sistem dan logo.
20. `users` - Akun pengguna login dan role RBAC.
21. `branches` - Kantor cabang & coverage area ISP.
22. `coa_accounts` - Chart of Accounts (COA) akuntansi.
23. `journal_entries` - Jurnal umum debit & kredit.
24. `tax_records` - Rekam faktur pajak PPh 23 / PPN.
25. `opex_expenses` - Pengeluaran operasional & voucher belanja.
26. `attendances` - Log absensi GPS pegawai.
27. `kpi_indicators` - Parameter KPI divisi.
28. `performance_reviews` - Evaluasi kinerja 360 derajat.
29. `salary_components` - Master tunjangan & potongan gaji.
30. `payroll_records` - Slip gaji dan Take Home Pay (THP).
31. `bonus_claims` - Klaim insentif aktivasi teknisi.
32. `backups` - Riwayat snapshot database.
33. `audit_logs` - Jejak audit aktivitas keamanan.

### C. Penyesuaian Sintaks SQL PostgreSQL
- **Pembaruan Sequence Serial**: Menyinkronkan nilai `setval()` pada seluruh sequence ID auto-increment PostgreSQL agar tidak terjadi error `duplicate key value violates unique constraint`.
- **Sintaks Upsert `ON CONFLICT`**: Memperbarui metode `Setting::set` dari sintaks SQLite `INSERT OR REPLACE` menjadi standar resmi PostgreSQL `INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value`.

---

## 3. PENGEMBANGAN MODUL BARU

### A. Modul Berita Acara Serah Terima (BAST) 2-in-1 (`crm/berita_acara.php`)
- **Master List Mode**: Menampilkan tabel seluruh rekaman BAST yang diterbitkan, lengkap dengan filter status, pencarian, dan metrik statistik (Total BAST, Menunggu TTD, Siap Terbit Invoice).
- **Document Detail Mode (`?id=XX`)**: Menampilkan dokumen legal resmi BAST lengkap dengan hasil uji *speedtest* (Download, Upload, Ping, Jitter), redaman optik dBm, lampiran foto instalasi lapangan, barcode QR legalitas digital, dan tombol cetak PDF eksekutif.
- **Modal Pembuatan BAST Baru**: Memudahkan penerbitan BAST langsung dari data Work Order / Pelanggan terpasang.

### B. Full Enterprise NOC FTTH Infrastructure Suite
Melengkapi 9 submenu operasional jaringan fiber optik:
1. **`noc/topologi.php`**: Diagram arsitektur jaringan 4-level interaktif (Upstream Transit BGP ➔ Core Router CCR ➔ OLT GPON Chassis ➔ ODC/ODP Distribusi).
2. **`noc/odp.php`**: Peta GIS interaktif (Leaflet.js) untuk sebaran titik ODP, splitter ratio (1:8, 1:16), port availability, dan status redaman dBm.
3. **`noc/olt.php`**: Manajemen chassis OLT (ZTE C320 & Huawei MA5608T), matriks PON port, dan auto-discovery unconfigured ONT.
4. **`noc/otb.php`**: Visualisasi rak Optical Distribution Frame (ODF/OTB) server room beserta panduan standar 12-warna TIA/EIA-598 (Biru, Oranye, Hijau, Cokelat, Abu-abu, Putih, Merah, Hitam, Kuning, Ungu, Pink, Toska).
5. **`noc/odc.php`**: Manajemen lemari distribusi outdoor (FDT/ODC), feeder cable input, dan splitter tahap pertama (1:4 / 1:8).
6. **`noc/onu.php`**: Inventaris armada modem pelanggan (ZTE, Huawei, Fiberhome), telemetri daya optik Rx/Tx secara live, dan aksi remote restart ONT melalui protokol TR-069/OMCI.

---

## 4. PERBAIKAN BUG & PEMBERSIHAN KODE

1. **Pemisahan Data Invoice & Tiket pada Profil Pelanggan 360 (`crm/detail.php`)**:
   - Menghapus fallback kuota global yang menyebabkan invoice pelanggan lain muncul di akun pelanggan yang berbeda.
   - Menerapkan query terfilter ketat berdasarkan `customer_id`.
2. **Pembersihan Teks Hardcoded SQLite**:
   - Menghapus teks *"SQLite"* yang hardcoded pada feedback alert flash messages, tombol simpan, dan footer halaman di seluruh modul (`pengaturan/payment_gateway.php`, `pengaturan/perusahaan.php`, `pengaturan/sistem.php`, `pengaturan/about.php`, `payroll/`, `kinerja/`, `billing/`).
   - Halaman **Tentang Sistem (`pengaturan/about.php`)** kini mendeteksi nama engine database aktif secara dinamis (`PostgreSQL 14+` vs `SQLite 3`).

---

## 5. PENGEMBANGAN ENDPOINT WEBHOOK PAYMENT GATEWAY (`api/payment_callback.php`)

Endpoint webhook universal dibangun untuk memproses notifikasi pembayaran otomatis dari seluruh Payment Gateway populer di Indonesia:

```
                  ┌───────────────────────────────┐
                  │ Midtrans / Xendit / Tripay    │
                  └──────────────┬────────────────┘
                                 │ HTTP POST Webhook
                                 ▼
                 ┌────────────────────────────────┐
                 │    /api/payment_callback.php   │
                 └──────────────┬─────────────────┘
                                │
        ┌───────────────────────┼───────────────────────┐
        ▼                       ▼                       ▼
┌───────────────┐       ┌───────────────┐       ┌───────────────┐
│ Validasi      │       │ Cek Nominal   │       │ Idempotency   │
│ Signature     │       │ Tagihan       │       │ Anti-Replay   │
│ Kriptografis  │       │ Database      │       │ Lock          │
└───────┬───────┘       └───────┬───────┘       └───────┬───────┘
        └───────────────────────┼───────────────────────┘
                                │ Transaksi Sah
                                ▼
        ┌───────────────────────────────────────────────┐
        │ 1. Set Status Invoice: 'paid' + paid_date     │
        │ 2. Auto-Reactivate / Unisolate Customer       │
        │ 3. Catat Mutasi Kas Masuk (cash_transactions) │
        │ 4. Rekam Jejak Keamanan (audit_logs)          │
        │ 5. Return HTTP 200 JSON OK Response           │
        └───────────────────────────────────────────────┘
```

### Mekanisme Kriptografi per Gateway:
- **Midtrans**: `SHA512(order_id + status_code + gross_amount + ServerKey)`
- **Tripay**: `HMAC-SHA256(raw_input, private_key)`
- **Xendit**: Header Token `X-CALLBACK-TOKEN`
- **Duitku**: `MD5(merchantCode + amount + merchantOrderId + apiKey)`

---

## 6. AUDIT & PENGUATAN KEAMANAN SIBER (OWASP TOP 10 HARDENING)

| Kerentanan | Risiko Sebelumnya | Tindakan Penguatan (*Remediation*) |
| :--- | :--- | :--- |
| **A01: Broken Access Control** | API dapat dieksekusi tanpa validasi autentikasi atau otorisasi peran. | • Menambahkan **Global Authentication Gate** pada [api/handler.php](file:///d:/PG/BILL-DASH/api/handler.php). Seluruh request selain login/logout diblokir jika tidak memiliki sesi aktif.<br>• Menerapkan **RBAC Admin Guard** (`$requireAdmin()`) untuk aksi administratif seperti manajemen pengguna, backup, dan pengaturan sistem. |
| **A02: Cryptographic Failures & Timing Attack** | Perbandingan string biasa `===` rentan terhadap *Side-Channel Timing Attacks*. | • Menggunakan fungsi `hash_equals()` pada seluruh verifikasi signature kriptografis.<br>• Password pengguna dienkripsi dengan standar **Bcrypt (`PASSWORD_BCRYPT`)**. |
| **A03: SQL Injection (SQLi)** | Potensi injeksi SQL pada pengolahan parameter request. | • Seluruh query model [config/models.php](file:///d:/PG/BILL-DASH/config/models.php) 100% menggunakan **PDO Parameterized Prepared Statements (`?`)**.<br>• Menerapkan sanitasi tipe data ketat (`intval()`, `floatval()`, `safeLimit`). |
| **A05: Exposure File Sensitif** | File `.env`, `.sql`, dan `.db` dapat diakses langsung oleh publik melalui browser. | • Membuat konfigurasi [.htaccess](file:///d:/PG/BILL-DASH/.htaccess) yang memblokir akses ke berkas sensitif (`.env`, `.git`, `.sql`, `.sqlite`, `.log`).<br>• Menambahkan HTTP Security Headers (`X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `X-XSS-Protection: 1; mode=block`). |
| **A07: Session Fixation & Hijacking** | Cookie sesi dapat dimanipulasi atau dicuri via XSS. | • Konfigurasi sesi pada [config/app.php](file:///d:/PG/BILL-DASH/config/app.php) diperketat dengan flag `session.cookie_httponly = 1`, `session.cookie_samesite = 'Lax'`, dan `session.use_strict_mode = 1`.<br>• Melakukan regenerasi ID sesi otomatis (`session_regenerate_id(true)`) saat proses login dan logout. |
| **Open Redirect Vulnerability** | Parameter `?redirect=` dapat dimanipulasi untuk serangan phishing. | • Menambahkan fungsi sanitasi `$safeRedirect()` yang menolak URL eksternal dengan protokol asing dan membatasi hanya pada rute internal aplikasi. |
| **Anti-Tampering & Audit Logging** | Transaksi pembayaran dimanipulasi nilainya tanpa terdeteksi. | • Pengecekan toleransi 0 rupiah pada nominal pembayaran.<br>• Setiap percobaan manipulasi atau kegagalan login dicatat secara instan ke `audit_logs` dengan status `security_alert`. |

---

## 7. DAFTAR PERUBAHAN BERKAS (*FILE CHANGE MATRIX*)

### Berkas Baru Dibuat (*New Files*):
- [api/payment_callback.php](file:///d:/PG/BILL-DASH/api/payment_callback.php): Endpoint webhook multi-payment gateway dengan verifikasi kriptografi.
- [.htaccess](file:///d:/PG/BILL-DASH/.htaccess): Konfigurasi keamanan web server Apache, proteksi file sensitif, dan HTTP headers.
- [noc/topologi.php](file:///d:/PG/BILL-DASH/noc/topologi.php): Modul visualisasi topologi jaringan ISP 4-tier.
- [noc/odp.php](file:///d:/PG/BILL-DASH/noc/odp.php): Modul GIS mapping titik ODP & kapasitas redaman optik.
- [noc/olt.php](file:///d:/PG/BILL-DASH/noc/olt.php): Modul manajemen OLT chassis & auto-discovery ONT.
- [noc/otb.php](file:///d:/PG/BILL-DASH/noc/otb.php): Modul manajemen Optical Terminal Block & standar 12-warna kabel fiber.
- [noc/odc.php](file:///d:/PG/BILL-DASH/noc/odc.php): Modul manajemen lemari outdoor Optical Distribution Cabinet (ODC).
- [noc/onu.php](file:///d:/PG/BILL-DASH/noc/onu.php): Modul armada modem ONT/ONU pelanggan & telemetri redaman Rx/Tx.
- [crm/berita_acara.php](file:///d:/PG/BILL-DASH/crm/berita_acara.php): Modul master BAST & dokumen sertifikat serah terima pekerjaan.
- [database/postgresql_schema.sql](file:///d:/PG/BILL-DASH/database/postgresql_schema.sql): DDL lengkap 33 tabel PostgreSQL beserta initial seeds.

### Berkas Diperbarui (*Modified Files*):
- [config/database.php](file:///d:/PG/BILL-DASH/config/database.php): Dual PDO database manager (.env support, PostgreSQL primary, SQLite fallback).
- [config/app.php](file:///d:/PG/BILL-DASH/config/app.php): Hardened session security, CSRF protection helpers, session regeneration.
- [config/models.php](file:///d:/PG/BILL-DASH/config/models.php): Prepared statements, Bcrypt hashing, query bounds protection, `ON CONFLICT` support.
- [api/handler.php](file:///d:/PG/BILL-DASH/api/handler.php): Global auth gate, RBAC admin guards, open redirect sanitization, intrusion logging.
- [login.php](file:///d:/PG/BILL-DASH/login.php): Hashing verification, multi-user switch, 2FA OTP integration.
- [pengaturan/payment_gateway.php](file:///d:/PG/BILL-DASH/pengaturan/payment_gateway.php): Pembersihan teks database SQLite.
- [pengaturan/perusahaan.php](file:///d:/PG/BILL-DASH/pengaturan/perusahaan.php): Pembersihan label database SQLite.
- [pengaturan/sistem.php](file:///d:/PG/BILL-DASH/pengaturan/sistem.php): Pembersihan tombol simpan.
- [pengaturan/about.php](file:///d:/PG/BILL-DASH/pengaturan/about.php): Deteksi dinamis engine basis data aktif (PostgreSQL / SQLite).
- [pengaturan/backup.php](file:///d:/PG/BILL-DASH/pengaturan/backup.php): Pembersihan label dan perbaikan deskripsi.
- [crm/detail.php](file:///d:/PG/BILL-DASH/crm/detail.php): Strict filtering invoice & tiket pelanggan.
- [crm/instalasi.php](file:///d:/PG/BILL-DASH/crm/instalasi.php): Direct link ke dokumen BAST.
- [includes/sidebar.php](file:///d:/PG/BILL-DASH/includes/sidebar.php): Penambahan navigasi untuk seluruh 9 submenu NOC.

### Berkas Sementara yang Telah Dihapus (*Cleaned Up*):
- `scratch/apply_full_pg.php` *(Skrip migrasi database sekali pakai)*
- `scratch/test_all_models.php` *(Skrip verifikasi 31 model sekali pakai)*
- `scratch/test_webhook.php` *(Skrip simulasi webhook sekali pakai)*

---

## 8. PANDUAN PEMELIHARAAN & SOP PEMBELAJARAN PENGEMBANG

### Standar Penulisan Query Database:
1. **DILARANG KERAS** menggunakan penggabungan string langsung (`"SELECT * WHERE id = " . $id`).
2. **WAJIB** menggunakan prepared statements:
   ```php
   $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
   $stmt->execute([intval($id)]);
   return $stmt->fetch();
   ```

### Standar Penambahan Action API Baru (`api/handler.php`):
1. Jika aksi bersifat administratif (mengubah konfigurasi, menghapus data inti), **WAJIB** menambahkan pemanggilan `$requireAdmin('nama_aksi')`.
2. Selalu gunakan `$safeRedirect($redirect, 'halaman_default.php')` saat mengarahkan kembali pengguna.
3. Rekam jejak aktivitas penting ke dalam audit log menggunakan `AuditLog::log(...)`.

### Standar Output HTML & Template Rendering:
1. Selalu gunakan `htmlspecialchars($data, ENT_QUOTES, 'UTF-8')` ketika mencetak variabel yang berasal dari input pengguna atau database untuk mencegah serangan XSS (*Cross-Site Scripting*).
