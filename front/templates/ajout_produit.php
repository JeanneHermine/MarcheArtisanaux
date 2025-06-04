<?php
session_start();
require_once '../../back/config.php';

if (!isset($_SESSION['artisan_id']) || !isset($_SESSION['identifiant_artisan'])) {
    $_SESSION['error_message'] = "Veuillez vous connecter pour accéder à cette page.";  
    header("Location: ./connexion_artisan.php");
    exit();
}

$id_artisan = $_SESSION['artisan_id'];

// Connexion sécurisée avec PDO
try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les catalogues de l'artisan
    $stmt = $pdo->prepare("SELECT id_catalogue, titre FROM catalogues WHERE id_artisan = :id_artisan");
    $stmt->execute(['id_artisan' => $id_artisan]);
    $catalogues = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ajouter un produit
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ajouter'])) {
        $data = [
            'id_catalogue' => $_POST['id_catalogue'],
            'nom_produit' => $_POST['nom_produit'],
            'description' => $_POST['description'],
            'prix' => $_POST['prix'],
            'stock' => $_POST['stock'],
            'statut' => $_POST['statut'],
            'photo_url' => $_POST['photo_url']
        ];

        $insert = $pdo->prepare("INSERT INTO produits (id_catalogue, nom_produit, description, prix, stock, statut, photo_url)
                                 VALUES (:id_catalogue, :nom_produit, :description, :prix, :stock, :statut, :photo_url)");
        $insert->execute($data);
        echo "<p style='color:green;'>Produit ajouté avec succès !</p>";
    }

    // Supprimer un produit
    if (isset($_GET['delete'])) {
        $id_produit = (int)$_GET['delete'];

        // Vérifie que le produit appartient à un catalogue de l’artisan
        $check = $pdo->prepare("
            SELECT p.id_produit FROM produits p
            JOIN catalogues c ON p.id_catalogue = c.id_catalogue
            WHERE p.id_produit = :id_produit AND c.id_artisan = :id_artisan
        ");
        $check->execute(['id_produit' => $id_produit, 'id_artisan' => $id_artisan]);

        if ($check->fetch()) {
            $delete = $pdo->prepare("DELETE FROM produits WHERE id_produit = ?");
            $delete->execute([$id_produit]);
            echo "<p style='color:red;'>Produit supprimé.</p>";
        }
    }

    // Récupérer tous les produits de l'artisan
    $produitsStmt = $pdo->prepare("
        SELECT p.*, c.titre AS nom_catalogue FROM produits p
        JOIN catalogues c ON p.id_catalogue = c.id_catalogue
        WHERE c.id_artisan = :id_artisan
    ");
    $produitsStmt->execute(['id_artisan' => $id_artisan]);
    $produits = $produitsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gérer mes produits</title>
  <link rel="stylesheet" href="../assets/css/ajout.css">
</head>
<body>
  <h2>Ajouter un produit à un catalogue</h2>

  <form method="POST">
    <input type="hidden" name="ajouter" value="1">
    <label>Catalogue :</label><br>
    <select name="id_catalogue" required>
      <option value="">-- Sélectionner --</option>
      <?php foreach ($catalogues as $cat): ?>
        <option value="<?= $cat['id_catalogue'] ?>"><?= htmlspecialchars($cat['titre']) ?></option>
      <?php endforeach; ?>
    </select><br><br>

    <label>Nom :</label><br>
    <input type="text" name="nom_produit" required><br><br>

    <label>Description :</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Prix (FCFA) :</label><br>
    <input type="number" name="prix" step="0.01" required><br><br>

    <label>Stock :</label><br>
    <input type="number" name="stock" required><br><br>

    <label>Statut :</label><br>
    <select name="statut">
      <option value="disponible">Disponible</option>
      <option value="épuisé">Épuisé</option>
    </select><br><br>

    <label>Photo URL :</label><br>
    <input type="url" name="photo_url" required><br><br>

    <input type="submit" value="Ajouter le produit">
  </form>

  <hr>

  <h2>Mes produits</h2>
  <?php if (count($produits) === 0): ?>
    <p>Aucun produit trouvé.</p>
  <?php else: ?>
    <table border="1" cellpadding="5" cellspacing="0">
      <tr>
        <th>Catalogue</th>
        <th>Nom</th>
        <th>Description</th>
        <th>Prix</th>
        <th>Stock</th>
        <th>Statut</th>
        <th>Photo</th>
        <th>Actions</th>
      </tr>
      <?php foreach ($produits as $prod): ?>
      <tr>
        <td><?= htmlspecialchars($prod['nom_catalogue']) ?></td>
        <td><?= htmlspecialchars($prod['nom_produit']) ?></td>
        <td><?= htmlspecialchars($prod['description']) ?></td>
        <td><?= number_format($prod['prix'], 2) ?> FCFA</td>
        <td><?= $prod['stock'] ?></td>
        <td><?= $prod['statut'] ?></td>
        <td><img src="<?= htmlspecialchars($prod['photo_url']) ?>" alt="Photo" width="60"></td>
        <td>
          <a href="./modifier_produit.php?id=<?= $prod['id_produit'] ?>">Modifier</a> |
          <a href="?delete=<?= $prod['id_produit'] ?>" onclick="return confirm('Supprimer ce produit ?');">Supprimer</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <br><a href="./catalogue_art.php">← Retour à mes catalogues</a>
</body>
</html>
