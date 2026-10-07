<?php
declare(strict_types=1);

/**
 * PostgreSQL PDO connection.
 *
 * Credentials come from environment variables (Render / production) or
 * config/config.local.php (local development overrides, never committed).
 */

$local = [];
if (is_file(__DIR__ . '/config.local.php')) {
    $loaded = require __DIR__ . '/config.local.php';
    if (is_array($loaded)) {
        $local = $loaded;
    }
}

$dbHost = getenv('DB_HOST') ?: ($local['DB_HOST'] ?? 'localhost');
$dbPort = getenv('DB_PORT') ?: ($local['DB_PORT'] ?? '5432');
$dbName = getenv('DB_NAME') ?: ($local['DB_NAME'] ?? 'pti_result_checker');
$dbUser = getenv('DB_USER') ?: ($local['DB_USER'] ?? 'postgres');
$dbPass = getenv('DB_PASSWORD') !== false
    ? getenv('DB_PASSWORD')
    : ($local['DB_PASSWORD'] ?? '');

function db(): PDO
{
    static $pdo = null;
    global $dbHost, $dbPort, $dbName, $dbUser, $dbPass;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $dbHost, $dbPort, $dbName);
    $sslMode = getenv('DB_SSLMODE') ?: ($GLOBALS['local']['DB_SSLMODE'] ?? '');
    if ($sslMode !== '') {
        $dsn .= ';sslmode=' . $sslMode;
    }
    try {
        $pdo = new PDO($dsn, (string) $dbUser, (string) $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        error_log('PTI DB connection failed: ' . $e->getMessage());
        http_response_code(500);
        exit('Something went wrong while processing your request. Please try again later.');
    }
    return $pdo;
}
