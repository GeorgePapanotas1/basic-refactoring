<?php

// Public front controller for the built-in PHP web server
// Loads Composer autoload (which in turn loads .env via src/bootstrap.php)
require __DIR__ . '/../vendor/autoload.php';

$appName = isset($_ENV['APP_NAME']) ? $_ENV['APP_NAME'] : ((getenv('APP_NAME') !== false) ? getenv('APP_NAME') : 'My PHP App');
$greeting = isset($_ENV['GREETING']) ? $_ENV['GREETING'] : ((getenv('GREETING') !== false) ? getenv('GREETING') : 'Hello');

$name = isset($_GET['name']) ? $_GET['name'] : 'World';

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        html, body { height: 100%; margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Fira Sans', 'Droid Sans', 'Helvetica Neue', Arial, sans-serif; }
        .wrap { display: grid; place-items: center; height: 100%; background: #0f172a; color: #e2e8f0; }
        .card { background: #111827; padding: 2rem 2.5rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,.35); max-width: 720px; }
        h1 { margin: 0 0 .5rem; font-size: 1.75rem; }
        p { margin: .25rem 0; color: #cbd5e1; }
        code { background: #0b1220; padding: .15rem .35rem; border-radius: 6px; color: #93c5fd; }
        a { color: #93c5fd; }
        .muted { color: #94a3b8; font-size: .95rem; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1><?php echo htmlspecialchars($greeting . ', ' . $name . '!', ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="muted">Welcome to <?php echo htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'); ?> running on the PHP built-in server.</p>
        <p>Try setting <code>APP_NAME</code> or <code>GREETING</code> in your <code>.env</code> file, then refresh this page.</p>
        <p>Pass a name via query: <code>?name=YourName</code></p>
        <p><small>Served from <code>public/index.php</code></small></p>
    </div>
</div>
</body>
</html>
