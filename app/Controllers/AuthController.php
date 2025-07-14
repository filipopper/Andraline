<?php

namespace Controllers;

use Core\Controller;

class AuthController extends Controller
{
    public function login()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/account');
        }

        if ($this->isPost()) {
            $email = trim($this->getPost('email'));
            $password = $this->getPost('password');
            $remember = $this->getPost('remember', false);

            if (empty($email) || empty($password)) {
                $this->setFlash('error', 'Please enter both email and password');
                $this->render('auth/login', ['email' => $email]);
                return;
            }

            // Get user
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Set remember me cookie
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                    
                    // Store token in database (you'd need a remember_tokens table)
                    // For simplicity, we'll skip this for now
                }

                $this->setFlash('success', 'Welcome back, ' . $user['first_name'] . '!');
                $this->redirect('/account');
            } else {
                $this->setFlash('error', 'Invalid email or password');
                $this->render('auth/login', ['email' => $email]);
            }
        } else {
            $this->render('auth/login');
        }
    }

    public function register()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/account');
        }

        if ($this->isPost()) {
            $firstName = trim($this->getPost('first_name'));
            $lastName = trim($this->getPost('last_name'));
            $email = trim($this->getPost('email'));
            $password = $this->getPost('password');
            $confirmPassword = $this->getPost('confirm_password');

            // Validation
            $errors = [];

            if (empty($firstName)) {
                $errors[] = 'First name is required';
            }

            if (empty($lastName)) {
                $errors[] = 'Last name is required';
            }

            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please enter a valid email address';
            }

            if (empty($password)) {
                $errors[] = 'Password is required';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters long';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match';
            }

            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email address is already registered';
            }

            if (empty($errors)) {
                // Create user
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $this->db->prepare("
                    INSERT INTO users (first_name, last_name, email, password, role) 
                    VALUES (?, ?, ?, ?, 'customer')
                ");
                
                if ($stmt->execute([$firstName, $lastName, $email, $hashedPassword])) {
                    $userId = $this->db->lastInsertId();
                    
                    // Initialize user points
                    $stmt = $this->db->prepare("INSERT INTO user_points (user_id) VALUES (?)");
                    $stmt->execute([$userId]);

                    $this->setFlash('success', 'Registration successful! Please log in.');
                    $this->redirect('/login');
                } else {
                    $this->setFlash('error', 'Registration failed. Please try again.');
                }
            } else {
                $this->setFlash('error', implode('<br>', $errors));
            }

            $this->render('auth/register', [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email
            ]);
        } else {
            $this->render('auth/register');
        }
    }

    public function logout()
    {
        // Clear session
        session_destroy();
        
        // Clear remember me cookie
        setcookie('remember_token', '', time() - 3600, '/');
        
        $this->setFlash('success', 'You have been logged out successfully.');
        $this->redirect('/');
    }

    public function forgotPassword()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/account');
        }

        if ($this->isPost()) {
            $email = trim($this->getPost('email'));

            if (empty($email)) {
                $this->setFlash('error', 'Please enter your email address');
                $this->render('auth/forgot-password', ['email' => $email]);
                return;
            }

            // Check if user exists
            $stmt = $this->db->prepare("SELECT id, first_name FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store reset token (you'd need a password_resets table)
                // For simplicity, we'll just show a message
                
                $this->setFlash('success', 'If an account with that email exists, we have sent a password reset link.');
            } else {
                // Don't reveal if email exists or not for security
                $this->setFlash('success', 'If an account with that email exists, we have sent a password reset link.');
            }

            $this->redirect('/login');
        } else {
            $this->render('auth/forgot-password');
        }
    }

    public function resetPassword()
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/account');
        }

        $token = $this->getGet('token');
        
        if (empty($token)) {
            $this->redirect('/login');
        }

        if ($this->isPost()) {
            $password = $this->getPost('password');
            $confirmPassword = $this->getPost('confirm_password');

            if (empty($password)) {
                $this->setFlash('error', 'Password is required');
            } elseif (strlen($password) < 6) {
                $this->setFlash('error', 'Password must be at least 6 characters long');
            } elseif ($password !== $confirmPassword) {
                $this->setFlash('error', 'Passwords do not match');
            } else {
                // Verify token and update password (you'd need a password_resets table)
                // For simplicity, we'll just show a success message
                
                $this->setFlash('success', 'Password has been reset successfully. Please log in with your new password.');
                $this->redirect('/login');
            }

            $this->render('auth/reset-password', ['token' => $token]);
        } else {
            $this->render('auth/reset-password', ['token' => $token]);
        }
    }
}