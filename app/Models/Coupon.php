<?php
namespace App\Models;

use App\Core\Model;

class Coupon extends Model
{
    protected static string $table = 'coupons';

    public static function findValid(string $code): ?object
    {
        $stmt = self::db()->prepare('SELECT * FROM coupons WHERE code = :code');
        $stmt->execute(['code' => $code]);
        $c = $stmt->fetch(\PDO::FETCH_OBJ);
        if (!$c) return null;
        if ($c->expires_at && strtotime($c->expires_at) < time()) return null;
        if ($c->max_uses && $c->uses >= $c->max_uses) return null;
        return $c;
    }

    public static function incrementUse(int $id): void
    {
        self::db()->prepare('UPDATE coupons SET uses = uses + 1 WHERE id = :id')->execute(['id' => $id]);
    }
}