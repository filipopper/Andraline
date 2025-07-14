<?php

namespace App\Models;

use Core\Model;

class PointTransaction extends Model
{
    protected string $table = 'point_transactions';
    protected array $fillable = ['user_id', 'points', 'type', 'reason', 'reference_type', 'reference_id'];
    
    public function getUser(): ?User
    {
        return User::find($this->user_id);
    }
    
    public static function getForUser(int $userId, int $limit = 50): array
    {
        $instance = new static();
        return $instance->db->fetchAll(
            "SELECT * FROM {$instance->table} WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }
}