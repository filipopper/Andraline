<div class="max-w-md mx-auto mt-10">
  <h2 class="text-2xl font-semibold mb-4">Admin Login</h2>
  <?php if (!empty($error)): ?>
    <p class="text-red-600 mb-2">Invalid credentials.</p>
  <?php endif; ?>
  <form class="space-y-4" action="/login" method="POST">
    <div>
      <label class="block mb-1">Email</label>
      <input type="email" name="email" class="border rounded w-full p-2" required>
    </div>
    <div>
      <label class="block mb-1">Password</label>
      <input type="password" name="password" class="border rounded w-full p-2" required>
    </div>
    <button class="bg-blue-600 text-white px-4 py-2 rounded" type="submit">Login</button>
  </form>
</div>