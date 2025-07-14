<?php
namespace App\Core;

class View
{
    public static function make(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = dirname(__DIR__) . "/Views/{$view}.php";
        $base     = dirname(__DIR__) . '/Views/layouts/base.php';
        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require $base;
    }
}