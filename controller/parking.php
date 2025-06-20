<?php

require_once __DIR__ . '/../model/place.php';
require_once __DIR__ . '/../model/vehicle.php';

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

        $place_types = $this->placeModel->getPlaceTypes();

        include __DIR__ . '/../view/parking.php';
    }
}
