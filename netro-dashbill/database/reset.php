<?php
/**
 * Database Reset Script for BILL-DASH / NETPRO ISP OS
 * Empties all tables (customers, invoices, tickets, outages, etc.)
 * Keeps only default system configuration, default packages, and superadmin account.
 */

require_once __DIR__ . '/../config/database.php';

global $pdo, $activeDriver;
echo "Starting Full Database Reset (Emptying all operational data - Driver: $activeDriver)...\n";

try {
    // List of all operational and transactional tables to wipe clean
    $operationalTables = [
        'invoices',
        'tickets',
        'complaints',
        'noc_outages',
        'surveys',
        'work_orders',
        'customers',
        'radius_users',
        'radius_vouchers',
        'radius_nas',
        'radius_profiles',
        'inventory_items',
        'employees',
        'leaves',
        'attendances',
        'cash_transactions',
        'leads',
        'journal_entries',
        'tax_records',
        'opex_expenses',
        'kpi_indicators',
        'performance_reviews',
        'salary_components',
        'payroll_records',
        'bonus_claims',
        'backups',
        'audit_logs'
    ];

    if ($activeDriver === 'pgsql') {
        $tableList = implode(', ', $operationalTables);
        $pdo->exec("TRUNCATE TABLE $tableList CASCADE;");

        // Reset primary key sequences for truncated tables
        foreach ($operationalTables as $tbl) {
            try {
                $pdo->exec("SELECT setval('{$tbl}_id_seq', 1, false);");
            } catch (Throwable $seqErr) {
                // Ignore if table has no serial sequence
            }
        }
    } else {
        $pdo->exec("PRAGMA foreign_keys = OFF;");
        foreach ($operationalTables as $tbl) {
            $pdo->exec("DELETE FROM $tbl;");
            $pdo->exec("DELETE FROM sqlite_sequence WHERE name='$tbl';");
        }
        $pdo->exec("PRAGMA foreign_keys = ON;");
    }

    echo "✓ All operational tables truncated/emptied successfully!\n";

    // Ensure default packages exist
    $pdo->exec("DELETE FROM packages;");
    $stmtPkg = $pdo->prepare("INSERT INTO packages (id, name, speed_mbps, price, default_ppn_mode, category) VALUES (?, ?, ?, ?, ?, ?)");
    $defaultPackages = [
        [1, 'Home Lite 10M', 10, 100000.00, 'include', 'home'],
        [2, 'Home Basic 20M', 20, 150000.00, 'include', 'home'],
        [3, 'Home Premium 50M', 50, 250000.00, 'include', 'home'],
        [4, 'SOHO Pro 100M', 100, 450000.00, 'include', 'business']
    ];
    foreach ($defaultPackages as $pkg) {
        $stmtPkg->execute($pkg);
    }
    echo "✓ Default packages restored (4 packages).\n";

    // Ensure Superadmin user exists for login
    $adminPass = password_hash('admin123', PASSWORD_BCRYPT);
    $pdo->exec("DELETE FROM users WHERE username = 'superadmin' OR id = 1;");
    $stmtUser = $pdo->prepare("INSERT INTO users (id, username, full_name, name, email, phone, division, password, role, status) VALUES 
        (1, 'superadmin', 'Super Administrator Utama', 'Super Administrator', 'superadmin@netpro.co.id', '0812-9876-5432', 'NOC & Core Infrastructure', ?, 'super admin', 'active')");
    $stmtUser->execute([$adminPass]);

    if ($activeDriver === 'pgsql') {
        $pdo->exec("SELECT setval('packages_id_seq', COALESCE((SELECT MAX(id) FROM packages), 0) + 1, false);");
        $pdo->exec("SELECT setval('users_id_seq', COALESCE((SELECT MAX(id) FROM users), 0) + 1, false);");
    }

    echo "✓ Default Superadmin account ready (username: superadmin / pass: admin123).\n";

    echo "\nSUCCESS: Database has been completely reset to zero data!\n";

} catch (Exception $e) {
    echo "❌ ERROR Resetting Database: " . $e->getMessage() . "\n";
}
