<h2 class="text-2xl font-semibold mb-6">Add User</h2>
<nav class="mb-4 space-x-4 text-blue-700">
  <a href="/admin/users" class="hover:underline">Back to Users</a>
  <a href="/logout" class="hover:underline">Logout</a>
</nav>
<form class="space-y-4 max-w-lg" action="/admin/users/store" method="POST">
  <div>
    <label class="block mb-1">Name</label>
    <input class="border rounded w-full p-2" type="text" name="name" required>
  </div>
  <div>
    <label class="block mb-1">Email</label>
    <input class="border rounded w-full p-2" type="email" name="email" required>
  </div>
  <div>
    <label class="block mb-1">Password</label>
    <input class="border rounded w-full p-2" type="password" name="password" required>
  </div>
  <div>
    <label class="block mb-1">Role</label>
    <select class="border rounded w-full p-2" name="role">
      <option value="customer">Customer</option>
      <option value="admin">Admin</option>
    </select>
  </div>
  <button class="bg-green-600 text-white px-4 py-2 rounded" type="submit">Create</button>
</form>