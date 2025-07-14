<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Review;

class ProductController extends Controller
{
    public function index(): void
    {
        $page = (int)$this->input('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $products = $this->db->fetchAll(
            "SELECT * FROM products WHERE status = 'published' 
             ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        
        $totalProducts = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE status = 'published'"
        );
        
        $totalPages = ceil($totalProducts['count'] / $limit);
        
        $this->view('products/index', [
            'products' => $products,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'page_title' => 'All Products'
        ]);
    }
    
    public function show(string $slug): void
    {
        $product = Product::findBySlug($slug);
        
        if (!$product || $product->status !== 'published') {
            $this->redirect('/products');
        }
        
        // Increment view count
        $product->incrementViews();
        
        // Get related data
        $category = $product->getCategory();
        $reviews = Review::getForProduct($product->id);
        $relatedProducts = $product->getRelatedProducts(4);
        $attributes = $product->getAttributes();
        $variants = $product->getVariants();
        
        // Check if user has this in wishlist
        $inWishlist = false;
        if (!$this->isGuest()) {
            $user = $this->auth();
            $wishlist = \App\Models\Wishlist::getDefaultForUser($user['id']);
            $inWishlist = $wishlist->hasItem($product->id);
        }
        
        $this->view('products/show', [
            'product' => $product,
            'category' => $category,
            'reviews' => $reviews,
            'related_products' => $relatedProducts,
            'attributes' => $attributes,
            'variants' => $variants,
            'in_wishlist' => $inWishlist,
            'page_title' => $product->name,
            'meta_description' => $product->short_description ?: $product->description
        ]);
    }
    
    public function search(): void
    {
        $query = trim($this->input('q', ''));
        $category = $this->input('category');
        $minPrice = $this->input('min_price');
        $maxPrice = $this->input('max_price');
        $sort = $this->input('sort', 'relevance');
        
        $products = [];
        $totalResults = 0;
        
        if (!empty($query)) {
            $products = Product::search($query, 50);
            $totalResults = count($products);
            
            // Apply filters
            if ($category) {
                $products = array_filter($products, function($product) use ($category) {
                    return $product['category_id'] == $category;
                });
            }
            
            if ($minPrice) {
                $products = array_filter($products, function($product) use ($minPrice) {
                    $price = $product['sale_price'] ?: $product['price'];
                    return $price >= $minPrice;
                });
            }
            
            if ($maxPrice) {
                $products = array_filter($products, function($product) use ($maxPrice) {
                    $price = $product['sale_price'] ?: $product['price'];
                    return $price <= $maxPrice;
                });
            }
            
            // Sort results
            switch ($sort) {
                case 'price_asc':
                    usort($products, function($a, $b) {
                        $priceA = $a['sale_price'] ?: $a['price'];
                        $priceB = $b['sale_price'] ?: $b['price'];
                        return $priceA <=> $priceB;
                    });
                    break;
                case 'price_desc':
                    usort($products, function($a, $b) {
                        $priceA = $a['sale_price'] ?: $a['price'];
                        $priceB = $b['sale_price'] ?: $b['price'];
                        return $priceB <=> $priceA;
                    });
                    break;
                case 'name':
                    usort($products, function($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    });
                    break;
                case 'newest':
                    usort($products, function($a, $b) {
                        return strtotime($b['created_at']) <=> strtotime($a['created_at']);
                    });
                    break;
                case 'rating':
                    usort($products, function($a, $b) {
                        return $b['rating_average'] <=> $a['rating_average'];
                    });
                    break;
            }
        }
        
        $categories = Category::active();
        
        $this->view('products/search', [
            'products' => $products,
            'categories' => $categories,
            'query' => $query,
            'category' => $category,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'sort' => $sort,
            'total_results' => $totalResults,
            'page_title' => $query ? "Search results for '{$query}'" : 'Search Products'
        ]);
    }
}