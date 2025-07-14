<?php
/**
 * Lightweight eCommerce Platform - Installation Script
 * Run this script to set up the database and create an admin user
 */

// Check if already installed
if (file_exists('database/ecommerce.db')) {
    die('The eCommerce platform is already installed. Delete database/ecommerce.db to reinstall.');
}

// Create necessary directories
$directories = [
    'database',
    'public/uploads',
    'public/icons',
    'app/Views/layouts',
    'app/Views/partials',
    'app/Views/home',
    'app/Views/products',
    'app/Views/cart',
    'app/Views/auth',
    'app/Views/errors'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }
}

// Include the main application
require_once 'index.php';

echo "✅ Installation completed successfully!\n\n";
echo "🎉 Your Lightweight eCommerce Platform is ready!\n\n";
echo "📋 Next steps:\n";
echo "1. Create an admin user by registering at /register\n";
echo "2. Update the user role in the database:\n";
echo "   UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';\n";
echo "3. Access the admin panel at /admin\n";
echo "4. Configure your site settings\n\n";
echo "🔗 Important URLs:\n";
echo "- Homepage: /\n";
echo "- Admin Panel: /admin\n";
echo "- Login: /login\n";
echo "- Register: /register\n\n";
echo "📚 Documentation: README.md\n";
echo "🛠 Support: Create an issue on GitHub\n\n";
echo "Happy selling! 🚀\n";