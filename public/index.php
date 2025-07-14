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
$router->get('/login', [\App\Controllers\AuthController::class, 'showLogin']);
$router->post('/login', [\App\Controllers\AuthController::class, 'login']);
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);

// Admin routes
$router->get('/admin', [\App\Controllers\AdminController::class, 'dashboard'])->middleware('auth');

$router->get('/admin/products', [\App\Controllers\Admin\ProductController::class, 'index'])->middleware('auth');
$router->get('/admin/products/create', [\App\Controllers\Admin\ProductController::class, 'create'])->middleware('auth');
$router->post('/admin/products/store', [\App\Controllers\Admin\ProductController::class, 'store'])->middleware('auth');
$router->get('/admin/products/edit', [\App\Controllers\Admin\ProductController::class, 'edit'])->middleware('auth');
$router->post('/admin/products/update', [\App\Controllers\Admin\ProductController::class, 'update'])->middleware('auth');
$router->get('/admin/products/delete', [\App\Controllers\Admin\ProductController::class, 'delete'])->middleware('auth');

$router->get('/admin/users', [\App\Controllers\Admin\UserController::class, 'index'])->middleware('auth');
$router->get('/admin/users/create', [\App\Controllers\Admin\UserController::class, 'create'])->middleware('auth');
$router->post('/admin/users/store', [\App\Controllers\Admin\UserController::class, 'store'])->middleware('auth');
$router->get('/admin/users/delete', [\App\Controllers\Admin\UserController::class, 'delete'])->middleware('auth');

$router->dispatch();