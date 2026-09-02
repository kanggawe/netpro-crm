import React, { useState, useEffect } from 'react';
import Navbar from './Navbar';
import Sidebar from './Sidebar';
import ModalRegistrasi from './ModalRegistrasi';
import Toast from './Toast';
import { getMpaUrl, getCurrentRouteFromUrl, checkAuthOrRedirect } from '../utils/mpaNavigation';

export const ROUTE_META = {
  'dashboard-utama': { title: 'Executive Dashboard Utama', subtitle: 'Pusat kendali monitoring bisnis ISP, billing, dan NOC.', module: 'Main Monitoring', icon: 'fa-gauge-high' },
  'dashboard-revenue': { title: 'Statistik Pendapatan & Target', subtitle: 'Laporan realisasi pembayaran tagihan, invoice outstanding, dan target bulanan.', module: 'Main Monitoring', icon: 'fa-arrow-trend-up' },
  'dashboard-overdue': { title: 'Tagihan Jatuh Tempo & Isolir', subtitle: 'Monitoring pelanggan nunggak dan kontrol isolir otomatis.', module: 'Main Monitoring', icon: 'fa-clock-rotate-left' },
  'dashboard-customers': { title: 'Status & Distribusi Pelanggan', subtitle: 'Distribusi pelanggan aktif, suspend, dan sebaran wilayah.', module: 'Main Monitoring', icon: 'fa-users-line' },
  'dashboard-tickets': { title: 'Antrean Tiket & SLA Komplain', subtitle: 'Monitoring penyelesaian gangguan teknis dan respon helpdesk.', module: 'Main Monitoring', icon: 'fa-triangle-exclamation' },
  'dashboard-noc': { title: 'Kesehatan Jaringan & POP NOC', subtitle: 'Status uplink BGP, OLT GPON, dan distribusi core fiber optik.', module: 'Main Monitoring', icon: 'fa-network-wired' },
  'dashboard-hr': { title: 'Statistik Karyawan & Presensi', subtitle: 'Monitoring kehadiran teknisi, staf NOC, dan pencapaian target.', module: 'Main Monitoring', icon: 'fa-user-check' },

  'crm-daftar': { title: 'Daftar Pelanggan FTTH / Hotspot', subtitle: 'Kelola data pelanggan, paket aktif, dan status layanan.', module: 'CRM & Pelanggan', icon: 'fa-address-book' },
  'crm-registrasi': { title: 'Registrasi Pelanggan Baru', subtitle: 'Pendaftaran formulir berlangganan internet dan penugasan survey.', module: 'CRM & Pelanggan', icon: 'fa-user-plus' },
  'crm-detail': { title: 'Profil 360° & Riwayat Pelanggan', subtitle: 'Informasi komprehensif pelanggan, perangkat ONT, dan billing.', module: 'CRM & Pelanggan', icon: 'fa-id-card' },
  'crm-riwayat': { title: 'Riwayat Layanan & Tagihan', subtitle: 'Histori transaksi pembayaran, invoice, dan perubahan paket.', module: 'CRM & Pelanggan', icon: 'fa-history' },
  'crm-paket': { title: 'Katalog Paket Internet & FUP', subtitle: 'Manajemen harga paket, bandwidth rate-limit, dan kuota FUP.', module: 'CRM & Pelanggan', icon: 'fa-box-open' },
  'crm-addon': { title: 'Add-On & Layanan Tambahan', subtitle: 'Layanan IP Publik statis, STB TV interaktif, dan booster speed.', module: 'CRM & Pelanggan', icon: 'fa-puzzle-piece' },
  'crm-promo': { title: 'Promo & Voucher Diskon', subtitle: 'Manajemen kode promo akuisisi sales dan diskon tagihan.', module: 'CRM & Pelanggan', icon: 'fa-tags' },
  'crm-survey': { title: 'Survey Lokasi & Validasi FAT', subtitle: 'Jadwal survey lapangan dan pengecekan redaman ODP terdekat.', module: 'CRM & Pelanggan', icon: 'fa-map-location-dot' },
  'crm-instalasi': { title: 'Perintah Kerja Instalasi Baru', subtitle: 'Instruksi kerja teknisi penarikan drop core dan setting ONT.', module: 'CRM & Pelanggan', icon: 'fa-screwdriver-wrench' },
  'crm-berita_acara': { title: 'Berita Acara Digital (BAST)', subtitle: 'Dokumen serah terima perangkat dan tanda tangan digital pelanggan.', module: 'CRM & Pelanggan', icon: 'fa-file-signature' },

  'billing-daftar': { title: 'Daftar Tagihan & Invoice', subtitle: 'Monitoring faktur tagihan pelanggan prabayar & pascabayar.', module: 'Billing & Invoicing', icon: 'fa-file-invoice-dollar' },
  'billing-generate': { title: 'Generate Tagihan Massal', subtitle: 'Pembuatan invoice siklus bulanan otomatis dengan PPN 11%.', module: 'Billing & Invoicing', icon: 'fa-receipt' },
  'billing-pembayaran': { title: 'Pencatatan Pembayaran Tagihan', subtitle: 'Verifikasi pelunasan via QRIS, Virtual Account, dan Kasir Loket.', module: 'Billing & Invoicing', icon: 'fa-money-bill-transfer' },
  'billing-riwayat': { title: 'Riwayat Pembayaran Masuk', subtitle: 'Log penerimaan kas dan konfirmasi payment gateway.', module: 'Billing & Invoicing', icon: 'fa-clock-rotate-left' },
  'billing-invoice': { title: 'Cetak & Ekspor Invoice Faktur', subtitle: 'Unduh faktur resmi format PDF dan kirim invoice via WhatsApp.', module: 'Billing & Invoicing', icon: 'fa-file-pdf' },
  'billing-denda': { title: 'Denda Keterlambatan Tagihan', subtitle: 'Kalkulasi penalti denda keterlambatan dan biaya re-koneksi.', module: 'Billing & Invoicing', icon: 'fa-triangle-exclamation' },

  'radius-nas': { title: 'Manajemen NAS BRAS & Router', subtitle: 'Konfigurasi IP MikroTik, shared secret, dan port CoA RFC 3576.', module: 'FreeRADIUS & AAA', icon: 'fa-server' },
  'radius-users': { title: 'Kredensial Akun PPPoE / Hotspot', subtitle: 'Database user radcheck, radreply, dan sinkronisasi password.', module: 'FreeRADIUS & AAA', icon: 'fa-user-shield' },
  'radius-sessions': { title: 'Sesi Aktif & CoA Kick Live', subtitle: 'Monitoring live sesi dial-in, pemakaian kuota, dan disconnect real-time.', module: 'FreeRADIUS & AAA', icon: 'fa-tower-broadcast' },
  'radius-profiles': { title: 'Profil Kecepatan Bandwidth', subtitle: 'Manajemen atribut Mikrotik-Rate-Limit dan grouped pool IP.', module: 'FreeRADIUS & AAA', icon: 'fa-gauge' },
  'radius-vouchers': { title: 'Generator Voucher Hotspot', subtitle: 'Cetak voucher massal dengan QR Code login instan.', module: 'FreeRADIUS & AAA', icon: 'fa-ticket' },
  'radius-reports': { title: 'Laporan Akuntansi RADIUS', subtitle: 'Rekapitulasi trafik radacct, session duration, dan log audit.', module: 'FreeRADIUS & AAA', icon: 'fa-chart-column' },

  'noc-monitoring': { title: 'Monitoring Backbone Jaringan NOC', subtitle: 'Status real-time Core Switch, OLT, link BGP, dan utilisasi trafik.', module: 'NOC & Network Ops', icon: 'fa-microchip' },
  'noc-topologi': { title: 'Visualisasi Peta Topologi FTTx', subtitle: 'Peta rute kabel fiber optik dari POP, ODC, hingga ODP tiang.', module: 'NOC & Network Ops', icon: 'fa-microchip' },
  'noc-olt': { title: 'Manajemen GPON OLT Chassis', subtitle: 'Monitoring suhu PON board, optical output, dan status uplink.', module: 'NOC & Network Ops', icon: 'fa-microchip' },
  'noc-otb': { title: 'Manajemen OTB & Rak Distribusi', subtitle: 'Alokasi port pigtail dan patchcord di Optical Terminal Box.', module: 'NOC & Network Ops', icon: 'fa-microchip' },
  'noc-odc': { title: 'Manajemen ODC (FDT Cabinet)', subtitle: 'Kapasitas passive splitter dan terminasi kabel feeder primer.', module: 'NOC & Network Ops', icon: 'fa-microchip' },
  'noc-odp': { title: 'Manajemen ODP (FAT Tiang)', subtitle: 'Status port splitter distribusi dan pemetaan redaman optik.', module: 'NOC & Network Ops', icon: 'fa-microchip' },
  'noc-onu': { title: 'Monitoring Modem ONT / ONU', subtitle: 'Pengecekan sinyal RX optical power (dBm) dan serial number.', module: 'NOC & Network Ops', icon: 'fa-microchip' },
  'noc-mikrotik': { title: 'Manajemen MikroTik API', subtitle: 'Sinkronisasi address-list isolir dan mangle rule.', module: 'NOC & Network Ops', icon: 'fa-microchip' },
  'noc-outage': { title: 'Insiden & Outage Fiber', subtitle: 'Jadwal pemeliharaan jaringan dan pelaporan insiden putus kabel FO.', module: 'NOC & Network Ops', icon: 'fa-microchip' },

  'tickets-list': { title: 'Daftar Tiket Gangguan', subtitle: 'Tiket gangguan masuk, eskalasi teknisi, dan SLA resolution.', module: 'Ticketing & CSAT', icon: 'fa-headset' },
  'tickets-complaints': { title: 'Komplain & Survey CSAT', subtitle: 'Pencatatan feedback pelanggan dan riwayat penanganan CSAT.', module: 'Ticketing & CSAT', icon: 'fa-headset' },

  'finance-kas': { title: 'Arus Kas & Bank Rekening', subtitle: 'Pencatatan mutasi kas kecil, penerimaan pembayaran, dan bank.', module: 'Keuangan & Akuntansi', icon: 'fa-wallet' },
  'finance-opex': { title: 'Pengeluaran Biaya OPEX', subtitle: 'Biaya sewa bandwidth upstream, utilitas, dan perawatan armada.', module: 'Keuangan & Akuntansi', icon: 'fa-wallet' },
  'finance-akuntansi': { title: 'Buku Besar & COA Akun', subtitle: 'Daftar akun standar akuntansi dan jurnal umum berpasangan.', module: 'Keuangan & Akuntansi', icon: 'fa-wallet' },
  'finance-laporan': { title: 'Laporan Laba Rugi & Neraca', subtitle: 'Laporan laba rugi, neraca, PPh 23, dan iuran PNBP USO 1.25%.', module: 'Keuangan & Akuntansi', icon: 'fa-wallet' },
  'finance-pajak': { title: 'Kewajiban Pajak & PNBP', subtitle: 'Rekapitulasi SPT Masa PPN 11% dan e-Bupot Unifikasi.', module: 'Keuangan & Akuntansi', icon: 'fa-wallet' },

  'inventory-barang': { title: 'Stok Perangkat & Material', subtitle: 'Stok modem ONT, kabel drop core FO, ODP, dan SFP module.', module: 'Inventory & Asset Tools', icon: 'fa-boxes-stacked' },
  'inventory-mutasi': { title: 'Mutasi Keluar & Masuk', subtitle: 'Mutasi material untuk kebutuhan instalasi baru dan perbaikan.', module: 'Inventory & Asset Tools', icon: 'fa-boxes-stacked' },
  'inventory-asset': { title: 'Aset Kantor & Toolkit Fiber', subtitle: 'Inventarisasi server, splicer, OTDR, dan kendaraan operasional.', module: 'Inventory & Asset Tools', icon: 'fa-boxes-stacked' },
  'inventory-supplier': { title: 'Supplier & Purchase Order', subtitle: 'Data vendor penyedia perangkat jaringan dan status PO material.', module: 'Inventory & Asset Tools', icon: 'fa-boxes-stacked' },

  'hr-karyawan': { title: 'Master Data Karyawan', subtitle: 'Database pegawai, status kepegawaian, dan dokumen kontrak.', module: 'HR & Manajemen Staf', icon: 'fa-user-tie' },
  'hr-absensi': { title: 'Absensi & Presensi GPS', subtitle: 'Validasi presensi radius GPS dan jadwal shift kerja teknisi.', module: 'HR & Manajemen Staf', icon: 'fa-user-tie' },
  'hr-cuti': { title: 'Pengajuan Cuti & Izin', subtitle: 'Formulir permohonan cuti dan alur persetujuan atasan.', module: 'HR & Manajemen Staf', icon: 'fa-user-tie' },

  'pengaturan-sistem': { title: 'Konfigurasi Sistem & Server', subtitle: 'Pengaturan environment, lisensi, database, dan mail server.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
  'pengaturan-perusahaan': { title: 'Identitas & Cabang ISP', subtitle: 'Profil perusahaan, izin Kominfo, NPWP, dan cabang kantor.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
  'pengaturan-billing': { title: 'Otomatisasi Billing & Denda', subtitle: 'Jadwal penagihan otomatis, tanggal jatuh tempo, dan denda.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
  'pengaturan-payment': { title: 'Payment Gateways & QRIS', subtitle: 'Integrasi Midtrans, Xendit, Tripay, dan BCA Virtual Account.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
  'pengaturan-wa': { title: 'WhatsApp & Server Notifikasi', subtitle: 'Konfigurasi Fonnte, Waba, dan template pesan otomatis.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
  'pengaturan-radius': { title: 'Integrasi MikroTik & RADIUS', subtitle: 'Pengaturan koneksi FreeRADIUS, secret key, dan CoA Port 3799.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
  'pengaturan-backup': { title: 'Backup & Database Restore', subtitle: 'Manajemen pencadangan data otomatis dan snapshot file.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
  'pengaturan-users': { title: 'User Admin & Roles RBAC', subtitle: 'Manajemen hak akses admin, finance, NOC, teknisi, dan CS.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
  'pengaturan-logs': { title: 'Audit Logs System Trail', subtitle: 'Jejak audit seluruh aktivitas pengguna dan pencatatan IP.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
  'pengaturan-lisensi': { title: 'Lisensi & Aktivasi Sistem', subtitle: 'Status lisensi platform ISP, masa aktif, dan kuota nodes.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
  'pengaturan-about': { title: 'Tentang Aplikasi Platform', subtitle: 'Informasi versi NETPRO CRM OS, arsitektur, dan kredit pengembang.', module: 'Pengaturan Sistem', icon: 'fa-gear' },
};

export default function MpaLayout({ children, defaultRoute = 'dashboard-utama' }) {
  const [currentRoute, setCurrentRoute] = useState(() => getCurrentRouteFromUrl(defaultRoute));
  const [user, setUser] = useState(() => {
    const saved = localStorage.getItem('netpro_user');
    return saved ? JSON.parse(saved) : { name: 'Super Administrator', role: 'Superadmin' };
  });
  const [mobileSidebarOpen, setMobileSidebarOpen] = useState(false);
  const [isQuickRegisterOpen, setIsQuickRegisterOpen] = useState(false);
  const [toast, setToast] = useState(null);

  useEffect(() => {
    checkAuthOrRedirect();
  }, []);

  const showToast = ({ type = 'info', title, message }) => {
    setToast({ type, title: title || (type === 'error' ? 'Kesalahan' : 'Sukses'), message });
  };

  const handleLogout = () => {
    localStorage.removeItem('netpro_token');
    localStorage.removeItem('netpro_user');
    window.location.href = '/login.html';
  };

  // MPA Native Navigation function
  const handleNavigate = (routeId) => {
    const targetUrl = getMpaUrl(routeId);
    window.location.href = targetUrl;
  };

  const currentMeta = ROUTE_META[currentRoute] || {
    title: 'NETPRO CRM & Billing OS',
    subtitle: 'Sistem Operasi Manajemen ISP & Billing.',
    module: 'Dashboard',
    icon: 'fa-chart-pie',
  };

  return (
    <div className="flex h-screen w-screen overflow-hidden bg-slate-50 text-slate-800 antialiased font-sans">
      {/* Mobile Overlay */}
      {mobileSidebarOpen && (
        <div
          onClick={() => setMobileSidebarOpen(false)}
          className="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-30 md:hidden"
        />
      )}

      {/* Sidebar with native MPA links */}
      <Sidebar
        currentRoute={currentRoute}
        setRoute={handleNavigate}
        user={user}
        onLogout={handleLogout}
        isOpen={mobileSidebarOpen}
        onClose={() => setMobileSidebarOpen(false)}
      />

      {/* Main Viewport */}
      <main className="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#f8fafc]">
        {/* Navbar */}
        <Navbar
          pageTitle={currentMeta.title}
          pageSubtitle={currentMeta.subtitle}
          onOpenMobileSidebar={() => setMobileSidebarOpen(true)}
          onQuickRegister={() => setIsQuickRegisterOpen(true)}
          onLogout={handleLogout}
          onNavigate={handleNavigate}
          user={user}
        />

        {/* Dynamic Content Viewport */}
        <div className="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 sm:space-y-6">
          <div className="w-full space-y-6">
            {/* Dynamic Chevron / Arrow Breadcrumb Navigation */}
            <div className="arrow-breadcrumb-wrapper">
              <nav aria-label="Breadcrumb" className="w-full">
                <ol className="arrow-breadcrumb">
                  <li
                    onClick={() => handleNavigate('dashboard-utama')}
                    className="arrow-breadcrumb-item is-completed cursor-pointer"
                  >
                    <span className="arrow-breadcrumb-badge">
                      <i className="fa-solid fa-house text-[9px]"></i>
                    </span>
                    <span>Home</span>
                  </li>
                  <li className="arrow-breadcrumb-item is-completed">
                    <span className="arrow-breadcrumb-badge">
                      <i className={`fa-solid ${currentMeta.icon || 'fa-folder'} text-[9px]`}></i>
                    </span>
                    <span>{currentMeta.module || 'Modul'}</span>
                  </li>
                  <li className="arrow-breadcrumb-item is-active">
                    <span className="arrow-breadcrumb-badge">
                      <i className="fa-solid fa-check text-[9px]"></i>
                    </span>
                    <span className="truncate">{currentMeta.title}</span>
                  </li>
                </ol>
              </nav>
            </div>

            {/* Child content rendered with showToast and onNavigate */}
            {typeof children === 'function'
              ? children({ showToast, onNavigate: handleNavigate, currentRoute })
              : React.cloneElement(children, { showToast, onNavigate: handleNavigate, currentRoute })}
          </div>
        </div>
      </main>

      {/* Quick Customer Registration Modal */}
      <ModalRegistrasi
        isOpen={isQuickRegisterOpen}
        onClose={() => setIsQuickRegisterOpen(false)}
        onSuccess={(newCust) => {
          showToast({
            type: 'success',
            title: 'Registrasi Berhasil',
            message: `Pelanggan ${newCust?.name || ''} berhasil didaftarkan.`,
          });
          handleNavigate('crm-daftar');
        }}
      />

      {/* Global Toast */}
      {toast && (
        <Toast
          type={toast.type}
          title={toast.title}
          message={toast.message}
          onClose={() => setToast(null)}
        />
      )}
    </div>
  );
}
