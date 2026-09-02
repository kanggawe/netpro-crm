# 📘 Dokumentasi Resmi & Hasil Pengujian Endpoint API: NETPRO CRM Backend (Laravel 13)

Dokumen ini berisi arsitektur lengkap, petunjuk teknis, dokumentasi seluruh endpoint RESTful API (v1), serta hasil pengujian otomatis dan contoh payload/response riil dari backend **NETPRO CRM** berbasis **Laravel 13.29.0** dan **PHP 8.5.6** yang berlokasi di [`d:\PG\NETPRO-CRM\backend`](file:///d:/PG/NETPRO-CRM/backend).

---

## 🏗️ 1. Ringkasan Arsitektur & Struktur Proyek

Proyek backend dibangun dengan pola arsitektur bersih (*Clean Service-Repository Pattern*) yang memisahkan antara lapisan routing, validasi controller, domain business service, dan Eloquent ORM.

```
d:\PG\NETPRO-CRM\backend/
├── app/
│   ├── Console/Commands/
│   │   ├── BillingSchedulerCommand.php       # CLI Daemon: isp:billing-scheduler
│   │   ├── RadiusSyncCommand.php             # CLI Sync: isp:radius-sync
│   │   └── SystemBackupCommand.php           # CLI Backup: isp:db-backup
│   ├── Http/Controllers/Api/V1/
│   │   ├── AuthController.php                # Otentikasi Sanctum & Profil
│   │   ├── CustomerController.php            # Master Pelanggan & Aktivasi
│   │   ├── PackageController.php             # Katalog Paket, Addon, Promo
│   │   ├── BillingController.php             # Invoices, Pelunasan, Reminder
│   │   ├── RadiusController.php              # Telemetri, PPPoE Users, CoA Kick
│   │   ├── NocController.php                 # Outages FO & Penanganan Insiden
│   │   ├── WorkOrderController.php           # Survey, SPK Instalasi, BAST
│   │   ├── TicketController.php              # Trouble Ticket Helpdesk & SLA
│   │   ├── FinanceController.php             # COA PSAK, Jurnal, e-Bupot PPh 23, Kominfo
│   │   ├── HrController.php                  # Presensi GPS Haversine, Cuti, KPI
│   │   ├── PayrollController.php             # Gaji THP, Insentif Poin BAST
│   │   ├── InventoryController.php           # Material ONT/Kabel, Leads, Cabang
│   │   └── SettingController.php             # Konfigurasi Global & Audit Trail
│   ├── Models/                               # 33 Eloquent Models Lengkap
│   └── Services/                             # 5 Core Domain Services
│       ├── BillingService.php                # Engine DPP/PPN 11%, Prorata, Isolir
│       ├── RadiusCoaService.php              # Protokol CoA RFC 3576 UDP 3799 & Socket Test
│       ├── AccountingService.php             # Auto-journal PSAK & Pajak Unifikasi
│       ├── PayrollService.php                # Presensi GPS Haversine & Payroll THP
│       └── NotificationService.php           # Integrasi Notifikasi WhatsApp Bot
├── config/
│   ├── isp.php                               # Konfigurasi Regulasi PPN 11%, USO 1.25%, BHP 0.5%
│   └── radius.php                            # Konfigurasi FreeRADIUS & MikroTik BRAS
├── database/
│   ├── migrations/                           # 15 Migrasi Database Skema Lengkap
│   └── seeders/DatabaseSeeder.php            # Master Seeder Data Awal
├── routes/api.php                            # RESTful API Endpoints v1
└── tests/                                    # Automated Unit & Feature Test Suite
```

---

## 🧪 2. Ringkasan Hasil Pengujian Otomatis (PHPUnit)

Seluruh pengujian unit dan fitur telah dijalankan dengan hasil **100% Lulus**:

```
PHPUnit 12.5.34 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.5.6
Configuration: d:\PG\NETPRO-CRM\backend\phpunit.xml

........                                                            8 / 8 (100%)

Time: 00:02.204, Memory: 36.50 MB

OK (8 tests, 96 assertions)
```

| Modul Pengujian | Target Uji | Status | Jumlah Assertions |
|---|---|:---:|:---:|
| `BillingServiceTest` | Kalkulasi PPN 11% Include Scheme | **PASSED** 🟢 | 4 |
| `BillingServiceTest` | Kalkulasi PPN 11% Exclude Scheme | **PASSED** 🟢 | 4 |
| `BillingServiceTest` | Kalkulasi Prorata Clamping Kabisat | **PASSED** 🟢 | 4 |
| `ApiAuthAndCustomerTest` | Login Sanctum Token & Profil User | **PASSED** 🟢 | 8 |
| `ApiAuthAndCustomerTest` | Registrasi Pelanggan & Auto-Activation | **PASSED** 🟢 | 12 |
| `CompleteApiEndpointsTest` | Pengujian 64+ REST API Endpoints v1 | **PASSED** 🟢 | 64 |

---

## 📡 3. Dokumentasi Endpoint API & Hasil Respons Riil

Base URL: `http://localhost:8000/api/v1`  
Semua endpoint terproteksi memerlukan Header: `Authorization: Bearer <token>` dan `Accept: application/json`.

---

### A. Modul Autentikasi & Akun (`/auth`)

#### 1. POST `/api/v1/auth/login`
- **Deskripsi**: Autentikasi user (Admin / Finance / Teknisi) dan menerbitkan Bearer Token Sanctum.
- **Request Body**:
  ```json
  {
    "username": "superadmin",
    "password": "password123"
  }
  ```
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Login berhasil.",
    "data": {
      "token": "1|N4cI6U7r2e3iR8XqWp0...",
      "user": {
        "id": 1,
        "username": "superadmin",
        "name": "Super Administrator",
        "full_name": "Super Administrator Utama NETPRO",
        "email": "superadmin@netpro.co.id",
        "division": "NOC & Core Infrastructure",
        "role": "super admin"
      }
    }
  }
  ```

#### 2. GET `/api/v1/auth/me`
- **Deskripsi**: Mengambil data profil user yang sedang terautentikasi.
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "id": 1,
      "username": "superadmin",
      "name": "Super Administrator",
      "full_name": "Super Administrator Utama NETPRO",
      "email": "superadmin@netpro.co.id",
      "phone": "0812-9876-5432",
      "division": "NOC & Core Infrastructure",
      "role": "super admin",
      "status": "active"
    }
  }
  ```

