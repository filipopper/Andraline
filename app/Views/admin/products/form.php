<?php $editing = isset($product); ?>
<h2 class="text-2xl font-semibold mb-6"><?= $editing ? 'Edit' : 'Add' ?> Product</h2>
<nav class="mb-4 space-x-4 text-blue-700">
  <a href="/admin/products" class="hover:underline">Back to Products</a>
  <a href="/logout" class="hover:underline">Logout</a>
</nav>
<form class="space-y-4 max-w-lg" action="<?= $editing ? '/admin/products/update' : '/admin/products/store' ?>" method="POST">
  <?php if ($editing): ?>
    <input type="hidden" name="id" value="<?= $product->id ?>">
  <?php endif; ?>
  <div>
    <label class="block mb-1">Name</label>
    <input class="border rounded w-full p-2" type="text" name="name" value="<?= $editing ? htmlspecialchars($product->name) : '' ?>" required>
  </div>
  <div>
    <label class="block mb-1">Description</label>
    <textarea class="border rounded w-full p-2" name="description" rows="3" required><?= $editing ? htmlspecialchars($product->description) : '' ?></textarea>
  </div>
  <div>
    <label class="block mb-1">Price ($)</label>
    <input class="border rounded w-full p-2" type="number" step="0.01" name="price" value="<?= $editing ? $product->price : '' ?>" required>
  </div>
  <button class="bg-green-600 text-white px-4 py-2 rounded" type="submit">Save</button>
</form>