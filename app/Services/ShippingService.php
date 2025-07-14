<?php
namespace App\Services;

class ShippingService
{
    public static function calculate(string $address): float
    {
        // Simple rule: domestic (contains 'USA' or 'US') flat $5, else $15
        $domestic = stripos($address, 'US') !== false || stripos($address, 'USA') !== false;
        return $domestic ? 5.00 : 15.00;
    }
}