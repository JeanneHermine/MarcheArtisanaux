<?php
session_start();
require_once '../../back/config.php';

if (!isset($_SESSION['artisan_id']) || !isset($_GET['id'])) {
    header("Location: ./connexion_artisan.php");
    exit();
}

$id_artisan = $_SESSION['artisan_id'];
$id_produit = (int)$_GET['id'];

// Vérifier que le produit appartient à l'artisan connecté
$stmt = $pdo->prepare("
    SELECT p.*, c.titre AS nom_catalogue 
    FROM produits p
    JOIN catalogues c ON p.id_catalogue = c.id_catalogue
    WHERE p.id_produit = :id_produit AND c.id_artisan = :id_artisan
");
$stmt->execute(['id_produit' => $id_produit, 'id_artisan' => $id_artisan]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    echo "Produit non trouvé ou accès non autorisé.";
    exit();
}

// Mise à jour du produit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $update = $pdo->prepare("
        UPDATE produits 
        SET nom_produit = :nom, description = :desc, prix = :prix, stock = :stock, statut = :statut, photo_url = :photo 
        WHERE id_produit = :id_produit
    ");
    $update->execute([
        'nom' => $_POST['nom_produit'],
        'desc' => $_POST['description'],
        'prix' => $_POST['prix'],
        'stock' => $_POST['stock'],
        'statut' => $_POST['statut'],
        'photo' => $_POST['photo_url'],
        'id_produit' => $id_produit
    ]);

    echo "<p style='color:green;'>Produit mis à jour avec succès !</p>";

}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier le produit</title>
</head>
<body>
  <h2>Modifier le produit de « <?= htmlspecialchars($produit['nom_catalogue']) ?> »</h2>

  <form method="POST">
    <label>Nom du produit :</label><br>
    <input type="text" name="nom_produit" value="<?= htmlspecialchars($produit['nom_produit']) ?>" required><br><br>

    <label>Description :</label><br>
    <textarea name="description" required><?= htmlspecialchars($produit['description']) ?></textarea><br><br>

    <label>Prix (FCFA) :</label><br>
    <input type="number" name="prix" step="0.01" value="<?= htmlspecialchars($produit['prix']) ?>" required><br><br>

    <label>Stock :</label><br>
    <input type="number" name="stock" value="<?= htmlspecialchars($produit['stock']) ?>" required><br><br>

    <label>Statut :</label><br>
    <select name="statut" required>
      <option value="disponible" <?= $produit['statut'] === 'disponible' ? 'selected' : '' ?>>Disponible</option>
      <option value="épuisé" <?= $produit['statut'] === 'épuisé' ? 'selected' : '' ?>>Épuisé</option>
    </select><br><br>

    <label>URL de la photo :</label><br>
    <input type="url" name="photo_url" value="<?= htmlspecialchars($produit['photo_url']) ?>" required><br><br>
    <img src="<?= htmlspecialchars($produit['photo_url']) ?>" alt="Photo actuelle" width="100"><br><br>

    <input type="submit" value="Enregistrer les modifications">
  </form>

  <br><a href="./ajout_produit.php">← Retour à mes produits</a>
</body>
</html>
