<?php
header('Content-Type: text/plain');

$vars = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'];

foreach ($vars as $var) {
    $value = getenv($var);
    echo $var . ': ' . ($value !== false && $value !== '' ? 'SET' : 'NOT SET') . PHP_EOL;
}
