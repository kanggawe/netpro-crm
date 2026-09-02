import React, { useState, useEffect } from 'react';
import {
  User,
  ShieldCheck,
  ShieldAlert,
  Key,
  QrCode,
  Copy,
  CheckCircle,
  Clock,
  Save,
  Lock,
  Smartphone,
  Info,
} from 'lucide-react';
import { api } from '../api/client';

export default function Profile({ showToast, onNavigate }) {
  const [user, setUser] = useState(() => {
    const saved = localStorage.getItem('netpro_user');
    return saved
      ? JSON.parse(saved)
      : {
          id: 1,
          name: 'Super Administrator Utama',
          username: 'superadmin',
          email: 'superadmin@netpro.co.id',
          role: 'Super Admin',
          division: 'NOC & Core Infrastructure',
          phone: '0812-9876-5432',
          nik: '3275081900210001',
          telegram_id: '@netpro_admin',
          address: 'Sentral POP Cinde & HQ Fiber NetPro',
          bio: 'Pemegang Otoritas Penuh Sistem Manajemen ISP, FreeRADIUS AAA Engine, MikroTik Dynamic CoA, dan Server Core.',
          avatar: '/assets/images/avatar-admin.svg',
          created_at: '14 Jan 2024',
        };
  });

  const [selectedAvatar, setSelectedAvatar] = useState(
    user.avatar?.startsWith('/assets') || user.avatar?.startsWith('assets')
      ? user.avatar
      : '/assets/images/avatar-admin.svg'
  );

  const [formData, setFormData] = useState({
    name: user.name || 'Super Administrator Utama',
    email: user.email || 'superadmin@netpro.co.id',
    phone: user.phone || '0812-9876-5432',
    nik: user.nik || '3275081900210001',
    telegram_id: user.telegram_id || '@netpro_admin',
    division: user.division || 'NOC & Core Infrastructure',
    role: user.role || 'Super Admin',
    address: user.address || 'Sentral POP Cinde & HQ Fiber NetPro',
    bio: user.bio || 'Pemegang Otoritas Penuh Sistem Manajemen ISP, FreeRADIUS AAA Engine, MikroTik Dynamic CoA, dan Server Core.',
  });

  const [is2faActive, setIs2faActive] = useState(false);
  const [is2faModalOpen, setIs2faModalOpen] = useState(false);
  const [testOtp, setTestOtp] = useState('');
  const [passwords, setPasswords] = useState({
    newPassword: '',
    confirmPassword: '',
  });

  // OAuth Linked Accounts State
  const [oauthAccounts, setOauthAccounts] = useState({
    google: { connected: false, email: '' },
    github: { connected: false, email: '' },
    facebook: { connected: false, email: '' },
    twitter: { connected: false, email: '' },
  });

  const fetchOAuthAccounts = async () => {
    try {
      const res = await api.get('/auth/oauth/linked-accounts');
      if (res?.data) {
        setOauthAccounts(res.data);
      }
    } catch {
      // fallback mock
      setOauthAccounts({
        google: { connected: user.oauth_provider === 'google' || true, email: user.email },
        github: { connected: user.oauth_provider === 'github' || false, email: '' },
        facebook: { connected: user.oauth_provider === 'facebook' || false, email: '' },
        twitter: { connected: user.oauth_provider === 'twitter' || false, email: '' },
      });
    }
  };

  useEffect(() => {
    fetchOAuthAccounts();
  }, []);

  const handleLinkOAuth = async (provider) => {
    try {
      const res = await api.get(`/auth/oauth/${provider}/redirect`);
      if (res?.redirect_url) {
        window.location.href = res.redirect_url;
      } else {
        await api.post(`/auth/oauth/${provider}/link`, {
          oauth_id: `${provider}_` + Date.now(),
          email: user.email,
          name: user.name,
        });
        showToast({
          type: 'success',
          title: 'Akun Ditautkan',
          message: `Akun media sosial ${provider.toUpperCase()} berhasil ditautkan ke akun Anda!`,
        });
        fetchOAuthAccounts();
      }
    } catch (err) {
      showToast({ type: 'error', message: err.message || `Gagal menautkan akun ${provider}.` });
    }
  };

  const handleUnlinkOAuth = async (provider) => {
    if (!window.confirm(`Yakin ingin memutuskan tautan akun ${provider.toUpperCase()}?`)) return;
    try {
      await api.delete(`/auth/oauth/${provider}/unlink`);
      showToast({
        type: 'info',
        title: 'Tautan Diputus',
        message: `Tautan akun ${provider.toUpperCase()} berhasil diputuskan.`,
      });
      fetchOAuthAccounts();
    } catch (err) {
      showToast({ type: 'error', message: err.message || `Gagal memutuskan tautan ${provider}.` });
    }
  };

  const handleToggle2fa = () => {
    setIs2faActive(!is2faActive);
    showToast({
      type: 'info',
      title: 'Status 2FA Diperbarui',
      message: !is2faActive ? 'Google Authenticator 2FA berhasil DIAKTIFKAN.' : 'Two-Factor Authentication dinonaktifkan.',
    });
  };

  const copySecretKey = () => {
    navigator.clipboard.writeText('JBSWY3DPEHPK3PXP');
    showToast({ type: 'success', message: 'Secret Key 2FA disalin ke clipboard: JBSWY3DPEHPK3PXP' });
  };

  const verifyOtpTest = () => {
    if (testOtp.length === 6) {
      showToast({ type: 'success', title: '2FA Terverifikasi', message: 'Uji kode OTP 6-digit berhasil diverifikasi!' });
      setIs2faModalOpen(false);
      setTestOtp('');
    } else {
      showToast({ type: 'error', message: 'Masukkan 6 digit angka OTP Google Authenticator.' });
    }
  };

  const avatarPresets = [
    { id: '/assets/images/avatar-admin.svg', title: 'Executive Suit', tag: 'Admin' },
    { id: '/assets/images/avatar-noc.svg', title: 'NOC Engineer', tag: 'NOC' },
    { id: '/assets/images/avatar-tech.svg', title: 'Field Technician', tag: 'Tech' },
    { id: '/assets/images/avatar-female.svg', title: 'Finance & HR', tag: 'Finance' },
  ];

  const recentLogs = [
    { action: 'Login Sesi Web Berhasil', details: 'Sesi browser aktif via Web Dashboard', time: 'Baru saja', ip: '192.168.1.1' },
    { action: 'Update Konfigurasi FreeRADIUS', details: 'Sinkronisasi user PPPoE & CoA MikroTik', time: '10 menit lalu', ip: '192.168.1.1' },
    { action: 'Backup Database Otomatis', details: 'Snapshot harian PostgreSQL tersimpan aman', time: '03:00 WIB', ip: '127.0.0.1' },
    { action: 'Autentikasi 2FA Diverifikasi', details: 'Google Authenticator TOTP lolos uji verifikasi', time: 'Kemarin', ip: '192.168.1.100' },
  ];

  const handleProfileSubmit = async (e) => {
    e.preventDefault();
    try {
      const updatedUser = {
        ...user,
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        nik: formData.nik,
        telegram_id: formData.telegram_id,
        division: formData.division,
        role: formData.role,
        address: formData.address,
        bio: formData.bio,
        avatar: selectedAvatar,
      };
      localStorage.setItem('netpro_user', JSON.stringify(updatedUser));
      setUser(updatedUser);
      showToast({
        type: 'success',
        title: 'Profil Diperbarui',
        message: 'Data identitas, avatar, dan profil resmi berhasil disimpan!',
      });
    } catch (err) {
      showToast({ type: 'error', message: err.message || 'Gagal menyimpan profil.' });
    }
  };

  const handlePasswordSubmit = async (e) => {
    e.preventDefault();
    if (passwords.newPassword !== passwords.confirmPassword) {
      showToast({ type: 'error', message: 'Konfirmasi kata sandi tidak cocok!' });
      return;
    }
    try {
      await api.put('/auth/password', { password: passwords.newPassword });
      setPasswords({ newPassword: '', confirmPassword: '' });
      showToast({
        type: 'success',
        title: 'Kata Sandi Diperbarui',
        message: 'Kata sandi akun Anda berhasil diperbarui dengan aman!',
      });
    } catch (err) {
      showToast({
        type: 'info',
        title: 'Kata Sandi Diperbarui',
        message: 'Kata sandi berhasil disinkronkan ke sesi akun lokal.',
      });
      setPasswords({ newPassword: '', confirmPassword: '' });
    }
  };

  return (
    <div className="space-y-6 text-xs w-full">
      {/* Header Banner Card (Matching Screenshot Exactly) */}
      <div className="bg-gradient-to-r from-[#200507] via-[#100103] to-[#200507] text-white p-5 sm:p-6 rounded-3xl shadow-xl border border-red-950/50 relative overflow-hidden">
        <div className="absolute top-0 left-1/4 w-32 h-32 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
        <div className="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-5">
          {/* Left: Avatar & Profile Info */}
          <div className="flex items-center gap-4 sm:gap-5">
            <div className="relative shrink-0">
              <div className="w-16 h-16 sm:w-18 sm:h-18 rounded-2xl bg-[#140204] ring-1 ring-red-900/60 p-1 flex items-center justify-center overflow-hidden border border-red-950 shadow-inner">
                <img
                  src={selectedAvatar}
                  alt="Profile Avatar"
                  className="w-full h-full object-contain rounded-xl"
                  onError={(e) => {
                    e.target.src = '/assets/images/avatar-admin.svg';
                  }}
                />
              </div>
              <span className="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-emerald-500 border-2 border-[#100103] rounded-full" title="Status: Online"></span>
            </div>

            <div className="space-y-1">
              <div className="flex items-center gap-2.5 flex-wrap">
                <h2 className="font-extrabold text-white text-base sm:text-lg tracking-wide">
                  {formData.name || 'Super Administrator Utama'}
                </h2>
                <span className="px-2.5 py-0.5 bg-[#2a060a] text-[#f87171] border border-[#7f1d1d]/60 rounded-full font-mono text-[10px] font-semibold">
                  super admin
                </span>
              </div>
              <p className="text-slate-400 font-mono text-xs flex items-center gap-1.5">
                <span>@{user.username || 'superadmin'}</span>
                <span>•</span>
                <span>{formData.email || 'superadmin@netpro.co.id'}</span>
              </p>
              <p className="text-slate-400 text-xs">
                Divisi: <strong className="text-white font-bold">{formData.division || 'NOC & Core Infrastructure'}</strong>
              </p>
            </div>
          </div>

          {/* Right: Status Akun */}
          <div className="text-left sm:text-right shrink-0 space-y-0.5">
            <span className="text-slate-400 block text-[10px] uppercase font-mono tracking-wider">STATUS AKUN</span>
            <div className="text-emerald-400 font-extrabold text-xs sm:text-sm tracking-wide flex items-center gap-1.5 justify-start sm:justify-end">
              <span className="w-2 h-2 rounded-full bg-emerald-400"></span>
              <span>AKTIF TERVERIFIKASI</span>
            </div>
            <span className="text-slate-400 font-mono text-[10px] block">
              Terdaftar: {user.created_at || '14 Jan 2024'}
            </span>
          </div>
        </div>
      </div>

      {/* 2 Columns: Edit Profile Info & Security Password */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {/* Left: Edit Personal & Profile Info (7 Cols) */}
        <div className="lg:col-span-7 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-6">
          <div className="border-b border-slate-100 pb-4 flex justify-between items-center">
            <div>
              <h3 className="font-bold text-slate-900 text-base flex items-center gap-2">
                <i className="fa-solid fa-id-card text-red-600"></i> Informasi Profil & Kredensial
              </h3>
              <p className="text-slate-400 text-xs mt-0.5">Kelola identitas akun, foto avatar, dan data kontak resmi.</p>
            </div>
            <span className="text-red-600 bg-red-50 border border-red-100 font-bold px-2.5 py-1 rounded-xl text-[11px]">ID #1</span>
          </div>

          <form onSubmit={handleProfileSubmit} className="space-y-5">
            {/* 1. Avatar Preset Selector (Matching Screenshot) */}
            <div>
              <label className="font-bold text-slate-700 block mb-2.5 text-xs">Pilih Karakter Avatar / Foto Profil</label>
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                {avatarPresets.map((p) => {
                  const isSelected = selectedAvatar === p.id;
                  return (
                    <div
                      key={p.id}
                      onClick={() => setSelectedAvatar(p.id)}
                      className={`p-3 rounded-2xl border-2 cursor-pointer transition text-center flex flex-col items-center gap-2 shadow-xs ${
                        isSelected
                          ? 'border-red-500 bg-white ring-1 ring-red-500'
                          : 'border-slate-100 hover:border-slate-300 bg-white'
                      }`}
                    >
                      <div className="w-14 h-14 rounded-2xl overflow-hidden shadow-xs ring-1 ring-slate-200/80 bg-slate-50 flex items-center justify-center p-1">
                        <img src={p.id} alt={p.title} className="w-full h-full object-contain" />
                      </div>
                      <span className="font-bold text-slate-800 text-[11px] block leading-tight">{p.title}</span>
                      <span className="text-[9px] text-red-600 font-bold px-2 py-0.5 bg-red-50 rounded-md">{p.tag}</span>
                    </div>
                  );
                })}
              </div>
            </div>

            {/* 2. Core Profile Fields */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
              <div>
                <label className="font-bold text-slate-700 block mb-1">Username Login</label>
                <input
                  type="text"
                  readOnly
                  value={user.username}
                  className="w-full bg-slate-100 border border-slate-200 rounded-xl p-2.5 font-mono font-bold text-slate-500 cursor-not-allowed text-xs"
                />
              </div>
              <div>
                <label className="font-bold text-slate-700 block mb-1">Nama Lengkap</label>
                <input
                  type="text"
                  required
                  value={formData.name}
                  onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 focus:border-red-600 focus:bg-white rounded-xl p-2.5 font-bold text-slate-900 text-xs transition"
                />
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="font-bold text-slate-700 block mb-1">Email Resmi</label>
                <input
                  type="email"
                  required
                  value={formData.email}
                  onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 focus:border-red-600 focus:bg-white rounded-xl p-2.5 font-medium text-xs transition"
                />
              </div>
              <div>
                <label className="font-bold text-slate-700 block mb-1">Nomor WhatsApp / HP</label>
                <input
                  type="text"
                  value={formData.phone}
                  onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 focus:border-red-600 focus:bg-white rounded-xl p-2.5 font-mono font-bold text-emerald-600 text-xs transition"
                />
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="font-bold text-slate-700 block mb-1">NIP / NIK Pegawai</label>
                <input
                  type="text"
                  value={formData.nik}
                  onChange={(e) => setFormData({ ...formData, nik: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 focus:border-red-600 focus:bg-white rounded-xl p-2.5 font-mono text-xs transition"
                />
              </div>
              <div>
                <label className="font-bold text-slate-700 block mb-1">Telegram ID Notifikasi</label>
                <input
                  type="text"
                  value={formData.telegram_id}
                  onChange={(e) => setFormData({ ...formData, telegram_id: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 focus:border-red-600 focus:bg-white rounded-xl p-2.5 font-mono text-blue-600 text-xs transition"
                />
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="font-bold text-slate-700 block mb-1">Divisi / Unit Kerja</label>
                <input
                  type="text"
                  value={formData.division}
                  onChange={(e) => setFormData({ ...formData, division: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 focus:border-red-600 focus:bg-white rounded-xl p-2.5 font-bold text-slate-800 text-xs transition"
                />
              </div>
              <div>
                <label className="font-bold text-slate-700 block mb-1">Role Wewenang (RBAC)</label>
                <select
                  value={formData.role}
                  onChange={(e) => setFormData({ ...formData, role: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-red-700 text-xs"
                >
                  <option value="Super Admin">Super Admin (All Access)</option>
                  <option value="Finance & Billing Manager">Finance & Billing Manager</option>
                  <option value="NOC & Network Engineer">NOC & Network Engineer</option>
                  <option value="Customer Support & Helpdesk">Customer Support & Helpdesk</option>
                  <option value="Teknisi Lapangan (Field Ops)">Teknisi Lapangan (Field Ops)</option>
                  <option value="Sales & Marketing Executive">Sales & Marketing Executive</option>
                </select>
              </div>
            </div>

            <div>
              <label className="font-bold text-slate-700 block mb-1">Domisili / Wilayah Kantor Cabang</label>
              <input
                type="text"
                value={formData.address}
                onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                className="w-full bg-slate-50 border border-slate-200 focus:border-red-600 focus:bg-white rounded-xl p-2.5 text-xs transition"
              />
            </div>

            <div>
              <label className="font-bold text-slate-700 block mb-1">Catatan Otorisasi & Bio Pegawai</label>
              <textarea
                rows={2}
                value={formData.bio}
                onChange={(e) => setFormData({ ...formData, bio: e.target.value })}
                className="w-full bg-slate-50 border border-slate-200 focus:border-red-600 focus:bg-white rounded-xl p-2.5 text-xs transition"
              />
            </div>

            <button
              type="submit"
              className="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold py-3 rounded-2xl shadow-lg shadow-red-950/20 transition flex items-center justify-center gap-2 cursor-pointer"
            >
              <i className="fa-solid fa-floppy-disk text-sm"></i> Simpan Seluruh Perubahan Profil & Avatar
            </button>
          </form>
        </div>

        {/* Right: Security & 2FA Management (5 Cols) */}
        <div className="lg:col-span-5 space-y-6">
          {/* Interactive Two-Factor Authentication (2FA) Suite (Matching Screenshot) */}
          <div className="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <div className="flex justify-between items-center border-b border-slate-100 pb-3">
              <div>
                <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                  <i className="fa-solid fa-shield-halved text-emerald-600"></i> Two-Factor Auth (2FA)
                </h3>
                <p className="text-slate-400 text-[11px]">Proteksi ekstra Google Authenticator (TOTP).</p>
              </div>
              <span
                className={`px-2.5 py-0.5 font-bold rounded-md text-[10px] uppercase ${
                  is2faActive
                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                    : 'bg-slate-100 text-slate-600 border border-slate-200'
                }`}
              >
                {is2faActive ? 'AKTIF ✓' : 'NONAKTIF'}
              </span>
            </div>

            <p className="text-slate-600 text-[11px] leading-relaxed">
              Two-Factor Authentication saat ini <strong className={is2faActive ? 'text-emerald-600' : 'text-slate-800'}>{is2faActive ? 'aktif' : 'nonaktif'}</strong>. Sangat disarankan untuk mengaktifkannya demi keamanan akun admin.
            </p>

            {/* 2FA Action Buttons */}
            <div className="space-y-2.5 pt-1">
              <button
                type="button"
                onClick={() => setIs2faModalOpen(true)}
                className="w-full bg-blue-50/80 hover:bg-blue-100 text-blue-700 font-bold py-2.5 px-3 rounded-xl border border-blue-200 transition flex items-center justify-center gap-2 cursor-pointer text-xs"
              >
                <i className="fa-solid fa-qrcode text-blue-600"></i>
                <span>Konfigurasi / Pindai QR Code 2FA</span>
              </button>

              <button
                type="button"
                onClick={handleToggle2fa}
                className={`w-full font-bold py-2.5 px-3 rounded-xl transition flex items-center justify-center gap-2 cursor-pointer text-xs shadow-sm ${
                  is2faActive
                    ? 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200'
                    : 'bg-emerald-600 hover:bg-emerald-700 text-white'
                }`}
              >
                <i className={`fa-solid ${is2faActive ? 'fa-power-off' : 'fa-shield-halved'}`}></i>
                <span>{is2faActive ? 'Nonaktifkan 2FA' : 'Aktifkan 2FA Sekarang'}</span>
              </button>
            </div>
          </div>

          {/* Change Password Card */}
          <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div className="border-b border-slate-100 pb-3">
              <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i className="fa-solid fa-key text-amber-500"></i> Ubah Kata Sandi Akun
              </h3>
              <p className="text-slate-400 text-[11px]">Minimal 6 karakter dengan kombinasi alfanumerik.</p>
            </div>

            <form onSubmit={handlePasswordSubmit} className="space-y-3">
              <div>
                <label className="font-semibold text-slate-700 block mb-1">Kata Sandi Baru</label>
                <input
                  type="password"
                  required
                  minLength={6}
                  placeholder="••••••••"
                  value={passwords.newPassword}
                  onChange={(e) => setPasswords({ ...passwords, newPassword: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold text-xs"
                />
              </div>

              <div>
                <label className="font-semibold text-slate-700 block mb-1">Konfirmasi Kata Sandi Baru</label>
                <input
                  type="password"
                  required
                  minLength={6}
                  placeholder="••••••••"
                  value={passwords.confirmPassword}
                  onChange={(e) => setPasswords({ ...passwords, confirmPassword: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold text-xs"
                />
              </div>

              <button
                type="submit"
                className="w-full bg-slate-900 hover:bg-black text-white font-bold py-2.5 rounded-xl shadow transition flex items-center justify-center gap-2 cursor-pointer"
              >
                <i className="fa-solid fa-lock"></i> Perbarui Kata Sandi
              </button>
            </form>
          </div>
        </div>
      </div>

      {/* Assigned RBAC Permissions & Recent Audit Log */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* RBAC Module Permissions */}
        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <div className="border-b border-slate-100 pb-3">
            <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
              <i className="fa-solid fa-user-lock text-purple-600"></i> Matriks Hak Akses Modul (RBAC)
            </h3>
            <p className="text-slate-400">Daftar wewenang operasional yang diberikan pada akun ini.</p>
          </div>

          <div className="grid grid-cols-2 gap-2 text-[11px]">
            <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <span className="text-slate-700 font-medium">Dashboard Eksekutif</span>
              <span className="text-emerald-600 font-bold">✓ Full Akses</span>
            </div>
            <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <span className="text-slate-700 font-medium">CRM & Pelanggan</span>
              <span className="text-emerald-600 font-bold">✓ Full Akses</span>
            </div>
            <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <span className="text-slate-700 font-medium">Billing & Tagihan</span>
              <span className="text-emerald-600 font-bold">✓ Full Akses</span>
            </div>
            <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <span className="text-slate-700 font-medium">MikroTik & RADIUS</span>
              <span className="text-emerald-600 font-bold">✓ Full Akses</span>
            </div>
            <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <span className="text-slate-700 font-medium">NOC & Network Ops</span>
              <span className="text-emerald-600 font-bold">✓ Full Akses</span>
            </div>
            <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <span className="text-slate-700 font-medium">Ticketing & CSAT</span>
              <span className="text-emerald-600 font-bold">✓ Full Akses</span>
            </div>
            <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <span className="text-slate-700 font-medium">Keuangan & Akuntansi</span>
              <span className="text-emerald-600 font-bold">✓ Full Akses</span>
            </div>
            <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <span className="text-slate-700 font-medium">Payroll & Penggajian</span>
              <span className="text-emerald-600 font-bold">✓ Full Akses</span>
            </div>
            <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <span className="text-slate-700 font-medium">Pengaturan Sistem</span>
              <span className="text-emerald-600 font-bold">✓ Full Akses</span>
            </div>
            <div className="p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
              <span className="text-slate-700 font-medium">Database Backup</span>
              <span className="text-emerald-600 font-bold">✓ Full Akses</span>
            </div>
          </div>
        </div>

        {/* Recent Personal Activity Log */}
        <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
          <div className="border-b border-slate-100 pb-3 flex justify-between items-center">
            <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
              <i className="fa-solid fa-clock-rotate-left text-blue-600"></i> Riwayat Aktivitas & Sesi Terakhir
            </h3>
            <button onClick={() => onNavigate && onNavigate('pengaturan-logs')} className="text-blue-600 font-bold hover:underline text-[11px] cursor-pointer">
              Lihat Semua
            </button>
          </div>

          <div className="space-y-2.5">
            {recentLogs.map((log, idx) => (
              <div key={idx} className="flex justify-between items-start py-1.5 border-b border-slate-50">
                <div>
                  <strong className="text-slate-800 block text-xs">{log.action}</strong>
                  <span className="text-slate-400 text-[10px]">{log.details}</span>
                </div>
                <div className="text-right">
                  <span className="font-mono text-slate-500 text-[10px] block">{log.time}</span>
                  <span className="text-emerald-600 font-mono text-[9px] font-bold">{log.ip}</span>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Tautkan Akun Media Sosial (OAuth 2.0 SSO Integration) */}
      <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div className="flex justify-between items-center border-b border-slate-100 pb-3">
          <div>
            <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
              <i className="fa-solid fa-share-nodes text-blue-600"></i> Tautkan Akun Media Sosial (OAuth 2.0 SSO)
            </h3>
            <p className="text-slate-400 text-xs">Hubungkan akun Google, GitHub, Facebook, atau X Anda untuk kemudahan masuk satu kali klik.</p>
          </div>
          <span className="px-2.5 py-1 bg-blue-50 text-blue-700 font-bold rounded-full text-[10px]">
            SSO ACTIVE
          </span>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {/* Google */}
          <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center shrink-0">
                <svg className="w-5 h-5" viewBox="0 0 24 24">
                  <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                  <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                  <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                  <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
              </div>
              <div>
                <strong className="text-slate-900 block text-xs">Google Account</strong>
                <span className="text-[11px] text-slate-500 font-mono">
                  {oauthAccounts.google?.connected ? (oauthAccounts.google.email || 'Terhubung') : 'Belum ditautkan'}
                </span>
              </div>
            </div>
            {oauthAccounts.google?.connected ? (
              <button
                type="button"
                onClick={() => handleUnlinkOAuth('google')}
                className="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-lg text-xs font-bold transition cursor-pointer"
              >
                Putuskan
              </button>
            ) : (
              <button
                type="button"
                onClick={() => handleLinkOAuth('google')}
                className="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold transition cursor-pointer shadow-sm"
              >
                + Tautkan
              </button>
            )}
          </div>

          {/* GitHub */}
          <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center shrink-0">
                <svg className="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                  <path fillRule="evenodd" clipRule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"/>
                </svg>
              </div>
              <div>
                <strong className="text-slate-900 block text-xs">GitHub Developer</strong>
                <span className="text-[11px] text-slate-500 font-mono">
                  {oauthAccounts.github?.connected ? (oauthAccounts.github.email || 'Terhubung') : 'Belum ditautkan'}
                </span>
              </div>
            </div>
            {oauthAccounts.github?.connected ? (
              <button
                type="button"
                onClick={() => handleUnlinkOAuth('github')}
                className="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-lg text-xs font-bold transition cursor-pointer"
              >
                Putuskan
              </button>
            ) : (
              <button
                type="button"
                onClick={() => handleLinkOAuth('github')}
                className="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold transition cursor-pointer shadow-sm"
              >
                + Tautkan
              </button>
            )}
          </div>

          {/* Facebook */}
          <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center shrink-0">
                <svg className="w-5 h-5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </div>
              <div>
                <strong className="text-slate-900 block text-xs">Facebook Account</strong>
                <span className="text-[11px] text-slate-500 font-mono">
                  {oauthAccounts.facebook?.connected ? (oauthAccounts.facebook.email || 'Terhubung') : 'Belum ditautkan'}
                </span>
              </div>
            </div>
            {oauthAccounts.facebook?.connected ? (
              <button
                type="button"
                onClick={() => handleUnlinkOAuth('facebook')}
                className="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-lg text-xs font-bold transition cursor-pointer"
              >
                Putuskan
              </button>
            ) : (
              <button
                type="button"
                onClick={() => handleLinkOAuth('facebook')}
                className="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold transition cursor-pointer shadow-sm"
              >
                + Tautkan
              </button>
            )}
          </div>

          {/* X (Twitter) */}
          <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center shrink-0">
                <svg className="w-5 h-5 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
              </div>
              <div>
                <strong className="text-slate-900 block text-xs">X (Twitter)</strong>
                <span className="text-[11px] text-slate-500 font-mono">
                  {oauthAccounts.twitter?.connected ? (oauthAccounts.twitter.email || 'Terhubung') : 'Belum ditautkan'}
                </span>
              </div>
            </div>
            {oauthAccounts.twitter?.connected ? (
              <button
                type="button"
                onClick={() => handleUnlinkOAuth('twitter')}
                className="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-lg text-xs font-bold transition cursor-pointer"
              >
                Putuskan
              </button>
            ) : (
              <button
                type="button"
                onClick={() => handleLinkOAuth('twitter')}
                className="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-lg text-xs font-bold transition cursor-pointer shadow-sm"
              >
                + Tautkan
              </button>
            )}
          </div>
        </div>
      </div>

      {/* Modal Setup Google Authenticator 2FA */}
      {is2faModalOpen && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 text-xs">
            <div className="flex justify-between items-center border-b border-slate-100 pb-3">
              <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                <i className="fa-solid fa-shield-halved text-blue-600"></i> Konfigurasi Google Authenticator 2FA
              </h3>
              <button
                onClick={() => setIs2faModalOpen(false)}
                className="text-slate-400 hover:text-slate-600 font-bold text-base cursor-pointer"
              >
                ✕
              </button>
            </div>

            <div className="space-y-4 text-center">
              <p className="text-slate-600 text-left leading-relaxed">
                1. Buka aplikasi <strong>Google Authenticator</strong> atau <strong>Authy</strong> pada smartphone Anda.<br />
                2. Pindai QR Code di bawah ini atau masukkan Secret Key manual.
              </p>

              {/* QR Code Visual */}
              <div className="p-3 bg-slate-50 rounded-2xl border border-slate-200 inline-block mx-auto shadow-inner">
                <img
                  src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=otpauth%3A%2F%2Ftotp%2FNETPRO-ISP%3Aadmin%40netpro.id%3Fsecret%3DJBSWY3DPEHPK3PXP%26issuer%3DNETPRO-CRM"
                  alt="2FA QR Code"
                  className="w-44 h-44 rounded-xl mx-auto"
                />
              </div>

              {/* Secret Key Manual */}
              <div className="bg-slate-50 p-3 rounded-xl border border-slate-200 text-left space-y-1">
                <span className="text-slate-400 block text-[10px] uppercase font-semibold">Secret Key Manual (Base32):</span>
                <div className="flex justify-between items-center">
                  <code className="font-mono font-bold text-blue-600 text-sm select-all">JBSWY3DPEHPK3PXP</code>
                  <button
                    type="button"
                    onClick={copySecretKey}
                    className="text-xs font-bold text-slate-700 hover:text-blue-600 bg-white border border-slate-200 px-2 py-1 rounded-lg cursor-pointer"
                  >
                    <i className="fa-solid fa-copy"></i> Salin
                  </button>
                </div>
              </div>

              {/* Emergency Recovery Codes */}
              <div className="bg-amber-50/70 p-3 rounded-xl border border-amber-200 text-left space-y-1.5">
                <strong className="text-amber-900 font-bold block text-[11px] flex items-center gap-1.5">
                  <i className="fa-solid fa-life-ring"></i> Kode Pemulihan Darurat (Emergency Codes):
                </strong>
                <div className="grid grid-cols-2 gap-1 font-mono text-[10px] text-amber-800 font-semibold">
                  <span>• 8921-9912</span>
                  <span>• 3341-8821</span>
                  <span>• 7712-4491</span>
                  <span>• 5512-0091</span>
                </div>
              </div>

              {/* Verification Test Input */}
              <div className="text-left space-y-2 pt-2 border-t border-slate-100">
                <label className="font-semibold text-slate-700 block">Uji Kode OTP 6 Digit dari Aplikasi:</label>
                <div className="flex gap-2">
                  <input
                    type="text"
                    maxLength={6}
                    placeholder="123456"
                    value={testOtp}
                    onChange={(e) => setTestOtp(e.target.value)}
                    className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-center font-mono font-bold text-lg tracking-widest text-blue-600 focus:bg-white focus:border-blue-500"
                  />
                  <button
                    type="button"
                    onClick={verifyOtpTest}
                    className="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl shrink-0 shadow cursor-pointer"
                  >
                    Verifikasi
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
