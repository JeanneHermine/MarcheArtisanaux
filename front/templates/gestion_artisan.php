<?php
require_once '../../back/config.php';
session_start();

// Vérification de l'authentification de l'administrateur
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./connexion.html");
    exit();
}

// Traitement de la modification de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_artisan'], $_POST['nouveau_statut'])) {
    $id_artisan = $_POST['id_artisan'];
    $nouveau_statut = $_POST['nouveau_statut'] === 'actif' ? 'actif' : 'inactif';

    $stmt = $pdo->prepare("UPDATE artisans SET statut = :statut WHERE id_artisan = :id");
    $stmt->execute([':statut' => $nouveau_statut, ':id' => $id_artisan]);

    header('Location: ./gestion_artisan.php');
    exit;
}

// Traitement de la recherche par nom
$filtre_nom = $_GET['filtre_nom'] ?? '';
if (!empty($filtre_nom)) {
    $stmt = $pdo->prepare("SELECT * FROM artisans WHERE nom LIKE :filtre ORDER BY date_inscription DESC");
    $stmt->execute([':filtre' => "%$filtre_nom%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM artisans ORDER BY date_inscription DESC");
}
$artisans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Artisans</title>
  <link rel="icon" href="../assets/img/logo.jpeg" type="image/x-icon">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
  <style>
    /* Ton style CSS reste inchangé (je ne le réaffiche pas ici pour alléger) */
  </style>
</head>
<body>

  <div class="top-bar">
    <div class="logo-title">
      <span class="logo">🛍️</span>
      <span class="title">BEN'ART</span>
    </div>
    <h2>Bienvenue, <?= htmlspecialchars($_SESSION['admin_nom'] ?? 'Administrateur') ?></h2>
    <div class="admin-info">
      <div class="actions">
        <a href="./boutique.php"> Accueil</a>
        <a href="../../back/deconnexion.php"> Déconnexion</a>
      </div>
    </div>
  </div>

  <h1>Liste des artisans</h1>

  <!-- Barre de recherche par nom -->
  <form method="get" style="margin-bottom: 20px; text-align: center;">
    <input type="text" name="filtre_nom" placeholder="🔍 Rechercher par nom..." value="<?= htmlspecialchars($filtre_nom) ?>" style="padding: 10px; border-radius: 6px; width: 300px; max-width: 90%;">
    <button type="submit" style="padding: 10px 15px; border-radius: 6px;">Rechercher</button>
  </form>

  <input type="text" id="searchInput" placeholder="🔍 Rechercher par nom, ville, ou email...">

  <table id="artisanTable">
    <thead>
      <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Ville</th>
        <th>Statut</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($artisans as $artisan): ?>
      <tr>
        <td data-label="Nom"><?= htmlspecialchars($artisan['nom'] ?? '') ?></td>
        <td data-label="Prénom"><?= htmlspecialchars($artisan['prenom'] ?? '') ?></td>
        <td data-label="Email"><?= htmlspecialchars($artisan['email'] ?? '') ?></td>
        <td data-label="Ville"><?= htmlspecialchars($artisan['ville'] ?? '') ?></td>
        <td data-label="Statut" class="<?= $artisan['statut'] ?>"><?= $artisan['statut'] ?></td>
        <td data-label="Action">
          <form method="post">
            <input type="hidden" name="id_artisan" value="<?= $artisan['id_artisan'] ?>">
            <select name="nouveau_statut">
              <option value="actif" <?= $artisan['statut'] === 'actif' ? 'selected' : '' ?>>actif</option>
              <option value="inactif" <?= $artisan['statut'] === 'inactif' ? 'selected' : '' ?>>inactif</option>
            </select>
            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir modifier le statut de cet artisan ?');">Modifier</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <button onclick="exporterPDF()" style="background-color: #66fcf1; color: black; font-weight: bold; padding: 10px 20px; border: none; border-radius: 5px; margin-bottom: 20px; cursor: pointer;">📄 Exporter en PDF</button>

  <script>
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('#artisanTable tbody tr');

    searchInput.addEventListener('input', () => {
      const value = searchInput.value.toLowerCase();
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(value) ? '' : 'none';
      });
    });

    async function exporterPDF() {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();

      const logoUrl = '../assets/img/logo.jpeg';

      const logoBase64 = await fetch(logoUrl)
        .then(res => res.blob())
        .then(blob => new Promise((resolve) => {
          const reader = new FileReader();
          reader.onload = () => resolve(reader.result);
          reader.readAsDataURL(blob);
        }));

      doc.addImage(logoBase64, 'PNG', 10, 10, 20, 20);
      doc.setFontSize(18);
      doc.setTextColor('#66fcf1');
      doc.setFont('helvetica', 'bold');
      doc.text("BEN'ART - Liste des Artisans", 35, 22);

      doc.setFontSize(11);
      doc.setTextColor(50);
      doc.text(`Date : ${new Date().toLocaleDateString('fr-FR')}`, 150, 22);

      const table = document.querySelector("table");
      const headers = [...table.querySelectorAll("thead th")].map(th => th.innerText.trim());
      const rows = [...table.querySelectorAll("tbody tr")].map(tr =>
        [...tr.querySelectorAll("td")].map(td => td.innerText.trim())
      );

      doc.autoTable({
        head: [headers],
        body: rows,
        startY: 35,
        styles: {
          fontSize: 10,
          textColor: 20,
        },
        headStyles: {
          fillColor: [102, 252, 241],
          textColor: 0,
          fontStyle: 'bold'
        },
        alternateRowStyles: {
          fillColor: [240, 240, 240]
        }
      });

      doc.save("artisans.pdf");
    }
  </script>

</body>
</html>
