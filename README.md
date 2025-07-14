# Lightweight eCommerce Platform

A modern, innovative, and extensible eCommerce platform built entirely with standard web technologies — HTML5, TailwindCSS, vanilla JavaScript, PHP 8+, and SQLite — following a clean MVC (Model-View-Controller) architecture.

## 🚀 Features

### Core eCommerce Features
- **Product Management**: Complete product catalog with categories, images, and attributes
- **Shopping Cart**: Persistent cart with session and user-based storage
- **User Authentication**: Secure registration, login, and password management
- **Order Processing**: Complete order lifecycle from cart to delivery
- **Payment Integration**: Ready for payment gateway integration
- **Inventory Management**: Stock tracking and low stock alerts

### Advanced Features
- **Subscriptions**: Recurring product subscriptions with flexible scheduling
- **Product Comparisons**: Side-by-side product comparison tool
- **Wishlist System**: Personal wishlists with sharing capabilities
- **Review System**: Customer reviews and ratings with moderation
- **Gamification**: Points system and user levels
- **Seller Dashboard**: Multi-vendor marketplace support

### Technical Features
- **PWA Support**: Progressive Web App with offline capabilities
- **Responsive Design**: Mobile-first design with TailwindCSS
- **Accessibility**: WCAG compliant with accessibility toggles
- **SEO Optimized**: Clean URLs, meta tags, and structured data
- **Performance Focused**: Optimized database queries and caching
- **Security**: CSRF protection, input validation, and secure headers

### Admin Features
- **Admin Dashboard**: Comprehensive admin interface
- **Analytics**: Sales reports and user analytics
- **Content Management**: Easy product and category management
- **User Management**: Customer and seller account management
- **Settings Panel**: Configurable site settings

## 🛠 Technology Stack

- **Backend**: PHP 8+ with custom MVC framework
- **Database**: SQLite (easily switchable to MySQL/PostgreSQL)
- **Frontend**: HTML5, TailwindCSS, vanilla JavaScript
- **PWA**: Service Workers, Web App Manifest
- **Security**: CSRF tokens, password hashing, input validation
- **Performance**: Query optimization, asset compression, caching

## 📋 Requirements

- PHP 8.0 or higher
- SQLite3 extension
- mod_rewrite enabled (Apache) or equivalent (Nginx)
- 50MB disk space (minimum)
- Shared hosting compatible (no CLI required)

## 🚀 Installation

### 1. Download and Extract
```bash
# Clone or download the project
git clone https://github.com/yourusername/lightweight-ecommerce.git
cd lightweight-ecommerce
```

### 2. Set Permissions
```bash
# Create necessary directories
mkdir -p database public/uploads public/icons

# Set write permissions
chmod 755 database public/uploads public/icons
chmod 644 database/ecommerce.db
```

### 3. Configure Web Server

#### Apache (.htaccess included)
The `.htaccess` file is already included and configured for Apache.

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/lightweight-ecommerce;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security: Prevent access to sensitive files
    location ~ /\. {
        deny all;
    }

    location ~ \.(env|log|sql|db)$ {
        deny all;
    }
}
```

### 4. Create Admin User
The system will automatically create the database and tables on first run. To create an admin user:

1. Register a normal account
2. Manually update the user role in the database:
```sql
UPDATE users SET role = 'admin' WHERE email = 'your-email@example.com';
```

### 5. Configure Settings
Access the admin panel at `/admin` and configure:
- Site name and description
- Currency and tax settings
- Shipping costs and thresholds
- Feature toggles (reviews, wishlist, subscriptions, etc.)

## 📁 Project Structure

```
lightweight-ecommerce/
├── app/
│   ├── Controllers/          # MVC Controllers
│   ├── Core/                # Core framework classes
│   ├── Models/              # Data models (future)
│   └── Views/               # Template files
├── database/                # SQLite database
├── public/                  # Public assets
│   ├── uploads/            # Product images
│   └── icons/              # PWA icons
├── .htaccess               # URL rewriting
├── index.php               # Entry point
└── README.md              # This file
```

## 🔧 Configuration

### Database Configuration
The system uses SQLite by default. To switch to MySQL/PostgreSQL:

1. Update the Database class in `app/Core/Database.php`
2. Create the database tables using the provided schema
3. Update connection parameters

### Site Settings
All site settings are stored in the `settings` table and can be managed through the admin panel:

- `site_name`: Your store name
- `currency`: Currency code (USD, EUR, etc.)
- `tax_rate`: Default tax rate
- `free_shipping_threshold`: Minimum order for free shipping
- `enable_reviews`: Toggle review system
- `enable_wishlist`: Toggle wishlist feature
- `enable_subscriptions`: Toggle subscription feature
- `enable_gamification`: Toggle points system

## 🎨 Customization

### Themes
The system uses TailwindCSS for styling. To customize:

1. Modify the TailwindCSS configuration in the layout file
2. Update color schemes in `app/Views/layouts/default.php`
3. Add custom CSS in the `<style>` section

### Adding Features
The modular architecture makes it easy to add new features:

1. Create a new controller in `app/Controllers/`
2. Add routes in `app/routes.php`
3. Create views in `app/Views/`
4. Update the database schema if needed

## 🔒 Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Input Validation**: Comprehensive input sanitization
- **Password Hashing**: Secure password storage with PHP's password_hash()
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Output escaping in templates
- **Security Headers**: Comprehensive security headers in .htaccess

## 📱 PWA Features

The platform includes Progressive Web App features:

- **Service Worker**: Offline caching and background sync
- **Web App Manifest**: Installable app experience
- **Responsive Design**: Works on all device sizes
- **Fast Loading**: Optimized for performance

## 🚀 Performance Optimizations

- **Database Optimization**: Efficient queries with proper indexing
- **Asset Compression**: Gzip compression for all assets
- **Caching**: Browser caching for static assets
- **Lazy Loading**: Images and content loaded on demand
- **Minification**: CSS and JS minification (can be added)

## 🔧 Development

### Adding New Controllers
```php
<?php
namespace Controllers;

use Core\Controller;

class MyController extends Controller
{
    public function index()
    {
        $this->render('my/index', [
            'data' => $this->getData()
        ]);
    }
}
```

### Adding New Routes
```php
// In app/routes.php
$router->add('my-route', ['controller' => 'MyController', 'action' => 'index']);
```

### Database Queries
```php
// Using the PDO instance
$stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
```

## 📊 Database Schema

The system includes comprehensive database tables:

- **users**: Customer, admin, and seller accounts
- **products**: Product catalog with attributes
- **categories**: Product categorization
- **orders**: Order management
- **cart_items**: Shopping cart
- **wishlist_items**: User wishlists
- **product_reviews**: Customer reviews
- **subscriptions**: Recurring orders
- **user_points**: Gamification system
- **settings**: Site configuration

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Create an issue on GitHub
- Check the documentation
- Review the code comments

## 🎯 Roadmap

- [ ] Multi-language support
- [ ] Advanced payment gateways
- [ ] Email marketing integration
- [ ] Advanced analytics
- [ ] Mobile app (React Native)
- [ ] API for third-party integrations
- [ ] Advanced inventory management
- [ ] Dropshipping support

---

**Built with ❤️ for modern eCommerce needs**