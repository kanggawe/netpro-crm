<?php
require_once __DIR__ . '/../config/app.php';

try {
    // Add billing_type and expired_at to customers table if not exists
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    if ($driver === 'pgsql') {
        $pdo->exec("ALTER TABLE customers ADD COLUMN IF NOT EXISTS billing_type VARCHAR(20) DEFAULT 'postpaid'");
        $pdo->exec("ALTER TABLE customers ADD COLUMN IF NOT EXISTS expired_at TIMESTAMP WITH TIME ZONE NULL");
        $pdo->exec("ALTER TABLE invoices ADD COLUMN IF NOT EXISTS billing_type VARCHAR(20) DEFAULT 'postpaid'");
    } else {
        // SQLite
        $cols = $pdo->query("PRAGMA table_info(customers)")->fetchAll(PDO::FETCH_ASSOC);
        $colNames = array_column($cols, 'name');
        if (!in_array('billing_type', $colNames)) {
            $pdo->exec("ALTER TABLE customers ADD COLUMN billing_type VARCHAR(20) DEFAULT 'postpaid'");
        }
        if (!in_array('expired_at', $colNames)) {
            $pdo->exec("ALTER TABLE customers ADD COLUMN expired_at DATETIME NULL");
        }
        
        $invCols = $pdo->query("PRAGMA table_info(invoices)")->fetchAll(PDO::FETCH_ASSOC);
        $invColNames = array_column($invCols, 'name');
        if (!in_array('billing_type', $invColNames)) {
            $pdo->exec("ALTER TABLE invoices ADD COLUMN billing_type VARCHAR(20) DEFAULT 'postpaid'");
        }
    }
    
    echo "✓ Migration success: billing_type and expired_at columns ready on {$driver}!\n";
} catch (Exception $e) {
    echo "Error migrating: " . $e->getMessage() . "\n";
}
