<?php
require_once __DIR__ . '/../config/app.php';
$page_title = "Keluar & Masuk Barang";
$page_subtitle = "Log transaksi mutasi barang gudang dan pemakaian material oleh teknisi.";
$active_menu = "m-inventory";
require_once __DIR__ . '/../includes/header.php';
?>

<div id="view-inventory-stok" class="view-panel space-y-6" data-title="Keluar & Masuk Barang" data-subtitle="Log transaksi mutasi barang gudang dan pemakaian material oleh teknisi.">
            
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4 text-xs">
                <h3 class="font-bold text-slate-900 text-sm">Log Transaksi Mutasi Stok Material</h3>
                <table class="w-full text-left">
                    <thead><tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold"><th class="py-3 px-4">No Mutasi</th><th class="py-3 px-4">Nama Material</th><th class="py-3 px-4">Jenis Mutasi</th><th class="py-3 px-4">Jumlah</th><th class="py-3 px-4">Teknisi / Ref WO</th><th class="py-3 px-4 text-right">Waktu</th></tr></thead>
                    <tbody>
                        <tr class="border-b border-slate-50">
                            <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada transaksi mutasi stok material di database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        
        </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
