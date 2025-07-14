<h2 class="text-2xl font-semibold mb-6">Product Comparison</h2>
<nav class="mb-4 space-x-4 text-blue-700">
  <a href="/" class="hover:underline">Home</a>
</nav>
<?php if (!$products): ?>
  <p>No products selected for comparison.</p>
<?php else: ?>
  <table class="min-w-full border divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left">Name</th>
        <th class="px-4 py-2 text-left">Description</th>
        <th class="px-4 py-2 text-left">Price</th>
        <th class="px-4 py-2">Remove</th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-100">
      <?php foreach ($products as $p): ?>
        <tr>
          <td class="px-4 py-2 font-semibold"><?= htmlspecialchars($p->name) ?></td>
          <td class="px-4 py-2"><?= htmlspecialchars($p->description) ?></td>
          <td class="px-4 py-2">$<?= number_format($p->price, 2) ?></td>
          <td class="px-4 py-2 text-center"><a class="text-red-600 hover:underline" href="/compare/remove?id=<?= $p->id ?>">Remove</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>