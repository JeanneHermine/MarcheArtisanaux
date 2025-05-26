<?php
session_start();
require_once 'config.php'; // Assurez-vous que le fichier config.php est correctement configuré
if (!isset($_SESSION['artisan_id'])) {
    $_SESSION['error_message'] = "Veuillez vous connecter pour accéder à cette page.";
    header("Location: connexion.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

// Vérification de la soumission du formulaire
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupération des données du formulaire
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $photo_url = $_POST['photo_url'];
        $artisan_id = $_SESSION['artisan_id'];

        // Préparation de la requête d'insertion
        $stmt = $pdo->prepare("INSERT INTO catalogues (titre, description, photo_url, id_artisan) VALUES (?, ?, ?, ?)");
        
        // Exécution de la requête
        $stmt->execute([$titre, $description, $photo_url, $artisan_id]);

        echo "Catalogue ajouté avec succès !";
    }
} catch (PDOException $e) {
    echo "Erreur lors de l'ajout du catalogue : " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un catalogue</title>
</head>
<body>
  <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['email']); ?></h2>
  <h3>Ajouter un nouveau catalogue</h3>

  <form method="POST" action="">
    <label for="titre">Titre :</label><br>
    <input type="text" name="titre" required><br><br>

    <label for="description">Description :</label><br>
    <textarea name="description" required></textarea><br><br>

    <label for="photo_url">URL de la photo :</label><br>
    <input type="url" name="photo_url" required><br><br>

    <input type="submit" value="Ajouter le catalogue">
  </form>

  <!-- <br><a href="deconnexion.php">Se déconnecter</a> -->
</body>
</html>
