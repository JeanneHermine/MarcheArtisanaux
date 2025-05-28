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
    <link rel="stylesheet" href="boutique.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">🛍️ BEN'ART</div>
        <div class="links">
             <?php if (isset($_SESSION['email'])): ?>
                <a href="profil.php">Profil</a>
                <a href="deconnexion.php">Déconnexion</a>
             <?php else: ?>
                 <a href="connexion.html">Se connecter</a>
                 <a href="inscription.html">S'inscrire</a>
             <?php endif; ?>
        </div>
    </div>


    <!-- 🔍 Barre de recherche -->
    <input type="text" id="searchInput" placeholder="Rechercher un catalogue..." style="width: 100%; padding: 10px; margin: 20px 0; border-radius: 8px; border: 1px solid #ccc; font-size: 16px;">

    <!-- 📦 Catalogue affichage -->
    <?php foreach ($catalogues as $cat): ?>
        <div class="catalogue">
            <a href="voir_produits.php?id_catalogue=<?= $cat['id_catalogue'] ?>">
                <img src="<?= htmlspecialchars($cat['photo_url']) ?>" alt="Image catalogue">
                <h4><?= htmlspecialchars($cat['titre']) ?></h4>
                <p><small>Par <?= htmlspecialchars($cat['prenom'] . ' ' . $cat['nom']) ?></small></p>
            </a>
        </div>
    <?php endforeach; ?>

    <!-- 🔍 Script de filtrage -->
    <script>
        const searchInput = document.getElementById('searchInput');
        const catalogues = document.querySelectorAll('.catalogue');

        searchInput.addEventListener('input', () => {
            const valeur = searchInput.value.toLowerCase();
            catalogues.forEach(catalogue => {
                const texte = catalogue.textContent.toLowerCase();
                catalogue.style.display = texte.includes(valeur) ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>
