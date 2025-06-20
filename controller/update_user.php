<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/AdminController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AdminController($pdo);
    $controller->updateUser();
} else {
    $_SESSION['error'] = 'Requête invalide';
    header("Location: ../index.php?page=admin");
    exit;
}
