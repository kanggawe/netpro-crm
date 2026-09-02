<?php
/**
 * NETPRO CRM - Application Configuration & Helpers
 */

// Application Constants
define('APP_NAME', 'NETPRO CRM');
define('APP_DESC', 'ISP Management OS');
define('APP_VERSION', '4.0.0-ENTERPRISE');
// Set Standard Timezone Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// Hardened Session Security Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// Auto-load Database & CRUD Models globally
require_once __DIR__ . '/models.php';

/**
 * CSRF Protection Helpers
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function validate_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Authentication Helpers
 */
function auth_user() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return !empty($_SESSION['user']) && is_array($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . base_url('login.php?msg=auth_required'));
        exit;
    }
}

function login_user($userData) {
    session_regenerate_id(true); // Prevent Session Fixation & Hijacking
    $_SESSION['user'] = $userData;
    $_SESSION['logged_in_at'] = date('Y-m-d H:i:s');
}

function logout_user() {
    unset($_SESSION['user']);
    unset($_SESSION['logged_in_at']);
    unset($_SESSION['2fa_pending_user']);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
        session_destroy();
    }
}

/**
 * Role-Based Access Control (RBAC) Matrix Configuration & Checker
 */
function get_rbac_matrix_permissions() {
    $defaultPermissions = [
        'super admin'   => ['all'],
        'administrator' => ['all'],
        'teknisi'       => ['m-dashboard', 'm-crm', 'm-noc', 'm-tickets', 'm-inventory', 'm-hr', 'm-payroll', 'm-kalkulator'],
        'field'         => ['m-dashboard', 'm-crm', 'm-noc', 'm-tickets', 'm-inventory', 'm-hr', 'm-payroll', 'm-kalkulator'],
        'finance'       => ['m-dashboard', 'm-billing', 'm-finance', 'm-payroll', 'm-kalkulator', 'm-laporan'],
        'kasir'         => ['m-dashboard', 'm-billing', 'm-finance', 'm-kalkulator'],
        'noc'           => ['m-dashboard', 'm-noc', 'm-radius', 'm-tickets', 'm-kalkulator', 'm-laporan'],
        'network'       => ['m-dashboard', 'm-noc', 'm-radius', 'm-tickets', 'm-kalkulator', 'm-laporan'],
        'support'       => ['m-dashboard', 'm-crm', 'm-billing', 'm-tickets', 'm-radius'],
        'cs'            => ['m-dashboard', 'm-crm', 'm-billing', 'm-tickets', 'm-radius'],
        'sales'         => ['m-dashboard', 'm-crm', 'm-marketing', 'm-kalkulator'],
        'marketing'     => ['m-dashboard', 'm-crm', 'm-marketing', 'm-kalkulator'],
        'hr'            => ['m-dashboard', 'm-hr', 'm-kinerja', 'm-payroll', 'm-kalkulator', 'm-laporan'],
        'ga'            => ['m-dashboard', 'm-hr', 'm-inventory', 'm-kalkulator'],
        'inventory'     => ['m-dashboard', 'm-inventory', 'm-noc', 'm-kalkulator'],
        'warehouse'     => ['m-dashboard', 'm-inventory', 'm-noc', 'm-kalkulator']
    ];

    if (class_exists('Setting')) {
        $saved = Setting::get('rbac_custom_permissions');
        if ($saved) {
            $custom = json_decode($saved, true);
            if (is_array($custom)) {
                return array_merge($defaultPermissions, $custom);
            }
        }
    }
    return $defaultPermissions;
}

function can_access($moduleId, $user = null) {
    $user = $user ?? auth_user();
    if (!$user) {
        return false;
    }
    $role = strtolower($user['role'] ?? '');

    // Super Admin has unrestricted access to all modules
    if (strpos($role, 'super') !== false) {
        return true;
    }

    $rolePermissions = get_rbac_matrix_permissions();

    foreach ($rolePermissions as $key => $allowedModules) {
        if (strpos($role, $key) !== false || $role === $key) {
            if (in_array('all', $allowedModules)) {
                return true;
            }
            return in_array($moduleId, $allowedModules);
        }
    }

    return false; // Strict RBAC: Deny access if role not explicitly matched
}

