<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">All Products</h1>
        <p class="text-gray-600">Browse our complete collection of products</p>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-8">
        <form action="/products" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="q" id="search" 
                       value="<?= isset($_GET['q']) ? $this->escape($_GET['q']) : '' ?>"
                       placeholder="Search products..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" id="category" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $this->escape($category['slug']) ?>" 
                                <?= (isset($_GET['category']) && $_GET['category'] === $category['slug']) ? 'selected' : '' ?>>
                            <?= $this->escape($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select name="sort" id="sort" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="newest" <?= (isset($_GET['sort']) && $_GET['sort'] === 'newest') ? 'selected' : '' ?>>Newest First</option>
                    <option value="price_low" <?= (isset($_GET['sort']) && $_GET['sort'] === 'price_low') ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_high" <?= (isset($_GET['sort']) && $_GET['sort'] === 'price_high') ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="name" <?= (isset($_GET['sort']) && $_GET['sort'] === 'name') ? 'selected' : '' ?>>Name A-Z</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" 
                        class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Results Summary -->
    <div class="mb-6">
        <p class="text-gray-600">
            Showing <?= count($products) ?> of <?= $total ?> products
            <?php if (isset($_GET['q']) || isset($_GET['category'])): ?>
                (filtered results)
            <?php endif; ?>
        </p>
    </div>

    <!-- Products Grid -->
    <?php if (empty($products)): ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">
                Try adjusting your search or filter criteria.
            </p>
            <div class="mt-6">
                <a href="/products" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    View All Products
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
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

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-8 flex justify-center">
                <nav class="flex items-center space-x-2">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 ?><?= isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?><?= isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '' ?><?= isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '' ?>" 
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Previous
                        </a>
                    <?php endif; ?>

                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <a href="?page=<?= $i ?><?= isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?><?= isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '' ?><?= isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '' ?>" 
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium <?= $i === $currentPage ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 ?><?= isset($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?><?= isset($_GET['category']) ? '&category=' . urlencode($_GET['category']) : '' ?><?= isset($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : '' ?>" 
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Next
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

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
        } else {
            alert(data.error || 'Error adding to cart');
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        alert('Error adding to cart');
    });
}
</script>