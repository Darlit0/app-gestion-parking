<?php

?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-lg">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">
                    <i class="fa-solid fa-right-to-bracket text-primary me-2"></i>
                    Connexion
                </h2>
                  <form method="POST" action="controller/auth.php">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fa-solid fa-envelope me-1"></i>Adresse e-mail
                        </label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="votre@email.com" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fa-solid fa-lock me-1"></i>Mot de passe
                        </label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Votre mot de passe" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>Se connecter
                    </button>
                    
                    <div class="text-center">
                        <p class="mb-0">Pas encore de compte ? 
                            <a href="index.php?page=register" class="text-primary text-decoration-none">
                                <i class="fa-solid fa-user-plus me-1"></i>S'inscrire
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
