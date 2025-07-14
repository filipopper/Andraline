<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 slide-up">
                Welcome to <span class="text-yellow-300">eCommerce</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-primary-100 slide-up">
                Discover amazing products at unbeatable prices
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center slide-up">
                <a href="/products" class="bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Shop Now
                </a>
                <a href="/about" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-primary-600 transition duration-300">
                    Learn More
                </a>
            </div>
        </div>
    </div>
    
    <!-- Decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-500 rounded-full opacity-20"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-primary-400 rounded-full opacity-20"></div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Shop by Category</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Explore our curated collection of products organized by category</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            <?php foreach ($categories as $category): ?>
                <a href="/category/<?= $this->escape($category['slug']) ?>" 
                   class="group block text-center p-6 bg-gray-50 rounded-lg hover:bg-primary-50 transition duration-300">
                    <?php if ($category['image']): ?>
                        <img src="<?= $this->asset('uploads/' . $category['image']) ?>" 
                             alt="<?= $this->escape($category['name']) ?>" 
                             class="w-16 h-16 mx-auto mb-4 rounded-lg object-cover">
                    <?php else: ?>
                        <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    <?php endif; ?>
                    <h3 class="font-semibold text-gray-900 group-hover:text-primary-600 transition duration-300">
                        <?= $this->escape($category['name']) ?>
                    </h3>
                    <p class="text-sm text-gray-500 mt-1"><?= $category['product_count'] ?> products</p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Products</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Handpicked products that our customers love</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition duration-300 fade-in">
                    <a href="/products/<?= $this->escape($product['slug']) ?>" class="block">
                        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-t-lg bg-gray-200">
                            <?php if ($product['primary_image']): ?>
                                <img src="<?= $this->asset('uploads/' . $product['primary_image']) ?>" 
                                     alt="<?= $this->escape($product['name']) ?>" 
                                     class="h-full w-full object-cover object-center">
                            <?php else: ?>
                                <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-primary-600 transition duration-300">
                                <?= $this->escape($product['name']) ?>
                            </h3>
                            <p class="text-sm text-gray-500 mb-2"><?= $this->escape($product['category_name']) ?></p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-primary-600">
                                    <?= $this->formatPrice($product['price']) ?>
                                </span>
                                <button onclick="addToCart(<?= $product['id'] ?>)" 
                                        class="bg-primary-600 text-white px-3 py-1 rounded text-sm hover:bg-primary-700 transition duration-300">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-8">
            <a href="/products" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 transition duration-300">
                View All Products
                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Latest Products Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Latest Arrivals</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Check out our newest products</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach (array_slice($latestProducts, 0, 8) as $product): ?>
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition duration-300 fade-in">
                    <a href="/products/<?= $this->escape($product['slug']) ?>" class="block">
                        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-t-lg bg-gray-200">
                            <?php if ($product['primary_image']): ?>
                                <img src="<?= $this->asset('uploads/' . $product['primary_image']) ?>" 
                                     alt="<?= $this->escape($product['name']) ?>" 
                                     class="h-full w-full object-cover object-center">
                            <?php else: ?>
                                <div class="h-full w-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-primary-600 transition duration-300">
                                <?= $this->escape($product['name']) ?>
                            </h3>
                            <p class="text-sm text-gray-500 mb-2"><?= $this->escape($product['category_name']) ?></p>
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-primary-600">
                                    <?= $this->formatPrice($product['price']) ?>
                                </span>
                                <button onclick="addToCart(<?= $product['id'] ?>)" 
                                        class="bg-primary-600 text-white px-3 py-1 rounded text-sm hover:bg-primary-700 transition duration-300">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Us</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">We provide the best shopping experience with innovative features</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Fast Delivery</h3>
                <p class="text-gray-600">Quick and reliable shipping to your doorstep</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Quality Guarantee</h3>
                <p class="text-gray-600">Premium products with quality assurance</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Secure Payment</h3>
                <p class="text-gray-600">Safe and secure payment processing</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">24/7 Support</h3>
                <p class="text-gray-600">Round-the-clock customer support</p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-16 bg-primary-600 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">Stay Updated</h2>
        <p class="text-primary-100 mb-8 max-w-2xl mx-auto">
            Subscribe to our newsletter for the latest products, exclusive offers, and updates
        </p>
        <form class="max-w-md mx-auto flex gap-4">
            <input type="email" placeholder="Enter your email" 
                   class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white">
            <button type="submit" 
                    class="bg-white text-primary-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                Subscribe
            </button>
        </form>
    </div>
</section>

<script>
function addToCart(productId) {
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `product_id=${productId}&quantity=1&csrf_token=<?= $this->csrfToken() ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount();
            
            // Show success message
            const flash = document.createElement('div');
            flash.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 fade-in';
            flash.textContent = data.message;
            document.body.appendChild(flash);
            
            setTimeout(() => {
                flash.remove();
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
    });
}
</script>