/**
 * Get Base URL dynamically
 */
function base_url($path = '') {
    $currentUri = $_SERVER['PHP_SELF'] ?? '';
    $parts = explode('/', trim($currentUri, '/'));
    
    // If inside a subfolder (dashboard, crm, billing, etc.)
    $depth = count($parts) - 1;
    $prefix = ($depth > 0) ? str_repeat('../', $depth) : './';
    
    return $prefix . ltrim($path, '/');
}

/**
 * Get Asset URL
 */
function asset_url($path = '') {
    return base_url('assets/' . ltrim($path, '/'));
}

/**
 * Format Rupiah Currency
 */
function format_rupiah($angka, $withPrefix = true) {
    $hasil = number_format($angka, 0, ',', '.');
    return $withPrefix ? 'Rp ' . $hasil : $hasil;
}

/**
 * Calculate PPN 11% (Include vs Exclude)
 */
function calculate_ppn($nominal, $mode = 'include') {
    $nominal = floatval($nominal);
    if ($mode === 'include') {
        $dpp = round($nominal / 1.11);
        $ppn = $nominal - $dpp;
        $total = $nominal;
    } else {
        $dpp = $nominal;
        $ppn = round($nominal * 0.11);
        $total = $nominal + $ppn;
    }
    return [
        'dpp' => $dpp,
        'ppn' => $ppn,
        'total' => $total,
        'mode' => $mode
    ];
}

/**
 * Check Physical Reachability of Hardware Node (Socket / Ping Probe)
 * Menghasilkan TRUE hanya jika perangkat fisik menyala dan terhubung ke jaringan.
 */
function is_hardware_node_online($host, $port = 8728, $timeout = 0.2) {
    if (empty($host) || $host === '0.0.0.0' || $host === 'none') {
        return false;
    }
    
    // Probe primary port with short non-blocking timeout
    $fp = @fsockopen($host, intval($port), $errno, $errstr, $timeout);
    if ($fp) {
        fclose($fp);
        return true;
    }
    
    // Fallback probe port 80 / 443 / 22 / 23 jika port utama berbeda
    if ($port !== 80) {
        $fp80 = @fsockopen($host, 80, $errno, $errstr, $timeout);
        if ($fp80) {
            fclose($fp80);
            return true;
        }
    }
    
    return false;
}

/**
 * RFC 6238 TOTP (Time-Based One-Time Password) Engine
 * Compatible with Google Authenticator, Microsoft Authenticator, Authy, and 1Password.
 */
class TOTP {
    /**
     * Decode a Base32 string to binary bytes
     */
    public static function base32Decode($base32) {
        $base32 = strtoupper(str_replace([' ', '-'], '', (string)$base32));
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $buffer = 0;
        $bitsLeft = 0;
        $binary = '';
        $len = strlen($base32);
        for ($i = 0; $i < $len; $i++) {
            $val = strpos($chars, $base32[$i]);
            if ($val === false) continue;
            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $binary .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }
        return $binary;
    }

