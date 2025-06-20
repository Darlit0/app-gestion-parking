<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/vehicle.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new VehicleController($pdo);
    $controller->update();
} else {
    $_SESSION['error'] = 'Requête invalide';
    header("Location: ../index.php");
    exit;
}
