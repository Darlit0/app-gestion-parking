<?php

class Place {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAvailableByType($placeTypeId) {
        $stmt = $this->pdo->prepare("SELECT id FROM places WHERE place_id = ? ORDER BY id ASC");
        $stmt->execute([$placeTypeId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function findAvailablePlace($placeTypeId, $dateReservation, $heureArrivee, $heureDepart) {
        $allPlaces = $this->getAvailableByType($placeTypeId);
        
        if (empty($allPlaces)) {
            return null;
        }
        
        $reservationModel = new Reservation($this->pdo);
        
        foreach ($allPlaces as $placeId) {
            if ($reservationModel->findConflictingReservations($placeId, $dateReservation, $heureArrivee, $heureDepart) == 0) {
                return $placeId;
            }
        }
        
        return null;
    }
    
    public function getPlaceTypeStats() {
        $stmt = $this->pdo->query("
            SELECT 
              ptc.id,
              ptc.type_name,
              ptc.total_places,
              ptc.prix_heure,
              COALESCE(occupied.places_occupees, 0) as places_occupees,
              (ptc.total_places - COALESCE(occupied.places_occupees, 0)) as places_libres,
              1 as active
            FROM place_types_config ptc
            LEFT JOIN (
                SELECT 
                    r.place_type_id,
                    COUNT(*) as places_occupees
                FROM reservations r 
                WHERE r.date_reservation = CURDATE()
                AND CURTIME() BETWEEN r.heure_arrivee AND r.heure_depart
                GROUP BY r.place_type_id
            ) occupied ON ptc.id = occupied.place_type_id
            ORDER BY ptc.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPlaceTypes() {
        $stmt = $this->pdo->query("SELECT * FROM place_types_config ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function updatePlaceTypeConfig($id, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE place_types_config 
            SET total_places = ?, prix_heure = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['total_places'],
            $data['prix_heure'],
            $id
        ]);
    }
}
