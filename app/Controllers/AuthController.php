<?php
namespace App\Controllers;

use App\Core\View;
use App\Models\User;

class AuthController
{
    public function showLogin(): void
    {
        if (!empty($_SESSION['user'])) {
            header('Location: /admin');
            exit;
        }
        $error = $_GET['error'] ?? null;
        View::make('login', compact('error'));
    }

    public function login(): void
    {
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::verifyCredentials($email, $password);
        if ($user) {
            $_SESSION['user'] = [
                'id'   => $user->id,
                'role' => $user->role,
                'name' => $user->name,
            ];
            header('Location: /admin');
            exit;
        }
        header('Location: /login?error=invalid');
    }

    public function logout(): void
    {
        session_destroy();
        header('Location: /');
    }

    public function showRegister(): void
    {
        View::make('register');
    }

    public function register(): void
    {
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $name     = $_POST['name'] ?? '';
        if ($email && $password) {
            $uid = \App\Models\User::createUser($email, $password, $name);
            $_SESSION['user'] = ['id'=>$uid,'role'=>'customer','name'=>$name];
            header('Location: /');
            exit;
        }
        header('Location: /register');
    }
}