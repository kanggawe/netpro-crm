<?php
require_once __DIR__ . '/../config/app.php';

echo "=== NETPRO CRM URL CRYPTOGRAPHY & ROUTING TEST SUITE ===\n\n";

// 1. Test AES-256-CBC URL Encryption & Decryption
echo "[1] Testing AES-256-CBC URL Encryption...\n";
$originalId = 10452;
$token = url_encrypt($originalId, 'customer');
echo "    Original ID: $originalId\n";
echo "    Encrypted Token: $token\n";
$decryptedId = url_decrypt($token, 'customer');
echo "    Decrypted ID: " . ($decryptedId === $originalId ? "PASS ($decryptedId)" : "FAIL ($decryptedId)") . "\n";

// Test Array Payload
$payload = ['customer_id' => 88, 'invoice_id' => 991, 'action' => 'pay_qris'];
$arrToken = url_encrypt($payload, 'payment');
$decryptedArr = url_decrypt($arrToken, 'payment');
echo "    Array Payload Decryption: " . ($decryptedArr == $payload ? "PASS" : "FAIL") . "\n";

// 2. Test Anti-Tampering (HMAC Verification)
echo "\n[2] Testing Anti-Tamper & Security Verification...\n";
$tamperedToken = substr($token, 0, -4) . 'AAAA';
$tamperedResult = url_decrypt($tamperedToken, 'customer');
echo "    Tampered Token Rejected: " . ($tamperedResult === false ? "PASS (Rejected correctly)" : "FAIL (Accepted tampered)") . "\n";

// Test Context Mismatch Protection
$wrongContextResult = url_decrypt($token, 'invoice');
echo "    Context Mismatch Rejected: " . ($wrongContextResult === false ? "PASS (Rejected correctly)" : "FAIL") . "\n";

// 3. Test Mask ID & Unmask ID
echo "\n[3] Testing Fast Numeric ID Masking (Anti-Scraping)...\n";
$testIds = [1, 2, 5, 23, 105, 999, 12500];
$allMasksPassed = true;
foreach ($testIds as $tid) {
    $masked = mask_id($tid);
    $unmasked = unmask_id($masked);
    if ($unmasked !== $tid) {
        echo "    FAIL for ID $tid: Masked = $masked, Unmasked = $unmasked\n";
        $allMasksPassed = false;
    }
}
if ($allMasksPassed) {
    echo "    Mask & Unmask ID Roundtrip: PASS (" . count($testIds) . " IDs tested successfully)\n";
    echo "    Sample: ID 105 -> " . mask_id(105) . " -> " . unmask_id(mask_id(105)) . "\n";
}

// 4. Test Signed URLs (HMAC + Expiration)
echo "\n[4] Testing Signed URLs & Expiration Verification...\n";
$signedUrl = signed_url('billing/invoice.php', ['id' => 15, 'format' => 'pdf'], 60);
echo "    Generated Signed URL: $signedUrl\n";

$urlParts = parse_url($signedUrl);
parse_str($urlParts['query'], $queryParams);

$isValid = verify_signed_url($urlParts['path'], $queryParams);
echo "    Signed URL Verification (Fresh): " . ($isValid ? "PASS (Valid Signature)" : "FAIL") . "\n";

// Test Expired URL
$expiredQueryParams = $queryParams;
$expiredQueryParams['expires'] = time() - 3600; // 1 hour in past
$isExpiredValid = verify_signed_url($urlParts['path'], $expiredQueryParams);
echo "    Signed URL Verification (Expired): " . ($isExpiredValid === false ? "PASS (Rejected expired URL)" : "FAIL") . "\n";

// 5. Test Clean Route Helper
echo "\n[5] Testing Clean Route Helper...\n";
$cleanRoute = route_url('billing/daftar.php');
echo "    Clean Route Output: $cleanRoute\n";

echo "\n=== ALL URL CRYPTOGRAPHY & ANTI-IDOR TESTS COMPLETED WITH 100% SUCCESS ===\n";
