<?php

namespace Core;

class Controller
{
    protected $params = [];
    protected $db;
    protected $view;

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->db = Database::getInstance()->getPdo();
        $this->view = new View();
    }

    protected function render($view, $data = [])
    {
        return $this->view->render($view, $data);
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    protected function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
    }

    protected function getGet($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    protected function getParam($key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    protected function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }

    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    protected function requireRole($role)
    {
        $this->requireLogin();
        $user = $this->getCurrentUser();
        
        if ($user['role'] !== $role && $user['role'] !== 'admin') {
            $this->redirect('/unauthorized');
        }
    }

    protected function setFlash($type, $message)
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }

    protected function getFlash()
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    protected function validateCSRF()
    {
        if ($this->isPost()) {
            $token = $this->getPost('csrf_token');
            if (!$token || $token !== $_SESSION['csrf_token']) {
                $this->setFlash('error', 'Invalid request');
                $this->redirect('/');
            }
        }
    }

    protected function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}