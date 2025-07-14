<h2 class="text-2xl font-semibold mb-6">Order #<?= $order->id ?></h2>
<form action="/admin/orders/update" method="POST" class="mb-4">
  <input type="hidden" name="id" value="<?= $order->id ?>">
  <label class="mr-2">Status:</label>
  <select name="status" class="border rounded p-1">
    <?php foreach (['pending','paid','shipped','completed','refunded'] as $s): ?>
      <option value="<?= $s ?>" <?= $order->status===$s ? 'selected':'' ?>><?= ucfirst($s) ?></option>
    <?php endforeach; ?>
  </select>
  <button class="ml-2 bg-blue-600 text-white px-3 py-1 rounded" type="submit">Update</button>
</form>
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