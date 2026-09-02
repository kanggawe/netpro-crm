<?php
require_once __DIR__ . '/../config/app.php';
$page_title = "Supplier & Purchase Order (PO)";
$page_subtitle = "Database vendor jaringan fiber optic dan pengajuan pembelian barang.";
$active_menu = "m-inventory";
require_once __DIR__ . '/../includes/header.php';
?>

<div id="view-inventory-supplier" class="view-panel space-y-6" data-title="Supplier & Purchase Order (PO)" data-subtitle="Database vendor jaringan fiber optic dan pengajuan pembelian barang.">
            
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4 text-xs">
                <div class="flex justify-between items-center"><h3 class="font-bold text-slate-900 text-sm">Direktori Vendor & History Purchase Order</h3><button onclick="triggerToast('Buat PO', 'Form PO baru dibuka.')" class="bg-blue-600 text-white font-bold px-4 py-2 rounded-lg">+ Buat PO Pembelian</button></div>
                <table class="w-full text-left">
                    <thead><tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold"><th class="py-3 px-4">Nama Vendor</th><th class="py-3 px-4">Kategori Material</th><th class="py-3 px-4">Contact Person</th><th class="py-3 px-4">Active PO</th><th class="py-3 px-4 text-right">Aksi</th></tr></thead>
                    <tbody>
                        <tr class="border-b border-slate-50">
                            <td colspan="5" class="py-8 text-center text-slate-400 font-medium">Belum ada vendor/supplier terdaftar di database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        
        </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