    /**
     * Generate a cryptographically secure Base32 Secret Key (RFC 4648)
     */
    public static function generateSecret($length = 16) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[ord($bytes[$i]) % 32];
        }
        return $secret;
    }

    /**
     * Calculate 6-digit TOTP token for given secret and time slice
     */
    public static function getCode($secret, $timeSlice = null) {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / 30);
        }
        $secretKey = self::base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hmac = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashpart = substr($hmac, $offset, 4);
        $value = unpack('N', $hashpart);
        $value = $value[1] & 0x7FFFFFFF;
        return str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify a 6-digit TOTP token with time discrepancy tolerance
     */
    public static function verifyCode($secret, $code, $discrepancy = 1) {
        $code = trim(str_replace([' ', '-'], '', (string)$code));
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }
        $currentTimeSlice = floor(time() / 30);
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculated = self::getCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculated, $code)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate Google Authenticator compatible otpauth:// QR code image URL
     */
    public static function getQrUrl($label, $secret, $issuer = 'NETPRO CRM') {
        $encodedLabel = rawurlencode($issuer . ':' . $label);
        $encodedIssuer = rawurlencode($issuer);
        $encodedSecret = rawurlencode($secret);
        $otpauth = "otpauth://totp/{$encodedLabel}?secret={$encodedSecret}&issuer={$encodedIssuer}&period=30&digits=6";
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauth);
    }

    /**
     * Get or initialize a user's 2FA secret key
     */
    public static function getUserSecret($userId) {
        $key = 'two_factor_secret_user_' . intval($userId);
        $secret = Setting::get($key);
        if (empty($secret)) {
            $secret = self::generateSecret(16);
            Setting::set($key, $secret);
        }
        return $secret;
    }

    /**
     * Check if 2FA is active for a user
     */
    public static function isUser2FAEnabled($userId) {
        return (Setting::get('two_factor_user_' . intval($userId), '0') === '1');
    }
}

/**
 * =========================================================================
 * Cryptographic URL Parameter & Anti-IDOR Security Suite
 * =========================================================================
 * Provides AES-256-CBC URL parameter encryption, numeric ID masking/obfuscation,
 * HMAC-SHA256 Signed URLs with expiration timestamps, and Clean URL helpers.
 */
class UrlCrypto {
    private static $secretKey = null;

