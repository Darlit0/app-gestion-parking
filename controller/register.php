<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/auth.php';

$controller = new AuthController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->register();
} else {
    $controller->showRegister();
}