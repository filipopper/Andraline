<!DOCTYPE html>
<html lang="en" <?= $accessibility_mode ?? false ? 'class="high-contrast"' : '' ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'LightCommerce' ?> - Modern eCommerce Platform</title>
    <meta name="description" content="<?= $meta_description ?? 'Lightweight, innovative, and extensible eCommerce platform built with modern web technologies' ?>">
    <meta name="keywords" content="ecommerce, online store, shopping cart, products">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= $page_title ?? 'LightCommerce' ?>">
    <meta property="og:description" content="<?= $meta_description ?? 'Modern eCommerce Platform' ?>">
    <meta property="og:image" content="<?= asset('images/og-image.jpg') ?>">
    <meta property="og:url" content="<?= url($_SERVER['REQUEST_URI'] ?? '') ?>">
    <meta property="og:type" content="website">
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <style>
        .high-contrast {
            filter: contrast(150%) saturate(150%);
        }
        .accessibility-focus:focus {
            outline: 3px solid #fbbf24 !important;
            outline-offset: 2px !important;
        }
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .btn-primary {
            @apply bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-colors;
        }
        .btn-secondary {
            @apply bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors;
        }
    </style>
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#3b82f6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= csrf_token() ?>">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Accessibility Skip Links -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-0 focus:left-0 bg-primary-600 text-white p-2 z-50">
        Skip to main content
    </a>
    
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <!-- Top Bar -->
            <div class="flex items-center justify-between py-2 text-sm border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Free shipping on orders over $50</span>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Accessibility Toggle -->
                    <button onclick="toggleAccessibility()" class="text-gray-600 hover:text-primary-600" title="Toggle High Contrast">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2L13.09 8.26L20 9L14 14.74L15.18 21.02L10 17.77L4.82 21.02L6 14.74L0 9L6.91 8.26L10 2Z"/>
                        </svg>
                    </button>
                    <?php if (is_logged_in()): ?>
                        <a href="/account" class="text-gray-600 hover:text-primary-600">My Account</a>
                        <a href="/logout" class="text-gray-600 hover:text-primary-600">Logout</a>
                    <?php else: ?>
                        <a href="/login" class="text-gray-600 hover:text-primary-600">Login</a>
                        <a href="/register" class="text-gray-600 hover:text-primary-600">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Main Header -->
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">L</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">LightCommerce</span>
                </a>
                
                <!-- Search Bar -->
                <div class="flex-1 max-w-lg mx-8">
                    <form action="/search" method="GET" class="relative">
                        <input type="text" name="q" placeholder="Search products..." 
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                               class="w-full pl-4 pr-12 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent accessibility-focus">
                        <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-primary-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>
                </div>
                
                <!-- Header Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Wishlist -->
                    <?php if (is_logged_in()): ?>
                        <a href="/wishlist" class="relative text-gray-600 hover:text-primary-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </a>
                    <?php endif; ?>
                    
                    <!-- Cart -->
                    <a href="/cart" class="relative text-gray-600 hover:text-primary-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13h10m-4 7a1 1 0 100-2 1 1 0 000 2zm6 0a1 1 0 100-2 1 1 0 000 2z"/>
                        </svg>
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-primary-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </a>
                    
                    <!-- Mobile Menu Toggle -->
                    <button class="md:hidden text-gray-600" onclick="toggleMobileMenu()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="hidden md:flex border-t border-gray-200 py-4">
                <div class="flex space-x-8">
                    <a href="/" class="text-gray-700 hover:text-primary-600 font-medium">Home</a>
                    <a href="/products" class="text-gray-700 hover:text-primary-600 font-medium">All Products</a>
                    <a href="/categories" class="text-gray-700 hover:text-primary-600 font-medium">Categories</a>
                    <a href="/compare" class="text-gray-700 hover:text-primary-600 font-medium">Compare</a>
                    <?php if (is_logged_in()): ?>
                        <a href="/subscriptions" class="text-gray-700 hover:text-primary-600 font-medium">Subscriptions</a>
                        <a href="/profile/badges" class="text-gray-700 hover:text-primary-600 font-medium">Badges</a>
                    <?php endif; ?>
                    <a href="/about" class="text-gray-700 hover:text-primary-600 font-medium">About</a>
                    <a href="/contact" class="text-gray-700 hover:text-primary-600 font-medium">Contact</a>
                </div>
            </nav>
            
            <!-- Mobile Navigation -->
            <nav id="mobile-menu" class="md:hidden hidden border-t border-gray-200 py-4">
                <div class="flex flex-col space-y-2">
                    <a href="/" class="text-gray-700 hover:text-primary-600 font-medium py-2">Home</a>
                    <a href="/products" class="text-gray-700 hover:text-primary-600 font-medium py-2">All Products</a>
                    <a href="/categories" class="text-gray-700 hover:text-primary-600 font-medium py-2">Categories</a>
                    <a href="/compare" class="text-gray-700 hover:text-primary-600 font-medium py-2">Compare</a>
                    <?php if (is_logged_in()): ?>
                        <a href="/subscriptions" class="text-gray-700 hover:text-primary-600 font-medium py-2">Subscriptions</a>
                        <a href="/profile/badges" class="text-gray-700 hover:text-primary-600 font-medium py-2">Badges</a>
                    <?php endif; ?>
                    <a href="/about" class="text-gray-700 hover:text-primary-600 font-medium py-2">About</a>
                    <a href="/contact" class="text-gray-700 hover:text-primary-600 font-medium py-2">Contact</a>
                </div>
            </nav>
        </div>
    </header>
    
    <!-- Flash Messages -->
    <?php if ($message = flash('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
        </div>
    <?php endif; ?>
    
    <?php if ($message = flash('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
        </div>
    <?php endif; ?>
    
    <?php if ($message = flash('warning')): ?>
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($message) ?></span>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main id="main-content" class="container mx-auto px-4 py-8">
        <?= $content ?? '' ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-lg">L</span>
                        </div>
                        <span class="text-xl font-bold">LightCommerce</span>
                    </div>
                    <p class="text-gray-400">
                        Lightweight, innovative, and extensible eCommerce platform built with modern web technologies.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/products" class="hover:text-white">Products</a></li>
                        <li><a href="/categories" class="hover:text-white">Categories</a></li>
                        <li><a href="/about" class="hover:text-white">About Us</a></li>
                        <li><a href="/contact" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>
                
                <!-- Customer Service -->
                <div>
                    <h3 class="font-semibold mb-4">Customer Service</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/account" class="hover:text-white">My Account</a></li>
                        <li><a href="/cart" class="hover:text-white">Shopping Cart</a></li>
                        <li><a href="/wishlist" class="hover:text-white">Wishlist</a></li>
                        <li><a href="/compare" class="hover:text-white">Compare</a></li>
                    </ul>
                </div>
                
                <!-- Connect -->
                <div>
                    <h3 class="font-semibold mb-4">Connect</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; <?= date('Y') ?> LightCommerce. All rights reserved. Built with modern web technologies.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // CSRF Token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Accessibility Toggle
        function toggleAccessibility() {
            document.documentElement.classList.toggle('high-contrast');
            localStorage.setItem('accessibility', document.documentElement.classList.contains('high-contrast'));
        }
        
        // Load accessibility preference
        if (localStorage.getItem('accessibility') === 'true') {
            document.documentElement.classList.add('high-contrast');
        }
        
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
        
        // Cart count update
        function updateCartCount() {
            fetch('/api/cart/count', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('cart-count').textContent = data.count;
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
        }
        
        // Add to cart function
        function addToCart(productId, quantity = 1, variantId = null) {
            const data = {
                product_id: productId,
                quantity: quantity,
                csrf_token: csrfToken
            };
            
            if (variantId) {
                data.variant_id = variantId;
            }
            
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCartCount();
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showNotification('Error adding product to cart', 'error');
            });
        }
        
        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-blue-500'
            } text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Initialize cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
        
        // PWA Installation
        let deferredPrompt;
        
        window.addEventListener('beforeinstallprompt', (e) => {
            deferredPrompt = e;
            // Show install button
        });
    </script>
    
    <!-- Service Worker for PWA -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful');
                })
                .catch(function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
</body>
</html>