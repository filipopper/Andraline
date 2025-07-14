<h2 class="text-2xl font-semibold mb-6">Products</h2>
<nav class="mb-4 space-x-4 text-blue-700">
  <a href="/admin" class="hover:underline">Dashboard</a>
  <a href="/admin/products/create" class="hover:underline">Add Product</a>
  <a href="/logout" class="hover:underline">Logout</a>
</nav>
<table class="min-w-full border divide-y divide-gray-200">
  <thead class="bg-gray-50">
    <tr>
      <th class="px-4 py-2 text-left">ID</th>
      <th class="px-4 py-2 text-left">Name</th>
      <th class="px-4 py-2 text-left">Price</th>
      <th class="px-4 py-2">Actions</th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-100">
    <?php foreach ($products as $p): ?>
      <tr>
        <td class="px-4 py-2"><?= $p->id ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($p->name) ?></td>
        <td class="px-4 py-2">$<?= number_format($p->price,2) ?></td>
        <td class="px-4 py-2 space-x-2 text-center">
          <a class="text-blue-600 hover:underline" href="/admin/products/edit?id=<?= $p->id ?>">Edit</a>
          <a class="text-red-600 hover:underline" href="/admin/products/delete?id=<?= $p->id ?>" onclick="return confirm('Delete product?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>