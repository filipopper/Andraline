<h2 class="text-2xl font-semibold mb-6">Shopping Cart</h2>
<?php if (!$items): ?>
<p>Your cart is empty.</p>
<?php else: ?>
<form action="/cart/update" method="POST">
<table class="min-w-full border divide-y divide-gray-200 mb-4">
  <thead class="bg-gray-50">
    <tr>
      <th class="px-4 py-2 text-left">Product</th>
      <th class="px-4 py-2 text-left">Price</th>
      <th class="px-4 py-2 text-left">Qty</th>
      <th class="px-4 py-2 text-left">Total</th>
      <th class="px-4 py-2"></th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-100">
  <?php foreach ($items as $item): ?>
    <tr>
      <td class="px-4 py-2"><?= htmlspecialchars($item['name']) ?></td>
      <td class="px-4 py-2">$<?= number_format($item['price'],2) ?></td>
      <td class="px-4 py-2"><input class="border w-16" type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>"></td>
      <td class="px-4 py-2">$<?= number_format($item['total'],2) ?></td>
      <td class="px-4 py-2"><a class="text-red-600" href="/cart/remove?id=<?= $item['id'] ?>">Remove</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">Update Cart</button>
<a class="ml-4 bg-green-600 text-white px-4 py-2 rounded" href="/cart/checkout">Checkout ($<?= number_format($total,2) ?>)</a>
</form>
<?php endif; ?>