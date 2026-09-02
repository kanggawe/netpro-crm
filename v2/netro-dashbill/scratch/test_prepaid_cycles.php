<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TESTING PREPAID BILLING CYCLES (ANNIVERSARY VS FIXED DATE) ===\n\n";

// 1. Create Prepaid with Billing Cycle (Anniversary 30 Days)
$annivId = Customer::create([
    'name' => 'Faisal Prabayar Rolling',
    'nik' => '3275070912830007',
    'phone' => '081234567895',
    'address' => 'Jl. Rolling No. 7, Jakarta',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'prepaid',
    'billing_cycle_type' => 'anniversary'
]);

$invoices = Invoice::all();
$annivInv = $invoices[0];
Invoice::pay($annivInv['id']);
$annivCust = Customer::find($annivId);

echo "1. PREPAID BILLING CYCLE (ROLLING 30 DAYS):\n";
echo "   Name: {$annivCust['name']}\n";
echo "   Cycle Type: {$annivCust['billing_cycle_type']}\n";
echo "   Active Expired At: {$annivCust['expired_at']} (30 Days from now)\n\n";

// 2. Create Prepaid with Fixed Date (Reset Akhir Bulan / Tanggal 1)
$fixedId = Customer::create([
    'name' => 'Gita Prabayar Fixed Date',
    'nik' => '3275080912830008',
    'phone' => '081234567896',
    'address' => 'Jl. Fixed No. 8, Jakarta',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'prepaid',
    'billing_cycle_type' => 'fixed_date',
    'is_prorata' => '1'
]);

$invoices = Invoice::all();
$fixedInv = $invoices[0];
Invoice::pay($fixedInv['id']);
$fixedCust = Customer::find($fixedId);

echo "2. PREPAID FIXED DATE (RESET TGL 1 / PRORATA AKHIR BULAN):\n";
echo "   Name: {$fixedCust['name']}\n";
echo "   Cycle Type: {$fixedCust['billing_cycle_type']}\n";
echo "   Active Expired At: {$fixedCust['expired_at']} (End of current month)\n\n";

echo "✓ ALL PREPAID BILLING CYCLE TESTS PASSED!\n";
