<?php
namespace App\Services;

use Dompdf\Dompdf;
use App\Models\Order;
use App\Models\OrderItem;

class InvoiceService
{
    public static function generate(int $orderId): string
    {
        $order = Order::find($orderId);
        if (!$order) {
            throw new \Exception('Order not found');
        }
        $items = OrderItem::db()->query('SELECT * FROM order_items WHERE order_id = ' . $orderId)->fetchAll(\PDO::FETCH_OBJ);
        $html = self::buildHtml($order, $items);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $pdf = $dompdf->output();

        $path = dirname(__DIR__,2) . '/storage/invoices';
        if (!is_dir($path)) mkdir($path, 0775, true);
        $file = $path . '/invoice_' . $orderId . '.pdf';
        file_put_contents($file, $pdf);
        return $file;
    }

    private static function buildHtml(object $order, array $items): string
    {
        ob_start(); ?>
        <h1>Invoice #<?= $order->id ?></h1>
        <p>Date: <?= date('Y-m-d', strtotime($order->created_at)) ?></p>
        <p>Status: <?= htmlspecialchars($order->status) ?></p>
        <table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
            </thead>
            <tbody>
            <?php foreach ($items as $i): ?>
                <tr>
                    <td>ID <?= $i->product_id ?></td>
                    <td><?= $i->quantity ?></td>
                    <td>$<?= number_format($i->price,2) ?></td>
                    <td>$<?= number_format($i->price*$i->quantity,2) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <h3>Total: $<?= number_format($order->total,2) ?></h3>
        <?php return ob_get_clean();
    }
}