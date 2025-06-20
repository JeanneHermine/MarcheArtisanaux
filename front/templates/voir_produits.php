<?php
session_start();
require_once '../../back/config.php';

if (!isset($_GET['id_catalogue'])) {
    echo "Catalogue non spécifié.";
    exit();
}

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $id_catalogue = $_GET['id_catalogue'];

    // Récupérer les infos du catalogue
    $stmt = $pdo->prepare("SELECT * FROM catalogues WHERE id_catalogue = :id_catalogue");
    $stmt->execute(['id_catalogue' => $id_catalogue]);
    $catalogue = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$catalogue) {
        echo "Catalogue introuvable.";
        exit();
    }

    // Récupérer les produits du catalogue
    $stmt = $pdo->prepare("
        SELECT p.*, a.numero
        FROM produits p
        JOIN catalogues c ON p.id_catalogue = c.id_catalogue
        JOIN artisans a ON c.id_artisan = a.id_artisan
        WHERE p.id_catalogue = :id_catalogue
          AND a.statut = 'actif'
    ");
    $stmt->execute(['id_catalogue' => $id_catalogue]);

    if ($stmt->rowCount() === 0) {
        echo "Aucun produit disponible dans ce catalogue.";
        exit();
    }

    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Catalogue - <?= htmlspecialchars($catalogue['titre']) ?></title>
  <link rel="stylesheet" href="../../front/assets/css/visuel_produit.css">
</head>
<body>

<div class="produits">
  <h2>Catalogue : <?= htmlspecialchars($catalogue['titre']) ?></h2>
  <a href="javascript:history.back()" class="btn-retour">← Retour</a>

  <?php foreach ($produits as $prod): ?>
    <div class="produit">
      <img src="<?= htmlspecialchars($prod['photo_url']) ?>" alt="">
      <h4><?= htmlspecialchars($prod['nom_produit']) ?></h4>
      <p><strong><?= number_format($prod['prix'], 2, ',', ' ') ?> FCFA</strong></p>
      <p><?= nl2br(htmlspecialchars($prod['description'])) ?></p>
      <form onsubmit='ajouterDirectAuPanier(event, <?= json_encode($prod) ?>)'>
        <label>Quantité :</label>
        <input type="number" name="quantite" value="1" min="1" max="<?= $prod['stock'] ?>" required>
        <button type="submit">Ajouter au panier</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>

<button id="toggle-panier" onclick="togglePanier()">
  🛒<div class="badge" id="panier-count">0</div>
</button>

<aside id="panier">
  <button id="fermer-panier" onclick="togglePanier()">✖</button>
  <h3>Mon panier</h3>
  <div id="liste-panier"></div>
  <div id="total"></div>
  <form method="POST" action="../../back/validation_panier.php" id="form-commande">
    <input type="hidden" name="panier" id="input-panier" value=''>
    <button type="submit" id="valider-commande" style="display: none;">Valider la commande</button>
  </form>
</aside>

<script>
const panier = [];

function ajouterDirectAuPanier(event, produit) {
  event.preventDefault();
  const quantite = parseInt(event.target.quantite.value);

  const existant = panier.find(p => p.id === produit.id_produit);
  if (existant) {
    alert("Ce produit est déjà dans votre panier.");
    return;
  }

  panier.push({
    id: produit.id_produit,
    titre: produit.nom_produit,
    prix: parseFloat(produit.prix),
    quantite: quantite
  });

  afficherPanier();
}

function afficherPanier() {
  const container = document.getElementById('liste-panier');
  const totalContainer = document.getElementById('total');
  const countBadge = document.getElementById('panier-count');
  const validerCommandeButton = document.getElementById('valider-commande');

  container.innerHTML = '';
  let total = 0;
  let totalQuantite = 0;

  panier.forEach((item, index) => {
    const sousTotal = item.prix * item.quantite;
    total += sousTotal;
    totalQuantite += item.quantite;

    container.innerHTML += `
      <div class="panier-item">
        ${item.titre} - ${item.quantite} x ${item.prix.toFixed(2)} FCFA = ${sousTotal.toFixed(2)} FCFA
        <br><button onclick="retirerDuPanier(${index})">Supprimer</button>
      </div>
    `;
  });

  countBadge.innerText = totalQuantite;
  totalContainer.innerText = "Total à payer : " + total.toFixed(2) + " FCFA";
  validerCommandeButton.style.display = panier.length > 0 ? 'block' : 'none';
}

function retirerDuPanier(index) {
  panier.splice(index, 1);
  afficherPanier();
}

function togglePanier() {
  document.getElementById('panier').classList.toggle('ouvert');
}

document.getElementById('form-commande').addEventListener('submit', function () {
  document.getElementById('input-panier').value = JSON.stringify(panier);
});
</script>

</body>
</html>
