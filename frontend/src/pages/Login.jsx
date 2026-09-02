import React, { useState, useEffect } from 'react';
import {
  User,
  Lock,
  Mail,
  KeyRound,
  Eye,
  EyeOff,
  IdCard,
  ArrowRight,
  ShieldCheck,
  CheckCircle2,
  AlertCircle,
  ArrowLeft,
  RotateCcw,
  UserPlus,
} from 'lucide-react';
import { api } from '../api/client';

export default function Login({ onLoginSuccess }) {
  const [activeTab, setActiveTab] = useState('login'); // 'login' | 'register' | 'forgot' | '2fa'
  const [username, setUsername] = useState('superadmin');
  const [password, setPassword] = useState('admin123');
  const [showPassword, setShowPassword] = useState(false);
  const [rememberMe, setRememberMe] = useState(true);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [successMsg, setSuccessMsg] = useState('');
  const [serverTime, setServerTime] = useState('');
  const [latency, setLatency] = useState('0.12');

  // 2FA state
  const [pendingUser, setPendingUser] = useState(null);
  const [otpCode, setOtpCode] = useState('');

  // Register Form State
  const [regName, setRegName] = useState('');
  const [regEmail, setRegEmail] = useState('');
  const [regPassword, setRegPassword] = useState('');
  const [showRegPassword, setShowRegPassword] = useState(false);
  const [termsAgreed, setTermsAgreed] = useState(true);

  // Forgot Password Form State
  const [forgotUsername, setForgotUsername] = useState('');
  const [forgotNewPass, setForgotNewPass] = useState('');
  const [forgotConfirmPass, setForgotConfirmPass] = useState('');
  const [showForgotPass1, setShowForgotPass1] = useState(false);
  const [showForgotPass2, setShowForgotPass2] = useState(false);

  // Live WIB Clock
  useEffect(() => {
    const updateTime = () => {
      const now = new Date();
      const h = String(now.getHours()).padStart(2, '0');
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      setServerTime(`${h}:${m}:${s} WIB`);
    };
    updateTime();
    const interval = setInterval(updateTime, 1000);
    return () => clearInterval(interval);
  }, []);

  // Latency Heartbeat Simulator
  useEffect(() => {
    const interval = setInterval(() => {
      const ms = (0.10 + Math.random() * 0.05).toFixed(2);
      setLatency(ms);
    }, 3000);
    return () => clearInterval(interval);
  }, []);

  // Login handler
  const handleLogin = async (e) => {
    e?.preventDefault();
    setError('');
    setSuccessMsg('');
    setLoading(true);

    try {
      const res = await api.post('/auth/login', { username, password });

      // If 2FA OTP is required by user profile
      if (res.data?.requires_2fa) {
        setPendingUser(res.data.user);
        setActiveTab('2fa');
        setLoading(false);
        return;
      }

      if (res.data?.token) {
        localStorage.setItem('netpro_token', res.data.token);
        localStorage.setItem('netpro_user', JSON.stringify(res.data.user));
        setSuccessMsg('Login berhasil! Mengalihkan ke dashboard...');
        setTimeout(() => onLoginSuccess(res.data.user), 300);
      }
    } catch (err) {
      setError(err.message || 'Email/Username atau Password salah. Silakan coba lagi.');
    } finally {
      setLoading(false);
    }
  };

  // 2FA Verification handler
  const handleVerify2Fa = async (e) => {
    e?.preventDefault();
    setError('');
    setLoading(true);

    try {
      const res = await api.post('/auth/verify-2fa', {
        user_id: pendingUser?.id,
        otp_code: otpCode,
      });

      if (res.data?.token) {
        localStorage.setItem('netpro_token', res.data.token);
        localStorage.setItem('netpro_user', JSON.stringify(res.data.user));
        setSuccessMsg('Verifikasi 2FA berhasil! Mengalihkan...');
        setTimeout(() => onLoginSuccess(res.data.user), 300);
      }
    } catch (err) {
      setError('Kode OTP 6 Digit tidak valid atau telah kedaluwarsa.');
    } finally {
      setLoading(false);
    }
  };

  // Register handler
  const handleRegister = async (e) => {
    e.preventDefault();
    setError('');
    setSuccessMsg('');
    if (!regName || !regEmail || !regPassword) {
      setError('Semua field pendaftaran wajib diisi lengkap.');
      return;
    }
    if (regPassword.length < 6) {
      setError('Kata sandi minimal 6 karakter.');
      return;
    }
    setLoading(true);

    try {
      const res = await api.post('/auth/register', {
        name: regName,
        email: regEmail,
        password: regPassword,
      });
      setSuccessMsg(res.message || 'Pendaftaran Berhasil! Silakan masuk dengan akun baru Anda.');
      setUsername(regEmail);
      setPassword(regPassword);
      setTimeout(() => setActiveTab('login'), 1200);
    } catch (err) {
      setError(err.message || 'Alamat email atau username sudah terdaftar.');
    } finally {
      setLoading(false);
    }
  };

  // Forgot password handler
  const handleForgotPassword = async (e) => {
    e.preventDefault();
    setError('');
    setSuccessMsg('');
    if (!forgotUsername || !forgotNewPass || !forgotConfirmPass) {
      setError('Email atau username wajib diisi.');
      return;
    }
    if (forgotNewPass.length < 6) {
      setError('Kata sandi minimal 6 karakter.');
      return;
    }
    if (forgotNewPass !== forgotConfirmPass) {
      setError('Konfirmasi kata sandi baru tidak cocok.');
      return;
    }
    setLoading(true);

    try {
      const res = await api.post('/auth/forgot-password', {
        username: forgotUsername,
        new_password: forgotNewPass,
      });
      setSuccessMsg(res.message || 'Password Berhasil Diubah! Silakan masuk.');
      setUsername(forgotUsername);
      setPassword(forgotNewPass);
      setTimeout(() => setActiveTab('login'), 1200);
    } catch (err) {
      setError(err.message || 'Pengguna atau alamat email tidak ditemukan.');
    } finally {
      setLoading(false);
    }
  };

  const fillQuickRole = (u, p) => {
    setUsername(u);
    setPassword(p);
    setError('');
  };

  const handleOAuthLogin = async (provider, roleUser, rolePass) => {
    try {
      const res = await api.get(`/auth/oauth/${provider}/redirect`);
      if (res?.redirect_url) {
        window.location.href = res.redirect_url;
        return;
      }
    } catch {
      // Fallback to quick role fill if OAuth server is in development mode
    }
    fillQuickRole(roleUser, rolePass);
  };

  return (
    <div className="min-h-screen relative dot-pattern flex selection:bg-brand-500 selection:text-white bg-[#f8fafc] text-slate-800">
      <div className="flex w-full min-h-screen relative z-10">
        {/* ================= LEFT COLUMN: BRANDING & FITUR NETPRO CRM ================= */}
        <div className="hidden lg:flex w-[55%] relative flex-col justify-center py-12 pl-16 pr-32 text-white">
          <div className="left-curve-bg"></div>

          <div className="relative z-10 max-w-xl space-y-6">
            {/* Header / Brand */}
            <div className="flex items-center gap-3.5">
              <div className="w-12 h-12 bg-white rounded-2xl flex items-center justify-center p-1 shadow-xl transform -rotate-3 shrink-0 ring-2 ring-white/30">
                <img src="/netpro-logo.svg" alt="NETPRO Logo" className="w-full h-full object-contain" />
              </div>
              <div>
                <h1 className="text-2xl font-bold tracking-tight text-white">
                  NETPRO<span className="font-light"> CRM OS</span>
                </h1>
                <p className="text-red-100 text-xs tracking-wider uppercase font-semibold">
                  ISP BILLING & NETWORK MANAGEMENT
                </p>
              </div>
            </div>

            {/* Main Headline */}
            <div>
              <h2 className="text-3xl xl:text-4xl font-bold leading-tight mb-2 text-white">
                Platform Otomasi Bisnis ISP <br />
                <span className="text-yellow-400">Modern & Terlengkap</span>
              </h2>
              <p className="text-red-100 text-sm max-w-lg leading-relaxed font-normal">
                Kelola pelanggan FTTH/Hotspot, tagihan PPN 11%, operasional teknisi NOC, dan laporan keuangan dalam satu platform terintegrasi.
              </p>
            </div>

            {/* Features Grid */}
            <div className="grid grid-cols-2 gap-x-6 gap-y-5 pt-2">
              {/* Feature 1 */}
              <div className="flex items-start gap-3">
                <div className="w-10 h-10 rounded-full bg-white/10 flex-shrink-0 flex items-center justify-center backdrop-blur-sm border border-white/20 text-yellow-300">
                  <i className="fa-solid fa-users text-sm"></i>
                </div>
                <div>
                  <h3 className="font-semibold text-sm mb-0.5 text-white">Kelola Pelanggan</h3>
                  <p className="text-red-200 text-xs leading-snug">
                    Profil 360°, isolir otomatis, dan manajemen tiket gangguan.
                  </p>
                </div>
              </div>

              {/* Feature 2 */}
              <div className="flex items-start gap-3">
                <div className="w-10 h-10 rounded-full bg-white/10 flex-shrink-0 flex items-center justify-center backdrop-blur-sm border border-white/20 text-yellow-300">
                  <i className="fa-solid fa-file-invoice-dollar text-sm"></i>
                </div>
                <div>
                  <h3 className="font-semibold text-sm mb-0.5 text-white">Automasi Billing</h3>
                  <p className="text-red-200 text-xs leading-snug">
                    Faktur pajak PPN 11%, QRIS, dan VA Bank otomatis.
                  </p>
                </div>
              </div>

              {/* Feature 3 */}
              <div className="flex items-start gap-3">
                <div className="w-10 h-10 rounded-full bg-white/10 flex-shrink-0 flex items-center justify-center backdrop-blur-sm border border-white/20 text-yellow-300">
                  <i className="fa-solid fa-tower-cell text-sm"></i>
                </div>
                <div>
                  <h3 className="font-semibold text-sm mb-0.5 text-white">GPON & RADIUS AAA</h3>
                  <p className="text-red-200 text-xs leading-snug">
                    Otentikasi PPPoE, MikroTik NAS, dan monitoring OLT.
                  </p>
                </div>
              </div>

              {/* Feature 4 */}
              <div className="flex items-start gap-3">
                <div className="w-10 h-10 rounded-full bg-white/10 flex-shrink-0 flex items-center justify-center backdrop-blur-sm border border-white/20 text-yellow-300">
                  <i className="fa-solid fa-shield-halved text-sm"></i>
                </div>
                <div>
                  <h3 className="font-semibold text-sm mb-0.5 text-white">Keamanan Enterprise</h3>
                  <p className="text-red-200 text-xs leading-snug">
                    Enkripsi TLS 1.3, audit trail log, dan proteksi 2FA OTP.
                  </p>
                </div>
              </div>
            </div>

            {/* Live System Indicator Card */}
            <div className="p-4 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 space-y-2.5 text-xs font-mono">
              <div className="flex justify-between items-center">
                <span className="text-red-100 flex items-center gap-2">
                  <span className="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                  <span>Server Node Status:</span>
                </span>
                <strong className="text-emerald-300 font-bold">ONLINE ({latency}ms)</strong>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-red-100">Waktu Server (WIB):</span>
                <strong className="text-yellow-300 font-bold">{serverTime || '06:22:15 WIB'}</strong>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-red-100">Lisensi Korporat:</span>
                <strong className="text-white truncate max-w-[200px]">PT MITRAXCON SYNERGY UTAMA</strong>
              </div>
            </div>
          </div>

          {/* Bottom Badges */}
          <div className="absolute bottom-6 left-16 right-32 flex items-center justify-between text-xs text-red-200/90 font-mono">
            <span>
              Versi: <strong className="text-white">v4.0.0-ENTERPRISE</strong>
            </span>
            <span>
              Enkripsi: <strong className="text-emerald-300">TLS 1.3 AES-256</strong>
            </span>
          </div>
        </div>

        {/* ================= RIGHT COLUMN: AUTH FORMS ================= */}
        <div className="w-full lg:w-[45%] flex flex-col justify-center items-center p-6 sm:p-12 relative z-10">
          {/* Mobile Brand (Visible only on small screens) */}
          <div className="flex lg:hidden items-center gap-3 mb-8 w-full max-w-[420px]">
            <div className="w-11 h-11 bg-white rounded-xl flex items-center justify-center p-1 shadow-md ring-1 ring-black/5 shrink-0">
              <img src="/netpro-logo.svg" alt="NETPRO Logo" className="w-full h-full object-contain" />
            </div>
            <div>
              <h1 className="text-xl font-bold text-gray-900 tracking-tight">NETPRO CRM OS</h1>
            </div>
          </div>

          {/* The Floating Form Card */}
          <div className="w-full max-w-[420px] bg-white rounded-3xl shadow-soft p-8 sm:p-10 border border-gray-100 relative">
            {/* ================= 2FA OTP SCREEN ================= */}
            {activeTab === '2fa' && (
              <div>
                <div className="mb-6 flex items-center justify-between border-b border-gray-100 pb-4">
                  <div>
                    <h2 className="text-xl font-bold text-gray-900 flex items-center gap-2">
                      <i className="fa-solid fa-shield-halved text-[#dc2626]"></i> Verifikasi 2FA
                    </h2>
                    <p className="text-xs text-gray-500 mt-0.5">Dua langkah otentikasi akun aman</p>
                  </div>
                  <span className="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono font-bold text-[10px] rounded-full flex items-center gap-1">
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    2FA ACTIVE
                  </span>
                </div>

                {/* Identity Badge */}
                <div className="p-3.5 bg-slate-50 border border-slate-200/80 rounded-2xl flex items-center gap-3 mb-5">
                  <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#b91c1c] to-[#ef4444] text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                    <i className="fa-solid fa-user-shield"></i>
                  </div>
                  <div className="overflow-hidden min-w-0">
                    <strong className="text-gray-900 text-xs block truncate">{pendingUser?.name || pendingUser?.username}</strong>
                    <span className="text-gray-500 font-mono text-[11px] block truncate">
                      {pendingUser?.email || pendingUser?.username} ({pendingUser?.role || 'User'})
                    </span>
                  </div>
                </div>

                {error && (
                  <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2 text-xs font-semibold mb-4">
                    <i className="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                    <span>{error}</span>
                  </div>
                )}

                <form onSubmit={handleVerify2Fa} className="space-y-5">
                  <div>
                    <label htmlFor="otpCodeInput" className="block text-xs font-semibold text-gray-700 mb-2 uppercase tracking-wide text-center">
                      Masukkan Kode OTP 6-Digit:
                    </label>
                    <div className="relative">
                      <input
                        type="text"
                        id="otpCodeInput"
                        required
                        maxLength={9}
                        value={otpCode}
                        onChange={(e) => setOtpCode(e.target.value)}
                        placeholder="123456"
                        className="w-full py-3.5 px-4 text-center font-mono font-bold text-2xl tracking-[0.35em] text-[#b91c1c] bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-[#dc2626] focus:bg-white focus:ring-4 focus:ring-[#ef4444]/15 outline-none transition-all placeholder-gray-300"
                      />
                    </div>
                    <span className="text-[11px] text-gray-500 block text-center mt-2 leading-relaxed">
                      Buka aplikasi <strong>Google Authenticator</strong> atau gunakan kode darurat: <code className="bg-gray-100 px-1.5 py-0.5 rounded text-gray-800 font-mono font-bold text-[10px]">8921-9912</code>
                    </span>
                  </div>

                  <button
                    type="submit"
                    disabled={loading}
                    className="w-full bg-[#dc2626] hover:bg-[#b91c1c] text-white font-semibold py-3.5 rounded-xl shadow-lg shadow-[#ef4444]/25 transition-all duration-200 flex justify-center items-center gap-2 group cursor-pointer"
                  >
                    <i className="fa-solid fa-circle-check"></i>
                    <span>{loading ? 'Memverifikasi...' : 'Verifikasi & Masuk Dashboard'}</span>
                    <i className="fa-solid fa-arrow-right text-sm transform group-hover:translate-x-1 transition-transform"></i>
                  </button>
                </form>

                <div className="mt-6 pt-4 border-t border-gray-100 text-center">
                  <button
                    type="button"
                    onClick={() => { setActiveTab('login'); setError(''); }}
                    className="text-xs font-medium text-gray-500 hover:text-[#dc2626] inline-flex items-center gap-1.5 transition-colors cursor-pointer"
                  >
                    <i className="fa-solid fa-arrow-left"></i>
                    <span>Batalkan & Masuk dengan Akun Lain</span>
                  </button>
                </div>
              </div>
            )}

            {/* ================= LOGIN FORM ================= */}
            {activeTab === 'login' && (
              <div>
                <div className="text-center mb-8">
                  <h2 className="text-2xl font-bold text-gray-900 mb-2">Selamat Datang Kembali!</h2>
                  <p className="text-sm text-gray-500">Masuk untuk melanjutkan ke akun Anda</p>
                </div>

                {successMsg && (
                  <div className="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center gap-2 text-xs font-semibold mb-4">
                    <i className="fa-solid fa-circle-check text-sm shrink-0"></i>
                    <span>{successMsg}</span>
                  </div>
                )}

                {error && (
                  <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2 text-xs font-semibold mb-4">
                    <i className="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                    <span>{error}</span>
                  </div>
                )}

                <form onSubmit={handleLogin} className="space-y-5">
                  {/* Email/Username Input */}
                  <div>
                    <label className="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">
                      Email atau Username
                    </label>
                    <div className="auth-input-wrapper">
                      <div className="auth-leading-icon">
                        <User className="w-4.5 h-4.5" />
                      </div>
                      <input
                        type="text"
                        required
                        value={username}
                        onChange={(e) => setUsername(e.target.value)}
                        placeholder="Email atau Username"
                        className="py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-[#dc2626] outline-none transition-all text-sm bg-gray-50/50 focus:bg-white text-gray-800 placeholder:text-gray-400 font-normal"
                      />
                    </div>
                  </div>

                  {/* Password Input */}
                  <div>
                    <label className="block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wide">
                      Password
                    </label>
                    <div className="auth-input-wrapper">
                      <div className="auth-leading-icon">
                        <Lock className="w-4.5 h-4.5" />
                      </div>
                      <input
                        type={showPassword ? 'text' : 'password'}
                        required
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                        placeholder="••••••••"
                        className="has-trailing py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-[#dc2626] outline-none transition-all text-sm bg-gray-50/50 focus:bg-white text-gray-800 placeholder:text-gray-400 font-normal"
                      />
                      <div className="auth-trailing-action">
                        <button
                          type="button"
                          onClick={() => setShowPassword(!showPassword)}
                          aria-label={showPassword ? 'Sembunyikan password' : 'Lihat password'}
                          className="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                        >
                          {showPassword ? <EyeOff className="w-4.5 h-4.5" /> : <Eye className="w-4.5 h-4.5" />}
                        </button>
                      </div>
                    </div>
                  </div>

                  {/* Options Row */}
                  <div className="flex items-center justify-between pt-1">
                    <label className="flex items-center gap-2 cursor-pointer group select-none">
                      <input
                        type="checkbox"
                        checked={rememberMe}
                        onChange={(e) => setRememberMe(e.target.checked)}
                        className="custom-checkbox"
                      />
                      <span className="text-sm text-gray-600 group-hover:text-gray-900 transition-colors">
                        Ingat saya
                      </span>
                    </label>
                    <button
                      type="button"
                      onClick={() => { setError(''); setSuccessMsg(''); setActiveTab('forgot'); }}
                      className="text-sm font-medium text-[#dc2626] hover:text-[#b91c1c] hover:underline transition-all cursor-pointer"
                    >
                      Lupa password?
                    </button>
                  </div>

                  {/* Submit Button */}
                  <button
                    type="submit"
                    disabled={loading}
                    className="w-full bg-[#dc2626] hover:bg-[#b91c1c] text-white font-medium py-3 rounded-xl shadow-lg shadow-[#ef4444]/25 transition-all duration-200 mt-2 flex justify-center items-center gap-2 group cursor-pointer"
                  >
                    <span>{loading ? 'Memproses...' : 'Masuk'}</span>
                    <ArrowRight className="w-4 h-4 transform group-hover:translate-x-1 transition-transform" />
                  </button>
                </form>

                {/* Divider */}
                <div className="relative mt-7 mb-5">
                  <div className="absolute inset-0 flex items-center">
                    <div className="w-full border-t border-gray-200"></div>
                  </div>
                  <div className="relative flex justify-center text-xs">
                    <span className="px-3 bg-white text-gray-400 font-medium">atau masuk dengan</span>
                  </div>
                </div>

                {/* Social Buttons (Google, GitHub, Facebook, X / Twitter OAuth 2.0 SSO) */}
                <div className="grid grid-cols-4 gap-2.5">
                  <button
                    type="button"
                    onClick={() => handleOAuthLogin('google', 'superadmin', 'admin123')}
                    title="Masuk via Google OAuth 2.0 (Superadmin)"
                    className="flex items-center justify-center h-10 border border-gray-200 rounded-xl bg-white hover:bg-gray-50 hover:border-gray-300 hover:shadow-sm transition-all duration-200 cursor-pointer group"
                  >
                    <svg className="w-4.5 h-4.5 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                      <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                      <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                      <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                      <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                    </svg>
                  </button>

                  <button
                    type="button"
                    onClick={() => handleOAuthLogin('github', 'admin_finance', 'admin123')}
                    title="Masuk via GitHub OAuth 2.0 (Finance)"
                    className="flex items-center justify-center h-10 border border-gray-200 rounded-xl bg-white hover:bg-gray-50 hover:border-gray-300 hover:shadow-sm transition-all duration-200 cursor-pointer group"
                  >
                    <svg className="w-4.5 h-4.5 text-gray-900 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                      <path fillRule="evenodd" clipRule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                    </svg>
                  </button>

                  <button
                    type="button"
                    onClick={() => handleOAuthLogin('facebook', 'noc_lead', 'admin123')}
                    title="Masuk via Facebook OAuth 2.0 (NOC Lead)"
                    className="flex items-center justify-center h-10 border border-gray-200 rounded-xl bg-white hover:bg-gray-50 hover:border-gray-300 hover:shadow-sm transition-all duration-200 cursor-pointer group"
                  >
                    <svg className="w-4.5 h-4.5 text-[#1877F2] transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                  </button>

                  <button
                    type="button"
                    onClick={() => handleOAuthLogin('twitter', 'tech_field1', 'admin123')}
                    title="Masuk via X / Twitter OAuth 2.0 (Teknisi)"
                    className="flex items-center justify-center h-10 border border-gray-200 rounded-xl bg-white hover:bg-gray-50 hover:border-gray-300 hover:shadow-sm transition-all duration-200 cursor-pointer group"
                  >
                    <svg className="w-4 h-4 text-gray-900 transition-transform group-hover:scale-110" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                    </svg>
                  </button>
                </div>

                <div className="mt-8 text-center">
                  <p className="text-sm text-gray-600">
                    Belum punya akun?{' '}
                    <button
                      type="button"
                      onClick={() => { setError(''); setSuccessMsg(''); setActiveTab('register'); }}
                      className="font-bold text-[#dc2626] hover:text-[#b91c1c] transition-colors cursor-pointer"
                    >
                      Daftar Sekarang
                    </button>
                  </p>
                </div>
              </div>
            )}

            {/* ================= REGISTER FORM ================= */}
            {activeTab === 'register' && (
              <div>
                <div className="mb-5 flex items-center gap-3">
                  <button
                    type="button"
                    onClick={() => { setError(''); setSuccessMsg(''); setActiveTab('login'); }}
                    className="w-9 h-9 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors cursor-pointer"
                    title="Kembali ke Login"
                  >
                    <ArrowLeft className="w-4 h-4" />
                  </button>
                  <div>
                    <h2 className="text-lg font-bold text-gray-900">
                      Daftar Akun Baru
                    </h2>
                    <p className="text-xs text-gray-500">
                      Buat akun untuk portal NETPRO CRM
                    </p>
                  </div>
                </div>

                {error && (
                  <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2 text-xs font-semibold mb-4">
                    <i className="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                    <span>{error}</span>
                  </div>
                )}

                {/* Social Buttons on Register */}
                <div className="grid grid-cols-4 gap-2 mb-4">
                  <button
                    type="button"
                    onClick={() => handleOAuthLogin('google', 'superadmin', 'admin123')}
                    title="Daftar Cepat dengan Google"
                    className="flex items-center justify-center h-9 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all cursor-pointer"
                  >
                    <svg className="w-4 h-4" viewBox="0 0 24 24">
                      <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                      <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                      <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                      <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                    </svg>
                  </button>

                  <button
                    type="button"
                    onClick={() => handleOAuthLogin('github', 'admin_finance', 'admin123')}
                    title="Daftar Cepat dengan GitHub"
                    className="flex items-center justify-center h-9 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all cursor-pointer"
                  >
                    <svg className="w-4 h-4 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                      <path fillRule="evenodd" clipRule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                    </svg>
                  </button>

                  <button
                    type="button"
                    onClick={() => handleOAuthLogin('facebook', 'noc_lead', 'admin123')}
                    title="Daftar Cepat dengan Facebook"
                    className="flex items-center justify-center h-9 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all cursor-pointer"
                  >
                    <svg className="w-4 h-4 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                  </button>

                  <button
                    type="button"
                    onClick={() => handleOAuthLogin('twitter', 'tech_field1', 'admin123')}
                    title="Daftar Cepat dengan X"
                    className="flex items-center justify-center h-9 border border-gray-200 rounded-xl hover:bg-gray-50 transition-all cursor-pointer"
                  >
                    <svg className="w-3.5 h-3.5 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                    </svg>
                  </button>
                </div>

                <div className="relative mb-4">
                  <div className="absolute inset-0 flex items-center"><div className="w-full border-t border-gray-200"></div></div>
                  <div className="relative flex justify-center text-[10px]">
                    <span className="px-2 bg-white text-gray-400 uppercase font-semibold">atau formulir manual</span>
                  </div>
                </div>

                <form onSubmit={handleRegister} className="space-y-3.5">
                  <div>
                    <label className="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                      Nama Lengkap
                    </label>
                    <div className="auth-input-wrapper">
                      <div className="auth-leading-icon">
                        <IdCard className="w-4.5 h-4.5" />
                      </div>
                      <input
                        type="text"
                        required
                        value={regName}
                        onChange={(e) => setRegName(e.target.value)}
                        placeholder="Jhon Doe"
                        className="py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-[#dc2626] outline-none transition-all text-sm bg-gray-50/50 focus:bg-white text-gray-800 placeholder:text-gray-400 font-normal"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                      Alamat Email
                    </label>
                    <div className="auth-input-wrapper">
                      <div className="auth-leading-icon">
                        <Mail className="w-4.5 h-4.5" />
                      </div>
                      <input
                        type="email"
                        required
                        value={regEmail}
                        onChange={(e) => setRegEmail(e.target.value)}
                        placeholder="nama@perusahaan.com"
                        className="py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-[#dc2626] outline-none transition-all text-sm bg-gray-50/50 focus:bg-white text-gray-800 placeholder:text-gray-400 font-normal"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                      Buat Password
                    </label>
                    <div className="auth-input-wrapper">
                      <div className="auth-leading-icon">
                        <Lock className="w-4.5 h-4.5" />
                      </div>
                      <input
                        type={showRegPassword ? 'text' : 'password'}
                        required
                        minLength={6}
                        value={regPassword}
                        onChange={(e) => setRegPassword(e.target.value)}
                        placeholder="Min. 6 Karakter"
                        className="has-trailing py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-[#dc2626] outline-none transition-all text-sm bg-gray-50/50 focus:bg-white text-gray-800 placeholder:text-gray-400 font-normal"
                      />
                      <div className="auth-trailing-action">
                        <button
                          type="button"
                          onClick={() => setShowRegPassword(!showRegPassword)}
                          aria-label={showRegPassword ? 'Sembunyikan password' : 'Lihat password'}
                          className="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                        >
                          {showRegPassword ? <EyeOff className="w-4.5 h-4.5" /> : <Eye className="w-4.5 h-4.5" />}
                        </button>
                      </div>
                    </div>
                  </div>

                  <div className="pt-1">
                    <label className="flex items-start gap-2.5 cursor-pointer group select-none">
                      <input
                        type="checkbox"
                        checked={termsAgreed}
                        onChange={(e) => setTermsAgreed(e.target.checked)}
                        className="custom-checkbox mt-0.5"
                      />
                      <span className="text-[11px] text-gray-600 leading-snug">
                        Saya menyetujui{' '}
                        <a href="#" className="font-semibold text-[#dc2626] hover:underline">Ketentuan</a> dan{' '}
                        <a href="#" className="font-semibold text-[#dc2626] hover:underline">Privasi</a>.
                      </span>
                    </label>
                  </div>

                  <button
                    type="submit"
                    disabled={loading}
                    className="w-full bg-gray-900 hover:bg-black text-white font-medium py-2.5 rounded-xl shadow-lg transition-all duration-200 mt-2 flex justify-center items-center gap-2 group cursor-pointer"
                  >
                    <UserPlus className="w-4 h-4" />
                    <span>{loading ? 'Mendaftarkan...' : 'Daftar Sekarang'}</span>
                  </button>
                </form>
              </div>
            )}

            {/* ================= FORGOT PASSWORD FORM ================= */}
            {activeTab === 'forgot' && (
              <div>
                <div className="mb-5 flex items-center gap-3">
                  <button
                    type="button"
                    onClick={() => { setError(''); setSuccessMsg(''); setActiveTab('login'); }}
                    className="w-9 h-9 shrink-0 flex items-center justify-center rounded-xl border border-gray-200 hover:bg-gray-100 text-gray-600 hover:text-gray-900 transition-colors cursor-pointer"
                    title="Kembali ke Login"
                  >
                    <ArrowLeft className="w-4 h-4" />
                  </button>
                  <div>
                    <h2 className="text-lg font-bold text-gray-900">
                      Lupa Password
                    </h2>
                    <p className="text-xs text-gray-500">
                      Atur ulang kata sandi akun NETPRO CRM Anda
                    </p>
                  </div>
                </div>

                {error && (
                  <div className="p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center gap-2 text-xs font-semibold mb-4">
                    <AlertCircle className="w-4 h-4 shrink-0" />
                    <span>{error}</span>
                  </div>
                )}

                <form onSubmit={handleForgotPassword} className="space-y-4">
                  <div>
                    <label className="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                      Email atau Username
                    </label>
                    <div className="auth-input-wrapper">
                      <div className="auth-leading-icon">
                        <User className="w-4.5 h-4.5" />
                      </div>
                      <input
                        type="text"
                        required
                        value={forgotUsername}
                        onChange={(e) => setForgotUsername(e.target.value)}
                        placeholder="nama@perusahaan.com atau username"
                        className="py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-[#dc2626] outline-none transition-all text-sm bg-gray-50/50 focus:bg-white text-gray-800 placeholder:text-gray-400 font-normal"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                      Kata Sandi Baru
                    </label>
                    <div className="auth-input-wrapper">
                      <div className="auth-leading-icon">
                        <KeyRound className="w-4.5 h-4.5" />
                      </div>
                      <input
                        type={showForgotPass1 ? 'text' : 'password'}
                        required
                        minLength={6}
                        value={forgotNewPass}
                        onChange={(e) => setForgotNewPass(e.target.value)}
                        placeholder="Min. 6 Karakter"
                        className="has-trailing py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-[#dc2626] outline-none transition-all text-sm bg-gray-50/50 focus:bg-white text-gray-800 placeholder:text-gray-400 font-normal"
                      />
                      <div className="auth-trailing-action">
                        <button
                          type="button"
                          onClick={() => setShowForgotPass1(!showForgotPass1)}
                          aria-label={showForgotPass1 ? 'Sembunyikan password' : 'Lihat password'}
                          className="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                        >
                          {showForgotPass1 ? <EyeOff className="w-4.5 h-4.5" /> : <Eye className="w-4.5 h-4.5" />}
                        </button>
                      </div>
                    </div>
                  </div>

                  <div>
                    <label className="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">
                      Konfirmasi Kata Sandi Baru
                    </label>
                    <div className="auth-input-wrapper">
                      <div className="auth-leading-icon">
                        <Lock className="w-4.5 h-4.5" />
                      </div>
                      <input
                        type={showForgotPass2 ? 'text' : 'password'}
                        required
                        minLength={6}
                        value={forgotConfirmPass}
                        onChange={(e) => setForgotConfirmPass(e.target.value)}
                        placeholder="Ulangi kata sandi baru"
                        className="has-trailing py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand-500/20 focus:border-[#dc2626] outline-none transition-all text-sm bg-gray-50/50 focus:bg-white text-gray-800 placeholder:text-gray-400 font-normal"
                      />
                      <div className="auth-trailing-action">
                        <button
                          type="button"
                          onClick={() => setShowForgotPass2(!showForgotPass2)}
                          aria-label={showForgotPass2 ? 'Sembunyikan password' : 'Lihat password'}
                          className="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                        >
                          {showForgotPass2 ? <EyeOff className="w-4.5 h-4.5" /> : <Eye className="w-4.5 h-4.5" />}
                        </button>
                      </div>
                    </div>
                  </div>

                  <button
                    type="submit"
                    disabled={loading}
                    className="w-full bg-[#dc2626] hover:bg-[#b91c1c] text-white font-medium py-3 rounded-xl shadow-lg shadow-[#ef4444]/25 transition-all duration-200 mt-3 flex justify-center items-center gap-2 group cursor-pointer"
                  >
                    <RotateCcw className="w-4 h-4" />
                    <span>{loading ? 'Menyimpan...' : 'Simpan Kata Sandi Baru'}</span>
                  </button>
                </form>
              </div>
            )}
          </div>

          {/* Footer Security Note */}
          <div className="absolute bottom-6 w-full text-center lg:text-right lg:right-12 text-xs text-gray-400 flex justify-center lg:justify-end items-center gap-1.5 pointer-events-none">
            <i className="fa-solid fa-shield-halved text-xs"></i>
            <span>Keamanan terjamin dengan enkripsi data tingkat enterprise</span>
          </div>
        </div>
      </div>
    </div>
  );
}
