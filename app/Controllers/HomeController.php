<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(): void
    {
        $featuredProducts = Product::featured(8);
        $latestProducts = Product::latest(12);
        $categories = Category::getRootCategories();
        
        $this->view('home/index', [
            'featured_products' => $featuredProducts,
            'latest_products' => $latestProducts,
            'categories' => $categories,
            'page_title' => 'Welcome to LightCommerce'
        ]);
    }
}