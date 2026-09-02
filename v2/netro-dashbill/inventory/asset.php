<?php
require_once __DIR__ . '/../config/app.php';
$page_title = "Aset Perusahaan & Tools";
$page_subtitle = "Pencatatan alat ukur OTDR, Fusion Splicer, Tangga Fiber, Laptop & Kendaraan Ops.";
$active_menu = "m-inventory";
require_once __DIR__ . '/../includes/header.php';
?>

<div id="view-inventory-asset" class="view-panel space-y-6" data-title="Aset Perusahaan & Tools" data-subtitle="Pencatatan alat ukur OTDR, Fusion Splicer, Tangga Fiber, Laptop & Kendaraan Ops.">
            
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4 text-xs">
                <div class="flex justify-between items-center"><h3 class="font-bold text-slate-900 text-sm">Registrasi Aset Perusahaan & Tools Kerja</h3><button onclick="triggerToast('Tambah Aset', 'Form aset dibuka.')" class="bg-blue-600 text-white font-bold px-4 py-2 rounded-lg">+ Tambah Aset</button></div>
                <table class="w-full text-left">
                    <thead><tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold"><th class="py-3 px-4">Asset Tag</th><th class="py-3 px-4">Nama Alat / Aset</th><th class="py-3 px-4">Serial Number</th><th class="py-3 px-4">Penanggung Jawab</th><th class="py-3 px-4">Kondisi</th><th class="py-3 px-4 text-right">Aksi</th></tr></thead>
                    <tbody>
                        <tr class="border-b border-slate-50">
                            <td colspan="6" class="py-8 text-center text-slate-400 font-medium">Belum ada aset perusahaan & tools terdaftar di database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        
        </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
