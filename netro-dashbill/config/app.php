<?php
/**
 * NETPRO CRM - Application Configuration & Helpers
 */

// Application Constants
define('APP_NAME', 'NETPRO CRM');
define('APP_DESC', 'ISP Management OS');
define('APP_VERSION', '2.5.0');
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
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    // Determine depth relative to root
    $root = rtrim($scriptDir, '/');
    
    // Normalize root path
    $currentUri = $_SERVER['PHP_SELF'];
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

