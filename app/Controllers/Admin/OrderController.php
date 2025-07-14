<?php
namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController
{
    public function index(): void
    {
        $orders = Order::all();
        View::make('admin/orders/index', compact('orders'));
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $order = Order::find($id);
        if (!$order) {
            header('Location: /admin/orders');
            exit;
        }
        $items = OrderItem::db()->query('SELECT * FROM order_items WHERE order_id = ' . $id)->fetchAll(\PDO::FETCH_OBJ);
        View::make('admin/orders/show', compact('order', 'items'));
    }

    public function updateStatus(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'pending';
        Order::update($id, ['status' => $status]);
        header('Location: /admin/orders/show?id=' . $id);
    }
}