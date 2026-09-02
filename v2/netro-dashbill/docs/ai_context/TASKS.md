# 📋 TASK LIST & SPRINT TRACKER — NETPRO CRM

## 🔥 Prioritas Utama (Sprint Ini)
- [ ] **TASK-01**: **Uji Validasi Simulasi CoA Disconnect & Re-Auth Isolir**
  - [x] Buat script runner simulasi di `scratch/test_inactive_to_online.php`
  - [ ] Verifikasi handling jika IP Router NAS offline / timeout socket UDP 3799
  - [ ] Tambahkan log audit kegagalan CoA ke tabel `activity_logs`
- [x] **TASK-02**: **Optimasi Penagihan Massal & Reminder WhatsApp Bot**
  - [x] Scheduler cronjob untuk generasi invoice tanggal 1 (Pascabayar) & Daemon CLI `cron/billing_scheduler.php`
  - [x] Proteksi kalender tahun kabisat (Leap-Year Safe) & Month-Safe Clamping
  - [x] Integrasi antrean auto-reminder H-3, H-1, dan H+1 (Isolir) via WhatsApp Gateway API
- [x] **TASK-03**: **Sinkronisasi Otomatis Buku Besar PSAK dari Payment & Kasir**
  - [x] Trigger jurnal debit Kas/Bank (`1101`/`1102`) dan kredit Pendapatan (`4101`) & PPN Keluaran (`2103`) secara seimbang otomatis pada setiap transaksi pelunasan.

## ⏳ Antrean Tugas (Backlog)
- [ ] **BACKLOG-01**: Integrasi SNMP Polling untuk status redaman optik port OLT GPON secara live di dashboard NOC.
- [ ] **BACKLOG-02**: Penambahan fitur export SPT Masa PPN 1111 (CSV e-Faktur DJP).
- [ ] **BACKLOG-03**: Modul self-service portal pelanggan (cek kuota FUP, bayar invoice, dan speedtest langsung).

