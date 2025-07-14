<?php

// Home and basic pages
$router->get('/', 'HomeController@index');
$router->get('/about', 'PageController@about');
$router->get('/contact', 'PageController@contact');
$router->post('/contact', 'PageController@contactSubmit');

// Authentication routes
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/forgot-password', 'AuthController@showForgotPassword');
$router->post('/forgot-password', 'AuthController@forgotPassword');
$router->get('/reset-password/{token}', 'AuthController@showResetPassword');
$router->post('/reset-password', 'AuthController@resetPassword');

// Product routes
$router->get('/products', 'ProductController@index');
$router->get('/products/{slug}', 'ProductController@show');
$router->get('/categories', 'CategoryController@index');
$router->get('/categories/{slug}', 'CategoryController@show');
$router->get('/search', 'ProductController@search');

// Shopping cart routes
$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');
$router->post('/cart/clear', 'CartController@clear');

// Checkout routes
$router->get('/checkout', 'CheckoutController@index', ['Auth']);
$router->post('/checkout', 'CheckoutController@process', ['Auth']);
$router->get('/checkout/success/{order}', 'CheckoutController@success', ['Auth']);

// User account routes
$router->get('/account', 'AccountController@dashboard', ['Auth']);
$router->get('/account/profile', 'AccountController@profile', ['Auth']);
$router->post('/account/profile', 'AccountController@updateProfile', ['Auth']);
$router->get('/account/orders', 'AccountController@orders', ['Auth']);
$router->get('/account/orders/{id}', 'AccountController@orderDetails', ['Auth']);
$router->get('/account/addresses', 'AccountController@addresses', ['Auth']);
$router->post('/account/addresses', 'AccountController@addAddress', ['Auth']);

// Wishlist routes
$router->get('/wishlist', 'WishlistController@index', ['Auth']);
$router->post('/wishlist/add', 'WishlistController@add', ['Auth']);
$router->post('/wishlist/remove', 'WishlistController@remove', ['Auth']);
$router->get('/wishlist/share/{token}', 'WishlistController@share');

// Product comparison routes
$router->get('/compare', 'ComparisonController@index');
$router->post('/compare/add', 'ComparisonController@add');
$router->post('/compare/remove', 'ComparisonController@remove');
$router->post('/compare/clear', 'ComparisonController@clear');

// Review routes
$router->get('/products/{slug}/reviews', 'ReviewController@index');
$router->post('/products/{slug}/reviews', 'ReviewController@store', ['Auth']);
$router->post('/reviews/{id}/helpful', 'ReviewController@markHelpful', ['Auth']);

// Subscription routes
$router->get('/subscriptions', 'SubscriptionController@index', ['Auth']);
$router->post('/subscriptions/{id}/pause', 'SubscriptionController@pause', ['Auth']);
$router->post('/subscriptions/{id}/resume', 'SubscriptionController@resume', ['Auth']);
$router->post('/subscriptions/{id}/cancel', 'SubscriptionController@cancel', ['Auth']);

// Gamification routes
$router->get('/profile/badges', 'GamificationController@badges', ['Auth']);
$router->get('/leaderboard', 'GamificationController@leaderboard');
$router->get('/profile/points', 'GamificationController@points', ['Auth']);

// Seller routes
$router->get('/seller/dashboard', 'SellerController@dashboard', ['Auth', 'Seller']);
$router->get('/seller/products', 'SellerController@products', ['Auth', 'Seller']);
$router->get('/seller/products/create', 'SellerController@createProduct', ['Auth', 'Seller']);
$router->post('/seller/products', 'SellerController@storeProduct', ['Auth', 'Seller']);
$router->get('/seller/products/{id}/edit', 'SellerController@editProduct', ['Auth', 'Seller']);
$router->post('/seller/products/{id}', 'SellerController@updateProduct', ['Auth', 'Seller']);
$router->get('/seller/orders', 'SellerController@orders', ['Auth', 'Seller']);

// Admin routes
$router->get('/admin', 'AdminController@dashboard', ['Auth', 'Admin']);
$router->get('/admin/products', 'AdminController@products', ['Auth', 'Admin']);
$router->get('/admin/orders', 'AdminController@orders', ['Auth', 'Admin']);
$router->get('/admin/users', 'AdminController@users', ['Auth', 'Admin']);
$router->get('/admin/categories', 'AdminController@categories', ['Auth', 'Admin']);
$router->get('/admin/settings', 'AdminController@settings', ['Auth', 'Admin']);
$router->post('/admin/settings', 'AdminController@updateSettings', ['Auth', 'Admin']);

// API routes for AJAX functionality
$router->post('/api/cart/count', 'ApiController@cartCount');
$router->post('/api/wishlist/toggle', 'ApiController@wishlistToggle', ['Auth']);
$router->post('/api/products/quick-view', 'ApiController@productQuickView');
$router->get('/api/search/suggestions', 'ApiController@searchSuggestions');

// PWA routes
$router->get('/manifest.json', 'PwaController@manifest');
$router->get('/service-worker.js', 'PwaController@serviceWorker');

// SEO routes
$router->get('/sitemap.xml', 'SeoController@sitemap');
$router->get('/robots.txt', 'SeoController@robots');