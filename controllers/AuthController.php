<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';

class AuthController {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function showLoginForm() {
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLoginForm();
            return;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = "Veuillez remplir tous les champs";
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }
        
        $user = $this->userModel->login($email, $password);
        
        if ($user) {
            // Créer la session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            // Rediriger selon le rôle
            if ($user['role'] === 'admin') {
                header('Location: index.php?action=admin-dashboard');
                exit;  
            } else {
                header('Location: index.php?action=student-dashboard');
                exit;  
            }
            
        } else {
            $error = "Email ou mot de passe incorrect";
            require_once __DIR__ . '/../views/auth/login.php';
        }
    }
    
    public function showRegisterForm() {
        require_once __DIR__ . '/../views/auth/register.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegisterForm();
            return;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($email) || empty($password)) {
            $errors[] = "Tous les champs sont obligatoires";
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email invalide";
        }
        
        if (strlen($password) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = "Les mots de passe ne correspondent pas";
        }
        
        if (!empty($errors)) {
            require_once __DIR__ . '/../views/auth/register.php';
            return;
        }
        
        $result = $this->userModel->register($email, $password, 'client');
        
        if ($result) {
            $user = $this->userModel->findByEmail($email);
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
            header('Location: index.php?action=complete-profile');
            exit;  
        } else {
            $errors[] = "Erreur lors de l'inscription (email déjà utilisé ?)";
            require_once __DIR__ . '/../views/auth/register.php';
        }
    }
    
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
        exit; 
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user']);
    }
    
    public static function isAdmin() {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }
}

?>