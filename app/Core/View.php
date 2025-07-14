<?php

namespace Core;

class View
{
    private $layout = 'default';

    public function render($view, $data = [])
    {
        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = APP_PATH . "/Views/$view.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \Exception("View file not found: $viewFile");
        }
        
        // Get the content
        $content = ob_get_clean();
        
        // Include the layout
        $layoutFile = APP_PATH . "/Views/layouts/{$this->layout}.php";
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            // If no layout, just output the content
            echo $content;
        }
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function partial($view, $data = [])
    {
        extract($data);
        $viewFile = APP_PATH . "/Views/partials/$view.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \Exception("Partial view file not found: $viewFile");
        }
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public function formatPrice($price, $currency = '$')
    {
        return $currency . number_format($price, 2);
    }

    public function formatDate($date)
    {
        return date('M j, Y', strtotime($date));
    }

    public function formatDateTime($datetime)
    {
        return date('M j, Y g:i A', strtotime($datetime));
    }

    public function truncate($text, $length = 100)
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }

    public function slugify($text)
    {
        // Convert to lowercase
        $text = strtolower($text);
        // Replace non-alphanumeric characters with hyphens
        $text = preg_replace('/[^a-z0-9-]/', '-', $text);
        // Remove multiple consecutive hyphens
        $text = preg_replace('/-+/', '-', $text);
        // Remove leading and trailing hyphens
        return trim($text, '-');
    }

    public function asset($path)
    {
        return "/public/$path";
    }

    public function url($path = '')
    {
        return "/$path";
    }

    public function csrfToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}