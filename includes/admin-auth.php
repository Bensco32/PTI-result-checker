<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

/** Require a logged-in admin; otherwise redirect to admin login. */
function require_admin(): array
{
    if (empty($_SESSION['admin_id']) || !ctype_digit((string) $_SESSION['admin_id'])) {
        unset($_SESSION['admin_id']);
        set_flash('error', 'Please log in as administrator.');
        redirect(base('admin/login.php'));
    }
    $stmt = db()->prepare('SELECT * FROM admins WHERE id = ?');
    $stmt->execute([(int) $_SESSION['admin_id']]);
    $admin = $stmt->fetch();
    if (!$admin) {
        unset($_SESSION['admin_id']);
        redirect(base('admin/login.php'));
    }
    return $admin;
}

function admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}