---

### B. Modul Pelanggan & Onboarding FTTH (`/customers`)

#### 1. GET `/api/v1/customers`
- **Deskripsi**: Mengambil daftar pelanggan dengan filter status, pencarian nama/CID, dan pagination.
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "cid": "CID-100882",
          "name": "Susi Susanti",
          "nik": "3275012304900001",
          "phone": "0812-9988-7766",
          "address": "Jl. Kenanga No. 14 RT 02/05, Jakarta",
          "gps_lat": "-6.289123",
          "gps_lng": "106.918456",
          "package_id": 2,
          "ppn_scheme": "include",
          "auth_method": "pppoe",
          "pppoe_user": "32750123-SUSI",
          "billing_type": "postpaid",
          "status": "active",
          "package": {
            "id": 2,
            "name": "Home Basic 20M",
            "speed_mbps": 20,
            "price": "150000.00"
          },
          "radius_user": {
            "username": "32750123-SUSI",
            "profile_name": "PROFILE_HOME_BASIC_20M",
            "ip_address": "10.100.10.15",
            "status": "CONNECTED"
          }
        }
      ],
      "total": 1
    }
  }
  ```

#### 2. POST `/api/v1/customers`
- **Deskripsi**: Pendaftaran pelanggan baru, generate otomatis username PPPoE dari NIK, dan auto sync ke tabel FreeRADIUS.
- **Request Body**:
  ```json
  {
    "name": "Budi Pratama",
    "nik": "3275019800010005",
    "phone": "081234567890",
    "email": "budi@example.com",
    "address": "Jl. Boulevard No. 45",
    "package_id": 2,
    "billing_type": "postpaid"
  }
  ```
- **Response Riil (HTTP 201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Pelanggan berhasil didaftarkan.",
    "data": {
      "id": 2,
      "cid": "CID-671204",
      "name": "Budi Pratama",
      "nik": "3275019800010005",
      "pppoe_user": "32750198-BUDI",
      "status": "inactive"
    }
  }
  ```

#### 3. POST `/api/v1/customers/{id}/set-online`
- **Deskripsi**: Mengaktifkan pelanggan (setelah teknisi pasang modem ONT), aktivasi PPPoE di RADIUS, dan menerbitkan tagihan perdana.
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Pelanggan Budi Pratama telah diaktifkan dan terhubung.",
    "data": {
      "id": 2,
      "status": "active"
    }
  }
  ```

---

### C. Modul Billing Engine & Perpajakan (`/invoices`, `/tax`)

#### 1. GET `/api/v1/tax/simulation?amount=150000&mode=include`
- **Deskripsi**: Simulasi perhitungan DPP dan PPN 11% secara transparan.
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "dpp": 135135.14,
      "ppn": 14864.86,
      "total": 150000,
      "rate": 11,
      "mode": "include"
    }
  }
  ```

