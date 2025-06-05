<?php
session_start();
require_once '../../back/config.php';

if (!isset($_SESSION['artisan_id'])) {
    $_SESSION['error_message'] = "Veuillez vous connecter pour accéder à cette page.";
    header("Location: ./connexion_artisan.php");
    exit();
}

$artisan_id = $_SESSION['artisan_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de catalogue invalide.";
    exit();
}

$id_catalogue = (int)$_GET['id'];

// Récupérer les données du catalogue
$stmt = $pdo->prepare("SELECT * FROM catalogues WHERE id_catalogue = ? AND id_artisan = ?");
$stmt->execute([$id_catalogue, $artisan_id]);
$catalogue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$catalogue) {
    echo "Catalogue introuvable ou vous n'avez pas les droits.";
    exit();
}

// Mise à jour du catalogue
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $photo_url = $_POST['photo_url'];

    $updateStmt = $pdo->prepare("UPDATE catalogues SET titre = ?, description = ?, photo_url = ? WHERE id_catalogue = ? AND id_artisan = ?");
    $updateStmt->execute([$titre, $description, $photo_url, $id_catalogue, $artisan_id]);

    header("Location: ./catalogue_art.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier un catalogue</title>
  <link rel="stylesheet" href="../assets/css/ajout.css">
  <link rel="icon" href="../assets/img/logo.jpeg" type="image/x-icon">
</head>
<body>
  <h2>Modifier le catalogue</h2>

  <form method="POST">
    <label for="titre">Titre :</label><br>
    <input type="text" name="titre" value="<?= htmlspecialchars($catalogue['titre']) ?>" required><br><br>

    <label for="description">Description :</label><br>
    <textarea name="description" required><?= htmlspecialchars($catalogue['description']) ?></textarea><br><br>

    <label for="photo_url">URL de la photo :</label><br>
    <input type="url" name="photo_url" value="<?= htmlspecialchars($catalogue['photo_url']) ?>" required><br><br>

    <input type="submit" value="Mettre à jour">
    <a href="catalogue_art.php">Annuler</a>
  </form>
</body>
</html>
