<?php
declare(strict_types=1);

// PHP built-in server router — routes /return to return.php, everything else to index.php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

// Serve existing static files directly (CSS, JS, images…)
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    return false;
}

if ($path === '/return' || $path === '/return/') {
    require __DIR__ . '/return.php';
    exit;
}

require __DIR__ . '/index.php';