<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <style>
    body {
      font-family: 'Arial', sans-serif;
      background-color: #f9f9f9;
      padding: 20px;
      color: #333;
    }

    .mail-container {
      background-color: #fff;
      border-radius: 8px;
      padding: 20px;
      max-width: 600px;
      margin: auto;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .header {
      background-color: #007BFF;
      color: white;
      padding: 15px;
      border-radius: 6px 6px 0 0;
      text-align: center;
      font-size: 20px;
    }

    .section {
      padding: 10px 0;
    }

    .section h3 {
      margin-top: 20px;
      color: #007BFF;
    }

    .product-list li {
      margin-bottom: 8px;
    }

    .total {
      font-size: 18px;
      font-weight: bold;
      margin-top: 20px;
      color: #000;
    }

    .client-info li {
      margin-bottom: 4px;
    }

    .footer {
      font-size: 12px;
      text-align: center;
      margin-top: 30px;
      color: #999;
    }
  </style>
</head>
<body>
  <div class="mail-container">
    <div class="header">
      <?= $destinataire === 'client' ? 'Merci pour votre commande' : 'Nouvelle commande reçue' ?>
    </div>

    <div class="section">
      <p>
        <?= $destinataire === 'client'
          ? 'Bonjour ' . htmlspecialchars($client['nom']) . ', voici le récapitulatif de votre commande :'
          : 'Un client a passé une commande. Voici les détails :'
        ?>
      </p>

      <?php if ($destinataire === 'artisan'): ?>
        <h3>Informations du client</h3>
        <ul class="client-info">
          <li><strong>Nom :</strong> <?= htmlspecialchars($client['nom']) ?></li>
          <li><strong>Email :</strong> <?= htmlspecialchars($client['email']) ?></li>
          <?php if (!empty($client['ville'])): ?><li><strong>Ville :</strong> <?= htmlspecialchars($client['ville']) ?></li><?php endif; ?>
          <?php if (!empty($client['numero'])): ?><li><strong>Téléphone :</strong> <?= htmlspecialchars($client['numero']) ?></li><?php endif; ?>
        </ul>
      <?php endif; ?>

      <h3>Contenu de la commande</h3>
      <ul class="product-list">
        <?php foreach ($panier as $item): ?>
          <li><?= htmlspecialchars($item['titre']) ?> — <?= $item['quantite'] ?> × <?= number_format($item['prix'], 2, ',', ' ') ?> €</li>
        <?php endforeach; ?>
      </ul>

      <p class="total">
        Total à payer : <?= number_format(array_sum(array_map(fn($p) => $p['quantite'] * $p['prix'], $panier)), 2, ',', ' ') ?> €
      </p>
    </div>

    <div class="footer">
      Cet e-mail est généré automatiquement. Merci de ne pas y répondre.
    </div>
  </div>
</body>
</html>
<?php
$emailHtml = ob_get_clean();
