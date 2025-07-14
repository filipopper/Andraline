<?php
namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\User;

class UserController
{
    public function index(): void
    {
        $users = User::all();
        View::make('admin/users/index', compact('users'));
    }

    public function create(): void
    {
        View::make('admin/users/form');
    }

    public function store(): void
    {
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $name     = $_POST['name'] ?? '';
        $role     = $_POST['role'] ?? 'customer';

        User::createUser($email, $password, $name, $role);
        header('Location: /admin/users');
    }

    public function delete(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        User::delete($id);
        header('Location: /admin/users');
    }
}