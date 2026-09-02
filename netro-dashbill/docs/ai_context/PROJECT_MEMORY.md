# 🧠 PROJECT MEMORY — NETPRO CRM

**Terakhir Diperbarui:** 2026-08-26  
**Sistem:** NETPRO CRM (Enterprise ISP Management OS v3.2.0-STABLE)  
**Status Saat Ini:** Active Production / Modular Expansion  

---

## 📌 Ringkasan Proyek
**NETPRO CRM** adalah Sistem Operasi Manajemen ISP (*Internet Service Provider*) terpadu di Indonesia. Sistem ini mengintegrasikan:
1. **Otentikasi & Bandwidth Management**: AAA FreeRADIUS 3.0 & MikroTik RouterOS v7 via Dynamic CoA (UDP 3799).
2. **CRM & Onboarding FTTH**: Registrasi NIK, Work Order (SPK), Uji Redaman OPM Fiber Optik, dan BAST Digital.
3. **Dual Billing Engine**: Prabayar (Rolling 30 Hari / Fixed Date) & Pascabayar (Jatuh Tempo tgl 20), Prorata harian, Pajak PPN 11% (Include/Exclude), Midtrans QRIS/VA, dan Auto-Isolir.
4. **Akuntansi Keuangan PSAK**: 34 Akun COA (PSAK 72/73/16), Jurnal Otomatis, e-Bupot PPh 23 Unifikasi (NTPN), dan Laporan PNBP Kominfo (USO 1.25% & BHP 0.50%).
5. **HRD & Payroll**: Presensi GPS Lapangan, Insentif Poin BAST per titik pasang, dan Penggajian THP.
6. **NOC & GIS**: Peta Sebaran ODP/OLT/POP berbasis Leaflet GIS, SLA Tickets, dan Speedtest.

---

## 🛠️ Stack & Lingkungan
- **Backend / Core Engine:** PHP 8.2+ Native (Modular Architecture, PDO Prepared Statements)
- **Database:** PostgreSQL 16 (Didukung fallback SQLite untuk testing lokal), 33 Relasi Tabel
- **RADIUS Engine:** FreeRADIUS 3.0 (Tabel `nas`, `radcheck`, `radreply`, `radacct`, `radgroupcheck`, `radgroupreply`)
- **Network Appliance:** MikroTik RouterOS v7 (PPPoE Server, Hotspot, RADIUS Incoming / CoA RFC 3576)
- **Frontend / UI:** Modern Responsive Dashboard, Bootstrap 5 / Tailwind, Lucide Icons, Leaflet.js GIS, Chart.js
- **Payment & Gateway:** Midtrans (Snap/Core API), WhatsApp Gateway (Fonnte / Wablas API)

---

## 🏗️ Struktur Modul Utama
```
d:\PG\BILL-DASH/
├── api/             # REST API Handler & Webhook (Payment, CoA, WhatsApp)
├── billing/         # Tagihan, Invoice Massal, Top-Up, Isolir, Prorata
├── crm/             # Data Pelanggan, Registrasi, SPK/Work Order, BAST Digital
├── dashboard/       # Executive Telemetry & Realtime Analytics
├── database/        # Skema SQL PostgreSQL & SQLite, Migrasi
├── docs/            # Dokumentasi Sistem & AI Context Directory
│   └── ai_context/  # Sistem Memori, Tasks, ADR, Rules untuk AI IDE
├── finance/         # COA 34 Akun PSAK, Jurnal Umum, e-Bupot PPh 23, Kominfo PNBP
├── hr/              # Data Pegawai, Presensi GPS, Insentif Teknisi
├── inventory/       # Manajemen ONT/Router, Kabel Dropcore, ODP/OLT
├── noc/             # GIS Coverage Map, Monitoring Core Router, Speedtest
├── payroll/         # Penggajian THP, Potongan BPJS & PPh 21
├── radius/          # Router NAS, Users PPPoE/Hotspot, Profiles, CoA, Sessions
└── tickets/         # SLA & Gangguan Jaringan Pelanggan
```

---

