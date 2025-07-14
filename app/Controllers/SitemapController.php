<?php
namespace App\Controllers;

use App\Models\Product;

class SitemapController
{
    public function index(): void
    {
        header('Content-Type: application/xml');
        $base = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
        $urls = [
            '',
        ];
        $products = Product::all();
        foreach ($products as $p) {
            $urls[] = '/product?id=' . $p->id; // product route not yet implemented
        }
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $u) {
            echo '<url><loc>' . htmlspecialchars($base . $u) . '</loc></url>';
        }
        echo '</urlset>';
    }
}