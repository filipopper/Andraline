<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\Product;

class SellerController
{
    private int $sellerId;

    public function __construct()
    {
        if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'seller') {
            header('Location: /login');
            exit;
        }
        $this->sellerId = $_SESSION['user']['id'];
    }

    public function dashboard(): void
    {
        $pdo = Product::db();
        $count = $pdo->query('SELECT COUNT(*) FROM products WHERE seller_id = ' . (int)$this->sellerId)->fetchColumn();
        View::make('seller/dashboard', ['productCount' => $count]);
    }

    public function products(): void
    {
        $pdo = Product::db();
        $products = $pdo->query('SELECT * FROM products WHERE seller_id = ' . (int)$this->sellerId)->fetchAll(\PDO::FETCH_OBJ);
        View::make('seller/products', compact('products'));
    }
}