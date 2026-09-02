<?php
require_once __DIR__ . '/../config/database.php';
echo "Active Driver: " . $activeDriver . PHP_EOL;

$tables = ['customers', 'invoices', 'tickets', 'noc_outages', 'packages', 'users'];
foreach ($tables as $t) {
    try {
        $c = $pdo->query("SELECT count(*) FROM $t")->fetchColumn();
        echo "$t: $c\n";
    } catch (Exception $e) {
        echo "$t: Error - " . $e->getMessage() . "\n";
    }
}
