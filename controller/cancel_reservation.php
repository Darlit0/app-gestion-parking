<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Vous devez être connecté pour effectuer cette action';
    header("Location: ../index.php?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Requête invalide';
    header("Location: ../index.php?page=account");
    exit;
}

require_once __DIR__ . '/../config/database.php';

$reservation_id = $_POST['reservation_id'] ?? '';
$user_id = $_SESSION['user_id'];

if (empty($reservation_id)) {
    $_SESSION['error'] = 'ID de réservation manquant';
    header("Location: ../index.php?page=account");
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT id, date_reservation, heure_arrivee, status 
        FROM reservations 
        WHERE id = ? AND user_id = ? AND status = 'active'
    ");
    $stmt->execute([$reservation_id, $user_id]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        $_SESSION['error'] = 'Réservation non trouvée ou déjà annulée';
        $pdo->rollBack();
        header("Location: ../index.php?page=account");
        exit;
    }

    $now = new DateTime();
    $reservation_datetime = new DateTime($reservation['date_reservation'] . ' ' . $reservation['heure_arrivee']);
    
    if ($reservation_datetime <= $now) {
        $_SESSION['error'] = 'Impossible d\'annuler une réservation déjà commencée ou passée';
        $pdo->rollBack();
        header("Location: ../index.php?page=account");
        exit;
    }

    $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
    $stmt->execute([$reservation_id]);
    
    $pdo->commit();
    $_SESSION['success'] = 'Réservation annulée avec succès';
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Erreur lors de l\'annulation : ' . $e->getMessage();
}

header("Location: ../index.php?page=account");
exit;
?>
