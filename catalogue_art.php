<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['artisan_id'])) {
    $_SESSION['error_message'] = "Veuillez vous connecter pour accéder à cette page.";
    header("Location: connexion.php");
    exit();
}

$artisan_id = $_SESSION['artisan_id'];

// Ajouter un catalogue
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_catalogue'])) {
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $photo_url = $_POST['photo_url'];

        $stmt = $pdo->prepare("INSERT INTO catalogues (titre, description, photo_url, id_artisan, date_creation) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$titre, $description, $photo_url, $artisan_id]);

        header("Location: catalogue_art.php");
        exit();
    }

    // Supprimer un catalogue
    if (isset($_GET['supprimer'])) {
        $id_catalogue = (int)$_GET['supprimer'];

        $stmt = $pdo->prepare("DELETE FROM catalogues WHERE id_catalogue = ? AND id_artisan = ?");
        $stmt->execute([$id_catalogue, $artisan_id]);

        header("Location: catalogue_art.php");
        exit();
    }

    // Récupération des catalogues de l’artisan
    $stmt = $pdo->prepare("SELECT * FROM catalogues WHERE id_artisan = ?");
    $stmt->execute([$artisan_id]);
    $catalogues = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des catalogues</title>
</head>
<body>
  <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['email']); ?></h2>
  <!-- accueil -->
  <p><a href="boutique.php">Accueil</a></p>
  <p><a href="deconnexion.php">Déconnexion</a></p>
  <!-- page produits -->
  <hr>
  <p><a href="ajout_produit.php">Ajouter un produit</a></p>

  <h3>Ajouter un nouveau catalogue</h3>
  <form method="POST">
    <input type="hidden" name="ajouter_catalogue" value="1">
    <label>Titre :</label><br>
    <input type="text" name="titre" required><br><br>

    <label>Description :</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>URL de la photo :</label><br>
    <input type="url" name="photo_url" required><br><br>

    <input type="submit" value="Ajouter le catalogue">
  </form>

  <hr>

  <h3>Vos catalogues</h3>

  <?php if ($catalogues): ?>
    <?php foreach ($catalogues as $catalogue): ?>
      <div style="border: 1px solid #ccc; margin: 10px 0; padding: 10px;">
        <h4><?= htmlspecialchars($catalogue['titre']) ?></h4>
        <p><?= nl2br(htmlspecialchars($catalogue['description'])) ?></p>
        <img src="<?= htmlspecialchars($catalogue['photo_url']) ?>" alt="Photo" width="200"><br>
        <p>Créé le : <?= htmlspecialchars($catalogue['date_creation']) ?></p>

        <a href="modifier_catalogue.php?id=<?= $catalogue['id_catalogue'] ?>">✏️ Modifier</a> |
        <a href="?supprimer=<?= $catalogue['id_catalogue'] ?>" onclick="return confirm('Supprimer ce catalogue ?')">🗑️ Supprimer</a>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Vous n'avez encore aucun catalogue.</p>
  <?php endif; ?>
</body>
</html>
