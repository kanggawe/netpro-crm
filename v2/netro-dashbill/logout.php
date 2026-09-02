<?php
require_once __DIR__ . '/config/app.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();

header('Location: ' . base_url('login.php?msg=logged_out'));
exit;