## 🔄 Progres Terkini & State Berjalan
- [x] **Core RADIUS & CoA**: Integrasi MikroTik NAS, Profile Bandwidth, Disconnect Request UDP 3799.
- [x] **Dual Billing Engine**: Logika tagihan Prabayar, Pascabayar, Prorata harian, dan PPN 11%.
- [x] **CRM & Onboarding FTTH**: Pendaftaran NIK, WO Teknisi, Uji OPM dBm, BAST Digital PDF.
- [x] **Finance PSAK & e-Bupot**: Jurnal otomatis pelunasan, 34 Akun COA, NTPN PPh 23, kalkulator PNBP Kominfo.
- [x] **HR & Payroll**: Presensi GPS dan perhitungan insentif BAST teknisi.
- [x] **End-to-End Page Relations**: Integrasi mulus dari pendaftaran -> WO -> BAST -> Auto RADIUS -> Tagihan -> Pembayaran Kasir -> Auto Jurnal PSAK.
- [x] **Navigation & Breadcrumbs Consistency**: Dynamic Breadcrumbs di Navbar & auto-expand accordion sidebar pada seluruh 77 rute berkas PHP (77/77 Valid).
- [x] **URL Parameter & Foreign Key Integrity**: Standardisasi handoff `customer_id`, `invoice_id`, `username` di modul Billing, CRM, Tickets, dan RADIUS.
- [x] **RBAC Page-Level Guard & Matrix Alignment**: Proteksi 403 Forbidden Shield di `includes/header.php` & matriks 10 role di `config/app.php`.
- [x] **Automated Background Billing Scheduler**: Daemon CLI `cron/billing_scheduler.php`, proteksi kalender tahun kabisat (*Month-Safe Clamping*), auto-isolir grace period, dan WhatsApp reminder queue.
- [x] **Dual Database Multi-Server Architecture**: Dual PDO Engine (`$pdo` / `$pdoRadius`), skema resmi PostgreSQL FreeRADIUS (`radcheck`, `radreply`, `radacct`, `nas`), dan sinkronisasi lintas database.
- [x] **Automated Integration Tests**: Script pengujian end-to-end (15/15 Pass), navigasi (77/77 Pass), foreign key (7/7 Pass), RBAC matrix (27/27 Pass), scheduler & leap-year (11/11 Pass), dan dual database (9/9 Pass).
- [x] **Invoice & Faktur Pajak Standardization**: Format A4 presisi, barcode berdampingan, stempel watermark mint LUNAS, dan tanda tangan resmi.
- [x] **Split-Screen Authentication System**: Template `login.php` modern 1-viewport tanpa scrollbar, 4 pilar fitur ISP, dan telemetri live node status & jam real-time.
- [x] **Dashboard & Navbar Layout Reorganization**: Judul halaman bersih di navbar, breadcrumbs di viewport area konten, dan KPI cards dengan soft-shadow styling.
- [/] **NOC Telemetry & GIS**: Polling status OLT SNMP & realtime latency graph.

---

## ⚠️ Konteks Kritis & Gotchas Penting
1. **Dynamic CoA Disconnect**: Paket CoA dikirim ke IP NAS pada port UDP 3799 dengan atribut `User-Name` dan secret NAS yang terdaftar di tabel `nas`.
2. **Kalkulasi PPN 11%**:
   - Metode *Include*: $\text{DPP} = \text{Total Tagihan} / 1.11$, $\text{PPN} = \text{Total Tagihan} - \text{DPP}$.
   - Metode *Exclude*: $\text{PPN} = \text{Harga Paket} \times 11\%$, $\text{Total} = \text{Harga Paket} + \text{PPN}$.
3. **Kominfo PNBP**: USO = $1.25\% \times \text{Gross Revenue}$, BHP Telekomunikasi = $0.50\% \times \text{Gross Revenue}$.
4. **Isolir Pelanggan**: Saat akun di-suspend, record di `radcheck` diubah atributnya atau diarahkan ke IP Pool Isolir (`radreply`), lalu CRM mengirim CoA Disconnect agar sesi dial-up terputus seketika.
