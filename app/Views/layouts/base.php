<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= isset($metaTitle) ? htmlspecialchars($metaTitle) : 'MiniCommerce' ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php if(isset($metaDescription)): ?>
    <meta name="description" content="<?= htmlspecialchars($metaDescription) ?>">
  <?php endif; ?>
  <link rel="manifest" href="/manifest.json">
</head>
<?php $theme = $_COOKIE['theme'] ?? 'default'; ?>
<body class="min-h-screen flex flex-col <?= $theme === 'contrast' ? 'contrast' : ($theme === 'dark' ? 'dark' : '') ?>">
  <header class="bg-slate-800 text-white p-4 flex items-center justify-between">
    <h1 class="text-xl">MiniCommerce</h1>
    <button id="toggle-theme" class="bg-slate-600 px-2 py-1 rounded text-sm">A11y</button>
  </header>
  <main class="flex-grow p-4">
    <?= $content ?>
  </main>
  <footer class="bg-slate-100 p-4 text-center">
    &copy; <?= date('Y') ?>
  </footer>
  <script>
    // Accessibility toggle
    document.getElementById('toggle-theme').addEventListener('click', function () {
      const current = getCookie('theme') || 'default';
      const next = current === 'default' ? 'contrast' : current === 'contrast' ? 'dark' : 'default';
      document.cookie = 'theme=' + next + ';path=/;max-age=' + (60*60*24*30);
      location.reload();
    });

    function getCookie(name){return ('; '+document.cookie).split('; '+name+'=').pop().split(';')[0];}

    // Register service worker
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/service-worker.js').catch(console.error);
    }
  </script>
  <style>
    /* contrast theme */
    .contrast { filter: contrast(1.8) brightness(1.2); }
    /* dark theme */
    .dark { background-color:#1e293b; color:#f8fafc; }
    .dark a { color:#38bdf8; }
  </style>
</body>
</html>