import React, { useState, useEffect } from 'react';
import {
  X,
  UserPlus,
  ArrowRight,
  ArrowLeft,
  CheckCircle2,
  FileText,
  Radio,
  Clock,
  Calculator,
  Key,
  RotateCw,
  CreditCard,
  Phone,
  Mail,
  MapPin,
} from 'lucide-react';
import { api } from '../api/client';
import GpsMap from './GpsMap';

export default function ModalRegistrasi({ isOpen, onClose, onSuccess }) {
  const [currentStep, setCurrentStep] = useState(1);
  const [packages, setPackages] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const [form, setForm] = useState({
    name: '',
    nik: '',
    phone: '',
    email: '',
    address: '',
    gps_lat: -6.289123,
    gps_lng: 106.918456,
    package_id: '',
    ppn_scheme: 'include',
    billing_type: 'postpaid',
    billing_cycle_type: 'anniversary',
    is_prorata: '0',
    auth_method: 'pppoe',
    pppoe_user: '',
    pppoe_password: '',
  });

  const [showPppoePass, setShowPppoePass] = useState(false);

  useEffect(() => {
    if (isOpen) {
      setCurrentStep(1);
      setError('');
      api.get('/packages').then((res) => {
        if (res.data?.length > 0) {
          setPackages(res.data);
          if (!form.package_id) {
            setForm((f) => ({ ...f, package_id: res.data[0].id }));
          }
        }
      });
    }
  }, [isOpen]);

  if (!isOpen) return null;

  // Auto generate PPPoE username based on NIK and Name
  const generatePppoe = () => {
    const nikPrefix = form.nik ? form.nik.replace(/\D/g, '').substring(0, 8) : '32750101';
    const cleanName = form.name
      ? form.name.trim().split(' ')[0].toUpperCase().replace(/[^A-Z]/g, '')
      : 'USER';
    const generatedUser = `${nikPrefix}-${cleanName}`;
    const generatedPass = Math.random().toString(36).slice(-8);

    setForm((f) => ({
      ...f,
      pppoe_user: generatedUser,
      pppoe_password: generatedPass,
    }));
  };

  // Calculate pricing for Step 2 preview
  const selectedPackage = packages.find((p) => p.id == form.package_id) || packages[0] || { price: 250000, name: 'Paket Internet 50 Mbps' };
  const basePrice = Number(selectedPackage.price) || 250000;
  let dpp = basePrice;
  let ppn = 0;
  let total = basePrice;

  if (form.ppn_scheme === 'include') {
    dpp = Math.round(basePrice / 1.11);
    ppn = basePrice - dpp;
    total = basePrice;
  } else {
    dpp = basePrice;
    ppn = Math.round(basePrice * 0.11);
    total = basePrice + ppn;
  }

  const formatRupiah = (val) => 'Rp ' + Number(val).toLocaleString('id-ID');

  const handleNextStep1 = (e) => {
    e.preventDefault();
    if (!form.name || !form.nik || !form.phone || !form.address) {
      setError('Harap lengkapi semua data identitas bertanda bintang (*).');
      return;
    }
    setError('');
    setCurrentStep(2);
  };

  const handleNextStep2 = (e) => {
    e.preventDefault();
    setError('');
    if (!form.pppoe_user) {
      generatePppoe();
    }
    setCurrentStep(3);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const res = await api.post('/customers', form);
      if (onSuccess) onSuccess(res.data);
      onClose();
    } catch (err) {
      setError(err.message || 'Gagal mendaftarkan pelanggan.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md overflow-y-auto">
      <div className="bg-white rounded-3xl w-full max-w-3xl border border-slate-100 shadow-2xl p-6 sm:p-8 relative my-8 space-y-6 text-xs text-slate-800">
        <button
          onClick={onClose}
          className="absolute top-5 right-5 text-slate-400 hover:text-slate-700 p-1.5 rounded-full hover:bg-slate-100 transition cursor-pointer"
        >
          <X className="w-5 h-5" />
        </button>

        {/* Wizard Header & Progress */}
        <div className="space-y-4 border-b border-slate-100 pb-5">
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <div>
              <h3 className="font-bold text-slate-900 text-base flex items-center gap-2">
                <UserPlus className="w-5 h-5 text-blue-600" />
                <span>Registrasi Pelanggan Baru & Onboarding ISP</span>
              </h3>
              <p className="text-slate-400 text-xs mt-0.5">
                Ikuti 3 tahapan registrasi untuk aktivasi akun dan penerbitan faktur tagihan.
              </p>
            </div>
            <span className="px-3 py-1 bg-blue-50 text-blue-700 font-bold rounded-full border border-blue-200 text-[11px] shrink-0">
              Langkah {currentStep} dari 3
            </span>
          </div>

          {/* Arrow Stepper */}
          <div className="arrow-breadcrumb-wrapper pt-1">
            <div className="arrow-breadcrumb w-full flex">
              <div
                onClick={() => setCurrentStep(1)}
                className={`arrow-breadcrumb-item flex-1 cursor-pointer justify-center ${
                  currentStep === 1 ? 'is-active' : 'is-completed'
                }`}
              >
                <span className="arrow-breadcrumb-badge">1</span>
                <span className="truncate">1. Data Identitas KTP</span>
              </div>

              <div
                onClick={() => form.name && form.nik && setCurrentStep(2)}
                className={`arrow-breadcrumb-item flex-1 cursor-pointer justify-center ${
                  currentStep === 2 ? 'is-active' : currentStep > 2 ? 'is-completed' : 'is-inactive'
                }`}
              >
                <span className="arrow-breadcrumb-badge">2</span>
                <span className="truncate">2. Paket & Pajak PPN</span>
              </div>

              <div
                onClick={() => form.name && form.nik && setCurrentStep(3)}
                className={`arrow-breadcrumb-item flex-1 cursor-pointer justify-center ${
                  currentStep === 3 ? 'is-active' : 'is-inactive'
                }`}
              >
                <span className="arrow-breadcrumb-badge">3</span>
                <span className="truncate">3. Lokasi & Aktivasi</span>
              </div>
            </div>
          </div>
        </div>

        {error && (
          <div className="p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-semibold">
            {error}
          </div>
        )}

        {/* ================= STEP 1: DATA IDENTITAS ================= */}
        {currentStep === 1 && (
          <form onSubmit={handleNextStep1} className="space-y-4">
            <div className="p-4 bg-red-50/70 border border-red-100 rounded-2xl text-red-950 flex items-center gap-3">
              <CreditCard className="w-5 h-5 text-red-600 shrink-0" />
              <div>
                <strong className="font-bold block text-xs">Tahap 1: Identitas Legal Calon Pelanggan</strong>
                <span className="text-[11px] text-red-800/90">
                  Pastikan nomor KTP dan WhatsApp sesuai untuk keperluan kontrak & invoice digital.
                </span>
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="font-semibold text-slate-700 block mb-1">
                  Nama Lengkap (Sesuai KTP) <span className="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  required
                  value={form.name}
                  onChange={(e) => setForm({ ...form, name: e.target.value })}
                  placeholder="Contoh: Budi Santoso"
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-medium focus:bg-white focus:border-red-500 outline-none transition text-xs text-slate-800"
                />
              </div>

              <div>
                <label className="font-semibold text-slate-700 block mb-1">
                  Nomor Induk Kependudukan (NIK) <span className="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  required
                  maxLength={16}
                  value={form.nik}
                  onChange={(e) => setForm({ ...form, nik: e.target.value })}
                  placeholder="3275xxxxxxxxxxxx"
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-mono focus:bg-white focus:border-red-500 outline-none transition text-xs text-slate-800"
                />
              </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label className="font-semibold text-slate-700 block mb-1">
                  No. WhatsApp / Telepon <span className="text-red-500">*</span>
                </label>
                <input
                  type="tel"
                  required
                  value={form.phone}
                  onChange={(e) => setForm({ ...form, phone: e.target.value })}
                  placeholder="081234567890"
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-medium focus:bg-white focus:border-red-500 outline-none transition text-xs text-slate-800"
                />
              </div>

              <div>
                <label className="font-semibold text-slate-700 block mb-1">Alamat Email Aktif</label>
                <input
                  type="email"
                  value={form.email}
                  onChange={(e) => setForm({ ...form, email: e.target.value })}
                  placeholder="budi.santoso@gmail.com"
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-medium focus:bg-white focus:border-red-500 outline-none transition text-xs text-slate-800"
                />
              </div>
            </div>

            <div>
              <label className="font-semibold text-slate-700 block mb-1">
                Alamat Lengkap Domisili / Pemasangan <span className="text-red-500">*</span>
              </label>
              <textarea
                rows={3}
                required
                value={form.address}
                onChange={(e) => setForm({ ...form, address: e.target.value })}
                placeholder="Jl. Jatiwaringin Raya No. 45, RT 02/RW 05, Kel. Jaticempaka, Kec. Pondok Gede, Kota Bekasi..."
                className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:bg-white focus:border-red-500 outline-none transition text-xs text-slate-800"
              />
            </div>

            <div className="flex justify-end pt-2">
              <button
                type="submit"
                className="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-lg shadow-red-600/30 transition flex items-center gap-2 cursor-pointer"
              >
                <span>Lanjut ke Tahap 2 (Paket & PPN)</span>
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          </form>
        )}

        {/* ================= STEP 2: PAKET & PPN ================= */}
        {currentStep === 2 && (
          <form onSubmit={handleNextStep2} className="space-y-4">
            <div className="p-3.5 bg-indigo-50/70 border border-indigo-100 rounded-xl text-indigo-900 flex items-center gap-2.5">
              <FileText className="w-5 h-5 text-indigo-600 shrink-0" />
              <div>
                <strong className="font-bold block text-xs">
                  Tahap 2: Pemilihan Paket Bandwidth & Skema Perpajakan PPN
                </strong>
                <span className="text-[11px] text-indigo-700">
                  Sistem otomatis menghitung simulasi DPP & PPN 11% sesuai regulasi Dirjen Pajak.
                </span>
              </div>
            </div>

            {/* Model Penagihan */}
            <div className="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
              <label className="font-bold text-slate-800 block text-xs flex items-center gap-2">
                <Clock className="w-4 h-4 text-blue-600" />
                <span>Tipe Model Penagihan (Billing Mode) *</span>
              </label>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label
                  className={`p-3 bg-white rounded-xl border-2 cursor-pointer flex items-start gap-3 transition ${
                    form.billing_type === 'postpaid' ? 'border-blue-500 shadow-sm' : 'border-slate-200'
                  }`}
                >
                  <input
                    type="radio"
                    name="billing_type"
                    value="postpaid"
                    checked={form.billing_type === 'postpaid'}
                    onChange={(e) => setForm({ ...form, billing_type: e.target.value })}
                    className="accent-blue-600 mt-0.5"
                  />
                  <div>
                    <strong className="block text-slate-900 font-bold text-xs">Pascabayar (Postpaid Fixed Date)</strong>
                    <span className="text-[10px] text-slate-500 leading-tight block mt-0.5">
                      Tagihan rutin terbit tgl 1, jatuh tempo serentak tanggal 20.
                    </span>
                  </div>
                </label>

                <label
                  className={`p-3 bg-white rounded-xl border cursor-pointer flex items-start gap-3 transition ${
                    form.billing_type === 'prepaid' ? 'border-purple-500 shadow-sm' : 'border-slate-200'
                  }`}
                >
                  <input
                    type="radio"
                    name="billing_type"
                    value="prepaid"
                    checked={form.billing_type === 'prepaid'}
                    onChange={(e) => setForm({ ...form, billing_type: e.target.value })}
                    className="accent-purple-600 mt-0.5"
                  />
                  <div>
                    <div className="flex items-center gap-1.5">
                      <strong className="block text-slate-900 font-bold text-xs">Prabayar (Prepaid FTTH)</strong>
                      <span className="px-1.5 py-0.2 bg-purple-100 text-purple-700 font-bold text-[9px] rounded">
                        Grace 30 Mnt
                      </span>
                    </div>
                    <span className="text-[10px] text-slate-500 leading-tight block mt-0.5">
                      Bayar di awal. Mendukung Rolling 30 Hari & Fixed Date.
                    </span>
                  </div>
                </label>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="font-semibold text-slate-700 block mb-1">Pilihan Paket Layanan Internet</label>
                <select
                  value={form.package_id}
                  onChange={(e) => setForm({ ...form, package_id: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:bg-white focus:border-blue-500 outline-none transition text-xs"
                >
                  {packages.map((pkg) => (
                    <option key={pkg.id} value={pkg.id}>
                      {pkg.name} ({pkg.speed_mbps || 50} Mbps) - {formatRupiah(pkg.price)}/bln
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="font-semibold text-slate-700 block mb-1">Pilih Skema PPN Tagihan Invoice</label>
                <div className="grid grid-cols-2 gap-2 pt-0.5">
                  <label
                    className={`p-2.5 bg-white rounded-xl border cursor-pointer flex items-center gap-2 font-bold text-xs transition ${
                      form.ppn_scheme === 'include' ? 'border-blue-500 text-blue-900 border-2' : 'border-slate-200 text-slate-700'
                    }`}
                  >
                    <input
                      type="radio"
                      name="ppn_scheme"
                      value="include"
                      checked={form.ppn_scheme === 'include'}
                      onChange={(e) => setForm({ ...form, ppn_scheme: e.target.value })}
                      className="accent-blue-600"
                    />
                    <span>Include PPN</span>
                  </label>
                  <label
                    className={`p-2.5 bg-white rounded-xl border cursor-pointer flex items-center gap-2 font-bold text-xs transition ${
                      form.ppn_scheme === 'exclude' ? 'border-blue-500 text-blue-900 border-2' : 'border-slate-200 text-slate-700'
                    }`}
                  >
                    <input
                      type="radio"
                      name="ppn_scheme"
                      value="exclude"
                      checked={form.ppn_scheme === 'exclude'}
                      onChange={(e) => setForm({ ...form, ppn_scheme: e.target.value })}
                      className="accent-blue-600"
                    />
                    <span>Exclude PPN (+11%)</span>
                  </label>
                </div>
              </div>
            </div>

            {/* Real-time Invoice Simulation Card */}
            <div className="p-4 bg-slate-900 text-white rounded-2xl border border-slate-800 space-y-3">
              <div className="flex justify-between items-center border-b border-slate-800 pb-2">
                <span className="text-[11px] font-bold text-blue-400 flex items-center gap-1.5">
                  <Calculator className="w-3.5 h-3.5" />
                  <span>Pratinjau Tagihan Awal (Invoice Preview)</span>
                </span>
                <span className="text-[10px] text-slate-400 font-mono">Jatuh Tempo: Tgl 20</span>
              </div>
              <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                  <p className="font-extrabold text-white text-sm">
                    {selectedPackage.name} - {formatRupiah(basePrice)} ({form.ppn_scheme.toUpperCase()} PPN 11%)
                  </p>
                  <span className="text-[10px] text-slate-400 block mt-0.5">
                    Siklus otomatis dibuat oleh NETPRO Billing Engine
                  </span>
                </div>
                <div className="flex gap-4 text-right">
                  <div>
                    <span className="text-slate-400 block text-[10px]">DPP</span>
                    <strong className="font-mono text-slate-200 text-xs">{formatRupiah(dpp)}</strong>
                  </div>
                  <div>
                    <span className="text-slate-400 block text-[10px]">PPN 11%</span>
                    <strong className="font-mono text-blue-400 text-xs">{formatRupiah(ppn)}</strong>
                  </div>
                  <div>
                    <span className="text-slate-400 block text-[10px]">Total Bayar</span>
                    <strong className="font-mono text-emerald-400 text-sm">{formatRupiah(total)}</strong>
                  </div>
                </div>
              </div>
            </div>

            <div className="flex justify-between pt-2">
              <button
                type="button"
                onClick={() => setCurrentStep(1)}
                className="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer"
              >
                <ArrowLeft className="w-4 h-4" />
                <span>Kembali ke Tahap 1</span>
              </button>
              <button
                type="submit"
                className="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-lg shadow-red-600/30 transition flex items-center gap-2 cursor-pointer"
              >
                <span>Lanjut ke Tahap 3 (Lokasi GPS & Aktivasi)</span>
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          </form>
        )}

        {/* ================= STEP 3: LOKASI & AKTIVASI ================= */}
        {currentStep === 3 && (
          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="p-3.5 bg-emerald-50/70 border border-emerald-100 rounded-xl text-emerald-900 flex items-center gap-2.5">
              <MapPin className="w-5 h-5 text-emerald-600 shrink-0" />
              <div>
                <strong className="font-bold block text-xs">
                  Tahap 3: Pemetaan Koordinat GPS & Penugasan ODP Port
                </strong>
                <span className="text-[11px] text-emerald-700">
                  Tentukan koordinat presisi dan buat akun otentikasi PPPoE / RADIUS otomatis.
                </span>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="font-semibold text-slate-700 block mb-1">
                  <i className="fa-solid fa-map-pin text-emerald-600 mr-1"></i>
                  Koordinat GPS Pemasangan (Klik/Geser Pin di Peta)
                </label>
                <input
                  type="text"
                  required
                  value={`${form.gps_lat}, ${form.gps_lng}`}
                  onChange={(e) => {
                    const parts = e.target.value.split(',');
                    if (parts.length === 2) {
                      setForm({
                        ...form,
                        gps_lat: parseFloat(parts[0]) || -6.2891,
                        gps_lng: parseFloat(parts[1]) || 106.9184,
                      });
                    }
                  }}
                  className="w-full bg-slate-50 border border-blue-500 rounded-xl p-2.5 font-mono font-bold text-blue-700 focus:bg-white outline-none transition text-xs"
                />
                <GpsMap
                  lat={form.gps_lat}
                  lng={form.gps_lng}
                  title={form.name || 'Lokasi Pelanggan'}
                  subtitle="Titik Pemasangan Drop Cable"
                  height="140px"
                  zoom={15}
                  interactive={true}
                  onChange={(lat, lng) => {
                    setForm((prev) => ({
                      ...prev,
                      gps_lat: Number(lat.toFixed(6)),
                      gps_lng: Number(lng.toFixed(6)),
                    }));
                  }}
                />
              </div>

              <div>
                <label className="font-semibold text-slate-700 block mb-1">
                  Metode Otentikasi & Tipe Koneksi Router
                </label>
                <select
                  value={form.auth_method}
                  onChange={(e) => setForm({ ...form, auth_method: e.target.value })}
                  className="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 font-bold text-slate-800 focus:bg-white focus:border-blue-500 outline-none transition text-xs"
                >
                  <option value="pppoe">PPPoE Client (MikroTik RADIUS Authentication)</option>
                  <option value="dhcp">DHCP Lease / IPoE (MAC Binding & IP Pools)</option>
                  <option value="hotspot">Hotspot Voucher (RADIUS Captive Portal)</option>
                  <option value="static">Static IP (Routed Gateway Enterprise)</option>
                </select>
              </div>
            </div>

            {/* Dedicated PPPoE Credentials Configuration Box */}
            <div className="p-4 bg-blue-50/60 border border-blue-200 rounded-2xl space-y-3">
              <div className="flex justify-between items-center border-b border-blue-200/60 pb-2">
                <span className="font-bold text-blue-900 flex items-center gap-1.5 text-xs">
                  <Key className="w-4 h-4 text-blue-600" />
                  <span>Akun Otentikasi PPPoE Pelanggan (Dialer ONT / Router)</span>
                </span>
                <button
                  type="button"
                  onClick={generatePppoe}
                  className="text-[10px] text-blue-700 hover:text-blue-900 font-bold bg-white px-2.5 py-1 rounded-lg border border-blue-300 shadow-xs flex items-center gap-1 cursor-pointer"
                >
                  <RotateCw className="w-3 h-3" />
                  <span>Auto-Generate</span>
                </button>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                  <label className="font-semibold text-slate-700 block mb-1 text-[11px]">
                    Username PPPoE <span className="text-blue-600 font-normal">(Format: 8 Digit KTP-NAMA)</span>
                  </label>
                  <input
                    type="text"
                    required
                    value={form.pppoe_user}
                    onChange={(e) => setForm({ ...form, pppoe_user: e.target.value.toUpperCase() })}
                    placeholder="32750101-BUDI"
                    className="w-full bg-white border border-slate-300 rounded-xl p-2.5 font-mono font-bold text-blue-700 uppercase focus:border-blue-500 outline-none transition text-xs"
                  />
                </div>

                <div>
                  <label className="font-semibold text-slate-700 block mb-1 text-[11px]">Password PPPoE</label>
                  <div className="relative">
                    <input
                      type={showPppoePass ? 'text' : 'password'}
                      required
                      value={form.pppoe_password}
                      onChange={(e) => setForm({ ...form, pppoe_password: e.target.value })}
                      placeholder="Min. 6 Karakter"
                      className="w-full bg-white border border-slate-300 rounded-xl p-2.5 pr-10 font-mono font-bold text-slate-800 focus:border-blue-500 outline-none transition text-xs"
                    />
                    <button
                      type="button"
                      onClick={() => setShowPppoePass(!showPppoePass)}
                      className="absolute right-3 top-2.5 text-slate-400 hover:text-slate-700 cursor-pointer"
                    >
                      {showPppoePass ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <div className="flex justify-between pt-2">
              <button
                type="button"
                onClick={() => setCurrentStep(2)}
                className="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold px-5 py-2.5 rounded-xl transition flex items-center gap-2 cursor-pointer"
              >
                <ArrowLeft className="w-4 h-4" />
                <span>Kembali ke Tahap 2</span>
              </button>
              <button
                type="submit"
                disabled={loading}
                className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-7 py-2.5 rounded-xl shadow-lg shadow-emerald-600/30 transition flex items-center gap-2 cursor-pointer"
              >
                <CheckCircle2 className="w-4 h-4" />
                <span>{loading ? 'Menyimpan...' : 'Simpan & Aktivasi Pelanggan Baru'}</span>
              </button>
            </div>
          </form>
        )}
      </div>
    </div>
  );
}
