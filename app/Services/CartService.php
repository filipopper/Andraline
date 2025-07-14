<?php
namespace App\Services;

use App\Models\Product;
use App\Services\ShippingService;

class CartService
{
    private const KEY = 'cart';

    public static function items(): array
    {
        return $_SESSION[self::KEY] ?? [];
    }

    public static function add(int $productId, int $qty = 1, ?int $variantId=null): void
    {
        $key = $variantId ? $productId.'_'.$variantId : $productId;
        $_SESSION[self::KEY][$key] = ($_SESSION[self::KEY][$key] ?? 0) + $qty;
    }

    public static function update(int $productId, int $qty): void
    {
        $_SESSION[self::KEY][$productId] = $qty>0?$qty:0;
        if($qty<=0) unset($_SESSION[self::KEY][$productId]);
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
        foreach (self::items() as $key => $qty) {
            $parts = explode('_',$key);
            $pid = (int)$parts[0];
            $variantId = $parts[1]??null;
            $product = Product::find($pid);
            if (!$product) continue;
            $price = $product->price;
            $variantTitle=null;
            if ($variantId) {
                $variant = \App\Models\ProductVariant::find((int)$variantId);
                if ($variant) { $price = $variant->price; $variantTitle=$variant->title; }
            }
            $items[] = [
                'key'  => $key,
                'id'   => $pid,
                'variant_id'=>$variantId,
                'variant'=>$variantTitle,
                'name' => $product->name . ($variantTitle?(' - '.$variantTitle):''),
                'price'=> $price,
                'qty'  => $qty,
                'total'=> $price*$qty,
                'variant_obj'=> $variant ?? null,
            ];
        }
        return $items;
    }

    public static function total(): float
    {
        return array_sum(array_column(self::detailedItems(), 'total'));
    }

    public static function applyCoupon(string $code): ?object
    {
        $coupon = \App\Models\Coupon::findValid($code);
        if ($coupon) {
            $_SESSION['coupon'] = $coupon->code;
        }
        return $coupon;
    }

    public static function coupon(): ?object
    {
        if (empty($_SESSION['coupon'])) return null;
        return \App\Models\Coupon::findValid($_SESSION['coupon']);
    }

    public static function subtotal(): float
    {
        return array_sum(array_column(self::detailedItems(), 'total'));
    }

    public static function discount(): float
    {
        $coupon = self::coupon();
        if (!$coupon) return 0;
        $sub = self::subtotal();
        return $coupon->type === 'percent' ? ($sub * $coupon->discount / 100) : $coupon->discount;
    }

    public static function shipping(string $address): float
    {
        return ShippingService::calculate($address);
    }

    public static function grandTotal(string $address): float
    {
        return max(0, self::subtotal() - self::discount()) + self::shipping($address);
    }
}