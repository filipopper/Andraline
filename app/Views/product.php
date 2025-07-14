<h2 class="text-2xl font-semibold mb-4"><?= htmlspecialchars($product->name ?? '') ?></h2>
<p class="mb-2"><?= htmlspecialchars($product->description ?? '') ?></p>
<p class="text-lg font-bold mb-4">$<?= number_format($product->price ?? 0, 2) ?></p>
<div class="space-x-2">
  <a class="text-blue-600 hover:underline" href="/compare/add?id=<?= $product->id ?>">Compare</a>
  <a class="text-pink-600 hover:underline" href="/wishlist/add?id=<?= $product->id ?>">Wishlist</a>
</div>
<form action="/cart/add" method="GET" class="space-y-4">
  <input type="hidden" name="id" value="<?= $product->id ?>">
  <?php if(!empty($variants)): ?>
    <div>
      <label class="block mb-1">Variant</label>
      <select name="variant" class="border p-2 rounded" required>
        <?php foreach($variants as $v): ?>
          <option value="<?= $v->id ?>"><?= htmlspecialchars($v->title) ?> - $<?= number_format($v->price,2) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  <?php endif; ?>
  <div>
    <label class="block mb-1">Quantity</label>
    <input class="border p-2 w-20" type="number" name="qty" value="1" min="1">
  </div>
  <button class="bg-green-600 text-white px-4 py-2 rounded" type="submit">Add to Cart</button>
</form>