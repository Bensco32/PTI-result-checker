<?php
declare(strict_types=1);
$base = '';
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $logoutStudent = ($_POST['who'] ?? 'student') === 'student';
    session_unset();
    session_destroy();
    session_start();
    session_regenerate_id(true);
    redirect('login.php');
}

// GET fallback: clear session fully
session_unset();
session_destroy();
session_start();
session_regenerate_id(true);
redirect('index.php');
