<?php

?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-lg">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">
                    <i class="fa-solid fa-user-plus text-primary me-2"></i>
                    Inscription
                </h2>
                    
                    <form method="POST" action="controller/auth.php">
                        <input type="hidden" name="action" value="register">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fa-solid fa-user me-1"></i>Prénom
                            </label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Votre prénom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="family_name" class="form-label">
                                <i class="fa-solid fa-user me-1"></i>Nom de famille
                            </label>
                            <input type="text" class="form-control" id="family_name" name="family_name" placeholder="Votre nom" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fa-solid fa-envelope me-1"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="votre@email.com" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">
                                <i class="fa-solid fa-phone me-1"></i>Téléphone (optionnel)
                            </label>
                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="06 12 34 56 78">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fa-solid fa-lock me-1"></i>Mot de passe
                            </label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimum 6 caractères" required>
                        </div>                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">
                                <i class="fa-solid fa-lock me-1"></i>Confirmer le mot de passe
                            </label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Répétez le mot de passe" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fa-solid fa-user-plus me-2"></i>S'inscrire
                        </button>
                        
                        <div class="text-center">
                            <p class="mb-0">Déjà un compte ? 
                                <a href="index.php?page=login" class="text-primary text-decoration-none">
                                    <i class="fa-solid fa-right-to-bracket me-1"></i>Se connecter
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>