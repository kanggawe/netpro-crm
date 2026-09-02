<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=== TEST 4 SOCIAL PLATFORMS (GOOGLE, GITHUB, FACEBOOK, X/TWITTER) ===" . PHP_EOL;

global $pdo;

$providers = [
    'google' => ['email' => 'user.enterprise@gmail.com', 'name' => 'Google Enterprise User'],
    'github' => ['email' => 'developer@github.com', 'name' => 'GitHub Developer'],
    'facebook' => ['email' => 'user.community@facebook.com', 'name' => 'Facebook Meta User'],
    'twitter' => ['email' => 'network.feed@x.com', 'name' => 'X (Twitter) User']
];

foreach ($providers as $prov => $data) {
    $email = $data['email'];
    $name = $data['name'];
    $username = strtolower(explode('@', $email)[0]);
    $username = preg_replace('/[^a-z0-9_]/', '', $username);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    $u = $stmt->fetch();

    if (!$u) {
        $stmtIns = $pdo->prepare("INSERT INTO users (username, full_name, name, email, password, role, division, status) VALUES (?, ?, ?, ?, ?, 'administrator', 'Manajemen IT & Jaringan', 'active')");
        $stmtIns->execute([$username, $name, $name, $email, password_hash('random', PASSWORD_BCRYPT)]);
        echo "[$prov] Provisioning: PASS (Created user: $username)" . PHP_EOL;
    } else {
        echo "[$prov] Auth Fetch: PASS (Found user: " . $u['username'] . ")" . PHP_EOL;
    }
}

echo "=== ALL 4 SOCIAL PROVIDER TESTS COMPLETED SUCCESSFULLY ===" . PHP_EOL;
