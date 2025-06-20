<?php

$success = isset($_GET['success']);
$canceled = isset($_GET['canceled']);
$payment_method = $_GET['method'] ?? 'stripe';
$paypal_order_id = $_GET['paypal_order_id'] ?? null;

$place_types = [1 => 'Voiture', 2 => 'Moto', 3 => 'Handicap', 4 => 'Électrique'];

$reservation_data = $_SESSION['reservation_data'] ?? [];

$vehicle_info = null;
if (!empty($reservation_data['vehicle_id']) && isset($_SESSION['user_id'])) {
    
    if (isset($pdo)) {
        $stmt = $pdo->prepare('SELECT * FROM vehicles WHERE id = ? AND user_id = ?');
        $stmt->execute([$reservation_data['vehicle_id'], $_SESSION['user_id']]);
        $vehicle_info = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<div class="container my-5">
  <h2 class="text-center mb-4"><i class="fa-solid fa-credit-card"></i> Paiement de la réservation</h2>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger text-center">
      <i class="fa-solid fa-exclamation-triangle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
      <?php unset($_SESSION['error']); ?>
    </div>
  <?php endif; ?>
    <!-- Mode débogage (à supprimer en production) -->
  <?php if (isset($_GET['debug'])): ?>
  <div class="alert alert-info">
    <h6><i class="fa-solid fa-bug"></i> Mode débogage</h6>
    <p><strong>User ID:</strong> <?= $_SESSION['user_id'] ?? 'Non connecté' ?></p>
    <p><strong>Réservation:</strong> <?= isset($_SESSION['reservation_data']) ? 'Présente' : 'Absente' ?></p>
    <?php if (isset($_SESSION['reservation_data'])): ?>
    <details>
      <summary>Données de réservation</summary>
      <pre><?= htmlspecialchars(print_r($_SESSION['reservation_data'], true)) ?></pre>
    </details>
    <?php endif; ?>
    <p><a href="debug_payment.php" class="btn btn-sm btn-info">Outil de débogage avancé</a></p>
  </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success text-center">
      <i class="fa-solid fa-check-circle"></i> Paiement réussi ! Merci pour votre réservation.
      <?php if (isset($_SESSION['success_place_id']) && $_SESSION['success_place_id']): ?>
        <br><strong>Votre place attribuée : <span class="badge bg-primary">#<?= htmlspecialchars($_SESSION['success_place_id']) ?></span></strong>
        <?php unset($_SESSION['success_place_id']); ?>
      <?php endif; ?>
      <br><a href="index.php?page=account" class="btn btn-primary mt-2">Voir mes réservations</a>
    </div>
  <?php elseif ($canceled): ?>
    <div class="alert alert-danger text-center">
      <i class="fa-solid fa-times-circle"></i> Paiement annulé.
      <?php if ($payment_method === 'paypal'): ?>
        <br><small>Méthode: PayPal</small>
      <?php else: ?>
        <br><small>Méthode: Stripe</small>
      <?php endif; ?>
      <br><a href="index.php" class="btn btn-secondary mt-2">Retour à l'accueil</a>
    </div>
  <?php elseif (!empty($reservation_data)): ?>
    <!-- Résumé de la réservation -->
    <div class="card mb-4" style="background: #fff;">
      <div class="card-header">
        <h5><i class="fa-solid fa-file-invoice"></i> Résumé de votre réservation</h5>
      </div>
      <div class="card-body">
        <div class="row">          <div class="col-md-6">
            <p><strong>Type de place:</strong> <?= $place_types[$reservation_data['place_type_id']] ?? 'Inconnu' ?></p>
            <?php if (!empty($reservation_data['place_id'])): ?>
            <p><strong>Numéro de place:</strong> <span class="badge bg-primary fs-6">#<?= htmlspecialchars($reservation_data['place_id']) ?></span></p>
            <?php endif; ?>
            <p><strong>Date:</strong> <?= htmlspecialchars($reservation_data['date_reservation']) ?></p>
            <p><strong>Heure d'arrivée:</strong> <?= htmlspecialchars($reservation_data['heure_arrivee']) ?></p>
            <p><strong>Heure de départ:</strong> <?= htmlspecialchars($reservation_data['heure_depart']) ?></p>
          </div>
          <div class="col-md-6">
            <?php if ($vehicle_info): ?>
            <p><strong>Véhicule:</strong> <?= htmlspecialchars($vehicle_info['brand_car']) ?> <?= htmlspecialchars($vehicle_info['model_car']) ?></p>
            <p><strong>Plaque:</strong> <?= htmlspecialchars($vehicle_info['plate']) ?></p>
            <?php else: ?>
            <p><strong>Véhicule:</strong> Aucun véhicule sélectionné</p>
            <?php endif; ?>
            <p class="h4 text-primary"><strong>Prix total: <?= number_format(floatval($reservation_data['prix']), 2) ?> €</strong></p>
          </div>
        </div>
      </div>
    </div>    
    <!-- Formulaire de paiement -->
    <div class="text-center">
      <h5 class="mb-3">Choisissez votre méthode de paiement :</h5>
      
      <div class="row justify-content-center">
        <!-- Option Stripe -->
        <div class="col-md-5 mb-3">
          <div class="card h-100">
            <div class="card-body">
              <h6 class="card-title">
                <i class="fa-solid fa-credit-card"></i> Carte bancaire (Stripe)
              </h6>
              <p class="card-text">Paiement sécurisé par carte bancaire</p>              <form action="controller/payement.php" method="POST">
                <input type="hidden" name="method" value="stripe">
                <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="alert alert-warning">
                  <i class="fa-solid fa-exclamation-triangle"></i> Vous devez être connecté pour effectuer un paiement
                </div>
                <button type="button" class="btn btn-secondary w-100" disabled>
                  Connexion requise
                </button>
                <?php elseif (!isset($_SESSION['reservation_data'])): ?>
                <div class="alert alert-warning">
                  <i class="fa-solid fa-exclamation-triangle"></i> Aucune réservation en cours
                </div>
                <button type="button" class="btn btn-secondary w-100" disabled>
                  Pas de réservation
                </button>
                <?php else: ?>
                <button type="submit" class="btn btn-primary w-100">
                  Payer avec Stripe<br>
                  <small>(<?= number_format(floatval($reservation_data['prix']), 2) ?> €)</small>
                </button>
                <?php endif; ?>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Option PayPal -->
        <div class="col-md-5 mb-3">
          <div class="card h-100">
            <div class="card-body">
              <h6 class="card-title">
                <i class="fa-brands fa-paypal"></i> PayPal
              </h6>
              <p class="card-text">Paiement sécurisé via PayPal</p>
              <div id="paypal-button-container"></div>
            </div>
          </div>
        </div>
      </div>
      
      <a href="index.php" class="btn btn-secondary mt-3">Annuler</a>
    </div>
    
    <!-- Script PayPal -->
    <?php 
    
    $paypal_config = require __DIR__ . '/../config/paypal.php';
    ?>
    <script src="https:
    <script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?= number_format(floatval($reservation_data['prix']), 2, '.', '') ?>'
                    },
                    description: 'Réservation parking - <?= htmlspecialchars($reservation_data['date_reservation']) ?> de <?= htmlspecialchars($reservation_data['heure_arrivee']) ?> à <?= htmlspecialchars($reservation_data['heure_depart']) ?>'
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                
                window.location.href = '<?= $paypal_config['success_url'] ?>&paypal_order_id=' + data.orderID;
            });
        },
        onCancel: function(data) {
            
            window.location.href = '<?= $paypal_config['cancel_url'] ?>';
        },
        onError: function(err) {
            console.error('Erreur PayPal:', err);
            alert('Erreur lors du paiement PayPal. Veuillez réessayer.');
        }
    }).render('#paypal-button-container');
    </script>
  <?php else: ?>
    <div class="alert alert-warning text-center">
      <i class="fa-solid fa-exclamation-triangle"></i> Aucune réservation en cours.
      <br><a href="index.php" class="btn btn-primary mt-2">Faire une réservation</a>
    </div>
  <?php endif; ?>
</div>
