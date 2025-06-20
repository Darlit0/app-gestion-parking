<?php

require_once __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Vous devez être connecté pour effectuer un paiement';
    header('Location: index.php?page=login');
    exit;
}

if (!isset($_SESSION['reservation_data'])) {
    $_SESSION['error'] = 'Aucune réservation en cours';
    header('Location: index.php?page=parking');
    exit;
}

$method = $_POST['method'] ?? '';
$reservation_data = $_SESSION['reservation_data'];

try {
    switch ($method) {
        case 'stripe':
            handleStripePayment($reservation_data);
            break;
            
        case 'paypal':
            
            break;
            
        default:
            throw new Exception('Méthode de paiement non supportée');
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Erreur de paiement : ' . $e->getMessage();
    header('Location: index.php?page=payement&canceled=1');
    exit;
}

function handleStripePayment($reservation_data) {
    
    $stripe_config = require __DIR__ . '/../config/stripe.php';

    if (!$stripe_config || !isset($stripe_config['secret_key'])) {
        throw new Exception('Configuration Stripe manquante');
    }

    require_once __DIR__ . '/../vendor/autoload.php';
    \Stripe\Stripe::setApiKey($stripe_config['secret_key']);
    
    try {
        
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $stripe_config['currency'],
                    'product_data' => [
                        'name' => 'Réservation parking',
                        'description' => sprintf(
                            'Réservation pour le %s de %s à %s',
                            $reservation_data['date_reservation'],
                            $reservation_data['heure_arrivee'],
                            $reservation_data['heure_depart']
                        ),
                    ],
                    'unit_amount' => intval(floatval($reservation_data['prix']) * 100), 
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $stripe_config['success_url'] . '&method=stripe',
            'cancel_url' => $stripe_config['cancel_url'] . '&method=stripe',
            'metadata' => [
                'user_id' => $_SESSION['user_id'],
                'place_type_id' => $reservation_data['place_type_id'],
                'date_reservation' => $reservation_data['date_reservation'],
                'heure_arrivee' => $reservation_data['heure_arrivee'],
                'heure_depart' => $reservation_data['heure_depart'],
            ],
        ]);

        header('Location: ' . $session->url);
        exit;
        
    } catch (\Stripe\Exception\CardException $e) {
        throw new Exception('Erreur de carte bancaire : ' . $e->getError()->message);
    } catch (\Stripe\Exception\RateLimitException $e) {
        throw new Exception('Trop de requêtes. Veuillez réessayer plus tard.');
    } catch (\Stripe\Exception\InvalidRequestException $e) {
        throw new Exception('Requête invalide : ' . $e->getMessage());
    } catch (\Stripe\Exception\AuthenticationException $e) {
        throw new Exception('Erreur d\'authentification Stripe');
    } catch (\Stripe\Exception\ApiConnectionException $e) {
        throw new Exception('Problème de connexion avec Stripe');
    } catch (\Stripe\Exception\ApiErrorException $e) {
        throw new Exception('Erreur Stripe : ' . $e->getMessage());
    } catch (Exception $e) {
        throw new Exception('Erreur inattendue : ' . $e->getMessage());
    }
}

function handleTestPayment($reservation_data) {
    
    $success_url = 'http:
    header('Location: ' . $success_url);
    exit;
}
?>
