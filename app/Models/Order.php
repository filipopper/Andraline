<?php

namespace App\Models;

use Core\Model;

class Order extends Model
{
    protected string $table = 'orders';
    protected array $fillable = [
        'order_number', 'user_id', 'status', 'payment_status', 
        'payment_method', 'payment_id', 'currency', 'subtotal',
        'tax_amount', 'shipping_amount', 'discount_amount', 'total_amount',
        'billing_address', 'shipping_address', 'notes'
    ];
    
    public static function findByOrderNumber(string $orderNumber): ?self
    {
        $orders = self::where('order_number', '=', $orderNumber);
        return !empty($orders) ? $orders[0] : null;
    }
    
    public function getUser(): ?User
    {
        return $this->user_id ? User::find($this->user_id) : null;
    }
    
    public function getItems(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM order_items WHERE order_id = ?",
            [$this->id]
        );
    }
    
    public function getItemsWithProducts(): array
    {
        return $this->db->fetchAll(
            "SELECT oi.*, p.name as product_name, p.featured_image 
             FROM order_items oi 
             LEFT JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?",
            [$this->id]
        );
    }
    
    public function addItem(Product $product, int $quantity, float $price, ?int $variantId = null): void
    {
        $this->db->insert('order_items', [
            'order_id' => $this->id,
            'product_id' => $product->id,
            'variant_id' => $variantId,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => $quantity,
            'price' => $price,
            'total' => $price * $quantity
        ]);
    }
    
    public function calculateTotals(): void
    {
        $items = $this->getItems();
        $subtotal = 0;
        
        foreach ($items as $item) {
            $subtotal += $item['total'];
        }
        
        $this->subtotal = $subtotal;
        $this->tax_amount = $subtotal * 0.1; // 10% tax
        $this->shipping_amount = $subtotal >= 50 ? 0 : 9.99; // Free shipping over $50
        $this->total_amount = $subtotal + $this->tax_amount + $this->shipping_amount - $this->discount_amount;
        
        $this->save();
    }
    
    public function updateStatus(string $status): void
    {
        $oldStatus = $this->status;
        $this->status = $status;
        $this->save();
        
        // Handle status changes
        if ($status === 'shipped' && $oldStatus !== 'shipped') {
            $this->shipped_at = date('Y-m-d H:i:s');
            $this->save();
        }
        
        if ($status === 'delivered' && $oldStatus !== 'delivered') {
            $this->delivered_at = date('Y-m-d H:i:s');
            $this->save();
            
            // Award points for completed order
            $user = $this->getUser();
            if ($user) {
                $points = calculate_points($this->total_amount);
                $user->addPoints($points, 'Order completed', 'order', $this->id);
                $user->updateTotalSpent($this->total_amount);
                
                // Award first purchase badge
                $orderCount = count($user->getOrders());
                if ($orderCount === 1) {
                    $badge = Badge::where('name', '=', 'First Purchase');
                    if (!empty($badge)) {
                        $user->awardBadge($badge[0]->id);
                    }
                }
            }
        }
    }
    
    public function updatePaymentStatus(string $status): void
    {
        $this->payment_status = $status;
        $this->save();
        
        if ($status === 'paid') {
            $this->updateStatus('processing');
            
            // Reduce stock for all items
            $items = $this->getItemsWithProducts();
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->reduceStock($item['quantity']);
                    $product->incrementPurchases();
                }
            }
        }
    }
    
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }
    
    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }
        
        $this->updateStatus('cancelled');
        
        // Restore stock for all items
        $items = $this->getItemsWithProducts();
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $product->restoreStock($item['quantity']);
            }
        }
        
        return true;
    }
    
    public function getBillingAddress(): ?array
    {
        return $this->billing_address ? json_decode($this->billing_address, true) : null;
    }
    
    public function getShippingAddress(): ?array
    {
        return $this->shipping_address ? json_decode($this->shipping_address, true) : null;
    }
    
    public function setBillingAddress(array $address): void
    {
        $this->billing_address = json_encode($address);
    }
    
    public function setShippingAddress(array $address): void
    {
        $this->shipping_address = json_encode($address);
    }
    
    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded'
        ];
        
        return $labels[$this->status] ?? ucfirst($this->status);
    }
    
    public function getPaymentStatusLabel(): string
    {
        $labels = [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded'
        ];
        
        return $labels[$this->payment_status] ?? ucfirst($this->payment_status);
    }
    
    public static function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(uniqid());
    }
    
    public function getItemCount(): int
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(quantity) as total FROM order_items WHERE order_id = ?",
            [$this->id]
        );
        
        return $result ? (int)$result['total'] : 0;
    }
    
    public function hasDigitalItems(): bool
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count 
             FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ? AND p.is_digital = 1",
            [$this->id]
        );
        
        return $result && $result['count'] > 0;
    }
}