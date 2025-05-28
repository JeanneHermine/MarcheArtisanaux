<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion Client</title>
  <link rel="stylesheet" href="InscripConnexClient.css" />
</head>
<body>

<div class="form-section">
  <h2>Connexion Client</h2>
  <?php if (isset($_SESSION['error_client'])): ?>
    <p class="error"><?= $_SESSION['error_client']; unset($_SESSION['error_client']); ?></p>
  <?php endif; ?>
  <form method="POST" action="traitement_connexion_client.php">
    <label>Email :</label>
    <input type="email" name="email" required>
    <label>Mot de passe :</label>
    <input type="password" name="mot_de_passe" required>
    <input type="submit" value="Se connecter en tant que client">
  </form>
</div>

</body>
</html>
