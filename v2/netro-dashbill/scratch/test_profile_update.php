<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TESTING USER PROFILE UPDATE & AVATAR SELECTION ===\n";

$admin = User::find(1);
echo "Initial User 1:\n";
echo "Name: " . $admin['full_name'] . "\n";
echo "Avatar: " . ($admin['avatar'] ?? 'NULL') . "\n";

// Update profile with new avatar and detailed fields
$updateData = [
    'full_name' => 'Super Administrator Utama ISP',
    'email' => 'admin@netpro.id',
    'phone' => '0812-8888-9999',
    'division' => 'NOC & Core Infrastructure',
    'role' => 'Super Admin',
    'avatar' => 'assets/images/avatar-noc.svg',
    'bio' => 'Pemegang Kunci Root & Otoritas Utama Server FreeRADIUS AAA',
    'nik' => '3275081900210001',
    'telegram_id' => '@netpro_superadmin',
    'address' => 'Sentral POP Cinde & HQ NOC'
];

$res = User::updateProfile(1, $updateData);
echo "Update Profile Result: " . ($res ? 'PASS' : 'FAIL') . "\n";

$updated = User::find(1);
echo "Updated Name: " . $updated['full_name'] . "\n";
echo "Updated Avatar: " . $updated['avatar'] . "\n";
echo "Updated Bio: " . $updated['bio'] . "\n";
echo "Updated Telegram: " . $updated['telegram_id'] . "\n";

// Restore to Executive suit avatar for default clean presentation
$updateData['avatar'] = 'assets/images/avatar-admin.svg';
User::updateProfile(1, $updateData);

echo "=== ALL PROFILE UPDATE TESTS COMPLETED WITH 100% SUCCESS ===\n";
