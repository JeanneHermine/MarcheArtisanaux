<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion Client</title>
  <link rel="stylesheet" href="../assets/css/InscripConnexClient.css"/>
  <link rel="icon" href="../assets/img/logo.jpeg" type="image/x-icon">
</head>
<body>

<div class="form-section">
  <h2>Connexion Client</h2>
  <?php if (isset($_SESSION['error_client'])): ?>
    <p class="error"><?= $_SESSION['error_client']; unset($_SESSION['error_client']); ?></p>
  <?php endif; ?>
  <form method="POST" action="../../back/traitement_connexion_client.php">
      <label>Email ou numéro de téléphone :</label>
      <input type="text" name="identifiant" required>

      <label>Mot de passe :</label>
      <input type="password" name="mot_de_passe" required>

      <input type="submit" value="Se connecter en tant que client">
  </form>

</div>

</body>
</html>
