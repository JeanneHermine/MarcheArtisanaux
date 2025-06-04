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

    $stmt = $pdo->prepare("SELECT * FROM catalogues WHERE id_catalogue = :id_catalogue");
    $stmt->execute(['id_catalogue' => $id_catalogue]);
    $catalogue = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$catalogue) {
        echo "Catalogue introuvable.";
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id_catalogue = :id_catalogue AND statut = 'disponible'");
    $stmt->execute(['id_catalogue' => $id_catalogue]);
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
    <?php if (empty($produits)): ?>
      <p>Aucun produit disponible.</p>
    <?php else: ?>
      <?php foreach ($produits as $prod): ?>
        <div class="produit" onclick='afficherProduit(<?= json_encode($prod) ?>)'>
          <img src="<?= htmlspecialchars($prod['photo_url']) ?>" alt="">
          <h4><?= htmlspecialchars($prod['nom_produit']) ?></h4>
          <p><strong><?= number_format($prod['prix'], 2, ',', ' ') ?> FCFA</strong></p>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div id="details">
        <div id="details-right">
            <img id="photo-produit" src="" alt="Photo du produit"><br><br>
            <h3 id="titre-produit"></h3>
            <p id="description-produit"></p>
            <p><strong id="prix-produit"></strong></p>
        </div>
      <div id="details-left">
        <form id="form-panier" onsubmit="ajouterAuPanier(event)">
          <input type="hidden" name="id_produit" id="id_produit">
          <label>Quantité :</label><br>
          <input type="number" name="quantite" id="quantite" value="1" min="1" required><br><br>
          <button type="submit">Ajouter au panier</button>
        </form>
      </div>
      <div id="avis-section" style="margin-top: 20px;">
      <h4>Avis des clients :</h4>
      <div id="liste-avis">Chargement des avis...</div>
    </div>


    </div>
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

    function afficherProduit(produit) {
        document.getElementById('details').style.display = 'flex';
        document.getElementById('id_produit').value = produit.id_produit;
        document.getElementById('titre-produit').innerText = produit.nom_produit;
        document.getElementById('description-produit').innerText = produit.description;
        document.getElementById('prix-produit').innerText = parseFloat(produit.prix).toFixed(2) + ' FCFA';
        document.getElementById('photo-produit').src = produit.photo_url;

        const quantiteInput = document.getElementById('quantite');
        quantiteInput.value = 1;
        quantiteInput.max = produit.stock;

        // Charger les avis
        fetch(`get_avis.php?id_produit=${produit.id_produit}`)
            .then(res => res.json())
            .then(data => {
                const avisContainer = document.getElementById('liste-avis');
                if (data.length === 0) {
                    avisContainer.innerHTML = "<p>Aucun avis pour ce produit.</p>";
                } else {
                    avisContainer.innerHTML = data.map(avis => `
                        <div style="margin-bottom:10px; border-bottom: 1px solid #ccc; padding-bottom: 10px;">
                            <strong>${avis.nom} ${avis.prenom}</strong> 
                            <span style="color: #f39c12;">${"★".repeat(avis.note)}${"☆".repeat(5 - avis.note)}</span>
                            <p>${avis.commentaire}</p>
                            <small>${avis.date_avis}</small>
                        </div>
                    `).join('');
                }
            }).catch(() => {
                document.getElementById('liste-avis').innerText = "Erreur lors du chargement des avis.";
            });
    }


    function ajouterAuPanier(event) {
      event.preventDefault();
      const id = document.getElementById('id_produit').value;
      const quantite = parseInt(document.getElementById('quantite').value);
      const titre = document.getElementById('titre-produit').innerText;
      const prix = parseFloat(document.getElementById('prix-produit').innerText.replace(' FCFA', ''));

      const existant = panier.find(p => p.id === id);
      if (existant) {
        existant.quantite = quantite; 
      } else {
        panier.push({ id, titre, prix, quantite });
      }

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
       

       // Afficher ou masquer le bouton en fonction du contenu du panier
       if (panier.length > 0) {
           validerCommandeButton.style.display = 'block';
       } else {
           validerCommandeButton.style.display = 'none';
       }
   }

    function retirerDuPanier(index) {
      panier.splice(index, 1);
      afficherPanier();
    }
    function togglePanier() {
    const aside = document.getElementById('panier');
    aside.classList.toggle('ouvert');
    }


    document.getElementById('form-commande').addEventListener('submit', function() {
        document.getElementById('input-panier').value = JSON.stringify(panier);
    });

  </script>
</body>
</html>
