<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Réservation Parking</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
  <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <div class="container my-5">
    <h2 class="text-center mb-4">Réserver une place de parking</h2>
    <form action="../controller/parking.php" method="POST" class="card p-4 shadow">
      <div class="mb-3">
        <label class="form-label">Type de place</label>
        <select class="form-select" name="vehicle_type" required>
          <option value="voiture">Voiture</option>
          <option value="handicap">Voiture (handicap)</option>
          <option value="moto">Moto</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="date" class="form-label">Jour</label>
        <input type="date" class="form-control" id="date" name="date" required>
      </div>
      <div class="mb-3">
        <label for="start_time" class="form-label">Heure de début</label>
        <select class="form-select" name="start_time" required>
          <?php for ($h = 6; $h <= 22; $h++): foreach ([0, 15, 30, 45] as $m): ?>
            <option value="<?= sprintf('%02d:%02d', $h, $m) ?>">
              <?= sprintf('%02d:%02d', $h, $m) ?>
            </option>
          <?php endforeach; endfor; ?>
        </select>
      </div>
      <div class="mb-3">
        <label for="end_time" class="form-label">Heure de fin</label>
        <select class="form-select" name="end_time" required>
          <?php for ($h = 6; $h <= 22; $h++): foreach ([0, 15, 30, 45] as $m): ?>
            <option value="<?= sprintf('%02d:%02d', $h, $m) ?>">
              <?= sprintf('%02d:%02d', $h, $m) ?>
            </option>
          <?php endforeach; endfor; ?>
        </select>
      </div>
      <div class="mb-3">
        <label for="price" class="form-label">Prix estimé (€)</label>
        <input type="text" class="form-control" id="price" name="price" value="3" readonly>
      </div>
      <button type="submit" class="btn btn-primary w-100">Réserver</button>
    </form>
  </div>
</body>
</html>
