<?php

namespace App\Models;

use Core\Model;

class Cart extends Model
{
    protected string $table = 'carts';
    protected array $fillable = ['user_id', 'session_id'];
    
    public static function getForUser(?int $userId, string $sessionId): self
    {
        $instance = new static();
        
        if ($userId) {
            $carts = self::where('user_id', '=', $userId);
            if (!empty($carts)) {
                return $carts[0];
            }
        }
        
        // Try to find by session
        $carts = self::where('session_id', '=', $sessionId);
        if (!empty($carts)) {
            $cart = $carts[0];
            
            // If user just logged in, associate cart with user
            if ($userId && !$cart->user_id) {
                $cart->user_id = $userId;
                $cart->save();
            }
            
            return $cart;
        }
        
        // Create new cart
        return self::create([
            'user_id' => $userId,
            'session_id' => $sessionId
        ]);
    }
    
    public function getItems(): array
    {
        return $this->db->fetchAll(
            "SELECT ci.*, p.name, p.slug, p.featured_image, p.stock_quantity, p.stock_status
             FROM cart_items ci 
             JOIN products p ON ci.product_id = p.id 
             WHERE ci.cart_id = ?
             ORDER BY ci.created_at DESC",
            [$this->id]
        );
    }
    
    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): bool
    {
        $product = Product::find($productId);
        if (!$product || !$product->isInStock()) {
            return false;
        }
        
        // Check if item already exists
        $existingItem = $this->getCartItem($productId, $variantId);
        
        if ($existingItem) {
            $newQuantity = $existingItem['quantity'] + $quantity;
            
            // Check stock availability
            if ($product->manage_stock && $newQuantity > $product->stock_quantity) {
                return false;
            }
            
            $this->db->update('cart_items', 
                ['quantity' => $newQuantity], 
                'id = ?', 
                [$existingItem['id']]
            );
        } else {
            // Check stock availability
            if ($product->manage_stock && $quantity > $product->stock_quantity) {
                return false;
            }
            
            $this->db->insert('cart_items', [
                'cart_id' => $this->id,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $product->getCurrentPrice()
            ]);
        }
        
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();
        
        return true;
    }
    
    public function updateItemQuantity(int $itemId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($itemId);
        }
        
        $item = $this->db->fetchOne(
            "SELECT ci.*, p.stock_quantity, p.manage_stock 
             FROM cart_items ci 
             JOIN products p ON ci.product_id = p.id 
             WHERE ci.id = ? AND ci.cart_id = ?",
            [$itemId, $this->id]
        );
        
        if (!$item) {
            return false;
        }
        
        // Check stock availability
        if ($item['manage_stock'] && $quantity > $item['stock_quantity']) {
            return false;
        }
        
        $this->db->update('cart_items', 
            ['quantity' => $quantity], 
            'id = ?', 
            [$itemId]
        );
        
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();
        
        return true;
    }
    
    public function removeItem(int $itemId): bool
    {
        $deleted = $this->db->delete('cart_items', 'id = ? AND cart_id = ?', [$itemId, $this->id]);
        
        if ($deleted > 0) {
            $this->updated_at = date('Y-m-d H:i:s');
            $this->save();
        }
        
        return $deleted > 0;
    }
    
    public function clear(): void
    {
        $this->db->delete('cart_items', 'cart_id = ?', [$this->id]);
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();
    }
    
    public function getItemCount(): int
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(quantity) as total FROM cart_items WHERE cart_id = ?",
            [$this->id]
        );
        
        return $result ? (int)$result['total'] : 0;
    }
    
    public function getSubtotal(): float
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(quantity * price) as subtotal FROM cart_items WHERE cart_id = ?",
            [$this->id]
        );
        
        return $result ? (float)$result['subtotal'] : 0;
    }
    
    public function getTotals(): array
    {
        $subtotal = $this->getSubtotal();
        $taxRate = 0.1; // 10% tax
        $shippingThreshold = 50;
        $shippingCost = 9.99;
        
        $taxAmount = $subtotal * $taxRate;
        $shippingAmount = $subtotal >= $shippingThreshold ? 0 : $shippingCost;
        $total = $subtotal + $taxAmount + $shippingAmount;
        
        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'total' => $total,
            'free_shipping_eligible' => $subtotal >= $shippingThreshold
        ];
    }
    
    public function isEmpty(): bool
    {
        return $this->getItemCount() === 0;
    }
    
    public function hasUnavailableItems(): bool
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count 
             FROM cart_items ci 
             JOIN products p ON ci.product_id = p.id 
             WHERE ci.cart_id = ? AND (
                 p.status != 'published' OR 
                 p.stock_status = 'out_of_stock' OR 
                 (p.manage_stock = 1 AND p.stock_quantity < ci.quantity)
             )",
            [$this->id]
        );
        
        return $result && $result['count'] > 0;
    }
    
    public function removeUnavailableItems(): array
    {
        $unavailableItems = $this->db->fetchAll(
            "SELECT ci.id, p.name 
             FROM cart_items ci 
             JOIN products p ON ci.product_id = p.id 
             WHERE ci.cart_id = ? AND (
                 p.status != 'published' OR 
                 p.stock_status = 'out_of_stock' OR 
                 (p.manage_stock = 1 AND p.stock_quantity < ci.quantity)
             )",
            [$this->id]
        );
        
        foreach ($unavailableItems as $item) {
            $this->removeItem($item['id']);
        }
        
        return $unavailableItems;
    }
    
    private function getCartItem(int $productId, ?int $variantId = null): ?array
    {
        $sql = "SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?";
        $params = [$this->id, $productId];
        
        if ($variantId) {
            $sql .= " AND variant_id = ?";
            $params[] = $variantId;
        } else {
            $sql .= " AND variant_id IS NULL";
        }
        
        return $this->db->fetchOne($sql, $params);
    }
    
    public function convertToOrder(array $billingAddress, array $shippingAddress): ?Order
    {
        if ($this->isEmpty() || $this->hasUnavailableItems()) {
            return null;
        }
        
        $totals = $this->getTotals();
        
        // Create order
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $this->user_id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'currency' => 'USD',
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax_amount'],
            'shipping_amount' => $totals['shipping_amount'],
            'discount_amount' => 0,
            'total_amount' => $totals['total']
        ]);
        
        $order->setBillingAddress($billingAddress);
        $order->setShippingAddress($shippingAddress);
        $order->save();
        
        // Add items to order
        $items = $this->getItems();
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $order->addItem($product, $item['quantity'], $item['price'], $item['variant_id']);
        }
        
        // Clear cart
        $this->clear();
        
        return $order;
    }
}