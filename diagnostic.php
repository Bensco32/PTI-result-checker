<?php
declare(strict_types=1);

header('Content-Type: text/plain');

require __DIR__ . '/config/database.php';

try {
    $pdo = db();

    echo "DATABASE CONNECTION: SUCCESS" . PHP_EOL;
    echo "PostgreSQL VERSION: " . $pdo->query('SELECT version()')->fetchColumn() . PHP_EOL;
    echo PHP_EOL;

    $tables = ['admins', 'students', 'results'];

    foreach ($tables as $table) {
        $stmt = $pdo->prepare("
            SELECT EXISTS (
                SELECT 1
                FROM information_schema.tables
                WHERE table_schema = 'public'
                AND table_name = :table
            )
        ");
        $stmt->execute(['table' => $table]);

        echo $table . ': ' . ($stmt->fetchColumn() ? 'EXISTS' : 'MISSING') . PHP_EOL;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo "DATABASE CONNECTION: FAILED" . PHP_EOL;
    echo "ERROR TYPE: " . get_class($e) . PHP_EOL;
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
