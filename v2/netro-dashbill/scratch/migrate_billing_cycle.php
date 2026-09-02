<?php
require_once __DIR__ . '/../config/app.php';

try {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    if ($driver === 'pgsql') {
        $pdo->exec("ALTER TABLE customers ADD COLUMN IF NOT EXISTS billing_cycle_type VARCHAR(30) DEFAULT 'anniversary'");
        $pdo->exec("ALTER TABLE invoices ALTER COLUMN billing_period TYPE VARCHAR(150)");
    } else {
        $cols = $pdo->query("PRAGMA table_info(customers)")->fetchAll(PDO::FETCH_ASSOC);
        $colNames = array_column($cols, 'name');
        if (!in_array('billing_cycle_type', $colNames)) {
            $pdo->exec("ALTER TABLE customers ADD COLUMN billing_cycle_type VARCHAR(30) DEFAULT 'anniversary'");
        }
    }
    
    echo "✓ Migration success: billing_cycle_type column ready on {$driver}!\n";
} catch (Exception $e) {
    echo "Error migrating: " . $e->getMessage() . "\n";
}
