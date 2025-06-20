<?php

require_once __DIR__ . '/../model/Place.php';
require_once __DIR__ . '/../model/Vehicle.php';

class ParkingController {
    private $placeModel;
    private $vehicleModel;
    
    public function __construct($pdo) {
        $this->placeModel = new Place($pdo);
        $this->vehicleModel = new Vehicle($pdo);
    }
    
    public function show() {
        
        $user_vehicles = [];
        $user_id = $_SESSION['user_id'] ?? null;
        
        if ($user_id) {
            $user_vehicles = $this->vehicleModel->findByUserId($user_id);
        }

        $places_disponibles = $this->placeModel->getPlaceTypeStats();

        include __DIR__ . '/../view/parking.php';
    }
}
