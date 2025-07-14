<h2 class="text-2xl font-semibold mb-6">Admin Dashboard</h2>
<nav class="mb-6 space-x-4 text-blue-700">
  <a href="/admin/products" class="hover:underline">Products</a>
  <a href="/admin/users" class="hover:underline">Users</a>
  <a href="/logout" class="hover:underline">Logout</a>
</nav>
<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
  <div class="p-4 border rounded shadow">
    <h3 class="text-xl font-semibold">Products</h3>
    <p class="text-3xl"><?= $counts['products'] ?></p>
  </div>
  <div class="p-4 border rounded shadow">
    <h3 class="text-xl font-semibold">Users</h3>
    <p class="text-3xl"><?= $counts['users'] ?></p>
  </div>
</div>