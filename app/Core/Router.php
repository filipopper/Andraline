<?php
namespace App\Core;

class Router
{
    protected array $routes = ['GET' => [], 'POST' => []];
    protected array $middleware = [];

    public function get(string $uri, array $action): self
    {
        $this->routes['GET'][$this->normalize($uri)] = $action;
        return $this;
    }

    public function post(string $uri, array $action): self
    {
        $this->routes['POST'][$this->normalize($uri)] = $action;
        return $this;
    }

    public function middleware(string $name): self
    {
        $key = array_key_last($this->routes['GET'] + $this->routes['POST']);
        $this->middleware[$key] = $name;
        return $this;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $this->normalize($_SERVER['REQUEST_URI'] ?? '/');

        $action = $this->routes[$method][$uri] ?? null;

        if (!$action) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        // Basic auth middleware example
        if (isset($this->middleware[$uri])) {
            $mw = $this->middleware[$uri];
            if ($mw === 'auth' && empty($_SESSION['user'])) {
                header('Location: /login');
                exit;
            }
        }

        [$class, $fn] = $action;
        call_user_func([new $class, $fn]);
    }

    private function normalize(string $uri): string
    {
        return rtrim(parse_url($uri, PHP_URL_PATH), '/') ?: '/';
    }
}