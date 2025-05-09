<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Parking demo</title>
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </head>
  <body>
  
    <?php include '_partials/navbar.html'; ?>
    <?php include("view/parking.php"); ?>

    <a class="btn btn-primary" id="adminBtn" href="/gestion-parking/view/admin.php">Admin</a>

  </body>
</html>