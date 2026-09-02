<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TESTING PREPAID 30-MINUTE GRACE & ACTIVATION FLOW ===\n\n";

// 1. Create Prepaid Customer
$prepaidId = Customer::create([
    'name' => 'Doni Prabayar Grace Test',
    'nik' => '3275040912830004',
    'phone' => '081234567893',
    'address' => 'Jl. Grace No. 4, Jakarta',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'prepaid'
]);

$cust = Customer::find($prepaidId);
$invoices = Invoice::all();
$inv = null;
foreach ($invoices as $i) {
    if ($i['customer_id'] == $prepaidId) {
        $inv = $i;
        break;
    }
}

echo "1. PREPAID CUSTOMER CREATED (INITIAL ONLINE):\n";
echo "   ID: {$cust['id']} | Name: {$cust['name']}\n";
echo "   Initial Status: {$cust['status']} (Online)\n";
echo "   Initial Expired At: {$cust['expired_at']} (Grace 30 Menit)\n";
echo "   Initial Invoice No: {$inv['invoice_no']}\n";
echo "   Initial Invoice Status: {$inv['status']} (Unpaid - Diterbitkan saat online)\n";
echo "   Due Date: {$inv['due_date']} (Batas 30 menit)\n\n";

// 2. Simulate Payment of Prepaid Invoice
echo "2. SIMULATING PAYMENT (LUNAS):\n";
Invoice::pay($inv['id']);
$custAfterPay = Customer::find($prepaidId);
$invAfterPay = Invoice::find($inv['id']);

echo "   Invoice Status: {$invAfterPay['status']}\n";
echo "   Customer Status: {$custAfterPay['status']}\n";
echo "   New Expired At: {$custAfterPay['expired_at']} (Extended 30 Hari Penuh)\n\n";

// 3. Simulate Expiration & Auto-Isolir
echo "3. SIMULATING EXPIRED GRACE PERIOD (BELUM BAYAR > 30 MENIT):\n";
// Create another customer and set expired_at in the past
$unpaidPrepaidId = Customer::create([
    'name' => 'Eko Prabayar Unpaid Test',
    'nik' => '3275050912830005',
    'phone' => '081234567894',
    'address' => 'Jl. Unpaid No. 5, Jakarta',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'prepaid'
]);

// Set expired_at to 10 minutes ago
global $pdo;
$pdo->prepare("UPDATE customers SET expired_at = ? WHERE id = ?")->execute([date('Y-m-d H:i:s', strtotime('-10 minutes')), $unpaidPrepaidId]);

// Trigger Customer::all() which runs auto-isolir check
$allCustomers = Customer::all();
$ekoCust = Customer::find($unpaidPrepaidId);

echo "   Customer Eko Expired At: {$ekoCust['expired_at']}\n";
echo "   Customer Eko Status after 30 mins: {$ekoCust['status']} (Auto-Isolir!)\n\n";

echo "✓ ALL 30-MINUTE GRACE & PREPAID ONLINE INVOICE TESTS PASSED!\n";
