<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\Product;
use App\Models\User;

class AdminController
{
    public function dashboard(): void
    {
        $counts = [
            'products' => count(Product::all()),
            'users'    => count(User::all()),
        ];
        View::make('admin/dashboard', compact('counts'));
    }
}