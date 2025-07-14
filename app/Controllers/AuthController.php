<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (!$this->isGuest()) {
            $this->redirect('/account');
        }
        
        $this->view('auth/login', [
            'page_title' => 'Login'
        ]);
    }
    
    public function login(): void
    {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Invalid request');
            $this->redirect('/login');
        }
        
        $email = $this->input('email');
        $password = $this->input('password');
        $remember = $this->input('remember');
        
        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if (!empty($errors)) {
            $this->flash('error', 'Please fix the errors below');
            $this->redirect('/login');
        }
        
        $user = User::findByEmail($email);
        
        if (!$user || !$user->verifyPassword($password)) {
            $this->flash('error', 'Invalid email or password');
            $this->redirect('/login');
        }
        
        if (!$user->is_active) {
            $this->flash('error', 'Your account has been deactivated');
            $this->redirect('/login');
        }
        
        // Login successful
        $_SESSION['user'] = $user->toArray();
        
        $this->flash('success', 'Welcome back, ' . $user->getFullName());
        $this->redirect('/account');
    }
    
    public function showRegister(): void
    {
        if (!$this->isGuest()) {
            $this->redirect('/account');
        }
        
        $this->view('auth/register', [
            'page_title' => 'Register'
        ]);
    }
    
    public function register(): void
    {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Invalid request');
            $this->redirect('/register');
        }
        
        $data = [
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
            'email' => $this->input('email'),
            'password' => $this->input('password'),
            'password_confirm' => $this->input('password_confirm')
        ];
        
        $errors = $this->validate([
            'first_name' => 'required|min:2',
            'last_name' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirm' => 'required'
        ]);
        
        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'][] = 'Passwords do not match';
        }
        
        // Check if email already exists
        if (User::findByEmail($data['email'])) {
            $errors['email'][] = 'Email already exists';
        }
        
        if (!empty($errors)) {
            $this->flash('error', 'Please fix the errors below');
            $this->redirect('/register');
        }
        
        // Create user
        $user = new User();
        $user->fill($data);
        $user->hashPassword($data['password']);
        $user->role = 'customer';
        $user->save();
        
        // Generate email verification token
        $user->generateEmailVerificationToken();
        
        // Login user
        $_SESSION['user'] = $user->toArray();
        
        $this->flash('success', 'Registration successful! Welcome to LightCommerce');
        $this->redirect('/account');
    }
    
    public function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
        $this->redirect('/');
    }
    
    public function showForgotPassword(): void
    {
        if (!$this->isGuest()) {
            $this->redirect('/account');
        }
        
        $this->view('auth/forgot-password', [
            'page_title' => 'Forgot Password'
        ]);
    }
    
    public function forgotPassword(): void
    {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Invalid request');
            $this->redirect('/forgot-password');
        }
        
        $email = $this->input('email');
        
        $errors = $this->validate([
            'email' => 'required|email'
        ]);
        
        if (!empty($errors)) {
            $this->flash('error', 'Please enter a valid email address');
            $this->redirect('/forgot-password');
        }
        
        $user = User::findByEmail($email);
        
        if ($user) {
            $token = $user->generatePasswordResetToken();
            // Here you would send an email with the reset link
            // For now, we'll just store it in session for demo
            $_SESSION['reset_token'] = $token;
        }
        
        // Always show success message for security
        $this->flash('success', 'If an account with that email exists, we\'ve sent a password reset link');
        $this->redirect('/forgot-password');
    }
    
    public function showResetPassword(string $token): void
    {
        if (!$this->isGuest()) {
            $this->redirect('/account');
        }
        
        // Find user by reset token
        $user = User::where('password_reset_token', '=', $token);
        
        if (empty($user) || strtotime($user[0]->password_reset_expires) < time()) {
            $this->flash('error', 'Invalid or expired reset token');
            $this->redirect('/forgot-password');
        }
        
        $this->view('auth/reset-password', [
            'token' => $token,
            'page_title' => 'Reset Password'
        ]);
    }
    
    public function resetPassword(): void
    {
        if (!$this->verifyCsrfToken()) {
            $this->flash('error', 'Invalid request');
            $this->redirect('/forgot-password');
        }
        
        $token = $this->input('token');
        $password = $this->input('password');
        $passwordConfirm = $this->input('password_confirm');
        
        $errors = $this->validate([
            'password' => 'required|min:8',
            'password_confirm' => 'required'
        ]);
        
        if ($password !== $passwordConfirm) {
            $errors['password_confirm'][] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            $this->flash('error', 'Please fix the errors below');
            $this->redirect("/reset-password/{$token}");
        }
        
        // Find user by reset token
        $users = User::where('password_reset_token', '=', $token);
        
        if (empty($users) || strtotime($users[0]->password_reset_expires) < time()) {
            $this->flash('error', 'Invalid or expired reset token');
            $this->redirect('/forgot-password');
        }
        
        $user = $users[0];
        $user->hashPassword($password);
        $user->password_reset_token = null;
        $user->password_reset_expires = null;
        $user->save();
        
        $this->flash('success', 'Password reset successful. You can now login with your new password');
        $this->redirect('/login');
    }
}