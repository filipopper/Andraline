<?php
namespace App\Controllers;

use App\Core\View;

class CatalogController
{
    // TODO: Inject repositories/services when added
    public function index(): void
    {
        // Placeholder: fetch products, apply filters from $_GET
        $filters = $_GET;
        // $products = ProductService::filter($filters);
        $products = [];
        View::make('catalog/index', compact('products'));
    }
}