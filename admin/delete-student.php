<?php
declare(strict_types=1);
$base = '../';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/admin-auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('students.php');
}
verify_csrf();
$id = ctype_digit((string) ($_POST['id'] ?? '')) ? (int) $_POST['id'] : 0;

$stmt = db()->prepare('SELECT result_pdf FROM results WHERE student_id = ?');
$stmt->execute([$id]);
$files = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($files as $file) {
    $path = result_file_path($file);
    if ($path) {
        @unlink($path);
    }
}

$del = db()->prepare('DELETE FROM students WHERE id = ?');
$del->execute([$id]);
set_flash('success', 'Student deleted successfully.');
redirect('students.php');
