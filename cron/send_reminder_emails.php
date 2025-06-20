<?php

require_once __DIR__ . '/../config/database.php';

$smtp_host = 'smtp.gmail.com'; 
$smtp_port = 587;
$smtp_username = 'votre-email@gmail.com';
$smtp_password = 'votre-mot-de-passe-app';
$from_email = 'noreply@gestion-parking.com';
$from_name = 'Gestion Parking';

function sendReminderEmail($to_email, $user_name, $reservation_data) {
    global $from_email, $from_name;
    
    $subject = "Rappel : Votre réservation de parking dans 2h";
    
    $message = "
    <html>
    <head>
        <title>Rappel de réservation</title>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #0ea5e9; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f8f9fa; }
            .details { background: white; padding: 15px; border-radius: 8px; margin: 15px 0; }
            .highlight { color: #0ea5e9; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🅿️ Rappel de Réservation</h1>
            </div>
            <div class='content'>
                <p>Bonjour <strong>{$user_name}</strong>,</p>
                
                <p>Votre réservation de parking commence dans <span class='highlight'>2 heures</span> !</p>
                
                <div class='details'>
                    <h3>📋 Détails de votre réservation :</h3>
                    <ul>
                        <li><strong>Date :</strong> {$reservation_data['date_reservation']}</li>
                        <li><strong>Heure d'arrivée :</strong> {$reservation_data['heure_arrivee']}</li>
                        <li><strong>Heure de départ :</strong> {$reservation_data['heure_depart']}</li>
                        <li><strong>Type de place :</strong> {$reservation_data['type_place']}</li>
                        <li><strong>Prix :</strong> {$reservation_data['prix']} €</li>
                        " . ($reservation_data['vehicle_plate'] ? "<li><strong>Véhicule :</strong> {$reservation_data['vehicle_info']} ({$reservation_data['vehicle_plate']})</li>" : "") . "
                    </ul>
                </div>
                
                <p>💡 <strong>Conseil :</strong> Prévoyez quelques minutes d'avance pour trouver votre place !</p>
                
                <p>Bonne journée,<br>
                L'équipe Gestion Parking</p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = array(
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        "From: {$from_name} <{$from_email}>",
        'Reply-To: ' . $from_email,
        'X-Mailer: PHP/' . phpversion()
    );
    
    return mail($to_email, $subject, $message, implode("\r\n", $headers));
}

try {
    
    $now = new DateTime();
    $reminder_time_start = clone $now;
    $reminder_time_start->add(new DateInterval('PT1H45M')); 
    
    $reminder_time_end = clone $now;
    $reminder_time_end->add(new DateInterval('PT2H15M')); 
    
    $today = $now->format('Y-m-d');
    $time_start = $reminder_time_start->format('H:i:s');
    $time_end = $reminder_time_end->format('H:i:s');
    
    echo "[" . $now->format('Y-m-d H:i:s') . "] Vérification des réservations entre {$time_start} et {$time_end}\n";

    $sql = "
        SELECT 
            r.id,
            r.date_reservation,
            r.heure_arrivee,
            r.heure_depart,
            r.prix,
            u.name,
            u.family_name,
            u.email,
            ptc.type_name as type_place,
            v.type as vehicle_type,
            v.brand_car,
            v.model_car,
            v.plate as vehicle_plate
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN place_types_config ptc ON r.place_type_id = ptc.id
        LEFT JOIN vehicles v ON r.vehicle_id = v.id
        WHERE r.date_reservation = :today
        AND r.heure_arrivee BETWEEN :time_start AND :time_end
        AND r.email_sent IS NULL OR r.email_sent = 0
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'today' => $today,
        'time_start' => $time_start,
        'time_end' => $time_end
    ]);
    
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Trouvé " . count($reservations) . " réservation(s) à traiter\n";
    
    foreach ($reservations as $reservation) {
        $user_name = $reservation['name'] . ' ' . $reservation['family_name'];

        $reservation_data = [
            'date_reservation' => date('d/m/Y', strtotime($reservation['date_reservation'])),
            'heure_arrivee' => date('H:i', strtotime($reservation['heure_arrivee'])),
            'heure_depart' => date('H:i', strtotime($reservation['heure_depart'])),
            'type_place' => $reservation['type_place'],
            'prix' => number_format($reservation['prix'], 2),
            'vehicle_plate' => $reservation['vehicle_plate'],
            'vehicle_info' => $reservation['brand_car'] ? $reservation['brand_car'] . ' ' . $reservation['model_car'] : $reservation['vehicle_type']
        ];

        if (sendReminderEmail($reservation['email'], $user_name, $reservation_data)) {
            
            $update_sql = "UPDATE reservations SET email_sent = 1 WHERE id = :id";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute(['id' => $reservation['id']]);
            
            echo "✅ Email envoyé à {$reservation['email']} pour la réservation ID {$reservation['id']}\n";
        } else {
            echo "❌ Erreur envoi email à {$reservation['email']} pour la réservation ID {$reservation['id']}\n";
        }
    }
    
    echo "Script terminé avec succès\n";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
    error_log("Erreur script reminder emails: " . $e->getMessage());
}
?>
