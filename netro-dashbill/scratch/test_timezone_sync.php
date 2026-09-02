<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TIMEZONE & PREPAID GRACE TEST ===\n\n";

echo "PHP Time: " . date('Y-m-d H:i:s') . " (" . date_default_timezone_get() . ")\n";
global $pdo;
$pgTime = $pdo->query("SELECT CURRENT_TIMESTAMP")->fetchColumn();
echo "PostgreSQL Time: " . $pgTime . "\n\n";

// Register customer Muhammad Iqbal
$id = Customer::create([
    'name' => 'Muhammad Iqbal Test',
    'nik' => '3275060912830006',
    'phone' => '081223221752',
    'address' => 'Jalan Merdeka',
    'package_id' => 1,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'prepaid'
]);

// Query customers via Customer::all() (which runs auto-isolir check)
$customers = Customer::all();
$cust = Customer::find($id);

echo "Customer Registered:\n";
echo "ID: {$cust['id']} | Name: {$cust['name']}\n";
echo "Billing Type: {$cust['billing_type']}\n";
echo "Status: {$cust['status']}\n";
echo "Expired At: {$cust['expired_at']}\n";

$secondsLeft = strtotime($cust['expired_at']) - time();
$minutesLeft = ceil($secondsLeft / 60);
echo "Sisa Waktu Grace: $minutesLeft Menit (Seconds Left: $secondsLeft)\n";

if ($cust['status'] === 'active' && $minutesLeft > 0) {
    echo "✓ SUCCESS: Status is ACTIVE with $minutesLeft minutes grace period remaining!\n";
} else {
    echo "✗ FAILED: Status is {$cust['status']}!\n";
}
