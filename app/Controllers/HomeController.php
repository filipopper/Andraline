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
}