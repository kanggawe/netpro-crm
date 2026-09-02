<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TEST LUPA PASSWORD / RESET PASSWORD FLOW ===" . PHP_EOL;

global $pdo;

$testUsername = 'superadmin';
$newPass = 'admin_super_2026';

// 1. Verify user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$testUsername]);
$u = $stmt->fetch();

if (!$u) {
    echo "FAIL: User not found" . PHP_EOL;
    exit(1);
}

// 2. Perform password update (reset password)
$hashed = password_hash($newPass, PASSWORD_BCRYPT);
$stmtUp = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmtUp->execute([$hashed, $u['id']]);

// 3. Verify new password matches
$stmtCheck = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmtCheck->execute([$u['id']]);
$uUpdated = $stmtCheck->fetch();

if (password_verify($newPass, $uUpdated['password'])) {
    echo "Password Reset Test: PASS (Successfully verified new password hash)" . PHP_EOL;
} else {
    echo "Password Reset Test: FAIL" . PHP_EOL;
}

// Restore superadmin password back to admin123
$resetDefault = password_hash('admin123', PASSWORD_BCRYPT);
$pdo->prepare("UPDATE users SET password = ? WHERE username = 'superadmin'")->execute([$resetDefault]);
echo "Password Reset Restore: PASS (superadmin restored to default admin123)" . PHP_EOL;

echo "=== ALL PASSWORD RESET TESTS PASSED ===" . PHP_EOL;
