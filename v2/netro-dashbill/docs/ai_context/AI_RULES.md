# 🤖 AI WORKFLOW RULES & GUIDELINES — NETPRO CRM

Panduan dan aturan kerja khusus untuk AI IDE saat mengelola basis kode **NETPRO CRM (ISP Management OS)**.

---

## 1. Context & Initialization Protocol
- **Sebelum mulai bekerja**:
  - Baca `docs/ai_context/PROJECT_MEMORY.md` untuk memahami arsitektur 33 tabel dan relasi antar modul.
  - Baca `docs/ai_context/TASKS.md` untuk prioritas sprint saat ini.
  - Baca `docs/ai_context/HANDOVER.md` untuk mengetahui catatan sesi sebelumnya.
- **Sebelum mengubah arsitektur**:
  - Cek `docs/ai_context/DECISIONS.md` (ADR) agar tidak mengubah keputusan arsitektur FreeRADIUS, Dual Billing, atau Akuntansi PSAK secara sepihak.

---

## 2. Standar Koding & Integritas Sistem ISP
- **Database & FreeRADIUS Schema**:
  - Tabel core RADIUS (`radcheck`, `radreply`, `radacct`, `nas`, `radgroupcheck`, `radgroupreply`) harus selalu kompatibel dengan FreeRADIUS 3.0.
  - Password akun PPPoE/Hotspot di `radcheck` harus konsisten dengan atribut `Cleartext-Password` atau enkripsi yang didukung FreeRADIUS.
- **Dynamic CoA (Change of Authorization)**:
  - Perintah disconnect / rate-limit update harus dikirimkan ke UDP Port 3799 NAS MikroTik (RFC 3576). Pastikan socket timeout dan exception handling ditangani dengan baik.
- **Kepatuhan Regulasi & Pajak**:
  - Perhitungan PPN 11% (Include & Exclude) tidak boleh diubah formulanya tanpa arahan (DPP = Total/1.11 untuk Include).
  - Jurnal akuntansi otomatis (COA 34 Akun PSAK) harus selalu seimbang (*Debit = Kredit*) pada setiap transaksi pelunasan invoice.
  - Perhitungan PNBP Kominfo (USO 1.25% dan BHP 0.50%) berbasis Omzet Bruto.
- **Keamanan & Role-Based Access Control (RBAC)**:
  - Validasi session user (`$_SESSION['user_id']`, `role`, `is_logged_in`) dan cegah *privilege escalation* antar level (*Superadmin, Keuangan, NOC, Teknisi, HRD*).
  - Gunakan *Prepared Statements* (PDO) untuk seluruh kueri database guna mencegah SQL Injection.

---

## 3. Workflow & Output Protocol
1. **Analisis & Rencana**: Jelaskan singkat file mana yang akan diubah dan dampaknya ke modul lain sebelum mengeksekusi.
2. **Eksekusi Bertahap**: Modifikasi file secara modular, hindari penulisan ulang file secara masif jika hanya butuh perbaikan terarah.
3. **Pembaruan Konteks**:
   - Perbarui checklist di `docs/ai_context/TASKS.md`.
   - Catat bug / edge case baru di `docs/ai_context/BUGS.md`.
   - Perbarui `docs/ai_context/HANDOVER.md` di akhir sesi.
