<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Include composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load configuration
$config = require_once __DIR__ . '/config/app.php';

// Initialize database (this will create tables if they don't exist)
try {
    $db = \Core\Database\Database::getInstance();
    
    // Run migrations if database is empty
    $migrationFile = __DIR__ . '/database/migrations/001_create_tables.sql';
    if (file_exists($migrationFile)) {
        $sql = file_get_contents($migrationFile);
        $db->getConnection()->exec($sql);
        
        // Run sample data seeder after migration
        if (file_exists(__DIR__ . '/database/seeds/sample_data.php')) {
            require_once __DIR__ . '/database/seeds/sample_data.php';
            seedSampleData();
        }
    }
} catch (Exception $e) {
    if ($config['debug']) {
        die("Database error: " . $e->getMessage());
    } else {
        die("Database connection failed. Please try again later.");
    }
}

// Initialize router
$router = new \Core\Router\Router();

// Define routes
require_once __DIR__ . '/routes/web.php';

// Helper functions
function config($key, $default = null) {
    global $config;
    $keys = explode('.', $key);
    $value = $config;
    
    foreach ($keys as $keyPart) {
        if (isset($value[$keyPart])) {
            $value = $value[$keyPart];
        } else {
            return $default;
        }
    }
    
    return $value;
}

function url($path = '') {
    $baseUrl = config('url', 'http://localhost');
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function asset($path) {
    return url('assets/' . ltrim($path, '/'));
}

function csrf_token() {
    return $_SESSION['csrf_token'] ?? '';
}

function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

function flash($key) {
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

function auth() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function format_currency($amount, $currency = 'USD') {
    return '$' . number_format($amount, 2);
}

function format_date($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

function str_slug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

function generate_order_number() {
    return 'ORD-' . strtoupper(uniqid());
}

function calculate_points($amount) {
    $pointsPerDollar = config('gamification.reward_actions.points_per_dollar', 10);
    return intval($amount * $pointsPerDollar);
}

// Resolve the current route
try {
    $router->resolve();
} catch (Exception $e) {
    if (config('debug')) {
        die("Router error: " . $e->getMessage());
    } else {
        http_response_code(500);
        include_once __DIR__ . '/app/Views/errors/500.php';
    }
}