# 💡 PROMPT REPOSITORY — NETPRO CRM

Kumpulan template prompt yang terbukti efektif dan siap pakai untuk berbagai skenario di proyek NETPRO CRM.

---

### 1. 🔄 Session Start / Context Refresh Prompt
```text
Baca seluruh file di folder docs/ai_context/ (terutama AI_RULES.md, PROJECT_MEMORY.md, TASKS.md, dan HANDOVER.md). Berikan ringkasan singkat pemahamanmu tentang status proyek NETPRO CRM saat ini dan apa tugas prioritas yang perlu kita kerjakan.
```

---

### 2. 🌐 RADIUS AAA & MikroTik CoA Audit Prompt
```text
Tinjau implementasi otentikasi RADIUS dan Change of Authorization (CoA) pada file [path/to/file]:
1. Pastikan atribut RFC 3576 / RFC 5176 (User-Name, Mikrotik-Rate-Limit, Framed-IP-Address) sudah valid.
2. Pastikan penanganan socket UDP port 3799 memiliki timeout dan error handling jika router NAS offline.
3. Pastikan status isolir dan perubahan bandwidth tersinkronisasi ke tabel radcheck dan radreply.
```

---

### 3. 💵 Billing Engine & Tax Compliance Audit Prompt
```text
Periksa logika perhitungan tagihan pada file [path/to/file]:
1. Validasi rumus PPN 11% (Include: DPP = Total/1.11 vs Exclude: DPP * 11%).
2. Validasi formula Prorata harian untuk pelanggan baru di tengah siklus berjalan.
3. Pastikan status invoice (Unpaid, Paid, Overdue, Cancelled) memicu perubahan status pelanggan secara tepat.
```

---

### 4. 📊 Accounting & General Ledger Verifier Prompt
```text
Tinjau pembuatan jurnal otomatis pada modul [finance/path/to/file]:
1. Pastikan setiap transaksi selalu menghasilkan jurnal seimbang (Total Debit == Total Kredit).
2. Periksa kecocokan nomor akun COA dengan 34 Akun Standar PSAK ISP.
3. Pastikan pemotongan e-Bupot PPh 23 (2%) dan pencatatan NTPN terekam dengan benar.
```

---

### 5. 🤝 End-of-Session Handover Update Prompt
```text
Pekerjaan kita untuk sesi ini sudah selesai. Tolong perbarui:
1. docs/ai_context/TASKS.md (tandai tugas yang selesai dan tambahkan tugas baru jika ada).
2. docs/ai_context/HANDOVER.md (ringkas apa yang dikerjakan, kondisi kode terakhir, dan langkah berikutnya).
3. docs/ai_context/BUGS.md (jika ada bug/edge case baru yang ditemukan).
```
