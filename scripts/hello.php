<?php

require __DIR__ . '/../vendor/autoload.php';

$name = $argv[1] ?? 'World';
$greeting = $_ENV['GREETING'] ?? getenv('GREETING') ?: 'Hello';

echo "$greeting, {$name}!" . PHP_EOL;
