# 🐛 BUG TRACKER & KNOWN ISSUES — NETPRO CRM

Pelacak isu teknis, *edge cases*, dan catatan perbaikan pada sistem NETPRO CRM.

| ID | Deskripsi Bug / Issue | Lokasi / Modul Terkait | Severity | Status | Catatan / Rencana Solusi |
|---|---|---|---|---|---|
| **BUG-001** | Potensi timeout socket UDP 3799 saat Router NAS sedang unreachable / rebooting | `radius/` / `api/handler.php` | Medium | In Progress | Tambahkan socket timeout 2s dan catch error agar proses batch isolir tidak berhenti di tengah jalan. |
| **BUG-002** | Perbedaan pembulatan selisih desimal pada metode *Include PPN 11%* untuk paket nominal ganjil | `billing/` / `finance/` | Low | Fixed | Standardisasi menggunakan `ROUND(..., 2)` atau pembulatan floor/ceil seragam di level database dan PHP. |
| **BUG-003** | Validasi NIK pelanggan jika input mengandung karakter non-numerik | `crm/registrasi.php` | Medium | Fixed | Ditambahkan regex validation `preg_match('/^[0-9]{16}$/', $nik)` sebelum commit ke database. |
| **BUG-004** | Duplikasi penagihan invoice pascabayar jika cronjob dijalankan berulang pada tanggal 1 | `billing/cron_generate_invoices.php` | High | Fixed | Tambahkan *Unique Constraint* `(pelanggan_id, periode_bulan, periode_tahun)` pada tabel `invoices`. |
| **BUG-005** | Kartu telemetri GPON, Otentikasi RADIUS, dan Trafik di profil 360° menampilkan data dummy `CONNECTED` pada pelanggan baru / inactive | `crm/detail.php` | Medium | Fixed | Mengubah 4 kartu telemetri menjadi 100% dinamis mengikuti status pelanggan riil (`inactive`, `active`, `isolated`), riwayat invoice, dan data Work Order. |
| **BUG-006** | Tailwind CSS selector `space-y-6` menambahkan margin-top ke modal backdrop `fixed inset-0`, menyebabkan celah blur tidak menutup navbar atas | `assets/css/style.css` / `includes/navbar.php` | Medium | Fixed | Ditambahkan aturan CSS reset `.space-y-6 > .fixed { margin-top: 0 !important; }` dan full viewport overlay `z-index: 9999`. |

