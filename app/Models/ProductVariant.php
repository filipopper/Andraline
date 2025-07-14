<?php
namespace App\Models;

use App\Core\Model;

class ProductVariant extends Model
{
    protected static string $table = 'product_variants';

    public static function variantsForProduct(int $productId): array
    {
        $stmt = self::db()->prepare('SELECT * FROM product_variants WHERE product_id = :pid');
        $stmt->execute(['pid' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}