#### 2. POST `/api/v1/invoices/{id}/pay`
- **Deskripsi**: Mencatat pelunasan tagihan pelanggan. Secara otomatis:
  1. Mengubah status invoice menjadi `paid`.
  2. Memicu **Double-Entry Auto-Journal** PSAK (Debit Kas 1101, Kredit Pendapatan 4101, Kredit PPN 2102).
  3. Memulihkan status isolir pelanggan dan sesi PPPoE RADIUS.
- **Request Body**:
  ```json
  {
    "payment_method": "BCA Virtual Account",
    "amount": 150000
  }
  ```
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Pembayaran invoice INV-202608-1002 berhasil dicatat dan diverifikasi.",
    "data": {
      "invoice": {
        "id": 1,
        "invoice_no": "INV-202608-1002",
        "status": "paid",
        "paid_date": "2026-08-31",
        "payment_method": "BCA Virtual Account"
      },
      "payment": {
        "id": 1,
        "payment_ref": "PAY-1756661720-412",
        "amount": "150000.00",
        "payment_method": "BCA Virtual Account"
      }
    }
  }
  ```

---

### D. Modul FreeRADIUS & MikroTik CoA (`/radius`)

#### 1. GET `/api/v1/radius/telemetry`
- **Deskripsi**: Telemetri core RADIUS, status port otentikasi (1812), port CoA (3799), latency probing, dan statistik user aktif.
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "radius_server": {
        "status": "ONLINE",
        "latency_ms": 0.42,
        "port_auth": 1812,
        "port_coa": 3799
      },
      "mikrotik_core": {
        "status": "ONLINE",
        "latency_ms": 0.38
      },
      "subscribers": {
        "total": 1,
        "connected": 1,
        "isolated": 0,
        "offline": 0
      },
      "traffic_stats": {
        "total_sessions": 0,
        "total_upload_gb": 0,
        "total_download_gb": 0
      }
    }
  }
  ```

#### 2. POST `/api/v1/radius/disconnect`
- **Deskripsi**: Mengirim paket RFC 3576 Disconnect-Request ke router MikroTik pada UDP 3799 untuk memutus/kick sesi aktif secara instan tanpa reboot router.
- **Request Body**:
  ```json
  {
    "username": "32750123-SUSI"
  }
  ```
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Paket Disconnect-Request (CoA) berhasil dikirim untuk akun [32750123-SUSI]."
  }
  ```

---

### E. Modul Akuntansi PSAK, e-Bupot PPh 23 & Kominfo (`/finance`)

#### 1. GET `/api/v1/finance/coa`
- **Deskripsi**: Mengambil Chart of Accounts (COA 34 Akun Standar PSAK) beserta saldo terkini.
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "data": [
      { "code": "1101", "name": "Kas & Setara Kas (BCA Operasional)", "category": "ASET", "balance": "45150000.00" },
      { "code": "1102", "name": "Bank Mandiri Penerimaan VA", "category": "ASET", "balance": "25000000.00" },
      { "code": "2102", "name": "Hutang PPN Keluaran 11%", "category": "KEWAJIBAN", "balance": "4214864.86" },
      { "code": "4101", "name": "Pendapatan Jasa Internet Residensial", "category": "PENDAPATAN", "balance": "135135.14" },
      { "code": "5104", "name": "Beban PNBP Iuran USO Kominfo (1.25%)", "category": "BEBAN", "balance": "0.00" },
      { "code": "5105", "name": "Beban PNBP Iuran BHP Kominfo (0.50%)", "category": "BEBAN", "balance": "0.00" }
    ]
  }
  ```

#### 2. GET `/api/v1/finance/regulatory-summary`
- **Deskripsi**: Perhitungan otomatis kewajiban Iuran Kominfo PNBP (**USO 1.25%** dan **BHP Hak Penyelenggaraan 0.50%**) dari *Gross Revenue* pendapatan jasa internet.
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "data": {
      "gross_revenue": 135135.14,
      "uso_rate": 1.25,
      "uso_amount": 1689.19,
      "bhp_rate": 0.5,
      "bhp_amount": 675.68,
      "total_regulatory_fees": 2364.87
    }
  }
  ```

#### 3. POST `/api/v1/finance/taxes`
- **Deskripsi**: Penerbitan Bukti Potong Pajak e-Bupot PPh 23 Unifikasi atas sewa tiang, kabel optik, dan upstream transit.
- **Request Body**:
  ```json
  {
    "vendor_name": "PT Moratelindo TBK",
    "npwp": "01.345.678.9-012.000",
    "obj_income": "Sewa Core Fiber Optik",
    "dpp_amount": 15000000,
    "rate_percent": 2.0
  }
  ```
- **Response Riil (HTTP 201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Bukti Potong PPh 23 BPT-202608-8812 berhasil diterbitkan.",
    "data": {
      "id": 1,
      "bupot_no": "BPT-202608-8812",
      "vendor_name": "PT Moratelindo TBK",
      "dpp_amount": "15000000.00",
      "rate_percent": "2.00",
      "tax_amount": "300000.00",
      "status": "TERBIT"
    }
  }
  ```

