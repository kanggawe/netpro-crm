import React, { useEffect, useState } from 'react';
import {
  TrendingUp,
  AlertTriangle,
  Zap,
  CheckCircle,
  Users,
  Activity,
  ArrowRight,
  Power,
  RefreshCw,
  Clock,
  Shield,
  CreditCard,
  Radio,
  FileText,
  DollarSign,
  Send,
  Check,
  Server,
  Wifi,
  Star,
  MapPin,
  Calendar,
  UserCheck,
  Flame,
  Award,
} from 'lucide-react';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Tooltip,
  Legend,
} from 'chart.js';
import { Line } from 'react-chartjs-2';
import { api } from '../api/client';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Tooltip,
  Legend
);

export default function Dashboard({ onNavigate, showToast, currentRoute = 'dashboard-utama' }) {
  const [telemetry, setTelemetry] = useState(null);
  const [invoices, setInvoices] = useState([]);
  const [customers, setCustomers] = useState([]);
  const [sessions, setSessions] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchDashboardData = async () => {
    setLoading(true);
    try {
      const [telRes, custRes, invRes, sessRes] = await Promise.all([
        api.get('/radius/telemetry').catch(() => ({ data: null })),
        api.get('/customers?per_page=50').catch(() => ({ data: { data: [] } })),
        api.get('/invoices?per_page=50').catch(() => ({ data: { data: [] } })),
        api.get('/radius/active-sessions').catch(() => ({ data: [] })),
      ]);

      setTelemetry(telRes.data);
      setCustomers(custRes.data?.data || []);
      setInvoices(invRes.data?.data || []);
      setSessions(sessRes.data || []);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchDashboardData();
  }, []);

  const totalActive = customers.filter((c) => c.status === 'active').length || 1245;
  const totalIsolated = customers.filter((c) => c.status === 'isolated').length || 65;
  const totalIncome = invoices.reduce((acc, curr) => acc + (curr.status === 'paid' ? Number(curr.total_amount) : 0), 0) || 128450000;

  const handleDisconnect = async (username) => {
    try {
      await api.post('/radius/disconnect', { username });
      showToast({
        type: 'success',
        title: 'CoA Disconnect Berhasil',
        message: `Sesi PPPoE ${username} berhasil diputus via RFC 3576.`,
      });
      fetchDashboardData();
    } catch (err) {
      showToast({ type: 'error', message: err.message || 'Gagal memutuskan sesi.' });
    }
  };

  // Financial Chart Data (Crimson Red Theme)
  const chartData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
    datasets: [
      {
        type: 'bar',
        label: 'Realisasi Target Pembayaran (Juta Rp)',
        data: [85, 92, 105, 112, 120, 128],
        backgroundColor: '#dc2626',
        borderRadius: 8,
      },
      {
        type: 'line',
        label: 'Target Bulanan',
        data: [90, 95, 100, 110, 115, 130],
        borderColor: '#7f1d1d',
        borderWidth: 2,
        tension: 0.3,
        pointBackgroundColor: '#b91c1c',
      },
    ],
  };

  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'top',
        labels: {
          font: { family: 'Inter', size: 11 },
          color: '#64748b',
        },
      },
    },
    scales: {
      y: {
        grid: { color: '#f1f5f9' },
        ticks: { color: '#94a3b8', font: { size: 10 } },
      },
      x: {
        grid: { display: false },
        ticks: { color: '#94a3b8', font: { size: 10 } },
      },
    },
  };

  // Subview 2: Pendapatan Bulan Berjalan (revenue.php)
  if (currentRoute === 'dashboard-revenue') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Dashboard Pendapatan, Billing & Keuangan ISP</h2>
            <p className="text-xs text-slate-500">Statistik realisasi tagihan lunas, Monthly Recurring Revenue (MRR), dan target omset.</p>
          </div>
          <span className="badge badge-success text-xs font-bold px-3 py-1">
            Realisasi: 98.2% dari Target
          </span>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Monthly Recurring Revenue (MRR)</span>
            <h3 className="text-2xl font-extrabold text-slate-900 mt-1">Rp 128.450.000</h3>
            <span className="text-xs text-emerald-600 font-semibold mt-2 block">▲ +12.4% vs bulan lalu</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Tagihan Tertagih (Paid)</span>
            <h3 className="text-2xl font-extrabold text-emerald-600 mt-1">1.245 Invoice</h3>
            <span className="text-xs text-slate-400 mt-2 block">Rata-rata ARPU: Rp 215.000</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Piutang Berjalan (Unpaid)</span>
            <h3 className="text-2xl font-extrabold text-red-600 mt-1">Rp 16.750.000</h3>
            <span className="text-xs text-amber-600 font-semibold mt-2 block">65 Pelanggan nunggak</span>
          </div>
        </div>

        <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
          <h3 className="text-sm font-bold text-slate-900">Grafik Pertumbuhan Pendapatan Bulanan (2026)</h3>
          <div className="h-72">
            <Line data={chartData} options={chartOptions} />
          </div>
        </div>
      </div>
    );
  }

  // Subview 3: Tagihan Jatuh Tempo & Isolir (overdue.php)
  if (currentRoute === 'dashboard-overdue') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Monitoring Tagihan Jatuh Tempo & Kontrol Isolir</h2>
            <p className="text-xs text-slate-500">Daftar pelanggan nunggak melewati batas tanggal tagihan (due date) dan aksi isolir otomatis.</p>
          </div>
          <button
            onClick={() => showToast({ type: 'success', message: 'Notifikasi peringatan tagihan dikirim ke seluruh pelanggan nunggak via WhatsApp.' })}
            className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer"
          >
            <Send className="w-3.5 h-3.5" />
            <span>Blast Peringatan WhatsApp</span>
          </button>
        </div>

        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <table className="custom-table">
            <thead>
              <tr>
                <th>NO INVOICE</th>
                <th>NAMA PELANGGAN</th>
                <th>PAKET</th>
                <th>NOMINAL</th>
                <th>KETERLAMBATAN</th>
                <th className="text-right">AKSI ISOLIR</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td className="font-mono text-xs font-bold text-red-600">INV-2026-06041</td>
                <td>
                  <strong className="text-xs text-slate-900 block">PT Nusantara Jaya Abadi</strong>
                  <span className="text-[11px] text-slate-400">0812-9988-1122</span>
                </td>
                <td className="text-xs text-slate-700">Biz 100M</td>
                <td className="font-mono font-bold text-xs text-slate-900">Rp 499.500</td>
                <td><span className="text-xs font-bold text-red-600">Lewat 5 Hari</span></td>
                <td className="text-right">
                  <button onClick={() => showToast({ type: 'success', message: 'Akun PT Nusantara Jaya Abadi diisolir.' })} className="text-xs py-1 px-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 cursor-pointer">
                    Isolir Sesi
                  </button>
                </td>
              </tr>
              <tr>
                <td className="font-mono text-xs font-bold text-red-600">INV-2026-06055</td>
                <td>
                  <strong className="text-xs text-slate-900 block">Wahyu Hidayat</strong>
                  <span className="text-[11px] text-slate-400">0856-1122-3344</span>
                </td>
                <td className="text-xs text-slate-700">Home 50M</td>
                <td className="font-mono font-bold text-xs text-slate-900">Rp 277.500</td>
                <td><span className="text-xs font-bold text-red-600">Lewat 3 Hari</span></td>
                <td className="text-right">
                  <button onClick={() => showToast({ type: 'success', message: 'Akun Wahyu Hidayat diisolir.' })} className="text-xs py-1 px-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 cursor-pointer">
                    Isolir Sesi
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    );
  }

  // Subview 4: Dashboard Pelanggan & Retensi (customers.php)
  if (currentRoute === 'dashboard-customers') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Dashboard Pelanggan (Customer Lifecycle & Retention)</h2>
            <p className="text-xs text-slate-500">Distribusi paket FTTH, status isolir vs aktif, dan analisis retensi pelanggan.</p>
          </div>
          <span className="badge badge-online text-xs font-bold px-3 py-1">
            Churn Rate: 1.2% (Sangat Rendah)
          </span>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-4 gap-5">
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Total Pelanggan Terdaftar</span>
            <h3 className="text-2xl font-extrabold text-slate-900 mt-1">1.310 Pengguna</h3>
            <span className="text-xs text-emerald-600 font-semibold mt-2 block">▲ +35 Pasang Baru bln ini</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Pelanggan Aktif Dial-in</span>
            <h3 className="text-2xl font-extrabold text-emerald-600 mt-1">1.245 Aktif (95%)</h3>
            <span className="text-xs text-slate-400 mt-2 block">RADIUS Session Valid</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Pelanggan Terisolir</span>
            <h3 className="text-2xl font-extrabold text-red-600 mt-1">65 Isolir (5%)</h3>
            <span className="text-xs text-amber-600 font-semibold mt-2 block">Auto-Disconnect CoA</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Rasio Kepuasan (CSAT)</span>
            <h3 className="text-2xl font-extrabold text-yellow-500 mt-1">4.8 / 5.0 ⭐</h3>
            <span className="text-xs text-slate-400 mt-2 block">98% Pelanggan Puas</span>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <h3 className="text-sm font-bold text-slate-900">Distribusi Paket Pelanggan</h3>
            <div className="space-y-3 pt-2">
              <div>
                <div className="flex justify-between text-xs mb-1">
                  <span className="font-semibold text-slate-700">Home 20M (Rp 150rb)</span>
                  <span className="font-bold text-slate-900">540 User (41%)</span>
                </div>
                <div className="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                  <div className="bg-red-500 h-full rounded-full" style={{ width: '41%' }}></div>
                </div>
              </div>
              <div>
                <div className="flex justify-between text-xs mb-1">
                  <span className="font-semibold text-slate-700">Home 50M (Rp 250rb)</span>
                  <span className="font-bold text-slate-900">412 User (31%)</span>
                </div>
                <div className="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                  <div className="bg-amber-500 h-full rounded-full" style={{ width: '31%' }}></div>
                </div>
              </div>
              <div>
                <div className="flex justify-between text-xs mb-1">
                  <span className="font-semibold text-slate-700">Biz 100M (Rp 450rb)</span>
                  <span className="font-bold text-slate-900">180 User (14%)</span>
                </div>
                <div className="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                  <div className="bg-blue-500 h-full rounded-full" style={{ width: '14%' }}></div>
                </div>
              </div>
              <div>
                <div className="flex justify-between text-xs mb-1">
                  <span className="font-semibold text-slate-700">Dedicated 1:1 (Rp 1.2jt)</span>
                  <span className="font-bold text-slate-900">45 User (4%)</span>
                </div>
                <div className="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                  <div className="bg-purple-500 h-full rounded-full" style={{ width: '4%' }}></div>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <h3 className="text-sm font-bold text-slate-900">Rasio Perpanjangan Otomatis (Auto-Renew)</h3>
            <div className="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 text-center space-y-1">
              <span className="text-3xl font-extrabold text-emerald-700">93.8%</span>
              <p className="text-xs text-emerald-800 font-semibold">Tingkat Perpanjangan Tepat Waktu</p>
              <p className="text-[11px] text-emerald-600">Pelanggan membayar sebelum H-1 tanggal jatuh tempo due date.</p>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // Subview 5: Dashboard Tiket & SLA (tickets.php)
  if (currentRoute === 'dashboard-tickets') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Dashboard Tiket Gangguan & Helpdesk SLA</h2>
            <p className="text-xs text-slate-500">Monitoring eskalasi kendala teknis, kecepatan respon teknisi, dan kepatuhan SLA 99.8%.</p>
          </div>
          <span className="badge badge-success text-xs font-bold px-3 py-1">
            MTTR: 42 Menit (Optimal)
          </span>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Tiket Masuk Hari Ini</span>
            <h3 className="text-2xl font-extrabold text-slate-900 mt-1">12 Laporan</h3>
            <span className="text-xs text-slate-400 mt-2 block">Mayoritas: LOS Merah / Kabel Putus</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Tiket Selesai (Resolved)</span>
            <h3 className="text-2xl font-extrabold text-emerald-600 mt-1">10 Tiket (83%)</h3>
            <span className="text-xs text-emerald-600 font-semibold mt-2 block">Diselesaikan dalam &lt; 1 jam</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Tiket Open / Proses</span>
            <h3 className="text-2xl font-extrabold text-amber-600 mt-1">2 Tiket</h3>
            <span className="text-xs text-slate-400 mt-2 block">Tim Teknisi di Lapangan</span>
          </div>
        </div>
      </div>
    );
  }

  // Subview 6: Dashboard NOC & Jaringan (noc.php)
  if (currentRoute === 'dashboard-noc') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Dashboard NOC & Backbone Jaringan FTTx</h2>
            <p className="text-xs text-slate-500">Status OLT GPON, utilisasi BGP IP Transit 10 Gbps, dan pemantauan Core Router.</p>
          </div>
          <span className="badge badge-online text-xs font-bold px-3 py-1">
            Uptime Backbone: 99.98%
          </span>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Utilisasi IP Transit Upstream</span>
            <h3 className="text-2xl font-extrabold text-blue-600 mt-1">4.2 Gbps / 10 Gbps</h3>
            <span className="text-xs text-slate-400 mt-2 block">Kapasitas beban: 42%</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Status GPON OLT</span>
            <h3 className="text-2xl font-extrabold text-emerald-600 mt-1">12 OLT Online (100%)</h3>
            <span className="text-xs text-slate-400 mt-2 block">Suhu rata-rata: 38.5°C</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Total ODP Tiang Terpasang</span>
            <h3 className="text-2xl font-extrabold text-slate-900 mt-1">148 Titik ODP</h3>
            <span className="text-xs text-emerald-600 font-semibold mt-2 block">1.184 Kapasitas Port</span>
          </div>
        </div>
      </div>
    );
  }

  // Subview 7: Dashboard HR & Karyawan (hr.php)
  if (currentRoute === 'dashboard-hr') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Dashboard HRD, Presensi & Kinerja Teknisi</h2>
            <p className="text-xs text-slate-500">Statistik kehadiran karyawan harian, validasi GPS presensi, dan efisiensi tim lapangan.</p>
          </div>
          <span className="badge badge-success text-xs font-bold px-3 py-1">
            Presensi Hari Ini: 96%
          </span>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-4 gap-5">
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Total Staf & Pegawai</span>
            <h3 className="text-2xl font-extrabold text-slate-900 mt-1">28 Pegawai</h3>
            <span className="text-xs text-slate-400 mt-2 block">Teknisi, NOC, CS & Finance</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Hadir Tepat Waktu</span>
            <h3 className="text-2xl font-extrabold text-emerald-600 mt-1">26 Orang (93%)</h3>
            <span className="text-xs text-slate-400 mt-2 block">Radius GPS Terverifikasi</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Cuti & Izin</span>
            <h3 className="text-2xl font-extrabold text-amber-600 mt-1">2 Orang</h3>
            <span className="text-xs text-slate-400 mt-2 block">1 Cuti Tahunan, 1 Sakit</span>
          </div>
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span className="text-xs font-semibold text-slate-500 uppercase">Instalasi Baru Hari Ini</span>
            <h3 className="text-2xl font-extrabold text-red-600 mt-1">8 Titik Pasang</h3>
            <span className="text-xs text-emerald-600 font-semibold mt-2 block">Target 6 Titik Terlampaui</span>
          </div>
        </div>
      </div>
    );
  }

  // Default: Dashboard Utama (utama.php)
  return (
    <div className="space-y-6">
      {/* 4 Summary Stats Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        {/* Metric 1: Income */}
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
          <div className="space-y-1">
            <span className="text-xs font-semibold text-slate-500 block uppercase tracking-wider">
              Pendapatan Bulan Berjalan
            </span>
            <p className="text-2xl font-bold text-slate-900">
              Rp {Number(totalIncome).toLocaleString('id-ID')}
            </p>
            <span className="text-[11px] text-emerald-600 font-medium">
              ▲ +12.4% vs bulan lalu
            </span>
          </div>
          <div className="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl font-bold shadow-inner">
            💰
          </div>
        </div>

        {/* Metric 2: Active Clients */}
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
          <div className="space-y-1">
            <span className="text-xs font-semibold text-slate-500 block uppercase tracking-wider">
              Pelanggan Aktif
            </span>
            <p className="text-2xl font-bold text-slate-900">
              {totalActive.toLocaleString('id-ID')}{' '}
              <span className="text-xs text-slate-400 font-normal">
                / {(totalActive + totalIsolated).toLocaleString('id-ID')} total
              </span>
            </p>
            <span className="text-[11px] text-amber-600 font-medium">
              {totalIsolated} Akun ter-suspend / isolir
            </span>
          </div>
          <div className="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl font-bold shadow-inner">
            👥
          </div>
        </div>

        {/* Metric 3: Active Incident Tickets */}
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
          <div className="space-y-1">
            <span className="text-xs font-semibold text-slate-500 block uppercase tracking-wider">
              Tiket Gangguan Terbuka
            </span>
            <p className="text-2xl font-bold text-red-600">
              8 <span className="text-xs text-slate-400 font-normal">Aktif</span>
            </p>
            <span className="text-[11px] text-red-500 font-medium">
              3 Melebihi batas SLA
            </span>
          </div>
          <div className="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center text-xl font-bold shadow-inner">
            🚨
          </div>
        </div>

        {/* Metric 4: Device/Router Online */}
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
          <div className="space-y-1">
            <span className="text-xs font-semibold text-slate-500 block uppercase tracking-wider">
              Status Perangkat Utama
            </span>
            <p className="text-2xl font-bold text-emerald-600">
              98.4% <span className="text-xs text-slate-400 font-normal">SLA</span>
            </p>
            <span className="text-[11px] text-emerald-600 font-medium">
              12 OLT & 3 Radius Active
            </span>
          </div>
          <div className="w-12 h-12 bg-cyan-50 text-cyan-600 rounded-xl flex items-center justify-center text-xl font-bold shadow-inner">
            ⚡
          </div>
        </div>
      </div>

      {/* Visual Charts & NOC Activity Log Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Revenue Trends Widget */}
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm lg:col-span-2 space-y-4">
          <div className="flex items-center justify-between">
            <div>
              <h3 className="font-bold text-sm text-slate-900">
                Statistik Keuangan & Pendapatan
              </h3>
              <p className="text-[11px] text-slate-400">
                Arus pertumbuhan tagihan lunas vs piutang berjalan tahun 2026.
              </p>
            </div>
            <select className="bg-slate-50 border border-slate-200 text-slate-600 text-xs py-1 px-2.5 rounded-md focus:outline-none focus:ring-1 focus:ring-red-500">
              <option>6 Bulan Terakhir</option>
              <option>Tahun Berjalan</option>
            </select>
          </div>

          <div className="h-64 w-full">
            <Line data={chartData} options={chartOptions} />
          </div>
        </div>

        {/* Active OLT Port Outage & NOC Log */}
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-4 flex flex-col justify-between">
          <div>
            <h3 className="font-bold text-sm text-slate-900">
              Log Kejadian NOC Terbaru
            </h3>
            <p className="text-[11px] text-slate-400">
              Status telemetri link BGP, OLT, & sesi putus.
            </p>
          </div>

          <div className="space-y-3 my-2">
            <div className="flex items-start space-x-3 text-xs">
              <div className="w-2 h-2 rounded-full bg-emerald-500 mt-1 shrink-0"></div>
              <div>
                <p className="font-semibold text-slate-800">
                  OLT Cilegon-01 PON-4 Restored
                </p>
                <span className="text-[10px] text-slate-400">
                  12 menit yang lalu • Port 4 Normal
                </span>
              </div>
            </div>

            <div className="flex items-start space-x-3 text-xs">
              <div className="w-2 h-2 rounded-full bg-red-500 mt-1 shrink-0"></div>
              <div>
                <p className="font-semibold text-slate-800">
                  FO Cut Feeder ODC-02 Core 8
                </p>
                <span className="text-[10px] text-slate-400">
                  45 menit yang lalu • Tim restore meluncur
                </span>
              </div>
            </div>

            <div className="flex items-start space-x-3 text-xs">
              <div className="w-2 h-2 rounded-full bg-amber-500 mt-1 shrink-0"></div>
              <div>
                <p className="font-semibold text-slate-800">
                  Auto-Isolir 5 Pelanggan Overdue
                </p>
                <span className="text-[10px] text-slate-400">
                  2 jam yang lalu • Dynamic CoA UDP 3799
                </span>
              </div>
            </div>
          </div>

          <button
            onClick={() => onNavigate && onNavigate('noc-monitoring')}
            className="w-full py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold text-xs rounded-lg transition flex items-center justify-center space-x-1 cursor-pointer"
          >
            <span>Buka NOC Topology Map</span>
            <ArrowRight className="w-3.5 h-3.5" />
          </button>
        </div>
      </div>
    </div>
  );
}
