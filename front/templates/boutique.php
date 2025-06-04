<?php
session_start();
require_once '../../back/config.php'; 

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
    <link rel="stylesheet" href="../assets/css/boutique.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">🛍️ BEN'ART</div>
        <div class="links">
             <?php if (isset($_SESSION['artisan_id'])): ?>
                <a href="./profil/profil.php">Profil</a>
                <a href="./catalogue_art.php">Mes catalogues</a>
                <a href="./about.html">À propos</a>
                <a href="../../back/deconnexion.php">Déconnexion</a>
              <?php elseif (isset($_SESSION['client_id'])): ?>
                <a href="./profil/profil.php">Mon profil</a>
                <a href="./about.html">À propos</a>
                <a href="../../back/deconnexion.php">Déconnexion</a>
             <?php else: ?>
                 <a href="./connexion.html">Se connecter</a>
                 <a href="./inscription.html">S'inscrire</a>
                 <a href="./about.html">À propos</a>
             <?php endif; ?>
        </div>
    </div>

    <input type="text" id="searchInput" placeholder="Rechercher un catalogue...">

    <div class="catalogue-grid">
    <?php foreach ($catalogues as $cat): ?>
        <div class="catalogue">
            <a href="./voir_produits.php?id_catalogue=<?= $cat['id_catalogue'] ?>">
                <img src="<?= htmlspecialchars($cat['photo_url']) ?>" alt="Image du catalogue">
                <h4><?= htmlspecialchars($cat['titre']) ?></h4>
            </a>
            <p>Par <strong><?= htmlspecialchars($cat['prenom'] . ' ' . $cat['nom']) ?></strong></p>
            <a href="./voir_produits.php?id_catalogue=<?= $cat['id_catalogue'] ?>" class="btn">Voir les produits</a>
        </div>
    <?php endforeach; ?>
    </div>
    <div class="site-info">
    <div class="info-bloc">
        <img src="https://images.pexels.com/photos/28100861/pexels-photo-28100861/free-photo-of-atelier-velo.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Atelier artisan">
        <div class="info-text">
            <h2>Notre mission</h2>
            <p>Chez BEN'ART, nous mettons en lumière le talent des artisans locaux. Notre plateforme permet à chacun de partager ses créations et de toucher un public plus large.</p>
        </div>
    </div>

    <div class="info-bloc reverse">
        <img src="https://images.pexels.com/photos/11588124/pexels-photo-11588124.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Marché artisanal">
        <div class="info-text">
            <h2>Pourquoi acheter chez nous ?</h2>
            <p>Chaque article est unique, conçu avec passion et savoir-faire. En achetant ici, vous soutenez directement des artistes et artisans indépendants.</p>
        </div>
    </div>

    <div class="info-bloc">
        <img src="https://images.pexels.com/photos/13740587/pexels-photo-13740587.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Outils artisanaux">
        <div class="info-text">
            <h2>Des créations uniques</h2>
            <p>Du bois, du cuir, du tissu, du métal... chaque matière est transformée à la main pour vous proposer des produits originaux et durables.</p>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-column">
            <h4>BEN'ART</h4>
            <p>La plateforme dédiée aux artisans passionnés et à ceux qui aiment le fait-main.</p>
        </div>
        <div class="footer-column">
            <h4>Navigation</h4>
            <ul>
                <li><a href="#">Accueil</a></li>
                <li><a href="./about.html">À propos</a></li>
                <li><a href="./connexion.html">Connexion</a></li>
                <li><a href="./inscription.html">Inscription</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>Suivez-nous</h4>
            <div class="socials">
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook"></a>
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" alt="Instagram"></a>
                <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/2111/2111463.png" alt="TikTok"></a>
            </div>
        </div>
    </div>
    <p class="footer-bottom">&copy; <?= date('Y') ?> BEN'ART - Tous droits réservés.</p>
</footer>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("fade-in");
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.catalogue, .info-bloc').forEach(el => {
                el.classList.add('fade-init');
                observer.observe(el);
            });
        });
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
