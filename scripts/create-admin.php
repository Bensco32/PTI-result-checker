<?php
declare(strict_types=1);
/**
 * Create the first administrator account.
 * Usage: php scripts/create-admin.php [--username=... --email=... --name=... --password=...]
 * If arguments are omitted, you will be prompted interactively.
 */
require_once __DIR__ . '/../includes/functions.php';

$opts = [];
foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^--([^=]+)=(.*)$/', $arg, $m)) {
        $opts[$m[1]] = $m[2];
    }
}

function ask(string $prompt): string
{
    fwrite(STDOUT, $prompt);
    $line = fgets(STDIN);
    return trim((string) $line);
}

$username = $opts['username'] ?? ask('Admin username: ');
$email    = $opts['email'] ?? ask('Admin email: ');
$name     = $opts['name'] ?? ('Administrator ' . $username);
$password = $opts['password'] ?? null;
if ($password === null) {
    $password = ask('Admin password (min 8 chars): ');
}

if (strlen($password) < 8) {
    fwrite(STDERR, "Password must be at least 8 characters.\n");
    exit(1);
}
if ($username === '' || $email === '') {
    fwrite(STDERR, "Username and email are required.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
try {
    $stmt = db()->prepare(
        'INSERT INTO admins (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)
         ON CONFLICT (username) DO UPDATE SET full_name = EXCLUDED.full_name, email = EXCLUDED.email, password_hash = EXCLUDED.password_hash, updated_at = now()'
    );
    $stmt->execute([$name, $username, $email, $hash]);
    fwrite(STDOUT, "Admin account created/updated for '{$username}'.\n");
} catch (Throwable $e) {
    fwrite(STDERR, 'Failed: ' . $e->getMessage() . "\n");
    exit(1);
}
