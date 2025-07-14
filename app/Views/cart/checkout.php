<h2 class="text-2xl font-semibold mb-6">Checkout</h2>
<form action="/cart/checkout" method="POST" class="space-y-4 max-w-lg">
  <div>
    <label class="block mb-1">Shipping Address</label>
    <textarea class="border rounded w-full p-2" name="address" rows="3" required></textarea>
  </div>
  <h3 class="text-xl font-semibold">Order Summary</h3>
  <ul class="space-y-1">
    <?php foreach ($items as $item): ?>
      <li><?= htmlspecialchars($item['name']) ?> × <?= $item['qty'] ?> – $<?= number_format($item['total'],2) ?></li>
    <?php endforeach; ?>
  </ul>
  <p class="font-bold">Total: $<?= number_format($total,2) ?></p>
  <button class="bg-green-600 text-white px-4 py-2 rounded" type="submit">Place Order</button>
</form>