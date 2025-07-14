<?php
namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Product;

class ProductController
{
    public function index(): void
    {
        $products = Product::all();
        View::make('admin/products/index', compact('products'));
    }

    public function create(): void
    {
        View::make('admin/products/form');
    }

    public function store(): void
    {
        $data = [
            'name'        => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price'       => (float) ($_POST['price'] ?? 0),
        ];
        Product::create($data);
        header('Location: /admin/products');
    }

    public function edit(): void
    {
        $id      = (int) ($_GET['id'] ?? 0);
        $product = Product::find($id);
        if (!$product) {
            header('Location: /admin/products');
            exit;
        }
        View::make('admin/products/form', compact('product'));
    }

    public function update(): void
    {
        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'name'        => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'price'       => (float) ($_POST['price'] ?? 0),
        ];
        Product::update($id, $data);
        header('Location: /admin/products');
    }

    public function delete(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        Product::delete($id);
        header('Location: /admin/products');
    }
}