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
        WHERE a.statut = 'actif'
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
             <?php if (isset($_SESSION['admin_id'])): ?>
                <a href="./gestion_artisan">Admin</a>
            <?php endif; ?>
        </div>
    </div>

    
    <!-- <div class="catalogue-section">
        <div class="video-background">
          <video autoplay muted loop playsinline>
            <source src="../../front/assets/img/14.mp4" type="video/mp4">
          </video>
        <div class="video-overlay"></div>
    </div> -->
    <section class="hero">
        <div class="hero-text">
            <h1>Bienvenue dans l'univers du fait-main</h1>
            <p>Découvrez des créations artisanales uniques fabriquées avec passion.</p>
            <a href="#catalogues" class="hero-btn">Explorer les catalogues</a>
        </div>
        <div class="hero-image-slider">
            <img class="slider-image active" src="https://images.pexels.com/photos/5450085/pexels-photo-5450085.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Artisan créant un objet">
            <img class="slider-image" src="https://images.pexels.com/photos/27998594/pexels-photo-27998594/free-photo-of-homme-industrie-architecture-afrique.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Objets artisanaux">
            <img class="slider-image" src="https://images.pexels.com/photos/31827078/pexels-photo-31827078.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Atelier d'artisan">
        </div>

    </section>


    <div class="catalogue-content">
           <div class="catalogue-grid">
               <?php if (empty($catalogues)): ?>
                   <p>Aucun catalogue disponible pour le moment.</p>
               <?php endif; ?>
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
        </div>
    </div>

<div class="site-info">
  <div class="info-bloc fade-init" data-aos="fade-right">
    <img src="https://images.pexels.com/photos/28100861/pexels-photo-28100861/free-photo-of-atelier-velo.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Atelier artisan">
    <div class="info-text">
      <h2>Notre mission</h2>
      <p>Chez BEN'ART, nous mettons en lumière le talent des artisans locaux...</p>
    </div>
  </div>

  <div class="info-bloc reverse fade-init" data-aos="fade-left">
    <img src="https://images.pexels.com/photos/11588124/pexels-photo-11588124.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Marché artisanal">
    <div class="info-text">
      <h2>Pourquoi acheter chez nous ?</h2>
      <p>Chaque article est unique, conçu avec passion et savoir-faire...</p>
    </div>
  </div>

  <div class="info-bloc fade-init" data-aos="fade-up">
    <img src="https://images.pexels.com/photos/13740587/pexels-photo-13740587.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Outils artisanaux">
    <div class="info-text">
      <h2>Des créations uniques</h2>
      <p>Du bois, du cuir, du tissu, du métal... chaque matière est transformée...</p>
    </div>
  </div>
</div>
<section class="testimonials">
  <h2>Ils parlent de nous</h2>
  <div class="testimonial-container">
    <div class="testimonial-card">
      <p>“J’ai trouvé le cadeau parfait pour l’anniversaire de ma mère. Merci BEN’ART !”</p>
      <strong>— Awa K., cliente</strong>
    </div>
    <div class="testimonial-card">
      <p>“Grâce à cette plateforme, mes produits atteignent plus de clients.”</p>
      <strong>— Idriss T., artisan</strong>
    </div>
    <div class="testimonial-card">
      <p>“Simple, pratique et authentique. Je recommande à tous les amoureux de l'artisanat.”</p>
      <strong>— Paulin G., client régulier</strong>
    </div>
  </div>
</section>

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
            const images = document.querySelectorAll('.slider-image');
            let current = 0;

            function showNextImage() {
                images[current].classList.remove('active');
                current = (current + 1) % images.length;
                images[current].classList.add('active');
            }

            setInterval(showNextImage, 3000);
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
