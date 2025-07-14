<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MiniCommerce</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="manifest" href="/manifest.json">
</head>
<body class="min-h-screen flex flex-col">
  <header class="bg-slate-800 text-white p-4">
    <h1 class="text-xl">MiniCommerce</h1>
  </header>
  <main class="flex-grow p-4">
    <?= $content ?>
  </main>
  <footer class="bg-slate-100 p-4 text-center">
    &copy; <?= date('Y') ?>
  </footer>
  <script src="/service-worker.js"></script>
</body>
</html>