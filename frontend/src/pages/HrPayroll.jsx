import React, { useEffect, useState } from 'react';
import { Briefcase, MapPin, UserCheck, DollarSign, Award } from 'lucide-react';
import { api } from '../api/client';
import GpsMap from '../components/GpsMap';

export default function HrPayroll({ showToast }) {
  const [employees, setEmployees] = useState([]);
  const [attendances, setAttendances] = useState([]);
  const [payrolls, setPayrolls] = useState([]);
  const [bonusClaims, setBonusClaims] = useState([]);
  const [loading, setLoading] = useState(true);

  // GPS Clock-in State
  const [selectedEmp, setSelectedEmp] = useState('');
  const [gpsLat, setGpsLat] = useState(-6.289110);
  const [gpsLng, setGpsLng] = useState(106.918210);
  const [clockingIn, setClockingIn] = useState(false);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [empRes, attRes, payRes, claimRes] = await Promise.all([
        api.get('/hr/employees'),
        api.get('/hr/attendances?per_page=20'),
        api.get('/payroll/records'),
        api.get('/payroll/bonus-claims'),
      ]);
      setEmployees(empRes.data || []);
      setAttendances(attRes.data?.data || []);
      setPayrolls(payRes.data || []);
      setBonusClaims(claimRes.data || []);
      if (empRes.data?.length > 0 && !selectedEmp) {
        setSelectedEmp(empRes.data[0].id);
      }
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleClockIn = async (e) => {
    e.preventDefault();
    setClockingIn(true);
    try {
      const res = await api.post('/hr/clock-in', {
        employee_id: selectedEmp,
        gps_lat: Number(gpsLat),
        gps_lng: Number(gpsLng),
      });
      showToast({
        type: 'success',
        title: 'Presensi Tercatat',
        message: `${res.message} (${res.data?.gps_validation?.distance_m}m)`,
      });
      fetchData();
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setClockingIn(false);
    }
  };

  const handleGeneratePayroll = async () => {
    try {
      const res = await api.post('/payroll/generate', { period: 'Agustus 2026' });
      showToast({
        type: 'success',
        title: 'Payroll Selesai',
        message: res.message,
      });
      fetchData();
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    }
  };

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-xl font-bold text-slate-900 flex items-center gap-2">
            <Briefcase className="w-5 h-5 text-red-600" />
            <span>HR, Presensi GPS Haversine & Payroll THP</span>
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Presensi masuk validasi radius GPS Haversine, insentif poin instalasi BAST, dan slip gaji otomatis.
          </p>
        </div>

        <button
          onClick={handleGeneratePayroll}
          className="btn-primary text-xs py-2.5 px-4 flex items-center space-x-2"
        >
          <DollarSign className="w-4 h-4" />
          <span>Kalkulasi Payroll THP Massal</span>
        </button>
      </div>

      {/* GPS Clock-in Simulator Card */}
      <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider mb-1 flex items-center gap-2">
          <MapPin className="w-4 h-4 text-red-600" />
          <span>Simulasi Presensi GPS Haversine Radius Kantor</span>
        </h3>
        <p className="text-xs text-slate-400 mb-4">
          Titik Pusat Kantor POP: Lat -6.289100, Lng 106.918200 (Batas radius valid: Maksimum 200 meter).
        </p>

        <form onSubmit={handleClockIn} className="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
          <div>
            <label className="block text-[11px] font-semibold text-slate-600 mb-1">Pilih Karyawan</label>
            <select
              value={selectedEmp}
              onChange={(e) => setSelectedEmp(e.target.value)}
              className="input-field text-xs py-2"
            >
              {employees.map((emp) => (
                <option key={emp.id} value={emp.id}>
                  {emp.name} ({emp.division})
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-[11px] font-semibold text-slate-600 mb-1">Koordinat GPS Lat</label>
            <input
              type="number"
              step="any"
              value={gpsLat}
              onChange={(e) => setGpsLat(e.target.value)}
              className="input-field text-xs py-1.5 font-mono"
            />
          </div>

          <div>
            <label className="block text-[11px] font-semibold text-slate-600 mb-1">Koordinat GPS Lng</label>
            <input
              type="number"
              step="any"
              value={gpsLng}
              onChange={(e) => setGpsLng(e.target.value)}
              className="input-field text-xs py-1.5 font-mono"
            />
          </div>

          <button
            type="submit"
            disabled={clockingIn}
            className="btn-primary text-xs py-2 px-4 flex items-center justify-center space-x-1.5"
          >
            <UserCheck className="w-4 h-4" />
            <span>{clockingIn ? 'Validasi Radius...' : 'Clock-In Sekarang'}</span>
          </button>
        </form>

        <div className="mt-4">
          <GpsMap
            lat={Number(gpsLat) || -6.289110}
            lng={Number(gpsLng) || 106.918210}
            title="Lokasi Karyawan"
            subtitle="Klik pada peta untuk mensimulasikan titik presensi"
            height="180px"
            zoom={16}
            interactive={true}
            onChange={(lat, lng) => {
              setGpsLat(Number(lat.toFixed(6)));
              setGpsLng(Number(lng.toFixed(6)));
            }}
          />
        </div>
      </div>

      {/* Grid Slip Gaji & Insentif */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Slip Gaji THP */}
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <div className="p-4 border-b border-slate-100 flex justify-between items-center">
            <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <DollarSign className="w-4 h-4 text-emerald-600" />
              <span>Slip Gaji Take Home Pay (THP)</span>
            </h3>
            <span className="badge badge-active text-[10px]">Periode Berjalan</span>
          </div>

          <div className="table-container">
            <table className="custom-table">
              <thead>
                <tr>
                  <th>Nama Karyawan</th>
                  <th>Gaji Pokok & Tunj</th>
                  <th>Bonus BAST</th>
                  <th>Potongan</th>
                  <th className="text-right">THP Bersih</th>
                </tr>
              </thead>
              <tbody>
                {payrolls.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="text-center py-6 text-slate-400 text-xs">
                      Belum ada data payroll. Klik tombol 'Kalkulasi Payroll THP Massal'.
                    </td>
                  </tr>
                ) : (
                  payrolls.map((p) => (
                    <tr key={p.id}>
                      <td className="font-bold text-slate-900 text-xs">{p.employee_name}</td>
                      <td className="text-xs text-slate-600">
                        Rp {Number(Number(p.basic_salary) + Number(p.allowance)).toLocaleString('id-ID')}
                      </td>
                      <td className="text-xs font-bold text-emerald-600">
                        Rp {Number(p.bonus).toLocaleString('id-ID')}
                      </td>
                      <td className="text-xs text-rose-600">
                        Rp {Number(p.deductions).toLocaleString('id-ID')}
                      </td>
                      <td className="text-right font-mono font-bold text-emerald-600 text-xs">
                        Rp {Number(p.thp).toLocaleString('id-ID')}
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>

        {/* Insentif Poin BAST */}
        <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
          <div className="p-4 border-b border-slate-100 flex justify-between items-center">
            <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
              <Award className="w-4 h-4 text-amber-600" />
              <span>Insentif Poin BAST Teknisi Lapangan</span>
            </h3>
          </div>

          <div className="table-container">
            <table className="custom-table">
              <thead>
                <tr>
                  <th>Nama Teknisi</th>
                  <th>No BAST</th>
                  <th>Poin</th>
                  <th>Nominal Bonus</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                {bonusClaims.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="text-center py-6 text-slate-400 text-xs">
                      Belum ada klaim bonus poin instalasi.
                    </td>
                  </tr>
                ) : (
                  bonusClaims.map((bc) => (
                    <tr key={bc.id}>
                      <td className="font-bold text-slate-900 text-xs">{bc.employee_name}</td>
                      <td className="font-mono text-xs text-slate-500">{bc.bast_no}</td>
                      <td className="text-xs font-bold text-amber-600">+{bc.points} Poin</td>
                      <td className="font-mono text-xs text-slate-800">
                        Rp {Number(bc.total_amount).toLocaleString('id-ID')}
                      </td>
                      <td>
                        <span className="badge badge-active">{bc.status}</span>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
}
