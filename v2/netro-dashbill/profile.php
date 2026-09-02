<?php
require_once __DIR__ . '/config/app.php';

$qs = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header('Location: ' . base_url('pengaturan/profile.php' . $qs));
exit;
