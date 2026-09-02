# 📘 Dokumentasi Resmi Proyek NETPRO CRM (Backend Laravel 13 & Frontend Vite JS)

Dokumen ini berisi dokumentasi teknis menyeluruh, arsitektur sistem, panduan pengoperasian, dokumentasi RESTful API (v1), serta hasil integrasi antarmuka frontend **NETPRO CRM (v4.2.0)** yang menggabungkan:
1. **Layout & Struktur Navigasi 14 Modul**: Berdasarkan [dashboard_isp_management.html](file:///d:/PG/NETPRO-CRM/frontend/dashboard_isp_management.html).
2. **Warna & Desain Navbar dan Sidebar**: 100% presisi mengacu pada [red_dashboard.html](file:///d:/PG/NETPRO-CRM/v2/netro-dashbill/red_dashboard.html).

---

## 🎨 1. Rincian Tampilan Navbar & Sidebar (`red_dashboard.html`)

### 📌 Sidebar Navigation:
- **Background**: Gradien vertikal `from-brand-950 via-slate-950 to-brand-950` dengan ambient glow ruby merah di sudut atas dan bawah.
- **Logo Header**: Badge gradien `from-brand-600 to-brand-800` berbayangan dengan teks **REDDASH** (atau NETPRO CRM) bertitik hijau status online, serta subtitle *ISP Carrier OS v4.2*.
- **Search Bar Input**: Dark rounded translucent capsule (`bg-white/[0.04]`) berikon merah dengan shortcut `⌘K`.
- **Menu Accordion 14 Modul**: Header kategori berikon dot merah menyala (`bg-brand-500 shadow-[0_0_6px_rgba(239,68,68,0.8)]`), tombol aktif bergradien `from-brand-600 to-brand-700` dengan border `border-brand-500/30`, dan sub-link berlatar belakang `bg-brand-900/50`.
- **Footer Profil**: Kartu profil mengambang dengan avatar gradien `from-brand-600 to-rose-600`, dot hijau status online, dan tombol logout.

### 📌 Top Navbar:
- **Background**: Translucent White Glassmorphism `bg-white/80 backdrop-blur-xl border-b border-slate-100 shadow-sm`.
- **Kiri**: Tombol hamburger mobile responsive, Judul tebal `text-slate-900 font-extrabold`, dan subtitle tanggal sinkronisasi real-time.
- **Kanan**: Tombol pesan beranimasi ping merah, lonceng notifikasi ber-badge angka merah (`bg-brand-600`), serta avatar Admin Utama ber-ring `ring-brand-200`.

---

## 🚀 2. Panduan Menjalankan Sistem Secara Lokal

### Langkah 1: Jalankan Backend Laravel
```bash
cd d:\PG\NETPRO-CRM\backend
php artisan serve --port=8000
```

### Langkah 2: Jalankan Frontend Vite JS
```bash
cd d:\PG\NETPRO-CRM\frontend
npm run dev
```
Akses di: **`http://localhost:5173`**

### Kredensial Login:
- **Username**: `superadmin`
- **Password**: `password123`
