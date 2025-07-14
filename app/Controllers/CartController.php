<?php
namespace App\Controllers;

use App\Core\View;
use App\Services\CartService;
use App\Models\Order;

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
        if ($id) {
            CartService::add($id, $qty);
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
            $address = $_POST['address'] ?? '';
            $orderId = Order::createOrder($uid, $total, $address, $items);
            CartService::clear();
            header('Location: /order?order_id=' . $orderId);
            exit;
        }
        View::make('cart/checkout', compact('items', 'total'));
    }
}