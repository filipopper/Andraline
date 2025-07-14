<?php

namespace Core\Router;

class Router 
{
    private array $routes = [];
    private array $middlewares = [];
    private string $currentRoute = '';
    
    public function get(string $route, $callback, array $middleware = []): void
    {
        $this->addRoute('GET', $route, $callback, $middleware);
    }
    
    public function post(string $route, $callback, array $middleware = []): void
    {
        $this->addRoute('POST', $route, $callback, $middleware);
    }
    
    public function put(string $route, $callback, array $middleware = []): void
    {
        $this->addRoute('PUT', $route, $callback, $middleware);
    }
    
    public function delete(string $route, $callback, array $middleware = []): void
    {
        $this->addRoute('DELETE', $route, $callback, $middleware);
    }
    
    private function addRoute(string $method, string $route, $callback, array $middleware = []): void
    {
        $route = $this->normalizeRoute($route);
        $this->routes[$method][$route] = [
            'callback' => $callback,
            'middleware' => $middleware
        ];
    }
    
    public function resolve(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $this->getCurrentUri();
        
        if (!isset($this->routes[$method])) {
            $this->handleNotFound();
            return;
        }
        
        foreach ($this->routes[$method] as $route => $data) {
            $pattern = $this->convertRouteToRegex($route);
            if (preg_match($pattern, $uri, $matches)) {
                $this->currentRoute = $route;
                
                // Execute middleware
                foreach ($data['middleware'] as $middleware) {
                    if (!$this->executeMiddleware($middleware)) {
                        return;
                    }
                }
                
                // Extract parameters
                $params = array_slice($matches, 1);
                $this->executeCallback($data['callback'], $params);
                return;
            }
        }
        
        $this->handleNotFound();
    }
    
    private function normalizeRoute(string $route): string
    {
        return '/' . trim($route, '/');
    }
    
    private function getCurrentUri(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return '/' . trim($uri, '/');
    }
    
    private function convertRouteToRegex(string $route): string
    {
        // Convert parameters like {id} to regex groups
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }
    
    private function executeMiddleware(string $middleware): bool
    {
        $middlewareClass = "\\App\\Middleware\\{$middleware}";
        if (class_exists($middlewareClass)) {
            $instance = new $middlewareClass();
            return $instance->handle();
        }
        return true;
    }
    
    private function executeCallback($callback, array $params = []): void
    {
        if (is_string($callback)) {
            // Handle Controller@method format
            if (strpos($callback, '@') !== false) {
                [$controller, $method] = explode('@', $callback);
                $controllerClass = "\\App\\Controllers\\{$controller}";
                
                if (class_exists($controllerClass)) {
                    $instance = new $controllerClass();
                    if (method_exists($instance, $method)) {
                        call_user_func_array([$instance, $method], $params);
                        return;
                    }
                }
            }
        } elseif (is_callable($callback)) {
            call_user_func_array($callback, $params);
            return;
        }
        
        $this->handleNotFound();
    }
    
    private function handleNotFound(): void
    {
        http_response_code(404);
        include_once __DIR__ . '/../../app/Views/errors/404.php';
    }
    
    public function url(string $route, array $params = []): string
    {
        $url = $route;
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }
        return $url;
    }
}