---

### F. Modul HR, Presensi GPS & Payroll THP (`/hr`, `/payroll`)

#### 1. POST `/api/v1/hr/clock-in`
- **Deskripsi**: Pencatatan presensi masuk teknisi dengan validasi koordinat GPS formula Haversine (jarak maks 200m dari titik kantor).
- **Request Body**:
  ```json
  {
    "employee_id": 1,
    "gps_lat": -6.289110,
    "gps_lng": 106.918210
  }
  ```
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Clock-in berhasil dicatat untuk Ahmad Rian Maulana. Status: TEPAT WAKTU.",
    "data": {
      "attendance": {
        "id": 1,
        "employee_name": "Ahmad Rian Maulana",
        "att_date": "2026-08-31",
        "clock_in": "07:45:10",
        "status": "TEPAT WAKTU"
      },
      "gps_validation": {
        "is_valid": true,
        "distance_m": 1.45,
        "max_allowed_m": 200,
        "message": "Lokasi presensi valid (1.45m dari titik pusat)"
      }
    }
  }
  ```

#### 2. POST `/api/v1/payroll/generate`
- **Deskripsi**: Rekapitulasi slip gaji bulanan karyawan (Gaji Pokok + Tunjangan + Poin Insentif BAST - Potongan BPJS).
- **Request Body**:
  ```json
  {
    "period": "Agustus 2026"
  }
  ```
- **Response Riil (HTTP 200 OK)**:
  ```json
  {
    "status": "success",
    "message": "Proses payroll periode Agustus 2026 berhasil dibuat untuk 1 karyawan.",
    "data": [
      {
        "id": 1,
        "employee_id": 1,
        "employee_name": "Ahmad Rian Maulana",
        "period": "Agustus 2026",
        "basic_salary": "5500000.00",
        "allowance": "1000000.00",
        "bonus": "500000.00",
        "deductions": "195000.00",
        "thp": "6805000.00",
        "status": "APPROVED",
        "bank_name": "BCA",
        "account_no": "8820192831"
      }
    ]
  }
  ```

---

### G. Modul Work Order FTTH, Survey & NOC (`/surveys`, `/work-orders`, `/noc`, `/tickets`)

#### 1. POST `/api/v1/work-orders`
- **Deskripsi**: Penerbitan Surat Perintah Kerja (SPK) pasang baru, pencatatan SN Modem ONT, redaman OPM (dBm), Berita Acara Serah Terima (BAST), serta otomatis memberikan klaim insentif 10 poin (Rp 500.000) ke teknisi terkait.
- **Request Body**:
  ```json
  {
    "customer_name": "Dewi Lestari",
    "package_name": "Home Basic 20M",
    "ont_type": "ZTE F670L",
    "ont_sn": "ZTEGC1234567",
    "tech_name": "Ahmad Rian Maulana",
    "odp_port": "ODP-JKT-04/Port-02",
    "attenuation": "-18.5 dBm"
  }
  ```
- **Response Riil (HTTP 201 Created)**:
  ```json
  {
    "status": "success",
    "message": "Surat Perintah Kerja (SPK) WO-20260831-502 dan BAST BAST-202608-4192 berhasil diterbitkan.",
    "data": {
      "id": 1,
      "wo_no": "WO-20260831-502",
      "bast_no": "BAST-202608-4192",
      "status": "AKTIF & ONLINE",
      "attenuation": "-18.5 dBm"
    }
  }
  ```

---

## 🛠️ 4. Pengoperasian Perintah CLI Artisan Daemon

Backend menyediakan 3 perintah scheduler siap pakai:

```bash
# 1. Menjalankan Scheduler Billing (Generate Tagihan, Auto-Isolir Overdue & WhatsApp Reminder)
php artisan isp:billing-scheduler --all

# 2. Sinkronisasi Kredensial Seluruh Pelanggan ke Server FreeRADIUS
php artisan isp:radius-sync

# 3. Snapshot Backup Database Sistem
php artisan isp:db-backup
```

---

## ✅ Kesimpulan
Backend **NETPRO CRM** telah selesai dikembangkan, diuji secara komprehensif, dan siap dihubungkan dengan antarmuka frontend web maupun mobile application.
