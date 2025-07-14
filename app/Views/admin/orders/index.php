<h2 class="text-2xl font-semibold mb-6">Orders</h2>
<table class="min-w-full border divide-y divide-gray-200">
  <thead class="bg-gray-50">
    <tr>
      <th class="px-4 py-2 text-left">ID</th>
      <th class="px-4 py-2 text-left">User</th>
      <th class="px-4 py-2 text-left">Total</th>
      <th class="px-4 py-2 text-left">Status</th>
      <th class="px-4 py-2 text-left"></th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-100">
    <?php foreach ($orders as $o): ?>
    <tr>
      <td class="px-4 py-2">#<?= $o->id ?></td>
      <td class="px-4 py-2"><?= $o->user_id ?></td>
      <td class="px-4 py-2">$<?= number_format($o->total,2) ?></td>
      <td class="px-4 py-2"><?= htmlspecialchars($o->status) ?></td>
      <td class="px-4 py-2"><a class="text-blue-600 hover:underline" href="/admin/orders/show?id=<?= $o->id ?>">View</a></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>