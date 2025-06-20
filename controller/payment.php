<?php

class PaymentController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function show() {
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page';
            header('Location: index.php?page=login');
            exit;
        }

        $success = isset($_GET['success']);
        $canceled = isset($_GET['canceled']);
        $paypal_order_id = $_GET['paypal_order_id'] ?? null;
        
        if ($success && isset($_SESSION['reservation_data'])) {
            $this->handlePaymentSuccess($paypal_order_id);
        }

        $reservation_data = $_SESSION['reservation_data'] ?? [];

        if (empty($reservation_data) && !$success && !$canceled) {
            $_SESSION['error'] = 'Aucune réservation en cours';
            header('Location: index.php?page=parking');
            exit;
        }

        $pdo = $this->pdo;

        include __DIR__ . '/../view/payement.php';
    }

    private function handlePaymentSuccess($paypal_order_id = null) {
        $reservation_data = $_SESSION['reservation_data'];
        $user_id = $_SESSION['user_id'];
        
        try {
            
            $stmt = $this->pdo->prepare("
                INSERT INTO reservations (user_id, vehicle_id, place_type_id, place_id, date_reservation, heure_arrivee, heure_depart, prix) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $vehicle_id = !empty($reservation_data['vehicle_id']) ? $reservation_data['vehicle_id'] : null;
            $place_id = $reservation_data['place_id'] ?? null;
            
            $stmt->execute([
                $user_id,
                $vehicle_id,
                $reservation_data['place_type_id'],
                $place_id,
                $reservation_data['date_reservation'],
                $reservation_data['heure_arrivee'],
                $reservation_data['heure_depart'],
                $reservation_data['prix']
            ]);

            $_SESSION['success_place_id'] = $place_id;

            $success_message = 'Paiement réussi ! Votre réservation a été enregistrée.';

            if ($paypal_order_id) {
                $success_message .= " (ID PayPal: $paypal_order_id)";
            }
            
            $_SESSION['success'] = $success_message;

            unset($_SESSION['reservation_data']);
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Erreur lors de l\'enregistrement de la réservation : ' . $e->getMessage();
        }
    }
}
