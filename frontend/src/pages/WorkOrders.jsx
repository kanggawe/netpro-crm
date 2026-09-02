import React, { useEffect, useState } from 'react';
import {
  ClipboardList,
  Plus,
  HardHat,
  Network,
  Radio,
  Server,
  Activity,
  AlertTriangle,
  Layers,
  Cpu,
  RefreshCw,
  CheckCircle,
  Router,
  MapPin,
  Check,
} from 'lucide-react';
import { api } from '../api/client';

export default function WorkOrders({ showToast, currentRoute = 'noc-monitoring', onNavigate }) {
  const [workOrders, setWorkOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isModalOpen, setIsModalOpen] = useState(false);

  // OLT list
  const [olts, setOlts] = useState([
    { name: 'OLT-ZTE-C320-KODAM', ip: '10.100.1.10', type: 'ZTE C320 (16 PON)', ponUsed: '14/16', onus: 512, status: 'Online', temp: '42°C' },
    { name: 'OLT-HW-MA5608T-PUSAT', ip: '10.100.1.11', type: 'Huawei MA5608T (8 PON)', ponUsed: '8/8', onus: 380, status: 'Online', temp: '44°C' },
    { name: 'OLT-VSOL-V1600G-CILEGON', ip: '10.100.1.12', type: 'V-SOL GPON (8 PON)', ponUsed: '6/8', onus: 245, status: 'Online', temp: '39°C' },
  ]);

  // ODP list
  const [odps, setOdps] = useState([
    { code: 'ODP-CLG-01', location: 'Jl. Ahmad Yani No. 12 (Tiang PLN 44)', capacity: '8 Port (7 Terpakai)', status: 'Optimal', rxPower: '-18.4 dBm' },
    { code: 'ODP-CLG-02', location: 'Perumahan Griya Indah Blok C', capacity: '16 Port (14 Terpakai)', status: 'Optimal', rxPower: '-19.1 dBm' },
    { code: 'ODP-CLG-03', location: 'Simpang Tiga Cilegon (Tiang 12)', capacity: '8 Port (8 Penuh)', status: 'Full', rxPower: '-20.5 dBm' },
    { code: 'ODP-CLG-04', location: 'Jl. Merdeka RT 04/02', capacity: '8 Port (4 Terpakai)', status: 'Optimal', rxPower: '-17.8 dBm' },
  ]);

  // ONUs list
  const [onus, setOnus] = useState([
    { sn: 'ZTEGC9921008', name: 'Bpk. Hendra Gunawan', olt: 'OLT-ZTE-C320-KODAM', pon: 'PON 1/2', rxPower: '-18.2 dBm', status: 'Online' },
    { sn: 'HWTC8812903', name: 'Klinik Medika Pratama', olt: 'OLT-HW-MA5608T-PUSAT', pon: 'PON 1/1', rxPower: '-19.4 dBm', status: 'Online' },
    { sn: 'ZTEGC9981120', name: 'Toko Sumber Rejeki', olt: 'OLT-ZTE-C320-KODAM', pon: 'PON 1/3', rxPower: '-27.8 dBm', status: 'Warning Low' },
    { sn: 'VSOL7721992', name: 'Ahmad Syafiq', olt: 'OLT-VSOL-V1600G-CILEGON', pon: 'PON 1/4', rxPower: '-18.9 dBm', status: 'Online' },
  ]);

  // Outages list
  const [outages, setOutages] = useState([
    { id: 'OUT-2026-012', region: 'Cilegon Barat - Segmen FO ODC-02', cause: 'Kabel FO Tertabrak Truk Kontainer', affected: '142 Pelanggan', status: 'Under Repair', eta: '45 Menit' },
    { id: 'OUT-2026-011', region: 'POP Kodam - PLN Outage', cause: 'Genset Otomatis Menyala Normal', affected: '0 Pelanggan (Backup Aktif)', status: 'Resolved', eta: 'Selesai' },
  ]);

  const [form, setForm] = useState({
    customer_name: '',
    package_name: 'Home 20M',
    ont_type: 'ZTE F670L Dual Band',
    ont_sn: '',
    tech_name: 'Ahmad Rian Maulana',
    odp_port: 'ODP-CLG-01/Port-01',
    attenuation: '-18.2 dBm',
  });

  const fetchData = async () => {
    setLoading(true);
    try {
      const woRes = await api.get('/work-orders').catch(() => ({ data: { data: [] } }));
      setWorkOrders(woRes.data?.data || []);
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  // Subview: Topologi Backbone
  if (currentRoute === 'noc-topologi') {
    return (
      <div className="space-y-6">
        <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex justify-between items-center">
          <div>
            <h2 className="text-base font-bold text-slate-900">Visualisasi Topologi Jaringan Backbone & FTTx</h2>
            <p className="text-xs text-slate-500">Arsitektur Core BRAS, OLT Chassis, FDT/ODC Cabinet, hingga FAT/ODP Tiang Pelanggan.</p>
          </div>
          <button onClick={() => showToast({ type: 'success', message: 'Topologi berhasil disinkronkan dari LLDP & SNMP.' })} className="btn-primary text-xs py-2 px-3 flex items-center gap-1.5">
            <RefreshCw className="w-3.5 h-3.5" />
            <span>Sync LLDP/SNMP</span>
          </button>
        </div>

        <div className="bg-slate-950 p-8 rounded-3xl border border-slate-800 text-white space-y-8">
          <div className="flex flex-col md:flex-row items-center justify-center gap-6">
            {/* Core */}
            <div className="bg-slate-900 border-2 border-red-500 p-4 rounded-2xl text-center w-56 shadow-lg shadow-red-950/50">
              <span className="text-[10px] font-mono text-red-400 uppercase font-bold">CORE BRAS ROUTER</span>
              <h4 className="font-bold text-sm text-white mt-1">CCR2116-CORE-01</h4>
              <span className="text-[11px] text-emerald-400 block mt-1">● BGP Upstream 10G</span>
            </div>

            <div className="text-red-500 text-xl font-bold hidden md:block">➔ 10G SFP+ ➔</div>

            {/* OLT */}
            <div className="bg-slate-900 border-2 border-blue-500 p-4 rounded-2xl text-center w-56 shadow-lg shadow-blue-950/50">
              <span className="text-[10px] font-mono text-blue-400 uppercase font-bold">GPON OLT CHASSIS</span>
              <h4 className="font-bold text-sm text-white mt-1">OLT-ZTE-C320</h4>
              <span className="text-[11px] text-blue-300 block mt-1">● 16 PON Active (1:64)</span>
            </div>

            <div className="text-blue-500 text-xl font-bold hidden md:block">➔ Feeder FO ➔</div>

            {/* ODC */}
            <div className="bg-slate-900 border-2 border-amber-500 p-4 rounded-2xl text-center w-56 shadow-lg shadow-amber-950/50">
              <span className="text-[10px] font-mono text-amber-400 uppercase font-bold">ODC DISTRIBUTION</span>
              <h4 className="font-bold text-sm text-white mt-1">ODC-KODAM-01</h4>
              <span className="text-[11px] text-amber-300 block mt-1">● 48 Core Splitter</span>
            </div>

            <div className="text-amber-500 text-xl font-bold hidden md:block">➔ Drop FO ➔</div>

            {/* ODP */}
            <div className="bg-slate-900 border-2 border-emerald-500 p-4 rounded-2xl text-center w-56 shadow-lg shadow-emerald-950/50">
              <span className="text-[10px] font-mono text-emerald-400 uppercase font-bold">FAT / ODP TIANG</span>
              <h4 className="font-bold text-sm text-white mt-1">ODP-CLG-01..16</h4>
              <span className="text-[11px] text-emerald-300 block mt-1">● Redaman -18 dBm</span>
            </div>
          </div>
        </div>
      </div>
    );
  }

  // Subview: GPON OLT (noc-olt)
  if (currentRoute === 'noc-olt') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Manajemen GPON OLT (Optical Line Terminal)</h2>
            <p className="text-xs text-slate-500">Monitoring status port PON, suhu chassis, power supply modul, dan otentikasi OMCI.</p>
          </div>
          <button onClick={() => showToast({ type: 'info', message: 'Form Tambah OLT dibuka.' })} className="btn-primary text-xs py-2 px-3 flex items-center gap-1.5">
            <Plus className="w-3.5 h-3.5" />
            <span>Tambah OLT</span>
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          {olts.map((olt, i) => (
            <div key={i} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
              <div className="flex justify-between items-center">
                <span className="font-mono text-xs font-bold text-red-600">{olt.name}</span>
                <span className="badge badge-online">{olt.status}</span>
              </div>
              <p className="text-xs text-slate-500">{olt.type}</p>
              <div className="text-xs space-y-1 bg-slate-50 p-3 rounded-xl border border-slate-100 font-mono">
                <div className="flex justify-between"><span className="text-slate-500">IP Host:</span><strong>{olt.ip}</strong></div>
                <div className="flex justify-between"><span className="text-slate-500">Port PON:</span><strong className="text-blue-600">{olt.ponUsed}</strong></div>
                <div className="flex justify-between"><span className="text-slate-500">Total ONT:</span><strong className="text-emerald-600">{olt.onus} Unit</strong></div>
                <div className="flex justify-between"><span className="text-slate-500">Suhu:</span><strong>{olt.temp}</strong></div>
              </div>
              <button onClick={() => showToast({ type: 'success', message: `OMCI Auto-Find pada ${olt.name} berhasil.` })} className="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Scan Unregistered ONT
              </button>
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Subview: ODP / FAT (noc-odp)
  if (currentRoute === 'noc-odp') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Manajemen FAT / ODP (Optical Distribution Point)</h2>
            <p className="text-xs text-slate-500">Pemetaan nomor tiang, kapasitas port splitter, dan rata-rata redaman optik.</p>
          </div>
          <button onClick={() => showToast({ type: 'info', message: 'Form Tambah ODP dibuka.' })} className="btn-primary text-xs py-2 px-3 flex items-center gap-1.5">
            <Plus className="w-3.5 h-3.5" />
            <span>Tambah ODP Tiang</span>
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          {odps.map((odp, i) => (
            <div key={i} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
              <div className="flex justify-between items-center">
                <span className="font-mono text-xs font-bold text-red-600">{odp.code}</span>
                <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full border ${
                  odp.status === 'Full' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'
                }`}>{odp.status}</span>
              </div>
              <div className="text-xs flex items-center gap-1 text-slate-600">
                <MapPin className="w-3.5 h-3.5 text-slate-400 shrink-0" />
                <span>{odp.location}</span>
              </div>
              <div className="text-xs space-y-1 bg-slate-50 p-3 rounded-xl border border-slate-100 font-mono">
                <div className="flex justify-between"><span className="text-slate-500">Kapasitas:</span><strong>{odp.capacity}</strong></div>
                <div className="flex justify-between"><span className="text-slate-500">Rata-rata Redaman:</span><strong className="text-emerald-600">{odp.rxPower}</strong></div>
              </div>
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Subview: ONUs & RX Power (noc-onu)
  if (currentRoute === 'noc-onu') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Monitoring Modem ONT / ONU & Redaman RX Real-Time</h2>
            <p className="text-xs text-slate-500">Pengecekan sinyal optik dBm dari OLT secara otomatis via OMCI CLI.</p>
          </div>
        </div>

        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <table className="custom-table">
            <thead>
              <tr>
                <th>SERIAL NUMBER (SN)</th>
                <th>PELANGGAN</th>
                <th>OLT & PON PORT</th>
                <th>REDAMAN RX (DBM)</th>
                <th>STATUS</th>
                <th className="text-right">AKSI</th>
              </tr>
            </thead>
            <tbody>
              {onus.map((onu, i) => (
                <tr key={i}>
                  <td className="font-mono text-xs font-bold text-red-600">{onu.sn}</td>
                  <td className="text-xs font-bold text-slate-900">{onu.name}</td>
                  <td className="text-xs font-mono text-slate-700">{onu.olt} ({onu.pon})</td>
                  <td>
                    <span className={`font-mono text-xs font-bold ${
                      onu.rxPower.startsWith('-27') ? 'text-red-600' : 'text-emerald-600'
                    }`}>{onu.rxPower}</span>
                  </td>
                  <td>
                    <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full border ${
                      onu.status === 'Online' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200'
                    }`}>{onu.status}</span>
                  </td>
                  <td className="text-right">
                    <button onClick={() => showToast({ type: 'info', message: `Reboot modem ${onu.sn} dikirim via OMCI.` })} className="text-xs py-1 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold">
                      Reboot ONT
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

  // Subview: Outage & Insiden (noc-outage)
  if (currentRoute === 'noc-outage') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Manajemen Insiden, Outage & Fiber Cut</h2>
            <p className="text-xs text-slate-500">Pusat pelaporan gangguan massal, estimasi waktu pemulihan (ETA), dan dispatch tim splicing FO.</p>
          </div>
          <button onClick={() => showToast({ type: 'info', message: 'Form Buat Tiket Outage dibuka.' })} className="btn-primary text-xs py-2 px-3 flex items-center gap-1.5">
            <AlertTriangle className="w-3.5 h-3.5" />
            <span>Lapor Insiden Outage</span>
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          {outages.map((out) => (
            <div key={out.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
              <div className="flex justify-between items-center">
                <span className="font-mono text-xs font-bold text-red-600">{out.id}</span>
                <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full border ${
                  out.status === 'Resolved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200'
                }`}>{out.status}</span>
              </div>
              <div>
                <h4 className="text-sm font-bold text-slate-900">{out.region}</h4>
                <p className="text-xs text-slate-500 mt-0.5">Penyebab: <strong>{out.cause}</strong></p>
              </div>
              <div className="text-xs space-y-1 bg-slate-50 p-3 rounded-xl border border-slate-100 font-mono">
                <div className="flex justify-between"><span className="text-slate-500">Dampak Pelanggan:</span><strong>{out.affected}</strong></div>
                <div className="flex justify-between"><span className="text-slate-500">Estimasi Selesai (ETA):</span><strong className="text-red-600">{out.eta}</strong></div>
              </div>
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Default: NOC Monitoring Overview (noc-monitoring / work-orders)
  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-xl font-bold text-slate-900 flex items-center gap-2">
            <Activity className="w-5 h-5 text-red-600" />
            <span>Monitoring NOC & Perangkat Jaringan ISP</span>
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Status OLT GPON, traffic bandwidth uplink, dan perintah kerja lapangan.
          </p>
        </div>

        <button
          onClick={() => setIsModalOpen(true)}
          className="btn-primary text-xs py-2.5 px-4 flex items-center space-x-2"
        >
          <Plus className="w-4 h-4" />
          <span>Buat SPK Instalasi</span>
        </button>
      </div>

      {/* Work Orders Table */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="p-4 border-b border-slate-100">
          <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
            <HardHat className="w-4 h-4 text-red-600" />
            <span>Daftar SPK Instalasi & Hasil Ukur OPM</span>
          </h3>
        </div>

        <div className="table-container">
          <table className="custom-table">
            <thead>
              <tr>
                <th>NO SPK / BAST</th>
                <th>PELANGGAN</th>
                <th>PERANGKAT ONT / SN</th>
                <th>ODP / PORT</th>
                <th>HASIL OPM (DBM)</th>
                <th>STATUS</th>
              </tr>
            </thead>
            <tbody>
              {workOrders.length === 0 ? (
                <tr>
                  <td colSpan={6} className="text-center py-6 text-slate-400 text-xs">
                    {loading ? 'Memuat daftar SPK...' : 'Belum ada data SPK.'}
                  </td>
                </tr>
              ) : (
                workOrders.map((wo) => (
                  <tr key={wo.id}>
                    <td>
                      <span className="font-mono font-bold text-xs text-red-600 block">
                        #{wo.wo_no}
                      </span>
                      <span className="text-[10px] text-slate-400 font-mono">
                        BAST #{wo.bast_no}
                      </span>
                    </td>
                    <td>
                      <div className="text-xs font-bold text-slate-900">{wo.customer_name}</div>
                      <div className="text-[11px] text-slate-500">{wo.package_name}</div>
                    </td>
                    <td>
                      <div className="text-xs text-slate-800">{wo.ont_type}</div>
                      <div className="font-mono text-[10px] text-slate-400">{wo.ont_sn}</div>
                    </td>
                    <td>
                      <span className="text-xs font-mono text-slate-700">{wo.odp_port}</span>
                    </td>
                    <td>
                      <span className="font-mono text-xs font-bold text-emerald-600">
                        {wo.attenuation}
                      </span>
                    </td>
                    <td>
                      <span className="badge badge-online">COMPLETED</span>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
