<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Gestion Parking</title>    <link href="https:
    <link rel="stylesheet" href="https:
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/components.css">
    <link rel="stylesheet" href="../css/auth.css">
    <link rel="stylesheet" href="../css/dark-mode.css">
</head>
<body>

<?php

if (session_status() === PHP_SESSION_NONE) session_start();
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">
                        <i class="fa-solid fa-user-plus text-primary me-2"></i>
                        Inscription
                    </h2>
                    
                    <form method="POST" action="controller/register.php">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fa-solid fa-user me-1"></i>Prénom
                            </label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="family_name" class="form-label">
                                <i class="fa-solid fa-user me-1"></i>Nom de famille
                            </label>
                            <input type="text" class="form-control" id="family_name" name="family_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fa-solid fa-envelope me-1"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="fa-solid fa-phone me-1"></i>Téléphone (optionnel)
                            </label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fa-solid fa-lock me-1"></i>Mot de passe
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                            <div class="form-text">Minimum 6 caractères</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">
                                <i class="fa-solid fa-lock me-1"></i>Confirmer le mot de passe
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fa-solid fa-user-plus me-2"></i>S'inscrire
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <a href="../index.php?page=login" class="text-decoration-none">
                            <i class="fa-solid fa-sign-in-alt me-1"></i>Déjà un compte ? Se connecter
                        </a>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="../index.php" class="text-muted text-decoration-none">
                            <i class="fa-solid fa-home me-1"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https:
<script>

document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Les mots de passe ne correspondent pas');
        return;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Le mot de passe doit contenir au moins 6 caractères');
        return;
    }
});
</script>

</body>
</html>
