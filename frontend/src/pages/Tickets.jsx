import React, { useEffect, useState } from 'react';
import {
  Ticket,
  AlertTriangle,
  Plus,
  CheckCircle,
  ShieldAlert,
  MessageSquare,
  Star,
  ThumbsUp,
  Clock,
  User,
  Search,
} from 'lucide-react';
import { api } from '../api/client';

export default function Tickets({ showToast, currentRoute = 'tickets-list', onNavigate }) {
  const [tickets, setTickets] = useState([]);
  const [outages, setOutages] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isTicketModalOpen, setIsTicketModalOpen] = useState(false);
  const [isOutageModalOpen, setIsOutageModalOpen] = useState(false);

  // Complaints / CSAT feedback state
  const [complaints, setComplaints] = useState([
    { id: 'CSAT-2026-091', cust: 'Bpk. Hendra Gunawan', rating: 5, category: 'Kecepatan Respon Teknisi', comment: 'Teknisi datang hanya 20 menit setelah laporan FO putus. Sangat memuaskan!', date: '2026-06-18', status: 'Followed Up' },
    { id: 'CSAT-2026-092', cust: 'Ibu Ratna Sari', rating: 4, category: 'Kualitas Sinyal Wi-Fi', comment: 'Setelah diganti router dual-band, lantai 2 sekarang dapat sinyal penuh.', date: '2026-06-17', status: 'Followed Up' },
    { id: 'CSAT-2026-093', cust: 'CV Mandiri Sejahtera', rating: 3, category: 'Waktu Perbaikan', comment: 'Sempat terputus 2 jam karena perbaikan gardu PLN, namun info WhatsApp cukup jelas.', date: '2026-06-16', status: 'In Review' },
  ]);

  const [ticketForm, setTicketForm] = useState({
    category: 'LOS_RED_LIGHT',
    priority: 'HIGH',
    description: '',
  });

  const fetchData = async () => {
    setLoading(true);
    try {
      const [tckRes, outRes] = await Promise.all([
        api.get('/tickets').catch(() => ({ data: { data: [] } })),
        api.get('/noc/outages').catch(() => ({ data: { data: [] } })),
      ]);
      setTickets(tckRes.data?.data || []);
      setOutages(outRes.data?.data || []);
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleCreateTicket = async (e) => {
    e.preventDefault();
    try {
      const res = await api.post('/tickets', ticketForm);
      showToast({
        type: 'success',
        title: 'Tiket Dibuka',
        message: `Tiket gangguan #${res.data?.ticket_no || '2026-081'} berhasil dibuat.`,
      });
      setIsTicketModalOpen(false);
      fetchData();
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    }
  };

  const handleResolveTicket = async (id) => {
    try {
      await api.post(`/tickets/${id}/resolve`, { solution: 'Perbaikan konektor optik & restart ONU.' });
      showToast({
        type: 'success',
        title: 'Tiket Selesai',
        message: 'Tiket gangguan telah ditandai terselesaikan.',
      });
      fetchData();
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    }
  };

  // Subview: Complaints & CSAT
  if (currentRoute === 'tickets-complaints') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Manajemen Komplain Pelanggan & Hasil Survey CSAT</h2>
            <p className="text-xs text-slate-500">Skor kepuasan layanan, catatan kendala berulang, dan audit penanganan CS/Helpdesk.</p>
          </div>
          <div className="flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-xl text-xs font-bold">
            <Star className="w-4 h-4 fill-emerald-500 text-emerald-500" />
            <span>Skor CSAT: 4.8 / 5.0 (98% Puas)</span>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          {complaints.map((c) => (
            <div key={c.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
              <div className="flex justify-between items-center">
                <span className="font-mono text-xs font-bold text-red-600">{c.id}</span>
                <div className="flex items-center gap-0.5">
                  {[...Array(5)].map((_, i) => (
                    <Star key={i} className={`w-3 h-3 ${i < c.rating ? 'fill-yellow-400 text-yellow-400' : 'text-slate-300'}`} />
                  ))}
                </div>
              </div>
              <div>
                <h4 className="text-sm font-bold text-slate-900">{c.cust}</h4>
                <span className="text-[11px] text-amber-600 font-semibold">{c.category}</span>
              </div>
              <p className="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 italic">
                "{c.comment}"
              </p>
              <div className="pt-2 flex justify-between items-center text-xs text-slate-400">
                <span>{c.date}</span>
                <span className="badge badge-online text-[10px]">{c.status}</span>
              </div>
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Default Subview: Trouble Tickets List (tickets-list)
  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-xl font-bold text-slate-900 flex items-center gap-2">
            <Ticket className="w-5 h-5 text-red-600" />
            <span>Helpdesk & Trouble Ticket Gangguan Pelanggan</span>
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Eskalasi tiket LOS lampu merah, drop sinyal redaman, dan komitmen SLA penanganan.
          </p>
        </div>

        <button
          onClick={() => setIsTicketModalOpen(true)}
          className="btn-primary text-xs py-2.5 px-4 flex items-center space-x-2"
        >
          <Plus className="w-4 h-4" />
          <span>Buat Tiket Baru</span>
        </button>
      </div>

      {/* Tickets Table */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="p-4 border-b border-slate-100">
          <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider">
            Antrean Tiket Aktif & Status Eskalasi
          </h3>
        </div>

        <div className="table-container">
          <table className="custom-table">
            <thead>
              <tr>
                <th>NO TIKET</th>
                <th>KATEGORI KENDALA</th>
                <th>PRIORITAS</th>
                <th>PELANGGAN / DESKRIPSI</th>
                <th>STATUS</th>
                <th className="text-right">AKSI SELESAI</th>
              </tr>
            </thead>
            <tbody>
              {tickets.length === 0 ? (
                <tr>
                  <td colSpan={6} className="text-center py-6 text-slate-400 text-xs">
                    {loading ? 'Memuat daftar tiket...' : 'Tidak ada tiket gangguan yang terbuka.'}
                  </td>
                </tr>
              ) : (
                tickets.map((t) => (
                  <tr key={t.id}>
                    <td className="font-mono text-xs font-bold text-red-600">
                      #{t.ticket_no}
                    </td>
                    <td>
                      <span className="badge bg-red-50 text-red-700 border border-red-200 text-[10px]">
                        {t.category}
                      </span>
                    </td>
                    <td>
                      <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full border ${
                        t.priority === 'CRITICAL' || t.priority === 'HIGH'
                          ? 'bg-red-50 text-red-700 border-red-200'
                          : 'bg-amber-50 text-amber-700 border-amber-200'
                      }`}>
                        {t.priority}
                      </span>
                    </td>
                    <td>
                      <div className="text-xs font-semibold text-slate-800">
                        {t.customer?.name || 'Pelanggan FTTH'}
                      </div>
                      <div className="text-[11px] text-slate-500 max-w-xs truncate">
                        {t.description || 'Lampu indikator PON/LOS berkedip merah.'}
                      </div>
                    </td>
                    <td>
                      <span className={`badge ${
                        t.status === 'resolved' ? 'badge-success' : 'badge-warning'
                      }`}>
                        {t.status === 'resolved' ? 'Selesai' : 'Sedang Diproses'}
                      </span>
                    </td>
                    <td className="text-right">
                      {t.status !== 'resolved' && (
                        <button
                          onClick={() => handleResolveTicket(t.id)}
                          className="px-2.5 py-1 rounded bg-emerald-50 text-emerald-600 hover:bg-emerald-100 text-xs font-bold transition flex items-center space-x-1 ml-auto"
                        >
                          <CheckCircle className="w-3 h-3" />
                          <span>Selesaikan</span>
                        </button>
                      )}
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
