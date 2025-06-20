<?php

require_once __DIR__ . '/../model/vehicle.php';

class VehicleController {
    private $vehicleModel;
    
    public function __construct($pdo) {
        $this->vehicleModel = new Vehicle($pdo);
    }
    
    public function add() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Utilisateur non connecté';
            $this->redirect();
            return;
        }
        
        $type = $_POST['type'] ?? '';
        $brand_car = $_POST['brand_car'] ?? '';
        $model_car = $_POST['model_car'] ?? '';
        $plate = $_POST['plate'] ?? '';

        if (empty($type)) {
            $_SESSION['error'] = 'Type de véhicule manquant';
            $this->redirect();
            return;
        }
        
        if (empty($plate)) {
            $_SESSION['error'] = 'Immatriculation manquante';
            $this->redirect();
            return;
        }

        $validTypes = ['Voiture', 'Voiture (handicap)', 'Voiture électrique', 'Moto'];
        if (!in_array($type, $validTypes)) {
            $_SESSION['error'] = 'Type de véhicule invalide';
            $this->redirect();
            return;
        }
        
        try {
            $data = [
                'user_id' => $_SESSION['user_id'],
                'type' => $type,
                'brand_car' => $brand_car,
                'model_car' => $model_car,
                'plate' => $plate
            ];
            
            $this->vehicleModel->create($data);
            $_SESSION['success'] = 'Véhicule ajouté avec succès';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de l\'ajout : ' . $e->getMessage();
        }
        
        $this->redirect();
    }
    
    public function update() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Utilisateur non connecté';
            $this->redirect();
            return;
        }
        
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $type = $_POST['type'] ?? '';
        $brand_car = $_POST['brand_car'] ?? '';
        $model_car = $_POST['model_car'] ?? '';
        $plate = $_POST['plate'] ?? '';

        if (!$id || empty($type) || empty($plate)) {
            $_SESSION['error'] = 'Données manquantes pour la modification';
            $this->redirect();
            return;
        }

        if (!$this->vehicleModel->belongsToUser($id, $_SESSION['user_id'])) {
            $_SESSION['error'] = 'Véhicule non trouvé ou non autorisé';
            $this->redirect();
            return;
        }

        $validTypes = ['Voiture', 'Voiture (handicap)', 'Voiture électrique', 'Moto'];
        if (!in_array($type, $validTypes)) {
            $_SESSION['error'] = 'Type de véhicule invalide';
            $this->redirect();
            return;
        }
        
        try {
            $data = [
                'type' => $type,
                'brand_car' => $brand_car,
                'model_car' => $model_car,
                'plate' => $plate
            ];
            
            $this->vehicleModel->update($id, $data);
            $_SESSION['success'] = 'Véhicule modifié avec succès';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la modification : ' . $e->getMessage();
        }
        
        $this->redirect();
    }
    
    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Utilisateur non connecté';
            $this->redirect();
            return;
        }
        
        $vehicle_id = $_POST['vehicle_id'] ?? '';
        
        if (empty($vehicle_id)) {
            $_SESSION['error'] = 'ID véhicule manquant';
            $this->redirect();
            return;
        }

        if (!$this->vehicleModel->belongsToUser($vehicle_id, $_SESSION['user_id'])) {
            $_SESSION['error'] = 'Véhicule non trouvé ou non autorisé';
            $this->redirect();
            return;
        }
        
        try {
            $this->vehicleModel->delete($vehicle_id);
            $_SESSION['success'] = 'Véhicule supprimé avec succès';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la suppression : ' . $e->getMessage();
        }
        
        $this->redirect();
    }
    
    private function redirect() {
        $redirect = $_POST['redirect'] ?? 'account';
        header("Location: ../index.php?page=$redirect");
        exit;
    }
}
