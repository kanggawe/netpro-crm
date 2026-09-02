<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

login_user(['id' => 1, 'username' => 'superadmin', 'role' => 'administrator']);

$dashboards = [
    'dashboard/utama.php',
    'dashboard/customers.php',
    'dashboard/revenue.php',
    'dashboard/noc.php',
    'dashboard/tickets.php',
    'dashboard/overdue.php',
    'dashboard/hr.php'
];

$hasError = false;
foreach ($dashboards as $dash) {
    $_SERVER['REQUEST_URI'] = '/' . $dash;
    ob_start();
    include __DIR__ . '/../' . $dash;
    $html = ob_get_clean();

    $warnings = [];
    foreach (explode("\n", $html) as $line) {
        if (stripos($line, 'Warning:') !== false || stripos($line, 'Notice:') !== false || stripos($line, 'Fatal error') !== false) {
            $warnings[] = trim($line);
        }
    }

    if (empty($warnings)) {
        echo "[PASS] $dash (Size: " . strlen($html) . " bytes)" . PHP_EOL;
    } else {
        $hasError = true;
        echo "[FAIL] $dash" . PHP_EOL;
        foreach ($warnings as $w) {
            echo "   -> $w" . PHP_EOL;
        }
    }
}

if (!$hasError) {
    echo "\n=== ALL 7 DASHBOARDS RENDERED 100% PERFECTLY WITHOUT WARNINGS! ===" . PHP_EOL;
}
