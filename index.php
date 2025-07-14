<?php
/**
 * Lightweight eCommerce Platform
 * Main entry point with routing and initialization
 */

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Define constants
define('ROOT_PATH', __DIR__);
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads');

// Autoloader
spl_autoload_register(function ($class) {
    $file = APP_PATH . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize database
try {
    $db = new \Core\Database();
} catch (Exception $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Initialize router
$router = new \Core\Router();

// Define routes
require_once APP_PATH . '/routes.php';

// Handle request
$router->dispatch();