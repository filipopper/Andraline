<?php

namespace Core;

use Core\Database\Database;

abstract class Controller
{
    protected Database $db;
    protected array $data = [];
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->data['csrf_token'] = $this->generateCsrfToken();
    }
    
    protected function view(string $view, array $data = []): void
    {
        $data = array_merge($this->data, $data);
        
        // Extract variables for the view
        extract($data);
        
        $viewFile = __DIR__ . "/../app/Views/{$view}.php";
        
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$viewFile}");
        }
        
        include $viewFile;
    }
    
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }
    
    protected function input(string $key, $default = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $_POST[$key] ?? $default;
        }
        return $_GET[$key] ?? $default;
    }
    
    protected function validate(array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $this->input($field);
            $ruleArray = explode('|', $rule);
            
            foreach ($ruleArray as $singleRule) {
                $ruleParts = explode(':', $singleRule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;
                
                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field][] = ucfirst($field) . ' is required';
                        }
                        break;
                        
                    case 'email':
                        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = ucfirst($field) . ' must be a valid email';
                        }
                        break;
                        
                    case 'min':
                        if (!empty($value) && strlen($value) < (int)$ruleValue) {
                            $errors[$field][] = ucfirst($field) . " must be at least {$ruleValue} characters";
                        }
                        break;
                        
                    case 'max':
                        if (!empty($value) && strlen($value) > (int)$ruleValue) {
                            $errors[$field][] = ucfirst($field) . " must not exceed {$ruleValue} characters";
                        }
                        break;
                        
                    case 'numeric':
                        if (!empty($value) && !is_numeric($value)) {
                            $errors[$field][] = ucfirst($field) . ' must be a number';
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }
    
    protected function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    protected function verifyCsrfToken(): bool
    {
        $token = $this->input('csrf_token');
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    protected function flash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }
    
    protected function getFlash(string $key): ?string
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    protected function auth(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
    
    protected function isGuest(): bool
    {
        return !isset($_SESSION['user']);
    }
    
    protected function requireAuth(): void
    {
        if ($this->isGuest()) {
            $this->redirect('/login');
        }
    }
    
    protected function requireAdmin(): void
    {
        $user = $this->auth();
        if (!$user || $user['role'] !== 'admin') {
            $this->redirect('/');
        }
    }
}