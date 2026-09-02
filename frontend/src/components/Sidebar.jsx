import React, { useState, useEffect, useMemo } from 'react';

export const getRoutePath = (itemId) => {
  if (!itemId) return '/dashboard?tab=dashboard-utama';
  const prefix = itemId.split('-')[0];
  const pageMap = {
    dashboard: '/dashboard',
    crm: '/crm',
    billing: '/billing',
    kalkulator: '/billing',
    radius: '/radius',
    noc: '/noc',
    tickets: '/tickets',
    finance: '/finance',
    laporan: '/finance',
    inventory: '/inventory',
    marketing: '/inventory',
    hr: '/hr',
    kinerja: '/hr',
    payroll: '/hr',
    pengaturan: '/settings',
  };
  const path = pageMap[prefix] || '/dashboard';
  return `${path}?tab=${itemId}`;
};

export default function Sidebar({ currentRoute, setRoute, user, onLogout, isOpen, onClose }) {
  const [searchQuery, setSearchQuery] = useState('');
  const [openSections, setOpenSections] = useState({
    'm-dashboard': true,
    'm-crm': false,
    'm-noc': false,
    'm-tickets': false,
    'm-billing': true,
    'm-radius': false,
    'm-marketing': false,
    'm-kalkulator': false,
    'm-finance': false,
    'm-inventory': false,
    'm-hr': false,
    'm-kinerja': false,
    'm-payroll': false,
    'm-laporan': false,
    'm-pengaturan': false,
  });

  // Auto-expand the accordion containing the active route
  useEffect(() => {
    if (!currentRoute) return;
    const prefix = currentRoute.split('-')[0];
    const targetKey = `m-${prefix}`;
    setOpenSections((prev) => ({
      ...prev,
      [targetKey]: true,
    }));
  }, [currentRoute]);

  const toggleSection = (id) => {
    setOpenSections((prev) => ({ ...prev, [id]: !prev[id] }));
  };

  const menuSections = [
    {
      category: 'MAIN MONITORING',
      menus: [
        {
          id: 'm-dashboard',
          title: 'Executive Dashboard',
          icon: 'fa-chart-pie',
          items: [
            { id: 'dashboard-utama', title: 'Ringkasan Bisnis Utama', icon: 'fa-gauge-high' },
            { id: 'dashboard-revenue', title: 'Pendapatan Bulan Berjalan', icon: 'fa-arrow-trend-up' },
            { id: 'dashboard-overdue', title: 'Tagihan Jatuh Tempo', icon: 'fa-clock-rotate-left' },
            { id: 'dashboard-customers', title: 'Pelanggan Aktif / Suspend', icon: 'fa-users-line' },
            { id: 'dashboard-tickets', title: 'Tiket & Antrean Gangguan', icon: 'fa-triangle-exclamation' },
            { id: 'dashboard-noc', title: 'Status Jaringan & POP NOC', icon: 'fa-network-wired' },
            { id: 'dashboard-hr', title: 'Statistik Karyawan & Absensi', icon: 'fa-user-check' },
          ],
        },
      ],
    },
    {
      category: 'OPERATIONAL & CRM',
      menus: [
        {
          id: 'm-crm',
          title: 'CRM & Pelanggan',
          icon: 'fa-users-gear',
          items: [
            { id: 'crm-daftar', title: 'Daftar Pelanggan', icon: 'fa-address-book' },
            { id: 'crm-registrasi', title: 'Registrasi Akun Baru', icon: 'fa-user-plus' },
            { id: 'crm-detail', title: '360° Profil & Telemetri', icon: 'fa-id-card' },
            { id: 'crm-riwayat', title: 'Riwayat Berlangganan', icon: 'fa-history' },
            { id: 'crm-paket', title: 'Katalog Paket Internet', icon: 'fa-box-open' },
            { id: 'crm-addon', title: 'Add-On & Layanan Ekstra', icon: 'fa-puzzle-piece' },
            { id: 'crm-promo', title: 'Promo & Voucher Diskon', icon: 'fa-tags' },
            { id: 'crm-survey', title: 'Survey Lokasi & FAT', icon: 'fa-map-location-dot' },
            { id: 'crm-instalasi', title: 'Instalasi & Pasang Baru', icon: 'fa-screwdriver-wrench' },
            { id: 'crm-berita_acara', title: 'Berita Acara (BAST)', icon: 'fa-file-signature' },
          ],
        },
        {
          id: 'm-noc',
          title: 'NOC & Jaringan FTTx',
          icon: 'fa-microchip',
          items: [
            { id: 'noc-monitoring', title: 'Monitoring Topologi NOC', icon: 'fa-display' },
            { id: 'noc-topologi', title: 'Peta Topologi Jaringan', icon: 'fa-network-wired' },
            { id: 'noc-olt', title: 'Manajemen GPON OLT', icon: 'fa-server' },
            { id: 'noc-otb', title: 'Manajemen OTB & Distribusi', icon: 'fa-diagram-project' },
            { id: 'noc-odc', title: 'Manajemen ODC (FDT)', icon: 'fa-box-archive' },
            { id: 'noc-odp', title: 'Manajemen ODP (FAT)', icon: 'fa-map-pin' },
            { id: 'noc-onu', title: 'Monitoring Modem ONT / ONU', icon: 'fa-wifi' },
            { id: 'noc-mikrotik', title: 'Manajemen MikroTik API', icon: 'fa-router' },
            { id: 'noc-outage', title: 'Insiden & Outage FO', icon: 'fa-circle-exclamation' },
          ],
        },
        {
          id: 'm-tickets',
          title: 'Trouble Ticket CSAT',
          icon: 'fa-headset',
          items: [
            { id: 'tickets-list', title: 'Daftar Tiket Gangguan', icon: 'fa-ticket' },
            { id: 'tickets-complaints', title: 'Komplain & Survey CSAT', icon: 'fa-comment-dots' },
          ],
        },
      ],
    },
    {
      category: 'FINANCE & BILLING',
      menus: [
        {
          id: 'm-billing',
          title: 'Billing & Invoicing',
          icon: 'fa-file-invoice-dollar',
          items: [
            { id: 'billing-daftar', title: 'Daftar Tagihan Pelanggan', icon: 'fa-file-invoice' },
            { id: 'billing-generate', title: 'Generate Tagihan Massal', icon: 'fa-receipt' },
            { id: 'billing-pembayaran', title: 'Pencatatan Pembayaran', icon: 'fa-money-bill-transfer' },
            { id: 'billing-riwayat', title: 'Riwayat Pembayaran Masuk', icon: 'fa-clock-rotate-left' },
            { id: 'billing-invoice', title: 'Cetak & Ekspor Invoice', icon: 'fa-file-pdf' },
            { id: 'billing-denda', title: 'Denda Keterlambatan', icon: 'fa-triangle-exclamation' },
          ],
        },
        {
          id: 'm-radius',
          title: 'FreeRADIUS & AAA',
          icon: 'fa-tower-cell',
          items: [
            { id: 'radius-nas', title: 'Manajemen NAS BRAS Router', icon: 'fa-server' },
            { id: 'radius-users', title: 'Kredensial PPPoE & Hotspot', icon: 'fa-user-shield' },
            { id: 'radius-sessions', title: 'Sesi Aktif & CoA Kick', icon: 'fa-tower-broadcast' },
            { id: 'radius-profiles', title: 'Profil Kecepatan Bandwidth', icon: 'fa-gauge' },
            { id: 'radius-vouchers', title: 'Generator Voucher Hotspot', icon: 'fa-ticket' },
            { id: 'radius-reports', title: 'Laporan Akuntansi RADIUS', icon: 'fa-chart-column' },
          ],
        },
        {
          id: 'm-marketing',
          title: 'Marketing & Sales',
          icon: 'fa-bullhorn',
          items: [
            { id: 'marketing-leads', title: 'Prospek & Leads Sales', icon: 'fa-funnel-dollar' },
            { id: 'marketing-campaign', title: 'Broadcast Promo WA', icon: 'fa-comments-dollar' },
            { id: 'marketing-sales', title: 'Target & Komisi Sales', icon: 'fa-percent' },
          ],
        },
        {
          id: 'm-kalkulator',
          title: 'Kalkulator ISP Tools',
          icon: 'fa-calculator',
          items: [
            { id: 'kalkulator-bandwidth', title: 'Kalkulator Bandwidth & CIR', icon: 'fa-chart-pie' },
            { id: 'kalkulator-pajak', title: 'Kalkulator Pajak & PPN', icon: 'fa-percent' },
          ],
        },
        {
          id: 'm-finance',
          title: 'Keuangan & Akuntansi',
          icon: 'fa-wallet',
          items: [
            { id: 'finance-kas', title: 'Arus Kas & Bank Rekening', icon: 'fa-vault' },
            { id: 'finance-opex', title: 'Pengeluaran Biaya OPEX', icon: 'fa-money-bill-trend-up' },
            { id: 'finance-akuntansi', title: 'Buku Besar & COA Akun', icon: 'fa-book' },
            { id: 'finance-laporan', title: 'Laba Rugi & PNBP Kominfo', icon: 'fa-file-contract' },
            { id: 'finance-pajak', title: 'Kewajiban Pajak & SPT PPN', icon: 'fa-receipt' },
          ],
        },
      ],
    },
    {
      category: 'LOGISTICS & HRD',
      menus: [
        {
          id: 'm-inventory',
          title: 'Inventory & Asset Tools',
          icon: 'fa-boxes-stacked',
          items: [
            { id: 'inventory-barang', title: 'Stok Perangkat & ONT', icon: 'fa-box' },
            { id: 'inventory-mutasi', title: 'Mutasi Keluar & Masuk', icon: 'fa-arrow-right-arrow-left' },
            { id: 'inventory-asset', title: 'Aset Kantor & Toolkit', icon: 'fa-wrench' },
            { id: 'inventory-supplier', title: 'Supplier & PO Vendor', icon: 'fa-truck' },
          ],
        },
        {
          id: 'm-hr',
          title: 'HR & Manajemen Staf',
          icon: 'fa-user-tie',
          items: [
            { id: 'hr-karyawan', title: 'Master Data Karyawan', icon: 'fa-id-badge' },
            { id: 'hr-absensi', title: 'Absensi & Presensi GPS', icon: 'fa-calendar-check' },
            { id: 'hr-cuti', title: 'Pengajuan Cuti & Izin', icon: 'fa-plane-departure' },
          ],
        },
        {
          id: 'm-kinerja',
          title: 'Penilaian Kinerja Tim',
          icon: 'fa-award',
          items: [
            { id: 'kinerja-kpi', title: 'Master KPI Divisi & SLA', icon: 'fa-list-ol' },
            { id: 'kinerja-target', title: 'Target Kerja & Okr Bulanan', icon: 'fa-bullseye' },
            { id: 'kinerja-review', title: 'Review Evaluasi Karyawan', icon: 'fa-user-pen' },
            { id: 'kinerja-leaderboard', title: 'Leaderboard & Prestasi', icon: 'fa-trophy' },
          ],
        },
        {
          id: 'm-payroll',
          title: 'Payroll & Penggajian',
          icon: 'fa-money-bill-wave',
          items: [
            { id: 'payroll-komponen', title: 'Master Komponen Gaji', icon: 'fa-coins' },
            { id: 'payroll-generate', title: 'Generate & Slip Gaji', icon: 'fa-file-invoice' },
            { id: 'payroll-bonus', title: 'Insentif & Bonus Pasang', icon: 'fa-hand-holding-dollar' },
            { id: 'payroll-rekap', title: 'Rekapitulasi Gaji & BPJS', icon: 'fa-scale-balanced' },
          ],
        },
      ],
    },
    {
      category: 'SYSTEM & CONTROLS',
      menus: [
        {
          id: 'm-laporan',
          title: 'Laporan Eksekutif',
          icon: 'fa-file-lines',
          items: [
            { id: 'laporan-summary', title: 'Laporan Summary Eksekutif', icon: 'fa-file-export' },
          ],
        },
        {
          id: 'm-pengaturan',
          title: 'Pengaturan Sistem',
          icon: 'fa-gear',
          items: [
            { id: 'pengaturan-sistem', title: 'Konfigurasi Sistem & Server', icon: 'fa-gears' },
            { id: 'pengaturan-perusahaan', title: 'Identitas & Cabang ISP', icon: 'fa-building' },
            { id: 'pengaturan-billing', title: 'Otomatisasi Billing & Denda', icon: 'fa-calendar-check' },
            { id: 'pengaturan-payment', title: 'Payment Gateways & QRIS', icon: 'fa-credit-card' },
            { id: 'pengaturan-wa', title: 'WhatsApp & Server Notifikasi', icon: 'fa-comments' },
            { id: 'pengaturan-radius', title: 'Integrasi MikroTik & RADIUS', icon: 'fa-code-branch' },
            { id: 'pengaturan-backup', title: 'Backup & Database Restore', icon: 'fa-database' },
            { id: 'pengaturan-users', title: 'User Admin & Roles RBAC', icon: 'fa-user-lock' },
            { id: 'pengaturan-logs', title: 'Audit Logs System Trail', icon: 'fa-clock-rotate-left' },
            { id: 'pengaturan-lisensi', title: 'Lisensi & Aktivasi Sistem', icon: 'fa-certificate' },
            { id: 'pengaturan-about', title: 'Tentang Aplikasi Platform', icon: 'fa-circle-info' },
          ],
        },
      ],
    },
  ];

  // Search Filtering
  const filteredSections = useMemo(() => {
    if (!searchQuery.trim()) return menuSections;
    const q = searchQuery.toLowerCase();

    return menuSections
      .map((sec) => ({
        ...sec,
        menus: sec.menus
          .map((m) => ({
            ...m,
            items: m.items.filter(
              (it) => it.title.toLowerCase().includes(q) || m.title.toLowerCase().includes(q)
            ),
          }))
          .filter((m) => m.items.length > 0 || m.title.toLowerCase().includes(q)),
      }))
      .filter((sec) => sec.menus.length > 0);
  }, [searchQuery]);

  return (
    <aside
      className={`sidebar-gradient-bg fixed inset-y-0 left-0 z-40 w-72 flex flex-col transition-transform duration-300 ease-in-out md:translate-x-0 md:static shrink-0 select-none shadow-2xl border-r border-[#7f1d1d] ${
        isOpen ? 'translate-x-0' : '-translate-x-full'
      }`}
    >
      {/* Brand Header */}
      <div className="h-16 flex items-center justify-between px-4 border-b border-white/10 bg-black/20 backdrop-blur-sm shrink-0">
        <div className="flex items-center space-x-3 cursor-pointer" onClick={() => setRoute('dashboard-utama')}>
          <div className="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1 shadow-lg transform -rotate-3 ring-2 ring-white/20 shrink-0">
            <img src="/netpro-logo.svg" alt="NETPRO Logo" className="w-full h-full object-contain" />
          </div>
          <div>
            <h1 className="text-white font-extrabold text-sm tracking-tight flex items-center gap-1.5">
              <span>NETPRO</span>
              <span className="font-light text-red-200 text-xs px-1.5 py-0.5 rounded-full bg-white/10 border border-white/20">CRM OS</span>
            </h1>
            <p className="text-[10px] text-red-200/90 font-medium tracking-wide uppercase">ISP Enterprise Edition</p>
          </div>
        </div>

        {onClose && (
          <button
            onClick={onClose}
            className="md:hidden text-red-200 hover:text-white p-1 rounded-lg hover:bg-white/10 transition"
          >
            <i className="fa-solid fa-xmark text-lg"></i>
          </button>
        )}
      </div>

      {/* Live Search Menu Input */}
      <div className="p-3 border-b border-white/10 bg-black/10 shrink-0">
        <div className="relative">
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Cari menu (e.g. OLT, Billing)..."
            className="w-full pl-8 pr-3 py-1.5 text-xs bg-black/30 border border-white/15 rounded-xl text-white placeholder-red-200/50 focus:outline-none focus:ring-2 focus:ring-white/30 focus:bg-black/50 transition"
          />
          <i className="fa-solid fa-magnifying-glass text-[11px] text-red-200/60 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
          {searchQuery && (
            <button
              onClick={() => setSearchQuery('')}
              className="absolute right-2.5 top-1/2 -translate-y-1/2 text-red-300 hover:text-white"
            >
              <i className="fa-solid fa-circle-xmark text-xs"></i>
            </button>
          )}
        </div>
      </div>

      {/* Navigation Accordion Sections */}
      <div className="flex-1 overflow-y-auto px-3 py-3.5 space-y-4 custom-sidebar-scroll">
        {filteredSections.map((sec, secIdx) => (
          <div key={secIdx} className="space-y-1">
            {/* Section Header Category */}
            <p className="px-3 text-[10px] font-extrabold text-red-200/90 uppercase tracking-widest mb-2.5 flex items-center">
              <span className="w-1.5 h-1.5 rounded-full bg-red-400 mr-2 shadow-[0_0_6px_rgba(248,113,113,0.9)]"></span>
              <span>{sec.category}</span>
            </p>

            <div className="space-y-1.5">
              {sec.menus.map((grp) => {
                const isOpen = openSections[grp.id] || searchQuery.trim().length > 0;
                const hasActive = grp.items.some((it) => currentRoute === it.id);
                const parentActive = hasActive || (isOpen && currentRoute.startsWith(grp.id.replace('m-', '')))
                  ? 'bg-white text-[#7f1d1d] font-bold shadow-lg shadow-black/20 border border-white'
                  : 'text-red-100/90 hover:bg-white/10 hover:text-white';
                const iconBox = hasActive || (isOpen && currentRoute.startsWith(grp.id.replace('m-', '')))
                  ? 'bg-[#7f1d1d]/10 text-[#b91c1c] shadow-sm'
                  : 'bg-white/10 text-red-200 group-hover:bg-white/20 group-hover:text-white';

                return (
                  <div key={grp.id} className="menu-group">
                    <button
                      onClick={() => toggleSection(grp.id)}
                      className={`w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 group cursor-pointer ${parentActive}`}
                    >
                      <div className="flex items-center">
                        <div className={`h-7 w-7 rounded-lg flex items-center justify-center mr-2.5 transition ${iconBox}`}>
                          <i className={`fa-solid ${grp.icon} text-xs`}></i>
                        </div>
                        <span className="text-xs font-semibold tracking-wide">{grp.title}</span>
                      </div>
                      <i
                        className={`fa-solid fa-chevron-down text-[9px] ${
                          hasActive ? 'text-[#7f1d1d]' : 'text-red-300 group-hover:text-white'
                        } transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}`}
                      ></i>
                    </button>

                    {/* Submenu Items */}
                    {isOpen && (
                      <div className="pl-3.5 pt-1.5 space-y-1 border-l border-white/20 ml-4 mt-1">
                        {grp.items.map((item) => {
                          const isActive = currentRoute === item.id;
                          const itemClass = isActive
                            ? 'sidebar-glow-active'
                            : 'text-red-100/80 hover:text-white hover:bg-white/10';
                          const bulletColor = isActive ? 'text-white' : 'text-red-300 group-hover:text-white';

                          return (
                            <button
                              key={item.id}
                              onClick={() => {
                                setRoute(item.id);
                                if (onClose) onClose();
                              }}
                              className={`w-full text-left flex items-center px-3 py-2 rounded-lg text-xs transition duration-150 group/sub cursor-pointer ${itemClass}`}
                            >
                              <i className={`fa-solid ${item.icon} text-[10px] mr-2.5 ${bulletColor} transition`}></i>
                              <span className="truncate">{item.title}</span>
                            </button>
                          );
                        })}
                      </div>
                    )}
                  </div>
                );
              })}
            </div>
          </div>
        ))}
      </div>

      {/* User Profile Footer Component */}
      <div className="p-3.5 border-t border-white/10 bg-black/20 backdrop-blur-md relative z-10 shrink-0">
        <div className="flex items-center justify-between bg-white/10 hover:bg-white/15 transition p-2.5 rounded-2xl border border-white/10 shadow-lg">
          <button
            onClick={() => setRoute('pengaturan-profile')}
            className="flex items-center space-x-2.5 overflow-hidden flex-1 text-left group cursor-pointer"
            title="Buka Detail Akun User"
          >
            <div className="relative shrink-0">
              <div className="h-9 w-9 rounded-xl bg-gradient-to-tr from-[#450a0a] to-[#7f1d1d] flex items-center justify-center font-bold text-white shadow-md ring-2 ring-white/30 group-hover:scale-105 transition-transform">
                <i className="fa-solid fa-user-shield text-xs"></i>
              </div>
              <span className="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full bg-emerald-400 border-2 border-[#7f1d1d] ring-1 ring-white/50"></span>
            </div>
            <div className="overflow-hidden min-w-0">
              <p className="text-xs font-bold text-white truncate group-hover:text-red-200 transition">
                {user?.name || 'Administrator'}
              </p>
              <p className="text-[10px] text-red-200/80 truncate font-mono">
                {user?.role || 'Superadmin'}
              </p>
            </div>
          </button>

          <button
            onClick={onLogout}
            title="Keluar dari Sesi Aplikasi"
            className="h-8 w-8 rounded-xl bg-white/10 hover:bg-red-600/80 text-red-200 hover:text-white flex items-center justify-center transition shadow-sm border border-white/10 shrink-0 ml-1.5 cursor-pointer"
          >
            <i className="fa-solid fa-arrow-right-from-bracket text-xs"></i>
          </button>
        </div>
      </div>
    </aside>
  );
}
