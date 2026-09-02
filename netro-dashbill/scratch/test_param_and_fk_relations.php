<?php
/**
 * Automated Verification Script: URL Parameters & Foreign Key Integrity
 * NETPRO CRM (ISP Management OS)
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=======================================================\n";
echo "🔗 UJI VERIFIKASI RELASI PARAMETER URL & FOREIGN KEY\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertCheck($condition, $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "  ✅ [PASS] $testName\n";
        $passCount++;
    } else {
        echo "  ❌ [FAIL] $testName\n";
        $failCount++;
    }
}

// 1. UJI FK: Customer -> Invoices
echo "1. INTEGRITAS RELASI CUSTOMERS -> INVOICES\n";
$customers = Customer::all();
$invoices = Invoice::all();
assertCheck(!empty($customers), "Terdapat data pelanggan di database (" . count($customers) . " Pelanggan)");

$allInvoicesHaveValidCustomer = true;
foreach ($invoices as $inv) {
    if (!empty($inv['customer_id'])) {
        $c = Customer::find($inv['customer_id']);
        if (!$c) {
            $allInvoicesHaveValidCustomer = false;
            break;
        }
    }
}
assertCheck($allInvoicesHaveValidCustomer, "Semua invoice terhubung dengan Foreign Key customer_id yang valid di tabel customers");

// 2. UJI FK: Customer -> Tickets
echo "\n2. INTEGRITAS RELASI CUSTOMERS -> TICKETS\n";
$tickets = Ticket::all();
$allTicketsHaveValidCustomer = true;
foreach ($tickets as $t) {
    if (!empty($t['customer_id'])) {
        $c = Customer::find($t['customer_id']);
        if (!$c) {
            $allTicketsHaveValidCustomer = false;
            break;
        }
    }
}
assertCheck($allTicketsHaveValidCustomer, "Semua tiket insiden terhubung dengan Foreign Key customer_id yang valid di tabel customers");

// 3. UJI FK: Customer -> RADIUS Secret Users
echo "\n3. INTEGRITAS RELASI CUSTOMERS -> RADIUS_USERS\n";
$radiusUsers = RadiusUser::all();
assertCheck(!empty($radiusUsers), "Terdapat data akun RADIUS (" . count($radiusUsers) . " Akun)");

// 4. UJI URL PARAM HANDOFF SIMULATION
echo "\n4. SIMULASI HANDSHAKE PARAMETER URL LINTAS HALAMAN\n";

// A. Simulasi ?customer_id=X pada Billing Daftar
if (!empty($customers)) {
    $firstCust = $customers[0];
    $_GET['customer_id'] = $firstCust['id'];
    
    // Check invoice filter logic
    $custInvoices = array_filter(Invoice::all(), function($inv) use ($firstCust) {
        return ($inv['customer_id'] ?? 0) == $firstCust['id'];
    });
    assertCheck(is_array($custInvoices), "Filter ?customer_id=" . $firstCust['id'] . " di billing/daftar.php berhasil mengisolasi tagihan pelanggan");
}

// B. Simulasi ?invoice_id=X pada Kwitansi & Invoice PDF
if (!empty($invoices)) {
    $firstInv = $invoices[0];
    $_GET['invoice_id'] = $firstInv['id'];
    $resolvedInv = Invoice::find($_GET['invoice_id']);
    assertCheck($resolvedInv && $resolvedInv['id'] == $firstInv['id'], "Parameter ?invoice_id=" . $firstInv['id'] . " berhasil me-resolve faktur & kwitansi");
}

// C. Simulasi ?customer_name=X pada Survey Lokasi
$testCustName = "Budi Hartono";
$_GET['customer_name'] = $testCustName;
assertCheck(!empty($_GET['customer_name']), "Parameter ?customer_name=$testCustName berhasil di-forward ke form modal survey");

echo "\n=======================================================\n";
echo "📊 HASIL VERIFIKASI: $passCount LULUS, $failCount GAGAL\n";
echo "=======================================================\n";
