<?php
declare(strict_types=1);
/**
 * Inline PDF preview endpoint.
 * Authorised when: a student views their own result, an admin views any result,
 * or the public checker supplies matching matric + session + semester.
 */
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/admin-auth.php';

$result = null;

if (isset($_GET['id']) && ctype_digit((string) $_GET['id'])) {
    $stmt = db()->prepare(
        'SELECT r.*, s.matric_number FROM results r JOIN students s ON s.id = r.student_id WHERE r.id = ?'
    );
    $stmt->execute([(int) $_GET['id']]);
    $candidate = $stmt->fetch();
    if ($candidate) {
        $allowed = false;
        if (!empty($_SESSION['admin_id'])) {
            $allowed = true;
        } elseif (!empty($_SESSION['student_id']) && (int) $_SESSION['student_id'] === (int) $candidate['student_id']) {
            $allowed = true;
        } else {
            // Public verification via student-identifying credentials
            $matric = trim((string) ($_GET['matric'] ?? ''));
            $session = trim((string) ($_GET['session'] ?? ''));
            $semester = trim((string) ($_GET['semester'] ?? ''));
            $s2 = db()->prepare('SELECT matric_number FROM students WHERE id = ?');
            $s2->execute([$candidate['student_id']]);
            $owner = $s2->fetch();
            if ($owner && $matric !== '' && $session !== '' && $semester !== ''
                && hash_equals($owner['matric_number'], $matric)
                && hash_equals($candidate['academic_session'], $session)
                && hash_equals($candidate['semester'], $semester)) {
                $allowed = true;
            }
        }
        if ($allowed) {
            $result = $candidate;
        }
    }
} else {
    $matric = trim((string) ($_GET['matric'] ?? ''));
    $session = trim((string) ($_GET['session'] ?? ''));
    $semester = trim((string) ($_GET['semester'] ?? ''));
    if ($matric !== '' && $session !== '' && $semester !== '') {
        $stmt = db()->prepare(
            'SELECT r.*, s.matric_number FROM results r JOIN students s ON s.id = r.student_id
             WHERE s.matric_number = ? AND r.academic_session = ? AND r.semester = ?'
        );
        $stmt->execute([$matric, $session, $semester]);
        $result = $stmt->fetch() ?: null;
    }
}

if (!$result) {
    http_response_code(404);
    exit('Result not found or access denied.');
}

$path = result_file_path($result['result_pdf']);
if ($path === null) {
    http_response_code(404);
    exit('The result file is missing. Please contact the administrator.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($path) . '"');
header('Content-Length: ' . filesize($path));
header('X-Content-Type-Options: nosniff');
readfile($path);
exit;
