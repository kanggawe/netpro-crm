import React, { useEffect, useState } from 'react';
import {
  Users,
  UserPlus,
  Search,
  CheckCircle,
  AlertTriangle,
  Zap,
  Power,
  RefreshCw,
  Phone,
  Home,
  Shield,
  Layers,
  Sparkles,
  ClipboardList,
  Wrench,
  FileCheck,
  Tag,
  Radio,
  FileText,
  MapPin,
  Clock,
  Check,
  ChevronRight,
  Download,
  Upload,
  FileSpreadsheet,
  FileUp,
  FileDown,
  Eye,
  Printer,
  Calendar,
  Activity,
  CreditCard,
  Wifi,
  Server,
  ArrowRight,
  Edit,
  Trash2,
  Lock,
  Key,
  X,
  FileCode,
  Plus,
  ClipboardPaste,
  Copy,
  SlidersHorizontal,
  Table,
} from 'lucide-react';
import * as XLSX from 'xlsx';
import ModalRegistrasi from '../components/ModalRegistrasi';
import GpsMap from '../components/GpsMap';
import { api } from '../api/client';

export default function Customers({ showToast, currentRoute = 'crm-daftar', onNavigate }) {
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [actionLoading, setActionLoading] = useState(null);
  const [selectedCustomer, setSelectedCustomer] = useState(null);
  const [bastPreviewModal, setBastPreviewModal] = useState(null);
  const [regStep, setRegStep] = useState(1);

  // Edit Customer Modal State
  const [editModalOpen, setEditModalOpen] = useState(false);
  const [editForm, setEditForm] = useState({
    id: null,
    name: '',
    phone: '',
    email: '',
    address: '',
    package_id: '1',
    billing_type: 'prepaid',
    status: 'active',
  });

  // Reset Password Modal State
  const [resetPassModalOpen, setResetPassModalOpen] = useState(false);
  const [newPassword, setNewPassword] = useState('');

  // Import/Export Excel Spreadsheet Studio State
  const [importModalOpen, setImportModalOpen] = useState(false);
  const [importFileName, setImportFileName] = useState('');
  const [importRows, setImportRows] = useState([]);
  const [importing, setImporting] = useState(false);
  const [dragActive, setDragActive] = useState(false);
  const [importResult, setImportResult] = useState(null);
  const [activeCellCoord, setActiveCellCoord] = useState('A1');
  const [activeCellValue, setActiveCellValue] = useState('');
  const [pasteModalOpen, setPasteModalOpen] = useState(false);
  const [pasteText, setPasteText] = useState('');

  // Full Registration Form State
  const [regForm, setRegForm] = useState({
    name: '',
    nik: '',
    phone: '',
    email: '',
    address: '',
    package_id: '1',
    billing_type: 'prepaid',
    due_date: '5',
    pppoe_user: '',
    pppoe_password: '',
    odp_name: 'ODP-CLG-01',
    ont_sn: '',
  });

  // Package Form State
  const [packages, setPackages] = useState([
    { id: 1, name: 'Home 20M', speed: '20 Mbps', price: 150000, fup: '500 GB', users: 540, badge: 'Popular' },
    { id: 2, name: 'Home 50M', speed: '50 Mbps', price: 250000, fup: '1.2 TB', users: 412, badge: 'Best Value' },
    { id: 3, name: 'Biz 100M', speed: '100 Mbps', price: 450000, fup: 'Unlimited', users: 180, badge: 'Enterprise' },
    { id: 4, name: 'Dedicated 1:1', speed: '200 Mbps', price: 1200000, fup: 'SLA 99.8%', users: 45, badge: 'Corporate' },
  ]);

  // Addons State
  const [addons, setAddons] = useState([
    { id: 1, name: 'IP Publik Statis (/32)', category: 'Network', price: 50000, desc: 'Akses CCTV dan port forwarding dari luar jaringan.' },
    { id: 2, name: 'STB Android 4K + OTT TV', category: 'Entertainment', price: 35000, desc: '60+ Channel lokal & internasional plus YouTube.' },
    { id: 3, name: 'Speed Booster 2x (3 Hari)', category: 'Booster', price: 25000, desc: 'Gandakan bandwidth instan tanpa ubah kontrak.' },
    { id: 4, name: 'Mesh Wi-Fi Router Ekstra', category: 'Hardware', price: 40000, desc: 'Perluas jangkauan sinyal tanpa dead zone.' },
  ]);

  // Promos State
  const [promos, setPromos] = useState([
    { id: 1, code: 'PASANGGRATIS', disc: '100% Biaya Pasang', exp: '31 Des 2026', desc: 'Bebas biaya instalasi untuk pelanggan baru komitmen 12 bln.', usage: 142 },
    { id: 2, code: 'UPGRADE30', disc: 'Potongan Rp 30.000 / bln', exp: '30 Sep 2026', desc: 'Diskon 3 bulan pertama untuk upgrade ke paket Home 50M.', usage: 89 },
    { id: 3, code: 'HEMATRAMADHAN', disc: 'Diskon 15%', exp: '28 Feb 2027', desc: 'Potongan tagihan bulan pertama pembayaran tahunan.', usage: 64 },
  ]);

  // Survey requests state
  const [surveys, setSurveys] = useState([
    { id: 'SRV-091', name: 'Bpk. Hendra Gunawan', address: 'Jl. Merdeka No. 45, RT 02/05', odp: 'ODP-CLG-04 (Port 6/8)', status: 'Approved', signal: '-18.4 dBm', date: '2026-06-18' },
    { id: 'SRV-092', name: 'Ibu Ratna Sari', address: 'Komplek Permata Indah Blok B3/12', odp: 'ODP-CLG-08 (Port 8/8 Penuh)', status: 'Need ODP Expansion', signal: 'N/A', date: '2026-06-19' },
    { id: 'SRV-093', name: 'PT Surya Kencana Mandiri', address: 'Kawasan Industri Cilegon Kav. 9', odp: 'ODC-KWS-01 Core 12', status: 'Pending Survey', signal: 'Pending', date: '2026-06-20' },
  ]);

  // Installation work orders
  const [installations, setInstallations] = useState([
    { id: 'WO-INS-2026-044', cust: 'Ahmad Syafiq', package: 'Home 50M', odp: 'ODP-CLG-02', technician: 'Rian & Tim Alpha', dropcore: '145 Meter', status: 'In Progress' },
    { id: 'WO-INS-2026-045', cust: 'Klinik Medika Pratama', package: 'Biz 100M', odp: 'ODP-CLG-05', technician: 'Budi (Field Tech 2)', dropcore: '80 Meter', status: 'Completed' },
    { id: 'WO-INS-2026-046', cust: 'Dedi Kurniawan', package: 'Home 20M', odp: 'ODP-CLG-09', technician: 'Rian & Tim Alpha', dropcore: '210 Meter', status: 'Scheduled' },
  ]);

  // BAST state
  const [basts, setBasts] = useState([
    { id: 'BAST-2026-0081', cust: 'Klinik Medika Pratama', cid: 'CID-2026-0081', date: '2026-06-19', rxPower: '-19.2 dBm', modemSn: 'ZTEGC9921008', tech: 'Rian Perdana (Tech-01)', status: 'Signed Digitally' },
    { id: 'BAST-2026-0082', cust: 'Toko Sumber Rejeki', cid: 'CID-2026-0082', date: '2026-06-18', rxPower: '-20.1 dBm', modemSn: 'HWTC8812903', tech: 'Budi Santoso (Tech-02)', status: 'Signed Digitally' },
  ]);

  // Riwayat subscription logs
  const [subscriptionLogs, setSubscriptionLogs] = useState([
    { id: 'LOG-001', cust: 'Ahmad Syafiq (CID-001)', event: 'Perpanjangan Paket Home 50M', date: '2026-06-01', operator: 'Auto-Billing Engine', status: 'Success' },
    { id: 'LOG-002', cust: 'Klinik Medika Pratama (CID-002)', event: 'Upgrade Paket dari Home 50M ke Biz 100M', date: '2026-05-15', operator: 'CS-Admin', status: 'Success' },
    { id: 'LOG-003', cust: 'Dedi Kurniawan (CID-003)', event: 'Buka Isolir Akun via Pelunasan QRIS', date: '2026-05-08', operator: 'Payment Gateway', status: 'Success' },
    { id: 'LOG-004', cust: 'CV Maju Lancar (CID-004)', event: 'Ganti Perangkat Modem ONT ZTE-F660', date: '2026-04-20', operator: 'Teknisi-01', status: 'Success' },
  ]);

  const fetchCustomers = async () => {
    setLoading(true);
    try {
      let url = `/customers?per_page=50`;
      if (statusFilter) url += `&status=${statusFilter}`;
      if (search) url += `&search=${encodeURIComponent(search)}`;

      const res = await api.get(url);
      const items = res.data?.data?.data || res.data?.data || res.data || [];
      const custList = Array.isArray(items) ? items : [];
      setCustomers(custList);

      if (!selectedCustomer && custList.length > 0) {
        setSelectedCustomer(custList[0]);
      } else if (selectedCustomer && custList.length > 0) {
        const found = custList.find((c) => c.id === selectedCustomer.id);
        if (found) setSelectedCustomer(found);
      }
    } catch (err) {
      showToast({ type: 'error', message: err.message || 'Gagal memuat data pelanggan.' });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchCustomers();
  }, [statusFilter]);

  const handleSearchSubmit = (e) => {
    e.preventDefault();
    fetchCustomers();
  };

  // CREATE (Full Registration)
  const handleFullRegistration = async (e) => {
    e.preventDefault();
    try {
      const res = await api.post('/customers', regForm);
      showToast({
        type: 'success',
        title: 'Registrasi Berhasil',
        message: `Akun pelanggan ${res.data?.data?.name || regForm.name} telah terdaftar dan dibuatkan kredensial PPPoE.`,
      });
      if (onNavigate) onNavigate('crm-daftar');
      fetchCustomers();
    } catch (err) {
      showToast({ type: 'error', message: err.message || 'Gagal mendaftarkan pelanggan.' });
    }
  };

  // UPDATE (Open Edit Modal)
  const handleOpenEdit = (customer) => {
    setEditForm({
      id: customer.id,
      cid: customer.cid,
      name: customer.name,
      phone: customer.phone || '',
      email: customer.email || '',
      address: customer.address || '',
      package_id: customer.package_id || customer.package?.id || '1',
      billing_type: customer.billing_type || 'prepaid',
      status: customer.status || 'active',
      gps_lat: Number(customer.gps_lat) || -6.289123,
      gps_lng: Number(customer.gps_lng) || 106.918456,
      pppoe_user: customer.pppoe_user || '',
    });
    setEditModalOpen(true);
  };

  // UPDATE (Save Edit Form)
  const handleUpdateCustomer = async (e) => {
    e.preventDefault();
    try {
      const res = await api.put(`/customers/${editForm.id}`, editForm);
      showToast({
        type: 'success',
        title: 'Perubahan Disimpan',
        message: `Data pelanggan ${editForm.name} berhasil diperbarui.`,
      });
      setEditModalOpen(false);
      fetchCustomers();
      if (selectedCustomer?.id === editForm.id) {
        setSelectedCustomer({ ...selectedCustomer, ...editForm });
      }
    } catch (err) {
      showToast({ type: 'error', message: err.message || 'Gagal memperbarui data pelanggan.' });
    }
  };

  // DELETE
  const handleDeleteCustomer = async (id, name) => {
    if (!window.confirm(`Yakin ingin menghapus data pelanggan "${name}"? Akun RADIUS dan riwayat terkait akan dihapus secara permanen.`)) return;
    setActionLoading(id);
    try {
      await api.delete(`/customers/${id}`);
      showToast({
        type: 'success',
        title: 'Pelanggan Dihapus',
        message: `Data pelanggan ${name} berhasil dihapus dari sistem.`,
      });
      fetchCustomers();
      if (selectedCustomer?.id === id) {
        setSelectedCustomer(null);
      }
    } catch (err) {
      showToast({ type: 'error', message: err.message || 'Gagal menghapus data pelanggan.' });
    } finally {
      setActionLoading(null);
    }
  };

  // RESET PASSWORD
  const handleResetPasswordSubmit = async (e) => {
    e.preventDefault();
    const custId = selectedCustomer?.id || editForm.id;
    if (!custId) return;
    try {
      await api.put(`/customers/${custId}`, {
        pppoe_password: newPassword || 'netpro' + Math.floor(1000 + Math.random() * 9000),
      });
      showToast({
        type: 'success',
        title: 'Password Berhasil Direset',
        message: `Password PPPoE baru telah disinkronkan ke RADIUS & dikirim ke WA pelanggan!`,
      });
      setResetPassModalOpen(false);
      fetchCustomers();
    } catch (err) {
      showToast({ type: 'error', message: err.message || 'Gagal mereset password.' });
    }
  };

  // OPERATIONAL: Set Online
  const handleSetOnline = async (id, name) => {
    setActionLoading(id);
    try {
      await api.post(`/customers/${id}/set-online`);
      showToast({
        type: 'success',
        title: 'Aktivasi Berhasil',
        message: `Pelanggan ${name} berhasil diaktifkan dan terhubung ke RADIUS.`,
      });
      fetchCustomers();
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setActionLoading(null);
    }
  };

  // OPERATIONAL: Isolate
  const handleIsolate = async (id, name) => {
    if (!window.confirm(`Yakin ingin mengisolir akun pelanggan ${name}? Sesi PPPoE aktif akan langsung diputus via CoA.`)) return;
    setActionLoading(id);
    try {
      await api.post(`/customers/${id}/isolate`);
      showToast({
        type: 'success',
        title: 'Akun Diisolir',
        message: `Pelanggan ${name} telah diisolir dan sesi PPPoE diputus.`,
      });
      fetchCustomers();
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setActionLoading(null);
    }
  };

  // EXCEL / SPREADSHEET STUDIO HANDLERS
  // 1. Download Sample Template for Excel (.xlsx)
  const handleDownloadTemplateExcel = () => {
    const data = [
      {
        'CID': 'CID-100201',
        'Nama Lengkap': 'Budi Santoso',
        'NIK': '3275010190010001',
        'No Telepon (WA)': '081234567890',
        'Email': 'budi.santoso@gmail.com',
        'Alamat Lengkap': 'Jl. Ahmad Yani No. 12 RT 01/02 Cilegon',
        'Koordinat Lat': -6.012345,
        'Koordinat Lng': 106.012345,
        'Nama Paket': 'Home Basic 20M',
        'Username PPPoE': '32750101-BUDI',
        'Password PPPoE': 'netpro123',
        'Tipe Billing (prepaid/postpaid)': 'postpaid',
        'Skema PPN (include/exclude)': 'include',
        'Status (active/inactive/isolated)': 'active'
      },
      {
        'CID': 'CID-100202',
        'Nama Lengkap': 'Siti Aminah',
        'NIK': '3275010291020002',
        'No Telepon (WA)': '085712345678',
        'Email': 'siti.aminah@yahoo.com',
        'Alamat Lengkap': 'Komplek Bukit Indah Blok C3 No. 8',
        'Koordinat Lat': -6.023456,
        'Koordinat Lng': 106.023456,
        'Nama Paket': 'Home Premium 50M',
        'Username PPPoE': '32750102-SITI',
        'Password PPPoE': 'netpro456',
        'Tipe Billing (prepaid/postpaid)': 'prepaid',
        'Skema PPN (include/exclude)': 'include',
        'Status (active/inactive/isolated)': 'active'
      },
      {
        'CID': 'CID-100203',
        'Nama Lengkap': 'PT Maju Bersama',
        'NIK': '3275010392030003',
        'No Telepon (WA)': '082198765432',
        'Email': 'it@majubersama.co.id',
        'Alamat Lengkap': 'Kawasan Industri Krakatau Kav. 15',
        'Koordinat Lat': -6.034567,
        'Koordinat Lng': 106.034567,
        'Nama Paket': 'Dedicated DIA 100M',
        'Username PPPoE': '32750103-PTMAJU',
        'Password PPPoE': 'netpro789',
        'Tipe Billing (prepaid/postpaid)': 'postpaid',
        'Skema PPN (include/exclude)': 'exclude',
        'Status (active/inactive/isolated)': 'active'
      }
    ];

    const ws = XLSX.utils.json_to_sheet(data);
    ws['!cols'] = [
      { wch: 15 }, // CID
      { wch: 25 }, // Nama
      { wch: 20 }, // NIK
      { wch: 18 }, // Telepon
      { wch: 25 }, // Email
      { wch: 38 }, // Alamat
      { wch: 15 }, // Lat
      { wch: 15 }, // Lng
      { wch: 20 }, // Paket
      { wch: 20 }, // PPPoE User
      { wch: 16 }, // Password
      { wch: 16 }, // Billing
      { wch: 14 }, // PPN
      { wch: 12 }, // Status
    ];

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Template Pelanggan');
    XLSX.writeFile(wb, 'Template_Import_Pelanggan_NETPRO.xlsx');

    showToast({
      type: 'success',
      title: 'Template Excel (.xlsx) Diunduh',
      message: 'Template_Import_Pelanggan_NETPRO.xlsx berhasil diunduh dan siap diisi di Excel.'
    });
  };

  // 2. Download Sample Template for CSV
  const handleDownloadTemplateCSV = () => {
    const headers = [
      'CID',
      'Nama Lengkap',
      'NIK',
      'No Telepon (WA)',
      'Email',
      'Alamat Lengkap',
      'Koordinat Lat',
      'Koordinat Lng',
      'Nama Paket',
      'Username PPPoE',
      'Password PPPoE',
      'Tipe Billing (prepaid/postpaid)',
      'Skema PPN (include/exclude)',
      'Status (active/inactive/isolated)'
    ];

    const sampleRows = [
      ['CID-100201', 'Budi Santoso', '3275010190010001', '081234567890', 'budi.santoso@gmail.com', 'Jl. Ahmad Yani No. 12 RT 01/02 Cilegon', '-6.012345', '106.012345', 'Home Basic 20M', '32750101-BUDI', 'netpro123', 'postpaid', 'include', 'active'],
      ['CID-100202', 'Siti Aminah', '3275010291020002', '085712345678', 'siti.aminah@yahoo.com', 'Komplek Bukit Indah Blok C3 No. 8', '-6.023456', '106.023456', 'Home Premium 50M', '32750102-SITI', 'netpro456', 'prepaid', 'include', 'active'],
      ['CID-100203', 'PT Maju Bersama', '3275010392030003', '082198765432', 'it@majubersama.co.id', 'Kawasan Industri Krakatau Kav. 15', '-6.034567', '106.034567', 'Dedicated DIA 100M', '32750103-PTMAJU', 'netpro789', 'postpaid', 'exclude', 'active']
    ];

    const csvContent = '\uFEFF' + [
      headers.map(h => `"${h.replace(/"/g, '""')}"`).join(','),
      ...sampleRows.map(row => row.map(cell => `"${String(cell || '').replace(/"/g, '""')}"`).join(','))
    ].join('\r\n');

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'Template_Import_Pelanggan_NETPRO.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    showToast({
      type: 'success',
      title: 'Template CSV Diunduh',
      message: 'Template_Import_Pelanggan_NETPRO.csv siap diisi via spreadsheet.'
    });
  };

  // 3. Export Live Customers to Real Microsoft Excel (.xlsx)
  const handleExportCustomersExcel = () => {
    if (!customers || customers.length === 0) {
      showToast({ type: 'warning', message: 'Belum ada data pelanggan untuk diexport.' });
      return;
    }

    const data = customers.map((c, idx) => ({
      'No': idx + 1,
      'CID': c.cid || '-',
      'Nama Pelanggan': c.name || '-',
      'NIK': c.nik || '-',
      'No Telepon (WA)': c.phone || '-',
      'Email': c.email || '-',
      'Alamat Lengkap': c.address || '-',
      'Koordinat Lat': c.gps_lat || '-',
      'Koordinat Lng': c.gps_lng || '-',
      'Paket Internet': c.package?.name || c.package_name || 'Home 20M',
      'Tarif Bulanan (Rp)': c.package?.price || c.package_price || 150000,
      'Tipe Billing': c.billing_type || 'postpaid',
      'Skema PPN': c.ppn_scheme || 'include',
      'Username PPPoE': c.pppoe_user || '-',
      'Status Akun': c.status || 'active',
      'Tanggal Registrasi': c.created_at ? new Date(c.created_at).toLocaleDateString('id-ID') : '-'
    }));

    const ws = XLSX.utils.json_to_sheet(data);
    ws['!cols'] = [
      { wch: 6 },
      { wch: 15 },
      { wch: 26 },
      { wch: 20 },
      { wch: 18 },
      { wch: 24 },
      { wch: 38 },
      { wch: 15 },
      { wch: 15 },
      { wch: 20 },
      { wch: 18 },
      { wch: 14 },
      { wch: 14 },
      { wch: 20 },
      { wch: 14 },
      { wch: 18 },
    ];

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Data Pelanggan NETPRO');
    const now = new Date();
    const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
    XLSX.writeFile(wb, `Data_Pelanggan_NETPRO_${dateStr}.xlsx`);

    showToast({
      type: 'success',
      title: 'Export Excel (.xlsx) Sukses',
      message: `Berhasil mengekspor ${customers.length} data pelanggan ke format Microsoft Excel (.xlsx).`
    });
  };

  // 4. Export Live Customers to CSV (.csv)
  const handleExportCustomersCSV = () => {
    if (!customers || customers.length === 0) {
      showToast({ type: 'warning', message: 'Belum ada data pelanggan untuk diexport.' });
      return;
    }

    const headers = [
      'No', 'CID', 'Nama Pelanggan', 'NIK', 'No Telepon (WA)', 'Email',
      'Alamat Lengkap', 'Koordinat Lat', 'Koordinat Lng', 'Paket Internet',
      'Tarif Bulanan (Rp)', 'Tipe Billing', 'Skema PPN', 'Username PPPoE',
      'Status Akun', 'Tanggal Registrasi'
    ];

    const rows = customers.map((c, idx) => [
      idx + 1,
      c.cid || '-',
      c.name || '-',
      c.nik || '-',
      c.phone || '-',
      c.email || '-',
      c.address || '-',
      c.gps_lat || '-',
      c.gps_lng || '-',
      c.package?.name || c.package_name || 'Home 20M',
      c.package?.price || c.package_price || 150000,
      c.billing_type || 'postpaid',
      c.ppn_scheme || 'include',
      c.pppoe_user || '-',
      c.status || 'active',
      c.created_at ? new Date(c.created_at).toLocaleDateString('id-ID') : '-'
    ]);

    const csvContent = '\uFEFF' + [
      headers.map(h => `"${h.replace(/"/g, '""')}"`).join(','),
      ...rows.map(row => row.map(cell => `"${String(cell || '').replace(/"/g, '""')}"`).join(','))
    ].join('\r\n');

    const now = new Date();
    const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', `Data_Pelanggan_NETPRO_${dateStr}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    showToast({
      type: 'success',
      title: 'Export CSV Sukses',
      message: `Berhasil mengekspor ${customers.length} data pelanggan ke format CSV.`
    });
  };

  // 5. Universal Excel & CSV File Reader (.xlsx, .xls, .csv, .txt)
  const handleFileUpload = (file) => {
    if (!file) return;
    setImportFileName(file.name);
    setImportResult(null);

    const reader = new FileReader();
    reader.onload = (e) => {
      try {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });
        const firstSheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];
        const jsonRows = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

        if (jsonRows.length < 2) {
          showToast({ type: 'error', message: 'File Excel / Spreadsheet tidak memiliki baris data pelanggan.' });
          setImportRows([]);
          return;
        }

        const headerRow = jsonRows[0].map(h => String(h || '').toLowerCase().replace(/[^a-z0-9]/g, ''));
        const colMap = {
          cid: headerRow.findIndex(h => h.includes('cid')),
          name: headerRow.findIndex(h => h.includes('nama') || h.includes('name') || h.includes('pelanggan')),
          nik: headerRow.findIndex(h => h.includes('nik') || h.includes('ktp')),
          phone: headerRow.findIndex(h => h.includes('tel') || h.includes('phone') || h.includes('wa') || h.includes('hp')),
          email: headerRow.findIndex(h => h.includes('email') || h.includes('surel')),
          address: headerRow.findIndex(h => h.includes('alamat') || h.includes('address')),
          gps_lat: headerRow.findIndex(h => h.includes('lat')),
          gps_lng: headerRow.findIndex(h => h.includes('lng') || h.includes('long') || h.includes('lon')),
          package_name: headerRow.findIndex(h => h.includes('paket') || h.includes('package') || h.includes('profile')),
          pppoe_user: headerRow.findIndex(h => h.includes('user') || h.includes('username') || h.includes('pppoe')),
          pppoe_password: headerRow.findIndex(h => h.includes('pass') || h.includes('password')),
          billing_type: headerRow.findIndex(h => h.includes('billing') || h.includes('bayar') || h.includes('tipe')),
          ppn_scheme: headerRow.findIndex(h => h.includes('ppn')),
          status: headerRow.findIndex(h => h.includes('status')),
        };

        const parsedData = [];
        for (let i = 1; i < jsonRows.length; i++) {
          const values = jsonRows[i];
          if (!values || values.length === 0 || !values.some(v => v !== null && v !== '')) continue;

          const name = colMap.name !== -1 ? String(values[colMap.name] || '') : String(values[1] || '');
          const phone = colMap.phone !== -1 ? String(values[colMap.phone] || '') : String(values[3] || '');
          if (!name.trim()) continue;

          parsedData.push({
            cid: colMap.cid !== -1 && values[colMap.cid] ? String(values[colMap.cid]) : '',
            name: name.trim(),
            nik: colMap.nik !== -1 && values[colMap.nik] ? String(values[colMap.nik]) : '',
            phone: phone.trim() || '081200000000',
            email: colMap.email !== -1 && values[colMap.email] ? String(values[colMap.email]) : '',
            address: colMap.address !== -1 && values[colMap.address] ? String(values[colMap.address]) : 'Alamat belum diatur',
            gps_lat: colMap.gps_lat !== -1 && values[colMap.gps_lat] ? String(values[colMap.gps_lat]) : '-6.289100',
            gps_lng: colMap.gps_lng !== -1 && values[colMap.gps_lng] ? String(values[colMap.gps_lng]) : '106.918200',
            package_name: colMap.package_name !== -1 && values[colMap.package_name] ? String(values[colMap.package_name]) : 'Home Basic 20M',
            pppoe_user: colMap.pppoe_user !== -1 && values[colMap.pppoe_user] ? String(values[colMap.pppoe_user]) : '',
            pppoe_password: colMap.pppoe_password !== -1 && values[colMap.pppoe_password] ? String(values[colMap.pppoe_password]) : 'netpro123',
            billing_type: colMap.billing_type !== -1 && values[colMap.billing_type] ? String(values[colMap.billing_type]).toLowerCase() : 'postpaid',
            ppn_scheme: colMap.ppn_scheme !== -1 && values[colMap.ppn_scheme] ? String(values[colMap.ppn_scheme]).toLowerCase() : 'include',
            status: colMap.status !== -1 && values[colMap.status] ? String(values[colMap.status]).toLowerCase() : 'active',
          });
        }

        setImportRows(parsedData);
        if (parsedData.length > 0) {
          setActiveCellCoord('A1');
          setActiveCellValue(parsedData[0].name || '');
        }
        showToast({
          type: 'success',
          title: 'Spreadsheet Excel Dimuat',
          message: `Berhasil memuat ${parsedData.length} baris data calon pelanggan dari ${file.name}.`
        });
      } catch (err) {
        showToast({ type: 'error', message: 'Gagal membaca file Excel/CSV: ' + err.message });
      }
    };
    reader.readAsArrayBuffer(file);
  };

  // 6. Direct Paste from Excel Clipboard (TSV / CSV format)
  const handlePasteProcess = (rawText) => {
    if (!rawText || !rawText.trim()) return;
    try {
      const lines = rawText.trim().split(/\r?\n/).filter(l => l.trim() !== '');
      if (lines.length === 0) return;

      const isFirstLineHeader = lines[0].toLowerCase().includes('nama') || lines[0].toLowerCase().includes('name') || lines[0].toLowerCase().includes('cid');
      const startIdx = isFirstLineHeader ? 1 : 0;

      const newRows = [];
      for (let i = startIdx; i < lines.length; i++) {
        const parts = lines[i].includes('\t') ? lines[i].split('\t') : lines[i].split(',');
        const cleanParts = parts.map(p => p.trim().replace(/^"|"$/g, ''));
        if (cleanParts.length === 0 || !cleanParts.some(p => p !== '')) continue;

        newRows.push({
          cid: cleanParts[0] && cleanParts[0].startsWith('CID-') ? cleanParts[0] : '',
          name: cleanParts[1] || cleanParts[0] || 'Pelanggan Baru',
          nik: cleanParts[2] || '',
          phone: cleanParts[3] || '081200000000',
          email: cleanParts[4] || '',
          address: cleanParts[5] || 'Alamat',
          gps_lat: cleanParts[6] || '-6.289100',
          gps_lng: cleanParts[7] || '106.918200',
          package_name: cleanParts[8] || 'Home Basic 20M',
          pppoe_user: cleanParts[9] || '',
          pppoe_password: cleanParts[10] || 'netpro123',
          billing_type: cleanParts[11] || 'postpaid',
          ppn_scheme: cleanParts[12] || 'include',
          status: cleanParts[13] || 'active',
        });
      }

      setImportRows((prev) => [...prev, ...newRows]);
      setPasteModalOpen(false);
      setPasteText('');
      showToast({
        type: 'success',
        title: 'Data Paste Dimasukkan',
        message: `Berhasil menambahkan ${newRows.length} baris dari clipboard Excel.`
      });
    } catch (err) {
      showToast({ type: 'error', message: 'Gagal memproses data paste: ' + err.message });
    }
  };

  // 7. Grid In-Place Cell Update
  const handleUpdateGridCell = (rowIndex, field, value) => {
    setImportRows((prev) => {
      const updated = [...prev];
      updated[rowIndex] = { ...updated[rowIndex], [field]: value };
      return updated;
    });
    setActiveCellValue(value);
  };

  // 8. Grid Row Management
  const handleAddNewGridRow = () => {
    const newRow = {
      cid: '',
      name: 'Pelanggan Baru ' + (importRows.length + 1),
      nik: '',
      phone: '0812',
      email: '',
      address: 'Alamat',
      gps_lat: '-6.289100',
      gps_lng: '106.918200',
      package_name: 'Home Basic 20M',
      pppoe_user: '',
      pppoe_password: 'netpro' + Math.floor(100 + Math.random() * 900),
      billing_type: 'postpaid',
      ppn_scheme: 'include',
      status: 'active',
    };
    setImportRows((prev) => [...prev, newRow]);
    showToast({ type: 'info', message: 'Baris baru ditambahkan ke grid spreadsheet.' });
  };

  const handleDeleteGridRow = (rowIndex) => {
    setImportRows((prev) => prev.filter((_, idx) => idx !== rowIndex));
  };

  const handleClearGrid = () => {
    if (importRows.length === 0) return;
    if (window.confirm('Yakin ingin mengosongkan seluruh baris di spreadsheet?')) {
      setImportRows([]);
      setImportFileName('');
      setActiveCellValue('');
    }
  };

  // 9. Submit Batch Import to Backend
  const handleImportSubmit = async () => {
    if (importRows.length === 0) {
      showToast({ type: 'warning', message: 'Spreadsheet masih kosong. Silakan buka file atau paste data.' });
      return;
    }

    setImporting(true);
    setImportResult(null);

    try {
      const res = await api.post('/customers/import', { customers: importRows });
      setImportResult(res.data);
      showToast({
        type: 'success',
        title: 'Import Excel Berhasil!',
        message: res.data.message || `Berhasil mengimpor ${res.data.imported_count} pelanggan ke database dan FreeRADIUS.`,
      });
      fetchCustomers();
      setTimeout(() => {
        setImportModalOpen(false);
        setImportRows([]);
        setImportFileName('');
        setImportResult(null);
      }, 1600);
    } catch (err) {
      showToast({
        type: 'error',
        title: 'Gagal Import',
        message: err.message || 'Terjadi kesalahan saat memproses data import.'
      });
    } finally {
      setImporting(false);
    }
  };

  // Subview: Registrasi Lengkap (3-Step Wizard matching registrasi.php)
  if (currentRoute === 'crm-registrasi') {
    const selectedPkg = packages.find((p) => String(p.id) === String(regForm.package_id)) || packages[0];
    const pkgPrice = selectedPkg ? selectedPkg.price : 250000;
    const isPpnInclude = regForm.ppn_scheme !== 'exclude';
    const dppAmount = isPpnInclude ? Math.round(pkgPrice / 1.11) : pkgPrice;
    const ppnAmount = isPpnInclude ? (pkgPrice - dppAmount) : Math.round(pkgPrice * 0.11);
    const totalFirstInvoice = isPpnInclude ? pkgPrice : (pkgPrice + ppnAmount);

    const autoGeneratePppoe = () => {
      const rawNik = (regForm.nik || '').replace(/[^0-9]/g, '');
      const prefix = rawNik.length >= 8 ? rawNik.substring(0, 8) : (rawNik || '32750101');
      const firstName = (regForm.name || 'USER').trim().split(' ')[0].toUpperCase().replace(/[^A-Z0-9]/g, '');
      const genUser = `${prefix}-${firstName}`;
      const genPass = String(Math.floor(100000 + Math.random() * 900000));
      setRegForm({
        ...regForm,
        pppoe_user: genUser,
        pppoe_password: genPass,
      });
      showToast({ type: 'success', message: `Kredensial PPPoE digenerate: ${genUser}` });
    };

    return (
      <div className="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-md max-w-4xl mx-auto space-y-6 text-xs">
        {/* Wizard Header & Progress Bar matching registrasi.php */}
        <div className="space-y-4 border-b border-slate-100 pb-5">
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
              <h3 className="font-bold text-slate-900 text-base flex items-center gap-2">
                <UserPlus className="w-5 h-5 text-blue-600" />
                <span>Registrasi Pelanggan Baru & Onboarding ISP</span>
              </h3>
              <p className="text-slate-400 text-xs">Ikuti 3 tahapan registrasi untuk aktivasi akun dan penerbitan faktur tagihan.</p>
            </div>
            <span className="px-3 py-1 bg-blue-50 text-blue-700 font-bold rounded-full border border-blue-200 text-[11px] shrink-0">
              Langkah {regStep} dari 3
            </span>
          </div>

          {/* Visual Step Stepper (Chevron Arrow Style) */}
          <div className="pt-2">
            <div className="w-full flex rounded-xl overflow-hidden border border-slate-200 font-bold text-xs">
              <button
                type="button"
                onClick={() => setRegStep(1)}
                className={`flex-1 py-2.5 px-3 flex items-center justify-center gap-2 transition cursor-pointer ${
                  regStep === 1
                    ? 'bg-[#7f1d1d] text-white'
                    : regStep > 1
                    ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                    : 'bg-slate-50 text-slate-400'
                }`}
              >
                <span className={`w-5 h-5 rounded-full flex items-center justify-center text-[10px] ${
                  regStep === 1 ? 'bg-red-600 text-white' : regStep > 1 ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-600'
                }`}>
                  {regStep > 1 ? '✓' : '1'}
                </span>
                <span>1. Data Identitas KTP</span>
              </button>

              <button
                type="button"
                onClick={() => {
                  if (regForm.name && regForm.nik && regForm.phone) setRegStep(2);
                  else showToast({ type: 'warning', message: 'Lengkapi Nama, NIK, dan No. HP di Tahap 1 terlebih dahulu.' });
                }}
                className={`flex-1 py-2.5 px-3 flex items-center justify-center gap-2 border-l border-slate-200 transition cursor-pointer ${
                  regStep === 2
                    ? 'bg-[#7f1d1d] text-white'
                    : regStep > 2
                    ? 'bg-slate-100 text-slate-700 hover:bg-slate-200'
                    : 'bg-slate-50 text-slate-400'
                }`}
              >
                <span className={`w-5 h-5 rounded-full flex items-center justify-center text-[10px] ${
                  regStep === 2 ? 'bg-red-600 text-white' : regStep > 2 ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-600'
                }`}>
                  {regStep > 2 ? '✓' : '2'}
                </span>
                <span>2. Paket & Pajak PPN</span>
              </button>

              <button
                type="button"
                onClick={() => {
                  if (regForm.name && regForm.nik && regForm.phone) setRegStep(3);
                  else showToast({ type: 'warning', message: 'Lengkapi data formulir terlebih dahulu.' });
                }}
                className={`flex-1 py-2.5 px-3 flex items-center justify-center gap-2 border-l border-slate-200 transition cursor-pointer ${
                  regStep === 3
                    ? 'bg-[#7f1d1d] text-white'
                    : 'bg-slate-50 text-slate-400'
                }`}
              >
                <span className={`w-5 h-5 rounded-full flex items-center justify-center text-[10px] ${
                  regStep === 3 ? 'bg-red-600 text-white' : 'bg-slate-300 text-slate-600'
                }`}>
                  3
                </span>
                <span>3. Lokasi & Aktivasi</span>
              </button>
            </div>
          </div>
        </div>

        <form onSubmit={handleFullRegistration} className="space-y-6">
          {/* ==================== STEP 1: DATA IDENTITAS ==================== */}
          {regStep === 1 && (
            <div className="space-y-4">
              <div className="p-4 bg-red-50/70 border border-red-100 rounded-2xl text-red-900 flex items-center gap-3">
                <i className="fa-solid fa-id-card text-red-600 text-lg shrink-0"></i>
                <div>
                  <strong className="font-bold block text-xs">Tahap 1: Identitas Legal Calon Pelanggan</strong>
                  <span className="text-[11px] text-red-700">Pastikan nomor KTP dan WhatsApp sesuai untuk keperluan kontrak & invoice digital.</span>
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">
                    Nama Lengkap (Sesuai KTP) <span className="text-rose-500">*</span>
                  </label>
                  <input
                    type="text"
                    required
                    placeholder="Contoh: Budi Santoso"
                    value={regForm.name}
                    onChange={(e) => setRegForm({ ...regForm, name: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium focus:bg-white focus:border-red-500 transition text-xs"
                  />
                </div>
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">
                    Nomor Induk Kependudukan (NIK) <span className="text-rose-500">*</span>
                  </label>
                  <input
                    type="text"
                    required
                    maxLength={16}
                    placeholder="3275xxxxxxxxxxxx"
                    value={regForm.nik}
                    onChange={(e) => setRegForm({ ...regForm, nik: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono focus:bg-white focus:border-red-500 transition text-xs"
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">
                    No. WhatsApp / Telepon <span className="text-rose-500">*</span>
                  </label>
                  <input
                    type="tel"
                    required
                    placeholder="081234567890"
                    value={regForm.phone}
                    onChange={(e) => setRegForm({ ...regForm, phone: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium focus:bg-white focus:border-red-500 transition text-xs"
                  />
                </div>
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Alamat Email Aktif</label>
                  <input
                    type="email"
                    placeholder="budi.santoso@gmail.com"
                    value={regForm.email}
                    onChange={(e) => setRegForm({ ...regForm, email: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-medium focus:bg-white focus:border-red-500 transition text-xs"
                  />
                </div>
              </div>

              <div>
                <label className="font-semibold text-slate-700 block mb-1">
                  Alamat Lengkap Domisili / Pemasangan <span className="text-rose-500">*</span>
                </label>
                <textarea
                  rows={3}
                  required
                  placeholder="Jl. Jatiwaringin Raya No. 45, RT 02/RW 05, Kel. Jaticempaka, Kec. Pondok Gede, Kota Bekasi..."
                  value={regForm.address}
                  onChange={(e) => setRegForm({ ...regForm, address: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:bg-white focus:border-red-500 transition text-xs"
                />
              </div>

              <div className="flex justify-end pt-2">
                <button
                  type="button"
                  onClick={() => {
                    if (!regForm.name || !regForm.nik || !regForm.phone || !regForm.address) {
                      showToast({ type: 'warning', message: 'Lengkapi Nama, NIK, No. HP, dan Alamat terlebih dahulu!' });
                      return;
                    }
                    setRegStep(2);
                  }}
                  className="bg-red-600 hover:bg-red-700 text-white font-extrabold px-6 py-2.5 rounded-xl shadow-lg shadow-red-950/30 transition flex items-center gap-2 cursor-pointer text-xs"
                >
                  <span>Lanjut ke Tahap 2 (Paket & PPN)</span>
                  <i className="fa-solid fa-arrow-right"></i>
                </button>
              </div>
            </div>
          )}

          {/* ==================== STEP 2: PAKET & PPN ==================== */}
          {regStep === 2 && (
            <div className="space-y-4">
              <div className="p-3.5 bg-indigo-50/70 border border-indigo-100 rounded-xl text-indigo-800 flex items-center gap-2.5">
                <i className="fa-solid fa-file-invoice-dollar text-indigo-600 text-base shrink-0"></i>
                <div>
                  <strong className="font-bold block text-xs">Tahap 2: Pemilihan Paket Bandwidth & Skema Perpajakan PPN</strong>
                  <span className="text-[11px] text-indigo-600">Sistem otomatis menghitung simulasi DPP & PPN 11% sesuai regulasi Dirjen Pajak.</span>
                </div>
              </div>

              {/* Tipe Model Penagihan */}
              <div className="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                <label className="font-bold text-slate-800 block text-xs flex items-center gap-2">
                  <i className="fa-solid fa-clock-rotate-left text-blue-600"></i> Tipe Model Penagihan (Billing Mode) <span className="text-rose-500">*</span>
                </label>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <label
                    onClick={() => setRegForm({ ...regForm, billing_type: 'postpaid' })}
                    className={`p-3 bg-white rounded-xl border-2 cursor-pointer flex items-start gap-3 transition shadow-xs ${
                      regForm.billing_type === 'postpaid' ? 'border-blue-500' : 'border-slate-200 hover:border-slate-300'
                    }`}
                  >
                    <input
                      type="radio"
                      name="billing_type"
                      checked={regForm.billing_type === 'postpaid'}
                      onChange={() => setRegForm({ ...regForm, billing_type: 'postpaid' })}
                      className="accent-blue-600 mt-1"
                    />
                    <div>
                      <strong className="block text-slate-900 font-bold text-xs">Pascabayar (Postpaid Fixed Date)</strong>
                      <span className="text-[10px] text-slate-500 leading-tight block mt-0.5">Tagihan rutin terbit tgl 1, jatuh tempo serentak tanggal 20.</span>
                    </div>
                  </label>

                  <label
                    onClick={() => setRegForm({ ...regForm, billing_type: 'prepaid' })}
                    className={`p-3 bg-white rounded-xl border-2 cursor-pointer flex items-start gap-3 transition shadow-xs ${
                      regForm.billing_type === 'prepaid' ? 'border-purple-500' : 'border-slate-200 hover:border-purple-400'
                    }`}
                  >
                    <input
                      type="radio"
                      name="billing_type"
                      checked={regForm.billing_type === 'prepaid'}
                      onChange={() => setRegForm({ ...regForm, billing_type: 'prepaid' })}
                      className="accent-purple-600 mt-1"
                    />
                    <div>
                      <div className="flex items-center gap-1.5">
                        <strong className="block text-slate-900 font-bold text-xs">Prabayar (Prepaid FTTH)</strong>
                        <span className="px-1.5 py-0.2 bg-purple-100 text-purple-700 font-bold text-[9px] rounded">Grace 30 Mnt</span>
                      </div>
                      <span className="text-[10px] text-slate-500 leading-tight block mt-0.5">Bayar / Top-up di awal. Mendukung Rolling 30 Hari & Fixed Date.</span>
                    </div>
                  </label>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Pilihan Paket Layanan Internet</label>
                  <select
                    value={regForm.package_id}
                    onChange={(e) => setRegForm({ ...regForm, package_id: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 focus:bg-white focus:border-blue-500 transition text-xs"
                  >
                    {packages.map((pkg) => (
                      <option key={pkg.id} value={pkg.id}>
                        {pkg.name} ({pkg.speed}) - Rp {pkg.price.toLocaleString('id-ID')}/bln
                      </option>
                    ))}
                  </select>
                </div>

                <div>
                  <label className="font-semibold text-slate-700 block mb-1">Pilih Skema PPN Tagihan Invoice</label>
                  <div className="grid grid-cols-2 gap-2 pt-0.5">
                    <label
                      onClick={() => setRegForm({ ...regForm, ppn_scheme: 'include' })}
                      className={`p-2.5 bg-white rounded-lg border-2 cursor-pointer flex items-center gap-2 font-bold text-xs transition ${
                        regForm.ppn_scheme !== 'exclude' ? 'border-blue-500 text-blue-900' : 'border-slate-200 text-slate-700'
                      }`}
                    >
                      <input
                        type="radio"
                        name="ppn_scheme"
                        checked={regForm.ppn_scheme !== 'exclude'}
                        onChange={() => setRegForm({ ...regForm, ppn_scheme: 'include' })}
                        className="accent-blue-600"
                      />
                      <span>Include PPN</span>
                    </label>

                    <label
                      onClick={() => setRegForm({ ...regForm, ppn_scheme: 'exclude' })}
                      className={`p-2.5 bg-white rounded-lg border-2 cursor-pointer flex items-center gap-2 font-bold text-xs transition ${
                        regForm.ppn_scheme === 'exclude' ? 'border-blue-500 text-blue-900' : 'border-slate-200 text-slate-700'
                      }`}
                    >
                      <input
                        type="radio"
                        name="ppn_scheme"
                        checked={regForm.ppn_scheme === 'exclude'}
                        onChange={() => setRegForm({ ...regForm, ppn_scheme: 'exclude' })}
                        className="accent-blue-600"
                      />
                      <span>Exclude PPN (+11%)</span>
                    </label>
                  </div>
                </div>
              </div>

              {/* Real-time Invoice Simulation Preview Card */}
              <div className="p-4 bg-slate-900 text-white rounded-2xl border border-slate-800 space-y-3">
                <div className="flex justify-between items-center border-b border-slate-800 pb-2">
                  <span className="text-[11px] font-bold text-blue-400 flex items-center gap-1.5">
                    <i className="fa-solid fa-calculator"></i> Pratinjau Tagihan Awal (Invoice Preview)
                  </span>
                  <span className="text-[10px] text-slate-400 font-mono">
                    Jatuh Tempo: {regForm.billing_type === 'prepaid' ? 'Saat Aktivasi' : 'Tgl 20'}
                  </span>
                </div>
                <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                  <div>
                    <p className="font-extrabold text-white text-sm">
                      {selectedPkg.name} - Rp {pkgPrice.toLocaleString('id-ID')} ({isPpnInclude ? 'Include PPN 11%' : 'Exclude PPN +11%'})
                    </p>
                    <span className="text-[10px] text-slate-400 block">Siklus otomatis dibuat oleh NETPRO Billing Engine</span>
                  </div>
                  <div className="flex gap-4 text-right">
                    <div>
                      <span className="text-slate-400 block text-[10px]">DPP</span>
                      <strong className="font-mono text-slate-200 text-xs">Rp {dppAmount.toLocaleString('id-ID')}</strong>
                    </div>
                    <div>
                      <span className="text-slate-400 block text-[10px]">PPN 11%</span>
                      <strong className="font-mono text-blue-400 text-xs">Rp {ppnAmount.toLocaleString('id-ID')}</strong>
                    </div>
                    <div>
                      <span className="text-slate-400 block text-[10px]">Total Bayar</span>
                      <strong className="font-mono text-emerald-400 text-sm font-extrabold">Rp {totalFirstInvoice.toLocaleString('id-ID')}</strong>
                    </div>
                  </div>
                </div>
              </div>

              <div className="flex justify-between pt-2">
                <button
                  type="button"
                  onClick={() => setRegStep(1)}
                  className="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer text-xs"
                >
                  <i className="fa-solid fa-arrow-left"></i>
                  <span>Kembali ke Tahap 1</span>
                </button>
                <button
                  type="button"
                  onClick={() => {
                    if (!regForm.pppoe_user) autoGeneratePppoe();
                    setRegStep(3);
                  }}
                  className="bg-red-600 hover:bg-red-700 text-white font-extrabold px-6 py-2.5 rounded-xl shadow-lg shadow-red-950/30 transition flex items-center gap-2 cursor-pointer text-xs"
                >
                  <span>Lanjut ke Tahap 3 (Lokasi GPS & Aktivasi)</span>
                  <i className="fa-solid fa-arrow-right"></i>
                </button>
              </div>
            </div>
          )}

          {/* ==================== STEP 3: LOKASI GPS & AKTIVASI ==================== */}
          {regStep === 3 && (
            <div className="space-y-4">
              <div className="p-3.5 bg-emerald-50/70 border border-emerald-100 rounded-xl text-emerald-800 flex items-center gap-2.5">
                <i className="fa-solid fa-map-pin text-emerald-600 text-base shrink-0"></i>
                <div>
                  <strong className="font-bold block text-xs">Tahap 3: Pemetaan Koordinat GPS & Penugasan ODP Port</strong>
                  <span className="text-[11px] text-emerald-600">Geser pin marker pada peta untuk menentukan koordinat presisi tarikan kabel dropcore.</span>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <div className="flex justify-between items-center mb-1">
                    <label className="font-semibold text-slate-700">Koordinat GPS Pemasangan</label>
                    <span className="text-[10px] text-blue-600 font-bold">📍 Drag & Drop Marker Peta</span>
                  </div>
                  <input
                    type="text"
                    required
                    value={regForm.gps_coords || '-6.2891, 106.9182'}
                    onChange={(e) => setRegForm({ ...regForm, gps_coords: e.target.value })}
                    className="w-full bg-slate-50 border border-blue-500 rounded-lg p-2.5 font-mono font-bold text-blue-700 focus:bg-white transition shadow-xs text-xs"
                  />
                </div>
                <div>
                  <label className="font-semibold text-slate-700 block mb-1">ODP / FAT Terdekat</label>
                  <select
                    value={regForm.odp_name}
                    onChange={(e) => setRegForm({ ...regForm, odp_name: e.target.value })}
                    className="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold text-slate-800 focus:bg-white focus:border-blue-500 transition text-xs font-mono"
                  >
                    <option value="ODP-CLG-01">ODP-CLG-01 (Port 3/8 Free)</option>
                    <option value="ODP-CLG-02">ODP-CLG-02 (Port 1/8 Free)</option>
                    <option value="ODP-CLG-04">ODP-CLG-04 (Port 5/8 Free)</option>
                    <option value="ODP-CLG-09">ODP-CLG-09 (Port 2/8 Free)</option>
                  </select>
                </div>
              </div>

              {/* Dedicated PPPoE Credentials Configuration Box */}
              <div className="p-4 bg-blue-50/60 border border-blue-200 rounded-xl space-y-3">
                <div className="flex justify-between items-center border-b border-blue-200/60 pb-2">
                  <span className="font-bold text-blue-900 flex items-center gap-1.5 text-xs">
                    <i className="fa-solid fa-key text-blue-600"></i> Akun Otentikasi PPPoE Pelanggan (Dialer ONT / Router)
                  </span>
                  <button
                    type="button"
                    onClick={autoGeneratePppoe}
                    className="text-[10px] text-blue-700 hover:text-blue-900 font-bold bg-white px-2 py-0.5 rounded border border-blue-300 shadow-xs flex items-center gap-1 cursor-pointer"
                  >
                    <i className="fa-solid fa-arrows-rotate"></i> Auto-Generate
                  </button>
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <div>
                    <label className="font-semibold text-slate-700 block mb-1">
                      Username PPPoE <span className="text-[10px] text-blue-600 font-normal">(Prefix: 8 Digit KTP-NAMA KAPITAL)</span>
                    </label>
                    <input
                      type="text"
                      required
                      placeholder="32750101-BUDI"
                      value={regForm.pppoe_user}
                      onChange={(e) => setRegForm({ ...regForm, pppoe_user: e.target.value })}
                      className="w-full bg-white border border-slate-300 rounded-lg p-2.5 font-mono font-bold text-blue-700 uppercase focus:border-blue-500 transition text-xs"
                    />
                  </div>
                  <div>
                    <label className="font-semibold text-slate-700 block mb-1">Password PPPoE</label>
                    <input
                      type="text"
                      required
                      placeholder="Min. 6 Karakter / Angka"
                      value={regForm.pppoe_password}
                      onChange={(e) => setRegForm({ ...regForm, pppoe_password: e.target.value })}
                      className="w-full bg-white border border-slate-300 rounded-lg p-2.5 font-mono font-bold text-slate-800 focus:border-blue-500 transition text-xs"
                    />
                  </div>
                </div>
                <p className="text-[10.5px] text-slate-500 leading-normal">
                  <i className="fa-solid fa-circle-info text-blue-500"></i> Format otomatis: <strong>8 digit awal NIK KTP - NAMA KAPITAL</strong>. Kredensial akan otomatis didaftarkan ke FreeRADIUS AAA / MikroTik NAS.
                </p>
              </div>

              {/* Map Container */}
              <div className="space-y-1.5">
                <span className="font-bold text-slate-800 text-xs">Peta Lokasi Pemasangan (Google Maps GIS & GPS Picker)</span>
                <GpsMap
                  lat={parseFloat(regForm.gps_coords?.split(',')[0]) || -6.289123}
                  lng={parseFloat(regForm.gps_coords?.split(',')[1]) || 106.918456}
                  title={regForm.name || 'Calon Pelanggan'}
                  subtitle={regForm.gps_coords || '-6.2891, 106.9182'}
                  height="200px"
                  zoom={15}
                  interactive={true}
                  onChange={(lat, lng) => {
                    setRegForm((prev) => ({
                      ...prev,
                      gps_coords: `${lat.toFixed(6)}, ${lng.toFixed(6)}`,
                    }));
                  }}
                />
              </div>

              {/* Review Summary Before Final Submit */}
              <div className="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                <span className="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Ringkasan Konfirmasi Registrasi</span>
                <div className="grid grid-cols-1 sm:grid-cols-4 gap-3 text-[11px]">
                  <div>
                    <span className="text-slate-400 block">Calon Pelanggan:</span>
                    <strong className="text-slate-800 font-bold">{regForm.name || '-'}</strong>
                  </div>
                  <div>
                    <span className="text-slate-400 block">Paket Internet:</span>
                    <strong className="text-blue-600 font-bold">{selectedPkg.name}</strong>
                  </div>
                  <div>
                    <span className="text-slate-400 block">Tipe Koneksi:</span>
                    <strong className="text-indigo-600 font-bold">PPPoE Client</strong>
                  </div>
                  <div>
                    <span className="text-slate-400 block">Estimasi Tagihan Pertama:</span>
                    <strong className="text-emerald-600 font-bold font-mono">Rp {totalFirstInvoice.toLocaleString('id-ID')}</strong>
                  </div>
                </div>
              </div>

              <div className="flex justify-between pt-2">
                <button
                  type="button"
                  onClick={() => setRegStep(2)}
                  className="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer text-xs"
                >
                  <i className="fa-solid fa-arrow-left"></i>
                  <span>Kembali ke Tahap 2</span>
                </button>
                <button
                  type="submit"
                  className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-emerald-500/25 transition flex items-center gap-2 text-xs cursor-pointer"
                >
                  <i className="fa-solid fa-circle-check"></i>
                  <span>Selesaikan Registrasi & Terbitkan Invoice Pertama</span>
                </button>
              </div>
            </div>
          )}
        </form>
      </div>
    );
  }

  // Subview: Profil 360 & Telemetri Pelanggan (crm-detail)
  if (currentRoute === 'crm-detail') {
    const cust = selectedCustomer || customers[0] || {
      id: 0,
      cid: '-',
      name: '-',
      nik: '-',
      phone: '-',
      email: '-',
      address: '-',
      gps_lat: 0,
      gps_lng: 0,
      package_name: '-',
      speed_mbps: 0,
      package_price: 0,
      ppn_scheme: '-',
      auth_method: 'pppoe',
      status: 'inactive',
      created_at: '2026-09-01',
    };

    const isOnline = cust.status === 'active' || cust.status === 'aktif';
    const isIsolated = cust.status === 'isolated' || cust.status === 'terisolir';
    const isInactive = !isOnline && !isIsolated;

    return (
      <div className="space-y-6 text-xs">
        {/* Header Profile Bar matching detail.php */}
        <div className="bg-white p-4 sm:p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
          <div className="flex items-start sm:items-center gap-3.5 sm:gap-4 w-full lg:w-auto">
            <div className="w-13 h-13 sm:w-16 sm:h-16 bg-gradient-to-br from-red-600 to-rose-700 text-white rounded-2xl flex items-center justify-center font-black text-xl sm:text-2xl shadow-md shrink-0">
              {cust.name && cust.name !== '-' ? cust.name.substring(0, 2).toUpperCase() : '-'}
            </div>
            <div className="flex-1 min-w-0">
              <div className="flex flex-wrap items-center gap-2">
                <h2 className="font-extrabold text-slate-900 text-base sm:text-lg break-words leading-tight">
                  {cust.name}
                </h2>
                {isOnline ? (
                  <span className="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full font-bold text-[10px] flex items-center gap-1 shrink-0">
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> ONLINE / AKTIF
                  </span>
                ) : isIsolated ? (
                  <span className="px-2.5 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded-full font-bold text-[10px] flex items-center gap-1 shrink-0">
                    <span className="w-1.5 h-1.5 rounded-full bg-rose-500"></span> ISOLIR / TUNGGAKAN
                  </span>
                ) : (
                  <span className="px-2.5 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-full font-bold text-[10px] flex items-center gap-1 shrink-0">
                    <span className="w-1.5 h-1.5 rounded-full bg-slate-400"></span> INACTIVE / BELUM AKTIF
                  </span>
                )}
                <span className="px-2 py-0.5 bg-red-50 text-red-700 border border-red-100 rounded-lg font-mono text-[10px] font-bold shrink-0">
                  {cust.cid || '-'}
                </span>
              </div>
              <p className="text-slate-400 text-[11px] sm:text-xs mt-1 leading-snug">
                Paket: <strong className="text-slate-700 font-semibold">{cust.package?.name || cust.package_name || '-'} ({cust.speed_mbps || cust.package?.speed || '0 Mbps'})</strong> • Terdaftar sejak {cust.created_at ? new Date(cust.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '01 Sep 2026'}
              </p>
            </div>
          </div>

          {/* Quick Action Buttons matching detail.php */}
          <div className="flex flex-wrap items-center gap-2 w-full lg:w-auto pt-2 lg:pt-0 border-t border-slate-100 lg:border-t-0">
            <button
              onClick={() => showToast({ type: 'success', title: 'Kirim WhatsApp', message: `Pesan rincian tagihan WhatsApp terkirim ke ${cust.phone}` })}
              className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-2 rounded-xl shadow transition flex items-center gap-1.5 cursor-pointer text-xs"
            >
              <i className="fa-brands fa-whatsapp text-sm"></i>
              <span>Kirim WA</span>
            </button>
            <button
              onClick={() => {
                setResetPassModalOpen(true);
              }}
              className="bg-slate-800 hover:bg-slate-700 text-white font-bold px-3 py-2 rounded-xl shadow transition flex items-center gap-1.5 cursor-pointer text-xs"
            >
              <i className="fa-solid fa-key"></i>
              <span>Reset Password</span>
            </button>
            {isOnline ? (
              <>
                <button
                  onClick={() => showToast({ type: 'info', title: 'PPPoE Disconnect', message: 'Sesi PPPoE di-kick dari MikroTik NAS & dial ulang otomatis.' })}
                  className="bg-amber-500 hover:bg-amber-600 text-white font-bold px-3 py-2 rounded-xl shadow transition flex items-center gap-1.5 cursor-pointer text-xs"
                >
                  <i className="fa-solid fa-rotate"></i>
                  <span>Re-Koneksi</span>
                </button>
                <button
                  onClick={() => handleIsolate(cust.id, cust.name)}
                  className="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold px-3 py-2 rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer text-xs"
                >
                  <i className="fa-solid fa-ban"></i>
                  <span>Isolir Akun</span>
                </button>
              </>
            ) : (
              <button
                onClick={() => handleSetOnline(cust.id, cust.name)}
                className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3.5 py-2 rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer text-xs"
              >
                <i className="fa-solid fa-circle-check"></i>
                <span>Aktivasi & Set Online</span>
              </button>
            )}
            <button
              onClick={() => onNavigate && onNavigate('noc-tickets')}
              className="bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 font-bold px-3 py-2 rounded-xl shadow-sm transition flex items-center gap-1.5 cursor-pointer text-xs"
            >
              <i className="fa-solid fa-ticket"></i>
              <span>Buka Tiket</span>
            </button>
            <button
              onClick={() => onNavigate && onNavigate('crm-instalasi')}
              className="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-3 py-2 rounded-xl transition flex items-center gap-1.5 cursor-pointer text-xs"
            >
              <i className="fa-solid fa-screwdriver-wrench"></i>
              <span>Terbitkan WO</span>
            </button>
          </div>
        </div>

        {/* 4 Telemetry & Status Indicator Cards matching detail.php */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          {/* Card 1: Otentikasi Jaringan */}
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div className="flex justify-between items-center border-b border-slate-100 pb-2">
              <h4 className="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                <i className="fa-solid fa-network-wired text-blue-600"></i> Otentikasi Jaringan
              </h4>
              {isOnline ? (
                <span className="px-2 py-0.5 bg-emerald-50 text-emerald-700 font-bold rounded text-[10px]">CONNECTED</span>
              ) : isIsolated ? (
                <span className="px-2 py-0.5 bg-rose-50 text-rose-700 font-bold rounded text-[10px]">SUSPENDED</span>
              ) : (
                <span className="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold rounded text-[10px]">BELUM AKTIF</span>
              )}
            </div>
            <div className="space-y-1.5">
              <div className="flex justify-between">
                <span className="text-slate-400">Tipe Koneksi:</span>
                <span className="px-1.5 py-0.2 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">PPPoE Client</span>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-slate-400">Username:</span>
                <strong className="font-mono text-slate-900 font-bold">{cust.pppoe_user || '-'}</strong>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-slate-400">Password:</span>
                <div className="flex items-center gap-1.5 font-mono">
                  <span className="font-bold text-slate-800 tracking-widest">{cust.pppoe_password ? cust.pppoe_password : '••••••'}</span>
                  <button type="button" className="text-slate-400 hover:text-blue-600 text-xs cursor-pointer">
                    <i className="fa-solid fa-eye text-[10px]"></i>
                  </button>
                </div>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">IP Terpasang:</span>
                {isOnline ? (
                  <strong className="font-mono text-blue-600">{cust.ip_address || '10.100.20.15'}</strong>
                ) : isIsolated ? (
                  <strong className="font-mono text-rose-600">10.200.0.15 (Pool Isolir)</strong>
                ) : (
                  <strong className="font-mono text-slate-400">Belum Terkoneksi (N/A)</strong>
                )}
              </div>
              <div className="pt-1 flex justify-end">
                <button
                  type="button"
                  onClick={() => setResetPassModalOpen(true)}
                  className="text-[10px] text-blue-600 hover:text-blue-800 font-bold hover:underline cursor-pointer"
                >
                  <i className="fa-solid fa-key"></i> Reset Password
                </button>
              </div>
            </div>
          </div>

          {/* Card 2: Billing & Skema PPN */}
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div className="flex justify-between items-center border-b border-slate-100 pb-2">
              <h4 className="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                <i className="fa-solid fa-file-invoice-dollar text-emerald-600"></i> Billing & Skema PPN
              </h4>
              <span className="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold rounded text-[10px]">
                {cust.package_price || cust.package?.price ? 'LUNAS (TERKINI)' : 'BELUM ADA INVOICE'}
              </span>
            </div>
            <div className="space-y-1.5">
              <div className="flex justify-between">
                <span className="text-slate-400">Tarif Bulanan:</span>
                <strong className="font-mono text-slate-900">
                  Rp {Number(cust.package_price || cust.package?.price || 0).toLocaleString('id-ID')}
                </strong>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">Skema PPN 11%:</span>
                <span className="px-1.5 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px] uppercase">
                  {cust.ppn_scheme || '-'}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">Siklus Billing:</span>
                <strong className="text-slate-700">
                  {cust.billing_type === 'prepaid' ? 'Prabayar (Rolling 30 Hari)' : 'Pascabayar (Tgl 1 - 20)'}
                </strong>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">Metode Bayar:</span>
                <strong className="text-slate-700">QRIS / VA Bank BCA</strong>
              </div>
            </div>
          </div>

          {/* Card 3: GPON & Optical Loss */}
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div className="flex justify-between items-center border-b border-slate-100 pb-2">
              <h4 className="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                <i className="fa-solid fa-tower-broadcast text-indigo-600"></i> GPON & Optical Loss
              </h4>
              <span className="px-2 py-0.5 bg-amber-50 text-amber-700 font-bold rounded text-[10px]">
                {cust.ont_sn ? 'TERPASANG' : 'BELUM INSTALASI'}
              </span>
            </div>
            <div className="space-y-1.5">
              <div className="flex justify-between">
                <span className="text-slate-400">Redaman OPM:</span>
                {cust.rx_power ? (
                  <strong className="font-mono text-emerald-600 font-bold">{cust.rx_power}</strong>
                ) : (
                  <span className="text-slate-400 font-mono">Belum Diukur (N/A)</span>
                )}
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">ODP Box / Port:</span>
                {cust.odp ? (
                  <strong className="font-mono text-slate-900">{cust.odp}</strong>
                ) : (
                  <span className="text-slate-400">Belum Ditugaskan</span>
                )}
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">SN Modem ONT:</span>
                {cust.ont_sn ? (
                  <strong className="font-mono text-slate-700">{cust.ont_sn}</strong>
                ) : (
                  <span className="text-slate-400">Belum Dipasang</span>
                )}
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">Status SPK / WO:</span>
                <strong className="text-amber-600 font-normal">Perlu Terbitkan WO</strong>
              </div>
            </div>
          </div>

          {/* Card 4: Trafik Real-time */}
          <div className="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
            <div className="flex justify-between items-center border-b border-slate-100 pb-2">
              <h4 className="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                <i className="fa-solid fa-chart-line text-purple-600"></i> Trafik Real-time
              </h4>
              {isOnline ? (
                <span className="px-2 py-0.5 bg-purple-50 text-purple-700 font-bold rounded text-[10px]">UNLIMITED FUP</span>
              ) : isIsolated ? (
                <span className="px-2 py-0.5 bg-rose-50 text-rose-700 font-bold rounded text-[10px]">ISOLATED (0 MB)</span>
              ) : (
                <span className="px-2 py-0.5 bg-slate-100 text-slate-600 font-bold rounded text-[10px]">OFFLINE (0 MB)</span>
              )}
            </div>
            <div className="space-y-1.5">
              <div className="flex justify-between">
                <span className="text-slate-400">Download Rate:</span>
                <span className="font-mono text-slate-400">
                  {isOnline ? '24.5 Mbps / 50M' : `0.0 Mbps / ${cust.speed_mbps || 0}M`}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">Upload Rate:</span>
                <span className="font-mono text-slate-400">
                  {isOnline ? '8.2 Mbps / 50M' : `0.0 Mbps / ${cust.speed_mbps || 0}M`}
                </span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">Akumulasi Bulan Ini:</span>
                <span className="text-slate-400 font-mono">{isOnline ? '312.4 GB' : '0.0 GB'}</span>
              </div>
              <div className="flex justify-between">
                <span className="text-slate-400">MTU Jaringan:</span>
                <strong className="font-mono text-slate-700">1492 (PPPoE)</strong>
              </div>
            </div>
          </div>
        </div>

        {/* Main Content 2 Columns matching detail.php */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
          {/* Left Column: Identitas & Map Location (5 Cols) */}
          <div className="lg:col-span-5 space-y-6">
            {/* Customer Biodata Card */}
            <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
              <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 className="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                  <i className="fa-solid fa-id-card text-blue-600"></i> Identitas & Kontak Pelanggan
                </h3>
                <button
                  onClick={() => handleOpenEdit(cust)}
                  className="text-blue-600 font-bold hover:underline cursor-pointer"
                >
                  Edit
                </button>
              </div>
              <div className="space-y-2.5">
                <div>
                  <span className="text-slate-400 block text-[10px] uppercase font-semibold">Nomor Induk Kependudukan (NIK)</span>
                  <strong className="font-mono text-slate-900 text-xs">{cust.nik || '-'}</strong>
                </div>
                <div>
                  <span className="text-slate-400 block text-[10px] uppercase font-semibold">Nomor WhatsApp Aktif</span>
                  <strong className="font-mono text-blue-600 text-xs block">{cust.phone || '-'}</strong>
                </div>
                <div>
                  <span className="text-slate-400 block text-[10px] uppercase font-semibold">Alamat Email</span>
                  <strong className="text-slate-800 text-xs">{cust.email || '-'}</strong>
                </div>
                <div>
                  <span className="text-slate-400 block text-[10px] uppercase font-semibold">Alamat Lengkap Pemasangan</span>
                  <p className="text-slate-700 text-xs leading-relaxed">{cust.address || '-'}</p>
                </div>
              </div>
            </div>

            {/* GPS Location Card with interactive Map preview */}
            <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-3">
              <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                  <h3 className="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                    <i className="fa-solid fa-map-location-dot text-emerald-600"></i> Titik Koordinat GPS Pemasangan
                  </h3>
                  <p className="text-slate-400 font-mono text-[10px]">{cust.gps_lat || 0}, {cust.gps_lng || 0}</p>
                </div>
                <a
                  href={`https://maps.google.com/?q=${cust.gps_lat || 0},${cust.gps_lng || 0}`}
                  target="_blank"
                  rel="noreferrer"
                  className="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-2.5 py-1 rounded-lg flex items-center gap-1 text-[11px] cursor-pointer"
                >
                  <i className="fa-solid fa-arrow-up-right-from-square"></i> Google Maps
                </a>
              </div>

              {/* View-Only Leaflet GPS Map */}
              <GpsMap
                lat={cust.gps_lat || -6.289123}
                lng={cust.gps_lng || 106.918456}
                title={cust.name}
                subtitle={cust.cid}
                height="200px"
                zoom={15}
                interactive={false}
                showSearch={false}
              />
            </div>
          </div>

          {/* Right Column: Riwayat Tagihan & Tiket (7 Cols) */}
          <div className="lg:col-span-7 space-y-6">
            {/* Billing History Table matching detail.php */}
            <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
              <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                  <h3 className="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                    <i className="fa-solid fa-receipt text-blue-600"></i> Riwayat Tagihan & Kwitansi Invoice
                  </h3>
                  <p className="text-slate-400 text-xs">Daftar penerbitan invoice bulanan dan status pelunasan.</p>
                </div>
                <button
                  onClick={() => onNavigate && onNavigate('billing-invoices')}
                  className="text-blue-600 font-bold hover:underline cursor-pointer"
                >
                  Lihat Semua Tagihan
                </button>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-xs">
                      <th className="py-2.5 px-3">No. Invoice</th>
                      <th className="py-2.5 px-3">Periode</th>
                      <th className="py-2.5 px-3 font-mono text-right">Total Tagihan</th>
                      <th className="py-2.5 px-3 text-center">Status</th>
                      <th className="py-2.5 px-3 text-right">Kwitansi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colSpan={5} className="py-8 text-center text-slate-400 text-xs">
                        Belum ada riwayat tagihan invoice untuk pelanggan ini.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            {/* Support Ticket History Table matching detail.php */}
            <div className="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
              <div className="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                  <h3 className="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                    <i className="fa-solid fa-ticket text-amber-600"></i> Riwayat Tiket Komplain & Bantuan Teknis
                  </h3>
                  <p className="text-slate-400 text-xs">Catatan gangguan, penugasan teknisi, dan penyelesaian masalah.</p>
                </div>
                <button
                  onClick={() => showToast({ type: 'info', message: 'Form pembukaan tiket keluhan dibuka.' })}
                  className="bg-red-600 hover:bg-red-700 text-white font-extrabold px-3 py-1.5 rounded-xl text-[11px] shadow-sm transition cursor-pointer"
                >
                  + Buat Tiket Baru
                </button>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold text-xs">
                      <th className="py-2.5 px-3">No. Tiket</th>
                      <th className="py-2.5 px-3">Keluhan Masalah</th>
                      <th className="py-2.5 px-3">Teknisi Bertugas</th>
                      <th className="py-2.5 px-3 text-center">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colSpan={4} className="py-8 text-center text-slate-400 text-xs">
                        Belum ada riwayat tiket gangguan untuk pelanggan ini.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // Subview: Riwayat Berlangganan (crm-riwayat)
  if (currentRoute === 'crm-riwayat') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Riwayat Berlangganan & Log Perubahan Status Pelanggan</h2>
            <p className="text-xs text-slate-500">Histori perubahan paket, rekoneksi isolir, mutasi perangkat ONT, dan audit trail.</p>
          </div>
        </div>

        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <table className="custom-table">
            <thead>
              <tr>
                <th>ID LOG</th>
                <th>PELANGGAN</th>
                <th>KEGIATAN / PERISTIWA</th>
                <th>TANGGAL</th>
                <th>OPERATOR / ENGINE</th>
                <th>STATUS</th>
              </tr>
            </thead>
            <tbody>
              {subscriptionLogs.map((log) => (
                <tr key={log.id}>
                  <td className="font-mono text-xs font-bold text-red-600">{log.id}</td>
                  <td className="font-bold text-slate-900 text-xs">{log.cust}</td>
                  <td className="text-xs text-slate-700">{log.event}</td>
                  <td className="text-xs text-slate-500">{log.date}</td>
                  <td className="text-xs font-semibold text-slate-600">{log.operator}</td>
                  <td><span className="badge badge-success text-[10px]">{log.status}</span></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    );
  }

  // Subview: Paket Internet
  if (currentRoute === 'crm-paket') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Katalog Paket Internet Broadband FTTH & Dedicated</h2>
            <p className="text-xs text-slate-500">Konfigurasi bandwidth upload/download, rasio FUP, profile RADIUS, dan harga dasar.</p>
          </div>
          <button onClick={() => showToast({ type: 'info', message: 'Fitur Tambah Paket Baru dibuka' })} className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer">
            <Tag className="w-3.5 h-3.5" />
            <span>Tambah Paket</span>
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
          {packages.map((pkg) => (
            <div key={pkg.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition space-y-4 relative overflow-hidden">
              <span className="absolute top-3 right-3 text-[10px] font-bold uppercase tracking-wider bg-red-50 text-red-600 px-2 py-0.5 rounded-full border border-red-200">
                {pkg.badge}
              </span>
              <div>
                <h3 className="text-lg font-bold text-slate-900">{pkg.name}</h3>
                <div className="flex items-baseline gap-1 mt-1">
                  <span className="text-2xl font-extrabold text-red-600">Rp {pkg.price.toLocaleString('id-ID')}</span>
                  <span className="text-xs text-slate-400">/ bulan</span>
                </div>
              </div>

              <div className="space-y-2 text-xs border-t border-slate-100 pt-3">
                <div className="flex justify-between text-slate-600">
                  <span>Kecepatan Up/Down:</span>
                  <strong className="text-slate-900">{pkg.speed}</strong>
                </div>
                <div className="flex justify-between text-slate-600">
                  <span>Batas FUP / Kuota:</span>
                  <strong className="text-slate-900">{pkg.fup}</strong>
                </div>
                <div className="flex justify-between text-slate-600">
                  <span>Pelanggan Aktif:</span>
                  <strong className="text-emerald-600 font-bold">{pkg.users} Pengguna</strong>
                </div>
              </div>

              <button
                onClick={() => showToast({ type: 'success', message: `Profile RADIUS ${pkg.name} disinkronkan ke MikroTik NAS.` })}
                className="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition cursor-pointer"
              >
                Sinkronisasi MikroTik
              </button>
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Subview: Add-on Layanan
  if (currentRoute === 'crm-addon') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Add-On & Layanan Tambahan Pelanggan</h2>
            <p className="text-xs text-slate-500">Katalog layanan tambahan seperti IP Publik Statis, STB OTT TV, dan Speed Booster harian.</p>
          </div>
          <button onClick={() => showToast({ type: 'info', message: 'Form Add-On baru dibuka.' })} className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer">
            <Sparkles className="w-3.5 h-3.5" />
            <span>Tambah Add-On</span>
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          {addons.map((add) => (
            <div key={add.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
              <div className="flex justify-between items-start">
                <div>
                  <span className="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200 uppercase">{add.category}</span>
                  <h3 className="text-sm font-bold text-slate-900 mt-1">{add.name}</h3>
                </div>
                <strong className="text-sm font-bold text-red-600">Rp {add.price.toLocaleString('id-ID')} / bln</strong>
              </div>
              <p className="text-xs text-slate-500">{add.desc}</p>
              <div className="pt-2 flex justify-end gap-2">
                <button onClick={() => showToast({ type: 'success', message: `Layanan ${add.name} ditambahkan ke penagihan.` })} className="text-xs py-1.5 px-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition cursor-pointer">
                  Terapkan ke Pelanggan
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Subview: Promo & Voucher
  if (currentRoute === 'crm-promo') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Manajemen Kupon & Promo Diskon Tagihan</h2>
            <p className="text-xs text-slate-500">Kode promo akuisisi sales, diskon pembayaran tahunan, dan kupon gratis biaya pasang.</p>
          </div>
          <button onClick={() => showToast({ type: 'info', message: 'Form Promo baru dibuka.' })} className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5 cursor-pointer">
            <Tag className="w-3.5 h-3.5" />
            <span>Buat Kode Promo</span>
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          {promos.map((p) => (
            <div key={p.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3 relative">
              <div className="flex justify-between items-center">
                <span className="font-mono text-xs font-bold text-red-700 bg-red-50 border border-red-200 px-2 py-1 rounded-lg tracking-wider">
                  {p.code}
                </span>
                <span className="text-[11px] text-slate-400">Exp: {p.exp}</span>
              </div>
              <div>
                <h4 className="text-sm font-bold text-slate-900">{p.disc}</h4>
                <p className="text-xs text-slate-500 mt-1">{p.desc}</p>
              </div>
              <div className="pt-3 border-t border-slate-100 flex justify-between items-center text-xs text-slate-600">
                <span>Telah Digunakan:</span>
                <strong className="text-slate-900">{p.usage}x transaksi</strong>
              </div>
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Subview: Survey Lapangan
  if (currentRoute === 'crm-survey') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Antrean Survey Lokasi & Validasi Redaman FAT/ODP</h2>
            <p className="text-xs text-slate-500">Verifikasi jarak tiang, kapasitas port ODP terdekat, dan kelayakan redaman dBm calon pelanggan.</p>
          </div>
        </div>

        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <table className="custom-table">
            <thead>
              <tr>
                <th>ID SURVEY / NAMA</th>
                <th>ALAMAT LOKASI</th>
                <th>ODP TERDEKAT</th>
                <th>REDAMAN HASIL</th>
                <th>STATUS</th>
                <th className="text-right">AKSI</th>
              </tr>
            </thead>
            <tbody>
              {surveys.map((s) => (
                <tr key={s.id}>
                  <td>
                    <div>
                      <span className="font-mono text-xs font-bold text-red-600">{s.id}</span>
                      <p className="font-bold text-slate-900 text-xs">{s.name}</p>
                    </div>
                  </td>
                  <td className="text-xs text-slate-600">{s.address}</td>
                  <td>
                    <span className="font-mono text-xs font-semibold text-slate-800">{s.odp}</span>
                  </td>
                  <td>
                    <span className="font-mono text-xs font-bold text-emerald-600">{s.signal}</span>
                  </td>
                  <td>
                    <span className={`text-[11px] font-bold px-2 py-0.5 rounded-full border ${
                      s.status === 'Approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'
                    }`}>
                      {s.status}
                    </span>
                  </td>
                  <td className="text-right">
                    <button onClick={() => showToast({ type: 'success', message: `Survey ${s.id} telah disetujui untuk diteruskan ke Instalasi.` })} className="text-xs py-1 px-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition cursor-pointer">
                      Proses WO Pasang
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    );
  }

  // Subview: Instalasi Baru
  if (currentRoute === 'crm-instalasi') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Perintah Kerja Instalasi Baru (Work Orders)</h2>
            <p className="text-xs text-slate-500">Penugasan teknisi lapangan, alokasi dropcore FO, binding serial number modem ONT ke OLT.</p>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          {installations.map((ins) => (
            <div key={ins.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
              <div className="flex justify-between items-center">
                <span className="font-mono text-xs font-bold text-red-600">{ins.id}</span>
                <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full border ${
                  ins.status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200'
                }`}>
                  {ins.status}
                </span>
              </div>
              <div>
                <h4 className="text-sm font-bold text-slate-900">{ins.cust}</h4>
                <p className="text-xs text-slate-500">Paket: <strong>{ins.package}</strong></p>
              </div>
              <div className="text-xs space-y-1 bg-slate-50 p-3 rounded-xl border border-slate-100 font-mono">
                <div className="flex justify-between">
                  <span className="text-slate-500">FAT/ODP:</span>
                  <span className="font-semibold text-slate-800">{ins.odp}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-500">Dropcore:</span>
                  <span className="font-semibold text-slate-800">{ins.dropcore}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-500">Teknisi:</span>
                  <span className="font-semibold text-slate-800">{ins.technician}</span>
                </div>
              </div>
              <button
                onClick={() => showToast({ type: 'success', message: `Work Order ${ins.id} berhasil diselesaikan & BAST diterbitkan.` })}
                className="w-full py-2 bg-slate-900 hover:bg-black text-white font-semibold text-xs rounded-xl transition cursor-pointer"
              >
                Selesaikan & Terbitkan BAST
              </button>
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Subview: Berita Acara (BAST) & Cetak BAST Preview
  if (currentRoute === 'crm-berita_acara') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Berita Acara Serah Terima (BAST) Digital</h2>
            <p className="text-xs text-slate-500">Dokumentasi tanda tangan digital serah terima modem ONT, hasil uji redaman optik, dan aktivasi layanan.</p>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          {basts.map((b) => (
            <div key={b.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
              <div className="flex justify-between items-start border-b border-slate-100 pb-3">
                <div>
                  <span className="font-mono text-xs font-bold text-red-600">{b.id}</span>
                  <h3 className="text-sm font-bold text-slate-900 mt-0.5">{b.cust}</h3>
                  <span className="text-xs text-slate-400 font-mono">{b.cid}</span>
                </div>
                <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full flex items-center gap-1">
                  <Check className="w-3 h-3" />
                  {b.status}
                </span>
              </div>

              <div className="grid grid-cols-2 gap-3 text-xs">
                <div className="bg-slate-50 p-2.5 rounded-xl">
                  <span className="text-slate-400 block text-[10px] uppercase">Redaman RX Optik:</span>
                  <strong className="font-mono font-bold text-emerald-600 text-sm">{b.rxPower}</strong>
                </div>
                <div className="bg-slate-50 p-2.5 rounded-xl">
                  <span className="text-slate-400 block text-[10px] uppercase">Serial Number ONT:</span>
                  <strong className="font-mono font-bold text-slate-800 text-sm">{b.modemSn}</strong>
                </div>
              </div>

              <div className="flex gap-2">
                <button
                  onClick={() => setBastPreviewModal(b)}
                  className="flex-1 py-2 bg-slate-900 hover:bg-black text-white font-semibold text-xs rounded-xl flex items-center justify-center gap-1.5 transition cursor-pointer"
                >
                  <Printer className="w-3.5 h-3.5" />
                  <span>Preview & Cetak BAST</span>
                </button>
                <button
                  onClick={() => showToast({ type: 'info', message: `Dokumen PDF ${b.id} berhasil diunduh.` })}
                  className="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl flex items-center justify-center gap-1.5 transition cursor-pointer"
                >
                  <Download className="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          ))}
        </div>

        {/* Modal Cetak BAST (matching crm/cetak_bast.php) */}
        {bastPreviewModal && (
          <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div className="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl space-y-6 text-xs max-h-[90vh] overflow-y-auto">
              <div className="flex justify-between items-start border-b border-slate-200 pb-4">
                <div>
                  <h3 className="font-extrabold text-base text-slate-900">BERITA ACARA SERAH TERIMA (BAST)</h3>
                  <p className="text-slate-400 text-xs font-mono">No: {bastPreviewModal.id}</p>
                </div>
                <button onClick={() => setBastPreviewModal(null)} className="text-slate-400 hover:text-slate-600 font-bold text-lg cursor-pointer">
                  ✕
                </button>
              </div>

              <div className="space-y-4 text-slate-700 leading-relaxed">
                <p>Pada hari ini telah dilakukan instalasi dan serah terima perangkat jaringan internet fiber optik:</p>
                <div className="bg-slate-50 p-4 rounded-2xl space-y-2 border border-slate-100 font-mono">
                  <div className="flex justify-between"><span>Nama Pelanggan:</span><strong className="text-slate-900">{bastPreviewModal.cust}</strong></div>
                  <div className="flex justify-between"><span>Customer ID (CID):</span><strong>{bastPreviewModal.cid}</strong></div>
                  <div className="flex justify-between"><span>Nomor Seri ONT:</span><strong>{bastPreviewModal.modemSn}</strong></div>
                  <div className="flex justify-between"><span>Hasil Redaman RX:</span><strong className="text-emerald-600">{bastPreviewModal.rxPower}</strong></div>
                  <div className="flex justify-between"><span>Teknisi Pelaksana:</span><strong>{bastPreviewModal.tech}</strong></div>
                </div>
                <p className="italic text-slate-500">Layanan telah diuji coba dan berfungsi dengan normal dengan kecepatan sesuai paket berlangganan.</p>
              </div>

              <div className="grid grid-cols-2 gap-6 pt-4 border-t border-slate-200 text-center">
                <div>
                  <span className="text-slate-400 block mb-8">Pelanggan Penerima</span>
                  <strong className="text-slate-900 block border-b border-slate-300 pb-1">{bastPreviewModal.cust}</strong>
                </div>
                <div>
                  <span className="text-slate-400 block mb-8">Teknisi Lapangan</span>
                  <strong className="text-slate-900 block border-b border-slate-300 pb-1">{bastPreviewModal.tech}</strong>
                </div>
              </div>

              <div className="flex justify-end gap-3 pt-4">
                <button onClick={() => window.print()} className="btn-primary text-xs px-5 py-2.5 flex items-center gap-1.5 cursor-pointer">
                  <Printer className="w-3.5 h-3.5" />
                  <span>Cetak Dokumen BAST</span>
                </button>
              </div>
            </div>
          </div>
        )}
      </div>
    );
  }

  // Default Subview: Daftar Pelanggan (crm-daftar / customers)
  return (
    <div className="space-y-6">
      {/* Header Banner */}
      <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div className="flex items-center space-x-3.5">
          <div className="h-11 w-11 rounded-xl bg-red-50 flex items-center justify-center text-red-600">
            <Users className="w-5 h-5" />
          </div>
          <div>
            <h1 className="text-base font-extrabold text-slate-900 tracking-tight">
              Manajemen Pelanggan FTTH & Kredensial PPPoE
            </h1>
            <p className="text-xs text-slate-500 mt-0.5">
              Master data pelanggan, kredensial dial-up PPPoE, aktivasi ONT, dan kontrol isolir jaringan.
            </p>
          </div>
        </div>

        <div className="flex items-center gap-2 shrink-0 flex-wrap sm:flex-nowrap">
          {/* Download Template Excel (.xlsx) */}
          <button
            type="button"
            onClick={handleDownloadTemplateExcel}
            title="Unduh file template Microsoft Excel (.xlsx) asli dengan contoh pengisian data pelanggan"
            className="text-xs py-2 px-3 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 hover:border-slate-300 rounded-xl font-semibold flex items-center space-x-1.5 transition cursor-pointer shadow-xs whitespace-nowrap"
          >
            <FileSpreadsheet className="w-4 h-4 text-emerald-600 shrink-0" />
            <span>Template</span>
          </button>

          {/* Export Data to Real Excel (.xlsx) */}
          <button
            type="button"
            onClick={handleExportCustomersExcel}
            title="Ekspor seluruh master data pelanggan ke format Microsoft Excel (.xlsx)"
            className="text-xs py-2 px-3 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 hover:border-slate-300 rounded-xl font-semibold flex items-center space-x-1.5 transition cursor-pointer shadow-xs whitespace-nowrap"
          >
            <FileDown className="w-4 h-4 text-blue-600 shrink-0" />
            <span>Export</span>
          </button>

          {/* Excel Studio (Import & Edit Grid) */}
          <button
            type="button"
            onClick={() => {
              setImportModalOpen(true);
              setImportRows([]);
              setImportFileName('');
              setImportResult(null);
            }}
            title="Buka Excel Spreadsheet Studio untuk unggah .xlsx, paste tabel, edit cell, dan import massal"
            className="text-xs py-2 px-3.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-xl font-bold flex items-center space-x-1.5 transition cursor-pointer shadow-xs whitespace-nowrap"
          >
            <FileUp className="w-4 h-4 text-emerald-600 shrink-0" />
            <span>Import Excel</span>
          </button>

          {/* Tambah Pelanggan Baru */}
          <button
            type="button"
            onClick={() => setIsModalOpen(true)}
            className="btn-primary text-xs py-2 px-3.5 flex items-center space-x-1.5 cursor-pointer shadow-md shadow-red-950/15 whitespace-nowrap shrink-0 font-bold"
          >
            <UserPlus className="w-4 h-4 shrink-0" />
            <span>Tambah Pelanggan</span>
          </button>
        </div>
      </div>

      {/* Filter & Search Bar */}
      <div className="bg-white p-4 rounded-xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-3">
        <form onSubmit={handleSearchSubmit} className="w-full md:w-80 relative">
          <Search className="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" />
          <input
            type="text"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Cari nama, CID, NIK, PPPoE..."
            className="input-field pl-9 text-xs py-1.5"
          />
        </form>

        <div className="flex items-center space-x-1.5 w-full md:w-auto overflow-x-auto">
          <button
            onClick={() => setStatusFilter('')}
            className={`text-xs px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer ${
              statusFilter === '' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            }`}
          >
            Semua
          </button>
          <button
            onClick={() => setStatusFilter('active')}
            className={`text-xs px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer ${
              statusFilter === 'active' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            }`}
          >
            🟢 Aktif
          </button>
          <button
            onClick={() => setStatusFilter('inactive')}
            className={`text-xs px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer ${
              statusFilter === 'inactive' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            }`}
          >
            🟡 Menunggu Aktivasi
          </button>
          <button
            onClick={() => setStatusFilter('isolated')}
            className={`text-xs px-3 py-1.5 rounded-lg font-semibold transition cursor-pointer ${
              statusFilter === 'isolated' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            }`}
          >
            🔴 Terisolir
          </button>

          <button
            onClick={fetchCustomers}
            disabled={loading}
            className="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 ml-1 cursor-pointer"
          >
            <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} />
          </button>
        </div>
      </div>

      {/* Customer Data Table */}
      <div className="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div className="table-container">
          <table className="custom-table">
            <thead>
              <tr>
                <th>CID / PELANGGAN</th>
                <th>KREDENSIAL PPPOE</th>
                <th>PAKET & HARGA</th>
                <th>TIPE BILLING</th>
                <th>STATUS</th>
                <th className="text-right">AKSI OPERASIONAL & CRUD</th>
              </tr>
            </thead>
            <tbody>
              {customers.length === 0 ? (
                <tr>
                  <td colSpan={6} className="text-center py-8 text-slate-400 text-xs">
                    {loading ? 'Memuat data pelanggan...' : 'Tidak ada data pelanggan yang sesuai filter.'}
                  </td>
                </tr>
              ) : (
                customers.map((c) => (
                  <tr key={c.id}>
                    <td>
                      <div>
                        <span className="font-mono text-xs font-bold text-red-600">{c.cid}</span>
                        <h4
                          onClick={() => {
                            setSelectedCustomer(c);
                            if (onNavigate) onNavigate('crm-detail');
                          }}
                          className="font-bold text-slate-900 text-xs hover:text-red-600 cursor-pointer transition"
                        >
                          {c.name}
                        </h4>
                        <div className="flex items-center space-x-1.5 text-[11px] text-slate-400 mt-0.5">
                          <Phone className="w-3 h-3 text-slate-400" />
                          <span>{c.phone}</span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div className="font-mono text-xs space-y-0.5">
                        <div className="text-slate-800 font-semibold">{c.pppoe_user}</div>
                        <div className="text-slate-400 text-[10px]">Pass: {c.pppoe_password || '••••••'}</div>
                      </div>
                    </td>
                    <td>
                      <div className="text-xs">
                        <p className="font-semibold text-slate-800">{c.package?.name || 'Home 20M'}</p>
                        <p className="text-slate-400 text-[11px]">
                          Rp {Number(c.package?.price || 150000).toLocaleString('id-ID')} / bln
                        </p>
                      </div>
                    </td>
                    <td>
                      <span className="text-xs font-semibold text-slate-600">
                        {c.billing_type === 'prepaid' ? 'Prabayar' : 'Pascabayar'}
                      </span>
                    </td>
                    <td>
                      <span className={`badge ${
                        c.status === 'active'
                          ? 'badge-success'
                          : c.status === 'isolated'
                          ? 'badge-danger'
                          : 'badge-warning'
                      }`}>
                        {c.status === 'active' ? 'Aktif' : c.status === 'isolated' ? 'Terisolir' : 'Menunggu'}
                      </span>
                    </td>
                    <td className="text-right">
                      <div className="flex items-center justify-end space-x-1.5">
                        {/* VIEW DETAIL */}
                        <button
                          onClick={() => {
                            setSelectedCustomer(c);
                            if (onNavigate) onNavigate('crm-detail');
                          }}
                          title="Lihat Detail Profil 360°"
                          className="p-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition cursor-pointer"
                        >
                          <Eye className="w-3.5 h-3.5" />
                        </button>

                        {/* EDIT CUSTOMER */}
                        <button
                          onClick={() => handleOpenEdit(c)}
                          title="Edit Data Pelanggan"
                          className="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition cursor-pointer"
                        >
                          <Edit className="w-3.5 h-3.5" />
                        </button>

                        {/* SET ONLINE / ISOLATE */}
                        {c.status !== 'active' && (
                          <button
                            onClick={() => handleSetOnline(c.id, c.name)}
                            disabled={actionLoading === c.id}
                            title="Aktifkan & Set Online PPPoE"
                            className="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition cursor-pointer"
                          >
                            <Zap className="w-3.5 h-3.5" />
                          </button>
                        )}
                        {c.status === 'active' && (
                          <button
                            onClick={() => handleIsolate(c.id, c.name)}
                            disabled={actionLoading === c.id}
                            title="Isolir Akun & Putus Sesi"
                            className="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition cursor-pointer"
                          >
                            <Power className="w-3.5 h-3.5" />
                          </button>
                        )}

                        {/* DELETE CUSTOMER */}
                        <button
                          onClick={() => handleDeleteCustomer(c.id, c.name)}
                          disabled={actionLoading === c.id}
                          title="Hapus Pelanggan"
                          className="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition cursor-pointer"
                        >
                          <Trash2 className="w-3.5 h-3.5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Quick Customer Registration Modal */}
      <ModalRegistrasi
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        onSuccess={(newCust) => {
          showToast({
            type: 'success',
            title: 'Registrasi Berhasil',
            message: `Pelanggan ${newCust?.name || ''} berhasil didaftarkan.`,
          });
          fetchCustomers();
        }}
      />

      {/* Modal Edit Pelanggan (UPDATE) */}
      {editModalOpen && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
          <div className="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl space-y-5 text-xs max-h-[92vh] overflow-y-auto my-auto">
            <div className="flex justify-between items-start border-b border-slate-200 pb-3">
              <div>
                <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                  <Edit className="w-4 h-4 text-blue-600" />
                  <span>Edit Data & Paket Pelanggan</span>
                </h3>
                <p className="text-slate-400 text-[11px] mt-0.5">
                  CID: <span className="font-mono font-bold text-slate-700">{editForm.cid || '-'}</span> • PPPoE User: <span className="font-mono font-bold text-blue-600">{editForm.pppoe_user || '-'}</span>
                </p>
              </div>
              <button onClick={() => setEditModalOpen(false)} className="text-slate-400 hover:text-slate-600 font-bold text-base cursor-pointer">
                ✕
              </button>
            </div>

            <form onSubmit={handleUpdateCustomer} className="space-y-4">
              <div>
                <label className="font-bold text-slate-700 block mb-1">Nama Pelanggan</label>
                <input
                  type="text"
                  required
                  value={editForm.name}
                  onChange={(e) => setEditForm({ ...editForm, name: e.target.value })}
                  className="input-field text-xs"
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="font-bold text-slate-700 block mb-1">No. WhatsApp</label>
                  <input
                    type="text"
                    required
                    value={editForm.phone}
                    onChange={(e) => setEditForm({ ...editForm, phone: e.target.value })}
                    className="input-field text-xs font-mono"
                  />
                </div>
                <div>
                  <label className="font-bold text-slate-700 block mb-1">Email</label>
                  <input
                    type="email"
                    value={editForm.email}
                    onChange={(e) => setEditForm({ ...editForm, email: e.target.value })}
                    className="input-field text-xs"
                  />
                </div>
              </div>

              <div>
                <label className="font-bold text-slate-700 block mb-1">Alamat Pemasangan</label>
                <textarea
                  rows={2}
                  required
                  value={editForm.address}
                  onChange={(e) => setEditForm({ ...editForm, address: e.target.value })}
                  className="input-field text-xs"
                />
              </div>

              {/* Titik Koordinat GPS Pemasangan & Interactive Map */}
              <div className="space-y-2 p-3 bg-slate-50 border border-slate-200 rounded-2xl">
                <div className="flex justify-between items-center">
                  <label className="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                    <i className="fa-solid fa-map-location-dot text-emerald-600"></i>
                    <span>Titik Koordinat GPS Pemasangan</span>
                  </label>
                  <span className="font-mono text-[10px] text-slate-500 font-bold">
                    {editForm.gps_lat}, {editForm.gps_lng}
                  </span>
                </div>

                <div className="grid grid-cols-2 gap-2">
                  <div>
                    <label className="text-[10px] font-semibold text-slate-500 block mb-0.5">Latitude</label>
                    <input
                      type="number"
                      step="any"
                      value={editForm.gps_lat}
                      onChange={(e) => setEditForm({ ...editForm, gps_lat: parseFloat(e.target.value) || 0 })}
                      className="input-field text-xs font-mono py-1.5"
                    />
                  </div>
                  <div>
                    <label className="text-[10px] font-semibold text-slate-500 block mb-0.5">Longitude</label>
                    <input
                      type="number"
                      step="any"
                      value={editForm.gps_lng}
                      onChange={(e) => setEditForm({ ...editForm, gps_lng: parseFloat(e.target.value) || 0 })}
                      className="input-field text-xs font-mono py-1.5"
                    />
                  </div>
                </div>

                <GpsMap
                  lat={editForm.gps_lat}
                  lng={editForm.gps_lng}
                  title={editForm.name}
                  subtitle={editForm.cid}
                  height="160px"
                  zoom={15}
                  interactive={true}
                  showSearch={true}
                  onChange={(lat, lng) => {
                    setEditForm((prev) => ({
                      ...prev,
                      gps_lat: lat,
                      gps_lng: lng,
                    }));
                  }}
                />
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="font-bold text-slate-700 block mb-1">Paket Berlangganan</label>
                  <select
                    value={editForm.package_id}
                    onChange={(e) => setEditForm({ ...editForm, package_id: e.target.value })}
                    className="input-field text-xs font-bold"
                  >
                    <option value="1">Home 20M - Rp 150.000</option>
                    <option value="2">Home 50M - Rp 250.000</option>
                    <option value="3">Biz 100M - Rp 450.000</option>
                    <option value="4">Dedicated 1:1 - Rp 1.200.000</option>
                  </select>
                </div>
                <div>
                  <label className="font-bold text-slate-700 block mb-1">Status Akun</label>
                  <select
                    value={editForm.status}
                    onChange={(e) => setEditForm({ ...editForm, status: e.target.value })}
                    className="input-field text-xs font-bold"
                  >
                    <option value="active">Aktif (Online)</option>
                    <option value="inactive">Menunggu Aktivasi</option>
                    <option value="isolated">Terisolir</option>
                  </select>
                </div>
              </div>

              <div className="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button
                  type="button"
                  onClick={() => setEditModalOpen(false)}
                  className="btn-secondary text-xs px-4 py-2 cursor-pointer"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  className="btn-primary text-xs px-5 py-2 flex items-center gap-1.5 cursor-pointer shadow-lg shadow-red-950/20"
                >
                  <Check className="w-3.5 h-3.5" />
                  <span>Simpan Perubahan</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal Reset Password RADIUS PPPoE */}
      {resetPassModalOpen && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-xs">
            <div className="flex justify-between items-center border-b border-slate-100 pb-3">
              <h3 className="font-bold text-slate-900 text-sm flex items-center gap-2">
                <Key className="w-4 h-4 text-red-600" />
                <span>Reset Password RADIUS PPPoE</span>
              </h3>
              <button onClick={() => setResetPassModalOpen(false)} className="text-slate-400 hover:text-slate-600 font-bold">
                ✕
              </button>
            </div>

            <form onSubmit={handleResetPasswordSubmit} className="space-y-4">
              <div>
                <label className="font-semibold text-slate-700 block mb-1">Username PPPoE</label>
                <input
                  type="text"
                  readOnly
                  value={selectedCustomer?.pppoe_user || 'pppoe_user'}
                  className="w-full bg-slate-100 border border-slate-200 rounded-lg p-2 font-mono font-bold text-slate-600 text-xs"
                />
              </div>

              <div>
                <label className="font-semibold text-slate-700 block mb-1">Password Baru</label>
                <input
                  type="text"
                  required
                  placeholder="Masukkan password baru..."
                  value={newPassword}
                  onChange={(e) => setNewPassword(e.target.value)}
                  className="input-field text-xs font-mono"
                />
              </div>

              <div className="flex justify-end gap-2 pt-2">
                <button
                  type="button"
                  onClick={() => setResetPassModalOpen(false)}
                  className="btn-secondary text-xs px-3 py-1.5 cursor-pointer"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  className="btn-primary text-xs px-4 py-1.5 flex items-center gap-1.5 cursor-pointer"
                >
                  <Check className="w-3.5 h-3.5" />
                  <span>Update Password</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* MODAL EXCEL SPREADSHEET STUDIO */}
      {importModalOpen && (
        <div className="fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-2 sm:p-4 overflow-y-auto">
          <div className="bg-white rounded-2xl max-w-6xl w-full shadow-2xl border border-slate-300 flex flex-col max-h-[95vh] overflow-hidden text-xs animate-in fade-in zoom-in-95 duration-200">
            
            {/* 1. EXCEL GREEN TITLEBAR */}
            <div className="bg-[#107c41] text-white px-4 py-2.5 flex items-center justify-between select-none shrink-0 shadow-md">
              <div className="flex items-center space-x-3">
                <div className="w-7 h-7 bg-white text-[#107c41] rounded-lg font-black text-sm flex items-center justify-center shadow-inner font-mono">
                  X
                </div>
                <div>
                  <div className="flex items-center gap-2">
                    <span className="font-extrabold tracking-tight text-sm">Excel Spreadsheet Studio</span>
                    <span className="bg-emerald-800/80 text-emerald-100 text-[10px] font-mono px-2 py-0.5 rounded-full border border-emerald-600/50">
                      {importFileName || 'Pelanggan_Baru_NETPRO.xlsx'}
                    </span>
                  </div>
                  <p className="text-[11px] text-emerald-100/80 leading-none mt-0.5">
                    Mode Interaktif: Buka file .xlsx, paste dari Excel, edit cell langsung, dan import ke database + FreeRADIUS
                  </p>
                </div>
              </div>
              <button
                onClick={() => { setImportModalOpen(false); setImportRows([]); setImportFileName(''); }}
                className="w-7 h-7 rounded-lg hover:bg-emerald-800 text-emerald-100 hover:text-white flex items-center justify-center transition cursor-pointer"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            {/* 2. EXCEL RIBBON & TOOLBAR */}
            <div className="bg-slate-100 border-b border-slate-200 p-2 sm:px-4 flex flex-wrap items-center justify-between gap-2 shrink-0">
              <div className="flex flex-wrap items-center gap-1.5">
                {/* File Upload Button */}
                <input
                  id="excelFileInput"
                  type="file"
                  accept=".xlsx,.xls,.csv,.txt"
                  className="hidden"
                  onChange={(e) => {
                    if (e.target.files && e.target.files[0]) {
                      handleFileUpload(e.target.files[0]);
                    }
                  }}
                />
                <button
                  type="button"
                  onClick={() => document.getElementById('excelFileInput')?.click()}
                  className="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 rounded-lg font-bold flex items-center gap-1.5 shadow-sm transition cursor-pointer text-xs"
                >
                  <Upload className="w-3.5 h-3.5 text-[#107c41]" />
                  <span>Buka File (.xlsx / .csv)</span>
                </button>

                {/* Paste from Excel Clipboard */}
                <button
                  type="button"
                  onClick={() => setPasteModalOpen(true)}
                  className="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 rounded-lg font-bold flex items-center gap-1.5 shadow-sm transition cursor-pointer text-xs"
                >
                  <ClipboardPaste className="w-3.5 h-3.5 text-blue-600" />
                  <span>Paste dari Excel</span>
                </button>

                {/* Add Row Button */}
                <button
                  type="button"
                  onClick={handleAddNewGridRow}
                  className="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 rounded-lg font-bold flex items-center gap-1.5 shadow-sm transition cursor-pointer text-xs"
                >
                  <Plus className="w-3.5 h-3.5 text-indigo-600" />
                  <span>Tambah Baris</span>
                </button>

                {/* Clear Grid */}
                {importRows.length > 0 && (
                  <button
                    type="button"
                    onClick={handleClearGrid}
                    className="px-2.5 py-1.5 bg-white hover:bg-red-50 text-red-600 border border-slate-300 rounded-lg font-bold flex items-center gap-1 shadow-sm transition cursor-pointer text-xs"
                  >
                    <Trash2 className="w-3.5 h-3.5" />
                    <span>Hapus Semua</span>
                  </button>
                )}
              </div>

              {/* Template Download Shortcuts */}
              <div className="flex items-center gap-1.5">
                <button
                  type="button"
                  onClick={handleDownloadTemplateExcel}
                  className="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-[#107c41] border border-emerald-200 rounded-lg font-bold flex items-center gap-1.5 shadow-sm transition cursor-pointer text-xs"
                >
                  <FileSpreadsheet className="w-3.5 h-3.5" />
                  <span>Unduh Template .xlsx</span>
                </button>
                <button
                  type="button"
                  onClick={handleDownloadTemplateCSV}
                  className="px-2.5 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-lg font-semibold flex items-center gap-1 shadow-sm transition cursor-pointer text-xs"
                >
                  <Download className="w-3.5 h-3.5 text-slate-500" />
                  <span>.csv</span>
                </button>
              </div>
            </div>

            {/* 3. EXCEL FORMULA BAR */}
            <div className="bg-slate-50 border-b border-slate-200 px-4 py-1.5 flex items-center space-x-2 shrink-0 text-xs">
              <span className="font-mono font-bold bg-white border border-slate-300 px-2 py-0.5 rounded text-slate-700 w-12 text-center shadow-inner">
                {activeCellCoord}
              </span>
              <span className="font-serif italic font-bold text-slate-400 select-none">fx</span>
              <input
                type="text"
                value={activeCellValue}
                onChange={(e) => setActiveCellValue(e.target.value)}
                placeholder="Pilih cell di spreadsheet untuk melihat atau mengedit isinya..."
                className="flex-1 bg-white border border-slate-300 rounded px-2 py-0.5 text-xs text-slate-800 font-mono focus:outline-none focus:border-[#107c41]"
              />
            </div>

            {/* 4. SPREADSHEET GRID AREA */}
            <div
              onDragOver={(e) => { e.preventDefault(); setDragActive(true); }}
              onDragLeave={() => setDragActive(false)}
              onDrop={(e) => {
                e.preventDefault();
                setDragActive(false);
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                  handleFileUpload(e.dataTransfer.files[0]);
                }
              }}
              className="flex-1 overflow-auto bg-slate-100 relative p-0 max-h-[55vh]"
            >
              {importRows.length === 0 ? (
                /* Empty Spreadsheet Placeholder with Drag & Drop */
                <div className="h-64 flex flex-col items-center justify-center p-6 text-center">
                  <div className="w-16 h-16 bg-emerald-50 rounded-2xl border-2 border-dashed border-emerald-300 text-[#107c41] flex items-center justify-center mb-3">
                    <FileSpreadsheet className="w-8 h-8" />
                  </div>
                  <h4 className="font-bold text-slate-900 text-sm">
                    Grid Spreadsheet Masih Kosong
                  </h4>
                  <p className="text-slate-500 text-xs max-w-md mt-1 mb-4">
                    Tarik file <strong>.xlsx / .csv</strong> ke sini, klik <strong>Buka File</strong>, atau klik <strong>Paste dari Excel</strong> untuk mengisi baris pelanggan.
                  </p>
                  <div className="flex flex-wrap items-center justify-center gap-2">
                    <button
                      type="button"
                      onClick={() => document.getElementById('excelFileInput')?.click()}
                      className="px-4 py-2 bg-[#107c41] hover:bg-[#0c6233] text-white rounded-xl font-bold flex items-center gap-1.5 shadow transition cursor-pointer text-xs"
                    >
                      <Upload className="w-4 h-4" />
                      <span>Pilih File Excel (.xlsx)</span>
                    </button>
                    <button
                      type="button"
                      onClick={() => setPasteModalOpen(true)}
                      className="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl font-bold flex items-center gap-1.5 transition cursor-pointer text-xs"
                    >
                      <ClipboardPaste className="w-4 h-4" />
                      <span>Paste dari Clipboard</span>
                    </button>
                  </div>
                </div>
              ) : (
                /* Interactive Excel Grid Table */
                <table className="w-full text-left border-collapse bg-white font-sans text-xs select-none">
                  <thead className="sticky top-0 z-10 bg-slate-200 shadow-sm border-b border-slate-300">
                    <tr className="text-slate-700 font-bold text-[11px]">
                      <th className="w-10 bg-slate-300 border-r border-b border-slate-300 p-1 text-center select-none font-mono text-[10px]">
                        ◢
                      </th>
                      <th className="p-2 border-r border-slate-300 min-w-[100px]"><span className="text-slate-400 font-mono text-[9px] block">A</span>CID</th>
                      <th className="p-2 border-r border-slate-300 min-w-[160px]"><span className="text-slate-400 font-mono text-[9px] block">B</span>Nama Pelanggan *</th>
                      <th className="p-2 border-r border-slate-300 min-w-[130px]"><span className="text-slate-400 font-mono text-[9px] block">C</span>NIK</th>
                      <th className="p-2 border-r border-slate-300 min-w-[120px]"><span className="text-slate-400 font-mono text-[9px] block">D</span>No WA *</th>
                      <th className="p-2 border-r border-slate-300 min-w-[150px]"><span className="text-slate-400 font-mono text-[9px] block">E</span>Email</th>
                      <th className="p-2 border-r border-slate-300 min-w-[200px]"><span className="text-slate-400 font-mono text-[9px] block">F</span>Alamat Lengkap</th>
                      <th className="p-2 border-r border-slate-300 min-w-[140px]"><span className="text-slate-400 font-mono text-[9px] block">G</span>Paket Internet</th>
                      <th className="p-2 border-r border-slate-300 min-w-[130px]"><span className="text-slate-400 font-mono text-[9px] block">H</span>PPPoE User</th>
                      <th className="p-2 border-r border-slate-300 min-w-[110px]"><span className="text-slate-400 font-mono text-[9px] block">I</span>Password</th>
                      <th className="p-2 border-r border-slate-300 min-w-[100px]"><span className="text-slate-400 font-mono text-[9px] block">J</span>Billing</th>
                      <th className="p-2 border-r border-slate-300 min-w-[90px]"><span className="text-slate-400 font-mono text-[9px] block">K</span>PPN</th>
                      <th className="p-2 border-r border-slate-300 min-w-[90px]"><span className="text-slate-400 font-mono text-[9px] block">L</span>Status</th>
                      <th className="p-2 text-center w-12"><span className="text-slate-400 font-mono text-[9px] block">M</span>Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-slate-200">
                    {importRows.map((row, idx) => (
                      <tr key={idx} className="hover:bg-emerald-50/40 group transition">
                        {/* Row Index */}
                        <td className="bg-slate-100 border-r border-slate-300 p-1 text-center font-mono font-bold text-slate-500 text-[11px]">
                          {idx + 1}
                        </td>

                        {/* CID */}
                        <td className="p-1 border-r border-slate-200">
                          <input
                            type="text"
                            value={row.cid}
                            onFocus={() => { setActiveCellCoord(`A${idx + 1}`); setActiveCellValue(row.cid); }}
                            onChange={(e) => handleUpdateGridCell(idx, 'cid', e.target.value)}
                            placeholder="(Auto)"
                            className="w-full bg-transparent px-1.5 py-0.5 rounded font-mono text-slate-600 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#107c41]"
                          />
                        </td>

                        {/* Nama Pelanggan */}
                        <td className="p-1 border-r border-slate-200">
                          <input
                            type="text"
                            value={row.name}
                            onFocus={() => { setActiveCellCoord(`B${idx + 1}`); setActiveCellValue(row.name); }}
                            onChange={(e) => handleUpdateGridCell(idx, 'name', e.target.value)}
                            className="w-full bg-transparent px-1.5 py-0.5 rounded font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#107c41]"
                          />
                        </td>

                        {/* NIK */}
                        <td className="p-1 border-r border-slate-200">
                          <input
                            type="text"
                            value={row.nik}
                            onFocus={() => { setActiveCellCoord(`C${idx + 1}`); setActiveCellValue(row.nik); }}
                            onChange={(e) => handleUpdateGridCell(idx, 'nik', e.target.value)}
                            className="w-full bg-transparent px-1.5 py-0.5 rounded font-mono text-slate-600 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#107c41]"
                          />
                        </td>

                        {/* No WA */}
                        <td className="p-1 border-r border-slate-200">
                          <input
                            type="text"
                            value={row.phone}
                            onFocus={() => { setActiveCellCoord(`D${idx + 1}`); setActiveCellValue(row.phone); }}
                            onChange={(e) => handleUpdateGridCell(idx, 'phone', e.target.value)}
                            className="w-full bg-transparent px-1.5 py-0.5 rounded font-mono text-slate-800 font-semibold focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#107c41]"
                          />
                        </td>

                        {/* Email */}
                        <td className="p-1 border-r border-slate-200">
                          <input
                            type="text"
                            value={row.email}
                            onFocus={() => { setActiveCellCoord(`E${idx + 1}`); setActiveCellValue(row.email); }}
                            onChange={(e) => handleUpdateGridCell(idx, 'email', e.target.value)}
                            className="w-full bg-transparent px-1.5 py-0.5 rounded text-slate-600 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#107c41]"
                          />
                        </td>

                        {/* Alamat */}
                        <td className="p-1 border-r border-slate-200">
                          <input
                            type="text"
                            value={row.address}
                            onFocus={() => { setActiveCellCoord(`F${idx + 1}`); setActiveCellValue(row.address); }}
                            onChange={(e) => handleUpdateGridCell(idx, 'address', e.target.value)}
                            className="w-full bg-transparent px-1.5 py-0.5 rounded text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#107c41]"
                          />
                        </td>

                        {/* Paket Internet */}
                        <td className="p-1 border-r border-slate-200">
                          <input
                            type="text"
                            value={row.package_name}
                            onFocus={() => { setActiveCellCoord(`G${idx + 1}`); setActiveCellValue(row.package_name); }}
                            onChange={(e) => handleUpdateGridCell(idx, 'package_name', e.target.value)}
                            className="w-full bg-transparent px-1.5 py-0.5 rounded font-bold text-blue-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#107c41]"
                          />
                        </td>

                        {/* PPPoE User */}
                        <td className="p-1 border-r border-slate-200">
                          <input
                            type="text"
                            value={row.pppoe_user}
                            onFocus={() => { setActiveCellCoord(`H${idx + 1}`); setActiveCellValue(row.pppoe_user); }}
                            onChange={(e) => handleUpdateGridCell(idx, 'pppoe_user', e.target.value)}
                            placeholder="(Auto Generated)"
                            className="w-full bg-transparent px-1.5 py-0.5 rounded font-mono text-indigo-600 font-bold focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#107c41]"
                          />
                        </td>

                        {/* Password */}
                        <td className="p-1 border-r border-slate-200">
                          <input
                            type="text"
                            value={row.pppoe_password}
                            onFocus={() => { setActiveCellCoord(`I${idx + 1}`); setActiveCellValue(row.pppoe_password); }}
                            onChange={(e) => handleUpdateGridCell(idx, 'pppoe_password', e.target.value)}
                            className="w-full bg-transparent px-1.5 py-0.5 rounded font-mono text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#107c41]"
                          />
                        </td>

                        {/* Billing Type */}
                        <td className="p-1 border-r border-slate-200">
                          <select
                            value={row.billing_type}
                            onChange={(e) => handleUpdateGridCell(idx, 'billing_type', e.target.value)}
                            className="w-full bg-transparent px-1 py-0.5 text-[11px] font-bold uppercase rounded text-slate-700 focus:bg-white focus:outline-none"
                          >
                            <option value="postpaid">Postpaid</option>
                            <option value="prepaid">Prepaid</option>
                          </select>
                        </td>

                        {/* PPN */}
                        <td className="p-1 border-r border-slate-200">
                          <select
                            value={row.ppn_scheme}
                            onChange={(e) => handleUpdateGridCell(idx, 'ppn_scheme', e.target.value)}
                            className="w-full bg-transparent px-1 py-0.5 text-[11px] font-bold uppercase rounded text-slate-700 focus:bg-white focus:outline-none"
                          >
                            <option value="include">Include</option>
                            <option value="exclude">Exclude</option>
                          </select>
                        </td>

                        {/* Status */}
                        <td className="p-1 border-r border-slate-200">
                          <select
                            value={row.status}
                            onChange={(e) => handleUpdateGridCell(idx, 'status', e.target.value)}
                            className="w-full bg-transparent px-1 py-0.5 text-[11px] font-bold rounded text-emerald-700 focus:bg-white focus:outline-none"
                          >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="isolated">Isolated</option>
                          </select>
                        </td>

                        {/* Delete Row */}
                        <td className="p-1 text-center">
                          <button
                            type="button"
                            onClick={() => handleDeleteGridRow(idx)}
                            className="text-slate-300 hover:text-red-600 p-1 transition cursor-pointer"
                            title="Hapus baris ini"
                          >
                            <Trash2 className="w-3.5 h-3.5" />
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}
            </div>

            {/* 5. EXCEL SHEET TABS & FOOTER STATUSBAR */}
            <div className="bg-slate-200 border-t border-slate-300 px-4 py-2 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0 select-none">
              <div className="flex items-center space-x-2">
                {/* Active Sheet Tab */}
                <div className="bg-white px-3 py-1 rounded-t-lg border-t-2 border-[#107c41] font-bold text-slate-800 text-xs shadow-sm flex items-center gap-1.5">
                  <span className="w-2 h-2 rounded-full bg-emerald-500"></span>
                  <span>Sheet1 - Calon Pelanggan</span>
                </div>
                <button
                  type="button"
                  onClick={handleAddNewGridRow}
                  className="px-2 py-0.5 hover:bg-slate-300 rounded font-bold text-slate-600 text-sm transition cursor-pointer"
                  title="Tambah baris baru"
                >
                  +
                </button>
                <span className="text-[11px] text-slate-500 font-mono ml-2">
                  Total: <strong>{importRows.length}</strong> baris terdeteksi
                </span>
              </div>

              {/* Execution Actions */}
              <div className="flex items-center space-x-2">
                <button
                  type="button"
                  onClick={() => { setImportModalOpen(false); setImportRows([]); setImportFileName(''); }}
                  className="px-4 py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded-xl font-bold transition cursor-pointer text-xs"
                  disabled={importing}
                >
                  Tutup
                </button>
                <button
                  type="button"
                  onClick={handleImportSubmit}
                  disabled={importing || importRows.length === 0}
                  className="px-5 py-2 bg-[#107c41] hover:bg-[#0c6233] text-white rounded-xl font-bold flex items-center space-x-2 shadow-lg shadow-emerald-900/20 transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed text-xs"
                >
                  {importing ? (
                    <>
                      <RefreshCw className="w-3.5 h-3.5 animate-spin" />
                      <span>Mengimpor ke Database & FreeRADIUS...</span>
                    </>
                  ) : (
                    <>
                      <Check className="w-3.5 h-3.5" />
                      <span>Simpan & Import ({importRows.length} Pelanggan)</span>
                    </>
                  )}
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* MODAL PASTE FROM EXCEL CLIPBOARD */}
      {pasteModalOpen && (
        <div className="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 text-xs">
            <div className="flex justify-between items-start border-b border-slate-100 pb-3">
              <div className="flex items-center space-x-2.5">
                <div className="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                  <ClipboardPaste className="w-4.5 h-4.5" />
                </div>
                <div>
                  <h3 className="font-bold text-slate-900 text-sm">Paste Data Langsung dari Excel</h3>
                  <p className="text-slate-400 text-xs">Salin (Ctrl+C) tabel dari Excel atau Google Sheets lalu paste (Ctrl+V) di bawah.</p>
                </div>
              </div>
              <button onClick={() => { setPasteModalOpen(false); setPasteText(''); }} className="text-slate-400 hover:text-slate-600 font-bold">
                ✕
              </button>
            </div>

            <textarea
              rows={8}
              value={pasteText}
              onChange={(e) => setPasteText(e.target.value)}
              placeholder="Paste data tabel di sini (Kolom: CID, Nama, NIK, No WA, Email, Alamat, Lat, Lng, Paket, PPPoE User, Password, Billing, PPN, Status)..."
              className="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>

            <div className="flex justify-end space-x-2 pt-2">
              <button
                type="button"
                onClick={() => { setPasteModalOpen(false); setPasteText(''); }}
                className="btn-secondary text-xs px-4 py-2 cursor-pointer"
              >
                Batal
              </button>
              <button
                type="button"
                onClick={() => handlePasteProcess(pasteText)}
                disabled={!pasteText.trim()}
                className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold flex items-center gap-1.5 transition cursor-pointer disabled:opacity-50 text-xs"
              >
                <Check className="w-3.5 h-3.5" />
                <span>Masukkan ke Grid Spreadsheet</span>
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
