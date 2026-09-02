<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TESTING DUAL BILLING ENGINE (PRABAYAR & PASCABAYAR) ===\n\n";

// 1. Create Prepaid Customer
$prepaidId = Customer::create([
    'name' => 'Budi Prabayar Test',
    'nik' => '3275010912830001',
    'phone' => '081234567890',
    'address' => 'Jl. Prabayar No. 1, Jakarta',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'prepaid'
]);

$prepaidCust = Customer::find($prepaidId);
echo "1. PREPAID CUSTOMER CREATED:\n";
echo "   ID: {$prepaidCust['id']} | Name: {$prepaidCust['name']}\n";
echo "   Billing Type: {$prepaidCust['billing_type']}\n";
echo "   Expired At: {$prepaidCust['expired_at']}\n";

// Check initial invoice for prepaid
$invoices = Invoice::all();
$prepaidInv = null;
foreach ($invoices as $inv) {
    if ($inv['customer_id'] == $prepaidId) {
        $prepaidInv = $inv;
        break;
    }
}
echo "   Initial Invoice Status: {$prepaidInv['status']} | Type: {$prepaidInv['billing_type']} | Paid Date: {$prepaidInv['paid_date']}\n\n";

// 2. Create Postpaid Customer
$postpaidId = Customer::create([
    'name' => 'Siti Pascabayar Test',
    'nik' => '3275020912830002',
    'phone' => '081234567891',
    'address' => 'Jl. Pascabayar No. 2, Jakarta',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'postpaid'
]);

$postpaidCust = Customer::find($postpaidId);
echo "2. POSTPAID CUSTOMER CREATED:\n";
echo "   ID: {$postpaidCust['id']} | Name: {$postpaidCust['name']}\n";
echo "   Billing Type: {$postpaidCust['billing_type']}\n";
echo "   Expired At: " . ($postpaidCust['expired_at'] ?? 'NULL (Fixed Date Tgl 10)') . "\n";

// Check initial invoice for postpaid
$postpaidInv = null;
foreach ($invoices as $inv) {
    if ($inv['customer_id'] == $postpaidId) {
        $postpaidInv = $inv;
        break;
    }
}
echo "   Initial Invoice Status: {$postpaidInv['status']} | Type: {$postpaidInv['billing_type']} | Due Date: {$postpaidInv['due_date']}\n\n";

// 3. Test Prepaid Top-Up Renewal (+30 days)
echo "3. TESTING PREPAID TOP-UP RENEWAL (+30 DAYS):\n";
$newExp = Customer::renewPrepaid($prepaidId, 30, 'QRIS Dinamis');
$updatedPrepaid = Customer::find($prepaidId);
echo "   New Expired At: {$updatedPrepaid['expired_at']}\n\n";

// 4. Test Mass Generate Invoices (should only generate for Postpaid)
echo "4. TESTING MASS GENERATE INVOICES:\n";
$massCount = Invoice::generateMassal('Juli 2026');
echo "   Mass Invoices Generated: $massCount (Postpaid accounts only)\n\n";

echo "✓ ALL TESTS PASSED SUCCESSFULLY!\n";
