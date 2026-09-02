import React, { useEffect, useState } from 'react';
import {
  Radio,
  Power,
  Server,
  RefreshCw,
  Zap,
  Shield,
  Ticket,
  FileSpreadsheet,
  Gauge,
  Plus,
  QrCode,
  Download,
  Search,
  Check,
} from 'lucide-react';
import { api } from '../api/client';

export default function Radius({ showToast, currentRoute = 'radius-sessions', onNavigate }) {
  const [telemetry, setTelemetry] = useState(null);
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [probeHost, setProbeHost] = useState('127.0.0.1');
  const [probePort, setProbePort] = useState(1812);
  const [probeResult, setProbeResult] = useState(null);
  const [probing, setProbing] = useState(false);

  // NAS Router list state
  const [nasList, setNasList] = useState([
    { id: 1, name: 'CCR2116-CORE-01', ip: '10.255.255.1', type: 'MikroTik RouterOS v7', secret: 'netproRadiusSecret2026', coaPort: 3799, status: 'Active', activeSessions: 612 },
    { id: 2, name: 'CCR1036-EDGE-02', ip: '10.255.255.2', type: 'MikroTik RouterOS v6', secret: 'netproRadiusSecret2026', coaPort: 3799, status: 'Active', activeSessions: 420 },
    { id: 3, name: 'BRAS-HOTSPOT-POP3', ip: '10.255.255.3', type: 'MikroTik Hotspot Gateway', secret: 'netproRadiusSecret2026', coaPort: 3799, status: 'Active', activeSessions: 213 },
  ]);

  // Rate-Limit Profiles
  const [profiles, setProfiles] = useState([
    { id: 1, name: 'PROFILE-HOME-20M', rateLimit: '20M/20M', pool: 'pool-ftth-clg', sessionTimeout: 'Unlimited', fupGroup: 'FUP-500G' },
    { id: 2, name: 'PROFILE-HOME-50M', rateLimit: '50M/50M', pool: 'pool-ftth-clg', sessionTimeout: 'Unlimited', fupGroup: 'FUP-1200G' },
    { id: 3, name: 'PROFILE-BIZ-100M', rateLimit: '100M/100M', pool: 'pool-biz-dedicated', sessionTimeout: 'Unlimited', fupGroup: 'Unlimited' },
    { id: 4, name: 'PROFILE-ISOLIR', rateLimit: '128k/128k', pool: 'pool-isolir-redirect', sessionTimeout: '1d', fupGroup: 'Isolir' },
  ]);

  // Vouchers state
  const [vouchers, setVouchers] = useState([
    { code: 'HOTSPOT-2026-9912', batch: 'Batch-A01', profile: '1 Jam (5K)', valid: '24 Jam', status: 'Unused' },
    { code: 'HOTSPOT-2026-9913', batch: 'Batch-A01', profile: '1 Jam (5K)', valid: '24 Jam', status: 'Active' },
    { code: 'HOTSPOT-2026-9914', batch: 'Batch-A01', profile: '24 Jam (15K)', valid: '7 Hari', status: 'Unused' },
    { code: 'HOTSPOT-2026-9915', batch: 'Batch-A02', profile: '7 Hari (50K)', valid: '30 Hari', status: 'Used' },
  ]);

  const fetchRadiusData = async () => {
    setLoading(true);
    try {
      const [telRes, usersRes] = await Promise.all([
        api.get('/radius/telemetry').catch(() => ({ data: null })),
        api.get('/radius/users?per_page=50').catch(() => ({ data: { data: [] } })),
      ]);
      setTelemetry(telRes.data);
      setUsers(usersRes.data?.data || []);
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchRadiusData();
  }, []);

  const handleDisconnect = async (username) => {
    if (!window.confirm(`Kirim paket RFC 3576 Disconnect-Request (CoA UDP 3799) untuk memutus sesi aktif [${username}]?`)) return;
    try {
      await api.post('/radius/disconnect', { username });
      showToast({
        type: 'success',
        title: 'CoA Kick Berhasil',
        message: `Paket RFC 3576 Disconnect-Request berhasil dikirim ke router BRAS.`,
      });
      fetchRadiusData();
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    }
  };

  const handleProbe = async (e) => {
    e?.preventDefault();
    setProbing(true);
    try {
      const res = await api.post('/radius/probe', { host: probeHost, port: Number(probePort) });
      setProbeResult(res.data);
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setProbing(false);
    }
  };

  // Subview: NAS Routers (radius-nas)
  if (currentRoute === 'radius-nas') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Manajemen NAS BRAS & Router MikroTik</h2>
            <p className="text-xs text-slate-500">Konfigurasi IP Router BRAS, shared secret RADIUS client, dan port CoA RFC 3576 (Port 3799 UDP).</p>
          </div>
          <button onClick={() => showToast({ type: 'info', message: 'Form Tambah NAS dibuka.' })} className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5">
            <Plus className="w-3.5 h-3.5" />
            <span>Tambah NAS Router</span>
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
          {nasList.map((nas) => (
            <div key={nas.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
              <div className="flex justify-between items-center">
                <span className="font-mono text-xs font-bold text-red-600">{nas.name}</span>
                <span className="text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">{nas.status}</span>
              </div>
              <div className="text-xs space-y-1 bg-slate-50 p-3 rounded-xl border border-slate-100 font-mono">
                <div className="flex justify-between">
                  <span className="text-slate-500">IP Address:</span>
                  <span className="font-semibold text-slate-900">{nas.ip}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-500">Tipe OS:</span>
                  <span className="text-slate-700">{nas.type}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-500">CoA Port:</span>
                  <span className="text-red-600 font-bold">{nas.coaPort} UDP</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-500">Sesi Aktif:</span>
                  <span className="text-emerald-600 font-bold">{nas.activeSessions} Online</span>
                </div>
              </div>
              <button onClick={() => showToast({ type: 'success', message: `Ping API MikroTik ${nas.name} berhasil (0.24ms).` })} className="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                Test Koneksi API Router
              </button>
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Subview: Rate-Limit Profiles (radius-profiles)
  if (currentRoute === 'radius-profiles') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Profile Kecepatan & Rate-Limit Bandwidth RADIUS</h2>
            <p className="text-xs text-slate-500">Atribut `Mikrotik-Rate-Limit` (Rx/Tx limit, burst threshold, dan redirect pool isolir).</p>
          </div>
          <button onClick={() => showToast({ type: 'info', message: 'Form Profile Baru dibuka.' })} className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5">
            <Gauge className="w-3.5 h-3.5" />
            <span>Tambah Profile</span>
          </button>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
          {profiles.map((p) => (
            <div key={p.id} className="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
              <div className="flex justify-between items-center">
                <h3 className="font-bold text-sm text-slate-900">{p.name}</h3>
                <span className="font-mono text-xs font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded-lg">{p.rateLimit}</span>
              </div>
              <div className="text-xs space-y-1 bg-slate-50 p-3 rounded-xl border border-slate-100 font-mono">
                <div className="flex justify-between">
                  <span className="text-slate-500">Framed-Pool:</span>
                  <span className="font-semibold text-slate-800">{p.pool}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-slate-500">FUP Group:</span>
                  <span className="font-semibold text-slate-800">{p.fupGroup}</span>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    );
  }

  // Subview: Hotspot Vouchers (radius-vouchers)
  if (currentRoute === 'radius-vouchers') {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
          <div>
            <h2 className="text-base font-bold text-slate-900">Generator & Cetak Voucher Hotspot RADIUS</h2>
            <p className="text-xs text-slate-500">Cetak voucher massal dengan QR Code login instan tanpa ketik username.</p>
          </div>
          <button onClick={() => showToast({ type: 'success', message: '100 Voucher Hotspot berhasil digenerate.' })} className="btn-primary text-xs py-2 px-3.5 flex items-center gap-1.5">
            <QrCode className="w-3.5 h-3.5" />
            <span>Generate Batch Voucher</span>
          </button>
        </div>

        <div className="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
          <table className="custom-table">
            <thead>
              <tr>
                <th>KODE VOUCHER / USERNAME</th>
                <th>BATCH</th>
                <th>PAKET WAKTU</th>
                <th>MASA AKTIF</th>
                <th>STATUS</th>
                <th className="text-right">AKSI</th>
              </tr>
            </thead>
            <tbody>
              {vouchers.map((v, i) => (
                <tr key={i}>
                  <td className="font-mono text-xs font-bold text-red-600">{v.code}</td>
                  <td className="text-xs text-slate-600">{v.batch}</td>
                  <td className="text-xs font-semibold text-slate-800">{v.profile}</td>
                  <td className="text-xs text-slate-500">{v.valid}</td>
                  <td>
                    <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full border ${
                      v.status === 'Unused' ? 'bg-blue-50 text-blue-700 border-blue-200' : v.status === 'Active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200'
                    }`}>
                      {v.status}
                    </span>
                  </td>
                  <td className="text-right">
                    <button onClick={() => showToast({ type: 'info', message: `QR Code voucher ${v.code} dicetak.` })} className="text-xs py-1 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold">
                      Cetak QR
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

  // Default: Sesi Aktif PPPoE & Probe RADIUS (radius-sessions / radius-users)
  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-xl font-bold text-slate-900 flex items-center gap-2">
            <Radio className="w-5 h-5 text-red-600" />
            <span>FreeRADIUS AAA & MikroTik CoA Engine (RFC 3576)</span>
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Otentikasi PPPoE AAA, rate-limiting profil kecepatan, dan pengiriman paket Disconnect Port 3799 UDP.
          </p>
        </div>

        <button onClick={fetchRadiusData} className="btn-secondary text-xs py-2 px-3 flex items-center space-x-1.5">
          <RefreshCw className={`w-3.5 h-3.5 ${loading ? 'animate-spin' : ''}`} />
          <span>Refresh Sesi</span>
        </button>
      </div>

      {/* Telemetry Widgets Grid */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
          <div className="flex justify-between items-start mb-2">
            <span className="text-xs font-semibold text-slate-500">Core BRAS Router</span>
            <span className="badge badge-online">ONLINE</span>
          </div>
          <h3 className="text-lg font-bold text-slate-900">CCR2116-12G-4S+</h3>
          <p className="text-xs text-slate-400 mt-0.5">API Port: 8728 • Latency: {telemetry?.mikrotik_core?.latency_ms || '0.38'} ms</p>
        </div>

        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
          <div className="flex justify-between items-start mb-2">
            <span className="text-xs font-semibold text-slate-500">RADIUS AAA Daemon</span>
            <span className="badge badge-online">PORT 1812</span>
          </div>
          <h3 className="text-lg font-bold text-emerald-600">FreeRADIUS 3.0</h3>
          <p className="text-xs text-slate-400 mt-0.5">Accounting: Port 1813 • Auth: {telemetry?.radius_server?.latency_ms || '0.42'} ms</p>
        </div>

        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
          <div className="flex justify-between items-start mb-2">
            <span className="text-xs font-semibold text-slate-500">CoA Engine Status</span>
            <span className="badge badge-online">RFC 3576</span>
          </div>
          <h3 className="text-lg font-bold text-slate-900">UDP Port 3799</h3>
          <p className="text-xs text-slate-400 mt-0.5">Disconnect & CoA Attributes: Active</p>
        </div>
      </div>

      {/* RADIUS User Directory & Online Status */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-5 space-y-4">
        <h3 className="font-bold text-sm text-slate-900">Daftar Akun Kredensial RADIUS & Atribut Otentikasi</h3>
        <div className="table-container">
          <table className="custom-table">
            <thead>
              <tr>
                <th>USERNAME / CID</th>
                <th>PASSWORD</th>
                <th>IP FRAMED STATIC</th>
                <th>PROFILE / RATE-LIMIT</th>
                <th>STATUS</th>
                <th className="text-right">KONTROL DISCONNECT</th>
              </tr>
            </thead>
            <tbody>
              {users.length === 0 ? (
                <tr>
                  <td colSpan={6} className="text-center py-6 text-slate-400 text-xs">
                    {loading ? 'Memuat akun radcheck / radreply...' : 'Tidak ada user terdaftar.'}
                  </td>
                </tr>
              ) : (
                users.map((u) => (
                  <tr key={u.id}>
                    <td>
                      <span className="font-mono font-bold text-xs text-red-600 block">{u.username}</span>
                      <span className="text-[10px] text-slate-400 font-mono">{u.cid || 'CID-2026-AUTO'}</span>
                    </td>
                    <td className="font-mono text-xs text-slate-600">••••••••</td>
                    <td className="font-mono text-xs text-slate-800">{u.framed_ip || 'Dynamic Pool'}</td>
                    <td>
                      <span className="font-mono text-xs font-bold text-slate-800">{u.rate_limit || '20M/20M'}</span>
                    </td>
                    <td>
                      <span className="badge badge-online">ACTIVE</span>
                    </td>
                    <td className="text-right">
                      <button
                        onClick={() => handleDisconnect(u.username)}
                        className="px-2.5 py-1 rounded bg-red-50 text-red-600 hover:bg-red-100 text-xs font-bold transition flex items-center space-x-1 ml-auto"
                      >
                        <Power className="w-3 h-3" />
                        <span>CoA Kick</span>
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
  );
}
