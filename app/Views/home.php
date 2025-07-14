<div class="grid gap-6 md:grid-cols-3">
  <?php foreach ($products as $p): ?>
    <div class="border rounded shadow p-4">
      <h2 class="font-semibold text-lg"><?= htmlspecialchars($p->name) ?></h2>
      <p><?= htmlspecialchars($p->description) ?></p>
      <span class="block font-semibold mb-2">$<?= number_format($p->price, 2) ?></span>
    </div>
  <?php endforeach; ?>
</div>