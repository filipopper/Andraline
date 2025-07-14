#!/usr/bin/env php
<?php

/**
 * LightCommerce Installation Script
 * 
 * Simple script to set up the eCommerce platform
 */

echo "🚀 LightCommerce Installation Script\n";
echo "=====================================\n\n";

// Check PHP version
echo "🔍 Checking requirements...\n";
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    echo "❌ Error: PHP 8.0 or higher is required. Current version: " . PHP_VERSION . "\n";
    exit(1);
}
echo "✅ PHP version: " . PHP_VERSION . " ✓\n";

// Check SQLite extension
if (!extension_loaded('sqlite3')) {
    echo "❌ Error: SQLite3 extension is required but not installed.\n";
    exit(1);
}
echo "✅ SQLite3 extension ✓\n";

// Check if Composer is available
echo "\n📦 Checking Composer...\n";
if (!file_exists('composer.json')) {
    echo "❌ Error: composer.json not found. Make sure you're in the project root directory.\n";
    exit(1);
}

// Install Composer dependencies
if (!file_exists('vendor/autoload.php')) {
    echo "🔄 Installing Composer dependencies...\n";
    exec('composer install --no-dev --optimize-autoloader', $output, $returnCode);
    if ($returnCode !== 0) {
        echo "❌ Error: Failed to install Composer dependencies.\n";
        echo "Please run 'composer install' manually.\n";
        exit(1);
    }
    echo "✅ Composer dependencies installed ✓\n";
} else {
    echo "✅ Composer dependencies already installed ✓\n";
}

// Create necessary directories
echo "\n📁 Creating directories...\n";
$directories = [
    'storage',
    'storage/cache',
    'storage/logs', 
    'storage/sessions',
    'public/uploads',
    'public/uploads/products',
    'public/uploads/users'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created directory: $dir\n";
        } else {
            echo "❌ Failed to create directory: $dir\n";
        }
    } else {
        echo "✅ Directory exists: $dir\n";
    }
    
    // Set permissions
    if (chmod($dir, 0755)) {
        echo "✅ Set permissions for: $dir\n";
    } else {
        echo "⚠️  Warning: Could not set permissions for: $dir\n";
    }
}

// Initialize the application (this will create database and seed data)
echo "\n🗄️  Initializing database...\n";
try {
    require_once 'bootstrap.php';
    echo "✅ Database initialized successfully ✓\n";
    echo "✅ Sample data loaded ✓\n";
} catch (Exception $e) {
    echo "❌ Error initializing database: " . $e->getMessage() . "\n";
    exit(1);
}

// Create .htaccess if it doesn't exist
if (!file_exists('public/.htaccess')) {
    echo "\n⚠️  Warning: .htaccess file not found in public directory.\n";
    echo "   For Apache servers, this file is needed for clean URLs.\n";
} else {
    echo "\n✅ Apache .htaccess file configured ✓\n";
}

// Installation complete
echo "\n🎉 Installation Complete!\n";
echo "========================\n\n";

echo "🌐 Your LightCommerce platform is ready!\n\n";

echo "📋 Next Steps:\n";
echo "1. Start development server: php -S localhost:8000 -t public\n";
echo "2. Open http://localhost:8000 in your browser\n";
echo "3. Login with demo accounts:\n\n";

echo "   👑 Admin:    admin@lightcommerce.com    / admin123\n";
echo "   👤 Customer: customer@example.com       / customer123\n";
echo "   🏪 Seller:   seller@example.com         / seller123\n\n";

echo "📚 Features to explore:\n";
echo "   • Product catalog with categories\n";
echo "   • Shopping cart and checkout\n";
echo "   • User authentication and profiles\n";
echo "   • Wishlist and product comparison\n";
echo "   • Reviews and ratings system\n";
echo "   • Gamification (points and badges)\n";
echo "   • Admin dashboard and management\n";
echo "   • PWA features (offline support)\n";
echo "   • Accessibility features\n\n";

echo "📖 Documentation: README.md\n";
echo "🐛 Issues: https://github.com/yourusername/lightcommerce/issues\n\n";

echo "✨ Happy selling! ✨\n";

// Run platform verification
echo "\n🔍 Running platform verification...\n";
try {
    // Test database connection
    $db = \Core\Database\Database::getInstance();
    $userCount = $db->fetchOne("SELECT COUNT(*) as count FROM users");
    echo "✅ Database connection: OK (Users: {$userCount['count']})\n";
    
    // Test models
    $productCount = count(\App\Models\Product::all());
    echo "✅ Product models: OK (Products: {$productCount})\n";
    
    // Test categories
    $categoryCount = count(\App\Models\Category::all());
    echo "✅ Category models: OK (Categories: {$categoryCount})\n";
    
    echo "\n🎯 Platform Status: All systems operational!\n";
    
} catch (Exception $e) {
    echo "⚠️  Warning: Verification failed: " . $e->getMessage() . "\n";
    echo "   The platform may still work, but please check the logs.\n";
}

echo "\n🚀 Ready to launch!\n";
?>