    /**
     * Get or derive 256-bit application secret key
     */
    public static function getKey() {
        if (self::$secretKey === null) {
            $envKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? 'NETPRO-CRM-SECRET-SALT-KEY-2026-X99-PROD');
            self::$secretKey = hash('sha256', $envKey, true);
        }
        return self::$secretKey;
    }

    /**
     * Base64 URL-safe encode
     */
    public static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL-safe decode
     */
    public static function base64UrlDecode($data) {
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Encrypt data or ID into a tamper-proof URL token (AES-256-CBC + HMAC-SHA256)
     */
    public static function encrypt($data, $context = '') {
        $key = self::getKey();
        $payload = json_encode(['d' => $data, 'c' => $context, 't' => time()]);
        
        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($payload, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $iv . $ciphertext . $context, $key, true);
        
        return self::base64UrlEncode($iv . $hmac . $ciphertext);
    }

    /**
     * Decrypt and verify URL token. Returns original data or false if invalid/tampered.
     */
    public static function decrypt($token, $context = '') {
        if (empty($token) || !is_string($token)) return false;
        
        $raw = self::base64UrlDecode($token);
        if ($raw === false || strlen($raw) < 48) return false; // 16 (IV) + 32 (HMAC) = 48 bytes min
        
        $iv = substr($raw, 0, 16);
        $hmac = substr($raw, 16, 32);
        $ciphertext = substr($raw, 48);
        
        $key = self::getKey();
        $expectedHmac = hash_hmac('sha256', $iv . $ciphertext . $context, $key, true);
        
        if (!hash_equals($expectedHmac, $hmac)) {
            return false; // HMAC verification failed / Tamper detected
        }
        
        $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($decrypted === false) return false;
        
        $data = json_decode($decrypted, true);
        if (!isset($data['d'])) return false;
        
        if ($context !== '' && ($data['c'] ?? '') !== $context) {
            return false; // Context mismatch
        }
        
        return $data['d'];
    }

    /**
     * Fast, lightweight reversible integer ID obfuscation (Anti-Scraping / Anti-IDOR)
     */
    public static function maskId($id, $prefix = 'np_') {
        $id = intval($id);
        if ($id <= 0) return '';
        // Feistel-style bit mix with salt
        $mixed = (($id * 38271) ^ 0x5A5A5A5) & 0x7FFFFFFF;
        $hash = base_convert((string)$mixed, 10, 36);
        $chk = substr(hash('crc32b', $id . 'netpro'), 0, 2);
        return $prefix . $hash . $chk;
    }

    /**
     * Decode masked ID back to original numeric ID
     */
    public static function unmaskId($hash, $prefix = 'np_') {
        if (empty($hash)) return 0;
        if (is_numeric($hash)) return intval($hash); // Backward compatibility
        
        if (strpos($hash, $prefix) === 0) {
            $hash = substr($hash, strlen($prefix));
        }
        if (strlen($hash) < 3) return 0;
        
        $chk = substr($hash, -2);
        $body = substr($hash, 0, -2);
        $mixed = intval(base_convert($body, 36, 10));
        
        // Reverse bit mix
        $xor = $mixed ^ 0x5A5A5A5;
        for ($i = 1; $i <= 1000000; $i++) {
            if ((($i * 38271) & 0x7FFFFFFF) === $xor) {
                if (substr(hash('crc32b', $i . 'netpro'), 0, 2) === $chk) {
                    return $i;
                }
            }
        }
        return 0;
    }

    /**
     * Normalize URL path for signature hashing
     */
    public static function normalizePath($path) {
        $path = str_replace('\\', '/', (string)$path);
        $path = preg_replace('#^(\.\./|\./)+#', '', $path);
        $clean = trim(parse_url($path, PHP_URL_PATH) ?? $path, '/');
        return preg_replace('/\.php$/i', '', $clean);
    }

    /**
     * Generate Signed URL with HMAC signature and optional expiration
     */
    public static function signUrl($path, $params = [], $expiresInMinutes = null) {
        if ($expiresInMinutes !== null) {
            $params['expires'] = time() + (intval($expiresInMinutes) * 60);
        }
        ksort($params);
        $queryString = http_build_query($params);
        $key = self::getKey();
        $normPath = self::normalizePath($path);
        $signature = hash_hmac('sha256', $normPath . '?' . $queryString, $key);
        $params['signature'] = substr($signature, 0, 16);
        
        $finalQuery = http_build_query($params);
        return base_url($path) . ($finalQuery ? '?' . $finalQuery : '');
    }

    /**
     * Verify a Signed URL's integrity and expiration
     */
    public static function verifySignedUrl($path = null, $queryParams = null) {
        if ($queryParams === null) {
            $queryParams = $_GET;
        }
        if ($path === null) {
            $path = $_SERVER['PHP_SELF'] ?? '';
        }
        
        if (empty($queryParams['signature'])) {
            return false;
        }
        
        $signature = $queryParams['signature'];
        unset($queryParams['signature']);
        
        if (isset($queryParams['expires']) && time() > intval($queryParams['expires'])) {
            return false; // Signature expired
        }
        
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $key = self::getKey();
        $normPath = self::normalizePath($path);
        $expected = substr(hash_hmac('sha256', $normPath . '?' . $queryString, $key), 0, 16);
        
        return hash_equals($expected, $signature);
    }
}

/**
 * Global Helper Shortcuts
 */
function url_encrypt($data, $context = '') {
    return UrlCrypto::encrypt($data, $context);
}

function url_decrypt($token, $context = '') {
    return UrlCrypto::decrypt($token, $context);
}

function mask_id($id) {
    return UrlCrypto::maskId($id);
}

function unmask_id($hash) {
    return UrlCrypto::unmaskId($hash);
}

function signed_url($path, $params = [], $expiresInMinutes = null) {
    return UrlCrypto::signUrl($path, $params, $expiresInMinutes);
}

function verify_signed_url($path = null, $queryParams = null) {
    return UrlCrypto::verifySignedUrl($path, $queryParams);
}

function route_url($path) {
    // Return clean URL without .php extension if provided
    $cleanPath = preg_replace('/\.php(\?|$)/', '$1', $path);
    return base_url($cleanPath);
}
