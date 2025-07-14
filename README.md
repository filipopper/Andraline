# LightCommerce - Modern eCommerce Platform

A lightweight, innovative, and extensible eCommerce platform built entirely with standard web technologies — HTML5, TailwindCSS, vanilla JavaScript, PHP 8+, and SQLite — following a clean MVC (Model-View-Controller) architecture.

## 🚀 Features

### Core eCommerce Functionality
- **Product Management** - Complete product catalog with categories, variants, and attributes
- **Shopping Cart** - Session-based cart with quantity management and pricing calculations
- **Order Management** - Full order lifecycle from creation to delivery
- **User Authentication** - Secure registration, login, and password reset
- **Payment Integration** - Ready for Stripe, PayPal, and other payment gateways

### Advanced Features
- **📋 Subscriptions** - Recurring billing for subscription products
- **❤️ Wishlist** - Save and share favorite products with social features
- **⚖️ Product Comparison** - Side-by-side product comparison tool
- **🎮 Gamification** - Points system, badges, and user achievements
- **⭐ Reviews & Ratings** - Product reviews with helpful voting system
- **🏪 Multi-Seller** - Support for multiple sellers/vendors
- **📱 PWA Ready** - Progressive Web App with offline functionality
- **♿ Accessibility** - Built-in accessibility features and high-contrast mode

### Technical Excellence
- **🏗️ MVC Architecture** - Clean, maintainable code structure
- **🚀 Performance** - Optimized for speed and scalability
- **🔒 Security** - CSRF protection, secure authentication, SQL injection prevention
- **📱 Responsive** - Mobile-first design with TailwindCSS
- **🌐 SEO Optimized** - Meta tags, sitemap, robots.txt
- **🔧 No Build Tools** - Works without npm, Webpack, or complex tooling

## 🛠️ Technology Stack

- **Frontend**: HTML5, TailwindCSS, Vanilla JavaScript
- **Backend**: PHP 8+, SQLite
- **Architecture**: MVC with PSR-4 autoloading
- **Styling**: TailwindCSS (CDN)
- **Database**: SQLite (no server required)
- **Session Management**: PHP Sessions
- **Caching**: File-based caching

## 📦 Installation

### Requirements
- PHP 8.0 or higher
- SQLite extension for PHP
- Web server (Apache, Nginx, or PHP built-in server)

### Quick Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/lightcommerce.git
   cd lightcommerce
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Set permissions**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 public/uploads/
   ```

4. **Start the development server**
   ```bash
   php -S localhost:8000 -t public
   ```

5. **Visit your site**
   Open http://localhost:8000 in your browser

The database will be automatically created and seeded with sample data on first run.

## 🏗️ Project Structure

```
lightcommerce/
├── app/
│   ├── Controllers/         # Application controllers
│   ├── Models/             # Data models
│   ├── Views/              # View templates
│   ├── Middleware/         # Route middleware
│   └── Services/           # Business logic services
├── core/
│   ├── Router/             # Custom routing system
│   ├── Database/           # Database abstraction
│   ├── Auth/               # Authentication helpers
│   └── Cache/              # Caching system
├── config/
│   └── app.php             # Application configuration
├── database/
│   ├── migrations/         # Database schema
│   └── seeds/              # Sample data
├── public/
│   ├── assets/             # Static assets
│   ├── uploads/            # User uploads
│   ├── index.php           # Application entry point
│   ├── manifest.json       # PWA manifest
│   └── service-worker.js   # Service worker for PWA
├── routes/
│   └── web.php             # Route definitions
├── storage/
│   ├── cache/              # Application cache
│   ├── logs/               # Log files
│   └── sessions/           # Session storage
├── bootstrap.php           # Application bootstrap
└── composer.json           # Composer configuration
```

## 🎯 Key Components

### MVC Architecture

**Controllers** handle HTTP requests and business logic:
```php
class ProductController extends Controller
{
    public function show(string $slug): void
    {
        $product = Product::findBySlug($slug);
        $this->view('products/show', compact('product'));
    }
}
```

**Models** manage data and business rules:
```php
class Product extends Model
{
    protected array $fillable = ['name', 'price', 'description'];
    
    public function getCategory(): ?Category
    {
        return Category::find($this->category_id);
    }
}
```

**Views** render HTML templates:
```php
<div class="product-card">
    <h3><?= htmlspecialchars($product->name) ?></h3>
    <p><?= format_currency($product->price) ?></p>
