<?php
$_SERVER['REQUEST_URI'] = '/dashboard/customers.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

login_user(['id' => 1, 'username' => 'superadmin', 'role' => 'administrator']);

ob_start();
include __DIR__ . '/../dashboard/customers.php';
$html = ob_get_clean();

echo "Length: " . strlen($html) . PHP_EOL;
if (strpos($html, 'Undefined variable') === false && strpos($html, 'Warning:') === false) {
    echo "SUCCESS: No warnings or errors found in customers.php!" . PHP_EOL;
} else {
    echo "FAIL: Found warnings or errors in output!" . PHP_EOL;
    // Show lines containing Warning
    foreach (explode("\n", $html) as $line) {
        if (stripos($line, 'Warning') !== false || stripos($line, 'Notice') !== false) {
            echo "-> " . trim($line) . PHP_EOL;
        }
    }
}