## ✅ Selesai (Completed)
- [x] **TASK-001**: Inisialisasi skema database 33 tabel PostgreSQL & SQLite (`database/postgresql_schema.sql`).
- [x] **TASK-002**: Modul pendaftaran FTTH & Hotspot dengan validasi NIK 16 digit dan Leaflet Map (`crm/registrasi.php`).
- [x] **TASK-003**: Generator BAST Digital & Work Order Teknisi dengan pencatatan redaman OPM (`crm/spk.php`).
- [x] **TASK-004**: Dual Billing Engine (Prabayar Rolling 30 Hari & Pascabayar Fixed Date tgl 20 + Prorata).
- [x] **TASK-005**: 34 Akun Chart of Accounts PSAK, e-Bupot PPh 23 (NTPN), dan Laporan PNBP Kominfo (USO & BHP).
- [x] **TASK-006**: Pembuatan struktur dokumentasi memori AI di `docs/ai_context/`.
- [x] **TASK-007**: Perbaikan Alur Kerja Terintegrasi Lintas Halaman (*End-to-End Business Flow* CRM -> WO -> BAST -> RADIUS -> Billing -> Auto-Jurnal Finance).
- [x] **TASK-008**: Standardisasi Konsistensi Navigasi, Action Buttons, dan Dynamic Breadcrumbs di seluruh 77 rute modul.
- [x] **TASK-009**: Standardisasi Relasi Parameter URL & Integritas Foreign Key (FK) di seluruh halaman modul.
- [x] **TASK-010**: Penyelarasan Hak Akses (RBAC) per Halaman dengan 403 Forbidden Shield dan Matrix Validation.
- [x] **TASK-011**: Pembuatan Daemon CLI Automated Billing Scheduler (`cron/billing_scheduler.php`) & Leap-Year Safe Clamping.
- [x] **TASK-012**: Pemisahan Arsitektur Dual Database (App DB vs Dedicated Official FreeRADIUS PostgreSQL DB).
- [x] **TASK-013**: Harmonisasi Tema & Desain Dashboard Global (*Ignite Crimson Red & Modern Soft-Shadow UI*).
- [x] **TASK-014**: Komponen Arrow / Chevron Breadcrumb & Multi-Step Wizard Ribbon (`assets/css/style.css`, `includes/header.php`, `crm/registrasi.php`).
- [x] **TASK-015**: Probing Hardware Fisik Nyata Real-Time untuk Router NAS MikroTik, OLT ZTE/Huawei, & FreeRADIUS (`config/app.php`, `dashboard/utama.php`).
- [x] **TASK-016**: Penyelarasan Tema Dark Obsidian Navbar & Optimasi Responsif Mobile Layar Smartphone (`includes/navbar.php`, `crm/detail.php`).
- [x] **TASK-017**: Rilis Mayor v4.0.0-ENTERPRISE & Global Module Entry Index Routers (`dashboard/index.php`, `crm/index.php`, `billing/index.php`, `finance/index.php`, `noc/index.php`, `radius/index.php`).
- [x] **TASK-018**: Full CRUD (Edit & Delete) Manajemen Katalog CRM (`crm/paket.php`, `crm/addon.php`, `crm/promo.php`, `config/models.php`, `api/handler.php`).
- [x] **TASK-019**: Transformasi Tema Merah Eksekutif (Ruby Crimson Red Theme) pada Sidebar & Navbar (`includes/navbar.php`, `includes/sidebar.php`, `assets/css/style.css`).
- [x] **TASK-020**: Integrasi Two-Factor Authentication (2FA TOTP) & Challenge UI pada Halaman Login Utama (`login.php`, `login_pegawai.php`, `profile.php`, `api/handler.php`).
- [x] **TASK-021**: Pembangunan Native RFC 6238 TOTP Engine, QR Code Generator, AJAX Interactive Verifier, dan Per-User Enable/Disable Toggle (`config/app.php`, `profile.php`, `api/handler.php`, `docs/ai_context/DECISIONS.md`).
- [x] **TASK-022**: Integrasi Single Sign-On (SSO) OAuth 2.0 4 Platform (Google, GitHub, Facebook, X / Twitter) serta Self-Registration Akun Baru (`login.php`, `api/handler.php`, `docs/ai_context/DECISIONS.md`).
- [x] **TASK-023**: Integrasi Fitur Lupa Password & Self-Service Password Reset (`login.php`, `api/handler.php`, `docs/ai_context/DECISIONS.md`).
- [x] **TASK-024**: Transformasi Visual Grafik Pelanggan ke Modern Smooth Spline Gradient Area Chart & Dynamic Month Labels (`dashboard/customers.php`).
- [x] **TASK-025**: Modernisasi Seluruh Visual Grafik Analitik di 7 Dashboard (Revenue, Utama, NOC, Tickets, Overdue, HR, Customers) dengan Tema Ruby Crimson Gradient, Dark Tooltip, dan Zero-Safe Logic (`dashboard/*.php`).
- [x] **TASK-026**: Pembangunan Interaktif Notification Center Dropdown & Telemetry Alerts di Top Navbar Header (`includes/navbar.php`, `assets/js/app.js`).
- [x] **TASK-027**: Integrasi Vektor Logo Resmi NETPRO CRM (`assets/images/netpro-logo.svg`), Avatar Eksekutif (`assets/images/avatar-admin.svg`), dan Perbaikan Un-clipping Dropdown Notifikasi Navbar (`includes/sidebar.php`, `includes/navbar.php`, `login.php`, `pengaturan/profile.php`).
- [x] **TASK-028**: Peningkatan Manajemen Profil Akun Lengkap: 4 Karakter Avatar Preset (`assets/images/avatar-*.svg`), Dynamic Avatar Live Preview, dan Ekstensi Kolom Personal/Korporat (`pengaturan/profile.php`, `config/models.php`, `api/handler.php`).
- [x] **TASK-029**: Implementasi Cryptographic URL Parameter / Anti-IDOR Suite (`config/app.php`, `crm/detail.php`, `billing/invoice.php`, `docs/ai_context/DECISIONS.md`) & Penyelarasan Native `.php` Routing Universal untuk Standard Server.
- [x] **TASK-030**: Pembuatan Backup Lengkap Sistem & Database Pre-MVC Migration (`backups/netpro_crm_pre_mvc_backup_2026_08_27.zip`, `backups/app_pre_mvc.sqlite`).
