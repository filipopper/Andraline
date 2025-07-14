<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController
{
    public function show(): void
    {
        $uid = $_SESSION['user']['id'] ?? 0;
        $orderId = (int) ($_GET['order_id'] ?? 0);
        $order = Order::find($orderId);
        if (!$order || $order->user_id != $uid) {
            echo 'Order not found';
            return;
        }
        $items = OrderItem::db()->query('SELECT * FROM order_items WHERE order_id = ' . $orderId)->fetchAll(\PDO::FETCH_OBJ);
        View::make('order/show', compact('order', 'items'));
    }

    public function history(): void
    {
        $uid = $_SESSION['user']['id'] ?? 0;
        if (!$uid) {
            header('Location: /login');
            exit;
        }
        $orders = Order::db()->query('SELECT * FROM orders WHERE user_id = ' . $uid . ' ORDER BY id DESC')->fetchAll(\PDO::FETCH_OBJ);
        View::make('order/history', compact('orders'));
    }

    public function invoice(): void
    {
        $uid = $_SESSION['user']['id'] ?? 0;
        $orderId = (int) ($_GET['order_id'] ?? 0);
        $order = Order::find($orderId);
        if (!$order || $order->user_id != $uid) {
            echo 'Order not found';
            return;
        }
        $file = \App\Services\InvoiceService::generate($orderId);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="invoice_'.$orderId.'.pdf"');
        readfile($file);
        exit;
    }
}