<?php

return [
    'name' => 'LightCommerce',
    'description' => 'Lightweight, innovative, and extensible eCommerce platform',
    'version' => '1.0.0',
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'debug' => $_ENV['APP_DEBUG'] ?? true,
    'timezone' => 'UTC',
    
    'database' => [
        'driver' => 'sqlite',
        'path' => __DIR__ . '/../storage/database.sqlite'
    ],
    
    'security' => [
        'hash_algo' => PASSWORD_DEFAULT,
        'session_lifetime' => 120, // minutes
        'csrf_protection' => true,
        'rate_limiting' => [
            'enabled' => true,
            'max_attempts' => 60,
            'window' => 60 // seconds
        ]
    ],
    
    'mail' => [
        'driver' => 'smtp',
        'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from' => [
            'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@lightcommerce.com',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? 'LightCommerce'
        ]
    ],
    
    'cache' => [
        'default' => 'file',
        'stores' => [
            'file' => [
                'driver' => 'file',
                'path' => __DIR__ . '/../storage/cache'
            ]
        ]
    ],
    
    'upload' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'],
        'path' => __DIR__ . '/../public/uploads'
    ],
    
    'features' => [
        'subscriptions' => true,
        'wishlist' => true,
        'comparisons' => true,
        'gamification' => true,
        'reviews' => true,
        'multi_seller' => true,
        'pwa' => true,
        'accessibility' => true
    ],
    
    'seo' => [
        'meta_title' => 'LightCommerce - Modern eCommerce Platform',
        'meta_description' => 'Lightweight, innovative, and extensible eCommerce platform built with modern web technologies',
        'meta_keywords' => 'ecommerce, online store, shopping cart, products',
        'og_image' => '/assets/images/og-image.jpg',
        'sitemap_enabled' => true,
        'robots_txt_enabled' => true
    ],
    
    'payment' => [
        'default_currency' => 'USD',
        'supported_currencies' => ['USD', 'EUR', 'GBP', 'CAD'],
        'payment_methods' => [
            'stripe' => [
                'enabled' => false,
                'public_key' => $_ENV['STRIPE_PUBLIC_KEY'] ?? '',
                'secret_key' => $_ENV['STRIPE_SECRET_KEY'] ?? ''
            ],
            'paypal' => [
                'enabled' => false,
                'client_id' => $_ENV['PAYPAL_CLIENT_ID'] ?? '',
                'client_secret' => $_ENV['PAYPAL_CLIENT_SECRET'] ?? '',
                'sandbox' => $_ENV['PAYPAL_SANDBOX'] ?? true
            ]
        ]
    ],
    
    'gamification' => [
        'points_enabled' => true,
        'badges_enabled' => true,
        'leaderboard_enabled' => true,
        'reward_actions' => [
            'register' => 100,
            'first_purchase' => 500,
            'review_product' => 50,
            'share_product' => 25,
            'refer_friend' => 1000
        ]
    ]
];