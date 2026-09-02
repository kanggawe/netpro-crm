<?php
/**
 * Automated Verification Script: End-to-End Business Flow Simulation
 * NETPRO CRM (ISP Management OS)
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=======================================================\n";
echo "MEMULAI PENGUJIAN END-TO-END BUSINESS FLOW NETPRO CRM\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($condition, $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "  [PASS] $testName\n";
        $passCount++;
    } else {
        echo "  [FAIL] $testName\n";
        $failCount++;
    }
}

// 1. STEP 1: Registrasi Pelanggan Baru (FTTH Pascabayar & Prabayar)
echo "1. STEP 1: REGISTRASI PELANGGAN BARU\n";
$testName = "Test User E2E " . rand(100, 999);
$testNik = "327501" . rand(1000000000, 9999999999);
$testPhone = "0812" . rand(10000000, 99999999);
$pppoeUser = "e2e_user_" . rand(100, 999);
$pppoePass = "secret123";

$custId = Customer::create([
    'name' => $testName,
    'nik' => $testNik,
    'phone' => $testPhone,
    'email' => 'e2e@netpro.co.id',
    'address' => 'Jl. Boulevard Fiber No. 88, Bekasi',
    'gps_lat' => -6.2891,
    'gps_lng' => 106.9182,
    'package_id' => 3, // Home Premium 50M
    'ppn_scheme' => 'include',
    'auth_method' => 'pppoe',
    'pppoe_user' => $pppoeUser,
    'pppoe_password' => $pppoePass,
    'billing_type' => 'postpaid',
    'billing_cycle_type' => 'anniversary'
]);

assertTest($custId > 0, "Pelanggan baru berhasil disimpan dengan ID: $custId");

$cust = Customer::find($custId);
assertTest($cust && $cust['status'] === 'inactive', "Status awal pelanggan adalah 'inactive' (menunggu instalasi/aktivasi)");

// Cek sinkronisasi ke tabel radius_users
$stmtRad = $pdo->prepare("SELECT * FROM radius_users WHERE username = ?");
$stmtRad->execute([$pppoeUser]);
$radUser = $stmtRad->fetch();
assertTest($radUser && $radUser['username'] === $pppoeUser, "Kredensial PPPoE otomatis terdaftar di radius_users");

// 2. STEP 2: Work Order & BAST
echo "\n2. STEP 2: PENERBITAN WO & UJI REDAMAN BAST\n";
$woCreated = WorkOrder::create([
    'customer_name' => $testName,
    'package_name' => 'Home Premium 50M',
    'ont_type' => 'ZTE F660 Dualband',
    'ont_sn' => 'ZTEG' . rand(10000000, 99999999),
    'tech_name' => 'Teknisi E2E',
    'odp_port' => 'ODP-JTW-04/16 (Port 5)',
    'attenuation' => '-17.8 dBm'
]);
assertTest($woCreated, "Work Order & Lembar BAST berhasil diterbitkan");

// 3. STEP 3: Aktivasi Pelanggan (Set Online)
echo "\n3. STEP 3: AKTIVASI LAYANAN & SET ONLINE\n";
$onlineOk = Customer::setOnline($custId);
assertTest($onlineOk, "Customer::setOnline() berhasil dipanggil");

$custActive = Customer::find($custId);
assertTest($custActive && $custActive['status'] === 'active', "Status pelanggan berubah menjadi 'active'");

$stmtRad->execute([$pppoeUser]);
$radActive = $stmtRad->fetch();
assertTest($radActive && $radActive['status'] === 'CONNECTED', "Status di radius_users berubah menjadi 'CONNECTED'");

// Cek penerbitan invoice perdana
$stmtInv = $pdo->prepare("SELECT * FROM invoices WHERE customer_id = ? ORDER BY id DESC LIMIT 1");
$stmtInv->execute([$custId]);
$firstInv = $stmtInv->fetch();
assertTest($firstInv && $firstInv['total_amount'] > 0, "Invoice perdana otomatis diterbitkan (Nomor: " . ($firstInv['invoice_no'] ?? '') . ", Total: " . ($firstInv['total_amount'] ?? 0) . ")");

// 4. STEP 4: Pelunasan Pembayaran & Auto-Jurnal Keuangan PSAK
echo "\n4. STEP 4: PEMBAYARAN TAGIHAN & AUTO-JURNAL PSAK\n";
if ($firstInv) {
    $payOk = Invoice::pay($firstInv['id'], 'Transfer Bank BCA', 'TRX-E2E-' . rand(1000, 9999));
    assertTest($payOk, "Invoice::pay() berhasil dieksekusi");

    $invPaid = Invoice::find($firstInv['id']);
    assertTest($invPaid && $invPaid['status'] === 'paid', "Status invoice berubah menjadi 'paid'");

    // Cek catatan arus kas
    $stmtCash = $pdo->prepare("SELECT * FROM cash_transactions WHERE description LIKE ? ORDER BY id DESC LIMIT 1");
    $stmtCash->execute(['%' . $firstInv['invoice_no'] . '%']);
    $cashEntry = $stmtCash->fetch();
    assertTest($cashEntry && $cashEntry['amount'] == $firstInv['total_amount'], "Buku Kas mencatat penerimaan uang masuk Rp " . number_format($firstInv['total_amount']));

    // Cek jurnal umum seimbang (Debit == Kredit)
    $stmtJrn = $pdo->prepare("SELECT * FROM journal_entries WHERE description LIKE ?");
    $stmtJrn->execute(['%' . $firstInv['invoice_no'] . '%']);
    $jrnEntries = $stmtJrn->fetchAll();
    
    $totalDebit = 0;
    $totalCredit = 0;
    foreach ($jrnEntries as $je) {
        $totalDebit += floatval($je['debit']);
        $totalCredit += floatval($je['credit']);
    }
    assertTest(count($jrnEntries) >= 2, "Tercatat " . count($jrnEntries) . " baris Jurnal Umum otomatis");
    assertTest(abs($totalDebit - $totalCredit) < 0.01 && $totalDebit > 0, "Jurnal Umum SEIMBANG (Total Debit: Rp " . number_format($totalDebit) . " == Total Kredit: Rp " . number_format($totalCredit) . ")");
}

// 5. STEP 5: Tiket Gangguan & Integrasi Telemetri
echo "\n5. STEP 5: PENGADUAN TIKET GANGGUAN\n";
$ticketCreated = Ticket::create([
    'customer_id' => $custId,
    'category' => 'Keluhan Kecepatan Slow',
    'priority' => 'HIGH',
    'assigned_tech' => 'Teknisi E2E NOC',
    'sla_minutes' => 60,
    'status' => 'OPEN'
]);
assertTest($ticketCreated, "Tiket gangguan berhasil dibuka untuk ID pelanggan $custId");

$tck = Ticket::all();
$foundTicket = false;
foreach ($tck as $item) {
    if ($item['customer_id'] == $custId) {
        $foundTicket = true;
        break;
    }
}
assertTest($foundTicket, "Tiket terhubung langsung dengan nama & profil pelanggan");

echo "\n=======================================================\n";
echo "HASIL PENGUJIAN: $passCount LULUS, $failCount GAGAL\n";
echo "=======================================================\n";
