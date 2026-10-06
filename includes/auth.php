<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

/** Require a logged-in student; otherwise redirect to login. */
function require_student(): array
{
    if (empty($_SESSION['student_id']) || !ctype_digit((string) $_SESSION['student_id'])) {
        unset($_SESSION['student_id']);
        set_flash('error', 'Please log in to continue.');
        redirect(base('login.php'));
    }
    $stmt = db()->prepare('SELECT * FROM students WHERE id = ?');
    $stmt->execute([(int) $_SESSION['student_id']]);
    $student = $stmt->fetch();
    if (!$student) {
        session_unset();
        session_destroy();
        redirect(base('login.php'));
    }
    return $student;
}

function current_student(): ?array
{
    if (empty($_SESSION['student_id']) || !ctype_digit((string) $_SESSION['student_id'])) {
        return null;
    }
    $stmt = db()->prepare('SELECT * FROM students WHERE id = ?');
    $stmt->execute([(int) $_SESSION['student_id']]);
    return $stmt->fetch() ?: null;
}

function student_logged_in(): bool
{
    return !empty($_SESSION['student_id']);
}
