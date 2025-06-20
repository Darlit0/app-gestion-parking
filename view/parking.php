<?php

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation parking</title>    <link href="https:
    <link rel="stylesheet" href="https:
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/components.css">
    <link rel="stylesheet" href="../css/parking.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4"><i class="fa-solid fa-calendar-check"></i> Nouvelle réservation</h2>
    
    <form id="reservationForm" method="POST" action="controller/store_reservation_data.php" class="card p-4 shadow">        <?php if (!empty($user_vehicles)): ?>
        <div class="mb-3">
            <label class="form-label">Choisir un véhicule</label>
            <select class="form-select" name="vehicle_id" id="vehicle_select">
                <option value="">-- Aucun / Réserver sans véhicule --</option>
                <?php foreach ($user_vehicles as $v): ?>
                    <option value="<?= $v['id'] ?>" data-vehicle-type="<?= htmlspecialchars($v['type']) ?>">
                        <?= htmlspecialchars($v['brand_car']) ?> <?= htmlspecialchars($v['model_car']) ?> (<?= htmlspecialchars($v['plate']) ?>) - <?= htmlspecialchars($v['type']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>        <div class="mb-3">
            <label class="form-label"><i class="fa-solid fa-car"></i> Type de place</label>
            <select class="form-select" name="place_type_id" id="place_type_select" required>
                <option value="">-- Sélectionner --</option>
                <option value="1">Voiture</option>
                <option value="2">Moto</option>
                <option value="3" id="handicap_option">Voiture (handicap)</option>
                <option value="4" id="electric_option">Électrique</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="fa-solid fa-calendar-day"></i> Date de réservation</label>
            <?php $minDate = (new DateTime('+1 day'))->format('Y-m-d'); ?>
            <input type="date" class="form-control" name="date_reservation" required min="<?= $minDate ?>">
        </div>        <div class="mb-3">
            <label class="form-label"><i class="fa-solid fa-clock"></i> Heure d'arrivée</label>
            <select class="form-select" name="heure_arrivee" required>
                <option value="">-- Sélectionner --</option>
                <?php
                for ($h = 6; $h <= 23; $h++) {
                    for ($m = 0; $m < 60; $m += 30) {
                        $time = sprintf('%02d:%02d', $h, $m);
                        echo "<option value=\"$time\">$time</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label"><i class="fa-solid fa-clock"></i> Heure de départ</label>
            <select class="form-select" name="heure_depart" required>
                <option value="">-- Sélectionner --</option>
                <?php
                for ($h = 6; $h <= 23; $h++) {
                    for ($m = 0; $m < 60; $m += 30) {
                        $time = sprintf('%02d:%02d', $h, $m);
                        echo "<option value=\"$time\">$time</option>";
                    }
                }
                ?>
            </select>
        </div>        <div class="mb-3">
            <label class="form-label"><i class="fa-solid fa-euro-sign"></i> Prix (€)</label>
            <div class="input-group">
                <input type="number" step="0.01" class="form-control" name="prix" id="prix_input" readonly required>
                <span class="input-group-text">€</span>
            </div>
            <small class="text-muted">Le prix est calculé automatiquement selon le type de place et la durée</small>
        </div><button type="submit" class="btn btn-primary w-100">Réserver</button>
    </form>

    <!-- Affichage des places disponibles -->
    <div class="card p-4 shadow mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5><i class="fa-solid fa-chart-pie"></i> Places disponibles</h5>
            <span class="badge bg-info">Actuellement</span>
        </div>
          <div class="row g-3">
            <?php foreach ($places_disponibles as $place): ?>            <div class="col-6 col-md-6 col-lg-3">
                <div class="card h-100 border-0 parking-place-card">
                    <div class="card-body text-center"><h6 class="card-title text-primary mb-2">
                            <i class="fa-solid fa-<?php 
                                if ($place['type_name'] === 'Moto') {
                                    echo 'motorcycle';
                                } elseif (strpos($place['type_name'], 'handicap') !== false) {
                                    echo 'wheelchair';
                                } elseif (strpos($place['type_name'], 'lectrique') !== false) {
                                    echo 'bolt';
                                } else {
                                    echo 'car';
                                }
                            ?>"></i>
                            <?= htmlspecialchars($place['type_name']) ?>
                        </h6>
                        <div class="row text-center mb-2">
                            <div class="col">
                                <div class="text-success fw-bold fs-4"><?= $place['places_libres'] ?? ($place['total_places'] - ($place['places_occupees'] ?? 0)) ?></div>
                                <small class="text-muted">Libres</small>
                            </div>
                            <div class="col">
                                <div class="text-danger fw-bold fs-4"><?= $place['places_occupees'] ?? 0 ?></div>
                                <small class="text-muted">Occupées</small>
                            </div>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <?php 
                            $total = $place['total_places'];
                            $occupees = $place['places_occupees'] ?? 0;
                            $percentage = $total > 0 ? ($occupees / $total) * 100 : 0;
                            ?>
                            <div class="progress-bar bg-danger" style="width: <?= $percentage ?>%"></div>
                        </div>
                        <small class="text-muted">
                            Total: <?= $place['total_places'] ?> places
                            <?php if (isset($place['prix_heure'])): ?>
                            <br><?= number_format($place['prix_heure'], 2) ?> €/h
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>

const TARIFS_BD = {
    <?php foreach ($place_types as $type): ?>
    '<?= $type['id'] ?>': <?= $type['prix_heure'] ?>,
    <?php endforeach; ?>
};

const VEHICLE_TYPE_MAPPING = {
    'voiture électrique': ['4', '1', '3'], 
    'voiture (handicap)': ['3', '1'], 
    'voiture': ['1'], 
    'moto': ['2'], 
    'électrique': ['4', '1', '3'] 
};

function handleVehicleRestrictions() {
    const vehicleSelect = document.getElementById('vehicle_select');
    const placeTypeSelect = document.getElementById('place_type_select');
    
    if (!vehicleSelect || !placeTypeSelect) return;
    
    function updatePlaceTypeOptions() {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const vehicleType = selectedOption?.getAttribute('data-vehicle-type')?.toLowerCase() || '';
        const options = placeTypeSelect.querySelectorAll('option[value]');

        if (!vehicleSelect.value) {
            options.forEach(opt => {
                opt.disabled = false;
                opt.style.color = '';
                opt.textContent = opt.textContent.replace(' (non compatible)', '');
            });
            return;
        }        
        let allowedTypes = [];

        if (vehicleType.includes('électrique') || vehicleType.includes('electrique')) {
            allowedTypes = ['4', '1', '3']; 
        }
        
        else if (vehicleType.includes('handicap')) {
            allowedTypes = ['3', '1']; 
        }
        
        else if (vehicleType.includes('voiture')) {
            allowedTypes = ['1']; 
        }
        
        else if (vehicleType.includes('moto')) {
            allowedTypes = ['2']; 
        }
        
        else {
            for (const [type, placeTypes] of Object.entries(VEHICLE_TYPE_MAPPING)) {
                if (vehicleType.includes(type.toLowerCase())) {
                    allowedTypes = placeTypes;
                    break;
                }
            }
        }

        options.forEach(opt => {
            if (!opt.value) return; 
            
            const isAllowed = allowedTypes.includes(opt.value);
            opt.disabled = !isAllowed;
            opt.style.color = isAllowed ? '' : '#6c757d';
            
            const baseText = opt.textContent.replace(' (non compatible)', '');
            opt.textContent = isAllowed ? baseText : baseText + ' (non compatible)';
        });

        if (allowedTypes.length > 0 && !allowedTypes.includes(placeTypeSelect.value)) {
            placeTypeSelect.value = '';
        }
    }
    
    vehicleSelect.addEventListener('change', function() {
        updatePlaceTypeOptions();
        calculatePrice(); 
    });
}

function calculatePrice() {
    const placeTypeSelect = document.querySelector('select[name="place_type_id"]');
    const heureArrivee = document.querySelector('select[name="heure_arrivee"]').value;
    const heureDepart = document.querySelector('select[name="heure_depart"]').value;
    const prixInput = document.querySelector('input[name="prix"]');
    
    if (!placeTypeSelect.value || !heureArrivee || !heureDepart) {
        if (prixInput) prixInput.value = '0.00';
        return;
    }

    const tarifHoraire = TARIFS_BD[placeTypeSelect.value] || 2.50;

    const [heureA, minA] = heureArrivee.split(':').map(Number);
    const [heureD, minD] = heureDepart.split(':').map(Number);
    
    const minutesArrivee = heureA * 60 + minA;
    const minutesDepart = heureD * 60 + minD;
    
    if (minutesDepart <= minutesArrivee) {
        if (prixInput) prixInput.value = '0.00';
        return;
    }
    
    const dureeMinutes = minutesDepart - minutesArrivee;
    const dureeHeures = dureeMinutes / 60;
    const prix = dureeHeures * tarifHoraire;
    
    if (prixInput) prixInput.value = prix.toFixed(2);
}

function setupFormValidation() {
    const form = document.getElementById('reservationForm');
    const vehicleSelect = document.getElementById('vehicle_select');
    const placeTypeSelect = document.getElementById('place_type_select');
    
    form.addEventListener('submit', function(e) {
        
        <?php if (!$user_id): ?>
            e.preventDefault();
            alert('Vous devez être connecté pour effectuer une réservation.');
            return;
        <?php endif; ?>
          
        if (vehicleSelect && vehicleSelect.value) {
            const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
            const vehicleType = selectedOption?.getAttribute('data-vehicle-type')?.toLowerCase() || '';
            const selectedPlaceType = placeTypeSelect.value;
            
            let allowedTypes = [];

            if (vehicleType.includes('électrique') || vehicleType.includes('electrique')) {
                allowedTypes = ['4', '1', '3']; 
            }
            
            else if (vehicleType.includes('handicap')) {
                allowedTypes = ['3', '1']; 
            }
            
            else if (vehicleType.includes('voiture')) {
                allowedTypes = ['1']; 
            }
            
            else if (vehicleType.includes('moto')) {
                allowedTypes = ['2']; 
            }
            
            else {
                for (const [type, placeTypes] of Object.entries(VEHICLE_TYPE_MAPPING)) {
                    if (vehicleType.includes(type.toLowerCase())) {
                        allowedTypes = placeTypes;
                        break;
                    }
                }
            }
            
            if (allowedTypes.length > 0 && !allowedTypes.includes(selectedPlaceType)) {
                e.preventDefault();
                alert('❌ Le type de véhicule sélectionné ne correspond pas au type de place choisi !');
                return;
            }
        }

        const placeTypeId = placeTypeSelect.value;
        const dateReservation = document.querySelector('input[name="date_reservation"]').value;
        const heureArrivee = document.querySelector('select[name="heure_arrivee"]').value;
        const heureDepart = document.querySelector('select[name="heure_depart"]').value;
        const prix = document.querySelector('input[name="prix"]').value;
        
        if (!placeTypeId || !dateReservation || !heureArrivee || !heureDepart || !prix || parseFloat(prix) <= 0) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires et vérifier que le prix est correct.');
            return;
        }

        const [heureA, minA] = heureArrivee.split(':').map(Number);
        const [heureD, minD] = heureDepart.split(':').map(Number);
        const minutesArrivee = heureA * 60 + minA;
        const minutesDepart = heureD * 60 + minD;
        
        if (minutesDepart <= minutesArrivee) {
            e.preventDefault();
            alert('L\'heure de départ doit être postérieure à l\'heure d\'arrivée.');
            return;
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    
    const placeTypeSelect = document.querySelector('select[name="place_type_id"]');
    const heureArriveeSelect = document.querySelector('select[name="heure_arrivee"]');
    const heureDepartSelect = document.querySelector('select[name="heure_depart"]');

    if (placeTypeSelect) placeTypeSelect.addEventListener('change', calculatePrice);
    if (heureArriveeSelect) heureArriveeSelect.addEventListener('change', calculatePrice);
    if (heureDepartSelect) heureDepartSelect.addEventListener('change', calculatePrice);

    handleVehicleRestrictions();

    setupFormValidation();
});
</script>

</body>
</html>
