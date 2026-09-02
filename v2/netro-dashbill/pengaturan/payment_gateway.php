<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

$page_title = "Payment Gateways & Pengaturan Potongan Biaya";
$page_subtitle = "Konfigurasi terpisah tiap Payment Gateway (Midtrans, Xendit, Tripay, Duitku) dan skema potongan biaya admin.";
$active_menu = "m-pengaturan";
require_once __DIR__ . '/../includes/header.php';

// Midtrans
$midtransActive = Setting::get('midtrans_active', '1');
$midtransEnv = Setting::get('midtrans_env', 'production');
$midtransMerchantId = Setting::get('midtrans_merchant_id', 'G1928812');
$midtransServerKey = Setting::get('midtrans_server_key', 'SB-Mid-server-9912099318821');
$midtransClientKey = Setting::get('midtrans_client_key', 'SB-Mid-client-8819200192931');
$midtransFeeVa = Setting::get('midtrans_fee_va', '4000');
$midtransFeeQris = Setting::get('midtrans_fee_qris', '0.7');
$midtransFeeBorne = Setting::get('midtrans_fee_borne', 'customer');

// Xendit
$xenditActive = Setting::get('xendit_active', '0');
$xenditEnv = Setting::get('xendit_env', 'production');
$xenditSecretKey = Setting::get('xendit_secret_key', 'xnd_production_88219001928');
$xenditPublicKey = Setting::get('xendit_public_key', 'xnd_public_production_1928812');
$xenditWebhookToken = Setting::get('xendit_webhook_token', 'xnd_token_99120');
$xenditFeeVa = Setting::get('xendit_fee_va', '4500');
$xenditFeeQris = Setting::get('xendit_fee_qris', '0.7');
$xenditFeeBorne = Setting::get('xendit_fee_borne', 'customer');

// Tripay
$tripayActive = Setting::get('tripay_active', '0');
$tripayEnv = Setting::get('tripay_env', 'production');
$tripayMerchantCode = Setting::get('tripay_merchant_code', 'T19288');
$tripayApiKey = Setting::get('tripay_api_key', 'DEV-API-TRIPAY-99120');
$tripayPrivateKey = Setting::get('tripay_private_key', 'DEV-PRIV-TRIPAY-88129');
$tripayFeeFlat = Setting::get('tripay_fee_flat', '3500');
$tripayFeeBorne = Setting::get('tripay_fee_borne', 'customer');

// Duitku
$duitkuActive = Setting::get('duitku_active', '0');
$duitkuEnv = Setting::get('duitku_env', 'production');
$duitkuMerchantCode = Setting::get('duitku_merchant_code', 'D19822');
$duitkuApiKey = Setting::get('duitku_api_key', 'DUITKU-API-KEY-88129');
$duitkuFeeFlat = Setting::get('duitku_fee_flat', '3000');
$duitkuFeeBorne = Setting::get('duitku_fee_borne', 'customer');

// Manual Bank & Static QRIS
$bankBcaNo = Setting::get('bank_bca_no', '883-091-2881');
$bankBcaName = Setting::get('bank_bca_name', 'PT NETPRO TELEKOMUNIKASI');
$bankMandiriNo = Setting::get('bank_mandiri_no', '124-000-99812-34');
$bankMandiriName = Setting::get('bank_mandiri_name', 'PT NETPRO TELEKOMUNIKASI');
$bankBriNo = Setting::get('bank_bri_no', '0341-01-000123-30-5');
$bankBriName = Setting::get('bank_bri_name', 'PT NETPRO TELEKOMUNIKASI');
$qrisMerchantName = Setting::get('qris_merchant_name', 'NETPRO FIBER INTERNET');
$qrisNmid = Setting::get('qris_nmid', 'ID1020091823912');

// Global Fee Scheme
$globalFeeScheme = Setting::get('global_fee_scheme', 'surcharge'); // surcharge vs absorbed

$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg === 'saved'): ?>
    <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl flex items-center gap-2 text-xs font-bold max-w-5xl mx-auto">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        Konfigurasi Payment Gateway & Skema Potongan Biaya Admin berhasil disimpan ke database!
    </div>
<?php endif; ?>

