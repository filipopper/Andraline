<?php

namespace Controllers;

use Core\Controller;

class CartController extends Controller
{
    public function index()
    {
        $sessionId = session_id();
        $userId = $this->isLoggedIn() ? $_SESSION['user_id'] : null;

        // Get cart items
        $sql = "
            SELECT ci.*, p.name, p.price, p.sku, p.stock_quantity,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
            FROM cart_items ci
            JOIN products p ON ci.product_id = p.id
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

        $cartItems = $stmt->fetchAll();

        // Calculate totals
        $subtotal = 0;
        $totalItems = 0;
        
        foreach ($cartItems as &$item) {
            $item['total'] = $item['price'] * $item['quantity'];
            $subtotal += $item['total'];
            $totalItems += $item['quantity'];
        }

        // Get shipping settings
        $stmt = $this->db->prepare("SELECT setting_value FROM settings WHERE setting_key IN ('free_shipping_threshold', 'default_shipping_cost')");
        $stmt->execute();
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        $freeShippingThreshold = (float)($settings['free_shipping_threshold'] ?? 50.00);
        $defaultShippingCost = (float)($settings['default_shipping_cost'] ?? 5.99);
        
        $shippingCost = $subtotal >= $freeShippingThreshold ? 0 : $defaultShippingCost;
        $total = $subtotal + $shippingCost;

        $this->render('cart/index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shippingCost' => $shippingCost,
            'total' => $total,
            'totalItems' => $totalItems,
            'freeShippingThreshold' => $freeShippingThreshold,
            'currentUser' => $this->getCurrentUser()
        ]);
    }

    public function add()
    {
        if (!$this->isPost()) {
            $this->redirect('/cart');
        }

        $productId = (int)$this->getPost('product_id');
        $quantity = max(1, (int)$this->getPost('quantity', 1));
        $sessionId = session_id();
        $userId = $this->isLoggedIn() ? $_SESSION['user_id'] : null;

        // Check if product exists and is active
        $stmt = $this->db->prepare("SELECT id, name, price, stock_quantity FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            $this->setFlash('error', 'Product not found');
            $this->redirect('/products');
        }

        // Check stock
        if ($product['stock_quantity'] < $quantity) {
            $this->setFlash('error', 'Not enough stock available');
            $this->redirect('/products/' . $product['id']);
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
                $this->setFlash('error', 'Not enough stock available');
                $this->redirect('/cart');
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

        $this->setFlash('success', $product['name'] . ' added to cart');
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Product added to cart']);
        } else {
            $this->redirect('/cart');
        }
    }

    public function update()
    {
        if (!$this->isPost()) {
            $this->redirect('/cart');
        }

        $itemId = (int)$this->getPost('item_id');
        $quantity = max(1, (int)$this->getPost('quantity', 1));
        $sessionId = session_id();
        $userId = $this->isLoggedIn() ? $_SESSION['user_id'] : null;

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
            $this->setFlash('error', 'Cart item not found');
            $this->redirect('/cart');
        }

        // Check stock
        if ($quantity > $item['stock_quantity']) {
            $this->setFlash('error', 'Not enough stock available');
            $this->redirect('/cart');
        }

        // Update quantity
        $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$quantity, $itemId]);

        $this->setFlash('success', 'Cart updated');
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Cart updated']);
        } else {
            $this->redirect('/cart');
        }
    }

    public function remove()
    {
        if (!$this->isPost()) {
            $this->redirect('/cart');
        }

        $itemId = (int)$this->getPost('item_id');
        $sessionId = session_id();
        $userId = $this->isLoggedIn() ? $_SESSION['user_id'] : null;

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
            $this->setFlash('success', 'Item removed from cart');
        } else {
            $this->setFlash('error', 'Item not found');
        }

        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Item removed from cart']);
        } else {
            $this->redirect('/cart');
        }
    }

    public function clear()
    {
        if (!$this->isPost()) {
            $this->redirect('/cart');
        }

        $sessionId = session_id();
        $userId = $this->isLoggedIn() ? $_SESSION['user_id'] : null;

        // Clear cart
        $sql = "DELETE FROM cart_items WHERE session_id = ?";
        $params = [$sessionId];
        
        if ($userId) {
            $sql .= " OR user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $this->setFlash('success', 'Cart cleared');
        
        if ($this->isAjax()) {
            $this->json(['success' => true, 'message' => 'Cart cleared']);
        } else {
            $this->redirect('/cart');
        }
    }
}