<?php
declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Router;

session_start();

// Path to SQLite database file
define('DB_PATH', dirname(__DIR__) . '/storage/mini_commerce.sqlite');

$router = new Router();

// Public routes
$router->get('/', [\App\Controllers\HomeController::class, 'index']);

$router->dispatch();