</div>
```

### Custom Router

Clean, Laravel-inspired routing:
```php
$router->get('/products/{slug}', 'ProductController@show');
$router->post('/cart/add', 'CartController@add', ['Auth']);
```

### Database Layer

ActiveRecord-style models with SQLite:
```php
$products = Product::where('category_id', '=', 1);
$product = Product::create(['name' => 'iPhone', 'price' => 999]);
```

## 🛍️ eCommerce Features

### Product Management
- Hierarchical categories
- Product variants (size, color, etc.)
- Custom attributes
- Image galleries
- Stock management
- SEO-friendly URLs

### Shopping Experience
- **Smart Cart**: Persistent across sessions
- **Wishlist**: Save and share favorites
- **Comparison**: Compare up to 4 products
- **Search**: Full-text product search
- **Filters**: Price, category, rating filters

### User Features
- **Account Dashboard**: Order history, profile management
- **Gamification**: Earn points, unlock badges
- **Reviews**: Rate and review products
- **Subscriptions**: Manage recurring orders

### Admin Features
- **Dashboard**: Sales analytics and insights
- **Product Management**: CRUD operations
- **Order Management**: Process and track orders
- **User Management**: Customer and seller accounts
- **Settings**: Configure store settings

## 🎮 Gamification System

### Points System
Users earn points for various activities:
- Registration: 100 points
- First Purchase: 500 points
- Product Review: 50 points
- Social Sharing: 25 points
- Referrals: 1000 points

### Badges
Achievement badges motivate engagement:
- 🎉 **Welcome Badge**: Join the platform
- 🛍️ **First Purchase**: Make your first order
- ⭐ **Reviewer**: Leave your first review
- 🦋 **Social Butterfly**: Share a product
- 👑 **Loyal Customer**: Make 10 purchases
- 💎 **Big Spender**: Spend over $1000

## 📱 Progressive Web App

### PWA Features
- **Offline Support**: Browse cached products offline
- **Install Prompt**: Add to home screen
- **Push Notifications**: Order updates and promotions
- **Background Sync**: Sync cart when back online
- **App-like Experience**: Full-screen, native feel

### Service Worker
Handles caching strategies and offline functionality:
```javascript
// Cache-first strategy for static assets
// Network-first for dynamic content
// Background sync for critical actions
```

## ♿ Accessibility Features

### Built-in Accessibility
- **Keyboard Navigation**: Full keyboard support
- **Screen Reader**: ARIA labels and semantic HTML
- **High Contrast**: Toggle for better visibility
- **Focus Management**: Clear focus indicators
- **Skip Links**: Jump to main content

### Compliance
- WCAG 2.1 AA compliant
- Section 508 compliant
- Semantic HTML structure

## 🔒 Security Features

### Data Protection
- **CSRF Protection**: All forms protected
- **SQL Injection**: Prepared statements
- **XSS Prevention**: Output escaping
- **Session Security**: Secure session handling
- **Password Hashing**: Strong password encryption

### Input Validation
- Server-side validation
- Client-side validation
- File upload restrictions
- Rate limiting

## 🚀 Performance

### Optimization Techniques
- **Lazy Loading**: Images load on demand
- **Caching**: File-based caching system
- **Minification**: Optimized assets
- **CDN Ready**: TailwindCSS from CDN
- **Database Indexing**: Optimized queries

### Benchmarks
- **Page Load**: < 2 seconds
- **Time to Interactive**: < 3 seconds
- **Lighthouse Score**: 90+ across all metrics

## 📊 Analytics & SEO

### SEO Features
- **Meta Tags**: Dynamic meta descriptions
- **Open Graph**: Social media sharing
- **Schema Markup**: Rich snippets
- **Sitemap**: Auto-generated XML sitemap
- **Robots.txt**: Search engine directives
- **Friendly URLs**: Clean, descriptive URLs

### Analytics Ready
- Google Analytics integration
- Custom event tracking
- Conversion tracking
- Performance monitoring

## 🔧 Configuration

### Environment Configuration
Edit `config/app.php` for customization:

```php
return [
    'name' => 'Your Store Name',
    'url' => 'https://yourdomain.com',
    'currency' => 'USD',
    'tax_rate' => 0.10,
    'shipping_cost' => 9.99,
    'features' => [
        'subscriptions' => true,
        'wishlist' => true,
        'gamification' => true,
        // ... more features
    ]
];
```

### Payment Integration
Configure payment gateways:

```php
'payment' => [
    'stripe' => [
        'enabled' => true,
        'public_key' => 'pk_test_...',
        'secret_key' => 'sk_test_...'
    ],
    'paypal' => [
        'enabled' => true,
        'client_id' => 'your_client_id',
        'sandbox' => true
    ]
]
```

## 🔄 API & Extensions

### RESTful API
Built-in API endpoints for:
- Products: CRUD operations
- Cart: Add, update, remove items
- Orders: Create and track orders
- Users: Authentication and management

### Extension Points
- Custom payment gateways
- Shipping providers
- Third-party integrations
- Custom fields and attributes

## 📈 Scaling

### Shared Hosting Ready
- No CLI tools required
- File-based sessions and cache
- SQLite database (no server needed)
- Standard PHP hosting compatible

### Growth Path
1. **Start**: Shared hosting with SQLite
2. **Scale**: VPS with MySQL/PostgreSQL
3. **Enterprise**: Load balancers, CDN, microservices

## 🧪 Testing

### Test Categories
- Unit tests for models and services
- Integration tests for controllers
- Feature tests for user workflows
- Browser tests for UI interactions

### Quality Assurance
- Code coverage reports
- Performance profiling
- Security audits
- Accessibility testing

## 📚 Documentation

### Developer Guides
- [Installation Guide](docs/installation.md)
- [API Documentation](docs/api.md)
- [Customization Guide](docs/customization.md)
- [Deployment Guide](docs/deployment.md)

### User Guides
- [Admin Guide](docs/admin.md)
- [Seller Guide](docs/seller.md)
- [Customer Guide](docs/customer.md)

## 🤝 Contributing

We welcome contributions! Please read our [Contributing Guide](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

### Development Setup
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

### Community
- [GitHub Issues](https://github.com/yourusername/lightcommerce/issues)
- [Discussions](https://github.com/yourusername/lightcommerce/discussions)
- [Discord Community](https://discord.gg/lightcommerce)

### Professional Support
- Email: support@lightcommerce.com
- Priority support available
- Custom development services

## 🎉 Acknowledgments

- **TailwindCSS** for the amazing utility-first CSS framework
- **PHP Community** for excellent resources and support
- **Contributors** who make this project better every day

---

**LightCommerce** - Bridging the gap between minimal tech and million-dollar potential — efficient, maintainable, and made to grow. 🚀