<?php

namespace App\Models;

use Core\Model;

class Badge extends Model
{
    protected string $table = 'badges';
    protected array $fillable = ['name', 'description', 'icon', 'points_required', 'criteria', 'is_active'];
    
    public static function active(): array
    {
        return self::where('is_active', '=', 1);
    }
    
    public function getCriteria(): ?array
    {
        return $this->criteria ? json_decode($this->criteria, true) : null;
    }
    
    public function setCriteria(array $criteria): void
    {
        $this->criteria = json_encode($criteria);
    }
    
    public function getEarnedCount(): int
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM user_badges WHERE badge_id = ?",
            [$this->id]
        );
        
        return $result ? (int)$result['count'] : 0;
    }
}