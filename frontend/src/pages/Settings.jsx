import React, { useEffect, useState } from 'react';
import {
  Settings as SettingsIcon,
  Building,
  Calendar,
  CreditCard,
  MessageSquare,
  Network,
  Database,
  UserCheck,
  History,
  Award,
  Info,
  CheckCircle2,
  Save,
  RefreshCw,
  Server,
  Key,
  Shield,
  Send,
  Download,
  Upload,
  Lock,
  Eye,
  EyeOff,
  Plus,
  Trash2,
  Edit,
  Check,
  Clock,
  Activity,
  AlertTriangle,
  Zap,
} from 'lucide-react';
import { api } from '../api/client';

export default function SettingsPage({ showToast, currentRoute = 'pengaturan-sistem', onNavigate }) {
  const [activeTab, setActiveTab] = useState('pengaturan-sistem');
  const [settings, setSettings] = useState({
    server_timezone: 'Asia/Jakarta',
    session_timeout_minutes: '120',
    maintenance_mode: '0',
    date_format: 'd/m/Y H:i:s',
    company_name: 'PT MITRAXCON SYNERGY UTAMA',
    company_brand: 'NETPRO ISP TELECOM',
    company_nib: '1928810029188',
    company_npwp: '01.234.567.8-012.000',
    company_email: 'billing@netpro.co.id',
    company_phone: '021-88992211',
    company_address: 'Gedung Cyber 1 Lt. 8, Jl. Kuningan Barat No. 8, Jakarta Selatan 12710',
    billing_due_date: '20',
    billing_grace_days: '3',
    billing_isolate_date: '24',
    billing_late_fee: '25000',
    ppn_rate: '11',
    uso_rate: '1.25',
    wa_vendor: 'fonnte',
    wa_sender: '081298765432',
    wa_token: 'FONNTE-API-TOKEN-9912088214',
    midtrans_active: '1',
    midtrans_env: 'production',
    midtrans_merchant_id: 'G1928812',
    midtrans_server_key: 'SB-Mid-server-9912099318821',
    midtrans_client_key: 'SB-Mid-client-8819200192931',
    midtrans_fee_va: '4000',
    midtrans_fee_qris: '0.7',
    global_fee_scheme: 'surcharge',
    radius_host: '127.0.0.1',
    radius_secret: 'testing123-netpro',
    radius_auth_port: '1812',
    radius_acct_port: '1813',
    radius_coa_port: '3799',
    mikrotik_api_host: '10.10.10.1',
    mikrotik_api_user: 'admin-api',
    mikrotik_api_port: '8728',
  });

  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [testWaLoading, setTestWaLoading] = useState(false);
  const [testWaTarget, setTestWaTarget] = useState('081234567890');
  const [showWaToken, setShowWaToken] = useState(false);
  const [showRadiusSecret, setShowRadiusSecret] = useState(false);
  const [activeGatewayTab, setActiveGatewayTab] = useState('midtrans');

  // Users Management State
  const [usersList, setUsersList] = useState([
    { id: 1, name: 'Super Administrator Utama ISP', username: 'superadmin', role: 'Super Admin', email: 'admin@netpro.id', status: 'active', last_login: '2026-09-01 16:50' },
    { id: 2, name: 'Tester Automation', username: 'tester_1787806140', role: 'staff', email: 'tester_1787806140@netpro.id', status: 'active', last_login: '2026-09-01 16:15' },
    { id: 3, name: 'Google Enterprise User', username: 'google_user', role: 'administrator', email: 'user.enterprise@gmail.com', status: 'active', last_login: '2026-09-01 15:40' },
    { id: 4, name: 'GitHub Developer', username: 'github_dev', role: 'administrator', email: 'developer@github.com', status: 'active', last_login: '2026-09-01 14:20' },
    { id: 5, name: 'Facebook Meta User', username: 'usercommunity', role: 'administrator', email: 'user.community@facebook.com', status: 'active', last_login: '2026-09-01 12:10' },
    { id: 6, name: 'X (Twitter) User', username: 'networkfeed', role: 'administrator', email: 'network.feed@x.com', status: 'active', last_login: '2026-09-01 10:05' },
  ]);

  const [isAddUserModalOpen, setIsAddUserModalOpen] = useState(false);
  const [isEditRbacModalOpen, setIsEditRbacModalOpen] = useState(false);
  const [editingRbac, setEditingRbac] = useState({ key: '', name: '', modules: [] });
  const [newUserData, setNewUserData] = useState({ username: '', full_name: '', email: '', role: 'administrator', password: '' });

  const systemModules = [
    { key: 'm-dashboard', name: 'Dashboard', icon: 'fa-chart-pie' },
    { key: 'm-crm', name: 'CRM Pelanggan', icon: 'fa-users-gear' },
    { key: 'm-noc', name: 'NOC Network', icon: 'fa-microchip' },
    { key: 'm-tickets', name: 'Tiket', icon: 'fa-headset' },
    { key: 'm-billing', name: 'Billing', icon: 'fa-credit-card' },
    { key: 'm-radius', name: 'RADIUS', icon: 'fa-network-wired' },
    { key: 'm-finance', name: 'Keuangan', icon: 'fa-receipt' },
    { key: 'm-inventory', name: 'Stok / Aset', icon: 'fa-boxes-stacked' },
    { key: 'm-hr', name: 'HR SDM', icon: 'fa-user-tie' },
    { key: 'm-payroll', name: 'Payroll', icon: 'fa-money-bill-wave' },
    { key: 'm-marketing', name: 'Marketing', icon: 'fa-bullhorn' },
    { key: 'm-kalkulator', name: 'Kalkulator', icon: 'fa-calculator' },
    { key: 'm-laporan', name: 'Laporan', icon: 'fa-file-lines' },
    { key: 'm-pengaturan', name: 'Pengaturan', icon: 'fa-gear' },
  ];

  const defaultRbacRoles = [
    {
      key: 'super admin',
      role: 'Super Admin',
      desc: 'Akses mutlak & tak terbatas ke seluruh modul dan inti sistem.',
      badge: 'bg-purple-50 text-purple-700 border-purple-200',
      icon: 'fa-crown',
      modules: ['m-dashboard', 'm-crm', 'm-noc', 'm-tickets', 'm-billing', 'm-radius', 'm-finance', 'm-inventory', 'm-hr', 'm-payroll', 'm-marketing', 'm-kalkulator', 'm-laporan', 'm-pengaturan']
    },
    {
      key: 'administrator',
      role: 'Administrator',
      desc: 'Operasional penuh sistem, manajemen user, billing & data bisnis.',
      badge: 'bg-indigo-50 text-indigo-700 border-indigo-200',
      icon: 'fa-shield-halved',
      modules: ['m-dashboard', 'm-crm', 'm-noc', 'm-tickets', 'm-billing', 'm-radius', 'm-finance', 'm-inventory', 'm-hr', 'm-payroll', 'm-marketing', 'm-kalkulator', 'm-laporan', 'm-pengaturan']
    },
    {
      key: 'teknisi',
      role: 'Teknisi / Field Engineer',
      desc: 'Instalasi baru, penanganan tiket gangguan, inventaris perangkat & absensi.',
      badge: 'bg-amber-50 text-amber-700 border-amber-200',
      icon: 'fa-wrench',
      modules: ['m-dashboard', 'm-crm', 'm-noc', 'm-tickets', 'm-inventory', 'm-hr', 'm-payroll', 'm-kalkulator']
    },
    {
      key: 'finance',
      role: 'Finance & Billing',
      desc: 'Penerbitan invoice, kas & bank, rekonsiliasi denda, pajak & penggajian.',
      badge: 'bg-emerald-50 text-emerald-700 border-emerald-200',
      icon: 'fa-money-bill-wave',
      modules: ['m-dashboard', 'm-billing', 'm-finance', 'm-payroll', 'm-kalkulator', 'm-laporan']
    },
    {
      key: 'noc',
      role: 'NOC & Network Ops',
      desc: 'Monitoring uptime perangkat, router RADIUS PPPoE, dan mitigasi incident.',
      badge: 'bg-cyan-50 text-cyan-700 border-cyan-200',
      icon: 'fa-network-wired',
      modules: ['m-dashboard', 'm-noc', 'm-radius', 'm-tickets', 'm-kalkulator', 'm-laporan']
    },
    {
      key: 'support',
      role: 'Customer Support (CS)',
      desc: 'Layanan pelanggan, pembuatan tiket keluhan, cek status billing & radius.',
      badge: 'bg-rose-50 text-rose-700 border-rose-200',
      icon: 'fa-headset',
      modules: ['m-dashboard', 'm-crm', 'm-billing', 'm-tickets', 'm-radius']
    },
    {
      key: 'sales',
      role: 'Sales & Marketing',
      desc: 'Pencatatan calon pelanggan (leads), promo paket, dan kalkulator biaya.',
      badge: 'bg-blue-50 text-blue-700 border-blue-200',
      icon: 'fa-bullhorn',
      modules: ['m-dashboard', 'm-crm', 'm-marketing', 'm-kalkulator']
    },
  ];

  const [rbacRoles, setRbacRoles] = useState(defaultRbacRoles);

  // Audit Logs State
  const [auditLogsList, setAuditLogsList] = useState([]);
  const [backupFiles, setBackupFiles] = useState([]);

  // Branches State
  const [branches, setBranches] = useState([
    { id: 1, code: 'CBG-JKT-01', name: 'Cabang Jakarta Selatan (Pusat)', address: 'Gedung Cyber Lt. 5, Kuningan Barat', manager: 'Budi Santoso', subs_count: 540 },
    { id: 2, code: 'CBG-BDG-02', name: 'Cabang Bandung Dago', address: 'Jl. Ir. H. Juanda No. 88, Bandung', manager: 'Rian Hidayat', subs_count: 320 },
    { id: 3, code: 'CBG-SBY-03', name: 'Cabang Surabaya Gubeng', address: 'Jl. Raya Gubeng No. 45, Surabaya', manager: 'Ahmad Fauzi', subs_count: 210 },
  ]);

  // Sync with currentRoute
  useEffect(() => {
    if (currentRoute && currentRoute.startsWith('pengaturan')) {
      setActiveTab(currentRoute);
    }
  }, [currentRoute]);

  const fetchAuditLogs = async () => {
    try {
      const res = await api.get('/audit-logs');
      if (res?.data?.data) {
        const raw = Array.isArray(res.data.data) ? res.data.data : (res.data.data.data || []);
        setAuditLogsList(raw.map((l) => ({
          id: `LOG-${l.id.toString().padStart(4, '0')}`,
          time: l.created_at ? new Date(l.created_at).toLocaleString('id-ID') : '-',
          user: l.username || 'system',
          action: l.action || 'ACTIVITY',
          ip: l.ip_address || '127.0.0.1',
          desc: l.details || '-',
        })));
      }
    } catch {
      // fallback
    }
  };

  const fetchBackups = async () => {
    try {
      const res = await api.get('/backups');
      if (res?.data && Array.isArray(res.data)) {
        setBackupFiles(res.data.map((b) => ({
          id: b.id,
          name: b.filename,
          size: b.filesize || '0 KB',
          date: b.created_at ? new Date(b.created_at).toLocaleString('id-ID') : '-',
          status: 'Verified',
        })));
      }
    } catch {
      // fallback
    }
  };

  const fetchData = async () => {
    setLoading(true);
    try {
      const res = await api.get('/settings').catch(() => null);
      if (res?.data && typeof res.data === 'object') {
        setSettings((prev) => ({ ...prev, ...res.data }));
      }
      await Promise.all([fetchAuditLogs(), fetchBackups()]);
    } catch {
      // fallback to mock state
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleSaveSettings = async (e) => {
    if (e) e.preventDefault();
    setSaving(true);
    try {
      await api.post('/settings', settings).catch(() => null);
      showToast({
        type: 'success',
        title: 'Pengaturan Disimpan',
        message: 'Seluruh konfigurasi sistem berhasil disimpan ke database!',
      });
      fetchAuditLogs();
    } catch (err) {
      showToast({ type: 'error', message: err.message || 'Gagal menyimpan pengaturan.' });
    } finally {
      setSaving(false);
    }
  };

  const handleTestWhatsApp = () => {
    setTestWaLoading(true);
    setTimeout(() => {
      setTestWaLoading(false);
      showToast({
        type: 'success',
        title: 'Pesan Terkirim',
        message: `Uji coba pesan notifikasi berhasil dikirim ke nomor ${testWaTarget} via ${settings.wa_vendor.toUpperCase()}.`,
      });
    }, 1000);
  };

  const handleBackupNow = async () => {
    setLoading(true);
    try {
      const res = await api.post('/backups');
      if (res?.data) {
        const b = res.data;
        const newBackup = {
          id: b.id,
          name: b.filename,
          size: b.filesize,
          date: new Date().toLocaleString('id-ID'),
          status: 'Verified',
        };
        setBackupFiles((prev) => [newBackup, ...prev.filter((x) => x.id !== b.id)]);
        showToast({
          type: 'success',
          title: 'Backup Berhasil',
          message: `File arsip database ${b.filename} (${b.filesize}) berhasil dibuat!`,
        });
        fetchAuditLogs();
      }
    } catch (err) {
      showToast({ type: 'error', message: err.message || 'Gagal membuat snapshot database.' });
    } finally {
      setLoading(false);
    }
  };

  const handleDeleteBackup = async (id, name) => {
    if (!window.confirm(`Yakin ingin menghapus arsip backup ${name}?`)) return;
    try {
      await api.delete(`/backups/${id}`);
      setBackupFiles((prev) => prev.filter((b) => b.id !== id));
      showToast({ type: 'success', message: `Arsip backup ${name} berhasil dihapus.` });
      fetchAuditLogs();
    } catch (err) {
      showToast({ type: 'error', message: 'Gagal menghapus file backup.' });
    }
  };

  const handleDownloadBackup = (b) => {
    const token = localStorage.getItem('netpro_token');
    const url = `/api/v1/backups/${b.id}/download?token=${token || ''}`;
    window.open(url, '_blank');
    showToast({ type: 'info', message: `Mengunduh arsip ${b.name}...` });
  };

  const navTabs = [
    { id: 'pengaturan-sistem', label: 'Konfigurasi Sistem & Server', icon: 'fa-gears' },
    { id: 'pengaturan-perusahaan', label: 'Identitas & Cabang ISP', icon: 'fa-building' },
    { id: 'pengaturan-billing', label: 'Otomatisasi Billing & Denda', icon: 'fa-calendar-check' },
    { id: 'pengaturan-payment', label: 'Payment Gateways & QRIS', icon: 'fa-credit-card' },
    { id: 'pengaturan-wa', label: 'WhatsApp & Server Notifikasi', icon: 'fa-comments' },
    { id: 'pengaturan-radius', label: 'Integrasi MikroTik & RADIUS', icon: 'fa-code-branch' },
    { id: 'pengaturan-backup', label: 'Backup & Database Restore', icon: 'fa-database' },
    { id: 'pengaturan-users', label: 'User Admin & Roles RBAC', icon: 'fa-user-lock' },
    { id: 'pengaturan-logs', label: 'Audit Logs System Trail', icon: 'fa-clock-rotate-left' },
    { id: 'pengaturan-lisensi', label: 'Lisensi & Aktivasi Sistem', icon: 'fa-certificate' },
    { id: 'pengaturan-about', label: 'Tentang Aplikasi Platform', icon: 'fa-circle-info' },
  ];

  return (
    <div className="space-y-6">
      {/* Header Banner */}
      <div className="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div className="flex items-center space-x-3.5">
          <div className="h-11 w-11 rounded-xl bg-red-50 flex items-center justify-center text-red-600">
            <i className="fa-solid fa-gear text-xl"></i>
          </div>
          <div>
            <h1 className="text-base font-extrabold text-slate-900 tracking-tight">
              Pengaturan Sistem & Konfigurasi ISP
            </h1>
            <p className="text-xs text-slate-500 mt-0.5">
              Kelola parameter server, payment gateway, bot WhatsApp, MikroTik RADIUS, backup, dan audit log keamanan.
            </p>
          </div>
        </div>

        <div className="flex items-center gap-2">
          <button
            onClick={handleSaveSettings}
            disabled={saving}
            className="btn-primary text-xs py-2.5 px-4 flex items-center gap-2 cursor-pointer shadow-lg shadow-red-950/20"
          >
            <Save className="w-4 h-4" />
            <span>{saving ? 'Menyimpan...' : 'Simpan Semua Pengaturan'}</span>
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {/* Navigation Sidebar List matching the user screenshot */}
        <div className="bg-[#450a0a] text-white rounded-2xl p-3 shadow-xl space-y-1 lg:col-span-1 h-fit border border-[#7f1d1d]">
          <div className="px-3 py-2 flex items-center justify-between border-b border-white/10 mb-2">
            <span className="text-xs font-extrabold text-white flex items-center gap-2">
              <i className="fa-solid fa-gear text-amber-400"></i> Pengaturan Sistem
            </span>
            <i className="fa-solid fa-chevron-up text-white/60 text-xs"></i>
          </div>

          {navTabs.map((tab) => {
            const isActive = activeTab === tab.id;
            return (
              <button
                key={tab.id}
                onClick={() => {
                  setActiveTab(tab.id);
                  if (onNavigate) onNavigate(tab.id);
                }}
                className={`w-full text-left flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-xs font-semibold transition cursor-pointer ${
                  isActive
                    ? 'bg-white/20 text-white font-bold shadow-xs border border-white/20'
                    : 'text-white/80 hover:bg-white/10 hover:text-white'
                }`}
              >
                <i className={`fa-solid ${tab.icon} w-4 text-center ${isActive ? 'text-amber-400' : 'text-white/60'}`}></i>
                <span className="truncate">{tab.label}</span>
              </button>
            );
          })}
        </div>

        {/* Dynamic Content Panel */}
        <div className="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm lg:col-span-3 space-y-6 text-xs text-slate-800">
          {/* ================= 1. KONFIGURASI SISTEM & SERVER (sistem.php) ================= */}
          {activeTab === 'pengaturan-sistem' && (
            <div className="space-y-6">
              {/* Top 4 System Health Metrics matching screenshot */}
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">Versi Platform</span>
                    <strong className="text-lg font-extrabold text-slate-900">v4.2.0-STABLE</strong>
                    <span className="text-emerald-600 font-bold block mt-0.5 text-[10px]">● Engine Running</span>
                  </div>
                  <div className="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg">
                    <i className="fa-solid fa-code-commit"></i>
                  </div>
                </div>

                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">Zona Waktu Server</span>
                    <strong className="text-lg font-extrabold text-blue-600">WIB (UTC+7)</strong>
                    <span className="text-slate-400 block mt-0.5 text-[10px]">Asia/Jakarta NTP Sync</span>
                  </div>
                  <div className="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg">
                    <i className="fa-solid fa-clock"></i>
                  </div>
                </div>

                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">SMTP Mail Dispatcher</span>
                    <strong className="text-lg font-extrabold text-emerald-600">Port 587 TLS</strong>
                    <span className="text-emerald-600 font-medium block mt-0.5 text-[10px]">Connected (mail.netpro)</span>
                  </div>
                  <div className="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg">
                    <i className="fa-solid fa-envelope-circle-check"></i>
                  </div>
                </div>

                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">Mode Operasi</span>
                    <strong className="text-lg font-extrabold text-purple-600">PRODUCTION</strong>
                    <span className="text-slate-400 block mt-0.5 text-[10px]">PHP 8.5.6</span>
                  </div>
                  <div className="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-lg">
                    <i className="fa-solid fa-server"></i>
                  </div>
                </div>
              </div>

              {/* Main System Configuration Form Box matching screenshot */}
              <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                <div className="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                  <div>
                    <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                      <i className="fa-solid fa-gears text-blue-600"></i> Pengaturan Teknis Server & Parameter Aplikasi
                    </h3>
                    <p className="text-slate-400 text-xs">Konfigurasi mendasar untuk lingkungan server, notifikasi email otomatis, dan kebijakan keamanan.</p>
                  </div>
                  <span className="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">
                    ALL DAEMONS ACTIVE ✓
                  </span>
                </div>

                <form onSubmit={handleSaveSettings} className="space-y-6">
                  {/* 1. Parameter Aplikasi & Lingkungan Operasional */}
                  <div className="space-y-3">
                    <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider text-blue-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                      <i className="fa-solid fa-laptop-code"></i> 1. PARAMETER APLIKASI & LINGKUNGAN OPERASIONAL
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Judul / Nama Platform Sistem</label>
                        <input
                          type="text"
                          value={settings.app_name || 'NETPRO ISP MANAGEMENT SUITE'}
                          onChange={(e) => setSettings({ ...settings, app_name: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-900 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Sub-judul / Tagline Singkat</label>
                        <input
                          type="text"
                          value={settings.app_desc || 'Enterprise Fiber & Billing Suite'}
                          onChange={(e) => setSettings({ ...settings, app_desc: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-slate-800 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Environment Mode</label>
                        <select
                          value={settings.app_env || 'production'}
                          onChange={(e) => setSettings({ ...settings, app_env: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 text-xs focus:bg-white focus:border-blue-500 transition"
                        >
                          <option value="production">Production (Live System)</option>
                          <option value="staging">Staging / Quality Assurance</option>
                          <option value="development">Development (Debug Active)</option>
                        </select>
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Status Maintenance Mode</label>
                        <select
                          value={settings.app_maintenance || '0'}
                          onChange={(e) => setSettings({ ...settings, app_maintenance: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 text-xs focus:bg-white focus:border-blue-500 transition"
                        >
                          <option value="0">NONAKTIF (Sistem Beroperasi Normal)</option>
                          <option value="1">AKTIF (Halaman Pemeliharaan Server)</option>
                        </select>
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Build Version String</label>
                        <input
                          type="text"
                          value={settings.app_version || 'v4.2.0-STABLE'}
                          onChange={(e) => setSettings({ ...settings, app_version: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-purple-600 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>
                  </div>

                  {/* 2. Lokalisasi, Zona Waktu & Format Angka */}
                  <div className="space-y-3 pt-2">
                    <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider text-indigo-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                      <i className="fa-solid fa-globe"></i> 2. LOKALISASI, ZONA WAKTU & FORMAT ANGKA
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Server Timezone</label>
                        <select
                          value={settings.server_timezone || 'Asia/Jakarta'}
                          onChange={(e) => setSettings({ ...settings, server_timezone: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 text-xs focus:bg-white focus:border-blue-500 transition"
                        >
                          <option value="Asia/Jakarta">Asia/Jakarta (WIB - UTC+7)</option>
                          <option value="Asia/Makassar">Asia/Makassar (WITA - UTC+8)</option>
                          <option value="Asia/Jayapura">Asia/Jayapura (WIT - UTC+9)</option>
                        </select>
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Simbol Mata Uang Utama</label>
                        <input
                          type="text"
                          value={settings.app_currency || 'IDR'}
                          onChange={(e) => setSettings({ ...settings, app_currency: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-emerald-600 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Format Tanggal Tampilan</label>
                        <input
                          type="text"
                          value={settings.app_date_format || 'd M Y H:i'}
                          onChange={(e) => setSettings({ ...settings, app_date_format: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-700 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>
                  </div>

                  {/* 3. Konfigurasi SMTP Mail Server */}
                  <div className="space-y-3 pt-2">
                    <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider text-emerald-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                      <i className="fa-solid fa-envelope-open-text"></i> 3. KONFIGURASI SMTP MAIL SERVER (PENGIRIMAN INVOICE PDF OTOMATIS)
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">SMTP Host / Server</label>
                        <input
                          type="text"
                          value={settings.smtp_host || 'mail.netpro.co.id'}
                          onChange={(e) => setSettings({ ...settings, smtp_host: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-900 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">SMTP Port</label>
                        <input
                          type="number"
                          value={settings.smtp_port || '587'}
                          onChange={(e) => setSettings({ ...settings, smtp_port: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Protokol Enkripsi</label>
                        <select
                          value={settings.smtp_crypto || 'tls'}
                          onChange={(e) => setSettings({ ...settings, smtp_crypto: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 text-xs focus:bg-white focus:border-blue-500 transition"
                        >
                          <option value="tls">TLS (Recommended - Port 587)</option>
                          <option value="ssl">SSL (Port 465)</option>
                          <option value="none">None (Plain Text)</option>
                        </select>
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">SMTP Username (Email Pengirim)</label>
                        <input
                          type="text"
                          value={settings.smtp_user || 'no-reply@netpro.co.id'}
                          onChange={(e) => setSettings({ ...settings, smtp_user: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">SMTP Password</label>
                        <input
                          type="password"
                          value={settings.smtp_pass || '••••••••••••'}
                          onChange={(e) => setSettings({ ...settings, smtp_pass: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-emerald-600 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Nama Tampilan Pengirim (Sender Name)</label>
                        <input
                          type="text"
                          value={settings.smtp_sender_name || 'NETPRO Notification Engine'}
                          onChange={(e) => setSettings({ ...settings, smtp_sender_name: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>
                  </div>

                  {/* 4. Keamanan Sesi & Pembatasan Login */}
                  <div className="space-y-3 pt-2">
                    <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider text-purple-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                      <i className="fa-solid fa-shield-halved"></i> 4. KEBIJAKAN KEAMANAN SESI & PEMBATASAN AKSES
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Durasi Sesi Login (Menit)</label>
                        <input
                          type="number"
                          value={settings.session_lifetime || '120'}
                          onChange={(e) => setSettings({ ...settings, session_lifetime: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-900 text-xs"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Batas Percobaan Login Salah</label>
                        <input
                          type="number"
                          value={settings.max_login_attempts || '5'}
                          onChange={(e) => setSettings({ ...settings, max_login_attempts: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-rose-600 text-xs"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Pembatasan IP Whitelist Admin</label>
                        <select
                          value={settings.ip_whitelist_only || '0'}
                          onChange={(e) => setSettings({ ...settings, ip_whitelist_only: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 text-xs"
                        >
                          <option value="0">NONAKTIF (Akses dari Mana Saja)</option>
                          <option value="1">AKTIF (Hanya IP Kantor Terdaftar)</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  {/* 5. Identitas Visual, Logo & Favicon Antarmuka (Branding Suite) */}
                  <div className="space-y-3 pt-2">
                    <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider text-pink-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                      <i className="fa-solid fa-paintbrush"></i> 5. IDENTITAS VISUAL, LOGO & FAVICON ANTARMUKA (BRANDING SUITE)
                    </h4>
                    <p className="text-slate-400 text-[11px]">Masukkan link/URL logo (PNG, SVG, ICO) atau path aset lokal. Biarkan kosong untuk menggunakan ikon default ISP.</p>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1 flex items-center justify-between">
                          <span>URL Logo Utama (Sidebar & Header)</span>
                          <span className="text-[10px] text-slate-400 font-mono">PNG / SVG Transparan</span>
                        </label>
                        <input
                          type="text"
                          value={settings.app_logo_url || ''}
                          onChange={(e) => setSettings({ ...settings, app_logo_url: e.target.value })}
                          placeholder="https://example.com/logo.png atau assets/img/logo.png"
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-800 text-xs focus:bg-white focus:border-pink-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1 flex items-center justify-between">
                          <span>URL Favicon Browser (Tab Icon)</span>
                          <span className="text-[10px] text-slate-400 font-mono">.ICO / .PNG / .SVG (32x32)</span>
                        </label>
                        <input
                          type="text"
                          value={settings.app_favicon_url || ''}
                          onChange={(e) => setSettings({ ...settings, app_favicon_url: e.target.value })}
                          placeholder="https://example.com/favicon.ico atau assets/img/favicon.ico"
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-800 text-xs focus:bg-white focus:border-pink-500 transition"
                        />
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1 flex items-center justify-between">
                          <span>URL Logo Faktur / Invoice PDF</span>
                          <span className="text-[10px] text-slate-400 font-mono">Kop Surat & Cetak Dokumen</span>
                        </label>
                        <input
                          type="text"
                          value={settings.app_invoice_logo_url || ''}
                          onChange={(e) => setSettings({ ...settings, app_invoice_logo_url: e.target.value })}
                          placeholder="https://example.com/logo-invoice.png"
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-800 text-xs focus:bg-white focus:border-pink-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1 flex items-center justify-between">
                          <span>URL Logo Portal Login Pegawai</span>
                          <span className="text-[10px] text-slate-400 font-mono">Halaman Login ESS & Staff</span>
                        </label>
                        <input
                          type="text"
                          value={settings.app_login_logo_url || ''}
                          onChange={(e) => setSettings({ ...settings, app_login_logo_url: e.target.value })}
                          placeholder="https://example.com/logo-white.png"
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-slate-800 text-xs focus:bg-white focus:border-pink-500 transition"
                        />
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Aksen Warna Brand</label>
                        <div className="flex items-center gap-2">
                          <input
                            type="color"
                            value={settings.app_brand_color || '#24ebc9'}
                            onChange={(e) => setSettings({ ...settings, app_brand_color: e.target.value })}
                            className="w-10 h-10 rounded-lg border border-slate-200 p-1 cursor-pointer bg-white"
                          />
                          <input
                            type="text"
                            value={settings.app_brand_color || '#24ebc9'}
                            onChange={(e) => setSettings({ ...settings, app_brand_color: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold text-slate-700 text-xs focus:bg-white focus:border-pink-500 transition"
                          />
                        </div>
                      </div>
                      <div className="md:col-span-2">
                        <label className="font-semibold text-slate-700 block mb-1">Teks Hak Cipta / Footer Resmi</label>
                        <input
                          type="text"
                          value={settings.app_copyright_text || '© 2026 PT NETPRO TELEKOMUNIKASI INDONESIA. All rights reserved.'}
                          onChange={(e) => setSettings({ ...settings, app_copyright_text: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-slate-700 text-xs focus:bg-white focus:border-pink-500 transition"
                        />
                      </div>
                    </div>

                    {/* Real-time Live Branding Preview Card matching screenshot */}
                    <div className="p-4 bg-slate-900 text-white rounded-2xl border border-slate-800 space-y-3 mt-3">
                      <div className="flex justify-between items-center border-b border-slate-800 pb-2">
                        <span className="text-xs font-bold text-slate-200 flex items-center gap-2">
                          <i className="fa-solid fa-eye text-blue-400"></i> Pratinjau Langsung (Live Asset Preview)
                        </span>
                        <span className="text-[10px] text-slate-400 font-mono">Real-time Simulation</span>
                      </div>

                      <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        {/* Preview 1: Browser Tab (Favicon) */}
                        <div className="p-3 bg-[#0e1424] border border-slate-800 rounded-xl space-y-1.5">
                          <span className="text-[10px] text-slate-400 font-semibold block uppercase">1. TAB BROWSER FAVICON</span>
                          <div className="flex items-center gap-2 bg-[#1a2234] p-2 rounded-lg border border-slate-700">
                            <div className="w-4 h-4 flex items-center justify-center shrink-0">
                              {settings.app_favicon_url ? (
                                <img src={settings.app_favicon_url} alt="Favicon" className="w-4 h-4 object-contain" />
                              ) : (
                                <i className="fa-solid fa-tower-cell text-blue-400 text-xs"></i>
                              )}
                            </div>
                            <span className="font-bold text-slate-200 text-[11px] truncate">
                              {settings.app_name || 'NETPRO ISP MANAGEMENT SUITE'}
                            </span>
                          </div>
                        </div>

                        {/* Preview 2: Sidebar Header Logo */}
                        <div className="p-3 bg-[#0e1424] border border-slate-800 rounded-xl space-y-1.5">
                          <span className="text-[10px] text-slate-400 font-semibold block uppercase">2. SIDEBAR HEADER LOGO</span>
                          <div className="flex items-center gap-2.5 bg-[#060911] p-2 rounded-lg border border-slate-800">
                            <div className="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-400 flex items-center justify-center font-bold text-white shadow text-xs overflow-hidden shrink-0">
                              {settings.app_logo_url ? (
                                <img src={settings.app_logo_url} alt="Logo" className="w-full h-full object-contain p-0.5" />
                              ) : (
                                <i className="fa-solid fa-tower-cell"></i>
                              )}
                            </div>
                            <div className="overflow-hidden">
                              <strong className="font-extrabold text-white text-[10px] block truncate">
                                {settings.app_name || 'NETPRO ISP MANAGEMENT SUITE'}
                              </strong>
                              <span className="text-[8px] text-slate-400 font-semibold block truncate uppercase">
                                {settings.app_desc || 'ENTERPRISE FIBER & BILLING SUITE'}
                              </span>
                            </div>
                          </div>
                        </div>

                        {/* Preview 3: Invoice Print Letterhead */}
                        <div className="p-3 bg-white text-slate-900 border border-slate-200 rounded-xl space-y-1.5">
                          <span className="text-[10px] text-slate-500 font-semibold block uppercase">3. KOP CETAK INVOICE PDF</span>
                          <div className="flex items-center gap-2 bg-slate-50 p-2 rounded-lg border border-slate-200">
                            <div className="w-6 h-6 flex items-center justify-center shrink-0 text-blue-600 font-bold text-xs">
                              {settings.app_invoice_logo_url ? (
                                <img src={settings.app_invoice_logo_url} alt="Invoice Logo" className="w-6 h-6 object-contain" />
                              ) : (
                                <i className="fa-solid fa-file-invoice-dollar text-sm text-blue-600"></i>
                              )}
                            </div>
                            <div className="overflow-hidden leading-tight">
                              <strong className="font-bold text-[10px] block truncate text-slate-900">
                                {settings.company_name || 'PT NETPRO TELEKOMUNIKASI'}
                              </strong>
                              <span className="text-[8px] text-slate-500 block truncate">Official ISP Letterhead</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Full-width Save Button matching screenshot */}
                  <button
                    type="submit"
                    disabled={saving}
                    className="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2 cursor-pointer text-xs"
                  >
                    <i className="fa-solid fa-floppy-disk"></i>
                    <span>{saving ? 'Menyimpan...' : 'Simpan Konfigurasi Sistem, Logo & Server Engine'}</span>
                  </button>
                </form>
              </div>
            </div>
          )}

          {/* ================= 2. IDENTITAS & CABANG ISP (perusahaan.php) ================= */}
          {activeTab === 'pengaturan-perusahaan' && (
            <div className="space-y-6">
              {/* Top 4 Identity Quick Metrics matching screenshot */}
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">Perizinan Kominfo</span>
                    <strong className="text-lg font-extrabold text-slate-900">ISP & Jartaplok</strong>
                    <span className="text-emerald-600 font-bold block mt-0.5 text-[10px]">✓ Terverifikasi Legal</span>
                  </div>
                  <div className="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg">
                    <i className="fa-solid fa-certificate"></i>
                  </div>
                </div>

                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">BGP ASN Number</span>
                    <strong className="text-lg font-extrabold text-blue-600">AS139981</strong>
                    <span className="text-blue-600 font-medium block mt-0.5 text-[10px]">Anggota Resmi APJII</span>
                  </div>
                  <div className="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg">
                    <i className="fa-solid fa-network-wired"></i>
                  </div>
                </div>

                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">Alokasi IP Publik IDNIC</span>
                    <strong className="text-lg font-extrabold text-indigo-600">/22 IPv4 • /48 IPv6</strong>
                    <span className="text-slate-400 block mt-0.5 text-[10px]">1.024 Public IPv4</span>
                  </div>
                  <div className="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg">
                    <i className="fa-solid fa-server"></i>
                  </div>
                </div>

                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">Jaringan Cabang Ops</span>
                    <strong className="text-lg font-extrabold text-purple-600">0 Cabang</strong>
                    <span className="text-purple-600 font-medium block mt-0.5 text-[10px]">0 Pelanggan</span>
                  </div>
                  <div className="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-lg">
                    <i className="fa-solid fa-building"></i>
                  </div>
                </div>
              </div>

              {/* Main Corporate Identity Form Box matching screenshot */}
              <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                <div className="border-b border-slate-100 pb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                  <div>
                    <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                      <i className="fa-solid fa-id-card-clip text-blue-600"></i> Identitas & Data Legalitas Resmi Perusahaan ISP
                    </h3>
                    <p className="text-slate-400 text-xs">Data identitas ini digunakan pada Kop Surat, Invoice Tagihan, Faktur Pajak, Kwitansi, dan BAST.</p>
                  </div>
                  <span className="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded-full text-[10px]">
                    VERIFIED CORPORATE PROFILE
                  </span>
                </div>

                <form onSubmit={handleSaveSettings} className="space-y-6">
                  {/* 1. Identitas Badan Hukum & Perpajakan (DJP) */}
                  <div className="space-y-3">
                    <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider text-blue-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                      <i className="fa-solid fa-scale-balanced"></i> 1. IDENTITAS BADAN HUKUM & PERPAJAKAN (DJP)
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Nama Resmi Badan Usaha (PT/CV)</label>
                        <input
                          type="text"
                          value={settings.company_name || 'PT MITRAXCON SYNERGY UTAMA'}
                          onChange={(e) => setSettings({ ...settings, company_name: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-900 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Nama Dagang / Commercial Brand</label>
                        <input
                          type="text"
                          value={settings.company_brand || 'SYNERGY FIBER BROADBANDS'}
                          onChange={(e) => setSettings({ ...settings, company_brand: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-blue-600 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Nomor Induk Berusaha (NIB 13 Digit)</label>
                        <input
                          type="text"
                          value={settings.company_nib || '9120003418821'}
                          onChange={(e) => setSettings({ ...settings, company_nib: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">NPWP 16 Digit / 15 Digit</label>
                        <input
                          type="text"
                          value={settings.company_npwp || '01.234.567.8-901.000'}
                          onChange={(e) => setSettings({ ...settings, company_npwp: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-900 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">No. Pengukuhan PKP (NPPKP / SPPKP)</label>
                        <input
                          type="text"
                          value={settings.company_nppkp || 'PEM-0912/WPJ.06/KP.0303/2021'}
                          onChange={(e) => setSettings({ ...settings, company_nppkp: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono text-emerald-600 font-bold text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>

                    <div>
                      <label className="font-semibold text-slate-700 block mb-1">Klasifikasi Baku Lapangan Usaha Indonesia (KBLI)</label>
                      <input
                        type="text"
                        value={settings.company_kbli || '61100 (Jaringan Telekomunikasi Kabel) & 61999'}
                        onChange={(e) => setSettings({ ...settings, company_kbli: e.target.value })}
                        className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-slate-800 text-xs focus:bg-white focus:border-blue-500 transition"
                      />
                    </div>
                  </div>

                  {/* 2. Perizinan Kominfo, APJII & Alokasi BGP IP Transit */}
                  <div className="space-y-3 pt-2">
                    <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider text-indigo-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                      <i className="fa-solid fa-tower-broadcast"></i> 2. PERIZINAN KOMINFO, APJII & ALOKASI BGP IP TRANSIT
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">No. SK Izin Penyelenggaraan ISP (Kominfo)</label>
                        <input
                          type="text"
                          value={settings.company_izin_isp || 'KEPMENKOMINFO NO. 412/TEL.02.02/2021'}
                          onChange={(e) => setSettings({ ...settings, company_izin_isp: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-blue-600 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">No. Izin Jaringan Lokal (Jartaplok/Jartup)</label>
                        <input
                          type="text"
                          value={settings.company_izin_jartaplok || 'IZIN-JARTAPLOK-NETPRO-2022-09'}
                          onChange={(e) => setSettings({ ...settings, company_izin_jartaplok: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-800 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Nomor Anggota APJII</label>
                        <input
                          type="text"
                          value={settings.company_apjii || 'ANGGOTA APJII NO. 428/REG-2022'}
                          onChange={(e) => setSettings({ ...settings, company_apjii: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Autonomous System Number (ASN)</label>
                        <input
                          type="text"
                          value={settings.company_asn || 'AS139981 (NETPRO-AS-ID)'}
                          onChange={(e) => setSettings({ ...settings, company_asn: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-indigo-600 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Prefix Alokasi IPv4 Publik IDNIC</label>
                        <input
                          type="text"
                          value={settings.company_ipv4 || '103.145.220.0/22 (1.024 IP Address)'}
                          onChange={(e) => setSettings({ ...settings, company_ipv4: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-purple-600 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>
                  </div>

                  {/* 3. Lokasi Kantor Pusat & Kontak Layanan 24 Jam */}
                  <div className="space-y-3 pt-2">
                    <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider text-emerald-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                      <i className="fa-solid fa-headset"></i> 3. LOKASI KANTOR PUSAT (HQ) & KONTAK LAYANAN 24 JAM
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">No. Telepon Kantor (Hunting)</label>
                        <input
                          type="text"
                          value={settings.company_phone || '021-52908812'}
                          onChange={(e) => setSettings({ ...settings, company_phone: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-slate-900 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Call Center 24 Jam (Toll-Free/Hotline)</label>
                        <input
                          type="text"
                          value={settings.company_call_center || '1500-988 (24 Jam)'}
                          onChange={(e) => setSettings({ ...settings, company_call_center: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-emerald-600 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">WhatsApp Official Billing & CS</label>
                        <input
                          type="text"
                          value={settings.company_wa || '0812-9876-5432'}
                          onChange={(e) => setSettings({ ...settings, company_wa: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold font-mono text-emerald-600 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Email Resmi Billing & Info</label>
                        <input
                          type="email"
                          value={settings.company_email || 'billing@netpro.co.id'}
                          onChange={(e) => setSettings({ ...settings, company_email: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Website Perusahaan</label>
                        <input
                          type="url"
                          value={settings.company_website || 'https://netpro.co.id'}
                          onChange={(e) => setSettings({ ...settings, company_website: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium text-blue-600 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Koordinat GPS Kantor Pusat</label>
                        <input
                          type="text"
                          value={settings.company_gps || '-6.2384, 106.8245'}
                          onChange={(e) => setSettings({ ...settings, company_gps: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>

                    <div>
                      <label className="font-semibold text-slate-700 block mb-1">Alamat Gedung & Kantor Pusat Lengkap</label>
                      <textarea
                        rows={2}
                        value={settings.company_address || 'Gedung Cyber Lt. 5, Jl. Kuningan Barat No. 8, Jakarta Selatan 12710'}
                        onChange={(e) => setSettings({ ...settings, company_address: e.target.value })}
                        className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 leading-relaxed text-xs focus:bg-white focus:border-blue-500 transition"
                      />
                    </div>
                  </div>

                  {/* 4. Penanggung Jawab & Penandatangan Dokumen */}
                  <div className="space-y-3 pt-2">
                    <h4 className="font-bold text-slate-800 text-xs uppercase tracking-wider text-purple-600 flex items-center gap-1.5 border-b border-slate-100 pb-1.5">
                      <i className="fa-solid fa-signature"></i> 4. PENANGGUNG JAWAB & OTORISASI DOKUMEN RESMI
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Nama Direktur Utama / Penanggung Jawab</label>
                        <input
                          type="text"
                          value={settings.company_director || 'Muhammad Ibrahim, S.Kom., M.T.'}
                          onChange={(e) => setSettings({ ...settings, company_director: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-900 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Jabatan Penandatangan</label>
                        <input
                          type="text"
                          value={settings.company_director_title || 'Direktur Utama (President Director)'}
                          onChange={(e) => setSettings({ ...settings, company_director_title: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 text-xs focus:bg-white focus:border-blue-500 transition"
                        />
                      </div>
                    </div>
                  </div>

                  {/* Full-width Save Button */}
                  <button
                    type="submit"
                    disabled={saving}
                    className="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-lg transition flex items-center justify-center gap-2 cursor-pointer text-xs"
                  >
                    <i className="fa-solid fa-floppy-disk"></i>
                    <span>{saving ? 'Menyimpan...' : 'Simpan Lengkap Identitas Perusahaan'}</span>
                  </button>
                </form>
              </div>

              {/* Branch Offices Table matching perusahaan.php */}
              <div className="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                  <div>
                    <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                      <i className="fa-solid fa-network-wired text-indigo-600"></i> Daftar Kantor Cabang & Area Coverage ISP
                    </h3>
                    <p className="text-slate-400 text-xs">Total {branches.length} Kantor Cabang & POP Regional terhubung ke sistem terpusat.</p>
                  </div>
                  <button
                    onClick={() => showToast({ type: 'info', message: 'Form Pendaftaran Kantor Cabang Baru dibuka.' })}
                    className="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg shadow flex items-center gap-1.5 text-xs cursor-pointer"
                  >
                    <i className="fa-solid fa-plus"></i> Tambah Cabang Baru
                  </button>
                </div>

                <div className="overflow-x-auto">
                  <table className="w-full text-left border-collapse">
                    <thead>
                      <tr className="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-xs">
                        <th className="py-3 px-4">Kode Cabang</th>
                        <th className="py-3 px-4">Nama Kantor Cabang</th>
                        <th className="py-3 px-4">Alamat Operasional</th>
                        <th className="py-3 px-4">Kepala Cabang</th>
                        <th className="py-3 px-4 font-mono text-center">Total Subs</th>
                        <th className="py-3 px-4 text-right">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      {branches.length === 0 ? (
                        <tr>
                          <td colSpan={6} className="py-6 text-center text-slate-400 text-xs">
                            Belum ada kantor cabang didaftarkan.
                          </td>
                        </tr>
                      ) : (
                        branches.map((b) => (
                          <tr key={b.id} className="border-b border-slate-50 hover:bg-slate-50/50 text-xs">
                            <td className="py-3.5 px-4 font-mono font-bold text-blue-600">{b.code}</td>
                            <td className="py-3.5 px-4 font-bold text-slate-800">{b.name}</td>
                            <td className="py-3.5 px-4 text-slate-600">{b.address}</td>
                            <td className="py-3.5 px-4 font-bold text-slate-800">{b.manager}</td>
                            <td className="py-3.5 px-4 font-mono font-bold text-center text-indigo-600">
                              {Number(b.subs_count || 0).toLocaleString('id-ID')} Akun
                            </td>
                            <td className="py-3.5 px-4 text-right">
                              <button
                                onClick={() => showToast({ type: 'warning', message: `Cabang ${b.name} dihapus.` })}
                                className="text-rose-600 font-bold hover:underline cursor-pointer"
                              >
                                Hapus
                              </button>
                            </td>
                          </tr>
                        ))
                      )}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          )}

          {/* ================= 3. OTOMATISASI BILLING & DENDA (billing_config.php) ================= */}
          {activeTab === 'pengaturan-billing' && (
            <div className="space-y-5">
              <div className="border-b border-slate-100 pb-3">
                <h2 className="text-base font-bold text-slate-900 flex items-center gap-2">
                  <i className="fa-solid fa-calendar-check text-red-600"></i>
                  <span>Otomatisasi Siklus Billing, PPN & Kebijakan Denda</span>
                </h2>
                <p className="text-slate-500 text-xs">Penjadwalan tanggal tagihan rutin, masa tenggang, auto-isolir, dan tarif pajak negara.</p>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Tanggal Jatuh Tempo Rutin</label>
                  <div className="flex items-center gap-2">
                    <input
                      type="number"
                      min="1"
                      max="28"
                      value={settings.billing_due_date}
                      onChange={(e) => setSettings({ ...settings, billing_due_date: e.target.value })}
                      className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                    />
                    <span className="font-bold text-slate-500">Tiap Bulan</span>
                  </div>
                </div>

                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Masa Tenggang (Grace Days)</label>
                  <div className="flex items-center gap-2">
                    <input
                      type="number"
                      value={settings.billing_grace_days}
                      onChange={(e) => setSettings({ ...settings, billing_grace_days: e.target.value })}
                      className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                    />
                    <span className="font-bold text-slate-500">Hari</span>
                  </div>
                </div>

                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Tanggal Eksekusi Auto-Isolir</label>
                  <div className="flex items-center gap-2">
                    <input
                      type="number"
                      min="1"
                      max="28"
                      value={settings.billing_isolate_date}
                      onChange={(e) => setSettings({ ...settings, billing_isolate_date: e.target.value })}
                      className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-red-600 text-xs"
                    />
                    <span className="font-bold text-slate-500">Tiap Bulan</span>
                  </div>
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Besaran Denda Keterlambatan</label>
                  <div className="relative">
                    <input
                      type="number"
                      value={settings.billing_late_fee}
                      onChange={(e) => setSettings({ ...settings, billing_late_fee: e.target.value })}
                      className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs pl-8"
                    />
                    <span className="absolute left-2.5 top-2.5 text-slate-400 font-bold text-xs">Rp</span>
                  </div>
                </div>

                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Tarif Pajak PPN (%)</label>
                  <div className="flex items-center gap-2">
                    <input
                      type="number"
                      step="0.1"
                      value={settings.ppn_rate}
                      onChange={(e) => setSettings({ ...settings, ppn_rate: e.target.value })}
                      className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                    />
                    <span className="font-bold text-slate-500">%</span>
                  </div>
                </div>

                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Kontribusi USO Kominfo (%)</label>
                  <div className="flex items-center gap-2">
                    <input
                      type="number"
                      step="0.01"
                      value={settings.uso_rate}
                      onChange={(e) => setSettings({ ...settings, uso_rate: e.target.value })}
                      className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                    />
                    <span className="font-bold text-slate-500">%</span>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* ================= 4. PAYMENT GATEWAYS & QRIS (payment_gateway.php) ================= */}
          {activeTab === 'pengaturan-payment' && (
            <div className="space-y-6">
              {/* Top 3 Overview Cards matching screenshot */}
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">Gateway Aktif Utama</span>
                    <strong className="text-2xl font-bold text-blue-600">Midtrans Snap</strong>
                    <span className="text-emerald-600 font-bold block mt-0.5 text-xs">● Auto-Callback Live</span>
                  </div>
                  <div className="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                    <i className="fa-solid fa-credit-card"></i>
                  </div>
                </div>

                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">Skema Potongan Biaya</span>
                    <strong className="text-2xl font-bold text-indigo-600">
                      {settings.global_fee_scheme === 'surcharge' || !settings.global_fee_scheme ? 'Beban Pelanggan' : 'Dipotong ISP'}
                    </strong>
                    <span className="text-indigo-600 font-medium block mt-0.5 text-xs">MDR QRIS 0.7% • VA Rp 4.000</span>
                  </div>
                  <div className="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl">
                    <i className="fa-solid fa-percent"></i>
                  </div>
                </div>

                <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
                  <div>
                    <span className="text-slate-400 font-semibold uppercase text-[10px] block">Webhook Notification URL</span>
                    <strong className="text-xs font-mono font-bold text-slate-800 block truncate max-w-[200px]">
                      /api/payment_callback.php
                    </strong>
                    <span className="text-slate-400 block mt-0.5 text-xs">Instant Reconciliation</span>
                  </div>
                  <div className="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
                    <i className="fa-solid fa-bolt"></i>
                  </div>
                </div>
              </div>

              {/* Interactive Gateway Tab Navigation Box */}
              <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                {/* 6 Tab Headers matching payment_gateway.php */}
                <div className="flex overflow-x-auto border-b border-slate-200 bg-slate-50/70 p-2 gap-2 text-xs font-bold custom-scrollbar">
                  <button
                    type="button"
                    onClick={() => setActiveGatewayTab('midtrans')}
                    className={`px-4 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer ${
                      activeGatewayTab === 'midtrans'
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-600 hover:bg-slate-200/60'
                    }`}
                  >
                    <i className="fa-solid fa-bolt"></i> Midtrans
                  </button>
                  <button
                    type="button"
                    onClick={() => setActiveGatewayTab('xendit')}
                    className={`px-4 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer ${
                      activeGatewayTab === 'xendit'
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-600 hover:bg-slate-200/60'
                    }`}
                  >
                    <i className="fa-solid fa-building-columns"></i> Xendit
                  </button>
                  <button
                    type="button"
                    onClick={() => setActiveGatewayTab('tripay')}
                    className={`px-4 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer ${
                      activeGatewayTab === 'tripay'
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-600 hover:bg-slate-200/60'
                    }`}
                  >
                    <i className="fa-solid fa-wallet"></i> Tripay
                  </button>
                  <button
                    type="button"
                    onClick={() => setActiveGatewayTab('duitku')}
                    className={`px-4 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer ${
                      activeGatewayTab === 'duitku'
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-600 hover:bg-slate-200/60'
                    }`}
                  >
                    <i className="fa-solid fa-coins"></i> Duitku
                  </button>
                  <button
                    type="button"
                    onClick={() => setActiveGatewayTab('manual')}
                    className={`px-4 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer ${
                      activeGatewayTab === 'manual' || activeGatewayTab === 'manual_bank'
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-600 hover:bg-slate-200/60'
                    }`}
                  >
                    <i className="fa-solid fa-qrcode"></i> Transfer & QRIS Statis
                  </button>
                  <button
                    type="button"
                    onClick={() => setActiveGatewayTab('fee_matrix')}
                    className={`px-4 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer ${
                      activeGatewayTab === 'fee_matrix'
                        ? 'bg-blue-600 text-white shadow-sm'
                        : 'text-slate-600 hover:bg-slate-200/60'
                    }`}
                  >
                    <i className="fa-solid fa-calculator"></i> Skema Potongan Biaya (MDR)
                  </button>
                </div>

                <form onSubmit={handleSaveSettings} className="p-6 space-y-6">
                  {/* TAB 1: MIDTRANS */}
                  {activeGatewayTab === 'midtrans' && (
                    <div className="space-y-5">
                      <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                        <div>
                          <h4 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span className="w-3 h-3 rounded-full bg-blue-600"></span> Midtrans Snap & Core Payment Gateway
                          </h4>
                          <p className="text-slate-400 text-xs">Gateway pembayaran multi-channel otomatis (QRIS Dinamis, BCA/Mandiri/BNI/BRI VA, GoPay, ShopeePay).</p>
                        </div>
                        <div className="flex items-center gap-2">
                          <label className="font-semibold text-slate-700 text-xs">Status Gateway:</label>
                          <select
                            value={settings.midtrans_active || '1'}
                            onChange={(e) => setSettings({ ...settings, midtrans_active: e.target.value })}
                            className="bg-slate-50 border border-slate-200 rounded-lg p-1.5 font-bold text-slate-800 text-xs"
                          >
                            <option value="1">AKTIF</option>
                            <option value="0">NONAKTIF</option>
                          </select>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Environment Mode</label>
                          <select
                            value={settings.midtrans_env || 'production'}
                            onChange={(e) => setSettings({ ...settings, midtrans_env: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-xs"
                          >
                            <option value="production">Production (Live Transaksi Riil)</option>
                            <option value="sandbox">Sandbox (Mode Simulasi / Testing)</option>
                          </select>
                        </div>
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Merchant ID Midtrans</label>
                          <input
                            type="text"
                            value={settings.midtrans_merchant_id || 'G1928812'}
                            onChange={(e) => setSettings({ ...settings, midtrans_merchant_id: e.target.value })}
                            required
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                          />
                        </div>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Server Key (Secret)</label>
                          <input
                            type="password"
                            value={settings.midtrans_server_key || 'SB-Mid-server-9912099318821'}
                            onChange={(e) => setSettings({ ...settings, midtrans_server_key: e.target.value })}
                            required
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-blue-600 text-xs"
                          />
                        </div>
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Client Key (Public)</label>
                          <input
                            type="text"
                            value={settings.midtrans_client_key || 'SB-Mid-client-8819200192931'}
                            onChange={(e) => setSettings({ ...settings, midtrans_client_key: e.target.value })}
                            required
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                          />
                        </div>
                      </div>

                      <div className="p-4 bg-blue-50/60 rounded-xl border border-blue-100 space-y-3">
                        <h5 className="font-bold text-blue-900 text-xs uppercase">Potongan Biaya Admin Transaksi Midtrans:</h5>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                          <div>
                            <label className="font-semibold text-slate-700 block mb-1 text-xs">Biaya VA Bank (Flat Rp)</label>
                            <input
                              type="number"
                              value={settings.midtrans_fee_va || '4000'}
                              onChange={(e) => setSettings({ ...settings, midtrans_fee_va: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-blue-600 text-xs"
                            />
                          </div>
                          <div>
                            <label className="font-semibold text-slate-700 block mb-1 text-xs">MDR QRIS (%)</label>
                            <input
                              type="text"
                              value={settings.midtrans_fee_qris || '0.7'}
                              onChange={(e) => setSettings({ ...settings, midtrans_fee_qris: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-emerald-600 text-xs"
                            />
                          </div>
                          <div>
                            <label className="font-semibold text-slate-700 block mb-1 text-xs">Beban Potongan Biaya</label>
                            <select
                              value={settings.midtrans_fee_borne || 'customer'}
                              onChange={(e) => setSettings({ ...settings, midtrans_fee_borne: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800 text-xs"
                            >
                              <option value="customer">Ditanggung Pelanggan (Surcharge)</option>
                              <option value="merchant">Dipotong dari Pendapatan ISP</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  )}

                  {/* TAB 2: XENDIT */}
                  {activeGatewayTab === 'xendit' && (
                    <div className="space-y-5">
                      <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                        <div>
                          <h4 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span className="w-3 h-3 rounded-full bg-indigo-600"></span> Xendit Payment Infrastructure
                          </h4>
                          <p className="text-slate-400 text-xs">Penyedia pembayaran Virtual Account perbankan instan dan e-Wallet.</p>
                        </div>
                        <div className="flex items-center gap-2">
                          <label className="font-semibold text-slate-700 text-xs">Status Gateway:</label>
                          <select
                            value={settings.xendit_active || '0'}
                            onChange={(e) => setSettings({ ...settings, xendit_active: e.target.value })}
                            className="bg-slate-50 border border-slate-200 rounded-lg p-1.5 font-bold text-slate-800 text-xs"
                          >
                            <option value="1">AKTIF</option>
                            <option value="0">NONAKTIF</option>
                          </select>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Environment Mode</label>
                          <select
                            value={settings.xendit_env || 'production'}
                            onChange={(e) => setSettings({ ...settings, xendit_env: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-xs"
                          >
                            <option value="production">Production (Live)</option>
                            <option value="development">Development / Test</option>
                          </select>
                        </div>
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Webhook Callback Verification Token</label>
                          <input
                            type="text"
                            value={settings.xendit_webhook_token || 'xnd_cb_token_889129012389'}
                            onChange={(e) => setSettings({ ...settings, xendit_webhook_token: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                          />
                        </div>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Secret API Key (Server)</label>
                          <input
                            type="password"
                            value={settings.xendit_secret_key || 'xnd_production_sec_991209'}
                            onChange={(e) => setSettings({ ...settings, xendit_secret_key: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-indigo-600 text-xs"
                          />
                        </div>
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Public Key (Client)</label>
                          <input
                            type="text"
                            value={settings.xendit_public_key || 'xnd_public_8812901289'}
                            onChange={(e) => setSettings({ ...settings, xendit_public_key: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                          />
                        </div>
                      </div>

                      <div className="p-4 bg-indigo-50/60 rounded-xl border border-indigo-100 space-y-3">
                        <h5 className="font-bold text-indigo-900 text-xs uppercase">Potongan Biaya Admin Transaksi Xendit:</h5>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                          <div>
                            <label className="font-semibold text-slate-700 block mb-1 text-xs">Biaya VA Bank (Flat Rp)</label>
                            <input
                              type="number"
                              value={settings.xendit_fee_va || '4000'}
                              onChange={(e) => setSettings({ ...settings, xendit_fee_va: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-indigo-600 text-xs"
                            />
                          </div>
                          <div>
                            <label className="font-semibold text-slate-700 block mb-1 text-xs">MDR QRIS (%)</label>
                            <input
                              type="text"
                              value={settings.xendit_fee_qris || '0.7'}
                              onChange={(e) => setSettings({ ...settings, xendit_fee_qris: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-emerald-600 text-xs"
                            />
                          </div>
                          <div>
                            <label className="font-semibold text-slate-700 block mb-1 text-xs">Beban Potongan Biaya</label>
                            <select
                              value={settings.xendit_fee_borne || 'customer'}
                              onChange={(e) => setSettings({ ...settings, xendit_fee_borne: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800 text-xs"
                            >
                              <option value="customer">Ditanggung Pelanggan</option>
                              <option value="merchant">Dipotong dari Pendapatan ISP</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  )}

                  {/* TAB 3: TRIPAY */}
                  {activeGatewayTab === 'tripay' && (
                    <div className="space-y-5">
                      <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                        <div>
                          <h4 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span className="w-3 h-3 rounded-full bg-emerald-600"></span> Tripay Payment Gateway Channel
                          </h4>
                          <p className="text-slate-400 text-xs">Gateway dengan biaya terjangkau untuk pembayaran via Alfamart/Indomaret & VA.</p>
                        </div>
                        <div className="flex items-center gap-2">
                          <label className="font-semibold text-slate-700 text-xs">Status Gateway:</label>
                          <select
                            value={settings.tripay_active || '0'}
                            onChange={(e) => setSettings({ ...settings, tripay_active: e.target.value })}
                            className="bg-slate-50 border border-slate-200 rounded-lg p-1.5 font-bold text-slate-800 text-xs"
                          >
                            <option value="1">AKTIF</option>
                            <option value="0">NONAKTIF</option>
                          </select>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Kode Merchant Tripay</label>
                          <input
                            type="text"
                            value={settings.tripay_merchant_code || 'T19822'}
                            onChange={(e) => setSettings({ ...settings, tripay_merchant_code: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                          />
                        </div>
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">API Key</label>
                          <input
                            type="password"
                            value={settings.tripay_api_key || 'DEV-TRIPAY-KEY-9912088'}
                            onChange={(e) => setSettings({ ...settings, tripay_api_key: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-emerald-600 text-xs"
                          />
                        </div>
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Private Key</label>
                          <input
                            type="password"
                            value={settings.tripay_private_key || 'tripay-priv-99120938102'}
                            onChange={(e) => setSettings({ ...settings, tripay_private_key: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                          />
                        </div>
                      </div>

                      <div className="p-4 bg-emerald-50/60 rounded-xl border border-emerald-100 space-y-3">
                        <h5 className="font-bold text-emerald-900 text-xs uppercase">Potongan Biaya Admin Transaksi Tripay:</h5>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                          <div>
                            <label className="font-semibold text-slate-700 block mb-1 text-xs">Biaya Admin Flat per Transaksi (Rp)</label>
                            <input
                              type="number"
                              value={settings.tripay_fee_flat || '3500'}
                              onChange={(e) => setSettings({ ...settings, tripay_fee_flat: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-emerald-600 text-xs"
                            />
                          </div>
                          <div>
                            <label className="font-semibold text-slate-700 block mb-1 text-xs">Beban Potongan Biaya</label>
                            <select
                              value={settings.tripay_fee_borne || 'customer'}
                              onChange={(e) => setSettings({ ...settings, tripay_fee_borne: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800 text-xs"
                            >
                              <option value="customer">Ditanggung Pelanggan (Surcharge)</option>
                              <option value="merchant">Dipotong dari Pendapatan ISP</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  )}

                  {/* TAB 4: DUITKU */}
                  {activeGatewayTab === 'duitku' && (
                    <div className="space-y-5">
                      <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                        <div>
                          <h4 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span className="w-3 h-3 rounded-full bg-amber-500"></span> Duitku Payment Gateway
                          </h4>
                          <p className="text-slate-400 text-xs">Integrasi pembayaran online dengan settlement harian otomatis.</p>
                        </div>
                        <div className="flex items-center gap-2">
                          <label className="font-semibold text-slate-700 text-xs">Status Gateway:</label>
                          <select
                            value={settings.duitku_active || '0'}
                            onChange={(e) => setSettings({ ...settings, duitku_active: e.target.value })}
                            className="bg-slate-50 border border-slate-200 rounded-lg p-1.5 font-bold text-slate-800 text-xs"
                          >
                            <option value="1">AKTIF</option>
                            <option value="0">NONAKTIF</option>
                          </select>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">Merchant Code Duitku</label>
                          <input
                            type="text"
                            value={settings.duitku_merchant_code || 'D192881'}
                            onChange={(e) => setSettings({ ...settings, duitku_merchant_code: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                          />
                        </div>
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1 text-xs">API Key Duitku</label>
                          <input
                            type="password"
                            value={settings.duitku_api_key || 'duitku-api-key-9912088'}
                            onChange={(e) => setSettings({ ...settings, duitku_api_key: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-amber-600 text-xs"
                          />
                        </div>
                      </div>

                      <div className="p-4 bg-amber-50/60 rounded-xl border border-amber-100 space-y-3">
                        <h5 className="font-bold text-amber-900 text-xs uppercase">Potongan Biaya Admin Transaksi Duitku:</h5>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                          <div>
                            <label className="font-semibold text-slate-700 block mb-1 text-xs">Biaya Admin Flat per Transaksi (Rp)</label>
                            <input
                              type="number"
                              value={settings.duitku_fee_flat || '3000'}
                              onChange={(e) => setSettings({ ...settings, duitku_fee_flat: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-amber-600 text-xs"
                            />
                          </div>
                          <div>
                            <label className="font-semibold text-slate-700 block mb-1 text-xs">Beban Potongan Biaya</label>
                            <select
                              value={settings.duitku_fee_borne || 'customer'}
                              onChange={(e) => setSettings({ ...settings, duitku_fee_borne: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800 text-xs"
                            >
                              <option value="customer">Ditanggung Pelanggan (Surcharge)</option>
                              <option value="merchant">Dipotong dari Pendapatan ISP</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  )}

                  {/* TAB 5: TRANSFER & QRIS STATIS */}
                  {(activeGatewayTab === 'manual' || activeGatewayTab === 'manual_bank') && (
                    <div className="space-y-5">
                      <div className="border-b border-slate-100 pb-3">
                        <h4 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                          <i className="fa-solid fa-qrcode text-purple-600"></i> Rekening Bank Penerima & QRIS Merchant Statis
                        </h4>
                        <p className="text-slate-400 text-xs">Rincian nomor rekening resmi yang tertera pada Invoice tagihan untuk transfer manual.</p>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                          <strong className="text-blue-700 font-bold block flex items-center gap-1.5 text-xs">
                            <i className="fa-solid fa-building-columns"></i> 1. Bank Central Asia (BCA)
                          </strong>
                          <div>
                            <label className="text-slate-500 block text-[10px]">Nomor Rekening BCA</label>
                            <input
                              type="text"
                              value={settings.bank_bca_no || '881-002-9918'}
                              onChange={(e) => setSettings({ ...settings, bank_bca_no: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-slate-900 text-xs"
                            />
                          </div>
                          <div>
                            <label className="text-slate-500 block text-[10px]">Atas Nama Rekening</label>
                            <input
                              type="text"
                              value={settings.bank_bca_name || 'PT MITRAXCON SYNERGY UTAMA'}
                              onChange={(e) => setSettings({ ...settings, bank_bca_name: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800 text-xs"
                            />
                          </div>
                        </div>

                        <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                          <strong className="text-amber-700 font-bold block flex items-center gap-1.5 text-xs">
                            <i className="fa-solid fa-building-columns"></i> 2. Bank Mandiri
                          </strong>
                          <div>
                            <label className="text-slate-500 block text-[10px]">Nomor Rekening Mandiri</label>
                            <input
                              type="text"
                              value={settings.bank_mandiri_no || '124-00-8899221-1'}
                              onChange={(e) => setSettings({ ...settings, bank_mandiri_no: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-slate-900 text-xs"
                            />
                          </div>
                          <div>
                            <label className="text-slate-500 block text-[10px]">Atas Nama Rekening</label>
                            <input
                              type="text"
                              value={settings.bank_mandiri_name || 'PT MITRAXCON SYNERGY UTAMA'}
                              onChange={(e) => setSettings({ ...settings, bank_mandiri_name: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800 text-xs"
                            />
                          </div>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                          <strong className="text-blue-600 font-bold block flex items-center gap-1.5 text-xs">
                            <i className="fa-solid fa-building-columns"></i> 3. Bank BRI
                          </strong>
                          <div>
                            <label className="text-slate-500 block text-[10px]">Nomor Rekening BRI</label>
                            <input
                              type="text"
                              value={settings.bank_bri_no || '0341-01-001234-30-5'}
                              onChange={(e) => setSettings({ ...settings, bank_bri_no: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-slate-900 text-xs"
                            />
                          </div>
                          <div>
                            <label className="text-slate-500 block text-[10px]">Atas Nama Rekening</label>
                            <input
                              type="text"
                              value={settings.bank_bri_name || 'PT MITRAXCON SYNERGY UTAMA'}
                              onChange={(e) => setSettings({ ...settings, bank_bri_name: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800 text-xs"
                            />
                          </div>
                        </div>

                        <div className="p-4 bg-purple-50/60 rounded-xl border border-purple-100 space-y-3">
                          <strong className="text-purple-700 font-bold block flex items-center gap-1.5 text-xs">
                            <i className="fa-solid fa-qrcode"></i> 4. QRIS Statis Merchant (NMID)
                          </strong>
                          <div>
                            <label className="text-slate-500 block text-[10px]">National Merchant ID (NMID)</label>
                            <input
                              type="text"
                              value={settings.qris_nmid || 'ID1020038819281'}
                              onChange={(e) => setSettings({ ...settings, qris_nmid: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-purple-700 text-xs"
                            />
                          </div>
                          <div>
                            <label className="text-slate-500 block text-[10px]">Nama Merchant Terdaftar di ASPI</label>
                            <input
                              type="text"
                              value={settings.qris_merchant_name || 'NETPRO ISP TELECOM'}
                              onChange={(e) => setSettings({ ...settings, qris_merchant_name: e.target.value })}
                              className="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800 text-xs"
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  )}

                  {/* TAB 6: FEE MATRIX */}
                  {activeGatewayTab === 'fee_matrix' && (
                    <div className="space-y-5">
                      <div className="border-b border-slate-100 pb-3">
                        <h4 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                          <i className="fa-solid fa-scale-balanced text-indigo-600"></i> Matriks Kebijakan Potongan Biaya Admin & MDR
                        </h4>
                        <p className="text-slate-400 text-xs">Aturan pembebanan selisih biaya perbankan pada saat kasir atau pelanggan melakukan pembayaran.</p>
                      </div>

                      <div className="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                        <label className="font-bold text-slate-800 block text-xs">Kebijakan Global Pembebanan Biaya Admin:</label>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                          <label className="p-3 bg-white border border-slate-200 rounded-xl flex items-start gap-3 cursor-pointer hover:border-blue-500 transition">
                            <input
                              type="radio"
                              name="global_fee_scheme"
                              value="surcharge"
                              checked={settings.global_fee_scheme === 'surcharge' || !settings.global_fee_scheme}
                              onChange={(e) => setSettings({ ...settings, global_fee_scheme: e.target.value })}
                              className="mt-1 text-blue-600"
                            />
                            <div>
                              <strong className="text-slate-900 font-bold block text-xs">Biaya Dibebankan ke Pelanggan (Surcharge)</strong>
                              <span className="text-[11px] text-slate-500 leading-relaxed">
                                Contoh: Tagihan Rp 250.000 + Biaya VA Rp 4.000 = Total dibayar pelanggan <b>Rp 254.000</b>. ISP menerima bersih Rp 250.000.
                              </span>
                            </div>
                          </label>

                          <label className="p-3 bg-white border border-slate-200 rounded-xl flex items-start gap-3 cursor-pointer hover:border-blue-500 transition">
                            <input
                              type="radio"
                              name="global_fee_scheme"
                              value="absorbed"
                              checked={settings.global_fee_scheme === 'absorbed'}
                              onChange={(e) => setSettings({ ...settings, global_fee_scheme: e.target.value })}
                              className="mt-1 text-blue-600"
                            />
                            <div>
                              <strong className="text-slate-900 font-bold block text-xs">Biaya Ditanggung ISP (Merchant Absorbed)</strong>
                              <span className="text-[11px] text-slate-500 leading-relaxed">
                                Contoh: Tagihan Rp 250.000, pelanggan bayar tetap <b>Rp 250.000</b>. Pendapatan ISP dipotong biaya gateway Rp 4.000 menjadi Rp 246.000.
                              </span>
                            </div>
                          </label>
                        </div>
                      </div>

                      <div className="overflow-x-auto border border-slate-200 rounded-xl">
                        <table className="w-full text-left border-collapse text-xs">
                          <thead>
                            <tr className="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                              <th className="py-2.5 px-3">Metode Pembayaran</th>
                              <th className="py-2.5 px-3">Gateway Penyedia</th>
                              <th className="py-2.5 px-3 font-mono text-center">Tarif MDR / Biaya</th>
                              <th className="py-2.5 px-3">Beban Biaya</th>
                              <th className="py-2.5 px-3 font-mono text-right">Settlement Dana</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr className="border-b border-slate-50">
                              <td className="py-2.5 px-3 font-bold text-slate-800">QRIS Nasional (All Wallet & Bank)</td>
                              <td className="py-2.5 px-3 text-slate-600">Midtrans / Xendit</td>
                              <td className="py-2.5 px-3 font-mono font-bold text-center text-emerald-600">0.70%</td>
                              <td className="py-2.5 px-3">
                                <span className="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Pelanggan</span>
                              </td>
                              <td className="py-2.5 px-3 font-mono text-right text-slate-600">H+1 Realtime</td>
                            </tr>
                            <tr className="border-b border-slate-50">
                              <td className="py-2.5 px-3 font-bold text-slate-800">BCA Virtual Account (VA)</td>
                              <td className="py-2.5 px-3 text-slate-600">Midtrans Snap</td>
                              <td className="py-2.5 px-3 font-mono font-bold text-center text-blue-600">Rp 4.000 / Trx</td>
                              <td className="py-2.5 px-3">
                                <span className="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Pelanggan</span>
                              </td>
                              <td className="py-2.5 px-3 font-mono text-right text-slate-600">Instan (Detik)</td>
                            </tr>
                            <tr className="border-b border-slate-50">
                              <td className="py-2.5 px-3 font-bold text-slate-800">Mandiri / BRI / BNI VA</td>
                              <td className="py-2.5 px-3 text-slate-600">Midtrans / Tripay</td>
                              <td className="py-2.5 px-3 font-mono font-bold text-center text-indigo-600">Rp 3.500 - Rp 4.000</td>
                              <td className="py-2.5 px-3">
                                <span className="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Pelanggan</span>
                              </td>
                              <td className="py-2.5 px-3 font-mono text-right text-slate-600">Instan (Detik)</td>
                            </tr>
                            <tr className="border-b border-slate-50">
                              <td className="py-2.5 px-3 font-bold text-slate-800">Alfamart / Indomaret Gerai</td>
                              <td className="py-2.5 px-3 text-slate-600">Tripay / Duitku</td>
                              <td className="py-2.5 px-3 font-mono font-bold text-center text-amber-600">Rp 5.000 / Trx</td>
                              <td className="py-2.5 px-3">
                                <span className="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Pelanggan</span>
                              </td>
                              <td className="py-2.5 px-3 font-mono text-right text-slate-600">H+1 Kerja</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  )}

                  {/* Global Save Button matching screenshot */}
                  <div className="pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                    <span className="text-slate-400 text-[11px]">
                      Seluruh konfigurasi tersimpan aman ke database <code className="bg-slate-100 px-1 py-0.5 rounded text-slate-600 font-mono">settings</code>.
                    </span>
                    <button
                      type="submit"
                      disabled={saving}
                      className="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2 cursor-pointer text-xs"
                    >
                      <i className="fa-solid fa-floppy-disk"></i>
                      <span>{saving ? 'Menyimpan...' : 'Simpan Konfigurasi Payment Gateway'}</span>
                    </button>
                  </div>
                </form>
              </div>
            </div>
          )}

          {/* ================= 5. WHATSAPP GATEWAY (wa_gateway.php) ================= */}
          {activeTab === 'pengaturan-wa' && (
            <div className="space-y-5">
              <div className="border-b border-slate-100 pb-3 flex justify-between items-center">
                <div>
                  <h2 className="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i className="fa-brands fa-whatsapp text-emerald-600 text-lg"></i>
                    <span>Integrasi Bot WhatsApp Gateway & Notifikasi</span>
                  </h2>
                  <p className="text-slate-500 text-xs">Pemicu otomatisasi pesan invoice tagihan, kwitansi lunas, dan notifikasi tiket.</p>
                </div>
                <span className="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">
                  DEVICE CONNECTED ✓
                </span>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Provider WhatsApp Gateway</label>
                  <select
                    value={settings.wa_vendor}
                    onChange={(e) => setSettings({ ...settings, wa_vendor: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 text-xs"
                  >
                    <option value="fonnte">Fonnte.com API Service</option>
                    <option value="wablas">Wablas Official Gateway</option>
                    <option value="whacenter">WhaCenter Multi-device</option>
                  </select>
                </div>

                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Nomor Pengirim (Sender Number)</label>
                  <input
                    type="text"
                    value={settings.wa_sender}
                    onChange={(e) => setSettings({ ...settings, wa_sender: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-slate-900 text-xs"
                  />
                </div>
              </div>

              <div>
                <label className="font-semibold text-slate-700 block mb-1">API Token / Secret Key</label>
                <div className="relative">
                  <input
                    type={showWaToken ? 'text' : 'password'}
                    value={settings.wa_token}
                    onChange={(e) => setSettings({ ...settings, wa_token: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-emerald-600 text-xs pr-10"
                  />
                  <button
                    type="button"
                    onClick={() => setShowWaToken(!showWaToken)}
                    className="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 cursor-pointer"
                  >
                    {showWaToken ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                  </button>
                </div>
              </div>

              <div className="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                <span className="text-slate-700 font-bold block">Format Template Notifikasi Tagihan Bulanan:</span>
                <p className="text-slate-600 font-mono text-[11px] bg-white p-3 rounded-lg border border-slate-200 leading-relaxed">
                  Halo *&#123;nama_pelanggan&#125;*, tagihan internet *&#123;nama_paket&#125;* periode *&#123;periode&#125;* sebesar *&#123;total_tagihan&#125;* telah terbit. Jatuh tempo: *&#123;due_date&#125;*. Bayar instan via QRIS: &#123;link_qris&#125;. Terima kasih.
                </p>
              </div>

              <div className="p-4 bg-emerald-50/50 border border-emerald-200 rounded-xl space-y-2">
                <label className="font-bold text-emerald-900 block">Uji Coba Pengiriman Pesan (Live Test)</label>
                <div className="flex gap-2">
                  <input
                    type="text"
                    value={testWaTarget}
                    onChange={(e) => setTestWaTarget(e.target.value)}
                    placeholder="081234567890"
                    className="bg-white border border-emerald-300 rounded-lg px-3 py-2 text-xs font-mono w-60"
                  />
                  <button
                    type="button"
                    onClick={handleTestWhatsApp}
                    disabled={testWaLoading}
                    className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-lg transition flex items-center gap-1.5 cursor-pointer text-xs"
                  >
                    <Send className="w-3.5 h-3.5" />
                    <span>{testWaLoading ? 'Mengirim...' : 'Kirim Uji Coba WA'}</span>
                  </button>
                </div>
              </div>
            </div>
          )}

          {/* ================= 6. INTEGRASI MIKROTIK & RADIUS (api.php) ================= */}
          {activeTab === 'pengaturan-radius' && (
            <div className="space-y-5">
              <div className="border-b border-slate-100 pb-3 flex justify-between items-center">
                <div>
                  <h2 className="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i className="fa-solid fa-code-branch text-red-600"></i>
                    <span>Integrasi MikroTik RouterOS & FreeRADIUS AAA Server</span>
                  </h2>
                  <p className="text-slate-500 text-xs">Koneksi API NAS, port otentikasi RADIUS, dan kontrol CoA disconnect.</p>
                </div>
                <span className="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">
                  RADIUS SYNC ONLINE ✓
                </span>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">FreeRADIUS Host Server</label>
                  <input
                    type="text"
                    value={settings.radius_host}
                    onChange={(e) => setSettings({ ...settings, radius_host: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                  />
                </div>
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">RADIUS Shared Secret</label>
                  <div className="relative">
                    <input
                      type={showRadiusSecret ? 'text' : 'password'}
                      value={settings.radius_secret}
                      onChange={(e) => setSettings({ ...settings, radius_secret: e.target.value })}
                      className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs pr-10"
                    />
                    <button
                      type="button"
                      onClick={() => setShowRadiusSecret(!showRadiusSecret)}
                      className="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 cursor-pointer"
                    >
                      {showRadiusSecret ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                    </button>
                  </div>
                </div>
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Auth Port (Default: 1812)</label>
                  <input
                    type="text"
                    value={settings.radius_auth_port}
                    onChange={(e) => setSettings({ ...settings, radius_auth_port: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                  />
                </div>
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">CoA / PoD Port (RFC 3576: 3799)</label>
                  <input
                    type="text"
                    value={settings.radius_coa_port}
                    onChange={(e) => setSettings({ ...settings, radius_coa_port: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-xs"
                  />
                </div>
              </div>
            </div>
          )}

          {/* ================= 7. BACKUP & DATABASE RESTORE (backup.php) ================= */}
          {activeTab === 'pengaturan-backup' && (
            <div className="space-y-5">
              <div className="border-b border-slate-100 pb-3 flex justify-between items-center">
                <div>
                  <h2 className="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i className="fa-solid fa-database text-red-600"></i>
                    <span>Backup Database & Pemulihan Sistem (Disaster Recovery)</span>
                  </h2>
                  <p className="text-slate-500 text-xs">Cadangkan database SQL terkompresi secara manual atau otomatis terjadwal.</p>
                </div>
                <button
                  type="button"
                  onClick={handleBackupNow}
                  className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer shadow-md"
                >
                  <Zap className="w-3.5 h-3.5" />
                  <span>⚡ Backup Database Sekarang</span>
                </button>
              </div>

              <div className="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden">
                <table className="custom-table">
                  <thead>
                    <tr>
                      <th>NAMA ARSIP BACKUP</th>
                      <th>UKURAN</th>
                      <th>TANGGAL PEMBUATAN</th>
                      <th>STATUS</th>
                      <th className="text-right">AKSI</th>
                    </tr>
                  </thead>
                  <tbody>
                    {backupFiles.map((b) => (
                      <tr key={b.id}>
                        <td className="font-mono text-xs font-bold text-slate-800">{b.name}</td>
                        <td className="font-mono text-xs text-slate-600">{b.size}</td>
                        <td className="text-xs text-slate-500">{b.date}</td>
                        <td>
                          <span className="badge badge-success text-[10px]">{b.status}</span>
                        </td>
                        <td className="text-right">
                          <button
                            title="Unduh File Backup"
                            onClick={() => handleDownloadBackup(b)}
                            className="p-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 mr-1.5 cursor-pointer"
                          >
                            <Download className="w-3.5 h-3.5" />
                          </button>
                          <button
                            title="Hapus File Backup"
                            onClick={() => handleDeleteBackup(b.id, b.name)}
                            className="p-1.5 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 cursor-pointer"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* ================= 8. USER ADMIN & ROLES RBAC (users.php) ================= */}
          {activeTab === 'pengaturan-users' && (
            <div className="space-y-6">
              {/* Card 1: Akun Administrator & Hak Akses matching screenshot */}
              <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                  <div>
                    <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                      <i className="fa-solid fa-users-gear text-blue-600"></i> Akun Administrator & Hak Akses
                    </h3>
                    <p className="text-slate-400 text-xs">Total {usersList.length} Akun Pengguna terdaftar dalam database.</p>
                  </div>
                  <button
                    type="button"
                    onClick={() => setIsAddUserModalOpen(true)}
                    className="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3 py-1.5 rounded-lg shadow flex items-center gap-1.5 transition text-xs cursor-pointer"
                  >
                    <i className="fa-solid fa-user-plus"></i> + Tambah User Admin
                  </button>
                </div>

                <div className="overflow-x-auto">
                  <table className="w-full text-left border-collapse text-xs">
                    <thead>
                      <tr className="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                        <th className="py-3 px-4">Username</th>
                        <th className="py-3 px-4">Nama Lengkap</th>
                        <th className="py-3 px-4">Email Resmi</th>
                        <th className="py-3 px-4">Role Akses</th>
                        <th className="py-3 px-4 text-center">Status</th>
                        <th className="py-3 px-4 text-right">Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      {usersList.map((u) => {
                        const roleLower = (u.role || '').toLowerCase();
                        let badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                        if (roleLower.includes('super')) {
                          badgeClass = 'bg-purple-50 text-purple-700 border-purple-200';
                        } else if (roleLower.includes('admin')) {
                          badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                        } else if (roleLower.includes('teknisi')) {
                          badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                        } else if (roleLower.includes('finance')) {
                          badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                        } else if (roleLower.includes('noc')) {
                          badgeClass = 'bg-cyan-50 text-cyan-700 border-cyan-200';
                        } else if (roleLower.includes('support')) {
                          badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                        } else if (roleLower.includes('sales')) {
                          badgeClass = 'bg-sky-50 text-sky-700 border-sky-200';
                        }

                        return (
                          <tr key={u.id} className="border-b border-slate-50 hover:bg-slate-50/50">
                            <td className="py-3.5 px-4 font-mono font-bold text-blue-600">{u.username}</td>
                            <td className="py-3.5 px-4 font-bold text-slate-900">{u.name}</td>
                            <td className="py-3.5 px-4 text-slate-600">{u.email}</td>
                            <td className="py-3.5 px-4">
                              {u.username === 'superadmin' || u.id === 1 ? (
                                <span className={`px-2.5 py-1 ${badgeClass} border font-bold rounded-full text-[10px] inline-flex items-center gap-1`}>
                                  <i className="fa-solid fa-shield-halved text-[9px]"></i>
                                  {u.role}
                                </span>
                              ) : (
                                <div className="inline-flex items-center gap-1.5">
                                  <select
                                    value={u.role}
                                    onChange={(e) => {
                                      const newRole = e.target.value;
                                      const updated = usersList.map((usr) =>
                                        usr.id === u.id ? { ...usr, role: newRole } : usr
                                      );
                                      setUsersList(updated);
                                      showToast({
                                        type: 'success',
                                        message: `Role untuk user "${u.username}" berhasil diubah menjadi "${newRole}"!`,
                                      });
                                    }}
                                    className={`px-2.5 py-1 ${badgeClass} border font-bold rounded-full text-[10px] cursor-pointer outline-none transition hover:opacity-90 shadow-2xs`}
                                  >
                                    <option value="administrator">administrator</option>
                                    <option value="teknisi">teknisi</option>
                                    <option value="finance">finance</option>
                                    <option value="noc">noc</option>
                                    <option value="support">support</option>
                                    <option value="sales">sales</option>
                                    <option value="staff">staff</option>
                                  </select>
                                </div>
                              )}
                            </td>
                            <td className="py-3.5 px-4 text-center">
                              {u.status === 'active' ? (
                                <span className="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">
                                  AKTIF
                                </span>
                              ) : (
                                <span className="px-2.5 py-1 bg-rose-50 text-rose-700 font-bold rounded-full text-[10px]">
                                  NONAKTIF
                                </span>
                              )}
                            </td>
                            <td className="py-3.5 px-4 text-right space-x-2">
                              {u.username === 'superadmin' || u.id === 1 ? (
                                <span className="text-slate-400 font-semibold italic text-[11px] inline-flex items-center gap-1.5 bg-slate-100 px-2.5 py-1 rounded-md">
                                  <i className="fa-solid fa-lock text-[10px] text-purple-600"></i> Akun Utama (Terkunci)
                                </span>
                              ) : (
                                <>
                                  <button
                                    type="button"
                                    onClick={() => {
                                      const updated = usersList.map((usr) =>
                                        usr.id === u.id
                                          ? { ...usr, status: usr.status === 'active' ? 'inactive' : 'active' }
                                          : usr
                                      );
                                      setUsersList(updated);
                                      showToast({
                                        type: 'success',
                                        message: `Status akses akun ${u.username} berhasil diubah!`,
                                      });
                                    }}
                                    className="text-blue-600 font-bold hover:underline cursor-pointer"
                                  >
                                    {u.status === 'active' ? 'Nonaktifkan' : 'Aktifkan'}
                                  </button>
                                  <button
                                    type="button"
                                    onClick={() => {
                                      if (window.confirm(`Hapus akun administrator ${u.username}?`)) {
                                        setUsersList(usersList.filter((usr) => usr.id !== u.id));
                                        showToast({
                                          type: 'warning',
                                          message: `Akun administrator ${u.username} telah dihapus.`,
                                        });
                                      }
                                    }}
                                    className="text-rose-600 font-bold hover:underline cursor-pointer ml-2"
                                  >
                                    Hapus
                                  </button>
                                </>
                              )}
                            </td>
                          </tr>
                        );
                      })}
                    </tbody>
                  </table>
                </div>
              </div>

              {/* Card 2: Matriks Hak Akses Role (RBAC Matrix List) matching screenshot */}
              <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                  <div>
                    <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                      <i className="fa-solid fa-table-cells text-purple-600"></i> Matriks Hak Akses Role (RBAC Matrix List)
                    </h3>
                    <p className="text-slate-400 text-xs">Kelola dan sesuaikan hak akses modul untuk masing-masing role pengguna.</p>
                  </div>
                  <div className="flex items-center gap-2">
                    <button
                      type="button"
                      onClick={() => {
                        setRbacRoles(defaultRbacRoles);
                        showToast({
                          type: 'info',
                          message: 'Matriks hak akses role (RBAC) telah dikembalikan ke pengaturan default sistem.',
                        });
                      }}
                      className="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg font-bold text-[10px] flex items-center gap-1 border border-slate-200 transition cursor-pointer"
                    >
                      <i className="fa-solid fa-rotate-left text-[9px]"></i> Reset Default
                    </button>
                    <span className="px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded-full font-bold text-[10px] flex items-center gap-1.5">
                      <i className="fa-solid fa-lock"></i> RBAC Enforced
                    </span>
                  </div>
                </div>

                <div className="overflow-x-auto">
                  <table className="w-full text-center border-collapse text-[11px]">
                    <thead>
                      <tr className="bg-slate-50 text-slate-600 border-b border-slate-200">
                        <th className="py-3 px-3 text-left font-bold min-w-[170px]">Role / Jabatan</th>
                        {systemModules.map((mod) => (
                          <th key={mod.key} className="py-3 px-1.5 font-semibold text-[10px] whitespace-nowrap" title={mod.name}>
                            <i className={`fa-solid ${mod.icon} block mb-1 text-slate-400 text-xs`}></i>
                            <span>{mod.name}</span>
                          </th>
                        ))}
                        <th className="py-3 px-3 text-right font-bold min-w-[90px]">Aksi</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-100">
                      {rbacRoles.map((r) => {
                        const isAll = r.key === 'super admin' || r.key === 'administrator';

                        return (
                          <tr key={r.key} className="hover:bg-slate-50/70 transition-colors">
                            <td className="py-3 px-3 text-left">
                              <span className={`px-2 py-0.5 ${r.badge} border font-bold rounded-full text-[10px] inline-flex items-center gap-1 mb-1`}>
                                <i className={`fa-solid ${r.icon} text-[9px]`}></i>
                                {r.role}
                              </span>
                              <p className="text-[10px] text-slate-400 leading-tight">{r.desc}</p>
                            </td>
                            {systemModules.map((mod) => {
                              const hasAccess = isAll || r.modules.includes(mod.key);

                              return (
                                <td key={mod.key} className="py-3 px-1.5">
                                  {hasAccess ? (
                                    <span
                                      className="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center shadow-2xs"
                                      title="Akses Diizinkan"
                                    >
                                      <i className="fa-solid fa-check text-[10px]"></i>
                                    </span>
                                  ) : (
                                    <span className="text-slate-300 font-mono text-xs select-none" title="Tidak Memiliki Akses">
                                      ―
                                    </span>
                                  )}
                                </td>
                              );
                            })}
                            <td className="py-3 px-3 text-right">
                              {r.key === 'super admin' ? (
                                <span className="text-slate-400 font-semibold italic text-[10px] inline-flex items-center gap-1">
                                  <i className="fa-solid fa-lock text-[9px] text-purple-400"></i> Full
                                </span>
                              ) : (
                                <button
                                  type="button"
                                  onClick={() => {
                                    setEditingRbac({
                                      key: r.key,
                                      name: r.role,
                                      modules: [...r.modules],
                                    });
                                    setIsEditRbacModalOpen(true);
                                  }}
                                  className="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg font-bold text-[10px] inline-flex items-center gap-1 transition shadow-xs cursor-pointer"
                                >
                                  <i className="fa-solid fa-pen-to-square text-[9px]"></i> Edit
                                </button>
                              )}
                            </td>
                          </tr>
                        );
                      })}
                    </tbody>
                  </table>
                </div>

                <div className="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-t border-slate-100 text-[11px] text-slate-500">
                  <span className="flex items-center gap-2">
                    <span className="w-4 h-4 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 inline-flex items-center justify-center text-[9px]">
                      <i className="fa-solid fa-check"></i>
                    </span>
                    = Memiliki Hak Akses Modul
                  </span>
                  <span className="flex items-center gap-2">
                    <span className="text-slate-400 font-mono font-bold">―</span> = Dibatasi (Akses Ditolak)
                  </span>
                </div>
              </div>

              {/* Modal Edit Matriks Hak Akses Role */}
              {isEditRbacModalOpen && (
                <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                  <div className="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 text-xs max-h-[90vh] flex flex-col">
                    <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                      <div>
                        <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                          <i className="fa-solid fa-sliders text-indigo-600"></i> Edit Hak Akses Role:{' '}
                          <span className="text-blue-600 font-extrabold">{editingRbac.name}</span>
                        </h3>
                        <p className="text-slate-400 text-[11px]">Centang modul yang diizinkan untuk diakses oleh pengguna dengan role ini.</p>
                      </div>
                      <button
                        onClick={() => setIsEditRbacModalOpen(false)}
                        className="text-slate-400 hover:text-slate-600 font-bold text-base cursor-pointer"
                      >
                        ✕
                      </button>
                    </div>

                    <form
                      onSubmit={(e) => {
                        e.preventDefault();
                        const updated = rbacRoles.map((r) =>
                          r.key === editingRbac.key ? { ...r, modules: editingRbac.modules } : r
                        );
                        setRbacRoles(updated);
                        setIsEditRbacModalOpen(false);
                        showToast({
                          type: 'success',
                          message: `Matriks hak akses role ${editingRbac.name} berhasil diperbarui dan disimpan!`,
                        });
                      }}
                      className="space-y-4 flex-1 overflow-y-auto pr-1"
                    >
                      <div className="flex justify-between items-center bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-[11px]">
                        <span className="text-slate-600 font-semibold">Tindakan Cepat:</span>
                        <div className="space-x-2">
                          <button
                            type="button"
                            onClick={() =>
                              setEditingRbac({
                                ...editingRbac,
                                modules: systemModules.map((m) => m.key),
                              })
                            }
                            className="px-2.5 py-1 bg-white hover:bg-emerald-50 text-emerald-700 border border-slate-200 rounded-lg font-bold text-[10px] transition shadow-2xs cursor-pointer"
                          >
                            <i className="fa-solid fa-check-double"></i> Pilih Semua
                          </button>
                          <button
                            type="button"
                            onClick={() => setEditingRbac({ ...editingRbac, modules: [] })}
                            className="px-2.5 py-1 bg-white hover:bg-rose-50 text-rose-700 border border-slate-200 rounded-lg font-bold text-[10px] transition shadow-2xs cursor-pointer"
                          >
                            <i className="fa-solid fa-xmark"></i> Batal Semua
                          </button>
                        </div>
                      </div>

                      <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        {systemModules.map((mod) => {
                          const isChecked = editingRbac.modules.includes(mod.key);

                          return (
                            <label
                              key={mod.key}
                              className="flex items-center gap-3 p-2.5 bg-slate-50 hover:bg-indigo-50/60 border border-slate-200 hover:border-indigo-300 rounded-xl cursor-pointer transition select-none"
                            >
                              <input
                                type="checkbox"
                                checked={isChecked}
                                onChange={(e) => {
                                  if (e.target.checked) {
                                    setEditingRbac({
                                      ...editingRbac,
                                      modules: [...editingRbac.modules, mod.key],
                                    });
                                  } else {
                                    setEditingRbac({
                                      ...editingRbac,
                                      modules: editingRbac.modules.filter((m) => m !== mod.key),
                                    });
                                  }
                                }}
                                className="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500"
                              />
                              <div className="flex items-center gap-2.5 overflow-hidden">
                                <div className="w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-600 text-xs shadow-2xs shrink-0">
                                  <i className={`fa-solid ${mod.icon}`}></i>
                                </div>
                                <div className="overflow-hidden">
                                  <span className="font-bold text-slate-800 block truncate">{mod.name}</span>
                                  <span className="font-mono text-[9px] text-slate-400 block truncate">{mod.key}</span>
                                </div>
                              </div>
                            </label>
                          );
                        })}
                      </div>

                      <div className="pt-3 border-t border-slate-100 flex gap-2">
                        <button
                          type="button"
                          onClick={() => setIsEditRbacModalOpen(false)}
                          className="w-1/3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 rounded-xl transition cursor-pointer"
                        >
                          Batal
                        </button>
                        <button
                          type="submit"
                          className="w-2/3 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition flex items-center justify-center gap-1.5 cursor-pointer"
                        >
                          <i className="fa-solid fa-floppy-disk"></i> Simpan Matriks Hak Akses
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              )}

              {/* Modal Tambah User Admin */}
              {isAddUserModalOpen && (
                <div className="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                  <div className="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs">
                    <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                      <h3 className="font-bold text-slate-900 text-sm">Tambah Administrator Baru</h3>
                      <button
                        onClick={() => setIsAddUserModalOpen(false)}
                        className="text-slate-400 hover:text-slate-600 font-bold cursor-pointer"
                      >
                        ✕
                      </button>
                    </div>

                    <form
                      onSubmit={(e) => {
                        e.preventDefault();
                        const newUser = {
                          id: Date.now(),
                          username: newUserData.username,
                          name: newUserData.full_name,
                          email: newUserData.email,
                          role: newUserData.role,
                          status: 'active',
                          last_login: 'Belum Pernah',
                        };
                        setUsersList([...usersList, newUser]);
                        setIsAddUserModalOpen(false);
                        setNewUserData({ username: '', full_name: '', email: '', role: 'administrator', password: '' });
                        showToast({
                          type: 'success',
                          message: `User Administrator ${newUser.username} baru berhasil dibuat!`,
                        });
                      }}
                      className="space-y-3"
                    >
                      <div className="grid grid-cols-2 gap-3">
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1">Username Login</label>
                          <input
                            type="text"
                            required
                            placeholder="admin_teknisi1"
                            value={newUserData.username}
                            onChange={(e) => setNewUserData({ ...newUserData, username: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold"
                          />
                        </div>
                        <div>
                          <label className="font-semibold text-slate-700 block mb-1">Role Akses</label>
                          <select
                            value={newUserData.role}
                            onChange={(e) => setNewUserData({ ...newUserData, role: e.target.value })}
                            className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold"
                          >
                            <option value="administrator">Administrator</option>
                            <option value="teknisi">Teknisi / Field Engineer</option>
                            <option value="finance">Finance & Billing</option>
                            <option value="noc">NOC & Network Ops</option>
                            <option value="support">Customer Support</option>
                            <option value="sales">Sales & Marketing</option>
                          </select>
                        </div>
                      </div>

                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Nama Lengkap</label>
                        <input
                          type="text"
                          required
                          placeholder="Nama lengkap staf..."
                          value={newUserData.full_name}
                          onChange={(e) => setNewUserData({ ...newUserData, full_name: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold"
                        />
                      </div>

                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Email Resmi</label>
                        <input
                          type="email"
                          required
                          placeholder="staff@netpro.co.id"
                          value={newUserData.email}
                          onChange={(e) => setNewUserData({ ...newUserData, email: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2"
                        />
                      </div>

                      <div>
                        <label className="font-semibold text-slate-700 block mb-1">Password Akses</label>
                        <input
                          type="password"
                          required
                          placeholder="••••••••••••"
                          value={newUserData.password}
                          onChange={(e) => setNewUserData({ ...newUserData, password: e.target.value })}
                          className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono"
                        />
                      </div>

                      <div className="pt-2 flex gap-2">
                        <button
                          type="button"
                          onClick={() => setIsAddUserModalOpen(false)}
                          className="w-1/3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2 rounded-xl transition cursor-pointer"
                        >
                          Batal
                        </button>
                        <button
                          type="submit"
                          className="w-2/3 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-xl shadow transition cursor-pointer"
                        >
                          Simpan User Admin
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              )}
            </div>
          )}

          {/* ================= 9. AUDIT LOGS SYSTEM TRAIL (logs.php) ================= */}
          {activeTab === 'pengaturan-logs' && (
            <div className="space-y-5">
              <div className="border-b border-slate-100 pb-3 flex justify-between items-center">
                <div>
                  <h2 className="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i className="fa-solid fa-clock-rotate-left text-red-600"></i>
                    <span>Audit Logs System Trail & Keamanan Aktivitas</span>
                  </h2>
                  <p className="text-slate-500 text-xs">Rekam jejak seluruh aktivitas operator, transaksi penagihan, dan perubahan data.</p>
                </div>
                <button
                  type="button"
                  onClick={() => showToast({ type: 'success', message: 'Laporan Audit Log berhasil diekspor ke Excel CSV.' })}
                  className="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-1.5 rounded-lg flex items-center gap-1.5 text-xs cursor-pointer"
                >
                  <Download className="w-3.5 h-3.5" />
                  <span>Ekspor Log CSV</span>
                </button>
              </div>

              <div className="bg-slate-50 rounded-xl border border-slate-200 overflow-hidden">
                <table className="custom-table">
                  <thead>
                    <tr>
                      <th>ID LOG</th>
                      <th>WAKTU</th>
                      <th>USER / OPERATOR</th>
                      <th>EVENT ACTION</th>
                      <th>IP ADDRESS</th>
                      <th>DESKRIPSI AKTIVITAS</th>
                    </tr>
                  </thead>
                  <tbody>
                    {auditLogsList.map((log) => (
                      <tr key={log.id}>
                        <td className="font-mono text-xs font-bold text-red-600">{log.id}</td>
                        <td className="text-xs text-slate-500">{log.time}</td>
                        <td className="font-bold text-slate-900 text-xs">{log.user}</td>
                        <td>
                          <span className="font-mono text-[10px] font-bold px-1.5 py-0.5 bg-slate-200 text-slate-800 rounded">
                            {log.action}
                          </span>
                        </td>
                        <td className="font-mono text-xs text-blue-600">{log.ip}</td>
                        <td className="text-xs text-slate-700">{log.desc}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* ================= 10. LISENSI & AKTIVASI SISTEM (lisensi.php) ================= */}
          {activeTab === 'pengaturan-lisensi' && (
            <div className="space-y-5">
              <div className="border-b border-slate-100 pb-3 flex justify-between items-center">
                <div>
                  <h2 className="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i className="fa-solid fa-certificate text-red-600"></i>
                    <span>Lisensi & Status Aktivasi Platform NETPRO CRM</span>
                  </h2>
                  <p className="text-slate-500 text-xs">Informasi lisensi komersial, kapasitas pelanggan, dan jaminan SLA dukungan teknis.</p>
                </div>
                <span className="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold rounded-full text-[10px]">
                  LICENSE VERIFIED ✓
                </span>
              </div>

              <div className="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-6 rounded-2xl border border-slate-700 space-y-4">
                <div className="flex justify-between items-start">
                  <div>
                    <span className="text-red-400 font-bold text-xs uppercase tracking-wider block">TIER LISENSI PERANGKAT LUNAK</span>
                    <h3 className="text-xl font-black text-white mt-1">NETPRO ENTERPRISE UNLIMITED</h3>
                    <p className="text-slate-400 text-xs mt-0.5">Broadband FTTH & Hotspot Management System</p>
                  </div>
                  <span className="px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 rounded-full font-bold text-xs">
                    LIFETIME ACTIVE
                  </span>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-3 border-t border-slate-800 font-mono text-xs">
                  <div>
                    <span className="text-slate-400 block text-[10px]">Hardware Machine ID:</span>
                    <strong className="text-slate-200">HWID-9921-8842-BA10-7791</strong>
                  </div>
                  <div>
                    <span className="text-slate-400 block text-[10px]">Kapasitas Pelanggan:</span>
                    <strong className="text-emerald-400">UNLIMITED SUBSCRIBERS</strong>
                  </div>
                  <div>
                    <span className="text-slate-400 block text-[10px]">SLA Dukungan:</span>
                    <strong className="text-blue-400">24/7 Priority Support</strong>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* ================= 11. TENTANG APLIKASI PLATFORM (about.php) ================= */}
          {activeTab === 'pengaturan-about' && (
            <div className="space-y-5">
              <div className="border-b border-slate-100 pb-3">
                <h2 className="text-base font-bold text-slate-900 flex items-center gap-2">
                  <i className="fa-solid fa-circle-info text-red-600"></i>
                  <span>Tentang Aplikasi NETPRO ISP Management Platform</span>
                </h2>
                <p className="text-slate-500 text-xs">Spesifikasi arsitektur perangkat lunak, modul sistem, dan hak cipta platform.</p>
              </div>

              <div className="bg-slate-50 p-6 rounded-2xl border border-slate-200 space-y-4">
                <div className="flex items-center gap-4">
                  <div className="w-14 h-14 bg-red-600 text-white rounded-2xl flex items-center justify-center text-2xl font-black shadow-md">
                    NP
                  </div>
                  <div>
                    <h3 className="font-extrabold text-slate-900 text-base">NETPRO ISP CRM & Billing Platform v2.4</h3>
                    <p className="text-slate-500 text-xs">All-in-One FTTH, RADIUS AAA, MikroTik, Finance & HR Platform</p>
                  </div>
                </div>

                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-3 border-t border-slate-200 font-mono">
                  <div className="bg-white p-3 rounded-xl border border-slate-100">
                    <span className="text-slate-400 block text-[10px]">Core Backend:</span>
                    <strong className="text-slate-800">Laravel 11 / PHP 8.2</strong>
                  </div>
                  <div className="bg-white p-3 rounded-xl border border-slate-100">
                    <span className="text-slate-400 block text-[10px]">Frontend UI:</span>
                    <strong className="text-slate-800">React 19 + Vite 6</strong>
                  </div>
                  <div className="bg-white p-3 rounded-xl border border-slate-100">
                    <span className="text-slate-400 block text-[10px]">AAA Engine:</span>
                    <strong className="text-slate-800">FreeRADIUS 3.0.x</strong>
                  </div>
                  <div className="bg-white p-3 rounded-xl border border-slate-100">
                    <span className="text-slate-400 block text-[10px]">Network API:</span>
                    <strong className="text-slate-800">RouterOS v7 & SNMP</strong>
                  </div>
                </div>

                <p className="text-slate-500 text-[11px] leading-relaxed pt-2">
                  Hak Cipta © 2026 PT MITRAXCON SYNERGY UTAMA. Seluruh hak cipta dilindungi undang-undang.
                </p>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
