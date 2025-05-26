<?php
session_start();
require_once 'config.php'; 

if (!isset($_GET['id_catalogue'])) {
    echo "Catalogue non spécifié.";
    exit();
}

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id_catalogue = $_GET['id_catalogue'];

    // Récupérer le catalogue
    $stmt = $pdo->prepare("SELECT * FROM catalogues WHERE id_catalogue = :id_catalogue");
    $stmt->bindParam(':id_catalogue', $id_catalogue);
    $stmt->execute();
    $catalogue = $stmt->fetch(PDO::FETCH_ASSOC);

    // Produits associés
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id_catalogue = :id_catalogue AND statut = 'disponible'");
    $stmt->bindParam(':id_catalogue', $id_catalogue);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Produits</title>
    <style>
        .produit {
            display: inline-block;
            width: 220px;
            margin: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }

        .produit img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <h2>Catalogue : <?= htmlspecialchars($catalogue['titre']) ?></h2>

    <?php if (empty($produits)): ?>
        <p>Aucun produit disponible pour ce catalogue.</p>
    <?php else: ?>
        <?php foreach ($produits as $prod): ?>
            <div class="produit">
                <img src="<?= htmlspecialchars($prod['photo_url']) ?>" alt="Produit">
                <h4><?= htmlspecialchars($prod['nom_produit']) ?></h4>
                <p><?= htmlspecialchars($prod['description']) ?></p>
                <p><strong><?= number_format($prod['prix'], 2, ',', ' ') ?> €</strong></p>
                <form action="ajouter_panier.php" method="POST">
                    <input type="hidden" name="id_produit" value="<?= $prod['id_produit'] ?>">
                    <input type="number" name="quantite" value="1" min="1" max="<?= $prod['stock'] ?>">
                    <button type="submit">Ajouter au panier</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <br><a href="./boutique.php">← Retour à la boutique</a>
</body>
</html>
