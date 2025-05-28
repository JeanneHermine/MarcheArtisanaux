<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription Artisan</title>
  <link rel="stylesheet" href="InscripConnexArtisan.css" />
</head>
<body>

<div class="form-section">
  <h2>Inscription Artisan</h2>
  <?php if (isset($_SESSION['message_artisan'])): ?>
    <p class="<?= $_SESSION['type_artisan'] ?>"><?= $_SESSION['message_artisan']; unset($_SESSION['message_artisan'], $_SESSION['type_artisan']); ?></p>
  <?php endif; ?>
  <form method="POST" action="traitement_inscription_artisan.php">
    <label>Nom :</label>
    <input type="text" name="nom" required>
    <label>Prénom :</label>
    <input type="text" name="prenom" required>
    <label>Email :</label>
    <input type="email" name="email" required>
    <label>Mot de passe :</label>
    <input type="password" name="mot_de_passe" required>
    <label>Confirmer le mot de passe :</label>
    <input type="password" name="confirmer_mot_de_passe" required>
    <label>Numéro de téléphone :</label>
    <input type="text" name="telephone" required>
    <label>Ville :</label>  
    <input type="text" name="ville" required>
    <input type="submit" value="S'inscrire comme artisan">
  </form>
</div>

</body>
</html>
