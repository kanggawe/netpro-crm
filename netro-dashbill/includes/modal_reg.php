<?php
/**
 * Quick Customer Registration Modal with PPN & Leaflet Map
 */
require_once __DIR__ . '/../config/models.php';
$pkgList = Package::all();
?>
<div id="quickRegModal" class="fixed inset-0 bg-slate-950/65 z-[9999] flex items-center justify-center p-4 hidden backdrop-blur-md transition-all duration-300">
    <div class="bg-white rounded-3xl shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)] border border-slate-100 w-full max-w-xl overflow-hidden transform transition-all">
        <!-- Premium Modern Header -->
        <div class="p-6 pb-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-b from-slate-50/80 to-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm tracking-tight">Registrasi Pelanggan Baru</h3>
                    <p class="text-[11px] text-slate-400 font-medium">Input data langganan, profil PPPoE, dan skema PPN</p>
                </div>
            </div>
            <button onclick="hideQuickRegisterModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 transition flex items-center justify-center text-xs">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-4 text-xs max-h-[85vh] overflow-y-auto">
            <input type="hidden" name="action" value="create_customer">
            <input type="hidden" name="redirect" value="crm/daftar.php">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Budi Santoso" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium focus:border-blue-500">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">No WhatsApp</label>
                    <input type="tel" name="phone" required placeholder="08123456789" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-medium focus:border-blue-500">
                </div>
            </div>

            <!-- Paket & Skema PPN -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Paket Internet</label>
                    <select id="modalPkgSelect" name="package_id" onchange="calcModalPpn()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold">
                        <?php foreach ($pkgList as $p): ?>
                            <option value="<?= $p['id'] ?>" data-price="<?= $p['price'] ?>" <?= ($p['id'] == 2) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p['name']) ?> (<?= format_rupiah($p['price']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Skema PPN Tagihan</label>
                    <div class="grid grid-cols-2 gap-1.5 pt-0.5">
                        <label class="p-1.5 bg-slate-50 border border-blue-500 rounded flex items-center gap-1.5 font-bold text-[11px] text-blue-900 cursor-pointer">
                            <input type="radio" name="ppn_scheme" value="include" checked onchange="calcModalPpn()" class="accent-blue-600">
                            <span>Include</span>
                        </label>
                        <label class="p-1.5 bg-slate-50 border border-slate-200 rounded flex items-center gap-1.5 font-bold text-[11px] text-slate-700 cursor-pointer">
                            <input type="radio" name="ppn_scheme" value="exclude" onchange="calcModalPpn()" class="accent-blue-600">
                            <span>Exclude (+11%)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Live Calculation Preview -->
            <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl flex justify-between items-center text-[11px]">
                <span class="text-blue-900 font-medium" id="modalPpnDesc">DPP: Rp 225.225 | PPN: Rp 24.775</span>
                <strong class="text-blue-900 font-bold text-xs" id="modalTotalDesc">Total: Rp 250.000</strong>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Koordinat GPS Pemasangan</label>
                    <input type="text" name="gps_coords" value="-6.2891, 106.9182" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-mono font-bold text-blue-700">
                </div>
                <div>
                    <label class="font-semibold text-slate-700 block mb-1">Tipe Koneksi / Otentikasi</label>
                    <select id="modalAuthSelect" name="auth_method" onchange="toggleModalAuth()" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                        <option value="pppoe" selected>PPPoE Client</option>
                        <option value="dhcp">DHCP / IPoE Lease</option>
                        <option value="hotspot">Hotspot Voucher</option>
                        <option value="static">Static IP Gateway</option>
                    </select>
                </div>
            </div>

            <!-- PPPoE Credentials in Modal -->
            <div id="modalPppoeBox" class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl space-y-2">
                <div class="flex justify-between items-center">
                    <span class="font-bold text-blue-900 text-[11px]"><i class="fa-solid fa-key text-blue-600"></i> Akun PPPoE Dialer</span>
                    <span class="text-[10px] text-blue-600">Otomatis sync ke RADIUS</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-slate-600 block text-[10px] font-semibold mb-0.5">Username PPPoE</label>
                        <input type="text" name="pppoe_user" placeholder="Auto-generate" class="w-full bg-white border border-slate-200 rounded p-1.5 font-mono text-[11px]">
                    </div>
                    <div>
                        <label class="text-slate-600 block text-[10px] font-semibold mb-0.5">Password PPPoE</label>
                        <input type="text" name="pppoe_password" placeholder="Auto-generate" class="w-full bg-white border border-slate-200 rounded p-1.5 font-mono text-[11px]">
                    </div>
                </div>
            </div>

            <div>
                <label class="font-semibold text-slate-700 block mb-1">Alamat Lengkap Pemasangan</label>
                <textarea name="address" rows="2" placeholder="Nama Jalan, Blok, RT/RW, Kelurahan" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-100">
                <button type="button" onclick="hideQuickRegisterModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 hover:bg-slate-200 font-bold text-slate-700 transition">
                    Batal
                </button>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-file-invoice-dollar text-xs"></i> Simpan & Terbitkan Invoice
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function calcModalPpn() {
    var selectEl = document.getElementById('modalPkgSelect');
    var selectedOpt = selectEl.options[selectEl.selectedIndex];
    var val = parseFloat(selectedOpt.getAttribute('data-price')) || 250000;
    var isInc = document.querySelector('input[name="ppn_scheme"]:checked').value === 'include';
    var res = calculatePpn(val, isInc);
    document.getElementById('modalPpnDesc').innerText = "DPP: Rp " + res.dpp.toLocaleString('id-ID') + " | PPN: Rp " + res.ppn.toLocaleString('id-ID');
    document.getElementById('modalTotalDesc').innerText = "Total: Rp " + res.total.toLocaleString('id-ID');
}

function toggleModalAuth() {
    var sel = document.getElementById('modalAuthSelect');
    var box = document.getElementById('modalPppoeBox');
    if (box && sel) {
        if (sel.value === 'pppoe') {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }
}
</script>
