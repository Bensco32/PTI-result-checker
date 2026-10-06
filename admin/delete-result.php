<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('results.php');
}
verify_csrf();
$id = ctype_digit((string) ($_POST['id'] ?? '')) ? (int) $_POST['id'] : 0;

$stmt = db()->prepare('SELECT result_pdf FROM results WHERE id = ?');
$stmt->execute([$id]);
$file = $stmt->fetchColumn();
if ($file) {
    $path = result_file_path((string) $file);
    if ($path) {
        @unlink($path);
    }
    $del = db()->prepare('DELETE FROM results WHERE id = ?');
    $del->execute([$id]);
    set_flash('success', 'Result deleted successfully.');
} else {
    set_flash('error', 'Result not found.');
}
redirect('results.php');
