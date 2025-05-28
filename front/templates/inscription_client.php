<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription Client</title>
  <link rel="stylesheet" href="../assets/css/InscripConnexClient.css" />
</head>
<body>

<div class="form-section">
  <h2>Inscription Client</h2>
  <?php if (isset($_SESSION['message_client'])): ?>
    <p class="<?= $_SESSION['type_client'] ?>"><?= $_SESSION['message_client']; unset($_SESSION['message_client'], $_SESSION['type_client']); ?></p>
  <?php endif; ?>
  <form method="POST" action="../../back/traitement_inscription_client.php">
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

    <label>Adresse :</label>
    <input type="text" name="adresse" required>

    <label>Téléphone :</label>
    <input type="text" name="telephone" required>

    <input type="submit" value="S'inscrire comme client">
  </form>
</div>

</body>
</html>
