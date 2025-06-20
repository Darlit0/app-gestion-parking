<!DOCTYPE html>
<html lang="fr" data-bs-theme="">
<head>
  <meta charset="UTF-8">
  <title>Administration - Gestion Parking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https:
  <link rel="stylesheet" href="https:
  <link rel="stylesheet" href="../css/base.css">
  <link rel="stylesheet" href="../css/navbar.css">
  <link rel="stylesheet" href="../css/components.css">
  <link rel="stylesheet" href="../css/admin.css">
  <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>

<?php

?>

<div class="container my-5">
  <h2 class="text-center mb-4"><i class="fa-solid fa-screwdriver-wrench"></i> Espace Administrateur</h2>

  <!-- Gestion des places de parking -->
  <div class="card admin-card p-4 shadow mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5><i class="fa-solid fa-square-parking"></i> Configuration des places de parking</h5>
      <span class="badge bg-info">Temps réel</span>
    </div>
    
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle table-sm">
        <thead>
          <tr>
            <th style="width: 25%;">Type de place</th>
            <th style="width: 12%;">Total</th>
            <th style="width: 12%;">Occupées</th>
            <th style="width: 12%;">Libres</th>
            <th style="width: 15%;">Prix/h (€)</th>
            <th style="width: 24%;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($places_disponibles as $place): ?>
          <tr>
            <td><strong><?= htmlspecialchars($place['type_name']) ?></strong></td>
            <td>
              <span class="badge bg-primary"><?= $place['total_places'] ?></span>
            </td>
            <td>
              <span class="badge bg-warning"><?= $place['places_occupees'] ?? 0 ?></span>
            </td>
            <td>
              <span class="badge bg-success"><?= $place['places_libres'] ?? $place['total_places'] ?></span>
            </td>
            <td><?= number_format($place['prix_heure'], 2) ?> €</td>
            <td>
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editPlaceModal<?= $place['id'] ?>">
                <i class="fa-solid fa-pen"></i> Modifier
              </button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Gestion des réservations -->
  <div class="card admin-card p-4 shadow mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5><i class="fa-solid fa-calendar-check"></i> Toutes les réservations</h5>
      <span class="badge bg-info"><?= $total_reservations ?> réservation<?= $total_reservations > 1 ? 's' : '' ?></span>
    </div>
    
    <?php if (empty($reservations)): ?>
      <div class="text-center py-5">
        <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3"></i>
        <p class="text-muted mb-0">Aucune réservation dans la base de données</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle table-sm">
          <thead class="table-dark">
            <tr>
              <th style="width: 8%;">ID</th>
              <th style="width: 15%;">Utilisateur</th>
              <th style="width: 12%;">Date/Heure</th>
              <th style="width: 15%;">Véhicule</th>
              <th style="width: 12%;">Type de place</th>
              <th style="width: 8%;">Place #</th>
              <th style="width: 8%;">Prix</th>
              <th style="width: 10%;">Statut</th>
              <th style="width: 12%;">Contact</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservations as $reservation): ?>
            <tr>
              <td>
                <span class="badge bg-primary">#<?= $reservation['id'] ?></span>
              </td>
              <td>
                <strong><?= htmlspecialchars($reservation['user_name'] ?? '') ?> <?= htmlspecialchars($reservation['user_family_name'] ?? '') ?></strong>
              </td>
              <td>
                <div class="small">
                  <strong><?= date('d/m/Y', strtotime($reservation['date_reservation'])) ?></strong><br>
                  <span class="text-muted">
                    <?= date('H:i', strtotime($reservation['heure_arrivee'])) ?> - 
                    <?= date('H:i', strtotime($reservation['heure_depart'])) ?>
                  </span>
                </div>
              </td>
              <td>
                <?php if ($reservation['vehicle_id'] && ($reservation['brand_car'] || $reservation['model_car'] || $reservation['plate'])): ?>
                  <div class="small">
                    <strong><?= trim(htmlspecialchars($reservation['brand_car']) . ' ' . htmlspecialchars($reservation['model_car'])) ?: 'N/A' ?></strong><br>
                    <span class="text-primary"><?= htmlspecialchars($reservation['plate']) ?></span>
                    <?php if ($reservation['vehicle_type']): ?>
                      <br><span class="badge badge-sm bg-secondary"><?= htmlspecialchars($reservation['vehicle_type']) ?></span>
                    <?php endif; ?>
                  </div>
                <?php else: ?>
                  <span class="text-muted fst-italic small">Aucun véhicule</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge bg-info"><?= htmlspecialchars($reservation['place_type_name'] ?? 'Inconnu') ?></span>
              </td>
              <td>
                <?php if (!empty($reservation['place_id'])): ?>
                  <span class="badge bg-dark">#<?= htmlspecialchars($reservation['place_id']) ?></span>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <strong class="text-success"><?= htmlspecialchars($reservation['prix']) ?>€</strong>
              </td>
              <td>
                <?php 
                $status = $reservation['status'] ?? 'active';
                $statusConfig = [
                  'active' => ['badge' => 'bg-success', 'icon' => 'fa-check-circle', 'text' => 'Active'],
                  'cancelled' => ['badge' => 'bg-danger', 'icon' => 'fa-times-circle', 'text' => 'Annulée'],
                  'completed' => ['badge' => 'bg-secondary', 'icon' => 'fa-flag-checkered', 'text' => 'Terminée']
                ];
                $config = $statusConfig[$status] ?? $statusConfig['active'];
                ?>
                <span class="badge <?= $config['badge'] ?> small">
                  <i class="fa-solid <?= $config['icon'] ?> me-1"></i>
                  <?= $config['text'] ?>
                </span>
              </td>
              <td>
                <div class="small">
                  <?php if ($reservation['user_email']): ?>
                    <a href="mailto:<?= htmlspecialchars($reservation['user_email']) ?>" class="text-decoration-none">
                      <i class="fa-solid fa-envelope me-1"></i>
                    </a>
                  <?php endif; ?>
                  <?php if ($reservation['user_phone']): ?>
                    <a href="tel:<?= htmlspecialchars($reservation['user_phone']) ?>" class="text-decoration-none">
                      <i class="fa-solid fa-phone me-1"></i>
                    </a>
                    <br><span class="text-muted"><?= htmlspecialchars($reservation['user_phone']) ?></span>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted small">
          Affichage de <?= ($pagination_reservations['current_page'] - 1) * 10 + 1 ?> à <?= min($pagination_reservations['current_page'] * 10, $pagination_reservations['total_reservations']) ?> 
          sur <?= $pagination_reservations['total_reservations'] ?> réservation<?= $pagination_reservations['total_reservations'] > 1 ? 's' : '' ?>
        </div>
        
        <div class="btn-group" role="group">
          <?php if ($pagination_reservations['has_prev']): ?>
            <a href="?page=admin&reservations_page=<?= $pagination_reservations['prev_page'] ?>" class="btn btn-outline-primary btn-sm">
              <i class="fa-solid fa-chevron-left me-1"></i>Précédent
            </a>
          <?php else: ?>
            <button class="btn btn-outline-secondary btn-sm" disabled>
              <i class="fa-solid fa-chevron-left me-1"></i>Précédent
            </button>
          <?php endif; ?>
          
          <button class="btn btn-primary btn-sm" disabled>
            Page <?= $pagination_reservations['current_page'] ?> / <?= $pagination_reservations['total_pages'] ?>
          </button>
          
          <?php if ($pagination_reservations['has_next']): ?>
            <a href="?page=admin&reservations_page=<?= $pagination_reservations['next_page'] ?>" class="btn btn-outline-primary btn-sm">
              Suivant<i class="fa-solid fa-chevron-right ms-1"></i>
            </a>
          <?php else: ?>
            <button class="btn btn-outline-secondary btn-sm" disabled>
              Suivant<i class="fa-solid fa-chevron-right ms-1"></i>
            </button>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Table utilisateurs -->
  <div class="card admin-card p-4 shadow">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0"><i class="fa-solid fa-users"></i> Utilisateurs enregistrés</h5>
      <span class="badge bg-primary">
        <?= $pagination['total_users'] ?> utilisateur<?= $pagination['total_users'] > 1 ? 's' : '' ?>
      </span>
    </div>
    
    <!-- Informations de pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
      <span>
        Page <?= $pagination['current_page'] ?> sur <?= $pagination['total_pages'] ?> 
        (<?= ($pagination['current_page'] - 1) * $pagination['users_per_page'] + 1 ?> - 
        <?= min($pagination['current_page'] * $pagination['users_per_page'], $pagination['total_users']) ?> 
        sur <?= $pagination['total_users'] ?>)
      </span>
    </div>
    <?php endif; ?>
    
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Nom de famille</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($users)): ?>
          <tr>
            <td colspan="6" class="text-center text-muted py-4">
              <i class="fa-solid fa-users-slash fa-2x mb-2"></i><br>
              Aucun utilisateur trouvé
            </td>
          </tr>
          <?php else: ?>
          <?php foreach ($users as $user): ?>
          <tr>
            <td><?= htmlspecialchars($user['name'] ?? '') ?></td>
            <td><?= htmlspecialchars($user['family_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($user['phone'] ?? '-') ?></td>
            <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
            <td>
              <?php if ($user['admin']): ?>
                <span class="badge bg-danger">
                  <i class="fa-solid fa-crown me-1"></i>Administrateur
                </span>
              <?php else: ?>
                <span class="badge bg-secondary">
                  <i class="fa-solid fa-user me-1"></i>Utilisateur
                </span>
              <?php endif; ?>
            </td>
            <td>
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['id'] ?>">Modifier</button>
              <?php if ($user['id'] != $_SESSION['user_id']): ?>
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $user['id'] ?>">Supprimer</button>
              <?php else: ?>
                <button type="button" class="btn btn-sm btn-secondary" disabled title="Vous ne pouvez pas supprimer votre propre compte">Supprimer</button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <nav aria-label="Pagination utilisateurs" class="mt-4">
      <ul class="pagination justify-content-center mb-0">
        <!-- Bouton Précédent -->
        <li class="page-item <?= !$pagination['has_previous'] ? 'disabled' : '' ?>">
          <?php if ($pagination['has_previous']): ?>
            <a class="page-link" href="?page=admin&page_num=<?= $pagination['previous_page'] ?>" aria-label="Page précédente">
              <i class="fa-solid fa-chevron-left"></i>
            </a>
          <?php else: ?>
            <span class="page-link" aria-label="Page précédente">
              <i class="fa-solid fa-chevron-left"></i>
            </span>
          <?php endif; ?>
        </li>
        
        <!-- Pages numériques -->
        <?php 
        $start = max(1, $pagination['current_page'] - 2);
        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);

        if ($start > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?page=admin&page_num=1">1</a>
        </li>
        <?php if ($start > 2): ?>
        <li class="page-item disabled">
          <span class="page-link">...</span>
        </li>
        <?php endif; ?>
        <?php endif; ?>
        
        <!-- Pages autour de la page courante -->
        <?php for ($i = $start; $i <= $end; $i++): ?>
        <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
          <?php if ($i == $pagination['current_page']): ?>
            <span class="page-link" aria-current="page"><?= $i ?></span>
          <?php else: ?>
            <a class="page-link" href="?page=admin&page_num=<?= $i ?>"><?= $i ?></a>
          <?php endif; ?>
        </li>
        <?php endfor; ?>
        
        <!-- Afficher la dernière page si on est loin de la fin -->
        <?php if ($end < $pagination['total_pages']): ?>
        <?php if ($end < $pagination['total_pages'] - 1): ?>
        <li class="page-item disabled">
          <span class="page-link">...</span>
        </li>
        <?php endif; ?>
        <li class="page-item">
          <a class="page-link" href="?page=admin&page_num=<?= $pagination['total_pages'] ?>"><?= $pagination['total_pages'] ?></a>
        </li>
        <?php endif; ?>
        
        <!-- Bouton Suivant -->
        <li class="page-item <?= !$pagination['has_next'] ? 'disabled' : '' ?>">
          <?php if ($pagination['has_next']): ?>
            <a class="page-link" href="?page=admin&page_num=<?= $pagination['next_page'] ?>" aria-label="Page suivante">
              <i class="fa-solid fa-chevron-right"></i>
            </a>
          <?php else: ?>
            <span class="page-link" aria-label="Page suivante">
              <i class="fa-solid fa-chevron-right"></i>
            </span>
          <?php endif; ?>
        </li>
      </ul>
    </nav>
    <?php endif; ?>
  </div>
</div>

<!-- Modals pour éditer les utilisateurs -->
<?php foreach ($users as $user): ?>
<!-- Modal édition utilisateur -->
<div class="modal fade" id="editUserModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?= $user['id'] ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editUserModalLabel<?= $user['id'] ?>">Modifier l'utilisateur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="edit-user-form" method="POST" action="controller/update_user.php">
        <input type="hidden" name="id" value="<?= $user['id'] ?>">
        <input type="hidden" name="redirect" value="admin">
        <input type="hidden" name="page_num" value="<?= $pagination['current_page'] ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Nom de famille</label>
            <input type="text" class="form-control" name="family_name" value="<?= htmlspecialchars($user['family_name'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">
              <i class="fa-solid fa-crown me-1"></i>Statut administrateur
            </label>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="admin" id="adminSwitch<?= $user['id'] ?>" 
                     value="1" <?= $user['admin'] ? 'checked' : '' ?>
                     <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
              <label class="form-check-label" for="adminSwitch<?= $user['id'] ?>">
                Accorder les privilèges d'administrateur
              </label>
            </div>
            <?php if ($user['id'] == $_SESSION['user_id']): ?>
              <small class="text-muted">
                <i class="fa-solid fa-info-circle me-1"></i>
                Vous ne pouvez pas modifier votre propre statut administrateur
              </small>
            <?php else: ?>
              <small class="text-muted">
                <i class="fa-solid fa-exclamation-triangle me-1"></i>
                Attention : Les administrateurs ont accès à toutes les fonctionnalités du système
              </small>
            <?php endif; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal suppression utilisateur -->
<div class="modal fade" id="deleteUserModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="deleteUserModalLabel<?= $user['id'] ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteUserModalLabel<?= $user['id'] ?>">Confirmer la suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning">
          <i class="fa-solid fa-exclamation-triangle me-2"></i>
          <strong>Attention !</strong> Cette action est irréversible.
        </div>
        <p>Voulez-vous vraiment supprimer l'utilisateur <strong><?= htmlspecialchars($user['name'] . ' ' . $user['family_name']) ?></strong> ?</p>
        <p class="text-muted small">Email : <?= htmlspecialchars($user['email']) ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-danger" onclick="confirmDeleteUser(<?= $user['id'] ?>)" data-bs-dismiss="modal">
          <i class="fa-solid fa-trash me-1"></i>Supprimer définitivement
        </button>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>

<!-- Modals pour éditer les places -->
<?php foreach ($places_disponibles as $place): ?>
<div class="modal fade" id="editPlaceModal<?= $place['id'] ?>" tabindex="-1" aria-labelledby="editPlaceModalLabel<?= $place['id'] ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form class="edit-place-form" method="POST" action="controller/update_places.php">
        <input type="hidden" name="action" value="update_places">
        <input type="hidden" name="place_type_id" value="<?= $place['id'] ?>">
        <input type="hidden" name="redirect" value="admin">
        <div class="modal-header">
          <h5 class="modal-title" id="editPlaceModalLabel<?= $place['id'] ?>">
            <i class="fa-solid fa-square-parking me-2"></i>Modifier <?= htmlspecialchars($place['type_name']) ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fa-solid fa-info-circle me-2"></i>
            <strong>Configuration actuelle :</strong> <?= $place['total_places'] ?> places à <?= number_format($place['prix_heure'], 2) ?>€/h
          </div>
          
          <div class="mb-3">
            <label class="form-label">
              <i class="fa-solid fa-hashtag me-1"></i>Nombre total de places
            </label>
            <input type="number" class="form-control" name="total_places" value="<?= $place['total_places'] ?>" min="0" required>
            <div class="form-text">Actuellement : <?= $place['places_libres'] ?? $place['total_places'] ?> libres, <?= $place['places_occupees'] ?? 0 ?> occupées</div>
          </div>
          
          <div class="mb-3">
            <label class="form-label">
              <i class="fa-solid fa-euro-sign me-1"></i>Prix par heure (€)
            </label>
            <input type="number" step="0.01" class="form-control" name="prix_heure" value="<?= $place['prix_heure'] ?>" min="0" required>
          </div>
          
          <div class="mb-3">
            <label class="form-label">
              <i class="fa-solid fa-file-lines me-1"></i>Description (optionnel)
            </label>
            <textarea class="form-control" name="description" rows="2" placeholder="Description du type de place..."><?= htmlspecialchars($place['description'] ?? '') ?></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa-solid fa-times me-1"></i>Annuler
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-save me-1"></i>Enregistrer les modifications
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>

<script src="https:
<script>

function confirmDeleteUser(userId) {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'controller/delete_user.php';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id';
    input.value = userId;
    
    const redirectInput = document.createElement('input');
    redirectInput.type = 'hidden';
    redirectInput.name = 'redirect';
    redirectInput.value = 'admin';
    
    const pageInput = document.createElement('input');
    pageInput.type = 'hidden';
    pageInput.name = 'page_num';
    pageInput.value = '<?= $pagination['current_page'] ?>';
    
    form.appendChild(input);
    form.appendChild(redirectInput);
    form.appendChild(pageInput);
    document.body.appendChild(form);
    form.submit();
  }
}

document.addEventListener('DOMContentLoaded', function() {
  
  const modals = document.querySelectorAll('.modal');
  
  modals.forEach(function(modal) {
    
    modal.addEventListener('shown.bs.modal', function() {
      
      const firstInput = modal.querySelector('input:not([type="hidden"]):not([readonly]), select, textarea');
      if (firstInput) {
        setTimeout(function() {
          firstInput.focus();
          if (firstInput.type === 'text' || firstInput.type === 'email') {
            firstInput.select();
          }
        }, 100);
      }
    });

    modal.addEventListener('hidden.bs.modal', function() {
      
      const inputs = modal.querySelectorAll('.form-control');
      inputs.forEach(function(input) {
        input.classList.remove('is-valid', 'is-invalid');
      });

      const feedbacks = modal.querySelectorAll('.invalid-feedback, .valid-feedback');
      feedbacks.forEach(function(feedback) {
        feedback.remove();
      });
    });
  });

  const forms = document.querySelectorAll('.edit-user-form, .edit-place-form');
  
  forms.forEach(function(form) {
    const inputs = form.querySelectorAll('input[required], input[type="email"]');
    
    inputs.forEach(function(input) {
      input.addEventListener('blur', function() {
        validateInput(input);
      });
      
      input.addEventListener('input', function() {
        
        input.classList.remove('is-valid', 'is-invalid');
        const feedback = input.parentNode.querySelector('.invalid-feedback');
        if (feedback) {
          feedback.remove();
        }
      });
    });
    
    form.addEventListener('submit', function(e) {
      let isValid = true;
      
      inputs.forEach(function(input) {
        if (!validateInput(input)) {
          isValid = false;
        }
      });
      
      if (!isValid) {
        e.preventDefault();
        
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
          firstInvalid.focus();
        }
      }
    });
  });
});

