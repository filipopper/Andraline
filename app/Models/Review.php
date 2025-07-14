<?php

namespace App\Models;

use Core\Model;

class Review extends Model
{
    protected string $table = 'reviews';
    protected array $fillable = [
        'product_id', 'user_id', 'order_id', 'rating', 'title', 
        'comment', 'is_verified', 'is_approved'
    ];
    
    public function getProduct(): ?Product
    {
        return Product::find($this->product_id);
    }
    
    public function getUser(): ?User
    {
        return User::find($this->user_id);
    }
    
    public function getOrder(): ?Order
    {
        return $this->order_id ? Order::find($this->order_id) : null;
    }
    
    public function markHelpful(int $userId, bool $isHelpful): bool
    {
        // Check if user already voted
        $existing = $this->db->fetchOne(
            "SELECT id, is_helpful FROM review_votes WHERE review_id = ? AND user_id = ?",
            [$this->id, $userId]
        );
        
        if ($existing) {
            // Update existing vote
            if ($existing['is_helpful'] != $isHelpful) {
                $this->db->update(
                    'review_votes',
                    ['is_helpful' => $isHelpful],
                    'id = ?',
                    [$existing['id']]
                );
                
                // Update helpful count
                if ($isHelpful) {
                    $this->helpful_count++;
                } else {
                    $this->helpful_count--;
                }
                $this->save();
            }
            return true;
        }
        
        // Create new vote
        $this->db->insert('review_votes', [
            'review_id' => $this->id,
            'user_id' => $userId,
            'is_helpful' => $isHelpful
        ]);
        
        if ($isHelpful) {
            $this->helpful_count++;
            $this->save();
        }
        
        return true;
    }
    
    public function approve(): void
    {
        $this->is_approved = true;
        $this->save();
        
        // Update product rating
        $product = $this->getProduct();
        if ($product) {
            $product->updateRating();
        }
        
        // Award points to reviewer
        $user = $this->getUser();
        if ($user) {
            $user->addPoints(50, 'Product review', 'review', $this->id);
            
            // Award reviewer badge
            $badge = Badge::where('name', '=', 'Reviewer');
            if (!empty($badge)) {
                $user->awardBadge($badge[0]->id);
            }
        }
    }
    
    public function reject(): void
    {
        $this->is_approved = false;
        $this->save();
        
        // Update product rating
        $product = $this->getProduct();
        if ($product) {
            $product->updateRating();
        }
    }
    
    public static function getForProduct(int $productId, bool $approvedOnly = true): array
    {
        $instance = new static();
        $sql = "SELECT r.*, u.first_name, u.last_name 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ?";
        
        $params = [$productId];
        
        if ($approvedOnly) {
            $sql .= " AND r.is_approved = 1";
        }
        
        $sql .= " ORDER BY r.created_at DESC";
        
        return $instance->db->fetchAll($sql, $params);
    }
    
    public function getUserVote(int $userId): ?bool
    {
        $vote = $this->db->fetchOne(
            "SELECT is_helpful FROM review_votes WHERE review_id = ? AND user_id = ?",
            [$this->id, $userId]
        );
        
        return $vote ? (bool)$vote['is_helpful'] : null;
    }
    
    public function getStarRating(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }
}