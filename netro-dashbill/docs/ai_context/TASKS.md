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
