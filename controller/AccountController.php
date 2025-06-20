<?php

require_once __DIR__ . '/../model/User.php';

class AccountController {
    private $userModel;
    
    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }
    
    public function show() {
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page';
            header('Location: ../index.php?page=login');
            exit;
        }
        
        $userId = $_SESSION['user_id'];

        $user = $this->userModel->findById($userId);

        require_once __DIR__ . '/../model/Reservation.php';
        $reservationModel = new Reservation($this->userModel->getPdo());
        $reservations = $reservationModel->findByUserId($userId);

        require_once __DIR__ . '/../model/Vehicle.php';
        $vehicleModel = new Vehicle($this->userModel->getPdo());
        $vehicles = $vehicleModel->findByUserId($userId);

        $place_types = [1=>'Voiture', 2=>'Moto', 3=>'Handicap', 4=>'Electrique'];

        include __DIR__ . '/../view/account.php';
    }
    
    public function updateInfo() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Non autorisé';
            header('Location: ../index.php?page=login');
            exit;
        }
        
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $redirect = $_POST['redirect'] ?? 'account';

        if (empty($first_name) || empty($last_name) || empty($email)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs obligatoires';
            header("Location: ../index.php?page=$redirect");
            exit;
        }

        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['id'] != $_SESSION['user_id']) {
            $_SESSION['error'] = 'Cet email est déjà utilisé par un autre compte';
            header("Location: ../index.php?page=$redirect");
            exit;
        }
        
        try {
            $data = [
                'name' => $first_name,
                'family_name' => $last_name,
                'email' => $email,
                'phone' => $phone ?: null
            ];
            
            $this->userModel->update($_SESSION['user_id'], $data);
            $_SESSION['success'] = 'Vos informations ont été mises à jour avec succès';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la mise à jour : ' . $e->getMessage();
        }
        
        header("Location: ../index.php?page=$redirect");
        exit;
    }
    
    public function changePassword() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Non autorisé';
            header('Location: ../index.php?page=login');
            exit;
        }
        
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $redirect = $_POST['redirect'] ?? 'account';

        if (empty($old_password) || empty($new_password)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs';
            header("Location: ../index.php?page=$redirect");
            exit;
        }
        
        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'Le nouveau mot de passe doit contenir au moins 6 caractères';
            header("Location: ../index.php?page=$redirect");
            exit;
        }
        
        try {
            
            $user = $this->userModel->findById($_SESSION['user_id']);
            
            if (!$user || !password_verify($old_password, $user['password'])) {
                $_SESSION['error'] = 'Ancien mot de passe incorrect';
                header("Location: ../index.php?page=$redirect");
                exit;
            }

            $this->userModel->updatePassword($_SESSION['user_id'], $new_password);
            $_SESSION['success'] = 'Mot de passe modifié avec succès';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors du changement de mot de passe : ' . $e->getMessage();
        }
        
        header("Location: ../index.php?page=$redirect");
        exit;
    }
}
