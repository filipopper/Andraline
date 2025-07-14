<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = [
        'email', 'password', 'first_name', 'last_name', 'role', 
        'avatar', 'phone', 'birth_date', 'accessibility_preferences'
    ];
    
    public static function findByEmail(string $email): ?self
    {
        $users = self::where('email', '=', $email);
        return !empty($users) ? $users[0] : null;
    }
    
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }
    
    public function hashPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }
    
    public function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    
    public function isSeller(): bool
    {
        return $this->role === 'seller' || $this->role === 'admin';
    }
    
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
    
    public function addPoints(int $points, string $reason, string $referenceType = null, int $referenceId = null): void
    {
        $this->points += $points;
        $this->save();
        
        // Log the transaction
        $transaction = new PointTransaction([
            'user_id' => $this->id,
            'points' => $points,
            'type' => 'earned',
            'reason' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);
        $transaction->save();
    }
    
    public function spendPoints(int $points, string $reason): bool
    {
        if ($this->points < $points) {
            return false;
        }
        
        $this->points -= $points;
        $this->save();
        
        // Log the transaction
        $transaction = new PointTransaction([
            'user_id' => $this->id,
            'points' => -$points,
            'type' => 'spent',
            'reason' => $reason
        ]);
        $transaction->save();
        
        return true;
    }
    
    public function getOrders(): array
    {
        return Order::where('user_id', '=', $this->id);
    }
    
    public function getWishlists(): array
    {
        return Wishlist::where('user_id', '=', $this->id);
    }
    
    public function getDefaultWishlist(): ?Wishlist
    {
        $wishlists = $this->getWishlists();
        return !empty($wishlists) ? $wishlists[0] : null;
    }
    
    public function getBadges(): array
    {
        $sql = "SELECT b.*, ub.earned_at 
                FROM badges b 
                JOIN user_badges ub ON b.id = ub.badge_id 
                WHERE ub.user_id = ?
                ORDER BY ub.earned_at DESC";
        
        return $this->db->fetchAll($sql, [$this->id]);
    }
    
    public function awardBadge(int $badgeId): void
    {
        // Check if user already has this badge
        $existing = $this->db->fetchOne(
            "SELECT id FROM user_badges WHERE user_id = ? AND badge_id = ?",
            [$this->id, $badgeId]
        );
        
        if (!$existing) {
            $this->db->insert('user_badges', [
                'user_id' => $this->id,
                'badge_id' => $badgeId
            ]);
        }
    }
    
    public function updateTotalSpent(float $amount): void
    {
        $this->total_spent += $amount;
        $this->save();
        
        // Check for spending badges
        $this->checkSpendingBadges();
    }
    
    private function checkSpendingBadges(): void
    {
        // Award "Big Spender" badge if spent over $1000
        if ($this->total_spent >= 1000) {
            $badge = Badge::where('name', '=', 'Big Spender');
            if (!empty($badge)) {
                $this->awardBadge($badge[0]->id);
            }
        }
    }
    
    public function getReviews(): array
    {
        return Review::where('user_id', '=', $this->id);
    }
    
    public function getSubscriptions(): array
    {
        return Subscription::where('user_id', '=', $this->id);
    }
    
    public function generateEmailVerificationToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->email_verification_token = $token;
        $this->save();
        return $token;
    }
    
    public function generatePasswordResetToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->password_reset_token = $token;
        $this->password_reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->save();
        return $token;
    }
    
    public function verifyEmail(): void
    {
        $this->email_verified_at = date('Y-m-d H:i:s');
        $this->email_verification_token = null;
        $this->save();
        
        // Award welcome badge
        $badge = Badge::where('name', '=', 'Welcome Badge');
        if (!empty($badge)) {
            $this->awardBadge($badge[0]->id);
            $this->addPoints(100, 'Welcome bonus');
        }
    }
}