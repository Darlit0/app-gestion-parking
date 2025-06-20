<?php

class Vehicle {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function findByUserId($userId) {
        $stmt = $this->pdo->prepare('SELECT * FROM vehicles WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM vehicles WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
      public function create($data) {
        
        if ($this->plateExists($data['plate'])) {
            throw new Exception("Cette plaque d'immatriculation est déjà enregistrée dans le système.");
        }
        
        $stmt = $this->pdo->prepare("
            INSERT INTO vehicles (user_id, type, brand_car, model_car, plate) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['user_id'],
            $data['type'],
            $data['brand_car'],
            $data['model_car'],
            $data['plate']
        ]);
    }
    
    public function update($id, $data) {
        
        if ($this->plateExists($data['plate'], $id)) {
            throw new Exception("Cette plaque d'immatriculation est déjà enregistrée dans le système.");
        }
        
        $stmt = $this->pdo->prepare("
            UPDATE vehicles 
            SET type = ?, brand_car = ?, model_car = ?, plate = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['type'],
            $data['brand_car'],
            $data['model_car'],
            $data['plate'],
            $id
        ]);
    }public function delete($id) {
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM reservations WHERE vehicle_id = ? AND status = 'active'");
        $stmt->execute([$id]);
        $activeReservationCount = $stmt->fetchColumn();
        
        if ($activeReservationCount > 0) {
            throw new Exception("Impossible de supprimer ce véhicule car il a $activeReservationCount réservation(s) active(s) associée(s). Veuillez d'abord annuler les réservations actives.");
        }

        $stmt = $this->pdo->prepare("DELETE FROM reservations WHERE vehicle_id = ? AND status IN ('cancelled', 'completed')");
        $stmt->execute([$id]);

        $stmt = $this->pdo->prepare("DELETE FROM vehicles WHERE id = ?");
        return $stmt->execute([$id]);
    }
      public function belongsToUser($vehicleId, $userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE id = ? AND user_id = ?");
        $stmt->execute([$vehicleId, $userId]);
        return $stmt->fetchColumn() > 0;
    }

    public function plateExists($plate, $excludeId = null) {
        
        $cleanPlate = strtoupper(str_replace([' ', '-'], '', trim($plate)));
        
        if (empty($cleanPlate)) {
            return false;
        }
        
        $sql = "SELECT COUNT(*) FROM vehicles WHERE UPPER(REPLACE(REPLACE(plate, ' ', ''), '-', '')) = ?";
        $params = [$cleanPlate];

        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function findByPlate($plate) {
        $cleanPlate = strtoupper(str_replace([' ', '-'], '', trim($plate)));
        
        $stmt = $this->pdo->prepare("
            SELECT v.*, u.name, u.family_name, u.email 
            FROM vehicles v 
            JOIN users u ON v.user_id = u.id 
            WHERE UPPER(REPLACE(REPLACE(v.plate, ' ', ''), '-', '')) = ?
        ");
        $stmt->execute([$cleanPlate]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