function validateInput(input) {
  const value = input.value.trim();
  let isValid = true;
  let message = '';

  const existingFeedback = input.parentNode.querySelector('.invalid-feedback');
  if (existingFeedback) {
    existingFeedback.remove();
  }

  if (input.hasAttribute('required') && !value) {
    isValid = false;
    message = 'Ce champ est obligatoire';
  }

  else if (input.type === 'email' && value) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
      isValid = false;
      message = 'Format d\'email invalide';
    }
  }

  else if (input.type === 'number' && value) {
    const numValue = parseFloat(value);
    const min = parseFloat(input.getAttribute('min'));
    if (isNaN(numValue) || (min !== null && numValue < min)) {
      isValid = false;
      message = 'Valeur numérique invalide';
    }
  }

  if (isValid) {
    input.classList.remove('is-invalid');
    input.classList.add('is-valid');
  } else {
    input.classList.remove('is-valid');
    input.classList.add('is-invalid');

    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = message;
    input.parentNode.appendChild(feedback);
  }
  
  return isValid;
}

function confirmDeleteUser(userId) {
  
  const deleteModal = document.getElementById('deleteUserModal' + userId);
  if (deleteModal) {
    const modalInstance = bootstrap.Modal.getInstance(deleteModal);
    if (modalInstance) {
      modalInstance.hide();
    }
  }

  setTimeout(function() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'controller/delete_user.php';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'id';
    input.value = userId;
    
    const redirectInput = document.createElement('input');
    redirectInput.type = 'hidden';
    redirectInput.name = 'redirect';
    redirectInput.value = 'admin';
    
    form.appendChild(input);
    form.appendChild(redirectInput);
    document.body.appendChild(form);
    form.submit();
  }, 300);
}
</script>
</body>
</html>