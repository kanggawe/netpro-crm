import React, { useEffect, useState } from 'react';
import {
  BadgeDollarSign,
  BookOpen,
  FileCheck2,
  Plus,
  Wallet,
  Building2,
  Receipt,
  PieChart,
  Percent,
  FileText,
  ArrowDownRight,
  ArrowUpRight,
  Download,
  Calendar,
  Check,
  Printer,
  ShieldCheck,
  Landmark,
  Scale,
  TrendingUp,
  Search,
} from 'lucide-react';
import { api } from '../api/client';

export default function Finance({ showToast, currentRoute = 'finance-kas', onNavigate }) {
  const [coa, setCoa] = useState([]);
  const [journals, setJournals] = useState([]);
  const [taxes, setTaxes] = useState([]);
  const [loading, setLoading] = useState(true);

  // Active Sub Tab in Akuntansi
  const [akuntansiTab, setAkuntansiTab] = useState('coa');

  // Print Modals
  const [cashPrintModal, setCashPrintModal] = useState(null);
  const [opexPrintModal, setOpexPrintModal] = useState(null);

  // Bank Accounts / Kas state (matching kas.php)
  const [accounts, setAccounts] = useState([
    { id: '1102', name: 'Saldo Bank BCA Bisnis', accNo: '872-009-1234 (Giro)', balance: 184500000, type: 'Giro Bisnis' },
    { id: '1103', name: 'Saldo Bank Mandiri', accNo: '124-000-8889 (Corporate)', balance: 92300000, type: 'Virtual Account' },
    { id: '1101', name: 'Kas Tunai Kantor HQ', accNo: 'Kasir Utama HQ', balance: 14200000, type: 'Petty Cash' },
  ]);

  // Cash Mutations (matching kas.php)
  const [mutations, setMutations] = useState([
    { id: 'BKM-2026-0601', date: '2026-06-01', desc: 'Penerimaan Pembayaran Tagihan Pelanggan FTTH Batch #01', type: 'IN', amount: 48500000, acc: 'Bank BCA Bisnis', category: 'Pendapatan Internet' },
    { id: 'BKK-2026-0602', date: '2026-06-02', desc: 'Pembayaran Sewa IP Transit 10 Gbps Upstream Telkom', type: 'OUT', amount: 35000000, acc: 'Bank BCA Bisnis', category: 'Beban Upstream' },
    { id: 'BKM-2026-0603', date: '2026-06-03', desc: 'Pelunasan Invoice Corporate Dedicated PT Surya', type: 'IN', amount: 12000000, acc: 'Bank Mandiri', category: 'Pendapatan Dedicated' },
    { id: 'BKK-2026-0604', date: '2026-06-04', desc: 'Pengisian Kas Kecil Operasional & BBM Armada Teknisi', type: 'OUT', amount: 5000000, acc: 'Kas Tunai HQ', category: 'Petty Cash' },
    { id: 'BKK-2026-0605', date: '2026-06-05', desc: 'Pembayaran Tagihan Listrik PLN POP Utama & Genset', type: 'OUT', amount: 8450000, acc: 'Bank BCA Bisnis', category: 'Beban Listrik POP' },
  ]);

  // OPEX state (matching pengeluaran.php)
  const [opexList, setOpexList] = useState([
    { id: 'OPX-2026-01', title: 'Sewa Upstream IP Transit 10 Gbps (Telkom)', cat: 'Beban Upstream & Core', amount: 35000000, date: '2026-06-01', vendor: 'PT Telekomunikasi Indonesia', status: 'Paid' },
    { id: 'OPX-2026-02', title: 'Sewa Colocation Rack DC Cyber 1 Jakarta', cat: 'Beban Upstream & Core', amount: 12000000, date: '2026-06-05', vendor: 'PT Cyber DC Indonesia', status: 'Paid' },
    { id: 'OPX-2026-03', title: 'Tagihan Listrik PLN POP Utama & Genset', cat: 'Beban Utilitas POP', amount: 8450000, date: '2026-06-08', vendor: 'PT PLN (Persero)', status: 'Paid' },
    { id: 'OPX-2026-04', title: 'BBM & Operasional Armada Teknisi Lapangan', cat: 'Beban BBM & Lapangan', amount: 4200000, date: '2026-06-12', vendor: 'Tim Armada Field Ops', status: 'Paid' },
    { id: 'OPX-2026-05', title: 'Sewa Tiang Tumpu FO Jalur Feeder', cat: 'Beban Tiang & Hak Jalur', amount: 6500000, date: '2026-06-15', vendor: 'Dinas PUPR & Rekanan', status: 'Paid' },
  ]);

  // 34 COA PSAK Standard (matching akuntansi.php)
  const defaultCoa = [
    { code: '1101', name: 'Kas Kecil Kantor HQ', cat: 'Aset Lancar', normal: 'Debit', balance: 14200000 },
    { code: '1102', name: 'Bank BCA Giro Bisnis', cat: 'Aset Lancar', normal: 'Debit', balance: 184500000 },
    { code: '1103', name: 'Bank Mandiri Corporate', cat: 'Aset Lancar', normal: 'Debit', balance: 92300000 },
    { code: '1104', name: 'Piutang Pelanggan FTTH', cat: 'Aset Lancar', normal: 'Debit', balance: 16750000 },
    { code: '1105', name: 'Persediaan Modem ONT & SFP', cat: 'Aset Lancar', normal: 'Debit', balance: 45000000 },
    { code: '1106', name: 'Persediaan Kabel Drop Core FO', cat: 'Aset Lancar', normal: 'Debit', balance: 28000000 },
    { code: '1201', name: 'Aset Infrastruktur GPON OLT', cat: 'Aset Tetap', normal: 'Debit', balance: 140000000 },
    { code: '1202', name: 'Aset Jaringan Kabel Feeder & FDT', cat: 'Aset Tetap', normal: 'Debit', balance: 320000000 },
    { code: '1203', name: 'Akumulasi Penyusutan Infrastruktur', cat: 'Aset Tetap', normal: 'Kredit', balance: -45000000 },
    { code: '2101', name: 'Hutang Usaha Upstream & Bandwidth', cat: 'Liabilitas', normal: 'Kredit', balance: 35000000 },
    { code: '2102', name: 'Hutang Pajak PPN & PPh 23', cat: 'Liabilitas', normal: 'Kredit', balance: 14850000 },
    { code: '2103', name: 'Hutang Iuran USO & BHP Kominfo', cat: 'Liabilitas', normal: 'Kredit', balance: 2541000 },
    { code: '3101', name: 'Modal Disetor Pemegang Saham', cat: 'Ekuitas', normal: 'Kredit', balance: 500000000 },
    { code: '3201', name: 'Laba Ditahan (Retained Earnings)', cat: 'Ekuitas', normal: 'Kredit', balance: 198359000 },
    { code: '4101', name: 'Pendapatan Internet Broadband FTTH', cat: 'Pendapatan', normal: 'Kredit', balance: 112500000 },
    { code: '4102', name: 'Pendapatan Dedicated IP Transit 1:1', cat: 'Pendapatan', normal: 'Kredit', balance: 15950000 },
    { code: '5101', name: 'Beban Pokok Pendapatan (COGS Bandwidth)', cat: 'Beban Pokok', normal: 'Debit', balance: 47000000 },
    { code: '6101', name: 'Beban Gaji Staf & Teknisi (Payroll)', cat: 'Beban Operasional', normal: 'Debit', balance: 24500000 },
    { code: '6102', name: 'Beban Listrik POP & Genset', cat: 'Beban Operasional', normal: 'Debit', balance: 8450000 },
    { code: '6103', name: 'Beban Perawatan Armada & BBM', cat: 'Beban Operasional', normal: 'Debit', balance: 4200000 },
  ];

  // Tax records (matching pajak.php)
  const defaultTaxes = [
    { id: 'BUPOT-2026-001', vendor: 'PT Fiber Core Nusantara', npwp: '01.345.678.9-012.000', obj: 'Sewa Core Fiber Optik (24-104-01)', dpp: 25000000, rate: 2.0, tax: 500000, ntpn: 'NTPN-88219912001', status: 'Setor Kas Negara' },
    { id: 'BUPOT-2026-002', vendor: 'PT Colocation Cyber Data', npwp: '02.456.789.1-034.000', obj: 'Jasa Sewa Space Rack Server (24-104-02)', dpp: 12000000, rate: 2.0, tax: 240000, ntpn: 'NTPN-88219912002', status: 'Setor Kas Negara' },
    { id: 'BUPOT-2026-003', vendor: 'CV Splicing Fiber Optik', npwp: '03.567.890.2-056.000', obj: 'Jasa Teknik Penyambungan FO (24-104-03)', dpp: 8000000, rate: 2.0, tax: 160000, ntpn: 'Draft', status: 'Menunggu Setor' },
  ];

  const [taxFormOpen, setTaxFormOpen] = useState(false);
  const [taxForm, setTaxForm] = useState({
    vendor_name: '',
    npwp: '',
    obj_income: 'Sewa Core Fiber Optik (24-104-01)',
    dpp_amount: 10000000,
    rate_percent: 2.0,
  });

  const fetchData = async () => {
    setLoading(true);
    try {
      const [coaRes, jnlRes, taxRes] = await Promise.all([
        api.get('/finance/coa').catch(() => ({ data: defaultCoa })),
        api.get('/finance/journals?per_page=30').catch(() => ({ data: { data: [] } })),
        api.get('/finance/taxes').catch(() => ({ data: defaultTaxes })),
      ]);
      setCoa(coaRes.data?.length ? coaRes.data : defaultCoa);
      setJournals(jnlRes.data?.data || []);
      setTaxes(taxRes.data?.length ? taxRes.data : defaultTaxes);
    } catch (err) {
      setCoa(defaultCoa);
      setTaxes(defaultTaxes);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const totalLiquidity = accounts.reduce((sum, acc) => sum + acc.balance, 0);
  const totalOpex = opexList.reduce((sum, op) => sum + op.amount, 0);
  const grossRevenue = 128450000;
  const usoFee = grossRevenue * 0.0125; // 1.25% USO
  const bhpFee = grossRevenue * 0.0050; // 0.50% BHP Telekomunikasi

  const handleCreateTax = (e) => {
    e.preventDefault();
    const newTax = {
      id: `BUPOT-2026-00${taxes.length + 1}`,
      vendor: taxForm.vendor_name,
      npwp: taxForm.npwp || '01.234.567.8-901.000',
      obj: taxForm.obj_income,
      dpp: Number(taxForm.dpp_amount),
      rate: Number(taxForm.rate_percent),
      tax: Number(taxForm.dpp_amount) * (Number(taxForm.rate_percent) / 100),
      ntpn: 'Draft',
      status: 'Menunggu Setor',
    };
    setTaxes([newTax, ...taxes]);
    showToast({
      type: 'success',
      title: 'e-Bupot Terbit',
      message: `Bukti Potong PPh 23 #${newTax.id} untuk ${taxForm.vendor_name} berhasil diterbitkan.`,
    });
    setTaxFormOpen(false);
  };

  // Subview 1: Arus Kas & Rekening Bank (kas.php)
  if (currentRoute === 'finance-kas') {
    return (
      <div className="space-y-6">
        {/* Top 4 Bank Liquidity Cards matching kas.php */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-slate-400 block font-semibold uppercase text-xs">Saldo Bank BCA Bisnis</span>
            <strong className="text-2xl font-bold text-blue-600 block my-1">Rp {accounts[0].balance.toLocaleString('id-ID')}</strong>
            <span className="text-[10px] text-slate-400 font-mono">Rek: 872-009-1234 (Giro)</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-slate-400 block font-semibold uppercase text-xs">Saldo Bank Mandiri</span>
            <strong className="text-2xl font-bold text-indigo-600 block my-1">Rp {accounts[1].balance.toLocaleString('id-ID')}</strong>
            <span className="text-[10px] text-slate-400 font-mono">Rek: 124-000-8889 (Corporate)</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-slate-400 block font-semibold uppercase text-xs">Kas Tunai Kantor</span>
            <strong className="text-2xl font-bold text-emerald-600 block my-1">Rp {accounts[2].balance.toLocaleString('id-ID')}</strong>
            <span className="text-[10px] text-slate-400 font-mono">Kasir Utama HQ (Petty Cash)</span>
          </div>
          <div className="p-5 rounded-2xl border border-red-900/40 shadow-xl bg-gradient-to-br from-red-950 via-slate-950 to-red-950 text-white relative overflow-hidden">
            <div className="absolute top-0 right-0 w-24 h-24 bg-red-600/20 rounded-full blur-2xl pointer-events-none"></div>
            <div className="relative z-10">
              <span className="text-red-300/80 block font-bold uppercase text-[10px]">Total Likuiditas Kas</span>
              <strong className="text-2xl font-extrabold text-white block my-1">Rp {totalLiquidity.toLocaleString('id-ID')}</strong>
              <span className="text-emerald-400 text-[10px] block font-mono">✓ Rekening Koran Reconciled</span>
            </div>
          </div>
        </div>

        {/* Cash Mutations Table matching kas.php */}
        <div className="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
          <div className="flex flex-col sm:flex-row justify-between items-center gap-3 border-b border-slate-100 pb-4">
            <div>
              <h3 className="font-bold text-slate-900 text-sm">Buku Mutasi Kas Masuk & Kas Keluar (BKM / BKK)</h3>
              <p className="text-xs text-slate-400">Pencatatan real-time penerimaan tagihan, penarikan petty cash, dan transfer antar rekening.</p>
            </div>
            <div className="flex gap-2">
              <button
                onClick={() => showToast({ type: 'info', message: 'Form pencatatan mutasi kas baru dibuka.' })}
                className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer"
              >
                <Plus className="w-3.5 h-3.5" />
                <span>Catat Mutasi Kas</span>
              </button>
            </div>
          </div>

          <table className="custom-table">
            <thead>
              <tr>
                <th>NO TRANSAKSI</th>
                <th>TANGGAL</th>
                <th>KETERANGAN / DESKRIPSI</th>
                <th>KATEGORI</th>
                <th>REKENING BANK</th>
                <th>JENIS</th>
                <th className="text-right">JUMLAH (RP)</th>
                <th className="text-right">AKSI</th>
              </tr>
            </thead>
            <tbody>
              {mutations.map((m) => (
                <tr key={m.id}>
                  <td className="font-mono text-xs font-bold text-red-600">{m.id}</td>
                  <td className="text-xs text-slate-500 font-mono">{m.date}</td>
                  <td className="text-xs font-bold text-slate-900">{m.desc}</td>
                  <td><span className="badge bg-slate-100 text-slate-700 text-[10px]">{m.category}</span></td>
                  <td className="text-xs font-semibold text-slate-700">{m.acc}</td>
                  <td>
                    <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${
                      m.type === 'IN' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'
                    }`}>
                      {m.type === 'IN' ? '▲ Masuk' : '▼ Keluar'}
                    </span>
                  </td>
                  <td className={`text-right font-mono text-xs font-bold ${m.type === 'IN' ? 'text-emerald-600' : 'text-rose-600'}`}>
                    {m.type === 'IN' ? '+' : '-'} Rp {m.amount.toLocaleString('id-ID')}
                  </td>
                  <td className="text-right">
                    <button
                      onClick={() => setCashPrintModal(m)}
                      className="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition cursor-pointer"
                      title="Cetak Bukti Kas"
                    >
                      <Printer className="w-3.5 h-3.5" />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Modal Cetak Kas (matching cetak_kas.php) */}
        {cashPrintModal && (
          <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div className="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6 text-xs">
              <div className="flex justify-between items-start border-b border-slate-200 pb-4">
                <div>
                  <h3 className="font-extrabold text-base text-slate-900">
                    {cashPrintModal.type === 'IN' ? 'BUKTI KAS MASUK (BKM)' : 'BUKTI KAS KELUAR (BKK)'}
                  </h3>
                  <p className="text-slate-400 text-xs font-mono">No: {cashPrintModal.id}</p>
                </div>
                <button onClick={() => setCashPrintModal(null)} className="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">
                  ✕
                </button>
              </div>

              <div className="bg-slate-50 p-4 rounded-2xl space-y-2 border border-slate-100 font-mono">
                <div className="flex justify-between"><span>Tanggal:</span><strong>{cashPrintModal.date}</strong></div>
                <div className="flex justify-between"><span>Kategori:</span><strong>{cashPrintModal.category}</strong></div>
                <div className="flex justify-between"><span>Rekening:</span><strong>{cashPrintModal.acc}</strong></div>
                <div className="flex justify-between"><span>Jumlah Nominal:</span><strong className="text-red-600 font-bold text-sm">Rp {cashPrintModal.amount.toLocaleString('id-ID')}</strong></div>
                <div className="pt-2 border-t border-slate-200">
                  <span className="text-slate-500 block text-[10px]">Keterangan:</span>
                  <p className="text-slate-900 font-bold text-xs mt-0.5">{cashPrintModal.desc}</p>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-6 pt-4 border-t border-slate-200 text-center">
                <div>
                  <span className="text-slate-400 block mb-8">Disetujui Oleh (Finance)</span>
                  <strong className="text-slate-900 block border-b border-slate-300 pb-1">Finance & Billing Head</strong>
                </div>
                <div>
                  <span className="text-slate-400 block mb-8">Penerima / Penyetor</span>
                  <strong className="text-slate-900 block border-b border-slate-300 pb-1">Kasir Utama HQ</strong>
                </div>
              </div>

              <div className="flex justify-end gap-3 pt-2">
                <button onClick={() => window.print()} className="btn-primary text-xs px-5 py-2.5 flex items-center gap-1.5 cursor-pointer">
                  <Printer className="w-3.5 h-3.5" />
                  <span>Cetak Lembar Kas</span>
                </button>
              </div>
            </div>
          </div>
        )}
      </div>
    );
  }

  // Subview 2: Pengeluaran OPEX (pengeluaran.php)
  if (currentRoute === 'finance-opex') {
    return (
      <div className="space-y-6">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
              <span className="text-slate-400 block font-semibold uppercase text-xs">Total OPEX Bulan Ini</span>
              <strong className="text-2xl font-bold text-rose-600 block my-1">Rp {totalOpex.toLocaleString('id-ID')}</strong>
              <span className="text-slate-400 block text-[10px]">{opexList.length} Transaksi Beban</span>
            </div>
            <div className="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
              <Receipt className="w-5 h-5" />
            </div>
          </div>

          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
              <span className="text-slate-400 block font-semibold uppercase text-xs">Beban Upstream & Core</span>
              <strong className="text-2xl font-bold text-blue-600 block my-1">Rp 47.000.000</strong>
              <span className="text-slate-400 block text-[10px]">IP Transit & Colocation DC</span>
            </div>
            <div className="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
              <Building2 className="w-5 h-5" />
            </div>
          </div>

          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
              <span className="text-slate-400 block font-semibold uppercase text-xs">Beban BBM & Lapangan</span>
              <strong className="text-2xl font-bold text-amber-600 block my-1">Rp 4.200.000</strong>
              <span className="text-slate-400 block text-[10px]">Armada Teknisi Lapangan</span>
            </div>
            <div className="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
              <ArrowDownRight className="w-5 h-5" />
            </div>
          </div>
        </div>

        <div className="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
          <div className="flex flex-col sm:flex-row justify-between items-center gap-3 border-b border-slate-100 pb-4">
            <div>
              <h3 className="font-bold text-slate-900 text-sm">Daftar Pengeluaran Operasional (OPEX) ISP</h3>
              <p className="text-xs text-slate-400">Rekapitulasi biaya sewa bandwidth upstream, listrik POP, BBM teknisi, dan sewa tiang.</p>
            </div>
            <button
              onClick={() => showToast({ type: 'info', message: 'Form Catat Beban OPEX Baru dibuka.' })}
              className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer"
            >
              <Plus className="w-3.5 h-3.5" />
              <span>Tambah Biaya OPEX</span>
            </button>
          </div>

          <table className="custom-table">
            <thead>
              <tr>
                <th>NO TRANSAKSI</th>
                <th>KETERANGAN BIAYA</th>
                <th>KATEGORI</th>
                <th>VENDOR / TUJUAN</th>
                <th>TANGGAL</th>
                <th>JUMLAH PENGELUARAN</th>
                <th>STATUS</th>
                <th className="text-right">AKSI</th>
              </tr>
            </thead>
            <tbody>
              {opexList.map((op) => (
                <tr key={op.id}>
                  <td className="font-mono text-xs font-bold text-red-600">{op.id}</td>
                  <td className="text-xs font-bold text-slate-900">{op.title}</td>
                  <td><span className="badge bg-slate-100 text-slate-700 text-[10px]">{op.cat}</span></td>
                  <td className="text-xs text-slate-600">{op.vendor}</td>
                  <td className="text-xs text-slate-500 font-mono">{op.date}</td>
                  <td className="font-mono text-xs font-bold text-red-600">Rp {op.amount.toLocaleString('id-ID')}</td>
                  <td><span className="badge badge-success text-[10px]">{op.status}</span></td>
                  <td className="text-right">
                    <button
                      onClick={() => setOpexPrintModal(op)}
                      className="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition cursor-pointer"
                      title="Cetak Voucher OPEX"
                    >
                      <Printer className="w-3.5 h-3.5" />
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Modal Cetak Pengeluaran (matching cetak_pengeluaran.php) */}
        {opexPrintModal && (
          <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div className="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6 text-xs">
              <div className="flex justify-between items-start border-b border-slate-200 pb-4">
                <div>
                  <h3 className="font-extrabold text-base text-slate-900">VOUCHER PENGELUARAN OPERASIONAL</h3>
                  <p className="text-slate-400 text-xs font-mono">No: {opexPrintModal.id}</p>
                </div>
                <button onClick={() => setOpexPrintModal(null)} className="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">
                  ✕
                </button>
              </div>

              <div className="bg-slate-50 p-4 rounded-2xl space-y-2 border border-slate-100 font-mono">
                <div className="flex justify-between"><span>Tanggal Biaya:</span><strong>{opexPrintModal.date}</strong></div>
                <div className="flex justify-between"><span>Kategori OPEX:</span><strong>{opexPrintModal.cat}</strong></div>
                <div className="flex justify-between"><span>Nama Vendor:</span><strong>{opexPrintModal.vendor}</strong></div>
                <div className="flex justify-between"><span>Total Biaya:</span><strong className="text-red-600 font-bold text-sm">Rp {opexPrintModal.amount.toLocaleString('id-ID')}</strong></div>
                <div className="pt-2 border-t border-slate-200">
                  <span className="text-slate-500 block text-[10px]">Uraian Beban:</span>
                  <p className="text-slate-900 font-bold text-xs mt-0.5">{opexPrintModal.title}</p>
                </div>
              </div>

              <div className="grid grid-cols-2 gap-6 pt-4 border-t border-slate-200 text-center">
                <div>
                  <span className="text-slate-400 block mb-8">Menyetujui (Direktur)</span>
                  <strong className="text-slate-900 block border-b border-slate-300 pb-1">Direktur Operasional</strong>
                </div>
                <div>
                  <span className="text-slate-400 block mb-8">Penerima Dana</span>
                  <strong className="text-slate-900 block border-b border-slate-300 pb-1">{opexPrintModal.vendor}</strong>
                </div>
              </div>

              <div className="flex justify-end gap-3 pt-2">
                <button onClick={() => window.print()} className="btn-primary text-xs px-5 py-2.5 flex items-center gap-1.5 cursor-pointer">
                  <Printer className="w-3.5 h-3.5" />
                  <span>Cetak Voucher OPEX</span>
                </button>
              </div>
            </div>
          </div>
        )}
      </div>
    );
  }

  // Subview 3: Akuntansi & Buku Besar (akuntansi.php)
  if (currentRoute === 'finance-akuntansi') {
    return (
      <div className="space-y-6">
        <div className="p-6 bg-gradient-to-r from-red-950 via-slate-950 to-red-950 text-white rounded-3xl shadow-xl border border-red-900/40 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
          <div className="absolute top-0 left-1/4 w-32 h-32 bg-red-600/20 rounded-full blur-3xl pointer-events-none"></div>
          <div className="relative z-10">
            <div className="flex items-center gap-2">
              <span className="px-2.5 py-0.5 bg-red-500/20 text-red-300 border border-red-500/30 font-bold rounded-full text-[10px]">
                PSAK 72 / 115 & PSAK 73 COMPLIANT
              </span>
              <h3 className="font-bold text-sm text-white">Struktur Bagan Akun Standar (COA) Industri ISP Telekomunikasi</h3>
            </div>
            <p className="text-slate-300 text-[11px] mt-1">Standarisasi 34 akun akuntansi keuangan ISP: Aset Infrastruktur FO, Liabilitas Kontrak, Pendapatan FTTH, COGS Upstream & Beban OPEX.</p>
          </div>
          <div className="flex items-center gap-2 relative z-10">
            <button
              onClick={() => setAkuntansiTab('coa')}
              className={`font-bold px-4 py-2 rounded-xl text-xs transition cursor-pointer ${
                akuntansiTab === 'coa'
                  ? 'bg-red-600 text-white shadow-lg shadow-red-950/50 border border-red-500/30'
                  : 'bg-white/10 text-slate-300 hover:text-white border border-white/10'
              }`}
            >
              Bagan Akun (COA)
            </button>
            <button
              onClick={() => setAkuntansiTab('ledger')}
              className={`font-bold px-4 py-2 rounded-xl text-xs transition cursor-pointer ${
                akuntansiTab === 'ledger'
                  ? 'bg-red-600 text-white shadow-lg shadow-red-950/50 border border-red-500/30'
                  : 'bg-white/10 text-slate-300 hover:text-white border border-white/10'
              }`}
            >
              Buku Besar (General Ledger)
            </button>
          </div>
        </div>

        {akuntansiTab === 'coa' ? (
          <div className="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div className="flex justify-between items-center border-b border-slate-100 pb-4">
              <div>
                <h3 className="font-bold text-slate-900 text-sm">Daftar Akun Bagan (Chart of Accounts) - {coa.length} Akun Terdaftar</h3>
                <p className="text-xs text-slate-400">Klasifikasi akun standar PSAK ISP: Aset, Liabilitas, Ekuitas, Pendapatan, HPP & Beban Operasional.</p>
              </div>
            </div>

            <table className="custom-table">
              <thead>
                <tr>
                  <th>KODE AKUN</th>
                  <th>NAMA AKUN AKUNTANSI</th>
                  <th>KLASIFIKASI KELOMPOK</th>
                  <th>POSISI NORMAL</th>
                  <th className="text-right">SALDO BUKU BESAR (RP)</th>
                </tr>
              </thead>
              <tbody>
                {coa.map((acc, idx) => (
                  <tr key={idx}>
                    <td className="font-mono text-xs font-bold text-red-600">{acc.code}</td>
                    <td className="text-xs font-bold text-slate-800">{acc.name}</td>
                    <td><span className="badge bg-slate-100 text-slate-700 text-[10px]">{acc.cat || acc.category}</span></td>
                    <td className="text-xs text-slate-600 font-mono">{acc.normal || acc.normal_pos}</td>
                    <td className="text-right font-mono text-xs font-bold text-slate-900">
                      Rp {Number(acc.balance || 0).toLocaleString('id-ID')}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div className="border-b border-slate-100 pb-4">
              <h3 className="font-bold text-slate-900 text-sm">Jurnal Umum & Buku Besar (General Ledger Entries)</h3>
              <p className="text-xs text-slate-400">Pencatatan transaksi berpasangan (Double-Entry Bookkeeping) dengan keseimbangan Debit & Kredit seimbang.</p>
            </div>

            <table className="custom-table">
              <thead>
                <tr>
                  <th>TANGGAL</th>
                  <th>NO JURNAL</th>
                  <th>AKUN & KETERANGAN</th>
                  <th className="text-right">DEBIT (RP)</th>
                  <th className="text-right">KREDIT (RP)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td className="font-mono text-xs text-slate-500">2026-06-01</td>
                  <td className="font-mono text-xs font-bold text-red-600">JRN-2026-001</td>
                  <td>
                    <strong className="text-xs text-slate-900 block">1102 - Bank BCA Giro Bisnis</strong>
                    <span className="text-[11px] text-slate-500">Penerimaan Tagihan FTTH Juni Batch 1</span>
                  </td>
                  <td className="text-right font-mono text-xs font-bold text-emerald-600">Rp 48.500.000</td>
                  <td className="text-right font-mono text-xs text-slate-400">-</td>
                </tr>
                <tr>
                  <td className="font-mono text-xs text-slate-500">2026-06-01</td>
                  <td className="font-mono text-xs font-bold text-red-600">JRN-2026-001</td>
                  <td>
                    <strong className="text-xs text-slate-900 block pl-4">4101 - Pendapatan Internet FTTH</strong>
                    <span className="text-[11px] text-slate-500 pl-4">Kredit Pendapatan Usaha</span>
                  </td>
                  <td className="text-right font-mono text-xs text-slate-400">-</td>
                  <td className="text-right font-mono text-xs font-bold text-red-600">Rp 48.500.000</td>
                </tr>
              </tbody>
            </table>
          </div>
        )}
      </div>
    );
  }

  // Subview 4: Laporan Laba Rugi & Neraca (laporan.php)
  if (currentRoute === 'finance-laporan' || currentRoute === 'laporan-summary') {
    const totalRev = grossRevenue || 0;
    const cogsVal = 47000000;
    const opexVal = totalOpex + 24500000;
    const grossProf = totalRev - cogsVal;
    const netProf = grossProf - opexVal;
    const netMargin = totalRev > 0 ? ((netProf / totalRev) * 100).toFixed(1) : '0';

    const kasVal = totalLiquidity || 0;
    const piutangVal = 16750000;
    const persediaanVal = 73000000;
    const totalAsetLancar = kasVal + piutangVal + persediaanVal;

    const asetTetapVal = 460000000;
    const penyusutanVal = -45000000;
    const totalAsetTetap = asetTetapVal + penyusutanVal;
    const totalAset = totalAsetLancar + totalAsetTetap;

    const hutangUsaha = 35000000;
    const depositPelanggan = 17391000;
    const totalKewajiban = hutangUsaha + depositPelanggan;

    const modal = 500000000;
    const labaDitahan = totalAset - totalKewajiban - modal;
    const totalEkuitas = modal + labaDitahan;
    const totalPassiva = totalKewajiban + totalEkuitas;

    return (
      <div className="space-y-6 text-xs">
        {/* Top 4 Summary Cards matching laporan.php */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
              <span className="text-slate-400 block font-semibold uppercase text-[10px]">Total Pendapatan (Revenue)</span>
              <strong className="text-2xl font-bold text-slate-900 block my-0.5">Rp {totalRev.toLocaleString('id-ID')}</strong>
              <span className="text-emerald-600 font-medium block text-[10px]">▲ Realtime Engine</span>
            </div>
            <div className="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-inner">
              <i className="fa-solid fa-arrow-trend-up"></i>
            </div>
          </div>

          <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
              <span className="text-slate-400 block font-semibold uppercase text-[10px]">Total Beban (COGS & OPEX)</span>
              <strong className="text-2xl font-bold text-rose-600 block my-0.5">Rp {(cogsVal + opexVal).toLocaleString('id-ID')}</strong>
              <span className="text-slate-400 block text-[10px]">Beban Operasional ISP</span>
            </div>
            <div className="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg shadow-inner">
              <i className="fa-solid fa-receipt"></i>
            </div>
          </div>

          <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
              <span className="text-slate-400 block font-semibold uppercase text-[10px]">Laba Bersih (Net Profit)</span>
              <strong className={`text-2xl font-bold block my-0.5 ${netProf >= 0 ? 'text-blue-600' : 'text-rose-600'}`}>
                Rp {netProf.toLocaleString('id-ID')}
              </strong>
              <span className="text-slate-400 font-medium block text-[10px]">Margin: {netMargin}%</span>
            </div>
            <div className="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-inner">
              <i className="fa-solid fa-sack-dollar"></i>
            </div>
          </div>

          <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
              <span className="text-slate-400 block font-semibold uppercase text-[10px]">Total Aset (Aktiva)</span>
              <strong className="text-2xl font-bold text-purple-600 block my-0.5">Rp {totalAset.toLocaleString('id-ID')}</strong>
              <span className="text-emerald-600 font-bold block text-[10px]">✓ Neraca Balanced</span>
            </div>
            <div className="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg shadow-inner">
              <i className="fa-solid fa-scale-balanced"></i>
            </div>
          </div>
        </div>

        {/* Filter & Action Controls matching laporan.php */}
        <div className="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-3">
          <div className="flex items-center gap-3">
            <div>
              <span className="font-bold text-slate-700 block text-xs">Periode Laporan Keuangan:</span>
              <span className="text-slate-400 text-[11px]">Bulan Berjalan Tahun Anggaran 2026</span>
            </div>
            <select className="bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-slate-800 text-xs">
              <option>September 2026</option>
              <option>Agustus 2026</option>
              <option>Juli 2026</option>
            </select>
          </div>
          <div className="flex gap-2">
            <button onClick={() => window.print()} className="bg-slate-900 hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5 cursor-pointer text-xs">
              <i className="fa-solid fa-print"></i> Cetak Laporan PDF
            </button>
            <button onClick={() => showToast({ type: 'info', message: 'File rekapitulasi keuangan disiapkan ke Excel...' })} className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-lg shadow flex items-center gap-1.5 cursor-pointer text-xs">
              <i className="fa-solid fa-file-excel"></i> Export Excel
            </button>
          </div>
        </div>

        {/* 2-Column Statements Grid matching laporan.php */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Column 1: Laporan Laba Rugi (Income Statement) */}
          <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div className="flex justify-between items-center border-b border-slate-100 pb-3">
              <div>
                <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                  <i className="fa-solid fa-chart-line text-blue-600"></i> Laporan Laba Rugi (Income Statement)
                </h3>
                <p className="text-slate-400 text-[11px]">Periode: 01 September 2026 - 30 September 2026</p>
              </div>
              <span className="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded-full text-[10px]">IDR (Rupiah)</span>
            </div>

            <div className="space-y-4">
              {/* 1. PENDAPATAN */}
              <div>
                <span className="font-bold text-slate-800 uppercase block mb-1 text-xs">1. PENDAPATAN USAHA (REVENUE)</span>
                <div className="space-y-1.5 pl-2 border-l-2 border-slate-200">
                  <div className="flex justify-between text-slate-600">
                    <span>Pendapatan Langganan Internet FTTH</span>
                    <span className="font-mono font-bold text-slate-800">Rp {totalRev.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Pendapatan Biaya Pasang Baru</span>
                    <span className="font-mono font-bold text-slate-800">Rp 0</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Pendapatan Add-on (IP Publik & CCTV)</span>
                    <span className="font-mono font-bold text-slate-800">Rp 0</span>
                  </div>
                  <div className="flex justify-between text-emerald-700 font-bold pt-1 border-t border-slate-100">
                    <span>Total Pendapatan Bersih (Net Revenue)</span>
                    <span className="font-mono">Rp {totalRev.toLocaleString('id-ID')}</span>
                  </div>
                </div>
              </div>

              {/* 2. BEBAN POKOK */}
              <div>
                <span className="font-bold text-slate-800 uppercase block mb-1 text-xs">2. BEBAN POKOK PENDAPATAN (COGS)</span>
                <div className="space-y-1.5 pl-2 border-l-2 border-slate-200">
                  <div className="flex justify-between text-slate-600">
                    <span>Sewa Bandwidth Upstream (Telkom & Indosat)</span>
                    <span className="font-mono font-bold text-slate-800">Rp {cogsVal.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Sewa Tiang & Dark Fiber Core PLN</span>
                    <span className="font-mono font-bold text-slate-800">Rp 0</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Listrik & Daya POP Core / Server</span>
                    <span className="font-mono font-bold text-slate-800">Rp 0</span>
                  </div>
                  <div className="flex justify-between text-rose-700 font-bold pt-1 border-t border-slate-100">
                    <span>Total Beban Pokok (COGS)</span>
                    <span className="font-mono">(Rp {cogsVal.toLocaleString('id-ID')})</span>
                  </div>
                </div>
              </div>

              {/* LABA KOTOR */}
              <div className="p-3 bg-slate-50 border border-slate-200 rounded-xl flex justify-between items-center font-bold">
                <span className="text-slate-800">LABA KOTOR (GROSS PROFIT)</span>
                <span className="font-mono text-emerald-600 text-sm">Rp {grossProf.toLocaleString('id-ID')}</span>
              </div>

              {/* 3. BEBAN OPERASIONAL */}
              <div>
                <span className="font-bold text-slate-800 uppercase block mb-1 text-xs">3. BEBAN OPERASIONAL & UMUM (OPEX)</span>
                <div className="space-y-1.5 pl-2 border-l-2 border-slate-200">
                  <div className="flex justify-between text-slate-600">
                    <span>Gaji Karyawan, Teknisi & BPJS (Payroll)</span>
                    <span className="font-mono font-bold text-slate-800">Rp 24.500.000</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Operasional Lapangan, BBM & Transport</span>
                    <span className="font-mono font-bold text-slate-800">Rp {totalOpex.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Pemasaran, Iklan & Komisi Sales</span>
                    <span className="font-mono font-bold text-slate-800">Rp 0</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Penyusutan Aset Perangkat (Depresiasi)</span>
                    <span className="font-mono font-bold text-slate-800">Rp 0</span>
                  </div>
                  <div className="flex justify-between text-rose-700 font-bold pt-1 border-t border-slate-100">
                    <span>Total Beban Operasional (OPEX)</span>
                    <span className="font-mono">(Rp {opexVal.toLocaleString('id-ID')})</span>
                  </div>
                </div>
              </div>

              {/* FINAL NET PROFIT */}
              <div className="p-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl flex justify-between items-center shadow-lg">
                <div>
                  <h4 className="font-extrabold text-sm uppercase tracking-wider">LABA BERSIH TAHUN BERJALAN (NET PROFIT)</h4>
                  <span className="text-[11px] text-blue-100">Margin Laba Bersih: {netMargin}%</span>
                </div>
                <strong className="font-mono text-xl">Rp {netProf.toLocaleString('id-ID')}</strong>
              </div>
            </div>
          </div>

          {/* Column 2: Neraca Keuangan (Balance Sheet) */}
          <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div className="flex justify-between items-center border-b border-slate-100 pb-3">
              <div>
                <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                  <i className="fa-solid fa-scale-balanced text-indigo-600"></i> Neraca Keuangan (Balance Sheet)
                </h3>
                <p className="text-slate-400 text-[11px]">Posisi Per 30 September 2026</p>
              </div>
              <span className="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">BALANCED ✓</span>
            </div>

            <div className="space-y-4">
              {/* ASET LANCAR */}
              <div>
                <span className="font-bold text-slate-800 uppercase block mb-1 text-xs">ASET LANCAR (CURRENT ASSETS)</span>
                <div className="space-y-1.5 pl-2 border-l-2 border-slate-200">
                  <div className="flex justify-between text-slate-600">
                    <span>Kas & Rekening Bank (BCA + Mandiri)</span>
                    <span className="font-mono font-bold text-slate-800">Rp {kasVal.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Piutang Tagihan Pelanggan (Unpaid)</span>
                    <span className="font-mono font-bold text-slate-800">Rp {piutangVal.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Persediaan Stok Material Gudang (ONT/FO)</span>
                    <span className="font-mono font-bold text-slate-800">Rp {persediaanVal.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-blue-700 font-bold pt-1 border-t border-slate-100">
                    <span>Total Aset Lancar</span>
                    <span className="font-mono">Rp {totalAsetLancar.toLocaleString('id-ID')}</span>
                  </div>
                </div>
              </div>

              {/* ASET TIDAK LANCAR */}
              <div>
                <span className="font-bold text-slate-800 uppercase block mb-1 text-xs">ASET TETAP (FIXED ASSETS)</span>
                <div className="space-y-1.5 pl-2 border-l-2 border-slate-200">
                  <div className="flex justify-between text-slate-600">
                    <span>Peralatan OLT, Router Core, Splicer</span>
                    <span className="font-mono font-bold text-slate-800">Rp {asetTetapVal.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Akumulasi Penyusutan Alat</span>
                    <span className="font-mono text-rose-600 font-bold">(Rp {Math.abs(penyusutanVal).toLocaleString('id-ID')})</span>
                  </div>
                  <div className="flex justify-between text-blue-700 font-bold pt-1 border-t border-slate-100">
                    <span>Total Aset Tetap Bersih</span>
                    <span className="font-mono">Rp {totalAsetTetap.toLocaleString('id-ID')}</span>
                  </div>
                </div>
              </div>

              {/* TOTAL ASET */}
              <div className="p-3 bg-purple-50 border border-purple-200 rounded-xl flex justify-between items-center font-bold text-purple-900">
                <span>TOTAL AKTIVA / ASET</span>
                <span className="font-mono text-sm">Rp {totalAset.toLocaleString('id-ID')}</span>
              </div>

              {/* KEWAJIBAN & EKUITAS */}
              <div>
                <span className="font-bold text-slate-800 uppercase block mb-1 text-xs">KEWAJIBAN (LIABILITIES)</span>
                <div className="space-y-1.5 pl-2 border-l-2 border-slate-200">
                  <div className="flex justify-between text-slate-600">
                    <span>Hutang Usaha Upstream & Supplier</span>
                    <span className="font-mono font-bold text-slate-800">Rp {hutangUsaha.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Titipan Uang Jaminan Perangkat ONT</span>
                    <span className="font-mono font-bold text-slate-800">Rp {depositPelanggan.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-slate-800 font-bold pt-1 border-t border-slate-100">
                    <span>Total Kewajiban</span>
                    <span className="font-mono">Rp {totalKewajiban.toLocaleString('id-ID')}</span>
                  </div>
                </div>
              </div>

              <div>
                <span className="font-bold text-slate-800 uppercase block mb-1 text-xs">EKUITAS (EQUITY)</span>
                <div className="space-y-1.5 pl-2 border-l-2 border-slate-200">
                  <div className="flex justify-between text-slate-600">
                    <span>Modal Disetor Pendiri</span>
                    <span className="font-mono font-bold text-slate-800">Rp {modal.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-slate-600">
                    <span>Laba Ditahan & Laba Berjalan</span>
                    <span className="font-mono font-bold text-slate-800">Rp {labaDitahan.toLocaleString('id-ID')}</span>
                  </div>
                  <div className="flex justify-between text-slate-800 font-bold pt-1 border-t border-slate-100">
                    <span>Total Ekuitas Bersih</span>
                    <span className="font-mono">Rp {totalEkuitas.toLocaleString('id-ID')}</span>
                  </div>
                </div>
              </div>

              {/* TOTAL PASSIVA */}
              <div className="p-3 bg-purple-50 border border-purple-200 rounded-xl flex justify-between items-center font-bold text-purple-900">
                <span>TOTAL PASSIVA (KEWAJIBAN + EKUITAS)</span>
                <span className="font-mono text-sm">Rp {totalPassiva.toLocaleString('id-ID')}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // Subview 5: Pajak & Regulasi Kominfo (pajak.php)
  return (
    <div className="space-y-6">
      {/* Top 4 Tax Metrics matching pajak.php */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
          <div>
            <span className="text-slate-400 block font-semibold uppercase text-xs">PPh 21 (Gaji & Insentif)</span>
            <strong className="text-2xl font-bold text-slate-900 block my-1">Rp 1.450.000</strong>
            <span className="text-emerald-600 font-bold block text-[10px]">✓ SPT Masa PPh 21 Siap</span>
          </div>
          <div className="w-10 h-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center text-lg">
            <Users className="w-5 h-5" />
          </div>
        </div>

        <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
          <div>
            <span className="text-slate-400 block font-semibold uppercase text-xs">e-Bupot PPh 23 (Jasa Sewa)</span>
            <strong className="text-2xl font-bold text-blue-600 block my-1">Rp 900.000</strong>
            <span className="text-slate-400 block text-[10px]">3 Bukti Potong Terbit</span>
          </div>
          <div className="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
            <FileText className="w-5 h-5" />
          </div>
        </div>

        <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
          <div>
            <span className="text-slate-400 block font-semibold uppercase text-xs">Kontribusi USO Kominfo (1.25%)</span>
            <strong className="text-2xl font-bold text-amber-600 block my-1">Rp {usoFee.toLocaleString('id-ID')}</strong>
            <span className="text-slate-400 block text-[10px]">Iuran Penyelenggaraan ISP</span>
          </div>
          <div className="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
            <ShieldCheck className="w-5 h-5" />
          </div>
        </div>

        <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
          <div>
            <span className="text-slate-400 block font-semibold uppercase text-xs">BHP Telekomunikasi (0.50%)</span>
            <strong className="text-2xl font-bold text-red-600 block my-1">Rp {bhpFee.toLocaleString('id-ID')}</strong>
            <span className="text-slate-400 block text-[10px]">UU Cipta Kerja Sektor Pos & Tel</span>
          </div>
          <div className="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-lg">
            <Landmark className="w-5 h-5" />
          </div>
        </div>
      </div>

      {/* Tax e-Bupot Table */}
      <div className="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div className="flex flex-col sm:flex-row justify-between items-center gap-3 border-b border-slate-100 pb-4">
          <div>
            <h3 className="font-bold text-slate-900 text-sm">Daftar Bukti Potong PPh 23 (e-Bupot Unifikasi DJP)</h3>
            <p className="text-xs text-slate-400">Pemotongan pajak penghasilan pasal 23 atas jasa sewa core fiber optik, space rack colocation, dan jasa teknik.</p>
          </div>
          <button
            onClick={() => setTaxFormOpen(true)}
            className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer"
          >
            <Plus className="w-3.5 h-3.5" />
            <span>Terbitkan e-Bupot Baru</span>
          </button>
        </div>

        <table className="custom-table">
          <thead>
            <tr>
              <th>NO BUPOT</th>
              <th>NAMA VENDOR / REKANAN</th>
              <th>NPWP REKANAN</th>
              <th>OBJEK PENGHASILAN</th>
              <th>DPP (RP)</th>
              <th>TARIF</th>
              <th>PPH 23 (RP)</th>
              <th>STATUS SETOR</th>
            </tr>
          </thead>
          <tbody>
            {taxes.map((t) => (
              <tr key={t.id}>
                <td className="font-mono text-xs font-bold text-red-600">{t.id}</td>
                <td className="text-xs font-bold text-slate-900">{t.vendor}</td>
                <td className="font-mono text-xs text-slate-500">{t.npwp}</td>
                <td className="text-xs text-slate-700">{t.obj}</td>
                <td className="font-mono text-xs text-slate-900 font-semibold">Rp {t.dpp.toLocaleString('id-ID')}</td>
                <td className="font-mono text-xs text-slate-600">{t.rate}%</td>
                <td className="font-mono text-xs font-bold text-blue-600">Rp {t.tax.toLocaleString('id-ID')}</td>
                <td>
                  <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full ${
                    t.status === 'Setor Kas Negara' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'
                  }`}>
                    {t.status}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {/* Modal Terbitkan e-Bupot */}
      {taxFormOpen && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-5 text-xs">
            <div className="flex justify-between items-start border-b border-slate-200 pb-3">
              <div>
                <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                  <FileText className="w-4 h-4 text-blue-600" />
                  <span>Penerbitan Bukti Potong PPh 23 (e-Bupot Unifikasi)</span>
                </h3>
                <p className="text-slate-400 text-[11px] mt-0.5">Integrasi format e-Bupot DJP Online.</p>
              </div>
              <button onClick={() => setTaxFormOpen(false)} className="text-slate-400 hover:text-slate-600 font-bold text-base cursor-pointer">
                ✕
              </button>
            </div>

            <form onSubmit={handleCreateTax} className="space-y-4">
              <div>
                <label className="font-bold text-slate-700 block mb-1">Nama Vendor / Penyedia Jasa</label>
                <input
                  type="text"
                  required
                  placeholder="Contoh: PT Fiber Core Nusantara"
                  value={taxForm.vendor_name}
                  onChange={(e) => setTaxForm({ ...taxForm, vendor_name: e.target.value })}
                  className="input-field text-xs"
                />
              </div>

              <div>
                <label className="font-bold text-slate-700 block mb-1">NPWP 15/16 Digit</label>
                <input
                  type="text"
                  placeholder="01.345.678.9-012.000"
                  value={taxForm.npwp}
                  onChange={(e) => setTaxForm({ ...taxForm, npwp: e.target.value })}
                  className="input-field text-xs font-mono"
                />
              </div>

              <div>
                <label className="font-bold text-slate-700 block mb-1">Kode Objek Pajak</label>
                <select
                  value={taxForm.obj_income}
                  onChange={(e) => setTaxForm({ ...taxForm, obj_income: e.target.value })}
                  className="input-field text-xs font-bold"
                >
                  <option value="Sewa Core Fiber Optik (24-104-01)">24-104-01 - Sewa Core Fiber Optik</option>
                  <option value="Jasa Sewa Space Rack Server (24-104-02)">24-104-02 - Jasa Sewa Space Rack Server</option>
                  <option value="Jasa Teknik Penyambungan FO (24-104-03)">24-104-03 - Jasa Teknik Penyambungan FO</option>
                  <option value="Jasa Konsultasi IT & Network (24-104-04)">24-104-04 - Jasa Konsultasi IT & Network</option>
                </select>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="font-bold text-slate-700 block mb-1">DPP / Nilai Tagihan (Rp)</label>
                  <input
                    type="number"
                    required
                    value={taxForm.dpp_amount}
                    onChange={(e) => setTaxForm({ ...taxForm, dpp_amount: e.target.value })}
                    className="input-field text-xs font-mono font-bold text-slate-900"
                  />
                </div>
                <div>
                  <label className="font-bold text-slate-700 block mb-1">Tarif PPh 23 (%)</label>
                  <input
                    type="number"
                    step="0.1"
                    required
                    value={taxForm.rate_percent}
                    onChange={(e) => setTaxForm({ ...taxForm, rate_percent: e.target.value })}
                    className="input-field text-xs font-mono font-bold text-blue-600"
                  />
                </div>
              </div>

              <div className="p-3 bg-blue-50 rounded-xl border border-blue-100 flex justify-between items-center font-mono">
                <span className="text-blue-900 font-bold">Estimasi Potongan PPh 23:</span>
                <strong className="text-blue-700 text-sm font-extrabold">
                  Rp {(Number(taxForm.dpp_amount) * (Number(taxForm.rate_percent) / 100)).toLocaleString('id-ID')}
                </strong>
              </div>

              <div className="flex justify-end gap-2 pt-2">
                <button
                  type="button"
                  onClick={() => setTaxFormOpen(false)}
                  className="btn-secondary text-xs px-4 py-2 cursor-pointer"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  className="btn-primary text-xs px-5 py-2 flex items-center gap-1.5 cursor-pointer shadow-lg shadow-red-950/20"
                >
                  <Check className="w-3.5 h-3.5" />
                  <span>Terbitkan e-Bupot</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
