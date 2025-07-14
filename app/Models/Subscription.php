<?php

namespace App\Models;

use Core\Model;

class Subscription extends Model
{
    protected string $table = 'subscriptions';
    protected array $fillable = [
        'user_id', 'product_id', 'variant_id', 'status', 'billing_cycle', 
        'price', 'next_billing_date', 'started_at', 'paused_at', 'cancelled_at'
    ];
    
    public function getUser(): ?User
    {
        return User::find($this->user_id);
    }
    
    public function getProduct(): ?Product
    {
        return Product::find($this->product_id);
    }
    
    public function pause(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }
        
        $this->status = 'paused';
        $this->paused_at = date('Y-m-d H:i:s');
        $this->save();
        
        return true;
    }
    
    public function resume(): bool
    {
        if ($this->status !== 'paused') {
            return false;
        }
        
        $this->status = 'active';
        $this->paused_at = null;
        
        // Calculate next billing date based on billing cycle
        $this->calculateNextBillingDate();
        $this->save();
        
        return true;
    }
    
    public function cancel(): bool
    {
        if (!in_array($this->status, ['active', 'paused'])) {
            return false;
        }
        
        $this->status = 'cancelled';
        $this->cancelled_at = date('Y-m-d H:i:s');
        $this->save();
        
        return true;
    }
    
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
    
    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }
    
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
    
    public function isDue(): bool
    {
        return $this->isActive() && strtotime($this->next_billing_date) <= time();
    }
    
    public function processBilling(): ?Order
    {
        if (!$this->isDue()) {
            return null;
        }
        
        $product = $this->getProduct();
        if (!$product || !$product->isInStock()) {
            return null;
        }
        
        // Create subscription order
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $this->user_id,
            'status' => 'pending',
            'payment_status' => 'pending',
            'currency' => 'USD',
            'subtotal' => $this->price,
            'tax_amount' => $this->price * 0.1,
            'shipping_amount' => 0, // Free shipping for subscriptions
            'discount_amount' => 0,
            'total_amount' => $this->price * 1.1
        ]);
        
        $order->addItem($product, 1, $this->price, $this->variant_id);
        
        // Update next billing date
        $this->calculateNextBillingDate();
        $this->save();
        
        return $order;
    }
    
    private function calculateNextBillingDate(): void
    {
        $currentDate = date('Y-m-d');
        
        switch ($this->billing_cycle) {
            case 'weekly':
                $this->next_billing_date = date('Y-m-d', strtotime($currentDate . ' +1 week'));
                break;
            case 'monthly':
                $this->next_billing_date = date('Y-m-d', strtotime($currentDate . ' +1 month'));
                break;
            case 'quarterly':
                $this->next_billing_date = date('Y-m-d', strtotime($currentDate . ' +3 months'));
                break;
            case 'yearly':
                $this->next_billing_date = date('Y-m-d', strtotime($currentDate . ' +1 year'));
                break;
            default:
                $this->next_billing_date = date('Y-m-d', strtotime($currentDate . ' +1 month'));
        }
    }
    
    public function getStatusLabel(): string
    {
        $labels = [
            'active' => 'Active',
            'paused' => 'Paused',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired'
        ];
        
        return $labels[$this->status] ?? ucfirst($this->status);
    }
    
    public function getBillingCycleLabel(): string
    {
        $labels = [
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly'
        ];
        
        return $labels[$this->billing_cycle] ?? ucfirst($this->billing_cycle);
    }
    
    public static function getDueSubscriptions(): array
    {
        $instance = new static();
        return $instance->db->fetchAll(
            "SELECT * FROM {$instance->table} 
             WHERE status = 'active' AND next_billing_date <= DATE('now')
             ORDER BY next_billing_date ASC"
        );
    }
    
    public function getDaysUntilNextBilling(): int
    {
        $nextBilling = strtotime($this->next_billing_date);
        $today = strtotime(date('Y-m-d'));
        
        return max(0, ceil(($nextBilling - $today) / 86400));
    }
}