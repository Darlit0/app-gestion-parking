<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container position-relative">
  </div>
  <div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
      <h2 class="text-center mb-4">Créer un compte</h2>
      <form action="/controller/AuthController.php?action=register" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">Adresse e-mail</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Mot de passe</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-success">S'inscrire</button>
        </div>
        <p class="text-center mb-0">Déjà un compte ? <a href="/gestion-parking/view/login.php">Connexion</a></p>
        <p class="text-center mb-0">
        <a href="../index.php">Annuler</a>
        </p>
      </form>
    </div>
  </div>
</body>
</html>
