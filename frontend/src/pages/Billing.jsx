import React, { useEffect, useState } from 'react';
import {
  CreditCard,
  Layers,
  Calculator,
  RefreshCw,
  FileText,
} from 'lucide-react';
import { api } from '../api/client';

export default function Billing({ showToast, currentRoute = 'billing-daftar', onNavigate }) {
  const [invoices, setInvoices] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedInvoice, setSelectedInvoice] = useState(null);
  const [payModalOpen, setPayModalOpen] = useState(false);
  const [paymentMethod, setPaymentMethod] = useState('BCA Virtual Account');
  const [actionLoading, setActionLoading] = useState(false);

  // Generate Tagihan Form State
  const [billingPeriod, setBillingPeriod] = useState('Juni 2026');
  const [dueDate, setDueDate] = useState('2026-06-20');

  // Tax Simulation State
  const [simAmount, setSimAmount] = useState(150000);
  const [simMode, setSimMode] = useState('include');
  const [simResult, setSimResult] = useState(null);

  const fetchInvoices = async () => {
    setLoading(true);
    try {
      const res = await api.get('/invoices?per_page=50');
      setInvoices(res.data?.data || []);
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setLoading(false);
    }
  };

  const handleSimulateTax = async () => {
    try {
      const res = await api.get(`/tax/simulation?amount=${simAmount}&mode=${simMode}`);
      setSimResult(res.data);
    } catch (err) {
      console.error(err);
    }
  };

  useEffect(() => {
    fetchInvoices();
    handleSimulateTax();
  }, []);

  const handleGenerateMonthly = async (e) => {
    e?.preventDefault();
    setActionLoading(true);
    try {
      const res = await api.post('/invoices/generate-monthly', {
        period: billingPeriod,
        due_date: dueDate,
      });
      showToast({
        type: 'success',
        title: 'Generate Tagihan Berhasil',
        message: `Periode ${billingPeriod}: ${res.data?.generated_count || 1245} invoice pelanggan aktif berhasil diterbitkan.`,
      });
      if (onNavigate) {
        onNavigate('billing-daftar');
      }
      fetchInvoices();
    } catch (err) {
      showToast({ type: 'error', message: err.message || 'Gagal menerbitkan tagihan massal.' });
    } finally {
      setActionLoading(false);
    }
  };

  const handlePay = async (e) => {
    e.preventDefault();
    if (!selectedInvoice) return;
    setActionLoading(true);
    try {
      await api.post(`/invoices/${selectedInvoice.id}/pay`, {
        payment_method: paymentMethod,
        amount: selectedInvoice.total_amount,
      });
      showToast({
        type: 'success',
        title: 'Pembayaran Lunas',
        message: `Invoice ${selectedInvoice.invoice_no} lunas. Jurnal PSAK otomatis terbentuk & isolir dipulihkan.`,
      });
      setPayModalOpen(false);
      fetchInvoices();
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setActionLoading(false);
    }
  };

  const handleSendReminder = async (inv) => {
    try {
      await api.post(`/invoices/${inv.id}/send-reminder`);
      showToast({
        type: 'success',
        title: 'Reminder Terkirim',
        message: `Notifikasi pesan WhatsApp pengingat tagihan berhasil dikirim ke pelanggan.`,
      });
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    }
  };

  // If on "Generate Tagihan Massal" subroute
  if (currentRoute === 'billing-generate') {
    return (
      <div className="space-y-6 text-xs max-w-2xl mx-auto py-2">
        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-5">
          <div className="border-b border-slate-100 pb-3">
            <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
              <i className="fa-solid fa-file-invoice-dollar text-blue-600"></i> Prosedur Generate Tagihan Bulanan Massal
            </h3>
            <p className="text-slate-400 mt-0.5">
              Sistem akan membuat record invoice baru dengan kalkulasi PPN 11% sesuai skema registrasi.
            </p>
          </div>

          <form onSubmit={handleGenerateMonthly} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="font-semibold text-slate-700 block mb-1">Periode Tagihan</label>
                <select
                  value={billingPeriod}
                  onChange={(e) => setBillingPeriod(e.target.value)}
                  className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 text-xs focus:bg-white focus:border-[#dc2626] outline-none"
                >
                  <option>Juni 2026</option>
                  <option>Juli 2026</option>
                  <option>Agustus 2026</option>
                  <option>September 2026</option>
                </select>
              </div>
              <div>
                <label className="font-semibold text-slate-700 block mb-1">
                  Tanggal Jatuh Tempo (Due Date Pascabayar)
                </label>
                <input
                  type="date"
                  value={dueDate}
                  onChange={(e) => setDueDate(e.target.value)}
                  required
                  className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs focus:bg-white focus:border-[#dc2626] outline-none"
                />
                <span className="text-[10px] text-slate-400 block mt-0.5">Standar Jatuh Tempo: Tanggal 20</span>
              </div>
            </div>

            <div className="p-4 bg-red-50/70 border border-red-100 rounded-2xl space-y-1 text-slate-900">
              <div className="flex items-center gap-2 font-extrabold text-[#b91c1c] text-xs">
                <i className="fa-solid fa-triangle-exclamation text-[#dc2626]"></i> Integrasi Otomatis WhatsApp Gateway
              </div>
              <p className="text-[11px] text-[#7f1d1d] leading-relaxed">
                Setelah invoice digenerate, bot WhatsApp otomatis mengirimkan pesan rincian tagihan beserta link pembayaran QRIS/VA ke 1,245 nomor pelanggan aktif.
              </p>
            </div>

            <button
              type="submit"
              disabled={actionLoading}
              className="w-full bg-[#dc2626] hover:bg-[#b91c1c] text-white font-extrabold py-3.5 rounded-xl shadow-lg shadow-[#7f1d1d]/30 transition flex items-center justify-center gap-2 text-sm disabled:opacity-50"
            >
              <i className="fa-solid fa-bolt"></i>
              <span>{actionLoading ? 'Memproses Tagihan...' : 'Jalankan Generate Tagihan (1,245 Akun)'}</span>
            </button>
          </form>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-xl font-bold text-slate-900 flex items-center gap-2">
            <CreditCard className="w-5 h-5 text-red-600" />
            <span>Dual Billing Engine & Perpajakan PPN 11%</span>
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Faktur tagihan bulanan, simulasi perhitungan DPP & PPN 11%, pelunasan instan, dan auto-journaling PSAK.
          </p>
        </div>

        <button
          onClick={() => onNavigate && onNavigate('billing-generate')}
          className="btn-primary text-xs py-2.5 px-4 flex items-center space-x-2"
        >
          <Layers className="w-4 h-4" />
          <span>+ Generate Tagihan Massal</span>
        </button>
      </div>

      {/* Tax Simulator Tool Widget */}
      <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div className="flex items-center space-x-2 mb-3">
          <Calculator className="w-4 h-4 text-red-600" />
          <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider">
            Simulasi Rumus Perpajakan PPN 11% (DJP Kemenkeu)
          </h3>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
          <div>
            <label className="block text-[11px] font-semibold text-slate-600 mb-1">Nominal Paket (Rp)</label>
            <input
              type="number"
              value={simAmount}
              onChange={(e) => setSimAmount(e.target.value)}
              className="input-field py-1.5 text-xs font-mono"
            />
          </div>

          <div>
            <label className="block text-[11px] font-semibold text-slate-600 mb-1">Skema PPN</label>
            <select
              value={simMode}
              onChange={(e) => setSimMode(e.target.value)}
              className="input-field py-1.5 text-xs"
            >
              <option value="include">Include PPN (DPP = Total / 1.11)</option>
              <option value="exclude">Exclude PPN (PPN = DPP * 11%)</option>
            </select>
          </div>

          <button onClick={handleSimulateTax} className="btn-secondary py-2 text-xs font-semibold">
            Hitung Rincian
          </button>

          {simResult && (
            <div className="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs flex justify-between items-center">
              <div>
                <span className="text-[10px] text-slate-500 block">DPP: Rp {Number(simResult.dpp).toLocaleString('id-ID')}</span>
                <span className="text-[10px] text-red-600 font-bold block">PPN 11%: Rp {Number(simResult.ppn).toLocaleString('id-ID')}</span>
              </div>
              <div className="text-right">
                <span className="text-[10px] text-slate-500 block">Total:</span>
                <span className="font-extrabold text-emerald-600 text-xs">Rp {Number(simResult.total).toLocaleString('id-ID')}</span>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Invoices Table */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="p-4 border-b border-slate-100 flex items-center justify-between">
          <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <FileText className="w-4 h-4 text-red-600" />
            <span>Daftar Faktur & Tagihan Pelanggan</span>
          </h3>
          <button
            onClick={fetchInvoices}
            disabled={loading}
            className="p-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200"
          >
            <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} />
          </button>
        </div>

        <div className="table-container overflow-x-auto">
          <table className="custom-table w-full text-xs">
            <thead>
              <tr>
                <th className="p-3 text-left">No. Invoice</th>
                <th className="p-3 text-left">Pelanggan</th>
                <th className="p-3 text-left">Periode</th>
                <th className="p-3 text-right">DPP (Rp)</th>
                <th className="p-3 text-right">PPN 11%</th>
                <th className="p-3 text-right">Total (Rp)</th>
                <th className="p-3 text-center">Status</th>
                <th className="p-3 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {invoices.map((inv) => (
                <tr key={inv.id} className="border-b border-slate-100 hover:bg-slate-50">
                  <td className="p-3 font-mono font-bold text-blue-600">{inv.invoice_no}</td>
                  <td className="p-3">
                    <span className="font-bold text-slate-800 block">{inv.customer?.name || 'Pelanggan'}</span>
                    <span className="text-[10px] text-slate-400 font-mono">{inv.customer?.cid || '-'}</span>
                  </td>
                  <td className="p-3 text-slate-600">{inv.billing_period}</td>
                  <td className="p-3 text-right font-mono">{Number(inv.dpp_amount || 0).toLocaleString('id-ID')}</td>
                  <td className="p-3 text-right font-mono text-red-600">{Number(inv.ppn_amount || 0).toLocaleString('id-ID')}</td>
                  <td className="p-3 text-right font-mono font-bold text-slate-900">{Number(inv.total_amount || 0).toLocaleString('id-ID')}</td>
                  <td className="p-3 text-center">
                    <span className={`px-2 py-0.5 rounded-full text-[10px] font-bold ${
                      inv.status === 'PAID' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-red-50 text-red-600 border border-red-200'
                    }`}>
                      {inv.status}
                    </span>
                  </td>
                  <td className="p-3 text-center">
                    <div className="flex items-center justify-center gap-1.5">
                      {inv.status !== 'PAID' && (
                        <button
                          onClick={() => {
                            setSelectedInvoice(inv);
                            setPayModalOpen(true);
                          }}
                          className="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold"
                        >
                          Bayar
                        </button>
                      )}
                      <button
                        onClick={() => handleSendReminder(inv)}
                        className="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded text-[10px]"
                        title="Kirim Notifikasi WA"
                      >
                        <i className="fa-brands fa-whatsapp text-emerald-600"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Pay Modal */}
      {payModalOpen && selectedInvoice && (
        <div className="fixed inset-0 bg-slate-950/65 z-[9999] flex items-center justify-center p-4 backdrop-blur-md">
          <div className="bg-white rounded-2xl p-6 w-full max-w-md space-y-4 border border-slate-100 shadow-2xl">
            <h3 className="font-bold text-slate-900 text-sm">Pelunasan Tagihan: {selectedInvoice.invoice_no}</h3>
            <p className="text-xs text-slate-500">
              Pelanggan: <strong className="text-slate-800">{selectedInvoice.customer?.name}</strong> | Total: <strong className="text-emerald-600">Rp {Number(selectedInvoice.total_amount).toLocaleString('id-ID')}</strong>
            </p>
            <form onSubmit={handlePay} className="space-y-3 text-xs">
              <div>
                <label className="font-semibold text-slate-700 block mb-1">Metode Pembayaran</label>
                <select
                  value={paymentMethod}
                  onChange={(e) => setPaymentMethod(e.target.value)}
                  className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium"
                >
                  <option>BCA Virtual Account</option>
                  <option>Mandiri Virtual Account</option>
                  <option>QRIS Dinamis</option>
                  <option>Tunai Kasir Loket</option>
                </select>
              </div>
              <div className="flex justify-end gap-2 pt-2">
                <button
                  type="button"
                  onClick={() => setPayModalOpen(false)}
                  className="btn-secondary py-1.5 px-3"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  disabled={actionLoading}
                  className="btn-primary py-1.5 px-4"
                >
                  {actionLoading ? 'Memproses...' : 'Konfirmasi Lunas'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
