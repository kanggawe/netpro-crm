# 🚀 NETPRO CRM — Backend API Engine (Laravel 13)

Proyek backend resmi **NETPRO CRM** berbasis **Laravel 13.x** dan **PHP 8.5+** yang mengintegrasikan seluruh logika bisnis operasional ISP (*Internet Service Provider*):
- **Dual Billing Engine**: Prabayar, Pascabayar, Prorata, PPN 11% (*Include / Exclude*).
- **FreeRADIUS & MikroTik CoA**: Protokol RFC 3576 UDP 3799 (*Packet of Disconnect*), Rate Limiting, Telemetri.
- **Akuntansi PSAK & Kepatuhan Pajak**: Double-entry auto journaling, e-Bupot PPh 23, Iuran Kominfo USO (1.25%) & BHP (0.50%).
- **HR & Payroll**: Presensi GPS Haversine, Insentif Poin BAST Digital, Slip Gaji THP.
- **NOC & Helpdesk**: Insiden Fiber Cut, Trouble Ticket, Work Order FTTH, OPM Test.

---

## 🛠️ Persyaratan Lingkungan
- **PHP**: 8.2+ (Terverifikasi di PHP 8.5.6)
- **Composer**: 2.x
- **Database**: SQLite (default) / PostgreSQL 16 / MySQL 8+
- **Laravel Framework**: v13.29.0

---

## ⚡ Quick Start & Instalasi

1. **Jalankan Migrasi & Database Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Jalankan Local Development Server**:
   ```bash
   php artisan serve --port=8000
   ```

3. **Akun Pengujian Awal (RBAC)**:
   - **Superadmin**:
     - Username: `superadmin`
     - Password: `password123`
   - **Finance Officer**:
     - Username: `admin_finance`
     - Password: `password123`

---

## 📡 Daftar Endpoint RESTful API (v1)

Base URL: `http://localhost:8000/api/v1`

### 🔑 Autentikasi (Sanctum)
| Method | Endpoint | Keterangan |
|---|---|---|
| `POST` | `/auth/login` | Login user & dapatkan Bearer Token |
| `GET` | `/auth/me` | Profil user login aktif (Header `Authorization: Bearer <token>`) |
| `POST` | `/auth/logout` | Revoke token aktif |

### 👥 Pelanggan & Onboarding FTTH
| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/customers` | List pelanggan (filter status, billing_type, search) |
| `POST` | `/customers` | Registrasi pelanggan baru & auto sync RADIUS |
| `GET` | `/customers/{id}` | Detail pelanggan, paket, invoice, tiket |
| `PUT` | `/customers/{id}` | Update data pelanggan |
| `POST` | `/customers/{id}/set-online` | Aktivasi online modem, grace period, invoice perdana |
| `POST` | `/customers/{id}/isolate` | Isolir manual & CoA kick sesi aktif |
| `POST` | `/customers/{id}/quick-topup` | Top-up paket 30 hari prabayar instan |

### 💵 Billing & Tagihan
| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/invoices` | List invoice tagihan |
| `GET` | `/invoices/{id}` | Detail invoice & riwayat pembayaran |
| `POST` | `/invoices/generate-customer` | Terbitkan invoice satuan untuk pelanggan tertentu |
| `POST` | `/invoices/generate-monthly` | Batch generate invoice tanggal 1 massal |
| `POST` | `/invoices/{id}/pay` | Catat pembayaran, auto-journal PSAK, buka isolir |
| `POST` | `/invoices/{id}/send-reminder`| Kirim pengingat tagihan via WhatsApp Bot |
| `GET` | `/tax/simulation` | Simulasi hitung DPP & PPN 11% include/exclude |

### 🌐 FreeRADIUS & MikroTik CoA
| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/radius/telemetry` | Status telemetri RADIUS, mikrotik probe, traffic |
| `GET` | `/radius/users` | List akun PPPoE di tabel RADIUS |
| `GET` | `/radius/nas` | List perangkat router BRAS/NAS |
| `POST` | `/radius/disconnect` | Kirim RFC 3576 Disconnect-Request (CoA UDP 3799) |
| `POST` | `/radius/probe` | Socket probe latency test hardware |

### 📊 Akuntansi Keuangan & Pajak
| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/finance/coa` | Chart of Accounts (COA 34 Akun PSAK) & saldo |
| `GET` | `/finance/journals` | Buku Jurnal Umum transaksi otomatis & manual |
| `GET` | `/finance/taxes` | Daftar bukti potong e-Bupot PPh 23 |
| `POST` | `/finance/taxes` | Buat bukti potong PPh 23 sewa tiang/core |
| `GET` | `/finance/opex` | Daftar voucher pengeluaran operasional OPEX |
| `POST` | `/finance/opex` | Catat pengeluaran OPEX baru |
| `GET` | `/finance/cashflow` | Laporan arus kas masuk dan keluar |
| `GET` | `/finance/regulatory-summary` | Kalkulasi otomatis Iuran Kominfo USO 1.25% & BHP 0.50% |

### 👷 HR, Presensi GPS & Payroll THP
| Method | Endpoint | Keterangan |
|---|---|---|
| `GET` | `/hr/employees` | Master data karyawan |
| `POST` | `/hr/employees` | Tambah karyawan baru |
| `GET` | `/hr/attendances` | Riwayat presensi GPS karyawan |
| `POST` | `/hr/clock-in` | Presensi clock-in dengan validasi radius Haversine GPS |
| `GET` | `/hr/leaves` | Riwayat pengajuan cuti |
| `POST` | `/hr/leaves` | Ajukan permohonan cuti |
| `GET` | `/payroll/records` | Daftar slip gaji THP |
| `POST` | `/payroll/generate` | Kalkulasi massal THP + bonus BAST - potongan BPJS |
| `GET` | `/payroll/bonus-claims` | Klaim poin insentif instalasi BAST |
| `POST` | `/payroll/bonus-claims/{id}/approve` | Verifikasi klaim insentif teknisi |

---

## 🤖 Artisan CLI Schedulers

Backend menyediakan perintah CLI otomatis yang dapat didaftarkan pada Task Scheduler / Crontab:

```bash
# 1. Rutin Billing & Isolir Otomatis
php artisan isp:billing-scheduler --all

# 2. Sinkronisasi Kredensial PPPoE ke FreeRADIUS
php artisan isp:radius-sync

# 3. Snapshot Backup Database
php artisan isp:db-backup
```

---

## 🧪 Menjalankan Automated Test
```bash
php artisan test
```
