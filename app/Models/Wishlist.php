<?php

namespace App\Models;

use Core\Model;

class Wishlist extends Model
{
    protected string $table = 'wishlists';
    protected array $fillable = ['user_id', 'name', 'is_public', 'share_token'];
    
    public static function getDefaultForUser(int $userId): self
    {
        $wishlists = self::where('user_id', '=', $userId);
        
        if (!empty($wishlists)) {
            return $wishlists[0];
        }
        
        // Create default wishlist
        return self::create([
            'user_id' => $userId,
            'name' => 'My Wishlist',
            'is_public' => false
        ]);
    }
    
    public static function findByShareToken(string $token): ?self
    {
        $wishlists = self::where('share_token', '=', $token);
        return !empty($wishlists) ? $wishlists[0] : null;
    }
    
    public function getItems(): array
    {
        return $this->db->fetchAll(
            "SELECT wi.*, p.name, p.slug, p.price, p.sale_price, p.featured_image, p.stock_status
             FROM wishlist_items wi 
             JOIN products p ON wi.product_id = p.id 
             WHERE wi.wishlist_id = ?
             ORDER BY wi.priority DESC, wi.created_at DESC",
            [$this->id]
        );
    }
    
    public function addItem(int $productId, ?int $variantId = null, string $notes = '', int $priority = 0): bool
    {
        // Check if item already exists
        if ($this->hasItem($productId, $variantId)) {
            return false;
        }
        
        $this->db->insert('wishlist_items', [
            'wishlist_id' => $this->id,
            'product_id' => $productId,
            'variant_id' => $variantId,
            'notes' => $notes,
            'priority' => $priority
        ]);
        
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();
        
        return true;
    }
    
    public function removeItem(int $productId, ?int $variantId = null): bool
    {
        $sql = "DELETE FROM wishlist_items WHERE wishlist_id = ? AND product_id = ?";
        $params = [$this->id, $productId];
        
        if ($variantId) {
            $sql .= " AND variant_id = ?";
            $params[] = $variantId;
        } else {
            $sql .= " AND variant_id IS NULL";
        }
        
        $deleted = $this->db->query($sql, $params)->rowCount();
        
        if ($deleted > 0) {
            $this->updated_at = date('Y-m-d H:i:s');
            $this->save();
        }
        
        return $deleted > 0;
    }
    
    public function hasItem(int $productId, ?int $variantId = null): bool
    {
        $sql = "SELECT COUNT(*) as count FROM wishlist_items WHERE wishlist_id = ? AND product_id = ?";
        $params = [$this->id, $productId];
        
        if ($variantId) {
            $sql .= " AND variant_id = ?";
            $params[] = $variantId;
        } else {
            $sql .= " AND variant_id IS NULL";
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result && $result['count'] > 0;
    }
    
    public function getItemCount(): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM wishlist_items WHERE wishlist_id = ?",
            [$this->id]
        );
        
        return $result ? (int)$result['count'] : 0;
    }
    
    public function clear(): void
    {
        $this->db->delete('wishlist_items', 'wishlist_id = ?', [$this->id]);
        $this->updated_at = date('Y-m-d H:i:s');
        $this->save();
    }
    
    public function makePublic(): string
    {
        $this->is_public = true;
        $this->share_token = bin2hex(random_bytes(16));
        $this->save();
        
        return $this->share_token;
    }
    
    public function makePrivate(): void
    {
        $this->is_public = false;
        $this->share_token = null;
        $this->save();
    }
    
    public function getShareUrl(): ?string
    {
        if (!$this->is_public || !$this->share_token) {
            return null;
        }
        
        return url("wishlist/share/{$this->share_token}");
    }
    
    public function getUser(): ?User
    {
        return User::find($this->user_id);
    }
    
    public function getAvailableItems(): array
    {
        return $this->db->fetchAll(
            "SELECT wi.*, p.name, p.slug, p.price, p.sale_price, p.featured_image, p.stock_status
             FROM wishlist_items wi 
             JOIN products p ON wi.product_id = p.id 
             WHERE wi.wishlist_id = ? AND p.status = 'published' AND p.stock_status = 'in_stock'
             ORDER BY wi.priority DESC, wi.created_at DESC",
            [$this->id]
        );
    }
    
    public function getUnavailableItems(): array
    {
        return $this->db->fetchAll(
            "SELECT wi.*, p.name, p.slug, p.price, p.sale_price, p.featured_image, p.stock_status
             FROM wishlist_items wi 
             JOIN products p ON wi.product_id = p.id 
             WHERE wi.wishlist_id = ? AND (p.status != 'published' OR p.stock_status != 'in_stock')
             ORDER BY wi.created_at DESC",
            [$this->id]
        );
    }
    
    public function moveToCart(Cart $cart): int
    {
        $items = $this->getAvailableItems();
        $moved = 0;
        
        foreach ($items as $item) {
            if ($cart->addItem($item['product_id'], 1, $item['variant_id'])) {
                $this->removeItem($item['product_id'], $item['variant_id']);
                $moved++;
            }
        }
        
        return $moved;
    }
    
    public function updateItemPriority(int $itemId, int $priority): bool
    {
        $updated = $this->db->update(
            'wishlist_items',
            ['priority' => $priority],
            'id = ? AND wishlist_id = ?',
            [$itemId, $this->id]
        );
        
        if ($updated > 0) {
            $this->updated_at = date('Y-m-d H:i:s');
            $this->save();
        }
        
        return $updated > 0;
    }
    
    public function updateItemNotes(int $itemId, string $notes): bool
    {
        $updated = $this->db->update(
            'wishlist_items',
            ['notes' => $notes],
            'id = ? AND wishlist_id = ?',
            [$itemId, $this->id]
        );
        
        if ($updated > 0) {
            $this->updated_at = date('Y-m-d H:i:s');
            $this->save();
        }
        
        return $updated > 0;
    }
    
    public function getTotalValue(): float
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(COALESCE(p.sale_price, p.price)) as total
             FROM wishlist_items wi 
             JOIN products p ON wi.product_id = p.id 
             WHERE wi.wishlist_id = ? AND p.status = 'published'",
            [$this->id]
        );
        
        return $result ? (float)$result['total'] : 0;
    }
}