<?php
namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    protected static string $table = 'orders';

    public static function createOrder(int $userId, float $total, string $address, array $items): int
    {
        $orderId = parent::create([
            'user_id' => $userId,
            'total'   => $total,
            'address' => $address,
            'status'  => 'paid',
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id'   => $orderId,
                'product_id' => $item['id'],
                'quantity'   => $item['qty'],
                'price'      => $item['price'],
                'variant'    => $item['variant'] ?? null,
            ]);
        }

        return $orderId;
    }
}