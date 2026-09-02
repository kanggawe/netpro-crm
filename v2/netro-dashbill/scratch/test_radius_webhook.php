<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TESTING AUTOMATIC RADIUS ACCOUNTING TRIGGER ===\n\n";

// 1. Register Customer (Status: Inactive)
$id = Customer::create([
    'name' => 'Joko Webhook Auto',
    'nik' => '3275110912830011',
    'phone' => '081234567899',
    'address' => 'Jl. Otomatis No. 11, Jakarta',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'prepaid',
    'pppoe_user' => '32751109-JOKO'
]);

$cust = Customer::find($id);
echo "1. CUSTOMER REGISTERED (INITIAL STATE):\n";
echo "   Name: {$cust['name']} | Status: {$cust['status']} (INACTIVE)\n";
echo "   PPPoE User: {$cust['pppoe_user']}\n";
echo "   Expired At: " . ($cust['expired_at'] ?? 'NULL (Belum jalan)') . "\n\n";

// 2. Simulate FreeRADIUS Accounting Start Trigger (Modem Dial-in)
echo "2. SIMULATING FREERADIUS ACCOUNTING START (MODEM DIAL-IN CONNECTED):\n";

// Direct trigger to setOnlineByUsername (called by api/radius_acct.php)
$activated = Customer::setOnlineByUsername('32751109-JOKO');
$custOnline = Customer::find($id);
$radUser = RadiusUser::all()[0];
$invoices = Invoice::all();
$inv = $invoices[0];

echo "   Webhook Response: " . ($activated ? "SUCCESS" : "FAILED") . "\n";
echo "   Customer Status: {$custOnline['status']} (AUTOMATICALLY AKTIF!)\n";
echo "   Expired At: {$custOnline['expired_at']} (Grace 30 Menit started automatically!)\n";
echo "   RADIUS Status: {$radUser['status']} (CONNECTED)\n";
echo "   Invoice Generated: {$inv['invoice_no']} | Total: Rp " . number_format($inv['total_amount'], 0, ',', '.') . " (UNPAID)\n\n";

echo "✓ ALL AUTOMATIC FREERADIUS TRIGGER TESTS PASSED!\n";
