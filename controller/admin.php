<?php

require_once __DIR__ . '/../model/user.php';
require_once __DIR__ . '/../model/place.php';
require_once __DIR__ . '/../model/reservation.php';

class AdminController {
    private $userModel;
    private $placeModel;
    private $reservationModel;
    
    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->placeModel = new Place($pdo);
        $this->reservationModel = new Reservation($pdo);
    }    public function show() {
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: ../index.php?page=login');
            exit;
        }
        
        if (!$this->userModel->isAdmin($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Accès non autorisé';
            header('Location: ../index.php');
            exit;
        }

        $usersPerPage = 15;
        $currentUserPage = isset($_GET['page_num']) ? max(1, intval($_GET['page_num'])) : 1;
        $userOffset = ($currentUserPage - 1) * $usersPerPage;

        $places_config = $this->placeModel->getPlaceTypes();
        $places_disponibles = $this->placeModel->getPlaceTypeStats();

        $users = $this->userModel->getAllWithPagination($usersPerPage, $userOffset);
        $totalUsers = $this->userModel->getTotalCount();
        $totalUserPages = ceil($totalUsers / $usersPerPage);

        $pagination = [
            'current_page' => $currentUserPage,
            'total_pages' => $totalUserPages,
            'total_users' => $totalUsers,
            'users_per_page' => $usersPerPage,
            'has_previous' => $currentUserPage > 1,
            'has_next' => $currentUserPage < $totalUserPages,
            'previous_page' => $currentUserPage - 1,
            'next_page' => $currentUserPage + 1
        ];

        $page = isset($_GET['reservations_page']) ? (int)$_GET['reservations_page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $reservations_data = $this->reservationModel->getAllWithPagination($limit, $offset);
        $reservations = $reservations_data['reservations'];
        $total_reservations = $reservations_data['total'];
        $total_pages = ceil($total_reservations / $limit);

        $pagination_reservations = [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_reservations' => $total_reservations,
            'has_prev' => $page > 1,
            'has_next' => $page < $total_pages,
            'prev_page' => $page - 1,
            'next_page' => $page + 1
        ];

        include __DIR__ . '/../view/admin.php';
    }
      public function updateUser() {
        if (!$this->checkAdminAccess()) return;
        
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $family_name = $_POST['family_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $email = $_POST['email'] ?? '';
        $admin = isset($_POST['admin']) ? 1 : 0;
        $redirect = $_POST['redirect'] ?? 'admin';
        $page_num = $_POST['page_num'] ?? 1;

        if (empty($id) || empty($name) || empty($family_name) || empty($email)) {
            $_SESSION['error'] = 'Tous les champs obligatoires doivent être remplis';
            $redirectUrl = "../index.php?page=$redirect";
            if ($page_num > 1) $redirectUrl .= "&page_num=$page_num";
            header("Location: $redirectUrl");
            exit;
        }

        if ($id == $_SESSION['user_id']) {
            
            $currentUser = $this->userModel->findById($id);
            $admin = $currentUser['admin'];
        }

        if ($admin == 0 && $id != $_SESSION['user_id']) {
            $stmt = $this->userModel->getPdo()->prepare("SELECT COUNT(*) as count FROM users WHERE admin = 1 AND id != ?");
            $stmt->execute([$id]);
            $adminCount = $stmt->fetch()['count'];
            
            if ($adminCount == 0) {
                $_SESSION['error'] = 'Impossible de retirer le statut administrateur : il doit y avoir au moins un administrateur dans le système';
                $redirectUrl = "../index.php?page=$redirect";
                if ($page_num > 1) $redirectUrl .= "&page_num=$page_num";
                header("Location: $redirectUrl");
                exit;
            }
        }
        
        try {
            $data = [
                'name' => $name,
                'family_name' => $family_name,
                'email' => $email,
                'phone' => $phone ?: null,
                'admin' => $admin
            ];
            
            $this->userModel->update($id, $data);
            
            if ($admin == 1 && $id != $_SESSION['user_id']) {
                $_SESSION['success'] = 'Utilisateur modifié avec succès. Privilèges administrateur accordés.';
            } elseif ($admin == 0 && $id != $_SESSION['user_id']) {
                $_SESSION['success'] = 'Utilisateur modifié avec succès. Privilèges administrateur retirés.';
            } else {
                $_SESSION['success'] = 'Utilisateur modifié avec succès';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la modification : ' . $e->getMessage();
        }
        
        $redirectUrl = "../index.php?page=$redirect";
        if ($page_num > 1) $redirectUrl .= "&page_num=$page_num";
        header("Location: $redirectUrl");
        exit;
    }
    
    public function deleteUser() {
        if (!$this->checkAdminAccess()) return;
        
        $id = $_POST['id'] ?? '';
        $redirect = $_POST['redirect'] ?? 'admin';
        
        if (empty($id)) {
            $_SESSION['error'] = 'ID utilisateur manquant';
            header("Location: ../index.php?page=$redirect");
            exit;
        }

        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous ne pouvez pas supprimer votre propre compte';
            header("Location: ../index.php?page=$redirect");
            exit;
        }
        
        try {
            $this->userModel->delete($id);
            $_SESSION['success'] = 'Utilisateur supprimé avec succès';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la suppression : ' . $e->getMessage();
        }
        
        header("Location: ../index.php?page=$redirect");
        exit;
    }
    
    public function updatePlaces() {
        if (!$this->checkAdminAccess()) return;
        
        $place_type_id = $_POST['place_type_id'] ?? '';
        $total_places = $_POST['total_places'] ?? '';
        $prix_heure = $_POST['prix_heure'] ?? '';
        $redirect = $_POST['redirect'] ?? 'admin';

        if (empty($place_type_id) || empty($total_places) || empty($prix_heure)) {
            $_SESSION['error'] = 'Tous les champs sont obligatoires';
            header("Location: ../index.php?page=$redirect");
            exit;
        }
        
        if (!is_numeric($total_places) || intval($total_places) < 0) {
            $_SESSION['error'] = 'Nombre de places invalide';
            header("Location: ../index.php?page=$redirect");
            exit;
        }
        
        if (!is_numeric($prix_heure) || floatval($prix_heure) < 0) {
            $_SESSION['error'] = 'Prix invalide';
            header("Location: ../index.php?page=$redirect");
            exit;
        }
        
        try {
            $data = [
                'total_places' => intval($total_places),
                'prix_heure' => floatval($prix_heure)
            ];
            
            $this->placeModel->updatePlaceTypeConfig($place_type_id, $data);
            $_SESSION['success'] = 'Configuration des places mise à jour avec succès';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la mise à jour : ' . $e->getMessage();
        }
        
        header("Location: ../index.php?page=$redirect");
        exit;
    }
    
    private function checkAdminAccess() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté';
            header('Location: ../index.php?page=login');
            exit;
        }
        
        if (!$this->userModel->isAdmin($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Accès non autorisé';
            header('Location: ../index.php');
            exit;
        }
        
        return true;
    }
}
