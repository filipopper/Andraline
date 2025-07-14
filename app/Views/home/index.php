<?php ob_start(); ?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-primary-600 to-primary-700 text-white py-20 mb-12 rounded-2xl">
    <div class="text-center">
        <h1 class="text-5xl md:text-6xl font-bold mb-6">Welcome to LightCommerce</h1>
        <p class="text-xl md:text-2xl mb-8 opacity-90">Discover amazing products with innovative shopping features</p>
        <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="/products" class="bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Shop Now
            </a>
            <a href="/categories" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary-600 transition-colors">
                Browse Categories
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="mb-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="text-center p-6 bg-white rounded-xl shadow-md">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Smart Wishlist</h3>
            <p class="text-gray-600">Save your favorite products and share wishlists with friends and family</p>
        </div>
        
        <div class="text-center p-6 bg-white rounded-xl shadow-md">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Product Compare</h3>
            <p class="text-gray-600">Compare products side-by-side to make informed purchasing decisions</p>
        </div>
        
        <div class="text-center p-6 bg-white rounded-xl shadow-md">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Rewards Program</h3>
            <p class="text-gray-600">Earn points with every purchase and unlock exclusive badges and rewards</p>
        </div>
    </div>
</section>

<!-- Featured Products -->
<?php if (!empty($featured_products)): ?>
<section class="mb-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Featured Products</h2>
        <a href="/products" class="text-primary-600 hover:text-primary-700 font-medium">View All →</a>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($featured_products as $product): ?>
            <div class="product-card bg-white rounded-xl shadow-md overflow-hidden">
                <div class="relative">
                    <img src="<?= htmlspecialchars($product['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         class="w-full h-48 object-cover">
                    <?php if ($product['sale_price']): ?>
                        <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 text-xs rounded-full">
                            Sale
                        </span>
                    <?php endif; ?>
                    <?php if ($product['is_featured']): ?>
                        <span class="absolute top-2 right-2 bg-primary-500 text-white px-2 py-1 text-xs rounded-full">
                            Featured
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                        <a href="/products/<?= htmlspecialchars($product['slug']) ?>" class="hover:text-primary-600">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </h3>
                    
                    <div class="flex items-center mb-2">
                        <div class="flex items-center">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg class="w-4 h-4 <?= $i <= ($product['rating_average'] ?? 0) ? 'text-yellow-400' : 'text-gray-300' ?> fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            <?php endfor; ?>
                            <span class="text-sm text-gray-600 ml-1">(<?= $product['rating_count'] ?? 0 ?>)</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="price">
                            <?php if ($product['sale_price']): ?>
                                <span class="text-lg font-bold text-primary-600"><?= format_currency($product['sale_price']) ?></span>
                                <span class="text-sm text-gray-500 line-through ml-1"><?= format_currency($product['price']) ?></span>
                            <?php else: ?>
                                <span class="text-lg font-bold text-primary-600"><?= format_currency($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button onclick="addToCart(<?= $product['id'] ?>)" 
                                class="bg-primary-600 hover:bg-primary-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Categories -->
<?php if (!empty($categories)): ?>
<section class="mb-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Shop by Category</h2>
        <a href="/categories" class="text-primary-600 hover:text-primary-700 font-medium">View All →</a>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <?php foreach (array_slice($categories, 0, 8) as $category): ?>
            <div class="group">
                <a href="/categories/<?= htmlspecialchars($category['slug']) ?>" 
                   class="block bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="aspect-square bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
                        <div class="text-4xl">
                            <?php
                            $icons = [
                                'electronics' => '📱',
                                'clothing' => '👕',
                                'books' => '📚',
                                'home-garden' => '🏡',
                                'sports' => '⚽',
                                'beauty' => '💄',
                                'toys' => '🧸',
                                'automotive' => '🚗'
                            ];
                            echo $icons[$category['slug']] ?? '🛍️';
                            ?>
                        </div>
                    </div>
                    <div class="p-4 text-center">
                        <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors">
                            <?= htmlspecialchars($category['name']) ?>
                        </h3>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Latest Products -->
<?php if (!empty($latest_products)): ?>
<section class="mb-16">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Latest Products</h2>
        <a href="/products" class="text-primary-600 hover:text-primary-700 font-medium">View All →</a>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach (array_slice($latest_products, 0, 8) as $product): ?>
            <div class="product-card bg-white rounded-xl shadow-md overflow-hidden">
                <div class="relative">
                    <img src="<?= htmlspecialchars($product['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         class="w-full h-48 object-cover">
                    <span class="absolute top-2 left-2 bg-green-500 text-white px-2 py-1 text-xs rounded-full">
                        New
                    </span>
                </div>
                
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                        <a href="/products/<?= htmlspecialchars($product['slug']) ?>" class="hover:text-primary-600">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </h3>
                    
                    <div class="flex items-center justify-between">
                        <div class="price">
                            <?php if ($product['sale_price']): ?>
                                <span class="text-lg font-bold text-primary-600"><?= format_currency($product['sale_price']) ?></span>
                                <span class="text-sm text-gray-500 line-through ml-1"><?= format_currency($product['price']) ?></span>
                            <?php else: ?>
                                <span class="text-lg font-bold text-primary-600"><?= format_currency($product['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <button onclick="addToCart(<?= $product['id'] ?>)" 
                                class="bg-primary-600 hover:bg-primary-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter Signup -->
<section class="bg-primary-50 rounded-2xl p-8 text-center">
    <h2 class="text-3xl font-bold text-gray-900 mb-4">Stay in the Loop</h2>
    <p class="text-gray-600 mb-6">Get notified about new products, exclusive deals, and special offers</p>
    
    <form class="max-w-md mx-auto flex gap-2">
        <input type="email" placeholder="Enter your email" 
               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            Subscribe
        </button>
    </form>
</section>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layout.php'; ?>