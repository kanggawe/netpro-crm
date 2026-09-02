import React, { useEffect, useState } from 'react';
import { Boxes, Building2, UserCheck } from 'lucide-react';
import { api } from '../api/client';

export default function Inventory({ showToast }) {
  const [items, setItems] = useState([]);
  const [branches, setBranches] = useState([]);
  const [leads, setLeads] = useState([]);
  const [loading, setLoading] = useState(true);

  const [adjustModalOpen, setAdjustModalOpen] = useState(false);
  const [selectedItem, setSelectedItem] = useState(null);
  const [adjustment, setAdjustment] = useState(5);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [itemsRes, brRes, ldRes] = await Promise.all([
        api.get('/inventory/items'),
        api.get('/branches'),
        api.get('/leads'),
      ]);
      setItems(itemsRes.data || []);
      setBranches(brRes.data || []);
      setLeads(ldRes.data || []);
    } catch (err) {
      showToast({ type: 'error', message: err.message });
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleAdjustStock = async (e) => {
    e.preventDefault();
    if (!selectedItem) return;
    try {
      await api.post(`/inventory/items/${selectedItem.id}/adjust-stock`, { adjustment: Number(adjustment) });
      showToast({
        type: 'success',
        title: 'Stok Diperbarui',
        message: `Stok ${selectedItem.name} berhasil disesuaikan.`,
      });
      setAdjustModalOpen(false);
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
            <Boxes className="w-5 h-5 text-red-600" />
            <span>Gudang Material ONT & Infrastruktur Cabang</span>
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Inventori perangkat ONT, Drop Cable FO, SFP Transceiver, serta daftar kantor cabang operasional.
          </p>
        </div>
      </div>

      {/* Material Table */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div className="p-4 border-b border-slate-100">
          <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider">
            Stok Material & Hardware Jaringan
          </h3>
        </div>

        <div className="table-container">
          <table className="custom-table">
            <thead>
              <tr>
                <th>SKU</th>
                <th>Nama Material / Hardware</th>
                <th>Kategori</th>
                <th>Stok Gudang</th>
                <th>Estimasi Harga Satuan</th>
                <th>Status</th>
                <th className="text-right">Aksi</th>
              </tr>
            </thead>
            <tbody>
              {items.map((it) => (
                <tr key={it.id}>
                  <td className="font-mono text-xs font-bold text-red-600">{it.sku}</td>
                  <td className="font-bold text-slate-900 text-xs">{it.name}</td>
                  <td className="text-xs text-slate-500">{it.category}</td>
                  <td className="font-bold font-mono text-slate-900 text-xs">
                    {it.stock} {it.unit}
                  </td>
                  <td className="font-mono text-xs text-slate-600">
                    Rp {Number(it.unit_cost).toLocaleString('id-ID')}
                  </td>
                  <td>
                    <span className={`badge ${it.status === 'AMAN' ? 'badge-online' : 'badge-isolated'}`}>
                      {it.status}
                    </span>
                  </td>
                  <td className="text-right">
                    <button
                      onClick={() => {
                        setSelectedItem(it);
                        setAdjustModalOpen(true);
                      }}
                      className="btn-secondary text-xs py-1 px-2.5"
                    >
                      Sesuaikan Stok
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Kantor Cabang Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
          <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
            <Building2 className="w-4 h-4 text-red-600" />
            <span>Kantor Cabang Operasional ISP</span>
          </h3>
          <div className="space-y-2">
            {branches.map((br) => (
              <div key={br.id} className="p-3 rounded-lg bg-slate-50 border border-slate-200/60 flex justify-between items-center">
                <div>
                  <h4 className="text-xs font-bold text-slate-900">{br.name}</h4>
                  <p className="text-[11px] text-slate-500">{br.address} • Pimpinan: {br.manager}</p>
                </div>
                <span className="badge badge-active">{br.subs_count} Pelanggan</span>
              </div>
            ))}
          </div>
        </div>

        <div className="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
          <h3 className="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
            <UserCheck className="w-4 h-4 text-emerald-600" />
            <span>Prospek Sales & Calon Pelanggan (Leads)</span>
          </h3>
          <div className="space-y-2">
            {leads.length === 0 ? (
              <p className="text-xs text-slate-400 py-4 text-center">Belum ada leads aktif.</p>
            ) : (
              leads.map((ld) => (
                <div key={ld.id} className="p-3 rounded-lg bg-slate-50 border border-slate-200/60 flex justify-between items-center">
                  <div>
                    <h4 className="text-xs font-bold text-slate-900">{ld.name}</h4>
                    <p className="text-[11px] text-slate-500">{ld.phone} • Minat: {ld.package_interest}</p>
                  </div>
                  <span className="badge badge-inactive">{ld.status}</span>
                </div>
              ))
            )}
          </div>
        </div>
      </div>

      {/* Adjust Modal */}
      {adjustModalOpen && selectedItem && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm">
          <div className="bg-white rounded-2xl w-full max-w-sm p-6 border border-slate-200 shadow-2xl">
            <h3 className="text-base font-bold text-slate-900 mb-0.5">Penyesuaian Stok Material</h3>
            <p className="text-xs text-slate-500 mb-4">{selectedItem.name} (Stok Saat Ini: {selectedItem.stock} {selectedItem.unit})</p>

            <form onSubmit={handleAdjustStock} className="space-y-3">
              <div>
                <label className="block text-xs font-semibold text-slate-700 mb-1">
                  Jumlah Penambahan / Pengurangan
                </label>
                <input
                  type="number"
                  required
                  value={adjustment}
                  onChange={(e) => setAdjustment(e.target.value)}
                  placeholder="Contoh: +10 atau -5"
                  className="input-field text-xs py-2 font-mono"
                />
              </div>

              <div className="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="button" onClick={() => setAdjustModalOpen(false)} className="btn-secondary text-xs py-2 px-3">Batal</button>
                <button type="submit" className="btn-primary text-xs py-2 px-4">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
