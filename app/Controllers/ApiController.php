<?php

namespace Controllers;

use Core\Controller;

class ApiController extends Controller
{
    public function products()
    {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request']);
        }

        $page = max(1, (int)($this->getGet('page', 1)));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;
        $category = $this->getGet('category', '');
        $search = $this->getGet('search', '');

        // Build query
        $whereConditions = ['p.is_active = 1'];
        $params = [];

        if (!empty($category)) {
            $whereConditions[] = "c.slug = ?";
            $params[] = $category;
        }

        if (!empty($search)) {
            $whereConditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = implode(' AND ', $whereConditions);

        // Get products
        $sql = "
            SELECT p.*, c.name as category_name,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE $whereClause
            ORDER BY p.created_at DESC
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

        $this->json([
            'products' => $products,
            'total' => $total,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage)
        ]);
    }

    public function cart()
    {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request']);
        }

        $action = $this->getPost('action');
        $sessionId = session_id();
        $userId = $this->isLoggedIn() ? $_SESSION['user_id'] : null;

        switch ($action) {
            case 'add':
                $productId = (int)$this->getPost('product_id');
                $quantity = max(1, (int)$this->getPost('quantity', 1));

                // Check if product exists
                $stmt = $this->db->prepare("SELECT id, name, price, stock_quantity FROM products WHERE id = ? AND is_active = 1");
                $stmt->execute([$productId]);
                $product = $stmt->fetch();

                if (!$product) {
                    $this->json(['error' => 'Product not found']);
                }

                // Check stock
                if ($product['stock_quantity'] < $quantity) {
                    $this->json(['error' => 'Not enough stock available']);
                }

                // Check if item already in cart
                $sql = "SELECT id, quantity FROM cart_items WHERE product_id = ? AND (session_id = ?";
                $params = [$productId, $sessionId];
                
                if ($userId) {
                    $sql .= " OR user_id = ?";
                    $params[] = $userId;
                }
                
                $sql .= ")";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $existingItem = $stmt->fetch();

                if ($existingItem) {
                    // Update quantity
                    $newQuantity = $existingItem['quantity'] + $quantity;
                    if ($newQuantity > $product['stock_quantity']) {
                        $this->json(['error' => 'Not enough stock available']);
                    }

                    $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                    $stmt->execute([$newQuantity, $existingItem['id']]);
                } else {
                    // Add new item
                    $stmt = $this->db->prepare("
                        INSERT INTO cart_items (session_id, user_id, product_id, quantity) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([$sessionId, $userId, $productId, $quantity]);
                }

                $this->json(['success' => true, 'message' => $product['name'] . ' added to cart']);
                break;

            case 'update':
                $itemId = (int)$this->getPost('item_id');
                $quantity = max(1, (int)$this->getPost('quantity', 1));

                // Get cart item
                $sql = "
                    SELECT ci.*, p.stock_quantity, p.name 
                    FROM cart_items ci
                    JOIN products p ON ci.product_id = p.id
                    WHERE ci.id = ? AND (ci.session_id = ?
                ";
                $params = [$itemId, $sessionId];
                
                if ($userId) {
                    $sql .= " OR ci.user_id = ?";
                    $params[] = $userId;
                }
                
                $sql .= ")";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $item = $stmt->fetch();

                if (!$item) {
                    $this->json(['error' => 'Cart item not found']);
                }

                // Check stock
                if ($quantity > $item['stock_quantity']) {
                    $this->json(['error' => 'Not enough stock available']);
                }

                // Update quantity
                $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$quantity, $itemId]);

                $this->json(['success' => true, 'message' => 'Cart updated']);
                break;

            case 'remove':
                $itemId = (int)$this->getPost('item_id');

                // Delete cart item
                $sql = "DELETE FROM cart_items WHERE id = ? AND (session_id = ?";
                $params = [$itemId, $sessionId];
                
                if ($userId) {
                    $sql .= " OR user_id = ?";
                    $params[] = $userId;
                }
                
                $sql .= ")";
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);

                if ($stmt->rowCount() > 0) {
                    $this->json(['success' => true, 'message' => 'Item removed from cart']);
                } else {
                    $this->json(['error' => 'Item not found']);
                }
                break;

            case 'count':
                // Get cart count
                $sql = "
                    SELECT COUNT(*) as count, SUM(ci.quantity) as total_items
                    FROM cart_items ci
                    WHERE ci.session_id = ?
                ";
                
                if ($userId) {
                    $sql .= " OR ci.user_id = ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$sessionId, $userId]);
                } else {
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute([$sessionId]);
                }

                $result = $stmt->fetch();
                $this->json([
                    'count' => (int)$result['count'],
                    'totalItems' => (int)$result['total_items']
                ]);
                break;

            default:
                $this->json(['error' => 'Invalid action']);
        }
    }

    public function wishlist()
    {
        if (!$this->isAjax()) {
            $this->json(['error' => 'Invalid request']);
        }

        if (!$this->isLoggedIn()) {
            $this->json(['error' => 'Please log in to manage wishlist']);
        }

        $action = $this->getPost('action');
        $userId = $_SESSION['user_id'];

        switch ($action) {
            case 'add':
                $productId = (int)$this->getPost('product_id');

                // Check if product exists
                $stmt = $this->db->prepare("SELECT id, name FROM products WHERE id = ? AND is_active = 1");
                $stmt->execute([$productId]);
                $product = $stmt->fetch();

                if (!$product) {
                    $this->json(['error' => 'Product not found']);
                }

                // Check if already in wishlist
                $stmt = $this->db->prepare("SELECT id FROM wishlist_items WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$userId, $productId]);
                if ($stmt->fetch()) {
                    $this->json(['error' => 'Product already in wishlist']);
                }

                // Add to wishlist
                $stmt = $this->db->prepare("INSERT INTO wishlist_items (user_id, product_id) VALUES (?, ?)");
                $stmt->execute([$userId, $productId]);

                $this->json(['success' => true, 'message' => $product['name'] . ' added to wishlist']);
                break;

            case 'remove':
                $productId = (int)$this->getPost('product_id');

                $stmt = $this->db->prepare("DELETE FROM wishlist_items WHERE user_id = ? AND product_id = ?");
                $stmt->execute([$userId, $productId]);

                if ($stmt->rowCount() > 0) {
                    $this->json(['success' => true, 'message' => 'Product removed from wishlist']);
                } else {
                    $this->json(['error' => 'Product not found in wishlist']);
                }
                break;

            case 'list':
                $stmt = $this->db->prepare("
                    SELECT p.*, c.name as category_name,
                           (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                    FROM wishlist_items wi
                    JOIN products p ON wi.product_id = p.id
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE wi.user_id = ?
                    ORDER BY wi.created_at DESC
                ");
                $stmt->execute([$userId]);
                $wishlist = $stmt->fetchAll();

                $this->json(['wishlist' => $wishlist]);
                break;

            default:
                $this->json(['error' => 'Invalid action']);
        }
    }
}