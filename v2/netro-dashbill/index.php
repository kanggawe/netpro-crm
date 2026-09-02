<?php
/**
 * Root Entry Point - NETPRO CRM ISP Management OS
 */
require_once __DIR__ . '/config/app.php';

if (!is_logged_in()) {
    header('Location: ' . base_url('login.php'));
} else {
    header('Location: ' . base_url('dashboard/utama.php'));
}
exit;