<div class="space-y-6 text-xs max-w-5xl mx-auto">
    <!-- Top 3 Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Gateway Aktif Utama</span>
                <strong class="text-2xl font-bold text-blue-600">Midtrans Snap</strong>
                <span class="text-emerald-600 font-bold block mt-0.5">● Auto-Callback Live</span>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-credit-card"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Skema Potongan Biaya</span>
                <strong class="text-2xl font-bold text-indigo-600"><?= $globalFeeScheme === 'surcharge' ? 'Beban Pelanggan' : 'Dipotong ISP' ?></strong>
                <span class="text-indigo-600 font-medium block mt-0.5">MDR QRIS 0.7% • VA Rp 4.000</span>
            </div>
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-percent"></i></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex justify-between items-center">
            <div>
                <span class="text-slate-400 font-semibold uppercase text-[10px] block">Webhook Notification URL</span>
                <strong class="text-xs font-mono font-bold text-slate-800 block truncate max-w-[180px]">/api/payment_callback.php</strong>
                <span class="text-slate-400 block mt-0.5">Instant Reconciliation</span>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl"><i class="fa-solid fa-bolt"></i></div>
        </div>
    </div>

    <!-- Interactive Gateway Tab Navigation -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Tab Headers -->
        <div class="flex overflow-x-auto border-b border-slate-200 bg-slate-50/70 p-2 gap-2 text-xs font-bold custom-scrollbar">
            <button type="button" onclick="switchPgTab('tab-midtrans')" id="btn-tab-midtrans" class="pg-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 bg-blue-600 text-white shadow-sm">
                <i class="fa-solid fa-bolt"></i> Midtrans
            </button>
            <button type="button" onclick="switchPgTab('tab-xendit')" id="btn-tab-xendit" class="pg-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 text-slate-600 hover:bg-slate-200/60">
                <i class="fa-solid fa-building-columns"></i> Xendit
            </button>
            <button type="button" onclick="switchPgTab('tab-tripay')" id="btn-tab-tripay" class="pg-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 text-slate-600 hover:bg-slate-200/60">
                <i class="fa-solid fa-wallet"></i> Tripay
            </button>
            <button type="button" onclick="switchPgTab('tab-duitku')" id="btn-tab-duitku" class="pg-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 text-slate-600 hover:bg-slate-200/60">
                <i class="fa-solid fa-coins"></i> Duitku
            </button>
            <button type="button" onclick="switchPgTab('tab-manual')" id="btn-tab-manual" class="pg-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 text-slate-600 hover:bg-slate-200/60">
                <i class="fa-solid fa-qrcode"></i> Transfer & QRIS Statis
            </button>
            <button type="button" onclick="switchPgTab('tab-fee-matrix')" id="btn-tab-fee-matrix" class="pg-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 text-slate-600 hover:bg-slate-200/60">
                <i class="fa-solid fa-calculator"></i> Skema Potongan Biaya (MDR)
            </button>
        </div>

        <form action="<?= base_url('api/handler.php') ?>" method="POST" class="p-6 space-y-6">
            <input type="hidden" name="action" value="save_settings">
            <input type="hidden" name="redirect" value="pengaturan/payment_gateway.php">

            <!-- TAB 1: MIDTRANS -->
            <div id="tab-midtrans" class="pg-tab-content space-y-5">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-600"></span> Midtrans Snap & Core Payment Gateway
                        </h4>
                        <p class="text-slate-400">Gateway pembayaran multi-channel otomatis (QRIS Dinamis, BCA/Mandiri/BNI/BRI VA, GoPay, ShopeePay).</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="font-semibold text-slate-700">Status Gateway:</label>
                        <select name="midtrans_active" class="bg-slate-50 border border-slate-200 rounded-lg p-1.5 font-bold text-slate-800">
                            <option value="1" <?= $midtransActive === '1' ? 'selected' : '' ?>>AKTIF</option>
                            <option value="0" <?= $midtransActive === '0' ? 'selected' : '' ?>>NONAKTIF</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Environment Mode</label>
                        <select name="midtrans_env" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                            <option value="production" <?= $midtransEnv === 'production' ? 'selected' : '' ?>>Production (Live Transaksi Riil)</option>
                            <option value="sandbox" <?= $midtransEnv === 'sandbox' ? 'selected' : '' ?>>Sandbox (Mode Simulasi / Testing)</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Merchant ID Midtrans</label>
                        <input type="text" name="midtrans_merchant_id" value="<?= htmlspecialchars($midtransMerchantId) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Server Key (Secret)</label>
                        <input type="password" name="midtrans_server_key" value="<?= htmlspecialchars($midtransServerKey) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-blue-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Client Key (Public)</label>
                        <input type="text" name="midtrans_client_key" value="<?= htmlspecialchars($midtransClientKey) ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                </div>

                <div class="p-4 bg-blue-50/60 rounded-xl border border-blue-100 space-y-3">
                    <h5 class="font-bold text-blue-900 text-xs uppercase">Potongan Biaya Admin Transaksi Midtrans:</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Biaya VA Bank (Flat Rp)</label>
                            <input type="number" name="midtrans_fee_va" value="<?= htmlspecialchars($midtransFeeVa) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-blue-600">
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">MDR QRIS (%)</label>
                            <input type="text" name="midtrans_fee_qris" value="<?= htmlspecialchars($midtransFeeQris) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-emerald-600">
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Beban Potongan Biaya</label>
                            <select name="midtrans_fee_borne" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                                <option value="customer" <?= $midtransFeeBorne === 'customer' ? 'selected' : '' ?>>Ditanggung Pelanggan (Surcharge)</option>
                                <option value="merchant" <?= $midtransFeeBorne === 'merchant' ? 'selected' : '' ?>>Dipotong dari Pendapatan ISP</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: XENDIT -->
            <div id="tab-xendit" class="pg-tab-content space-y-5 hidden">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-indigo-600"></span> Xendit Payment Infrastructure
                        </h4>
                        <p class="text-slate-400">Penyedia pembayaran Virtual Account perbankan instan dan e-Wallet.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="font-semibold text-slate-700">Status Gateway:</label>
                        <select name="xendit_active" class="bg-slate-50 border border-slate-200 rounded-lg p-1.5 font-bold text-slate-800">
                            <option value="1" <?= $xenditActive === '1' ? 'selected' : '' ?>>AKTIF</option>
                            <option value="0" <?= $xenditActive === '0' ? 'selected' : '' ?>>NONAKTIF</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Environment Mode</label>
                        <select name="xendit_env" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-bold">
                            <option value="production" <?= $xenditEnv === 'production' ? 'selected' : '' ?>>Production (Live)</option>
                            <option value="development" <?= $xenditEnv === 'development' ? 'selected' : '' ?>>Development / Test</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Webhook Callback Verification Token</label>
                        <input type="text" name="xendit_webhook_token" value="<?= htmlspecialchars($xenditWebhookToken) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Secret API Key (Server)</label>
                        <input type="password" name="xendit_secret_key" value="<?= htmlspecialchars($xenditSecretKey) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-indigo-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Public Key (Client)</label>
                        <input type="text" name="xendit_public_key" value="<?= htmlspecialchars($xenditPublicKey) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                </div>

                <div class="p-4 bg-indigo-50/60 rounded-xl border border-indigo-100 space-y-3">
                    <h5 class="font-bold text-indigo-900 text-xs uppercase">Potongan Biaya Admin Transaksi Xendit:</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Biaya VA Bank (Flat Rp)</label>
                            <input type="number" name="xendit_fee_va" value="<?= htmlspecialchars($xenditFeeVa) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-indigo-600">
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">MDR QRIS (%)</label>
                            <input type="text" name="xendit_fee_qris" value="<?= htmlspecialchars($xenditFeeQris) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-emerald-600">
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Beban Potongan Biaya</label>
                            <select name="xendit_fee_borne" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                                <option value="customer" <?= $xenditFeeBorne === 'customer' ? 'selected' : '' ?>>Ditanggung Pelanggan</option>
                                <option value="merchant" <?= $xenditFeeBorne === 'merchant' ? 'selected' : '' ?>>Dipotong dari Pendapatan ISP</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: TRIPAY -->
            <div id="tab-tripay" class="pg-tab-content space-y-5 hidden">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-emerald-600"></span> Tripay Payment Gateway Channel
                        </h4>
                        <p class="text-slate-400">Gateway dengan biaya terjangkau untuk pembayaran via Alfamart/Indomaret & VA.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="font-semibold text-slate-700">Status Gateway:</label>
                        <select name="tripay_active" class="bg-slate-50 border border-slate-200 rounded-lg p-1.5 font-bold text-slate-800">
                            <option value="1" <?= $tripayActive === '1' ? 'selected' : '' ?>>AKTIF</option>
                            <option value="0" <?= $tripayActive === '0' ? 'selected' : '' ?>>NONAKTIF</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Kode Merchant Tripay</label>
                        <input type="text" name="tripay_merchant_code" value="<?= htmlspecialchars($tripayMerchantCode) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">API Key</label>
                        <input type="password" name="tripay_api_key" value="<?= htmlspecialchars($tripayApiKey) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-emerald-600">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Private Key</label>
                        <input type="password" name="tripay_private_key" value="<?= htmlspecialchars($tripayPrivateKey) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                </div>

                <div class="p-4 bg-emerald-50/60 rounded-xl border border-emerald-100 space-y-3">
                    <h5 class="font-bold text-emerald-900 text-xs uppercase">Potongan Biaya Admin Transaksi Tripay:</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Biaya Admin Flat per Transaksi (Rp)</label>
                            <input type="number" name="tripay_fee_flat" value="<?= htmlspecialchars($tripayFeeFlat) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-emerald-600">
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Beban Potongan Biaya</label>
                            <select name="tripay_fee_borne" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                                <option value="customer" <?= $tripayFeeBorne === 'customer' ? 'selected' : '' ?>>Ditanggung Pelanggan (Surcharge)</option>
                                <option value="merchant" <?= $tripayFeeBorne === 'merchant' ? 'selected' : '' ?>>Dipotong dari Pendapatan ISP</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: DUITKU -->
            <div id="tab-duitku" class="pg-tab-content space-y-5 hidden">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span> Duitku Payment Gateway
                        </h4>
                        <p class="text-slate-400">Integrasi pembayaran online dengan settlement harian otomatis.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="font-semibold text-slate-700">Status Gateway:</label>
                        <select name="duitku_active" class="bg-slate-50 border border-slate-200 rounded-lg p-1.5 font-bold text-slate-800">
                            <option value="1" <?= $duitkuActive === '1' ? 'selected' : '' ?>>AKTIF</option>
                            <option value="0" <?= $duitkuActive === '0' ? 'selected' : '' ?>>NONAKTIF</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Merchant Code Duitku</label>
                        <input type="text" name="duitku_merchant_code" value="<?= htmlspecialchars($duitkuMerchantCode) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold">
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">API Key Duitku</label>
                        <input type="password" name="duitku_api_key" value="<?= htmlspecialchars($duitkuApiKey) ?>" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 font-mono font-bold text-amber-600">
                    </div>
                </div>

                <div class="p-4 bg-amber-50/60 rounded-xl border border-amber-100 space-y-3">
                    <h5 class="font-bold text-amber-900 text-xs uppercase">Potongan Biaya Admin Transaksi Duitku:</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Biaya Admin Flat per Transaksi (Rp)</label>
                            <input type="number" name="duitku_fee_flat" value="<?= htmlspecialchars($duitkuFeeFlat) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-amber-600">
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Beban Potongan Biaya</label>
                            <select name="duitku_fee_borne" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                                <option value="customer" <?= $duitkuFeeBorne === 'customer' ? 'selected' : '' ?>>Ditanggung Pelanggan (Surcharge)</option>
                                <option value="merchant" <?= $duitkuFeeBorne === 'merchant' ? 'selected' : '' ?>>Dipotong dari Pendapatan ISP</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: MANUAL BANK & STATIC QRIS -->
            <div id="tab-manual" class="pg-tab-content space-y-5 hidden">
                <div class="border-b border-slate-100 pb-3">
                    <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-qrcode text-purple-600"></i> Rekening Bank Penerima & QRIS Merchant Statis
                    </h4>
                    <p class="text-slate-400">Rincian nomor rekening resmi yang tertera pada Invoice tagihan untuk transfer manual.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <strong class="text-blue-700 font-bold block flex items-center gap-1.5">
                            <i class="fa-solid fa-building-columns"></i> 1. Bank Central Asia (BCA)
                        </strong>
                        <div>
                            <label class="text-slate-500 block text-[10px]">Nomor Rekening BCA</label>
                            <input type="text" name="bank_bca_no" value="<?= htmlspecialchars($bankBcaNo) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="text-slate-500 block text-[10px]">Atas Nama Rekening</label>
                            <input type="text" name="bank_bca_name" value="<?= htmlspecialchars($bankBcaName) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <strong class="text-amber-700 font-bold block flex items-center gap-1.5">
                            <i class="fa-solid fa-building-columns"></i> 2. Bank Mandiri
                        </strong>
                        <div>
                            <label class="text-slate-500 block text-[10px]">Nomor Rekening Mandiri</label>
                            <input type="text" name="bank_mandiri_no" value="<?= htmlspecialchars($bankMandiriNo) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="text-slate-500 block text-[10px]">Atas Nama Rekening</label>
                            <input type="text" name="bank_mandiri_name" value="<?= htmlspecialchars($bankMandiriName) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <strong class="text-blue-600 font-bold block flex items-center gap-1.5">
                            <i class="fa-solid fa-building-columns"></i> 3. Bank BRI
                        </strong>
                        <div>
                            <label class="text-slate-500 block text-[10px]">Nomor Rekening BRI</label>
                            <input type="text" name="bank_bri_no" value="<?= htmlspecialchars($bankBriNo) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="text-slate-500 block text-[10px]">Atas Nama Rekening</label>
                            <input type="text" name="bank_bri_name" value="<?= htmlspecialchars($bankBriName) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                        </div>
                    </div>

                    <div class="p-4 bg-purple-50/60 rounded-xl border border-purple-100 space-y-3">
                        <strong class="text-purple-700 font-bold block flex items-center gap-1.5">
                            <i class="fa-solid fa-qrcode"></i> 4. QRIS Statis Merchant (NMID)
                        </strong>
                        <div>
                            <label class="text-slate-500 block text-[10px]">National Merchant ID (NMID)</label>
                            <input type="text" name="qris_nmid" value="<?= htmlspecialchars($qrisNmid) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-mono font-bold text-purple-700">
                        </div>
                        <div>
                            <label class="text-slate-500 block text-[10px]">Nama Merchant Terdaftar di ASPI</label>
                            <input type="text" name="qris_merchant_name" value="<?= htmlspecialchars($qrisMerchantName) ?>" class="w-full bg-white border border-slate-200 rounded-lg p-2 font-bold text-slate-800">
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: FEE MATRIX & SCHEME -->
            <div id="tab-fee-matrix" class="pg-tab-content space-y-5 hidden">
                <div class="border-b border-slate-100 pb-3">
                    <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-scale-balanced text-indigo-600"></i> Matriks Kebijakan Potongan Biaya Admin & MDR
                    </h4>
                    <p class="text-slate-400">Aturan pembebanan selisih biaya perbankan pada saat kasir atau pelanggan melakukan pembayaran.</p>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                    <label class="font-bold text-slate-800 block text-xs">Kebijakan Global Pembebanan Biaya Admin:</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="p-3 bg-white border border-slate-200 rounded-xl flex items-start gap-3 cursor-pointer hover:border-blue-500 transition">
                            <input type="radio" name="global_fee_scheme" value="surcharge" <?= $globalFeeScheme === 'surcharge' ? 'checked' : '' ?> class="mt-1 text-blue-600">
                            <div>
                                <strong class="text-slate-900 font-bold block">Biaya Dibebankan ke Pelanggan (Surcharge)</strong>
                                <span class="text-[11px] text-slate-500 leading-relaxed">Contoh: Tagihan Rp 250.000 + Biaya VA Rp 4.000 = Total dibayar pelanggan **Rp 254.000**. ISP menerima bersih Rp 250.000.</span>
                            </div>
                        </label>

                        <label class="p-3 bg-white border border-slate-200 rounded-xl flex items-start gap-3 cursor-pointer hover:border-blue-500 transition">
                            <input type="radio" name="global_fee_scheme" value="absorbed" <?= $globalFeeScheme === 'absorbed' ? 'checked' : '' ?> class="mt-1 text-blue-600">
                            <div>
                                <strong class="text-slate-900 font-bold block">Biaya Ditanggung ISP (Merchant Absorbed)</strong>
                                <span class="text-[11px] text-slate-500 leading-relaxed">Contoh: Tagihan Rp 250.000, pelanggan bayar tetap **Rp 250.000**. Pendapatan ISP dipotong biaya gateway Rp 4.000 menjadi Rp 246.000.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 border-b border-slate-100 font-semibold">
                                <th class="py-2.5 px-3">Metode Pembayaran</th>
                                <th class="py-2.5 px-3">Gateway Penyedia</th>
                                <th class="py-2.5 px-3 font-mono text-center">Tarif MDR / Biaya</th>
                                <th class="py-2.5 px-3">Beban Biaya</th>
                                <th class="py-2.5 px-3 font-mono text-right">Settlement Dana</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-slate-50">
                                <td class="py-2.5 px-3 font-bold text-slate-800">QRIS Nasional (All Wallet & Bank)</td>
                                <td class="py-2.5 px-3 text-slate-600">Midtrans / Xendit</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-center text-emerald-600">0.70%</td>
                                <td class="py-2.5 px-3"><span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Pelanggan</span></td>
                                <td class="py-2.5 px-3 font-mono text-right text-slate-600">H+1 Realtime</td>
                            </tr>
                            <tr class="border-b border-slate-50">
                                <td class="py-2.5 px-3 font-bold text-slate-800">BCA Virtual Account (VA)</td>
                                <td class="py-2.5 px-3 text-slate-600">Midtrans Snap</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-center text-blue-600">Rp 4.000 / Trx</td>
                                <td class="py-2.5 px-3"><span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Pelanggan</span></td>
                                <td class="py-2.5 px-3 font-mono text-right text-slate-600">Instan (Detik)</td>
                            </tr>
                            <tr class="border-b border-slate-50">
                                <td class="py-2.5 px-3 font-bold text-slate-800">Mandiri / BRI / BNI VA</td>
                                <td class="py-2.5 px-3 text-slate-600">Midtrans / Tripay</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-center text-indigo-600">Rp 3.500 - Rp 4.000</td>
                                <td class="py-2.5 px-3"><span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Pelanggan</span></td>
                                <td class="py-2.5 px-3 font-mono text-right text-slate-600">Instan (Detik)</td>
                            </tr>
                            <tr class="border-b border-slate-50">
                                <td class="py-2.5 px-3 font-bold text-slate-800">Alfamart / Indomaret Gerai</td>
                                <td class="py-2.5 px-3 text-slate-600">Tripay / Duitku</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-center text-amber-600">Rp 5.000 / Trx</td>
                                <td class="py-2.5 px-3"><span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded text-[10px]">Pelanggan</span></td>
                                <td class="py-2.5 px-3 font-mono text-right text-slate-600">H+1 Kerja</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Global Save Button -->
            <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                <span class="text-slate-400 text-[11px]">Seluruh konfigurasi tersimpan aman ke database <code>settings</code>.</span>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Konfigurasi Payment Gateway
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function switchPgTab(tabId) {
    // Hide all tab contents
    document.querySelectorAll('.pg-tab-content').forEach(function(el) {
        el.classList.add('hidden');
    });
    // Remove active class from buttons
    document.querySelectorAll('.pg-tab-btn').forEach(function(btn) {
        btn.className = "pg-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 text-slate-600 hover:bg-slate-200/60";
    });

    // Show target content
    var target = document.getElementById(tabId);
    if (target) {
        target.classList.remove('hidden');
    }
    // Highlight target button
    var btn = document.getElementById('btn-' + tabId);
    if (btn) {
        btn.className = "pg-tab-btn px-4 py-2.5 rounded-xl transition flex items-center gap-2 bg-blue-600 text-white shadow-sm";
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
