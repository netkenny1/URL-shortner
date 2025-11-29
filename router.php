<?php
// Serve existing files directly
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;
if ($path !== '/' && file_exists($file) && !is_dir($file)) { 
    return false; 
}

// Health and metrics endpoints
if ($path === '/health' || $path === '/metrics') {
    require __DIR__ . '/api.php';
    exit;
}

// API routes
if (strpos($path, '/api/') === 0) { 
    require __DIR__ . '/api.php'; 
    exit; 
}

// Short code redirect like /abc123
if (preg_match('#^/([A-Za-z0-9_-]{4,32})$#', $path)) {
    $_GET['code'] = substr($path, 1);
    require __DIR__ . '/redirect.php';
    exit;
}

// Default: home page
require __DIR__ . '/index.php';
