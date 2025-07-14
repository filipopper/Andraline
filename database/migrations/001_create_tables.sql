-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'customer',
    avatar VARCHAR(255),
    phone VARCHAR(20),
    birth_date DATE,
    email_verified_at DATETIME,
    email_verification_token VARCHAR(100),
    password_reset_token VARCHAR(100),
    password_reset_expires DATETIME,
    points INTEGER DEFAULT 0,
    total_spent DECIMAL(10,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    accessibility_preferences TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    parent_id INTEGER,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    short_description TEXT,
    sku VARCHAR(100) UNIQUE NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    cost_price DECIMAL(10,2),
    stock_quantity INTEGER DEFAULT 0,
    manage_stock BOOLEAN DEFAULT 1,
    stock_status VARCHAR(20) DEFAULT 'in_stock',
    weight DECIMAL(8,2),
    dimensions VARCHAR(100),
    category_id INTEGER NOT NULL,
    seller_id INTEGER,
    featured_image VARCHAR(255),
    gallery TEXT, -- JSON array of image URLs
    is_featured BOOLEAN DEFAULT 0,
    is_digital BOOLEAN DEFAULT 0,
    download_url VARCHAR(255),
    status VARCHAR(20) DEFAULT 'draft',
    visibility VARCHAR(20) DEFAULT 'public',
    rating_average DECIMAL(3,2) DEFAULT 0,
    rating_count INTEGER DEFAULT 0,
    views_count INTEGER DEFAULT 0,
    purchases_count INTEGER DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (seller_id) REFERENCES users(id)
);

-- Product attributes table
CREATE TABLE IF NOT EXISTS product_attributes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    name VARCHAR(100) NOT NULL,
    value TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Product variants table
CREATE TABLE IF NOT EXISTS product_variants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    sku VARCHAR(100) UNIQUE NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2),
    stock_quantity INTEGER DEFAULT 0,
    attributes TEXT, -- JSON object with variant attributes
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Carts table
CREATE TABLE IF NOT EXISTS carts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    session_id VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Cart items table
CREATE TABLE IF NOT EXISTS cart_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    cart_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    variant_id INTEGER,
    quantity INTEGER NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_number VARCHAR(100) UNIQUE NOT NULL,
    user_id INTEGER,
    status VARCHAR(20) DEFAULT 'pending',
    payment_status VARCHAR(20) DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_id VARCHAR(255),
    currency VARCHAR(3) DEFAULT 'USD',
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    shipping_amount DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    billing_address TEXT, -- JSON object
    shipping_address TEXT, -- JSON object
    notes TEXT,
    shipped_at DATETIME,
    delivered_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    variant_id INTEGER,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(100) NOT NULL,
    quantity INTEGER NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (variant_id) REFERENCES product_variants(id)
);

-- Subscriptions table
CREATE TABLE IF NOT EXISTS subscriptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    variant_id INTEGER,
    status VARCHAR(20) DEFAULT 'active',
    billing_cycle VARCHAR(20) NOT NULL, -- monthly, yearly, etc.
    price DECIMAL(10,2) NOT NULL,
    next_billing_date DATE NOT NULL,
    started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    paused_at DATETIME,
    cancelled_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (variant_id) REFERENCES product_variants(id)
);

-- Wishlists table
CREATE TABLE IF NOT EXISTS wishlists (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL DEFAULT 'My Wishlist',
    is_public BOOLEAN DEFAULT 0,
    share_token VARCHAR(100) UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Wishlist items table
CREATE TABLE IF NOT EXISTS wishlist_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    wishlist_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    variant_id INTEGER,
    notes TEXT,
    priority INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wishlist_id) REFERENCES wishlists(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE
);

-- Product comparisons table
CREATE TABLE IF NOT EXISTS product_comparisons (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    session_id VARCHAR(255),
    product_ids TEXT, -- JSON array of product IDs
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    order_id INTEGER,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255),
    comment TEXT,
    is_verified BOOLEAN DEFAULT 0,
    is_approved BOOLEAN DEFAULT 0,
    helpful_count INTEGER DEFAULT 0,
    reported_count INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

-- Review helpful votes table
CREATE TABLE IF NOT EXISTS review_votes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    review_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    is_helpful BOOLEAN NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(review_id, user_id)
);

