<?php
session_start();
require_once 'config.php'; 

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("
        SELECT c.*, a.nom, a.prenom
        FROM catalogues c
        JOIN artisans a ON c.id_artisan = a.id_artisan
        ORDER BY c.date_creation DESC
    ");
    $catalogues = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Boutique artisanale</title>
    <style>
        .catalogue {
            display: inline-block;
            margin: 10px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px;
            width: 220px;
        }

        .catalogue img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h2>🛍️ Boutique BEN'ART</h2>
    <!-- boutons pour se connecter ou s'inscrire et profil si la personne est connectée -->
    <?php if (isset($_SESSION['email'])): ?>
        <a href="profil.php">Profil</a>
    <?php else: ?>
        <p>
            <a href="connexion.php">Se connecter</a> |
            <a href="inscription.php">S'inscrire</a>
        </p>
    <?php endif; ?>

    <?php foreach ($catalogues as $cat): ?>
        <div class="catalogue">
            <a href="voir_produits.php?id_catalogue=<?= $cat['id_catalogue'] ?>">
                <img src="<?= htmlspecialchars($cat['photo_url']) ?>" alt="Image catalogue">
                <h4><?= htmlspecialchars($cat['titre']) ?></h4>
                <p><small>Par <?= htmlspecialchars($cat['prenom'] . ' ' . $cat['nom']) ?></small></p>
            </a>
        </div>
    <?php endforeach; ?>
</body>
</html>
