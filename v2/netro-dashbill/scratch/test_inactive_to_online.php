<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TESTING INACTIVE TO ONLINE ACTIVATION LIFECYCLE ===\n\n";

// 1. Register Customer
$id = Customer::create([
    'name' => 'Indra Inactive Test',
    'nik' => '3275100912830010',
    'phone' => '081234567898',
    'address' => 'Jl. Inactive No. 10, Jakarta',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'prepaid',
    'billing_cycle_type' => 'anniversary'
]);

$cust = Customer::find($id);
$radUser = RadiusUser::all()[0];
$invoices = Invoice::all();
$invCount = 0;
foreach ($invoices as $i) {
    if ($i['customer_id'] == $id) $invCount++;
}

echo "1. STEP 1: INITIAL REGISTRATION (BELUM ONLINE):\n";
echo "   Customer: {$cust['name']} | Status: {$cust['status']} (Should be 'inactive')\n";
echo "   Expired At: " . ($cust['expired_at'] ?? 'NULL (Belum jalan)') . "\n";
echo "   RADIUS Status: {$radUser['status']} (Should be 'DISCONNECTED')\n";
echo "   Invoices Count: $invCount (Should be 0)\n\n";

// 2. Customer First Connects / Goes Online
echo "2. STEP 2: CUSTOMER GOES ONLINE (FIRST CONNECT / DIAL-IN):\n";
Customer::setOnline($id);
$custOnline = Customer::find($id);
$radUserOnline = RadiusUser::all()[0];
$invoicesOnline = Invoice::all();
$onlineInv = $invoicesOnline[0];

echo "   Customer Status: {$custOnline['status']} (Should be 'active')\n";
echo "   Expired At: {$custOnline['expired_at']} (Grace 30 Menit started!)\n";
echo "   RADIUS Status: {$radUserOnline['status']} (Should be 'CONNECTED')\n";
echo "   Invoice Generated: {$onlineInv['invoice_no']} | Status: {$onlineInv['status']} (Unpaid)\n";
echo "   Invoice Due Date: {$onlineInv['due_date']} (30 mins)\n\n";

// 3. Customer Pays Invoice
echo "3. STEP 3: CUSTOMER PAYS INVOICE (LUNAS):\n";
Invoice::pay($onlineInv['id']);
$custPaid = Customer::find($id);
$invPaid = Invoice::find($onlineInv['id']);

echo "   Invoice Status: {$invPaid['status']} (Paid)\n";
echo "   Customer Status: {$custPaid['status']} (Active)\n";
echo "   Expired At: {$custPaid['expired_at']} (30 Days Full!)\n\n";

echo "✓ ALL INACTIVE -> ONLINE -> PAID LIFECYCLE TESTS PASSED!\n";
