<?php
namespace App\Services;

use App\Models\Product;

class CartService
{
    private const KEY = 'cart';

    public static function items(): array
    {
        return $_SESSION[self::KEY] ?? [];
    }

    public static function add(int $productId, int $qty = 1): void
    {
        $_SESSION[self::KEY][$productId] = ($_SESSION[self::KEY][$productId] ?? 0) + $qty;
    }

    public static function update(int $productId, int $qty): void
    {
        if ($qty <= 0) {
            self::remove($productId);
        } else {
            $_SESSION[self::KEY][$productId] = $qty;
        }
    }

    public static function remove(int $productId): void
    {
        unset($_SESSION[self::KEY][$productId]);
    }

    public static function clear(): void
    {
        unset($_SESSION[self::KEY]);
    }

    public static function detailedItems(): array
    {
        $items = [];
        foreach (self::items() as $pid => $qty) {
            $product = Product::find((int)$pid);
            if ($product) {
                $items[] = [
                    'id'    => $product->id,
                    'name'  => $product->name,
                    'price' => $product->price,
                    'qty'   => $qty,
                    'total' => $product->price * $qty,
                ];
            }
        }
        return $items;
    }

    public static function total(): float
    {
        return array_sum(array_column(self::detailedItems(), 'total'));
    }
}