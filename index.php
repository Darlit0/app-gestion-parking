<?php
session_start();

// Récupération de la page demandée
$page = $_GET['page'] ?? 'parking';

// Liste des pages autorisées
$allowed_pages = ['parking', 'account', 'admin', 'login', 'register', 'payement'];

// Vérification de la page demandée
if (!in_array($page, $allowed_pages)) {
    $page = 'parking';
}

// Vérification des permissions pour certaines pages
if ($page === 'account' && !isset($_SESSION['user_id'])) {
    $page = 'login';
}

if ($page === 'admin') {
    if (!isset($_SESSION['user_id'])) {
        $page = 'login';
    } else {
        // Vérifier si l'utilisateur est admin
        require_once 'config/database.php';
        $stmt = $pdo->prepare("SELECT admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if (!$user || !$user['admin']) {
            $page = 'parking';
        }
    }
}
?>
<!doctype html>
<html lang="fr" data-bs-theme="auto">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Parking - <?php echo ucfirst($page); ?></title>
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="css/base.css" as="style">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php 
    // Déterminer les fichiers CSS nécessaires selon la page
    $css_files = ['css/base.css']; // Base toujours nécessaire
    
    switch($page) {
        case 'parking':
            $css_files = array_merge($css_files, ['css/navbar.css', 'css/components.css', 'css/parking.css', 'css/dark-mode.css']);
            break;
        case 'admin':
            $css_files = array_merge($css_files, ['css/navbar.css', 'css/components.css', 'css/admin.css', 'css/dark-mode.css']);
            break;
        case 'account':
            $css_files = array_merge($css_files, ['css/navbar.css', 'css/components.css', 'css/account.css', 'css/dark-mode.css']);
            break;
        case 'payement':
            $css_files = array_merge($css_files, ['css/navbar.css', 'css/components.css', 'css/payment.css', 'css/dark-mode.css']);
            break;
        case 'login':
        case 'register':
            $css_files = array_merge($css_files, ['css/navbar.css', 'css/components.css', 'css/auth.css', 'css/dark-mode.css']);
            break;
        default:
            $css_files = array_merge($css_files, ['css/navbar.css', 'css/components.css', 'css/dark-mode.css']);
    }
    
    // Inclure les fichiers CSS
    foreach($css_files as $css_file) {
        echo "    <link rel=\"stylesheet\" href=\"$css_file\">\n";
    }
    ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  </head>
  <body style="background-color: var(--bs-primary-bg-subtle);">
    <div class="bg-circles">
      <div class="bg-circle blue1"></div>
      <div class="bg-circle blue2"></div>
    </div>
    
    <?php include '_partials/navbar.html'; ?>
    
    <div class="container my-5">
      <!-- Message de déconnexion -->
      <?php if (isset($_GET['logout']) && $_GET['logout'] === 'success'): ?>
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-info-circle"></i> Vous avez été déconnecté avec succès.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php endif; ?>
      
      <!-- Messages de succès/erreur -->
      <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['success']); endif; ?>
      
      <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-exclamation-circle"></i> <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['error']); endif; ?>
      
      <div id="main-content">
        <?php 
        // Utilisation des contrôleurs pour gérer les pages
        switch($page) {
            case 'parking':
                require_once 'config/database.php';
                require_once 'controller/parking.php';
                $controller = new ParkingController($pdo);
                $controller->show();
                break;
                
            case 'account':
                require_once 'config/database.php';
                require_once 'controller/account.php';
                $controller = new AccountController($pdo);
                $controller->show();
                break;
                
            case 'admin':
                require_once 'config/database.php';
                require_once 'controller/admin.php';
                $controller = new AdminController($pdo);
                $controller->show();
                break;
                
            case 'login':
                include 'view/login.php';
                break;
                
            case 'register':
                include 'view/register.php';
                break;
                
            case 'payement':
                require_once 'config/database.php';
                require_once 'controller/payment.php';
                $controller = new PaymentController($pdo);
                $controller->show();
                break;
                
            default:
                // Par défaut, afficher la page parking
                require_once 'config/database.php';
                require_once 'controller/parking.php';
                $controller = new ParkingController($pdo);
                $controller->show();
                break;
        }
        ?>
      </div>
    </div>

    <!-- Script pour le dark mode uniquement -->
    <script>
      // Dark mode toggle
      const darkModeToggle = document.getElementById('darkModeToggle');
      const darkModeIcon = document.getElementById('darkModeIcon');
      
      function setTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
        if (theme === 'dark') {
          darkModeIcon.className = 'fa-solid fa-sun';
        } else {
          darkModeIcon.className = 'fa-solid fa-moon';
        }
        localStorage.setItem('theme', theme);
      }
      
      // Récupération du thème sauvegardé ou préférence système
      const savedTheme = localStorage.getItem('theme');
      const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
      const theme = savedTheme || systemTheme;
      setTheme(theme);
      
      // Gestionnaire du bouton
      if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
          const currentTheme = document.documentElement.getAttribute('data-bs-theme');
          const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
          setTheme(newTheme);
        });
      }
    </script>
  </body>
</html>