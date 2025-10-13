<?php

declare(strict_types=1);

use Dotenv\Dotenv;

// Project root (one level up from src)
$root = dirname(__DIR__);

// Load environment variables from .env and .env.local if available
if (class_exists(Dotenv::class)) {
    // The third parameter (shortCircuit) = false to allow loading multiple files in order
    $dotenv = Dotenv::createImmutable($root, [
        '.env',
        '.env.local',
    ], false);
    // safeLoad(): loads only if files exist, without throwing exceptions
    $dotenv->safeLoad();
}
