<?php
/**
 * Automated Verification Script: RBAC Matrix Multi-Role Access Simulator
 * NETPRO CRM (ISP Management OS)
 */
require_once __DIR__ . '/../config/app.php';

echo "=======================================================\n";
echo "🛡️ UJI VERIFIKASI ROLE-BASED ACCESS CONTROL (RBAC)\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertRbac($condition, $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "  ✅ [PASS] $testName\n";
        $passCount++;
    } else {
        echo "  ❌ [FAIL] $testName\n";
        $failCount++;
    }
}

// 1. Super Administrator
$superAdmin = ['role' => 'Super Administrator'];
assertRbac(can_access('m-dashboard', $superAdmin), "Super Admin: Mengakses Dashboard (ALLOW)");
assertRbac(can_access('m-finance', $superAdmin), "Super Admin: Mengakses Keuangan & Akuntansi (ALLOW)");
assertRbac(can_access('m-pengaturan', $superAdmin), "Super Admin: Mengakses Pengaturan Sistem (ALLOW)");
assertRbac(can_access('m-radius', $superAdmin), "Super Admin: Mengakses RADIUS Server (ALLOW)");

// 2. Administrator
$admin = ['role' => 'Administrator'];
assertRbac(can_access('m-dashboard', $admin), "Administrator: Mengakses Dashboard (ALLOW)");
assertRbac(can_access('m-crm', $admin), "Administrator: Mengakses CRM & Pelanggan (ALLOW)");
assertRbac(can_access('m-finance', $admin), "Administrator: Mengakses Keuangan & Akuntansi (ALLOW)");

// 3. Teknisi Lapangan (Field Tech)
$teknisi = ['role' => 'Teknisi Lapangan'];
assertRbac(can_access('m-crm', $teknisi), "Teknisi: Mengakses CRM/WO/BAST (ALLOW)");
assertRbac(can_access('m-noc', $teknisi), "Teknisi: Mengakses NOC (ALLOW)");
assertRbac(can_access('m-tickets', $teknisi), "Teknisi: Mengakses Tiket Insiden (ALLOW)");
assertRbac(!can_access('m-finance', $teknisi), "Teknisi: Mengakses Keuangan & Akuntansi (DENIED)");
assertRbac(!can_access('m-pengaturan', $teknisi), "Teknisi: Mengakses Pengaturan Sistem (DENIED)");

// 4. Finance & Kasir
$finance = ['role' => 'Finance & Kasir'];
assertRbac(can_access('m-billing', $finance), "Finance: Mengakses Billing & Tagihan (ALLOW)");
assertRbac(can_access('m-finance', $finance), "Finance: Mengakses Keuangan & Kas (ALLOW)");
assertRbac(can_access('m-payroll', $finance), "Finance: Mengakses Payroll (ALLOW)");
assertRbac(!can_access('m-radius', $finance), "Finance: Mengakses RADIUS Server (DENIED)");
assertRbac(!can_access('m-noc', $finance), "Finance: Mengakses Topologi NOC (DENIED)");
assertRbac(!can_access('m-pengaturan', $finance), "Finance: Mengakses Pengaturan Sistem (DENIED)");

// 5. NOC & Network Engineer
$noc = ['role' => 'NOC & Network Engineer'];
assertRbac(can_access('m-noc', $noc), "NOC: Mengakses NOC Monitoring (ALLOW)");
assertRbac(can_access('m-radius', $noc), "NOC: Mengakses RADIUS Engine (ALLOW)");
assertRbac(can_access('m-tickets', $noc), "NOC: Mengakses Tiket Gangguan (ALLOW)");
assertRbac(!can_access('m-finance', $noc), "NOC: Mengakses Modul Keuangan (DENIED)");
assertRbac(!can_access('m-pengaturan', $noc), "NOC: Mengakses Pengaturan Sistem (DENIED)");

// 6. Sales & Marketing
$sales = ['role' => 'Sales & Marketing'];
assertRbac(can_access('m-marketing', $sales), "Sales: Mengakses Marketing & Leads (ALLOW)");
assertRbac(can_access('m-crm', $sales), "Sales: Mengakses Katalog Paket CRM (ALLOW)");
assertRbac(!can_access('m-finance', $sales), "Sales: Mengakses Modul Keuangan (DENIED)");
assertRbac(!can_access('m-noc', $sales), "Sales: Mengakses Modul NOC Core (DENIED)");

echo "\n=======================================================\n";
echo "📊 HASIL VERIFIKASI RBAC: $passCount LULUS, $failCount GAGAL\n";
echo "=======================================================\n";
