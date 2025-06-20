<?php

require_once __DIR__ . '/../model/User.php';

class AuthController {
    private $userModel;
    
    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }
    
    public function showLogin() {
        
        if (isset($_SESSION['user_id'])) {
            header('Location: ../index.php');
            exit;
        }
        
        include __DIR__ . '/../view/login.php';
    }
    
    public function showRegister() {
        
        if (isset($_SESSION['user_id'])) {
            header('Location: ../index.php');
            exit;
        }
        
        include __DIR__ . '/../view/register.php';
    }
    
    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email et mot de passe requis';
            header('Location: ../index.php?page=login');
            exit;
        }
        
        try {
            $user = $this->userModel->findByEmail($email);
            
            if (!$user || !password_verify($password, $user['password'])) {
                $_SESSION['error'] = 'Email ou mot de passe incorrect';
                header('Location: ../index.php?page=login');
                exit;
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['is_admin'] = !empty($user['admin']);
            
            $_SESSION['success'] = 'Connexion réussie !';
            header('Location: ../index.php');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la connexion : ' . $e->getMessage();
            header('Location: ../index.php?page=login');
            exit;
        }
    }
    
    public function register() {
        $email = $_POST['email'] ?? '';
        $name = $_POST['name'] ?? '';
        $family_name = $_POST['family_name'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $phone = $_POST['phone'] ?? '';

        if (empty($email) || empty($name) || empty($family_name) || empty($password)) {
            $_SESSION['error'] = 'Tous les champs obligatoires doivent être remplis';
            header('Location: ../index.php?page=register');
            exit;
        }
        
        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Les mots de passe ne correspondent pas';
            header('Location: ../index.php?page=register');
            exit;
        }
        
        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères';
            header('Location: ../index.php?page=register');
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Format d\'email invalide';
            header('Location: ../index.php?page=register');
            exit;
        }
        
        try {
            
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser) {
                $_SESSION['error'] = 'Cet email est déjà utilisé';
                header('Location: ../index.php?page=register');
                exit;
            }

            $data = [
                'name' => $name,
                'family_name' => $family_name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'phone' => $phone ?: null,
                'admin' => ''
            ];
            
            $this->userModel->create($data);
            $_SESSION['success'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            header('Location: ../index.php?page=login');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de l\'inscription : ' . $e->getMessage();
            header('Location: ../index.php?page=register');
            exit;
        }
    }
    
    public function logout() {
        
        $_SESSION = array();

        if (session_id() != '' || isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        session_destroy();

        header('Location: ../index.php');
        exit;
    }
}
