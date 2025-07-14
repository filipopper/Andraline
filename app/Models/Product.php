<?php

namespace App\Models;

use Core\Model;

class Product extends Model
{
    protected string $table = 'products';
    protected array $fillable = [
        'name', 'slug', 'description', 'short_description', 'sku', 
        'price', 'sale_price', 'cost_price', 'stock_quantity', 
        'manage_stock', 'stock_status', 'weight', 'dimensions',
        'category_id', 'seller_id', 'featured_image', 'gallery',
        'is_featured', 'is_digital', 'download_url', 'status',
        'visibility', 'meta_title', 'meta_description'
    ];
    
    public static function findBySlug(string $slug): ?self
    {
        $products = self::where('slug', '=', $slug);
        return !empty($products) ? $products[0] : null;
    }
    
    public static function featured(int $limit = 8): array
    {
        $instance = new static();
        return $instance->db->fetchAll(
            "SELECT * FROM {$instance->table} WHERE is_featured = 1 AND status = 'published' ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }
    
    public static function latest(int $limit = 12): array
    {
        $instance = new static();
        return $instance->db->fetchAll(
            "SELECT * FROM {$instance->table} WHERE status = 'published' ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }
    
    public static function search(string $query, int $limit = 20): array
    {
        $instance = new static();
        $searchTerm = "%{$query}%";
        return $instance->db->fetchAll(
            "SELECT * FROM {$instance->table} 
             WHERE (name LIKE ? OR description LIKE ? OR sku LIKE ?) 
             AND status = 'published' 
             ORDER BY name ASC LIMIT ?",
            [$searchTerm, $searchTerm, $searchTerm, $limit]
        );
    }
    
    public function getCategory(): ?Category
    {
        return Category::find($this->category_id);
    }
    
    public function getSeller(): ?User
    {
        return $this->seller_id ? User::find($this->seller_id) : null;
    }
    
    public function getCurrentPrice(): float
    {
        return $this->sale_price ?: $this->price;
    }
    
    public function hasDiscount(): bool
    {
        return $this->sale_price && $this->sale_price < $this->price;
    }
    
    public function getDiscountPercentage(): int
    {
        if (!$this->hasDiscount()) {
            return 0;
        }
        
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }
    
    public function isInStock(): bool
    {
        if (!$this->manage_stock) {
            return $this->stock_status === 'in_stock';
        }
        
        return $this->stock_quantity > 0;
    }
    
    public function getGalleryImages(): array
    {
        if (!$this->gallery) {
            return [];
        }
        
        $images = json_decode($this->gallery, true);
        return is_array($images) ? $images : [];
    }
    
    public function getAttributes(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM product_attributes WHERE product_id = ?",
            [$this->id]
        );
    }
    
    public function getVariants(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM product_variants WHERE product_id = ? AND is_active = 1",
            [$this->id]
        );
    }
    
    public function getReviews(): array
    {
        return $this->db->fetchAll(
            "SELECT r.*, u.first_name, u.last_name 
             FROM reviews r 
             JOIN users u ON r.user_id = u.id 
             WHERE r.product_id = ? AND r.is_approved = 1 
             ORDER BY r.created_at DESC",
            [$this->id]
        );
    }
    
    public function getAverageRating(): float
    {
        $result = $this->db->fetchOne(
            "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = ? AND is_approved = 1",
            [$this->id]
        );
        
        return $result ? round($result['avg_rating'], 1) : 0;
    }
    
    public function getReviewCount(): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM reviews WHERE product_id = ? AND is_approved = 1",
            [$this->id]
        );
        
        return $result ? $result['count'] : 0;
    }
    
    public function updateRating(): void
    {
        $this->rating_average = $this->getAverageRating();
        $this->rating_count = $this->getReviewCount();
        $this->save();
    }
    
    public function incrementViews(): void
    {
        $this->views_count++;
        $this->save();
    }
    
    public function incrementPurchases(): void
    {
        $this->purchases_count++;
        $this->save();
    }
    
    public function reduceStock(int $quantity): bool
    {
        if (!$this->manage_stock) {
            return true;
        }
        
        if ($this->stock_quantity < $quantity) {
            return false;
        }
        
        $this->stock_quantity -= $quantity;
        
        // Update stock status
        if ($this->stock_quantity <= 0) {
            $this->stock_status = 'out_of_stock';
        }
        
        $this->save();
        return true;
    }
    
    public function restoreStock(int $quantity): void
    {
        if (!$this->manage_stock) {
            return;
        }
        
        $this->stock_quantity += $quantity;
        
        // Update stock status
        if ($this->stock_quantity > 0 && $this->stock_status === 'out_of_stock') {
            $this->stock_status = 'in_stock';
        }
        
        $this->save();
    }
    
    public function getRelatedProducts(int $limit = 4): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} 
             WHERE category_id = ? AND id != ? AND status = 'published' 
             ORDER BY RANDOM() LIMIT ?",
            [$this->category_id, $this->id, $limit]
        );
    }
    
    public static function getByCategorySlug(string $slug, int $limit = 20, int $offset = 0): array
    {
        $instance = new static();
        return $instance->db->fetchAll(
            "SELECT p.* FROM products p 
             JOIN categories c ON p.category_id = c.id 
             WHERE c.slug = ? AND p.status = 'published' 
             ORDER BY p.created_at DESC LIMIT ? OFFSET ?",
            [$slug, $limit, $offset]
        );
    }
    
    public function addAttribute(string $name, string $value): void
    {
        $this->db->insert('product_attributes', [
            'product_id' => $this->id,
            'name' => $name,
            'value' => $value
        ]);
    }
    
    public function isEligibleForSubscription(): bool
    {
        return !$this->is_digital && $this->isInStock();
    }
    
    public function getUrl(): string
    {
        return "/products/{$this->slug}";
    }
    
    public function getImageUrl(): string
    {
        return $this->featured_image ? "/uploads/{$this->featured_image}" : '/assets/images/placeholder.jpg';
    }
}