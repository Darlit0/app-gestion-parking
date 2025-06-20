<?php

require_once __DIR__ . '/../model/Reservation.php';
require_once __DIR__ . '/../model/Place.php';
require_once __DIR__ . '/../model/Vehicle.php';

class ReservationController {
    private $reservationModel;
    private $placeModel;
    private $vehicleModel;
    
    public function __construct($pdo) {
        $this->reservationModel = new Reservation($pdo);
        $this->placeModel = new Place($pdo);
        $this->vehicleModel = new Vehicle($pdo);
    }
    
    public function store() {
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour effectuer une réservation';
            header('Location: ../index.php?page=login');
            exit;
        }
        
        $user_id = $_SESSION['user_id'];
        $vehicle_id = $_POST['vehicle_id'] ?? null;
        $place_type_id = $_POST['place_type_id'] ?? '';
        $date_reservation = $_POST['date_reservation'] ?? '';
        $heure_arrivee = $_POST['heure_arrivee'] ?? '';
        $heure_depart = $_POST['heure_depart'] ?? '';
        $prix = $_POST['prix'] ?? '';

        if (empty($place_type_id) || empty($date_reservation) || empty($heure_arrivee) || empty($heure_depart) || empty($prix)) {
            $_SESSION['error'] = 'Tous les champs sont obligatoires';
            header('Location: ../index.php?page=parking');
            exit;
        }

        if ($vehicle_id && !$this->vehicleModel->belongsToUser($vehicle_id, $user_id)) {
            $_SESSION['error'] = 'Véhicule non autorisé';
            header('Location: ../index.php?page=parking');
            exit;
        }

        if (strtotime($date_reservation) < strtotime(date('Y-m-d'))) {
            $_SESSION['error'] = 'La date de réservation ne peut pas être dans le passé';
            header('Location: ../index.php?page=parking');
            exit;
        }

        if (strtotime($heure_arrivee) >= strtotime($heure_depart)) {
            $_SESSION['error'] = 'L\'heure d\'arrivée doit être antérieure à l\'heure de départ';
            header('Location: ../index.php?page=parking');
            exit;
        }

        if (!is_numeric($prix) || floatval($prix) <= 0) {
            $_SESSION['error'] = 'Prix invalide';
            header('Location: ../index.php?page=parking');
            exit;
        }
        
        try {
            
            $place_id = $this->placeModel->findAvailablePlace($place_type_id, $date_reservation, $heure_arrivee, $heure_depart);
            
            if (!$place_id) {
                $_SESSION['error'] = 'Aucune place disponible pour ce créneau';
                header('Location: ../index.php?page=parking');
                exit;
            }

            $reservation_data = [
                'user_id' => $user_id,
                'vehicle_id' => $vehicle_id ?: null,
                'place_type_id' => $place_type_id,
                'place_id' => $place_id,
                'date_reservation' => $date_reservation,
                'heure_arrivee' => $heure_arrivee,
                'heure_depart' => $heure_depart,
                'prix' => $prix
            ];

            $_SESSION['reservation_data'] = $reservation_data;

            header('Location: ../index.php?page=payement');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la création de la réservation : ' . $e->getMessage();
            header('Location: ../index.php?page=parking');
            exit;
        }
    }
    
    public function confirmPayment() {
        
        if (!isset($_SESSION['reservation_data'])) {
            $_SESSION['error'] = 'Aucune réservation en cours';
            header('Location: ../index.php?page=parking');
            exit;
        }
        
        try {
            
            $reservation_data = $_SESSION['reservation_data'];
            $this->reservationModel->create($reservation_data);

            unset($_SESSION['reservation_data']);
            
            $_SESSION['success'] = 'Votre réservation a été confirmée avec succès !';
            header('Location: ../index.php?page=account');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la confirmation de la réservation : ' . $e->getMessage();
            header('Location: ../index.php?page=parking');
            exit;
        }
    }
}
