<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Page administrateur</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include '../_partials/navbar.html'; ?>
<div class="container my-5">
  <h2 class="text-center mb-4">Espace Administrateur</h2>

  <form action="../controller/admin.php" method="POST" class="card p-4 shadow mb-5">
    <h5>Modifier les paramètres du parking</h5>
    <div class="mb-3">
      <label class="form-label">Nombre total de places pour voitures</label>
      <input type="number" class="form-control" name="total_places_car" value="50" required>
      <label class="form-label">Nombre total de places pour motos</label>
      <input type="number" class="form-control" name="total_places_moto" value="10" required>
      <label class="form-label">Nombre total de places pour personne en situation d'handicap</label>
      <input type="number" class="form-control" name="total_places_handicap" value="20" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Prix par heure (€)</label>
      <input type="number" step="0.5" class="form-control" name="price_per_hour" value="3" required>
    </div>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
  </form>

  <div class="card p-4 shadow">
    <h5>Utilisateurs enregistrés</h5>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Email</th>
          <th>Type de place favoris</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Jean Dupont</td>
          <td>jean@example.com</td>
          <td>Voiture</td>
        </tr>
        <tr>
          <td>Marie Curie</td>
          <td>marie@example.com</td>
          <td>Moto</td>
        </tr>
        <tr>
          <td>Albert Einstein</td>
          <td>albert@example.com</td>
          <td>Voiture(handicap)</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
