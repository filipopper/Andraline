<?php
namespace App\Models;

use App\Core\Model;

class Subscription extends Model
{
    protected static string $table = 'subscriptions';

    public static function activeForUser(int $userId): ?object
    {
        $stmt = self::db()->prepare('SELECT * FROM subscriptions WHERE user_id = :uid AND status = "active" LIMIT 1');
        $stmt->execute(['uid' => $userId]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }
}