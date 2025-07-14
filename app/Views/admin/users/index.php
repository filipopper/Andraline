<h2 class="text-2xl font-semibold mb-6">Users</h2>
<nav class="mb-4 space-x-4 text-blue-700">
  <a href="/admin" class="hover:underline">Dashboard</a>
  <a href="/admin/users/create" class="hover:underline">Add User</a>
  <a href="/logout" class="hover:underline">Logout</a>
</nav>
<table class="min-w-full border divide-y divide-gray-200">
  <thead class="bg-gray-50">
    <tr>
      <th class="px-4 py-2 text-left">ID</th>
      <th class="px-4 py-2 text-left">Email</th>
      <th class="px-4 py-2 text-left">Role</th>
      <th class="px-4 py-2">Actions</th>
    </tr>
  </thead>
  <tbody class="bg-white divide-y divide-gray-100">
    <?php foreach ($users as $u): ?>
      <tr>
        <td class="px-4 py-2"><?= $u->id ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($u->email) ?></td>
        <td class="px-4 py-2"><?= htmlspecialchars($u->role) ?></td>
        <td class="px-4 py-2 text-center">
          <a class="text-red-600 hover:underline" href="/admin/users/delete?id=<?= $u->id ?>" onclick="return confirm('Delete user?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>