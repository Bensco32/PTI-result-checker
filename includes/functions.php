<?php
declare(strict_types=1);

/**
 * Shared bootstrap: session, DB connection, security helpers.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

require_once __DIR__ . '/../config/database.php';

/** Escape output for safe HTML rendering. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Redirect helper. */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Base path prefix: '' for public pages, '../' for sub-folder pages. */
function base(string $path = ''): string
{
    return ($GLOBALS['base'] ?? '') . $path;
}

/** Flash message helpers. */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/** CSRF protection. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Invalid security token. Please go back and try again.');
    }
}

/** Validate an uploaded PDF; returns [tmpPath, error]. */
function validate_pdf_upload(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return [null, 'Please upload a valid PDF file.'];
    }
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        return [null, 'The uploaded PDF exceeds the allowed file size (5MB).'];
    }
    $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        return [null, 'Please upload a valid PDF file.'];
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if ($mime !== 'application/pdf') {
        return [null, 'Please upload a valid PDF file.'];
    }
    return [$file['tmp_name'], null];
}

/** Store an uploaded PDF with a secure random name; returns stored filename. */
function store_pdf(string $tmpPath): string
{
    $name = 'result_' . bin2hex(random_bytes(8)) . '.pdf';
    $dest = __DIR__ . '/../uploads/results/' . $name;
    if (!move_uploaded_file($tmpPath, $dest)) {
        throw new RuntimeException('Could not store the uploaded file.');
    }
    chmod($dest, 0640);
    return $name;
}

/** Validate an uploaded student photo. */
function validate_photo_upload(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return [null, 'upload_error'];
    }
    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return [null, 'The uploaded photo exceeds the allowed file size (2MB).'];
    }
    $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
        return [null, 'Please upload a JPG or PNG photo.'];
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
        return [null, 'Please upload a JPG or PNG photo.'];
    }
    return [$file['tmp_name'], null];
}

function store_photo(string $tmpPath, string $originalName): string
{
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $name = 'photo_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = __DIR__ . '/../uploads/photos/' . $name;
    if (!move_uploaded_file($tmpPath, $dest)) {
        throw new RuntimeException('Could not store the uploaded photo.');
    }
    chmod($dest, 0640);
    return $name;
}

/** Full filesystem path for a stored result, with traversal protection. */
function result_file_path(string $filename): ?string
{
    $filename = basename($filename);
    $path = __DIR__ . '/../uploads/results/' . $filename;
    return is_file($path) ? $path : null;
}

function student_photo_url(?string $photo): string
{
    if ($photo && is_file(__DIR__ . '/../uploads/photos/' . basename($photo))) {
        return base('uploads/photos/' . basename($photo));
    }
    return base('assets/student-photo.jpeg');
}
