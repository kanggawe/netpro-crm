<?php
/**
 * Automated Verification Script: Dual Database Multi-Server Architecture
 * NETPRO CRM & Official FreeRADIUS 3.0 Engine
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/models.php';

echo "=================================================================\n";
echo "🗄️ UJI VERIFIKASI MULTI-DATABASE ARCHITECTURE (APP DB & RADIUS DB)\n";
echo "=================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertDb($condition, $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "  ✅ [PASS] $testName\n";
        $passCount++;
    } else {
        echo "  ❌ [FAIL] $testName\n";
        $failCount++;
    }
}

// 1. Uji Koneksi App DB ($pdo / get_db())
echo "1. INTEGRITAS KONEKSI DATABASE APLIKASI (APP CRM DB)\n";
$appDb = get_db();
assertDb($appDb instanceof PDO, "Koneksi \$pdo (App DB) aktif dan terinisialisasi");

$packages = Package::all();
assertDb(count($packages) > 0, "Tabel packages dapat diakses dari App DB (" . count($packages) . " Paket)");

$invoices = Invoice::all();
assertDb(is_array($invoices), "Tabel invoices dapat diakses dari App DB (" . count($invoices) . " Invoices)");

// 2. Uji Koneksi FreeRADIUS DB ($pdoRadius / get_radius_db())
echo "\n2. INTEGRITAS KONEKSI DEDICATED FREERADIUS DATABASE\n";
$radDb = get_radius_db();
assertDb($radDb instanceof PDO, "Koneksi \$pdoRadius (FreeRADIUS DB) aktif dan terinisialisasi");

// 3. Uji Model Skema Resmi FreeRADIUS (radcheck, radacct, nas)
echo "\n3. VALIDASI SKEMA RESMI FREERADIUS (radcheck, radacct, nas)\n";
$radCheckList = RadCheck::all();
assertDb(is_array($radCheckList), "Tabel resmi FreeRADIUS 'radcheck' berhasil diakses via model RadCheck");

$nasList = RadiusNas::all();
assertDb(is_array($nasList), "Tabel 'nas' / 'radius_nas' berhasil diakses via model RadiusNas (" . count($nasList) . " NAS Router)");

$radUsers = RadiusUser::all();
assertDb(is_array($radUsers), "Tabel 'radius_users' berhasil diakses via model RadiusUser (" . count($radUsers) . " Akun)");

// 4. Uji Sinkronisasi Kredensial Pelanggan ke FreeRADIUS
echo "\n4. UJI SINKRONISASI KREDENSIAL APP DB -> FREERADIUS DB\n";
$custTestName = "Testing MultiDB " . rand(100, 999);
$custTestUser = "test_multidb_" . rand(1000, 9999);
$custTestPass = "Secret123!";

$custId = Customer::create([
    'name' => $custTestName,
    'nik' => '3201123456780001',
    'phone' => '081299990000',
    'address' => 'Jl. Multidb Test No. 1',
    'package_id' => 2,
    'ppn_scheme' => 'include',
    'billing_type' => 'postpaid',
    'pppoe_user' => $custTestUser,
    'pppoe_password' => $custTestPass
]);

assertDb($custId > 0, "Pelanggan baru berhasil disimpan di App DB (ID: $custId)");

// Trigger sync ke Radius
$refreshedRadUsers = RadiusUser::all();
$foundInRadius = false;
foreach ($refreshedRadUsers as $ru) {
    if ($ru['username'] === $custTestUser) {
        $foundInRadius = true;
        break;
    }
}
assertDb($foundInRadius, "Kredensial PPPoE ($custTestUser) otomatis tersinkronisasi ke FreeRADIUS DB");

echo "\n=================================================================\n";
echo "📊 HASIL PENGUJIAN DUAL DATABASE: $passCount LULUS, $failCount GAGAL\n";
echo "=================================================================\n";
