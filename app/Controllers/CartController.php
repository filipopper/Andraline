<?php
namespace App\Controllers;

use App\Core\View;
use App\Services\CartService;
use App\Models\Order;
use App\Services\InvoiceService;

class CartController
{
    public function index(): void
    {
        $items = CartService::detailedItems();
        $total = CartService::total();
        View::make('cart/index', compact('items', 'total'));
    }

    public function add(): void
    {
        $id  = (int) ($_GET['id'] ?? 0);
        $qty = (int) ($_GET['qty'] ?? 1);
        $variant = isset($_GET['variant']) ? (int)$_GET['variant'] : null;
        if ($id) {
            CartService::add($id, $qty, $variant);
        }
        header('Location: /cart');
    }

    public function update(): void
    {
        foreach ($_POST['qty'] ?? [] as $pid => $qty) {
            CartService::update((int)$pid, (int)$qty);
        }
        header('Location: /cart');
    }

    public function remove(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        CartService::remove($id);
        header('Location: /cart');
    }

    public function checkout(): void
    {
        $uid = $_SESSION['user']['id'] ?? 0;
        if (!$uid) {
            header('Location: /login');
            exit;
        }
        $items = CartService::detailedItems();
        $total = CartService::total();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['apply_coupon'])) {
                $code = trim($_POST['coupon'] ?? '');
                $applied = \App\Services\CartService::applyCoupon($code);
                header('Location: /cart/checkout');
                exit;
            }
            $address = $_POST['address'] ?? '';
            $discount = CartService::discount();
            $shipping = CartService::shipping($address);
            $grandTotal = CartService::grandTotal($address);
            $orderId = Order::createOrder($uid, $grandTotal, $address, $items);
            // store coupon usage
            if ($coupon = CartService::coupon()) { \App\Models\Coupon::incrementUse($coupon->id); Order::update($orderId, ['coupon_code'=>$coupon->code]); }
            CartService::clear();
            \App\Services\InvoiceService::generate($orderId);
            header('Location: /order?order_id=' . $orderId);
            exit;
        }
        $coupon = CartService::coupon();
        $discount = CartService::discount();
        $shipping = 0; // shipping to be calculated after address
        $grandTotal = CartService::subtotal() - $discount;
        View::make('cart/checkout', compact('items', 'total', 'coupon', 'discount', 'shipping', 'grandTotal'));
    }
}