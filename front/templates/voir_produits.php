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
  <style>
    body { display: flex; font-family: sans-serif; }
    .produits { flex: 1; padding: 20px; }
    .produit { display: inline-block; width: 200px; margin: 10px; border: 1px solid #ccc; border-radius: 8px; padding: 10px; cursor: pointer; }
    .produit img { width: 100%; height: 120px; object-fit: cover; border-radius: 6px; }
    #details { width: 90%; margin: 0 auto; margin-top: 30px; padding: 20px; border-top: 2px solid #ddd; display: none; gap: 40px; flex-wrap: wrap; }
    #details-left, #details-right { flex: 1; min-width: 300px; }
    #details-right img { max-width: 100%; max-height: 250px; object-fit: cover; border-radius: 10px; }
    aside#panier { width: 300px; background: #f7f7f7; border-left: 2px solid #ccc; padding: 20px; }
    aside#panier h3 { margin-top: 0; }
    .panier-item { border-bottom: 1px solid #ddd; padding: 5px 0; }
    #total { margin-top: 15px; font-weight: bold; }
    #toggle-panier {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  font-size: 24px;
  cursor: pointer;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
  z-index: 1000;
}

#toggle-panier .badge {
  position: absolute;
  top: 2px;
  right: 2px;
  background: red;
  color: white;
  font-size: 12px;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

aside#panier {
  position: fixed;
  top: 0;
  right: -320px; /* caché par défaut */
  width: 300px;
  height: 100%;
  background: #fff;
  box-shadow: -2px 0 8px rgba(0,0,0,0.1);
  transition: right 0.3s ease;
  padding: 20px;
  z-index: 999;
  overflow-y: auto;
}

aside#panier.ouvert {
  right: 0;
}

#fermer-panier {
  background: transparent;
  border: none;
  font-size: 20px;
  float: right;
  cursor: pointer;
  margin-bottom: 10px;
}

  </style>
</head>
<body>

  <div class="produits">
    <h2>Catalogue : <?= htmlspecialchars($catalogue['titre']) ?></h2>

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
