<?php

// Home routes
$router->add('', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('home', ['controller' => 'HomeController', 'action' => 'index']);

// Product routes
$router->add('products', ['controller' => 'ProductController', 'action' => 'index']);
$router->add('products/{slug}', ['controller' => 'ProductController', 'action' => 'show']);
$router->add('category/{slug}', ['controller' => 'ProductController', 'action' => 'category']);
$router->add('search', ['controller' => 'ProductController', 'action' => 'search']);

// Cart routes
$router->add('cart', ['controller' => 'CartController', 'action' => 'index']);
$router->add('cart/add', ['controller' => 'CartController', 'action' => 'add']);
$router->add('cart/update', ['controller' => 'CartController', 'action' => 'update']);
$router->add('cart/remove', ['controller' => 'CartController', 'action' => 'remove']);
$router->add('cart/clear', ['controller' => 'CartController', 'action' => 'clear']);

// Checkout routes
$router->add('checkout', ['controller' => 'CheckoutController', 'action' => 'index']);
$router->add('checkout/process', ['controller' => 'CheckoutController', 'action' => 'process']);
$router->add('checkout/success', ['controller' => 'CheckoutController', 'action' => 'success']);

// User authentication routes
$router->add('login', ['controller' => 'AuthController', 'action' => 'login']);
$router->add('register', ['controller' => 'AuthController', 'action' => 'register']);
$router->add('logout', ['controller' => 'AuthController', 'action' => 'logout']);
$router->add('forgot-password', ['controller' => 'AuthController', 'action' => 'forgotPassword']);
$router->add('reset-password', ['controller' => 'AuthController', 'action' => 'resetPassword']);

// User account routes
$router->add('account', ['controller' => 'AccountController', 'action' => 'index']);
$router->add('account/profile', ['controller' => 'AccountController', 'action' => 'profile']);
$router->add('account/orders', ['controller' => 'AccountController', 'action' => 'orders']);
$router->add('account/order/{id}', ['controller' => 'AccountController', 'action' => 'order']);
$router->add('account/wishlist', ['controller' => 'AccountController', 'action' => 'wishlist']);
$router->add('account/subscriptions', ['controller' => 'AccountController', 'action' => 'subscriptions']);

// Wishlist routes
$router->add('wishlist/add', ['controller' => 'WishlistController', 'action' => 'add']);
$router->add('wishlist/remove', ['controller' => 'WishlistController', 'action' => 'remove']);
$router->add('wishlist/share', ['controller' => 'WishlistController', 'action' => 'share']);

// Review routes
$router->add('reviews/add', ['controller' => 'ReviewController', 'action' => 'add']);
$router->add('reviews/edit', ['controller' => 'ReviewController', 'action' => 'edit']);
$router->add('reviews/delete', ['controller' => 'ReviewController', 'action' => 'delete']);

// Subscription routes
$router->add('subscriptions/create', ['controller' => 'SubscriptionController', 'action' => 'create']);
$router->add('subscriptions/pause', ['controller' => 'SubscriptionController', 'action' => 'pause']);
$router->add('subscriptions/cancel', ['controller' => 'SubscriptionController', 'action' => 'cancel']);

// Admin routes
$router->add('admin', ['controller' => 'AdminController', 'action' => 'index']);
$router->add('admin/products', ['controller' => 'AdminController', 'action' => 'products']);
$router->add('admin/products/create', ['controller' => 'AdminController', 'action' => 'createProduct']);
$router->add('admin/products/edit/{id}', ['controller' => 'AdminController', 'action' => 'editProduct']);
$router->add('admin/products/delete', ['controller' => 'AdminController', 'action' => 'deleteProduct']);
$router->add('admin/orders', ['controller' => 'AdminController', 'action' => 'orders']);
$router->add('admin/orders/{id}', ['controller' => 'AdminController', 'action' => 'order']);
$router->add('admin/users', ['controller' => 'AdminController', 'action' => 'users']);
$router->add('admin/categories', ['controller' => 'AdminController', 'action' => 'categories']);
$router->add('admin/settings', ['controller' => 'AdminController', 'action' => 'settings']);

// Seller routes
$router->add('seller', ['controller' => 'SellerController', 'action' => 'index']);
$router->add('seller/products', ['controller' => 'SellerController', 'action' => 'products']);
$router->add('seller/orders', ['controller' => 'SellerController', 'action' => 'orders']);
$router->add('seller/analytics', ['controller' => 'SellerController', 'action' => 'analytics']);

// API routes
$router->add('api/products', ['controller' => 'ApiController', 'action' => 'products']);
$router->add('api/cart', ['controller' => 'ApiController', 'action' => 'cart']);
$router->add('api/wishlist', ['controller' => 'ApiController', 'action' => 'wishlist']);

// PWA routes
$router->add('manifest.json', ['controller' => 'PwaController', 'action' => 'manifest']);
$router->add('service-worker.js', ['controller' => 'PwaController', 'action' => 'serviceWorker']);

// Static pages
$router->add('about', ['controller' => 'PageController', 'action' => 'about']);
$router->add('contact', ['controller' => 'PageController', 'action' => 'contact']);
$router->add('privacy', ['controller' => 'PageController', 'action' => 'privacy']);
$router->add('terms', ['controller' => 'PageController', 'action' => 'terms']);
$router->add('faq', ['controller' => 'PageController', 'action' => 'faq']);

// Error pages
$router->add('404', ['controller' => 'ErrorController', 'action' => 'notFound']);
$router->add('unauthorized', ['controller' => 'ErrorController', 'action' => 'unauthorized']);