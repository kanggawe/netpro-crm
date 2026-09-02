# 📚 PANDUAN DOKUMENTASI KONTEKS AI — NETPRO CRM

Direktori [`docs/ai_context/`](file:///d:/PG/BILL-DASH/docs/ai_context/) berfungsi sebagai **sistem memori, state tracker, dan panduan kontinuitas konteks** untuk kolaborasi optimal bersama AI IDE (Antigravity, Cursor, Windsurf, Claude Code, dll) pada proyek **NETPRO CRM (ISP Management OS)**.

---

## 🗂️ Daftar Berkas & Fungsinya

| Berkas | Fungsi Utama | Kapan Diperbarui? |
|---|---|---|
| [`AI_RULES.md`](file:///d:/PG/BILL-DASH/docs/ai_context/AI_RULES.md) | Aturan operasional, standar FreeRADIUS, kepatuhan PPN/PSAK, dan keamanan RBAC. | Saat ada standar arsitektur baru. |
| [`PROJECT_MEMORY.md`](file:///d:/PG/BILL-DASH/docs/ai_context/PROJECT_MEMORY.md) | *Single source of truth* arsitektur 33 tabel, stack sistem, gotchas, dan state berjalan. | Saat ada milestone atau perubahan struktur modul. |
| [`TASKS.md`](file:///d:/PG/BILL-DASH/docs/ai_context/TASKS.md) | Sprint tracker, prioritas aktif, dan backlog fitur ISP. | Setiap memulai atau menyelesaikan tugas. |
| [`DECISIONS.md`](file:///d:/PG/BILL-DASH/docs/ai_context/DECISIONS.md) | *Architecture Decision Records* (ADR) untuk keputusan FreeRADIUS, CoA, Dual Billing, dan PSAK. | Saat mengambil keputusan desain teknis penting. |
| [`HANDOVER.md`](file:///d:/PG/BILL-DASH/docs/ai_context/HANDOVER.md) | Catatan serah terima pekerjaan, status kode terakhir, dan langkah berikutnya antar sesi AI. | Di akhir setiap sesi kerja AI. |
| [`BUGS.md`](file:///d:/PG/BILL-DASH/docs/ai_context/BUGS.md) | Pelacak isu teknis, bug isolir/CoA, edge case desimal PPN, dan catatan perbaikan. | Saat bug ditemukan atau diperbaiki. |
| [`PROMPTS.md`](file:///d:/PG/BILL-DASH/docs/ai_context/PROMPTS.md) | Kumpulan template prompt teruji untuk audit RADIUS, billing, akuntansi, dan handover. | Saat menemukan prompt yang sangat berguna. |
| [`CHANGELOG.md`](file:///d:/PG/BILL-DASH/docs/ai_context/CHANGELOG.md) | Riwayat versi, rilis modul, dan perubahan fitur NETPRO CRM (v1.0.0 s/d v2.5.0). | Saat rilis versi atau penambahan fitur baru. |
| [`HISTORI_INTEGRASI.md`](file:///d:/PG/BILL-DASH/HISTORI_INTEGRASI_LINTAS_HALAMAN_A_SAMPAI_D.md) | Master Walkthrough & Histori Integrasi Terpadu Lintas Halaman (Bagian A – D). | Arsip permanen implementasi sistem. |

---

## 🚀 Alur Kerja Praktis
1. **Memulai Sesi Baru**: Jalankan prompt inisialisasi dari [`PROMPTS.md`](file:///d:/PG/BILL-DASH/docs/ai_context/PROMPTS.md#1--session-start--context-refresh-prompt):
   > *"Baca seluruh file di folder `docs/ai_context/`. Berikan ringkasan singkat status proyek NETPRO CRM saat ini dan apa tugas prioritas kita."*
2. **Saat Mengerjakan Fitur**: Ambil ID tugas dari [`TASKS.md`](file:///d:/PG/BILL-DASH/docs/ai_context/TASKS.md) dan pastikan mengikuti aturan di [`AI_RULES.md`](file:///d:/PG/BILL-DASH/docs/ai_context/AI_RULES.md).
3. **Mengakhiri Sesi**: Minta AI memperbarui [`HANDOVER.md`](file:///d:/PG/BILL-DASH/docs/ai_context/HANDOVER.md) dan mencentang tugas yang selesai di [`TASKS.md`](file:///d:/PG/BILL-DASH/docs/ai_context/TASKS.md).
