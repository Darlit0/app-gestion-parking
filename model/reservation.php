<?php

class Reservation {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
      public function findByUserId($userId, $limit = 20) {
        $stmt = $this->pdo->prepare('
            SELECT r.*, v.brand_car, v.model_car, v.plate,
                   ptc.type_name as place_type_name
            FROM reservations r 
            LEFT JOIN vehicles v ON r.vehicle_id = v.id 
            LEFT JOIN place_types_config ptc ON r.place_type_id = ptc.id
            WHERE r.user_id = ? 
            ORDER BY r.date_reservation DESC, r.heure_arrivee DESC
            LIMIT ?
        ');
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $stmt = $this->pdo->prepare("
            INSERT INTO reservations (user_id, vehicle_id, place_type_id, place_id, date_reservation, heure_arrivee, heure_depart, prix) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['user_id'],
            $data['vehicle_id'] ?: null,
            $data['place_type_id'],
            $data['place_id'],
            $data['date_reservation'],
            $data['heure_arrivee'],
            $data['heure_depart'],
            $data['prix']
        ]);
    }
      public function getAll() {
        $stmt = $this->pdo->query("
            SELECT r.*, u.name, u.family_name, ptc.type_name 
            FROM reservations r 
            LEFT JOIN users u ON r.user_id = u.id 
            LEFT JOIN place_types_config ptc ON r.place_type_id = ptc.id
            ORDER BY r.date_reservation DESC, r.heure_arrivee DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllWithPagination($limit = 10, $offset = 0) {
        
        $countStmt = $this->pdo->query("SELECT COUNT(*) FROM reservations");
        $total = $countStmt->fetchColumn();
          
        $stmt = $this->pdo->prepare("
            SELECT r.*, 
                   u.name as user_name, 
                   u.family_name as user_family_name,
                   u.email as user_email,
                   u.phone as user_phone,
                   ptc.type_name as place_type_name,
                   v.brand_car, 
                   v.model_car, 
                   v.plate,
                   v.type as vehicle_type
            FROM reservations r 
            LEFT JOIN users u ON r.user_id = u.id 
            LEFT JOIN place_types_config ptc ON r.place_type_id = ptc.id
            LEFT JOIN vehicles v ON r.vehicle_id = v.id
            ORDER BY r.date_reservation ASC, r.heure_arrivee ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'reservations' => $reservations,
            'total' => $total
        ];
    }
    
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reservations WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function findConflictingReservations($placeId, $date, $heureArrivee, $heureDepart) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM reservations 
            WHERE place_id = ? 
            AND date_reservation = ? 
            AND (
                (heure_arrivee < ? AND heure_depart > ?) OR
                (heure_arrivee < ? AND heure_depart > ?) OR
                (heure_arrivee >= ? AND heure_depart <= ?)
            )
        ");
        $stmt->execute([
            $placeId, $date,
            $heureDepart, $heureArrivee,
            $heureArrivee, $heureDepart,
            $heureArrivee, $heureDepart
        ]);
        return $stmt->fetchColumn();
    }
}
