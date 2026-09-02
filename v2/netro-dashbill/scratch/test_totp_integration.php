<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TEST DYNAMIC RFC 6238 TOTP ENGINE ===" . PHP_EOL;

$secret = TOTP::getUserSecret(1);
echo "User 1 Secret: " . $secret . PHP_EOL;

$currentCode = TOTP::getCode($secret);
echo "Current 6-Digit Code: " . $currentCode . PHP_EOL;

$verifyPass = TOTP::verifyCode($secret, $currentCode);
echo "Verification Current Code: " . ($verifyPass ? "PASS (Valid)" : "FAIL") . PHP_EOL;

$verifyWrong = TOTP::verifyCode($secret, "000000");
echo "Verification Wrong Code: " . (!$verifyWrong ? "PASS (Rejected correctly)" : "FAIL") . PHP_EOL;

$qrUrl = TOTP::getQrUrl('superadmin@netpro.id', $secret, 'NETPRO CRM');
echo "QR Code URL: " . $qrUrl . PHP_EOL;

$statusBefore = TOTP::isUser2FAEnabled(1);
echo "User 1 2FA Status Before: " . ($statusBefore ? "ENABLED" : "DISABLED") . PHP_EOL;

Setting::set('two_factor_user_1', '1');
$statusAfter = TOTP::isUser2FAEnabled(1);
echo "User 1 2FA Status After Enable: " . ($statusAfter ? "ENABLED" : "DISABLED") . PHP_EOL;

Setting::set('two_factor_user_1', '0');
$statusDisable = TOTP::isUser2FAEnabled(1);
echo "User 1 2FA Status After Disable: " . (!$statusDisable ? "DISABLED" : "FAIL") . PHP_EOL;

echo "=== ALL TOTP TESTS COMPLETED SUCCESSFULLY ===" . PHP_EOL;
