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
| **BUG-007** | Status FreeRADIUS menampilkan ONLINE padahal service fisik mati karena fallback database lokal `$pdo !== null` | `dashboard/utama.php` | Medium | Fixed | Menghapus fallback database dan menerapkan socket probing murni `is_hardware_node_online` pada port autentikasi 1812/1813. |
| **BUG-008** | Celah putih / sub-pixel background bleed pada sambungan chevron breadcrumbs di layar smartphone | `assets/css/style.css` | Low | Fixed | Menerapkan Solid Flat Left Edge Overlay dengan `-10px margin-left` dan layer `z-index` menurun (10, 9, 8...). |
| **BUG-009** | Judul halaman panjang di navbar dan nama pelanggan di profil 360° bertumpuk vertikal/terpotong pada layar HP | `includes/navbar.php` / `crm/detail.php` | Medium | Fixed | Menerapkan container `flex-1 min-w-0` dengan `truncate` responsif di navbar dan `flex-wrap` dengan `shrink-0` badge di profil pelanggan. |
| **BUG-010** | Efek hover pada breadcrumb chevron (`translateY(-1px)` & `z-index: 20`) memunculkan patahan diagonal dan bayangan gelap pada segmen di sebelahnya | `assets/css/style.css` | Low | Fixed | Menghapus translasi vertikal, menjaga urutan stacking index pita poligon tetap rata, dan mengganti efek hover menjadi *glow brightness* serta *badge scaling* yang mulus. |



