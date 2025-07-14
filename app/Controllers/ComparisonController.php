<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\Product;

class ComparisonController
{
    private const SESSION_KEY = 'compare';

    public function add(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id) {
            $_SESSION[self::SESSION_KEY] = $_SESSION[self::SESSION_KEY] ?? [];
            if (!in_array($id, $_SESSION[self::SESSION_KEY])) {
                $_SESSION[self::SESSION_KEY][] = $id;
            }
        }
        header('Location: /compare');
    }

    public function remove(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if (isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = array_values(array_filter($_SESSION[self::SESSION_KEY], fn($pid) => $pid != $id));
        }
        header('Location: /compare');
    }

    public function index(): void
    {
        $ids = $_SESSION[self::SESSION_KEY] ?? [];
        $products = [];
        if ($ids) {
            $in = implode(',', array_map('intval', $ids));
            $pdo = Product::db();
            $products = $pdo->query("SELECT * FROM products WHERE id IN ($in)")->fetchAll(\PDO::FETCH_OBJ);
        }
        View::make('compare/index', compact('products'));
    }
}