<?php

// Sample data seeder for LightCommerce
// This file populates the database with demo content

require_once __DIR__ . '/../../bootstrap.php';

function seedSampleData() {
    $db = \Core\Database\Database::getInstance();
    
    echo "🌱 Seeding sample data...\n";
    
    // Check if data already exists
    $existingUsers = $db->fetchOne("SELECT COUNT(*) as count FROM users");
    if ($existingUsers['count'] > 0) {
        echo "✅ Sample data already exists. Skipping seeding.\n";
        return;
    }
    
    // Create sample users
    echo "👥 Creating sample users...\n";
    
    // Admin user
    $adminUser = new \App\Models\User();
    $adminUser->fill([
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin@lightcommerce.com',
        'role' => 'admin'
    ]);
    $adminUser->hashPassword('admin123');
    $adminUser->email_verified_at = date('Y-m-d H:i:s');
    $adminUser->save();
    echo "  ✓ Admin user created (admin@lightcommerce.com / admin123)\n";
    
    // Sample customer
    $customer = new \App\Models\User();
    $customer->fill([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'customer@example.com',
        'role' => 'customer'
    ]);
    $customer->hashPassword('customer123');
    $customer->email_verified_at = date('Y-m-d H:i:s');
    $customer->points = 1500;
    $customer->total_spent = 250.00;
    $customer->save();
    echo "  ✓ Customer user created (customer@example.com / customer123)\n";
    
    // Sample seller
    $seller = new \App\Models\User();
    $seller->fill([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'seller@example.com',
        'role' => 'seller'
    ]);
    $seller->hashPassword('seller123');
    $seller->email_verified_at = date('Y-m-d H:i:s');
    $seller->save();
    echo "  ✓ Seller user created (seller@example.com / seller123)\n";
    
    // Get categories (already created by migration)
    $categories = \App\Models\Category::all();
    $categoryMap = [];
    foreach ($categories as $category) {
        $categoryMap[$category->slug] = $category->id;
    }
    
    // Create sample products
    echo "📦 Creating sample products...\n";
    
    $sampleProducts = [
        [
            'name' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro',
            'description' => 'The most advanced iPhone ever with titanium design, powerful A17 Pro chip, and incredible camera capabilities.',
            'short_description' => 'Latest iPhone with titanium design and A17 Pro chip',
            'sku' => 'IPHONE15PRO',
            'price' => 999.00,
            'sale_price' => 899.00,
            'stock_quantity' => 50,
            'category_id' => $categoryMap['electronics'],
            'seller_id' => $seller->id,
            'is_featured' => true,
            'status' => 'published',
            'rating_average' => 4.8,
            'rating_count' => 127,
            'views_count' => 1543,
            'purchases_count' => 89
        ],
        [
            'name' => 'MacBook Air M3',
            'slug' => 'macbook-air-m3',
            'description' => 'Supercharged by the M3 chip, MacBook Air delivers exceptional performance and up to 18 hours of battery life.',
            'short_description' => 'Powerful laptop with M3 chip and all-day battery',
            'sku' => 'MACBOOK-AIR-M3',
            'price' => 1299.00,
            'stock_quantity' => 25,
            'category_id' => $categoryMap['electronics'],
            'seller_id' => $seller->id,
            'is_featured' => true,
            'status' => 'published',
            'rating_average' => 4.9,
            'rating_count' => 89,
            'views_count' => 987,
            'purchases_count' => 34
        ],
        [
            'name' => 'Premium Cotton T-Shirt',
            'slug' => 'premium-cotton-tshirt',
            'description' => 'Made from 100% organic cotton, this comfortable t-shirt is perfect for everyday wear. Available in multiple colors and sizes.',
            'short_description' => '100% organic cotton t-shirt in multiple colors',
            'sku' => 'TSHIRT-COTTON-001',
            'price' => 29.99,
            'sale_price' => 24.99,
            'stock_quantity' => 100,
            'category_id' => $categoryMap['clothing'],
            'seller_id' => $seller->id,
            'status' => 'published',
            'rating_average' => 4.5,
            'rating_count' => 234,
            'views_count' => 2156,
            'purchases_count' => 178
        ],
        [
            'name' => 'The Design of Everyday Things',
            'slug' => 'design-everyday-things',
            'description' => 'A classic book on design principles and how good design shapes our interaction with the world around us.',
            'short_description' => 'Essential book on design principles and user experience',
            'sku' => 'BOOK-DESIGN-001',
            'price' => 18.99,
            'stock_quantity' => 75,
            'category_id' => $categoryMap['books'],
            'seller_id' => $seller->id,
            'status' => 'published',
            'rating_average' => 4.7,
            'rating_count' => 156,
            'views_count' => 678,
            'purchases_count' => 123
        ],
        [
            'name' => 'Yoga Mat Pro',
            'slug' => 'yoga-mat-pro',
            'description' => 'Non-slip yoga mat made from eco-friendly materials. Perfect for all types of yoga and fitness exercises.',
            'short_description' => 'Eco-friendly non-slip yoga mat for all fitness levels',
            'sku' => 'YOGA-MAT-PRO',
            'price' => 49.99,
            'stock_quantity' => 60,
            'category_id' => $categoryMap['sports'],
            'seller_id' => $seller->id,
            'is_featured' => true,
            'status' => 'published',
            'rating_average' => 4.6,
            'rating_count' => 78,
            'views_count' => 543,
            'purchases_count' => 45
        ],
        [
            'name' => 'Wireless Bluetooth Headphones',
            'slug' => 'wireless-bluetooth-headphones',
            'description' => 'Premium wireless headphones with active noise cancellation, 30-hour battery life, and superior sound quality.',
            'short_description' => 'Premium wireless headphones with noise cancellation',
            'sku' => 'HEADPHONES-BT-001',
            'price' => 199.99,
            'sale_price' => 149.99,
            'stock_quantity' => 40,
            'category_id' => $categoryMap['electronics'],
            'seller_id' => $seller->id,
            'is_featured' => true,
            'status' => 'published',
            'rating_average' => 4.4,
            'rating_count' => 92,
            'views_count' => 1234,
            'purchases_count' => 67
        ],
        [
            'name' => 'Smart Home Security Camera',
            'slug' => 'smart-home-security-camera',
            'description' => '1080p HD security camera with night vision, motion detection, and smartphone app control.',
            'short_description' => 'Smart security camera with app control and night vision',
            'sku' => 'CAMERA-SEC-001',
            'price' => 89.99,
            'stock_quantity' => 35,
            'category_id' => $categoryMap['electronics'],
            'seller_id' => $seller->id,
            'status' => 'published',
            'rating_average' => 4.3,
            'rating_count' => 145,
            'views_count' => 876,
            'purchases_count' => 98
        ],
        [
            'name' => 'Organic Face Moisturizer',
            'slug' => 'organic-face-moisturizer',
            'description' => 'Natural moisturizer with organic ingredients. Suitable for all skin types, providing deep hydration and nourishment.',
            'short_description' => 'Natural moisturizer with organic ingredients for all skin types',
            'sku' => 'MOISTURIZER-ORG-001',
            'price' => 34.99,
            'stock_quantity' => 80,
            'category_id' => $categoryMap['beauty'],
            'seller_id' => $seller->id,
            'status' => 'published',
            'rating_average' => 4.8,
            'rating_count' => 203,
            'views_count' => 1567,
            'purchases_count' => 156
        ]
    ];
    
    foreach ($sampleProducts as $productData) {
        $product = \App\Models\Product::create($productData);
        echo "  ✓ Created product: {$product->name}\n";
        
        // Add some attributes for demonstration
        if ($product->slug === 'premium-cotton-tshirt') {
            $product->addAttribute('Material', '100% Organic Cotton');
            $product->addAttribute('Care Instructions', 'Machine wash cold, tumble dry low');
            $product->addAttribute('Fit', 'Regular');
        } elseif ($product->slug === 'wireless-bluetooth-headphones') {
            $product->addAttribute('Battery Life', '30 hours');
            $product->addAttribute('Connectivity', 'Bluetooth 5.0');
            $product->addAttribute('Noise Cancellation', 'Active');
        }
    }
    
    // Create sample reviews
    echo "⭐ Creating sample reviews...\n";
    
    $products = \App\Models\Product::all();
    $reviewTexts = [
        ['rating' => 5, 'title' => 'Excellent product!', 'comment' => 'This product exceeded my expectations. Great quality and fast shipping.'],
        ['rating' => 4, 'title' => 'Very good', 'comment' => 'Good quality product, exactly as described. Would recommend to others.'],
        ['rating' => 5, 'title' => 'Love it!', 'comment' => 'Amazing product! Works perfectly and looks great. Will definitely buy again.'],
        ['rating' => 4, 'title' => 'Good value', 'comment' => 'Great value for money. Good quality and arrived quickly.'],
        ['rating' => 5, 'title' => 'Perfect!', 'comment' => 'Exactly what I was looking for. High quality and great customer service.']
    ];
    
    foreach ($products as $product) {
        // Add 2-3 reviews per product
        $numReviews = rand(2, 3);
        for ($i = 0; $i < $numReviews; $i++) {
            $reviewData = $reviewTexts[array_rand($reviewTexts)];
            $review = \App\Models\Review::create([
                'product_id' => $product->id,
                'user_id' => $customer->id,
                'rating' => $reviewData['rating'],
                'title' => $reviewData['title'],
                'comment' => $reviewData['comment'],
                'is_verified' => true,
                'is_approved' => true
            ]);
        }
        
        // Update product rating
        $product->updateRating();
    }
    echo "  ✓ Created sample reviews for all products\n";
    
    // Award some badges to the customer
    echo "🏆 Awarding sample badges...\n";
    
    $badges = \App\Models\Badge::all();
    foreach ($badges as $badge) {
        if (in_array($badge->name, ['Welcome Badge', 'First Purchase', 'Reviewer'])) {
            $customer->awardBadge($badge->id);
            echo "  ✓ Awarded '{$badge->name}' badge to customer\n";
        }
    }
    
    // Create a sample wishlist
    echo "❤️ Creating sample wishlist...\n";
    
    $wishlist = \App\Models\Wishlist::getDefaultForUser($customer->id);
    $randomProducts = array_slice($products, 0, 3);
    foreach ($randomProducts as $product) {
        $wishlist->addItem($product->id, null, 'Added during demo setup');
    }
    echo "  ✓ Added {count($randomProducts)} products to customer's wishlist\n";
    
    // Create sample point transactions
    echo "💎 Creating sample point transactions...\n";
    
    $pointTransactions = [
        ['points' => 100, 'type' => 'earned', 'reason' => 'Welcome bonus'],
        ['points' => 500, 'type' => 'earned', 'reason' => 'First purchase'],
        ['points' => 50, 'type' => 'earned', 'reason' => 'Product review'],
        ['points' => 25, 'type' => 'earned', 'reason' => 'Social media share'],
        ['points' => -200, 'type' => 'spent', 'reason' => 'Discount redemption']
    ];
    
    foreach ($pointTransactions as $transactionData) {
        $transaction = \App\Models\PointTransaction::create([
            'user_id' => $customer->id,
            'points' => $transactionData['points'],
            'type' => $transactionData['type'],
            'reason' => $transactionData['reason']
        ]);
    }
    echo "  ✓ Created sample point transactions\n";
    
    echo "✅ Sample data seeding completed successfully!\n\n";
    
    echo "🎉 Demo Accounts Created:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "👑 Admin:    admin@lightcommerce.com    / admin123\n";
    echo "👤 Customer: customer@example.com       / customer123\n";
    echo "🏪 Seller:   seller@example.com         / seller123\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n📊 Sample Data Created:\n";
    echo "• " . count($sampleProducts) . " products across all categories\n";
    echo "• Product reviews and ratings\n";
    echo "• User badges and points\n";
    echo "• Wishlist with sample items\n";
    echo "• Point transaction history\n\n";
    echo "🚀 Your LightCommerce platform is ready to explore!\n";
}

// Run seeder if called directly
if (php_sapi_name() === 'cli') {
    seedSampleData();
}