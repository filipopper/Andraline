<h2 class="text-2xl font-semibold mb-6">My Orders</h2>
<?php if (!$orders): ?>
<p>No orders yet.</p>
<?php else: ?>
<table class="min-w-full border divide-y divide-gray-200">
  <thead class="bg-gray-50">
    <tr>
      <th class="px-4 py-2 text-left">Order #</th>
      <th class="px-4 py-2 text-left">Date</th>
      <th class="px-4 py-2 text-left">Total</th>
      <th class="px-4 py-2 text-left">Status</th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-100">
  <?php foreach ($orders as $o): ?>
    <tr>
      <td class="px-4 py-2"><a class="text-blue-600 hover:underline" href="/order?order_id=<?= $o->id ?>">#<?= $o->id ?></a></td>
      <td class="px-4 py-2"><?= date('Y-m-d', strtotime($o->created_at)) ?></td>
      <td class="px-4 py-2">$<?= number_format($o->total,2) ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($o->status) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>