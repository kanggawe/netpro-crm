# PANDUAN STANDAR PENGGAJIAN (PAYROLL) & REGULASI PENGUPAHAN ISP

Dokumen ini merupakan pedoman resmi pengelolaan kompensasi, benefit, potongan jaminan sosial, dan perpajakan ketenagakerjaan bagi perusahaan penyedia jasa internet (**Internet Service Provider - ISP**) di Indonesia.

---

## 1. Landasan Hukum & Regulasi Ketenagakerjaan

1. **Peraturan Pemerintah (PP) No. 36 Tahun 2021 tentang Pengupahan** (*jo. UU No. 6 Tahun 2023 Cipta Kerja*):
   - Standarisasi struktur dan skala upah perusahaan.
   - Ketentuan upah lembur dan komponen upah tetap vs tidak tetap.
2. **Peraturan Pemerintah (PP) No. 58 Tahun 2023 & PMK No. 168 Tahun 2023**:
   - Penerapan **Tarif Efektif Rata-Rata (TER)** pemotongan PPh Pasal 21 bulanan (Kategori TER A, TER B, dan TER C).
3. **UU No. 24 Tahun 2011 tentang BPJS**:
   - Kewajiban kepesertaan jaminan sosial ketenagakerjaan dan kesehatan.

---

## 2. Struktur Komponen Penggajian ISP

### A. Komponen Pendapatan (Penambah)
1. **Gaji Pokok**: Imbalan dasar sesuai jabatan dan grade level karyawan.
2. **Tunjangan Tetap Keahlian Fiber Optic & Splicer**: Diberikan kepada teknisi bersertifikasi FO/OTDR.
3. **Tunjangan Shift Malam NOC (22:00 - 07:00 WIB)**: Kompensasi kehadiran tim NOC shift malam (standar Rp 75.000 / kehadiran).
4. **Insentif Pasang Baru (Work Order FTTH)**: Bonus produktivitas teknisi per tarikan kabel drop yang sukses dan terbit BAST (standar Rp 50.000 / titik).
5. **Komisi Sales Akuisisi Pelanggan**: Insentif penutupan kontrak baru paket berlangganan.

### B. Komponen Potongan Resmi (Pengurang)
1. **BPJS Ketenagakerjaan**:
   - **Jaminan Hari Tua (JHT)**: 2.0% (Karyawan) + 3.7% (Perusahaan).
   - **Jaminan Pensiun (JP)**: 1.0% (Karyawan) + 2.0% (Perusahaan).
   - **Jaminan Kecelakaan Kerja (JKK)**: 0.24% - 1.74% (100% Ditanggung Perusahaan).
   - **Jaminan Kematian (JKM)**: 0.30% (100% Ditanggung Perusahaan).
2. **BPJS Kesehatan**: 1.0% (Karyawan) + 4.0% (Perusahaan).
3. **Pajak Penghasilan (PPh 21 TER)**: Dipotong dari penghasilan bruto sesuai tabel TER bulanan DJP.

---

## 3. Rincian Modul Payroll Aplikasi NETPRO

- **[`payroll/master.php`](file:///d:/PG/BILL-DASH/payroll/master.php)**: Master formula seluruh komponen pendapatan dan pemotongan.
- **[`payroll/generate.php`](file:///d:/PG/BILL-DASH/payroll/generate.php)**: Eksekusi batch payroll bulanan dan penerbitan slip gaji digital karyawan (*PDF Slip Download*).
- **[`payroll/bonus.php`](file:///d:/PG/BILL-DASH/payroll/bonus.php)**: Validasi dan persetujuan pencairan insentif teknisi per-titik BAST.
- **[`payroll/laporan.php`](file:///d:/PG/BILL-DASH/payroll/laporan.php)**: Rekapitulasi konsolidasi beban kas payroll, setoran BPJS, dan PPh 21 ke kas negara.
