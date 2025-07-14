<h2 class="text-2xl font-semibold mb-4"><?= htmlspecialchars($product->name ?? '') ?></h2>
<p class="mb-2"><?= htmlspecialchars($product->description ?? '') ?></p>
<p class="text-lg font-bold mb-4">$<?= number_format($product->price ?? 0, 2) ?></p>
<div class="space-x-2">
  <a class="text-blue-600 hover:underline" href="/compare/add?id=<?= $product->id ?>">Compare</a>
  <a class="text-pink-600 hover:underline" href="/wishlist/add?id=<?= $product->id ?>">Wishlist</a>
</div>