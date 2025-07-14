<h2 class="text-2xl font-semibold mb-6">Shared Wishlist</h2>
<nav class="mb-4 space-x-4 text-blue-700">
  <a href="/" class="hover:underline">Home</a>
</nav>
<?php if (!$items): ?>
  <p>This wishlist is empty.</p>
<?php else: ?>
  <div class="grid gap-6 md:grid-cols-3">
    <?php foreach ($items as $p): ?>
      <div class="border rounded shadow p-4">
        <h3 class="font-semibold text-lg mb-1"><?= htmlspecialchars($p->name) ?></h3>
        <p><?= htmlspecialchars($p->description) ?></p>
        <span class="block font-semibold">$<?= number_format($p->price, 2) ?></span>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>