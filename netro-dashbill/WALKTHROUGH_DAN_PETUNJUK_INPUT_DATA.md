# 📖 WALKTHROUGH & PETUNJUK LENGKAP INPUT DATA NATIVE (PEMBELAJARAN OPERASIONAL)

**Panduan Praktik Uji Coba Input Data dari Nol & Verifikasi Laporan**  
**Aplikasi: NETPRO CRM (ISP Management OS)**

---

## 📑 DAFTAR ISI
1. [Pendahuluan & Persiapan Lingkungan Uji Coba](#1-pendahuluan--persiapan-lingkungan-uji-coba)
2. [Langkah 1: Pengosongan Database (Reset to Zero)](#langkah-1-pengosongan-database-reset-to-zero)
3. [Langkah 2: Uji Coba Input Master Data (Paket & Router NAS)](#langkah-2-uji-coba-input-master-data-paket--router-nas)
4. [Langkah 3: Uji Coba Input Pelanggan Baru & Integrasi RADIUS](#langkah-3-uji-coba-input-pelanggan-baru--integrasi-radius)
5. [Langkah 4: Uji Coba Work Order (SPK) & Penerbitan BAST Digital](#langkah-4-uji-coba-work-order-spk--penerbitan-bast-digital)
6. [Langkah 5: Uji Coba Siklus Billing & Pembayaran Invoice](#langkah-5-uji-coba-siklus-billing--pembayaran-invoice)
7. [Langkah 6: Uji Coba Penanganan Tiket Gangguan & NOC Outage](#langkah-6-uji-coba-penanganan-tiket-gangguan--noc-outage)
8. [Langkah 7: Uji Coba Akuntansi, Pengeluaran OPEX & Bukti Potong PPh 23](#langkah-7-uji-coba-akuntansi-pengeluaran-opex--bukti-potong-pph-23)
9. [Langkah 8: Uji Coba HRD, Absensi GPS & Payroll THP Karyawan](#langkah-8-uji-coba-hrd-absensi-gps--payroll-thp-karyawan)
10. [Rangkuman Hasil Verifikasi & Checklist Laporan](#10-rangkuman-hasil-verifikasi--checklist-laporan)

---

## 1. Pendahuluan & Persiapan Lingkungan Uji Coba

Dokumen ini dibuat secara khusus untuk membantu pengguna/pelajar dalam **memahami alur kerja teknis dan mempraktikkan penginputan data sampel** dari awal hingga menghasilkan laporan keuangan, pajak, dan dashboard yang valid.

### Kebutuhan Server Lokal:
- Web Server (Apache/Nginx/PHP Server): `php -S localhost:8000`
- PHP Runtime: versi 8.0 atau yang lebih baru.
- Extension PHP: `pdo`, `pdo_pgsql`, `pdo_sqlite`.

---

## Langkah 1: Pengosongan Database (Reset to Zero)

Sebelum mulai memasukkan data baru untuk latihan, pastikan database dalam kondisi bersih (0 transaksi).

### Perintah CLI untuk Reset Database:
Buka terminal di folder project `d:\PG\BILL-DASH`, lalu jalankan:
```bash
php database/reset.php
```

### Hasil yang Diharapkan:
```text
Starting Full Database Reset (Emptying all operational data - Driver: pgsql)...
✓ All operational tables truncated/emptied successfully!
✓ Default packages restored (4 packages).
✓ Default Superadmin account ready (username: superadmin / pass: admin123).

SUCCESS: Database has been completely reset to zero data!
```

---

## Langkah 2: Uji Coba Input Master Data (Paket & Router NAS)

### A. Menambahkan Paket Internet Baru
1. Buka browser: `http://localhost:8000/marketing/paket.php` ([`marketing/paket.php`](file:///d:/PG/BILL-DASH/marketing/paket.php)).
2. Klik tombol **`+ Tambah Paket Baru`**.
3. Masukkan Data Sampel berikut:
   - **Nama Paket**: `Home Gamer 100M`
   - **Kecepatan Bandwidth**: `100` Mbps
   - **Harga Bulanan**: `450000`
   - **Skema PPN**: `Include PPN`
   - **Kategori**: `home`
4. Klik **`Simpan Paket Internet`**.

### B. Mendaftarkan Router NAS MikroTik Core
1. Buka browser: `http://localhost:8000/radius/nas.php` ([`radius/nas.php`](file:///d:/PG/BILL-DASH/radius/nas.php)).
2. Klik tombol **`+ Tambah Router NAS`**.
3. Masukkan Data Sampel:
   - **Nama NAS**: `CCR-CORE-HQ-01`
   - **IP Address Router**: `10.100.0.1`
   - **Model Hardware**: `MikroTik CCR2004-16G-2S+`
   - **RADIUS Secret**: `radiussecret123`
4. Klik **`Simpan Perangkat NAS`**.

---

## Langkah 3: Uji Coba Input Pelanggan Baru & Integrasi RADIUS

1. Buka browser: `http://localhost:8000/crm/registrasi.php` ([`crm/registrasi.php`](file:///d:/PG/BILL-DASH/crm/registrasi.php)).
2. Isikan data sampel pelanggan berikut pada form registrasi:

| Nama Field Form | Nilai Sampel yang Diinput | Keterangan / Fungsi |
| :--- | :--- | :--- |
| **Nama Pelanggan** | `Budi Wijaya` | Nama resmi pada identitas KTP |
| **NIK KTP** | `3275010912830001` | NIK 16-digit validasi unik |
| **Nomor Telepon/WA** | `081234567890` | Nomor penampung notifikasi WA |
| **Email Pelanggan** | `budi.wijaya@gmail.com` | Email pengiriman invoice PDF |
| **Alamat Pemasangan** | `Jl. Jatiwaringin Raya No. 12, Bekasi` | Alamat lokasi penarikan kabel FTTH |
| **Tipe Billing** | `Pascabayar` / `Prabayar` | Pascabayar (Jatuh Tempo Tgl 20) / Prabayar (Rolling 30 Hari) |
| **Pilihan Paket** | `Home Premium 50M` | Paket langganan internet bulanan |
| **Skema PPN** | `Include PPN 11%` | Metode perhitungan PPN invoice |
| **Tagihan Awal** | `Non-Prorata` / `Prorata` | Penuh 1 bulan / Sisa hari bulan berjalan |
| **Titik GPS (Lat, Lng)** | `-6.2891, 106.9182` | Koordinat peta Leaflet GIS |
| **Metode Otentikasi** | `PPPoE Client` | Tipe dial-up koneksi internet |
| **Username PPPoE** | `32750109-BUDI` | Credentials dial-up ke RADIUS |
| **Password PPPoE** | `passbudi123` | Password otentikasi RADIUS |

3. Klik tombol **`Simpan & Daftarkan Pelanggan Baru`**.
4. **Verifikasi**: Buka menu [**Master Data Pelanggan**](file:///d:/PG/BILL-DASH/crm/daftar.php). Akun `Budi Wijaya` kini aktif dengan badge tipe billing dan credentials `32750109-BUDI` otomatis terdaftar di menu [**RADIUS Users**](file:///d:/PG/BILL-DASH/radius/users.php).

---

## Langkah 4: Uji Coba Work Order (SPK) & Penerbitan BAST Digital

1. Buka browser: `http://localhost:8000/crm/instalasi.php` ([`crm/instalasi.php`](file:///d:/PG/BILL-DASH/crm/instalasi.php)).
2. Klik **`+ Terbitkan SPK Baru`**. Masukkan data:
   - **Nama Pelanggan**: `Budi Wijaya`
   - **Tipe ONT**: `Modem ZTE F670L Dualband`
   - **Serial Number (SN)**: `ZTEG88910283`
   - **Teknisi Bertugas**: `Teknisi Rian Hidayat`
   - **Port ODP**: `ODP-JTW-04/16 (Port 3)`
   - **Hasil Ukur Redaman (OPM)**: `-18.4 dBm`
3. Klik **`Simpan Work Order`**.
4. Buka menu [**Berita Acara Serah Terima (BAST)**](file:///d:/PG/BILL-DASH/crm/berita_acara.php).
5. Klik **`Lihat Hasil BAST`** pada baris pelanggan `Budi Wijaya`.
6. Dokumen BAST digital resmi diterbitkan dengan hasil uji redaman optik dan tanda tangan digital. Klik **`Cetak / Export PDF BAST`** untuk mencetak dokumen.

---

## Langkah 5: Uji Coba Siklus Billing & Pembayaran Invoice

1. **Pelanggan Pascabayar**:
   - Buka browser: `http://localhost:8000/billing/generate.php` ([`billing/generate.php`](file:///d:/PG/BILL-DASH/billing/generate.php)).
   - Pilih Periode Tagihan: `September 2026`. Klik **`Proses Generate Billing Massal`**.
   - Tagihan terbit serentak dengan **Tanggal Jatuh Tempo Tetap (Fixed Date: Tanggal 20)**.
2. **Pelanggan Prabayar**:
   - Invoice pertama langsung berstatus **LUNAS (PAID)** dengan masa aktif **30 Hari (Rolling Date)**.
   - Perpanjangan masa aktif dapat dilakukan kapan saja via tombol **`+ Top-Up`** di menu [**Daftar Pelanggan**](file:///d:/PG/BILL-DASH/crm/daftar.php).
3. **Pelunasan Invoice**:
   - Buka menu [**Invoice Tagihan**](file:///d:/PG/BILL-DASH/billing/daftar.php).
   - Klik tombol **`Bayar`** di sebelah baris invoice. Status berubah menjadi **`LUNAS`**.
4. **Verifikasi Laporan Keuangan**: Buka menu [**Laporan Laba Rugi**](file:///d:/PG/BILL-DASH/finance/laporan.php). Pendapatan langganan terhitung DPP dan Hutang PPN 11% tercatat presisi.

---

## Langkah 6: Uji Coba Penanganan Tiket Gangguan & NOC Outage

### A. Melaporkan Tiket Gangguan CS
1. Buka browser: `http://localhost:8000/tickets/list.php` ([`tickets/list.php`](file:///d:/PG/BILL-DASH/tickets/list.php)).
2. Klik **`+ Buat Tiket Gangguan Baru`**.
3. Masukkan data:
   - **Pelanggan**: `Budi Wijaya`
   - **Kategori Gangguan**: `Redaman Optik Tinggi / LOS`
   - **Prioritas**: `HIGH`
   - **Teknisi Penanggung Jawab**: `Teknisi Standby NOC`
   - **Target SLA**: `120` Menit
4. Klik **`Simpan Tiket Gangguan`**. Tiket `TCK-XXXX` diterbitkan.
5. Setelah perbaikan selesai, klik **`Resolusi Tiket`**, masukkan *Root Cause* (*Kabel dropcore tertekuk*) dan klik **`Tandai Selesai / Closed`**.

---

## Langkah 7: Uji Coba Akuntansi, Pengeluaran OPEX & Bukti Potong PPh 23

### A. Catat Pengeluaran Beban OPEX (Sewa Upstream Bandwidth)
1. Buka browser: `http://localhost:8000/finance/pengeluaran.php` ([`finance/pengeluaran.php`](file:///d:/PG/BILL-DASH/finance/pengeluaran.php)).
2. Klik **`+ Catat Pengeluaran`**.
3. Masukkan data sampel:
   - **Kategori Beban**: `Beban Sewa Upstream IP Transit`
   - **Nama Vendor**: `PT Telkom Indonesia (Indibiz Corporate)`
   - **Akun Pembayaran**: `Bank Mandiri Corporate`
   - **Nominal (Rp)**: `5000000` (Rp 5.000.000)
   - **Keterangan**: `Sewa Bandwidth Tier-1 IP Transit 1 Gbps`
   - **Approver**: `Manager Finance`
4. Klik **`Simpan & Ajukan Pengeluaran`**. Voucher `VCH-OPEX-xxxx` terbit dan mutasi kas bank berkurang.

### B. Terbitkan Bukti Potong e-Bupot PPh 23
1. Buka browser: `http://localhost:8000/finance/pajak.php` ([`finance/pajak.php`](file:///d:/PG/BILL-DASH/finance/pajak.php)).
2. Klik **`+ Terbitkan e-Bupot PPh 23`**.
3. Masukkan data:
   - **Jenis Pajak**: `PPh Pasal 23 (Sewa & Jasa Teknik)`
   - **Nama Vendor**: `PT Telkom Indonesia`
   - **NPWP Vendor**: `01.234.567.8-000.000`
   - **Objek Penghasilan**: `Sewa Kapasitas Bandwidth Upstream`
   - **Nilai DPP (Rp)**: `5000000`
   - **Tarif Pajak**: `2.0%`
4. Klik **`Terbitkan Bukti Potong e-Bupot`**. Nilai Pajak PPh 23 terhitung otomatis **Rp 100.000** dengan nomor dokumen `BUPOT-23-xxxx`.
5. Setelah disetor ke bank persepsi, klik **`Input NTPN Bayar`** dan masukkan 16 digit kode NTPN (contoh: `0192837465510293`).

---

## Langkah 8: Uji Coba HRD, Absensi GPS & Payroll THP Karyawan

1. **Presensi GPS**: Buka [`hr/absensi.php`](file:///d:/PG/BILL-DASH/hr/absensi.php), klik **`Clock-In`** presensi GPS teknisi.
2. **Klaim Insentif BAST**: Buka [`payroll/bonus.php`](file:///d:/PG/BILL-DASH/payroll/bonus.php), klik **`Klaim Poin Insentif`** atas perakitan FTTH `Budi Wijaya` (Poin: `10`, Rate: `Rp 50.000`, Total Insentif: `Rp 500.000`).
3. **Proses Payroll THP**: Buka [`payroll/gaji.php`](file:///d:/PG/BILL-DASH/payroll/gaji.php), klik **`Proses Batch Payroll Bulanan`**. Slip gaji diterbitkan dengan Take Home Pay (THP) yang menghitung insentif BAST secara presisi.

---

## 10. Rangkuman Hasil Verifikasi & Checklist Laporan

Setelah menyelesaikan seluruh langkah di atas, verifikasi laporan akhir Anda:

- [x] **[Dashboard Utama](file:///d:/PG/BILL-DASH/dashboard/utama.php)**: Menampilkan 1 Pelanggan Aktif, Pendapatan Rp 225.225,23, dan 0 Tiket Gangguan Terbuka.
- [x] **[Dashboard Billing & Pendapatan](file:///d:/PG/BILL-DASH/dashboard/revenue.php)**: Collection Rate 100%, MRR Rp 225.225,23, Piutang Rp 0.
- [x] **[Laporan Keuangan Neraca & Laba Rugi](file:///d:/PG/BILL-DASH/finance/laporan.php)**: Persamaan akuntansi `BALANCED ✓` (Aset = Liabilitas + Ekuitas).
- [x] **[Manajemen Pajak & Kominfo](file:///d:/PG/BILL-DASH/finance/pajak.php)**: Bukti potong PPh 23 status `LUNAS (NTPN)` dan Iuran Kominfo (USO 1.25% & BHP 0.50%) terhitung otomatis dari Gross Revenue.
