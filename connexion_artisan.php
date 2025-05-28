<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion Artisan</title>
  <link rel="stylesheet" href="InscripConnexArtisan.css" />
</head>
<body>

<div class="form-section">
  <h2>Connexion Artisan</h2>
  <?php if (isset($_SESSION['error_artisan'])): ?>
    <p class="error"><?= $_SESSION['error_artisan']; unset($_SESSION['error_artisan']); ?></p>
  <?php endif; ?>
  <form method="POST" action="traitement_connexion_artisan.php">
    <label>Email :</label>
    <input type="email" name="email" required>
    <label>Mot de passe :</label>
    <input type="password" name="mot_de_passe" required>
    <input type="submit" value="Se connecter en tant qu'artisan">
  </form>
</div>

</body>
</html>
