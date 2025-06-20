
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion Artisan</title>
  <link rel="stylesheet" href="../assets/css/InscripConnexArtisan.css" />
  <link rel="icon" href="../assets/img/logo.jpeg" type="image/x-icon">
</head>
<body>

<div class="form-section">
  <h2>Connexion Artisan</h2>
  <?php if (isset($_SESSION['error_artisan'])): ?>
    <p class="error"><?= $_SESSION['error_artisan']; unset($_SESSION['error_artisan']); ?></p>
  <?php endif; ?>
  <form method="POST" action="../../back/traitement_connexion_artisan.php">
      <label>Email ou numéro de téléphone :</label>
      <input type="text" name="identifiant" required>

      <label>Mot de passe :</label>
      <input type="password" name="mot_de_passe" required>

      <input type="submit" value="Se connecter en tant qu'artisan">
  </form>

</div>

</body>
</html>