-- Gamification badges table
CREATE TABLE IF NOT EXISTS badges (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(255),
    points_required INTEGER DEFAULT 0,
    criteria TEXT, -- JSON object describing achievement criteria
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- User badges table
CREATE TABLE IF NOT EXISTS user_badges (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    badge_id INTEGER NOT NULL,
    earned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
    UNIQUE(user_id, badge_id)
);

-- Points transactions table
CREATE TABLE IF NOT EXISTS point_transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    points INTEGER NOT NULL,
    type VARCHAR(20) NOT NULL, -- earned, spent, expired
    reason VARCHAR(255),
    reference_type VARCHAR(50), -- order, review, referral, etc.
    reference_id INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Coupons table
CREATE TABLE IF NOT EXISTS coupons (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(100) UNIQUE NOT NULL,
    type VARCHAR(20) NOT NULL, -- percentage, fixed_amount
    value DECIMAL(10,2) NOT NULL,
    minimum_amount DECIMAL(10,2) DEFAULT 0,
    maximum_uses INTEGER,
    used_count INTEGER DEFAULT 0,
    user_limit INTEGER DEFAULT 1,
    starts_at DATETIME,
    expires_at DATETIME,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Coupon uses table
CREATE TABLE IF NOT EXISTS coupon_uses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    coupon_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    order_id INTEGER NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    used_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    key VARCHAR(255) UNIQUE NOT NULL,
    value TEXT,
    type VARCHAR(20) DEFAULT 'string',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Sessions table
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id INTEGER,
    ip_address VARCHAR(45),
    user_agent TEXT,
    payload TEXT,
    last_activity INTEGER,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Activity log table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    action VARCHAR(100) NOT NULL,
    resource_type VARCHAR(100),
    resource_id INTEGER,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_seller ON products(seller_id);
CREATE INDEX IF NOT EXISTS idx_products_status ON products(status);
CREATE INDEX IF NOT EXISTS idx_products_featured ON products(is_featured);
CREATE INDEX IF NOT EXISTS idx_orders_user ON orders(user_id);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(status);
CREATE INDEX IF NOT EXISTS idx_reviews_product ON reviews(product_id);
CREATE INDEX IF NOT EXISTS idx_reviews_user ON reviews(user_id);
CREATE INDEX IF NOT EXISTS idx_cart_items_cart ON cart_items(cart_id);
CREATE INDEX IF NOT EXISTS idx_wishlist_items_wishlist ON wishlist_items(wishlist_id);
CREATE INDEX IF NOT EXISTS idx_point_transactions_user ON point_transactions(user_id);

-- Insert default categories
INSERT OR IGNORE INTO categories (name, slug, description) VALUES
('Electronics', 'electronics', 'Electronic devices and gadgets'),
('Clothing', 'clothing', 'Fashion and apparel'),
('Books', 'books', 'Books and literature'),
('Home & Garden', 'home-garden', 'Home improvement and gardening'),
('Sports', 'sports', 'Sports and fitness equipment'),
('Beauty', 'beauty', 'Beauty and personal care'),
('Toys', 'toys', 'Toys and games'),
('Automotive', 'automotive', 'Automotive parts and accessories');

-- Insert default badges
INSERT OR IGNORE INTO badges (name, description, icon, points_required) VALUES
('Welcome Badge', 'Awarded for joining the platform', '🎉', 0),
('First Purchase', 'Made your first purchase', '🛍️', 100),
('Reviewer', 'Left your first product review', '⭐', 150),
('Social Butterfly', 'Shared a product on social media', '🦋', 25),
('Loyal Customer', 'Made 10 purchases', '👑', 1000),
('Big Spender', 'Spent over $1000', '💎', 2000);

-- Insert default settings
INSERT OR IGNORE INTO settings (key, value, type) VALUES
('site_name', 'LightCommerce', 'string'),
('site_description', 'Modern eCommerce Platform', 'string'),
('currency', 'USD', 'string'),
('tax_rate', '0.10', 'decimal'),
('shipping_cost', '9.99', 'decimal'),
('free_shipping_threshold', '50.00', 'decimal'),
('enable_reviews', '1', 'boolean'),
('enable_wishlist', '1', 'boolean'),
('enable_comparisons', '1', 'boolean'),
('enable_points', '1', 'boolean'),
('points_per_dollar', '10', 'integer'),
('maintenance_mode', '0', 'boolean');