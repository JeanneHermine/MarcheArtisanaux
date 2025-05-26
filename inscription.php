<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      overflow-x: hidden;
      margin: 0;
      padding: 0;
    }

    .container {
      display: flex;
      transition: transform 0.5s ease-in-out;
      width: 200%;
    }

    .form-section {
      width: 50%;
      padding: 40px;
      box-sizing: border-box;
    }

    .tabs {
      display: flex;
      justify-content: center;
      margin-top: 20px;
    }

    .tabs button {
      margin: 0 10px;
      padding: 10px 20px;
      cursor: pointer;
      background-color: #eee;
      border: none;
      border-radius: 5px;
      font-weight: bold;
    }

    .active-tab {
      background-color: #ccc;
    }

    form {
      max-width: 400px;
      margin: auto;
    }

    label, input {
      display: block;
      width: 100%;
      margin-bottom: 10px;
    }

    input[type="submit"] {
      width: auto;
      background-color: #333;
      color: white;
      border: none;
      padding: 10px;
      cursor: pointer;
    }

    h2 {
      text-align: center;
    }

    .success, .error {
      text-align: center;
      font-weight: bold;
    }

    .success {
      color: green;
    }

    .error {
      color: red;
    }
  </style>
</head>
<body>

<div class="tabs">
  <button id="btn-client" class="active-tab">Je suis client</button>
  <button id="btn-artisan">Je suis artisan</button>
</div>

<div class="container" id="formContainer">
  <!-- Formulaire client -->
  <div class="form-section">
    <h2>Inscription Client</h2>
    <?php if (isset($_SESSION['message_client'])): ?>
      <p class="<?= $_SESSION['type_client'] ?>"><?= $_SESSION['message_client']; unset($_SESSION['message_client'], $_SESSION['type_client']); ?></p>
    <?php endif; ?>
    <form method="POST" action="traitement_inscription_client.php">
      <label>Nom :</label>
      <input type="text" name="nom" required>

      <label>Prénom :</label>
      <input type="text" name="prenom" required>

      <label>Email :</label>
      <input type="email" name="email" required>

      <label>Mot de passe :</label>
      <input type="password" name="mot_de_passe" required>

      <label>Adresse :</label>
      <input type="text" name="adresse" required>

      <label>Téléphone :</label>
      <input type="text" name="telephone" required>

      <input type="submit" value="S'inscrire comme client">
    </form>

  </div>

  <!-- Formulaire artisan -->
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
      <input type="submit" value="S'inscrire comme artisan">
    </form>
  </div>
</div>

<script>
  const container = document.getElementById('formContainer');
  const btnClient = document.getElementById('btn-client');
  const btnArtisan = document.getElementById('btn-artisan');

  btnClient.addEventListener('click', () => {
    container.style.transform = 'translateX(0%)';
    btnClient.classList.add('active-tab');
    btnArtisan.classList.remove('active-tab');
  });

  btnArtisan.addEventListener('click', () => {
    container.style.transform = 'translateX(-50%)';
    btnClient.classList.remove('active-tab');
    btnArtisan.classList.add('active-tab');
  });
</script>

</body>
</html>
