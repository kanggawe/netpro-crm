<?php
require_once __DIR__ . '/../config/app.php';
$page_title = "Target & Komisi Sales";
$page_subtitle = "Monitoring pencapaian target penjualan dan komisi tim marketing.";
$active_menu = "m-marketing";
require_once __DIR__ . '/../includes/header.php';
?>

<div id="view-marketing-sales" class="view-panel space-y-6" data-title="Target & Komisi Sales" data-subtitle="Monitoring pencapaian target penjualan dan komisi tim marketing.">
            
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 space-y-4 text-xs">
                <h3 class="font-bold text-slate-900 text-sm">Target vs Pencapaian Sales Marketing</h3>
                <table class="w-full text-left">
                    <thead><tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold"><th class="py-3 px-4">Nama Sales</th><th class="py-3 px-4">Target Deals</th><th class="py-3 px-4">Deals Closed</th><th class="py-3 px-4">Achievement %</th><th class="py-3 px-4">Komisi Earned</th></tr></thead>
                    <tbody>
                        <tr class="border-b border-slate-50">
                            <td colspan="5" class="py-8 text-center text-slate-400 font-medium">Belum ada data pencapaian sales di database.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        
        </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
