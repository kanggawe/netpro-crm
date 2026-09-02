<?php
/**
 * FreeRADIUS Accounting & MikroTik RouterOS Webhook Endpoint
 * Automatically triggers Customer & RADIUS status transitions on PPPoE Dial-in
 * 
 * Supported Parameters (via POST / JSON / GET):
 * - username / User-Name
 * - acct_status / status_type / Acct-Status-Type: Start | Stop | Interim-Update
 * - framed_ip / Framed-IP-Address
 * - nas_ip / NAS-IP-Address
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

// Accept JSON payload or standard POST/GET
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: $_REQUEST;

$username = $data['username'] ?? ($data['User-Name'] ?? ($data['user'] ?? ''));
$statusType = strtoupper($data['acct_status'] ?? ($data['status_type'] ?? ($data['Acct-Status-Type'] ?? 'START')));
$ipAddress = $data['framed_ip'] ?? ($data['Framed-IP-Address'] ?? ($data['ip'] ?? ''));

if (empty($username)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing username parameter'
    ]);
    exit;
}

global $pdo;

if ($statusType === 'START' || $statusType === '1') {
    // 1. Modem Dial-In (Accounting Start): Otomatis Aktifkan Pelanggan & Mulai Grace Period
    $activated = Customer::setOnlineByUsername($username);
    
    if ($activated) {
        AuditLog::log('RADIUS_DAEMON', 'ACCT_START', "PPPoE Accounting START diterima untuk username '$username'. Status otomatis AKTIF.");
        echo json_encode([
            'status' => 'success',
            'event' => 'ACCT_START',
            'username' => $username,
            'message' => 'Customer status successfully set to ACTIVE and grace/invoice started.'
        ]);
    } else {
        echo json_encode([
            'status' => 'ignored',
            'username' => $username,
            'message' => 'Username found in RADIUS but no matching customer record to activate.'
        ]);
    }
} elseif ($statusType === 'STOP' || $statusType === '2') {
    // 2. Modem Disconnect (Accounting Stop): Update radius_users to DISCONNECTED
    $stmt = $pdo->prepare("UPDATE radius_users SET status = 'DISCONNECTED' WHERE username = ?");
    $stmt->execute([$username]);

    AuditLog::log('RADIUS_DAEMON', 'ACCT_STOP', "PPPoE Accounting STOP diterima untuk username '$username'. Status session DISCONNECTED.");
    echo json_encode([
        'status' => 'success',
        'event' => 'ACCT_STOP',
        'username' => $username,
        'message' => 'Radius session set to DISCONNECTED.'
    ]);
} else {
    // 3. Interim-Update: Keep Alive
    echo json_encode([
        'status' => 'success',
        'event' => 'INTERIM_UPDATE',
        'username' => $username
    ]);
}
