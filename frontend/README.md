# 🖥️ NETPRO CRM — Frontend SPA (Vite JS + React)

Aplikasi antarmuka frontend resmi **NETPRO CRM (Enterprise Edition v4.0)** yang dibangun menggunakan **Vite JS** dan **React 19** dengan tema **Ruby Crimson Red & Dark Slate**.

Aplikasi ini terhubung langsung ke backend **Laravel 13 RESTful API** (`/api/v1`).

---

## ⚡ Cara Menjalankan Frontend

1. **Pastikan Backend Laravel Berjalan**:
   ```bash
   cd d:\PG\NETPRO-CRM\backend
   php artisan serve --port=8000
   ```

2. **Jalankan Frontend Vite Server**:
   ```bash
   cd d:\PG\NETPRO-CRM\frontend
   npm run dev
   ```
   Akses di browser: `http://localhost:5173`

3. **Akun Login Pengujian**:
   - **Superadmin**: `superadmin` / `password123`
   - **Finance**: `admin_finance` / `password123`

---

## 🧩 Fitur & Modul Utama
- **Executive Telemetry**: Status live hardware BRAS MikroTik & FreeRADIUS.
- **Pelanggan & FTTH**: Registrasi NIK 16-digit, PPPoE credentials, aktivasi modem online, isolir, quick top-up.
- **Billing & Faktur**: Tagihan bulanan, simulasi PPN 11% (*include / exclude*), pelunasan instan, WhatsApp reminder.
- **FreeRADIUS & CoA**: Active sessions, kick/disconnect via RFC 3576 CoA (UDP port 3799).
- **SPK & BAST Digital**: Surat Perintah Kerja teknisi, ukur redaman OPM (dBm), SN modem ONT, BAST digital.
- **Helpdesk & NOC**: Trouble tickets, penanganan gangguan putus kabel Fiber Optik (FO).
- **Akuntansi PSAK & Kominfo**: COA 34 Akun, Buku Jurnal, e-Bupot PPh 23, Iuran Kominfo PNBP USO 1.25% & BHP 0.50%.
- **HR & Payroll**: Presensi GPS validasi jarak Haversine, slip gaji THP, klaim bonus poin instalasi BAST.
- **Gudang Material & Pengaturan**: Stok ONT/Kabel FO, konfigurasi identitas perusahaan, snapshot backup.
