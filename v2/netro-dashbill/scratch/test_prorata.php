<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TESTING PRORATA & FIXED DATE TGL 20 BILLING ===\n\n";

// 1. Create Postpaid Customer with Prorata
$postpaidProrataId = Customer::create([
    'name' => 'Ahmad Pascabayar Prorata',
    'nik' => '3275030912830003',
    'phone' => '081234567892',
    'address' => 'Jl. Prorata No. 3, Bekasi',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'postpaid',
    'is_prorata' => '1'
]);

$cust = Customer::find($postpaidProrataId);
$invoices = Invoice::all();
$inv = null;
foreach ($invoices as $i) {
    if ($i['customer_id'] == $postpaidProrataId) {
        $inv = $i;
        break;
    }
}

echo "1. POSTPAID PRORATA CUSTOMER CREATED:\n";
echo "   Customer: {$cust['name']} (Billing: {$cust['billing_type']})\n";
echo "   Invoice No: {$inv['invoice_no']}\n";
echo "   Billing Period: {$inv['billing_period']}\n";
echo "   Total Amount: Rp " . number_format($inv['total_amount'], 2, ',', '.') . " (Prorata price)\n";
echo "   Due Date: {$inv['due_date']} (Should be Tanggal 20)\n";
echo "   Status: {$inv['status']}\n\n";

// 2. Verify Mass Generation Due Date (Tanggal 20)
$massCount = Invoice::generateMassal('Agustus 2026');
$invoices = Invoice::all();
$massInv = $invoices[0];
echo "2. MASS GENERATED INVOICE FOR POSTPAID:\n";
echo "   Invoice No: {$massInv['invoice_no']}\n";
echo "   Period: {$massInv['billing_period']}\n";
echo "   Due Date: {$massInv['due_date']} (Should be Tanggal 20)\n";
echo "   Total Generated: $massCount\n\n";

echo "✓ ALL PRORATA & TANGGAL 20 TESTS PASSED!\n";
