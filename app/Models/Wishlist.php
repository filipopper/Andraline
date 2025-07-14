<?php
namespace App\Models;

use App\Core\Model;

class Wishlist extends Model
{
    protected static string $table = 'wishlists';

    public static function createForUser(int $userId): int
    {
        $token = bin2hex(random_bytes(16));
        return parent::create([
            'user_id' => $userId,
            'token'   => $token,
        ]);
    }

    public static function findByToken(string $token): ?object
    {
        $stmt = self::db()->prepare('SELECT * FROM wishlists WHERE token = :token LIMIT 1');
        $stmt->execute(['token' => $token]);
        return $stmt->fetch(\PDO::FETCH_OBJ) ?: null;
    }

    public static function items(int $wishlistId): array
    {
        $stmt = self::db()->prepare('SELECT p.* FROM products p JOIN wishlist_items wi ON wi.product_id = p.id WHERE wi.wishlist_id = :wid');
        $stmt->execute(['wid' => $wishlistId]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    public static function addProduct(int $wishlistId, int $productId): int
    {
        return WishlistItem::create([
            'wishlist_id' => $wishlistId,
            'product_id'  => $productId,
        ]);
    }
}