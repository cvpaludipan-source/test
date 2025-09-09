<?php
declare(strict_types=1);

// Front controller
session_start();

// Autoload simple app files
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/auth.php';

// Simple routing
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// Strip base path if app is deployed under a subdirectory via BASE_PATH
$basePath = getenv('BASE_PATH') ?: '';
if ($basePath && str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath));
}

// Serve static assets from /public if requested directly
if ($path !== '/' && file_exists(__DIR__ . $path) && !is_dir(__DIR__ . $path)) {
    return false; // Let PHP's built-in server handle the file
}

require_once __DIR__ . '/../app/routes.php';

