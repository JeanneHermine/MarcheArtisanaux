<?php
session_start();
require_once 'config.php'; 

// Redirection si non connecté
if (!isset($_SESSION['artisan_id']) || !isset($_SESSION['email'])) {
    header("Location: connexion.php");
    exit();
}


try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l'id de l'artisan via l'email
    $stmt = $pdo->prepare("SELECT id_artisan FROM artisans WHERE email = :email");
    $stmt->bindParam(':email', $_SESSION['email']);
    $stmt->execute();
    $artisan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$artisan) {
        echo "Artisan introuvable.";
        exit();
    }

    $id_artisan = $artisan['id_artisan'];

    // Récupérer les catalogues de l'artisan
    $catalogues = $pdo->prepare("SELECT id_catalogue, titre FROM catalogues WHERE id_artisan = :id_artisan");
    $catalogues->bindParam(':id_artisan', $id_artisan);
    $catalogues->execute();
    $liste_catalogues = $catalogues->fetchAll(PDO::FETCH_ASSOC);

    // Ajouter un produit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_catalogue = $_POST['id_catalogue'];
        $nom_produit = $_POST['nom_produit'];
        $description = $_POST['description'];
        $prix = $_POST['prix'];
        $stock = $_POST['stock'];
        $statut = $_POST['statut'];
        $photo_url = $_POST['photo_url'];

        $insert = $pdo->prepare("INSERT INTO produits (id_catalogue, nom_produit, description, prix, stock, statut, photo_url)
                                 VALUES (:id_catalogue, :nom_produit, :description, :prix, :stock, :statut, :photo_url)");
        $insert->bindParam(':id_catalogue', $id_catalogue);
        $insert->bindParam(':nom_produit', $nom_produit);
        $insert->bindParam(':description', $description);
        $insert->bindParam(':prix', $prix);
        $insert->bindParam(':stock', $stock);
        $insert->bindParam(':statut', $statut);
        $insert->bindParam(':photo_url', $photo_url);
        $insert->execute();

        echo "<p style='color:green;'>Produit ajouté avec succès !</p>";
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un produit</title>
</head>
<body>
  <h2>Ajouter un produit à un catalogue</h2>

  <form method="POST" action="">
    <label for="id_catalogue">Choisir un catalogue :</label><br>
    <select name="id_catalogue" required>
      <option value="">-- Sélectionner --</option>
      <?php foreach ($liste_catalogues as $cat): ?>
        <option value="<?= $cat['id_catalogue'] ?>"><?= htmlspecialchars($cat['titre']) ?></option>
      <?php endforeach; ?>
    </select><br><br>

    <label for="nom_produit">Nom du produit :</label><br>
    <input type="text" name="nom_produit" required><br><br>

    <label for="description">Description :</label><br>
    <textarea name="description" required></textarea><br><br>

    <label for="prix">Prix (€) :</label><br>
    <input type="number" step="0.01" name="prix" required><br><br>

    <label for="stock">Stock :</label><br>
    <input type="number" name="stock" required><br><br>

    <label for="statut">Statut :</label><br>
    <select name="statut" required>
      <option value="disponible">Disponible</option>
      <option value="épuisé">Épuisé</option>
    </select><br><br>

    <label for="photo_url">URL de la photo :</label><br>
    <input type="url" name="photo_url" required><br><br>

    <input type="submit" value="Ajouter le produit">
  </form>

  <br><a href="catalogue_art.php">← Retour à mes catalogues</a>
</body>
</html>
