<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TESTING AUTO-SYNC CUSTOMER TO RADIUS USERS ===\n\n";

// 1. Create Customer
$id = Customer::create([
    'name' => 'Hendra Setiawan',
    'nik' => '3275090912830009',
    'phone' => '081234567897',
    'address' => 'Jl. Bulutangkis No. 9, Jakarta',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'billing_type' => 'postpaid',
    'pppoe_user' => '32750909-HENDRA',
    'pppoe_password' => 'secret123'
]);

$cust = Customer::find($id);
echo "1. CUSTOMER CREATED:\n";
echo "   ID: {$cust['id']} | Name: {$cust['name']}\n";
echo "   PPPoE User: {$cust['pppoe_user']}\n\n";

// 2. Check RADIUS Users
$radiusUsers = RadiusUser::all();
$found = null;
foreach ($radiusUsers as $ru) {
    if ($ru['username'] === '32750909-HENDRA') {
        $found = $ru;
        break;
    }
}

if ($found) {
    echo "2. RADIUS USER FOUND IN http://localhost:8000/radius/users.php:\n";
    echo "   Username: {$found['username']}\n";
    echo "   Password: {$found['password']}\n";
    echo "   Customer: {$found['customer_name']}\n";
    echo "   Profile: {$found['profile_name']}\n";
    echo "   IP Address: {$found['ip_address']}\n";
    echo "   NAS: {$found['nas_name']}\n";
    echo "   Status: {$found['status']}\n\n";
    echo "✓ SUCCESS: Customer is automatically registered in RADIUS Users table!\n";
} else {
    echo "✗ FAILED: Radius user not found!\n";
}

// 3. Test Customer Delete and Radius User Cleanup
Customer::delete($id);
$radiusAfterDelete = RadiusUser::all();
$foundAfter = false;
foreach ($radiusAfterDelete as $ru) {
    if ($ru['username'] === '32750909-HENDRA') {
        $foundAfter = true;
        break;
    }
}
if (!$foundAfter) {
    echo "✓ Radius user cleaned up after customer deletion!\n";
}
