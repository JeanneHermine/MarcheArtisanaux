<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion</title>
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

    .error {
      color: red;
      text-align: center;
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

  <!-- Formulaire artisan -->
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
