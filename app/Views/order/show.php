<h2 class="text-2xl font-semibold mb-6">Order #<?= $order->id ?></h2>
<p>Status: <span class="font-semibold"><?= htmlspecialchars($order->status) ?></span></p>
<p>Total: $<?= number_format($order->total,2) ?></p>
<p class="mt-4"><a class="text-blue-600 hover:underline" href="/order/invoice?order_id=<?= $order->id ?>">Download Invoice (PDF)</a></p>
<h3 class="text-xl font-semibold mt-4 mb-2">Items</h3>
<table class="min-w-full border divide-y divide-gray-200">
  <thead class="bg-gray-50">
    <tr>
      <th class="px-4 py-2 text-left">Product</th>
      <th class="px-4 py-2 text-left">Qty</th>
      <th class="px-4 py-2 text-left">Price</th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-100">
    <?php foreach ($items as $it): ?>
    <tr>
      <td class="px-4 py-2">ID <?= $it->product_id ?></td>
      <td class="px-4 py-2"><?= $it->quantity ?></td>
      <td class="px-4 py-2">$<?= number_format($it->price,2) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>