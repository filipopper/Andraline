<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\Product;

class HomeController
{
    public function index(): void
    {
        $products = Product::all();
        View::make('home', compact('products'));
    }

    public function product(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $product = \App\Models\Product::find($id);
        if (!$product) {
            echo 'Product not found';
            return;
        }
        $variants = \App\Models\ProductVariant::variantsForProduct($id);
        $metaTitle = $product->name . ' - MiniCommerce';
        $metaDescription = substr($product->description, 0, 150);
        \App\Core\View::make('product', compact('product','variants', 'metaTitle', 'metaDescription'));
    }
}