<?php

namespace Controllers;

use Core\Controller;

class ProductController extends Controller
{
    public function index()
    {
        $page = max(1, (int)($this->getGet('page', 1)));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        // Get products with pagination
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $products = $stmt->fetchAll();

        // Get total count for pagination
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
        $stmt->execute();
        $total = $stmt->fetch()['total'];
        $totalPages = ceil($total / $perPage);

        // Get categories for filter
        $stmt = $this->db->prepare("SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY name");
        $stmt->execute();
        $categories = $stmt->fetchAll();

        $this->render('products/index', [
            'products' => $products,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'currentUser' => $this->getCurrentUser()
        ]);
    }

    public function show()
    {
        $slug = $this->getParam('slug');
        
        // Get product details
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug,
                   u.first_name, u.last_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN users u ON p.seller_id = u.id
            WHERE p.slug = ? AND p.is_active = 1
        ");
        $stmt->execute([$slug]);
        $product = $stmt->fetch();

        if (!$product) {
            $this->redirect('/404');
        }

        // Get product images
        $stmt = $this->db->prepare("
            SELECT * FROM product_images 
            WHERE product_id = ? 
            ORDER BY is_primary DESC, sort_order ASC
        ");
        $stmt->execute([$product['id']]);
        $images = $stmt->fetchAll();

        // Get product attributes
        $stmt = $this->db->prepare("
            SELECT attribute_name, attribute_value 
            FROM product_attributes 
            WHERE product_id = ?
        ");
        $stmt->execute([$product['id']]);
        $attributes = $stmt->fetchAll();

        // Get reviews
        $stmt = $this->db->prepare("
            SELECT r.*, u.first_name, u.last_name
            FROM product_reviews r
            JOIN users u ON r.user_id = u.id
            WHERE r.product_id = ? AND r.is_approved = 1
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([$product['id']]);
        $reviews = $stmt->fetchAll();

        // Get related products
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p
            WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
            ORDER BY RANDOM()
            LIMIT 4
        ");
        $stmt->execute([$product['category_id'], $product['id']]);
        $relatedProducts = $stmt->fetchAll();

        $this->render('products/show', [
            'product' => $product,
            'images' => $images,
            'attributes' => $attributes,
            'reviews' => $reviews,
            'relatedProducts' => $relatedProducts,
            'currentUser' => $this->getCurrentUser()
        ]);
    }

    public function category()
    {
        $slug = $this->getParam('slug');
        $page = max(1, (int)($this->getGet('page', 1)));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        // Get category
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = ? AND is_active = 1");
        $stmt->execute([$slug]);
        $category = $stmt->fetch();

        if (!$category) {
            $this->redirect('/404');
        }

        // Get products in category
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p
            WHERE p.category_id = ? AND p.is_active = 1
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$category['id'], $perPage, $offset]);
        $products = $stmt->fetchAll();

        // Get total count for pagination
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM products WHERE category_id = ? AND is_active = 1");
        $stmt->execute([$category['id']]);
        $total = $stmt->fetch()['total'];
        $totalPages = ceil($total / $perPage);

        // Get subcategories
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(p.id) as product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
            WHERE c.parent_id = ? AND c.is_active = 1
            GROUP BY c.id
            ORDER BY c.name
        ");
        $stmt->execute([$category['id']]);
        $subcategories = $stmt->fetchAll();

        $this->render('products/category', [
            'category' => $category,
            'products' => $products,
            'subcategories' => $subcategories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'currentUser' => $this->getCurrentUser()
        ]);
    }

    public function search()
    {
        $query = trim($this->getGet('q', ''));
        $category = $this->getGet('category', '');
        $minPrice = $this->getGet('min_price', '');
        $maxPrice = $this->getGet('max_price', '');
        $sort = $this->getGet('sort', 'relevance');
        $page = max(1, (int)($this->getGet('page', 1)));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        if (empty($query) && empty($category) && empty($minPrice) && empty($maxPrice)) {
            $this->redirect('/products');
        }

        // Build search query
        $whereConditions = ['p.is_active = 1'];
        $params = [];

        if (!empty($query)) {
            $whereConditions[] = "(p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)";
            $searchTerm = "%$query%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($category)) {
            $whereConditions[] = "c.slug = ?";
            $params[] = $category;
        }

        if (!empty($minPrice)) {
            $whereConditions[] = "p.price >= ?";
            $params[] = $minPrice;
        }

        if (!empty($maxPrice)) {
            $whereConditions[] = "p.price <= ?";
            $params[] = $maxPrice;
        }

        $whereClause = implode(' AND ', $whereConditions);

        // Build order clause
        $orderClause = 'p.created_at DESC';
        switch ($sort) {
            case 'price_low':
                $orderClause = 'p.price ASC';
                break;
            case 'price_high':
                $orderClause = 'p.price DESC';
                break;
            case 'name':
                $orderClause = 'p.name ASC';
                break;
            case 'newest':
                $orderClause = 'p.created_at DESC';
                break;
        }

        // Get products
        $sql = "
            SELECT p.*, c.name as category_name, c.slug as category_slug,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE $whereClause
            ORDER BY $orderClause
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        // Get total count
        $countSql = "
            SELECT COUNT(*) as total
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE $whereClause
        ";
        
        $countParams = array_slice($params, 0, -2);
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($countParams);
        $total = $stmt->fetch()['total'];
        $totalPages = ceil($total / $perPage);

        // Get categories for filter
        $stmt = $this->db->prepare("SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY name");
        $stmt->execute();
        $categories = $stmt->fetchAll();

        $this->render('products/search', [
            'products' => $products,
            'categories' => $categories,
            'query' => $query,
            'selectedCategory' => $category,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sort' => $sort,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'currentUser' => $this->getCurrentUser()
        ]);
    }
}