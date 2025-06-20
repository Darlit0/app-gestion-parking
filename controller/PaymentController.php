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

        if (!isset($_SESSION['reservation_data'])) {
            $_SESSION['error'] = 'Aucune réservation en cours';
            header('Location: index.php?page=parking');
            exit;
        }

        $reservation_data = $_SESSION['reservation_data'];

        include __DIR__ . '/../view/payement.php';
    }
}
