<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ReservationController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ReservationController($pdo);
    $controller->store();
} else {
    $_SESSION['error'] = 'Méthode non autorisée';
    header('Location: ../index.php?page=parking');
    exit;
}
