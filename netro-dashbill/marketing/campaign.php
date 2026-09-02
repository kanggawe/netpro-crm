<?php
require_once __DIR__ . '/../config/app.php';
$page_title = "Broadcast Promo (WA / Email)";
$page_subtitle = "Alat pengiriman pesan promosi massal ke database prospek.";
$active_menu = "m-marketing";
require_once __DIR__ . '/../includes/header.php';
?>

<div id="view-marketing-campaign" class="view-panel space-y-6" data-title="Broadcast Promo (WA / Email)" data-subtitle="Alat pengiriman pesan promosi massal ke database prospek.">
            
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm max-w-2xl mx-auto space-y-5 text-xs">
                <h3 class="font-bold text-slate-900 text-sm">Tool WhatsApp Campaign Broadcast</h3>
                <div class="space-y-3">
                    <div><label class="font-semibold text-slate-700 block mb-1">Nama Campaign</label><input type="text" value="Promo Diskon Pendaftaran Gratis Juni" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2 font-bold"></div>
                    <div><label class="font-semibold text-slate-700 block mb-1">Target Segmen</label><select class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2"><option>Prospek Un-registered (520 Leads)</option><option>Pelanggan Home Basic (Upgrade Offer)</option></select></div>
                    <div><label class="font-semibold text-slate-700 block mb-1">Draft Pesan WhatsApp</label><textarea rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2">Halo Kak! Dapatkan GRATIS Biaya Pasang Baru WiFi 50Mbps bulan ini...</textarea></div>
                    <button onclick="triggerToast('Campaign Sent', 'Broadcast WA dikirim ke 520 nomor prospek!')" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-lg shadow">💬 Kirim WhatsApp Campaign Broadcast</button>
                </div>
            </div>
        
        </div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
