import React, { useState, useEffect, useRef } from 'react';

export default function Navbar({
  pageTitle = 'Dashboard Utama (Executive Command Center)',
  pageSubtitle = 'Konsolidasi performa jaringan ISP, status RADIUS real-time, pertumbuhan billing, dan SLA operasional.',
  pageBadge,
  onOpenMobileSidebar,
  user,
  onLogout,
  onNavigate,
}) {
  const [notifOpen, setNotifOpen] = useState(false);
  const notifRef = useRef(null);

  useEffect(() => {
    function handleClickOutside(event) {
      if (notifRef.current && !notifRef.current.contains(event.target)) {
        setNotifOpen(false);
      }
    }
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <header className="navbar-gradient-bg bg-gradient-to-r from-[#991b1b] via-[#b91c1c] to-[#7f1d1d] text-white shadow-xl flex items-center justify-between px-3.5 sm:px-8 z-30 shrink-0 border-b border-[#dc2626]/40 gap-2 sm:gap-4 relative select-none sticky top-0 h-20">
      {/* Glowing Ambient Lights for Red Theme */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute top-0 right-1/4 w-32 h-16 bg-white/10 rounded-full blur-2xl"></div>
        <div className="absolute bottom-0 left-1/3 w-32 h-16 bg-rose-400/20 rounded-full blur-2xl"></div>
      </div>

      {/* Left: Mobile Toggle & Page Title with Subtitle */}
      <div className="flex items-center gap-2.5 sm:gap-3.5 relative z-10 flex-1 min-w-0 pr-1">
        <button
          onClick={onOpenMobileSidebar}
          className="md:hidden p-2 text-red-100 hover:text-white rounded-xl border border-white/20 hover:bg-white/10 transition shrink-0"
        >
          <i className="fa-solid fa-bars text-base sm:text-lg"></i>
        </button>
        <div className="min-w-0 flex-1">
          <div className="flex items-center gap-2 max-w-full">
            <h2 className="text-sm sm:text-lg font-extrabold text-white tracking-tight leading-tight truncate">
              {pageTitle}
            </h2>
            {pageBadge && (
              <span className="px-2 py-0.5 bg-white/20 text-white rounded-full font-bold text-[9px] sm:text-[10px] border border-white/30 shadow-xs shrink-0">
                {pageBadge}
              </span>
            )}
          </div>
          <p className="text-xs text-red-100/90 font-medium mt-0.5 hidden sm:block truncate">
            {pageSubtitle}
          </p>
        </div>
      </div>

      {/* Right: Quick Action Controls & Status */}
      <div className="flex items-center justify-end gap-3 relative z-10">
        {/* Status Badge Radius Server */}
        <div className="hidden sm:flex items-center gap-2 bg-black/30 text-emerald-300 px-3.5 py-1.5 rounded-full border border-emerald-400/30 text-xs font-mono font-semibold shadow-xs">
          <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_8px_rgba(52,211,153,0.8)]"></span>
          <span>
            Radius: <strong className="text-emerald-200 font-bold">ONLINE</strong>
          </span>
        </div>

        {/* Quick Notification Bell with Dropdown Menu */}
        <div className="relative" ref={notifRef}>
          <button
            onClick={() => setNotifOpen(!notifOpen)}
            className="p-2.5 text-red-100 hover:text-white hover:bg-white/15 rounded-xl border border-white/20 transition shadow-xs relative flex items-center justify-center"
            title="Pusat Notifikasi Sistem"
          >
            <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-400 rounded-full animate-ping"></span>
            <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-amber-400 rounded-full shadow-[0_0_6px_rgba(251,191,36,0.9)]"></span>
            <i className="fa-regular fa-bell text-base"></i>
          </button>

          {/* Dropdown Notification Center */}
          {notifOpen && (
            <div className="absolute top-full right-0 mt-2.5 w-80 sm:w-96 bg-white text-slate-800 rounded-3xl shadow-2xl border border-slate-100 z-50 overflow-hidden transform origin-top-right ring-1 ring-black/5">
              {/* Dropdown Header */}
              <div className="p-4 bg-gradient-to-r from-[#7f1d1d] to-[#991b1b] text-white flex items-center justify-between border-b border-[#991b1b]">
                <div className="flex items-center gap-2.5">
                  <div className="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-xs shadow-inner">
                    <i className="fa-solid fa-bell"></i>
                  </div>
                  <div>
                    <h4 className="font-extrabold text-sm leading-tight">Pusat Notifikasi</h4>
                    <p className="text-[10px] text-red-200">Alert & Aktivitas Sistem Real-Time</p>
                  </div>
                </div>
                <button
                  onClick={() => setNotifOpen(false)}
                  className="text-[10px] bg-white/15 hover:bg-white/25 px-2.5 py-1 rounded-lg font-bold transition flex items-center gap-1 text-red-100"
                >
                  <i className="fa-solid fa-check-double text-[9px]"></i> Tandai Dibaca
                </button>
              </div>

              {/* Notification Items List */}
              <div className="max-h-80 overflow-y-auto divide-y divide-slate-100 text-xs">
                {/* Item 1: RADIUS Heartbeat */}
                <button
                  onClick={() => {
                    setNotifOpen(false);
                    if (onNavigate) onNavigate('radius-users');
                  }}
                  className="w-full text-left p-3.5 flex items-start gap-3 hover:bg-slate-50 transition group"
                >
                  <div className="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <i className="fa-solid fa-network-wired text-sm"></i>
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between gap-1">
                      <strong className="text-slate-900 font-bold truncate text-[11px]">
                        FreeRADIUS & MikroTik CoA
                      </strong>
                      <span className="text-[9px] text-emerald-600 font-bold bg-emerald-50 px-1.5 py-0.5 rounded">
                        ONLINE
                      </span>
                    </div>
                    <p className="text-slate-500 text-[11px] mt-0.5 leading-snug">
                      Layanan autentikasi PPPoE aktif & sinkronisasi Dynamic CoA port UDP 3799 normal.
                    </p>
                    <span className="text-[9px] text-slate-400 mt-1 block font-mono">
                      <i className="fa-regular fa-clock mr-1"></i> Baru saja
                    </span>
                  </div>
                </button>

                {/* Item 2: Billing & Isolir Alert */}
                <button
                  onClick={() => {
                    setNotifOpen(false);
                    if (onNavigate) onNavigate('billing-daftar');
                  }}
                  className="w-full text-left p-3.5 flex items-start gap-3 hover:bg-slate-50 transition group"
                >
                  <div className="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <i className="fa-solid fa-file-invoice-dollar text-sm"></i>
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between gap-1">
                      <strong className="text-slate-900 font-bold truncate text-[11px]">
                        Penagihan & Invoice Jatuh Tempo
                      </strong>
                      <span className="text-[9px] text-rose-600 font-bold bg-rose-50 px-1.5 py-0.5 rounded">
                        1 Unpaid
                      </span>
                    </div>
                    <p className="text-slate-500 text-[11px] mt-0.5 leading-snug">
                      Terdapat invoice pelanggan menunggu pembayaran & auto-isolir.
                    </p>
                    <span className="text-[9px] text-slate-400 mt-1 block font-mono">
                      <i className="fa-regular fa-clock mr-1"></i> Hari ini
                    </span>
                  </div>
                </button>

                {/* Item 3: CSAT / Tiket SLA */}
                <button
                  onClick={() => {
                    setNotifOpen(false);
                    if (onNavigate) onNavigate('tickets-list');
                  }}
                  className="w-full text-left p-3.5 flex items-start gap-3 hover:bg-slate-50 transition group"
                >
                  <div className="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                    <i className="fa-solid fa-headset text-sm"></i>
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center justify-between gap-1">
                      <strong className="text-slate-900 font-bold truncate text-[11px]">
                        Tiket Gangguan Pelanggan
                      </strong>
                      <span className="text-[9px] text-blue-600 font-bold bg-blue-50 px-1.5 py-0.5 rounded">
                        1 Tiket
                      </span>
                    </div>
                    <p className="text-slate-500 text-[11px] mt-0.5 leading-snug">
                      Ada 1 tiket eskalasi teknisi yang sedang ditangani. SLA 99.9% optimal.
                    </p>
                    <span className="text-[9px] text-slate-400 mt-1 block font-mono">
                      <i className="fa-regular fa-clock mr-1"></i> Real-time
                    </span>
                  </div>
                </button>
              </div>

              {/* Dropdown Footer */}
              <div className="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-slate-500">
                <span className="text-[10px] font-medium">Sistem Monitoring Terpadu</span>
                <button
                  onClick={() => {
                    setNotifOpen(false);
                    if (onNavigate) onNavigate('pengaturan-logs');
                  }}
                  className="text-[11px] font-bold text-[#dc2626] hover:text-[#b91c1c] transition flex items-center gap-1"
                >
                  Lihat Log Audit <i className="fa-solid fa-arrow-right text-[9px]"></i>
                </button>
              </div>
            </div>
          )}
        </div>

        <div className="h-6 w-px bg-white/20 mx-0.5 hidden sm:block"></div>

        {/* Employee Profile & Logout Action */}
        <div className="flex items-center pl-1 gap-2.5">
          <button
            onClick={() => onNavigate && onNavigate('pengaturan-profile')}
            className="flex items-center gap-2.5 text-white hover:text-red-100 transition group text-left cursor-pointer"
            title="Detail Akun User"
          >
            <div className="w-9 h-9 rounded-full ring-2 ring-white/40 shadow-lg group-hover:scale-105 transition-transform overflow-hidden shrink-0 bg-[#450a0a] flex items-center justify-center">
              <i className="fa-solid fa-user-shield text-xs"></i>
            </div>
            <div className="hidden lg:block text-left">
              <p className="text-xs font-bold text-white leading-tight group-hover:text-red-100 transition-colors">
                {user?.name || user?.full_name || 'Admin Utama'}
              </p>
              <span className="text-[10px] text-red-200 font-medium block leading-tight">
                {user?.role || 'Super Administrator'}
              </span>
            </div>
          </button>
          <button
            onClick={onLogout}
            className="p-2 text-red-200 hover:text-white hover:bg-white/15 rounded-xl border border-transparent hover:border-white/20 transition"
            title="Logout Pegawai"
          >
            <i className="fa-solid fa-arrow-right-from-bracket text-xs"></i>
          </button>
        </div>
      </div>
    </header>
  );
}
