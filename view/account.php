<?php

if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mon compte</title>  <!-- Preload des ressources critiques -->
  <link rel="preload" href="https:
  <link rel="preload" href="../css/base.css" as="style">
  <!-- CSS optimisé -->
  <link href="https:
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/navbar.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/account.css">
  <link rel="stylesheet" href="../css/dark-mode.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https:
</head>
<body>
<div class="container my-5">
  <h2 class="text-center mb-4"><i class="fa-solid fa-user"></i> Mon compte</h2>

  <form action="controller/account.php" method="POST" class="card account-card p-4 shadow mb-5">
    <input type="hidden" name="action" value="update_info">
    <input type="hidden" name="redirect" value="account">
    <h5><i class="fa-solid fa-address-card"></i> Mes informations</h5>
    <div class="mb-3">
      <label class="form-label">Nom</label>
      <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($user['family_name'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Prénom</label>
      <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Numéro de téléphone</label>
      <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
    </div>    <button type="submit" class="btn btn-success">Modifier</button>
  </form>

  <form action="controller/account.php" method="POST" class="card account-card p-4 shadow mb-5">
    <input type="hidden" name="action" value="change_password">
    <input type="hidden" name="redirect" value="account">
    <h5><i class="fa-solid fa-key"></i> Changer le mot de passe</h5>
    <div class="mb-3">
      <label class="form-label">Ancien mot de passe</label>
      <input type="password" class="form-control" name="old_password" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Nouveau mot de passe</label>
      <input type="password" class="form-control" name="new_password" required>
    </div>
    <button type="submit" class="btn btn-success">Modifier</button>
  </form>
  <div class="card account-card p-4 shadow mb-5">
    <div class="mb-4">
      <h5><i class="fa-solid fa-calendar-check"></i> Mes réservations</h5>
    </div>
    
    <?php if (empty($reservations)): ?>
      <div class="text-center py-5">
        <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3"></i>
        <p class="text-muted mb-0">Aucune réservation pour le moment</p>
      </div>
    <?php else: ?>      <div class="row">
        <?php foreach ($reservations as $r): ?>
          <div class="col-12 col-lg-6 mb-3">
            <div class="card reservation-card h-100 border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <div class="reservation-date">
                    <h6 class="mb-1 fw-bold">
                      <i class="fa-solid fa-calendar-day text-primary me-2"></i>
                      <?= date('d/m/Y', strtotime($r['date_reservation'])) ?>
                    </h6>
                    <small class="text-muted">
                      <?= date('H:i', strtotime($r['heure_arrivee'])) ?> - 
                      <?= date('H:i', strtotime($r['heure_depart'])) ?>
                    </small>
                  </div>
                  <div class="d-flex flex-column align-items-end">
                    <span class="badge bg-success fs-6 mb-2"><?= htmlspecialchars($r['prix']) ?>€</span>
                    <?php 
                    $status = $r['status'] ?? 'active';
                    $statusConfig = [
                      'active' => ['badge' => 'bg-success', 'icon' => 'fa-check-circle', 'text' => 'Active'],
                      'cancelled' => ['badge' => 'bg-danger', 'icon' => 'fa-times-circle', 'text' => 'Annulée'],
                      'completed' => ['badge' => 'bg-secondary', 'icon' => 'fa-flag-checkered', 'text' => 'Terminée']
                    ];
                    $config = $statusConfig[$status] ?? $statusConfig['active'];
                    ?>
                    <span class="badge <?= $config['badge'] ?> fs-6">
                      <i class="fa-solid <?= $config['icon'] ?> me-1"></i>
                      <?= $config['text'] ?>
                    </span>
                  </div>
                </div>
                  <div class="reservation-details">
                  <div class="mb-2">
                    <span class="badge reservation-type-badge me-2">
                      <?php 
                      $type_icons = [1=>'fa-car', 2=>'fa-motorcycle', 3=>'fa-wheelchair', 4=>'fa-bolt'];
                      $type_id = $r['place_type_id'];
                      $icon = $type_icons[$type_id] ?? 'fa-car';
                      ?>
                      <i class="fa-solid <?= $icon ?> me-1"></i>
                      <?= $place_types[$type_id] ?? 'Inconnu' ?>
                    </span>
                    <?php if (!empty($r['place_id'])): ?>
                    <span class="badge bg-dark me-2">
                      <i class="fa-solid fa-map-pin me-1"></i>
                      Place #<?= htmlspecialchars($r['place_id']) ?>
                    </span>
                    <?php endif; ?>
                  </div>
                    <?php if ($r['vehicle_id'] && ($r['brand_car'] || $r['model_car'] || $r['plate'])): ?>
                    <div class="vehicle-info mb-3">
                      <small class="text-muted d-block">
                        <i class="fa-solid fa-car-side me-1"></i>
                        <strong>Véhicule:</strong>
                        <?= trim(htmlspecialchars($r['brand_car']) . ' ' . htmlspecialchars($r['model_car'])) ?>
                        <?php if ($r['plate']): ?>
                          <span class="text-primary">(<?= htmlspecialchars($r['plate']) ?>)</span>
                        <?php endif; ?>
                      </small>
                    </div>
                  <?php else: ?>
                    <div class="vehicle-info mb-3">
                      <small class="text-muted d-block">
                        <i class="fa-solid fa-car-side me-1"></i>
                        <strong>Véhicule:</strong>
                        <span class="fst-italic text-warning">Aucun véhicule sélectionné</span>
                      </small>
                    </div>
                  <?php endif; ?>
                  
                  <?php 
                  
                  $canCancel = false;
                  if ($status === 'active') {
                    $now = new DateTime();
                    $reservation_datetime = new DateTime($r['date_reservation'] . ' ' . $r['heure_arrivee']);
                    $canCancel = $reservation_datetime > $now;
                  }
                  ?>
                  
                  <?php if ($canCancel): ?>
                    <div class="mt-3">
                      <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmCancelReservation(<?= $r['id'] ?>)">
                        <i class="fa-solid fa-times me-1"></i>Annuler la réservation
                      </button>
                    </div>
                  <?php elseif ($status === 'cancelled'): ?>
                    <div class="mt-3">
                      <small class="text-muted fst-italic">
                        <i class="fa-solid fa-info-circle me-1"></i>
                        Réservation annulée
                      </small>
                    </div>
                  <?php elseif ($status === 'active'): ?>
                    <div class="mt-3">
                      <small class="text-muted fst-italic">
                        <i class="fa-solid fa-info-circle me-1"></i>
                        Réservation en cours ou passée - Annulation impossible
                      </small>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>  <div class="card account-card p-4 shadow mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h5 class="mb-1"><i class="fa-solid fa-car"></i> Mes véhicules</h5>
        <small class="text-muted"><?= count($vehicles) ?> véhicule<?= count($vehicles) > 1 ? 's' : '' ?> enregistré<?= count($vehicles) > 1 ? 's' : '' ?></small>
      </div>
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
        <i class="fa-solid fa-plus me-2"></i>Nouveau véhicule
      </button>
    </div>
    
    <?php if (empty($vehicles)): ?>
      <div class="text-center py-5">
        <div class="mb-4">
          <i class="fa-solid fa-car-side fa-4x text-muted opacity-50"></i>
        </div>
        <h6 class="text-muted mb-2">Aucun véhicule enregistré</h6>
        <p class="text-muted small mb-4">Ajoutez votre premier véhicule pour commencer à réserver des places de parking</p>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
          <i class="fa-solid fa-plus me-2"></i>Ajouter un véhicule
        </button>
      </div>
    <?php else: ?>
      <div class="row">
        <?php foreach ($vehicles as $v): ?>
          <div class="col-12 col-md-6 col-xl-4 mb-4">
            <div class="card vehicle-card h-100 border-0 shadow-sm position-relative">
              <!-- Badge type de véhicule -->
              <div class="position-absolute top-0 start-0 m-3">              <?php 
                $vehicle_colors = [
                  'Voiture' => 'primary',
                  'Voiture (handicap)' => 'warning', 
                  'Voiture électrique' => 'success',
                  'Moto' => 'info'
                ];
                $color = $vehicle_colors[$v['type']] ?? 'primary';                $vehicle_icons = [
                  'Voiture' => 'fa-car',
                  'Voiture (handicap)' => 'fa-wheelchair', 
                  'Voiture électrique' => 'fa-charging-station',
                  'Moto' => 'fa-motorcycle'
                ];
                $icon = $vehicle_icons[$v['type']] ?? 'fa-car';
                ?>                <span class="badge bg-<?= $color ?> px-3 py-2">
                  <i class="fa-solid <?= $icon ?> me-1"></i>
                  <?= htmlspecialchars($v['type']) ?>
                </span>
              </div>
              
              <!-- Actions rapides -->
              <div class="position-absolute top-0 end-0 m-3">
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-sm btn-outline-light vehicle-action-btn" data-bs-toggle="modal" data-bs-target="#editVehicleModal<?= $v['id'] ?>" title="Modifier">
                    <i class="fa-solid fa-pen"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-light vehicle-action-btn text-danger" onclick="confirmDeleteVehicle(<?= $v['id'] ?>)" title="Supprimer">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </div>
              </div>
              
              <div class="card-body pt-5 pb-3">
                <!-- Icône principale du véhicule -->                <div class="text-center mb-4">
                  <div class="vehicle-icon-wrapper">
                    <i class="fa-solid <?= $icon ?> vehicle-main-icon text-<?= $color ?>"></i>
                  </div>
                </div>
                
                <!-- Informations véhicule -->
                <div class="vehicle-info-section">
                  <div class="text-center mb-3">
                    <div class="vehicle-plate-display">
                      <span class="vehicle-plate-number"><?= htmlspecialchars($v['plate']) ?></span>
                    </div>
                  </div>
                  
                  <?php if ($v['brand_car'] || $v['model_car']): ?>
                    <div class="vehicle-details-box">
                      <div class="d-flex align-items-center">
                        <i class="fa-solid fa-tag text-muted me-2"></i>
                        <span class="vehicle-brand-model">
                          <?= trim(htmlspecialchars($v['brand_car']) . ' ' . htmlspecialchars($v['model_car'])) ?: 'Marque/Modèle non renseigné' ?>
                        </span>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="vehicle-details-box">
                      <div class="d-flex align-items-center">
                        <i class="fa-solid fa-tag text-muted me-2"></i>
                        <span class="text-muted fst-italic">Marque/Modèle non renseigné</span>
                      </div>
                    </div>                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Modal ajout véhicule -->
  <div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form class="add-vehicle-form" action="controller/add_vehicle.php" method="post">
          <input type="hidden" name="redirect" value="account">
          <div class="modal-header">
            <h5 class="modal-title" id="addVehicleModalLabel">Ajouter un véhicule</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Type de véhicule</label>              <select class="form-select" name="type" required>
                <option value="">-- Sélectionner --</option>
                <option value="Voiture">Voiture</option>
                <option value="Voiture (handicap)">Voiture (handicap)</option>
                <option value="Voiture électrique">Voiture électrique</option>
                <option value="Moto">Moto</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Marque</label>
              <input type="text" class="form-control" name="brand_car" placeholder="Ex: Peugeot, BMW, Honda...">
            </div>
            <div class="mb-3">
              <label class="form-label">Modèle</label>
              <input type="text" class="form-control" name="model_car" placeholder="Ex: 208, X3, CBR600...">
            </div>            <div class="mb-3">
              <label class="form-label">Immatriculation</label>
              <input type="text" class="form-control" name="plate" id="new_vehicle_plate" required maxlength="20" 
                     placeholder="Ex: AB-123-CD" autocomplete="off">
              <div class="plate-feedback" id="new_plate_feedback"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-success">Ajouter</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">Véhicule modifié avec succès !</div>
  <?php elseif (isset($_GET['error'])): ?>
  <div class="alert alert-danger">Erreur lors de la modification du véhicule.</div>
  <?php endif; ?>

  <!-- Modals édition véhicule placées en dehors du tableau -->
  <?php foreach ($vehicles as $v): ?>
  <div class="modal fade" id="editVehicleModal<?= $v['id'] ?>" tabindex="-1" aria-labelledby="editVehicleModalLabel<?= $v['id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="controller/update_vehicle.php" method="post">
          <div class="modal-header">
            <h5 class="modal-title" id="editVehicleModalLabel<?= $v['id'] ?>">Modifier le véhicule</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" value="<?= $v['id'] ?>">
            <input type="hidden" name="redirect" value="account">
            <div class="mb-3">
              <label class="form-label" for="vehicle_type_<?= $v['id'] ?>">Type de véhicule</label>              <select class="form-select" id="vehicle_type_<?= $v['id'] ?>" name="type" required>
                <option value="Voiture" <?= $v['type']==='Voiture'?'selected':'' ?>>Voiture</option>
                <option value="Voiture (handicap)" <?= $v['type']==='Voiture (handicap)'?'selected':'' ?>>Voiture (handicap)</option>
                <option value="Voiture électrique" <?= $v['type']==='Voiture électrique'?'selected':'' ?>>Voiture électrique</option>
                <option value="Moto" <?= $v['type']==='Moto'?'selected':'' ?>>Moto</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label" for="vehicle_brand_<?= $v['id'] ?>">Marque</label>
              <input type="text" class="form-control" id="vehicle_brand_<?= $v['id'] ?>" name="brand_car" value="<?= htmlspecialchars($v['brand_car'] ?? '') ?>" placeholder="Ex: Peugeot, BMW, Honda...">
            </div>
            <div class="mb-3">
              <label class="form-label" for="vehicle_model_<?= $v['id'] ?>">Modèle</label>
              <input type="text" class="form-control" id="vehicle_model_<?= $v['id'] ?>" name="model_car" value="<?= htmlspecialchars($v['model_car'] ?? '') ?>" placeholder="Ex: 208, X3, CBR600...">
            </div>            <div class="mb-3">
              <label class="form-label" for="vehicle_plate_<?= $v['id'] ?>">Immatriculation</label>
              <input type="text" class="form-control vehicle-plate-input" id="vehicle_plate_<?= $v['id'] ?>" 
                     name="plate" value="<?= htmlspecialchars($v['plate']) ?>" required maxlength="20" 
                     data-vehicle-id="<?= $v['id'] ?>" autocomplete="off">
              <div class="plate-feedback" id="plate_feedback_<?= $v['id'] ?>"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-success">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<script src="https:
<style>

.modal {
  z-index: 1055 !important;
}
.modal-backdrop {
  z-index: 1050 !important;
}
.modal-dialog {
  z-index: 1060 !important;
}
.modal .form-control,
.modal .form-select {
  pointer-events: auto !important;
  user-select: auto !important;
  -webkit-user-select: auto !important;
  -moz-user-select: auto !important;
  position: relative !important;
  z-index: 1 !important;
}
.modal .form-control:focus,
.modal .form-select:focus {
  outline: 0 !important;
  border-color: #86b7fe !important;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}

.plate-feedback {
  font-size: 0.875em;
  margin-top: 0.25rem;
  min-height: 20px;
}

.plate-feedback.success {
  color: #198754;
}

.plate-feedback.error {
  color: #dc3545;
}

.plate-feedback.checking {
  color: #0d6efd;
}

.form-control.plate-valid {
  border-color: #198754;
}

.form-control.plate-invalid {
  border-color: #dc3545;
}
</style>
<script>

function confirmDeleteVehicle(vehicleId) {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')) {
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'controller/delete_vehicle.php';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'vehicle_id';
    input.value = vehicleId;
    
    const redirectInput = document.createElement('input');
    redirectInput.type = 'hidden';
    redirectInput.name = 'redirect';
    redirectInput.value = 'account';
    
    form.appendChild(input);
    form.appendChild(redirectInput);
    document.body.appendChild(form);
    form.submit();
  }
}

let plateCheckTimeout;

function checkPlateAvailability(plateInput, feedbackElement, excludeId = null) {
  clearTimeout(plateCheckTimeout);
  
  const plate = plateInput.value.trim();
  
  if (!plate) {
    feedbackElement.innerHTML = '';
    plateInput.classList.remove('plate-valid', 'plate-invalid');
    return;
  }

  if (plate.length < 2) {
    feedbackElement.innerHTML = '<span class="error">⚠️ Plaque trop courte</span>';
    feedbackElement.className = 'plate-feedback error';
    plateInput.classList.remove('plate-valid');
    plateInput.classList.add('plate-invalid');
    return;
  }
  
  feedbackElement.innerHTML = '<span class="checking">🔍 Vérification...</span>';
  feedbackElement.className = 'plate-feedback checking';
  plateInput.classList.remove('plate-valid', 'plate-invalid');
  
  plateCheckTimeout = setTimeout(() => {
    const requestData = {
      plate: plate
    };
    
    if (excludeId) {
      requestData.exclude_id = excludeId;
    }
    
    fetch('api/check_plate.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(requestData)
    })
    .then(response => response.json())
    .then(data => {
      if (data.exists) {
        feedbackElement.innerHTML = `<span class="error">❌ ${data.message}</span>`;
        feedbackElement.className = 'plate-feedback error';
        plateInput.classList.remove('plate-valid');
        plateInput.classList.add('plate-invalid');
      } else {
        feedbackElement.innerHTML = '<span class="success">✅ Plaque disponible</span>';
        feedbackElement.className = 'plate-feedback success';
        plateInput.classList.remove('plate-invalid');
        plateInput.classList.add('plate-valid');
      }
    })
    .catch(error => {
      console.error('Erreur lors de la vérification:', error);
      feedbackElement.innerHTML = '<span class="error">⚠️ Erreur de vérification</span>';
      feedbackElement.className = 'plate-feedback error';
      plateInput.classList.remove('plate-valid', 'plate-invalid');
    });
  }, 500); 
}

document.addEventListener('DOMContentLoaded', function() {
  
  const newVehiclePlateInput = document.getElementById('new_vehicle_plate');
  const newPlateFeedback = document.getElementById('new_plate_feedback');
  
  if (newVehiclePlateInput && newPlateFeedback) {
    newVehiclePlateInput.addEventListener('input', function() {
      checkPlateAvailability(this, newPlateFeedback);
    });
  }

  const editPlateInputs = document.querySelectorAll('.vehicle-plate-input');
  editPlateInputs.forEach(function(input) {
    const vehicleId = input.getAttribute('data-vehicle-id');
    const feedbackId = 'plate_feedback_' + vehicleId;
    const feedback = document.getElementById(feedbackId);
    
    if (feedback) {
      input.addEventListener('input', function() {
        checkPlateAvailability(this, feedback, vehicleId);
      });
    }
  });

  const editModals = document.querySelectorAll('[id^="editVehicleModal"]');
  editModals.forEach(function(modal) {
    modal.addEventListener('shown.bs.modal', function() {
      
      const inputs = modal.querySelectorAll('input, select, textarea');
      inputs.forEach(function(input) {
        input.style.pointerEvents = 'auto';
        input.style.userSelect = 'auto';
        input.removeAttribute('disabled');
        input.removeAttribute('readonly');
      });

      const firstInput = modal.querySelector('select, input[type="text"]:not([type="hidden"])');
      if (firstInput) {
        setTimeout(function() {
          firstInput.focus();
          firstInput.select();
        }, 150);
      }
    });
  });
});

function confirmCancelReservation(reservationId) {
  if (confirm('Êtes-vous sûr de vouloir annuler cette réservation ?\n\nCette action est irréversible.')) {
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'controller/cancel_reservation.php';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'reservation_id';
    input.value = reservationId;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
  }
}
</script>
</body>
</html>