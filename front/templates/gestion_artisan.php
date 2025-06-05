<?php
require_once '../../back/config.php';
session_start();
// Vérification de l'authentification de l'administrateur
if (!isset($_SESSION['admin_id'])) {
    header("Location: ./connexion.html");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_artisan'], $_POST['nouveau_statut'])) {
    $id_artisan = $_POST['id_artisan'];
    $nouveau_statut = $_POST['nouveau_statut'] === 'actif' ? 'actif' : 'inactif';

    $stmt = $pdo->prepare("UPDATE artisans SET statut = :statut WHERE id_artisan = :id");
    $stmt->execute([':statut' => $nouveau_statut, ':id' => $id_artisan]);

    header('Location: ./gestion_artisan.php'); 
    exit;
}

// Récupération de tous les artisans
$stmt = $pdo->query("SELECT * FROM artisans ORDER BY date_inscription DESC");
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
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f7f9fc;
      margin: 0;
      padding: 30px;
      color: #333;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #2c3e50;
    }

    #searchInput {
      width: 100%;
      max-width: 400px;
      padding: 10px 15px;
      margin: 0 auto 20px;
      display: block;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      border-radius: 12px;
      overflow: hidden;
    }

    th, td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: #eaf0f6;
      color: #2c3e50;
    }

    tr:hover {
      background-color: #f1f5fa;
    }

    .actif {
      color: #2ecc71;
      font-weight: bold;
    }

    .inactif {
      color: #e74c3c;
      font-weight: bold;
    }

    form {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    select, button {
      padding: 6px 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    button {
      background-color: #3498db;
      color: white;
      border: none;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background-color: #2980b9;
    }

    @media screen and (max-width: 768px) {
      table, thead, tbody, th, td, tr {
        display: block;
      }

      thead {
        display: none;
      }

      tr {
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 10px;
        background: white;
      }

      td {
        padding: 10px;
        border: none;
        display: flex;
        justify-content: space-between;
      }

      td::before {
        content: attr(data-label);
        font-weight: bold;
        color: #666;
      }
      }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #052523;
      color: #fff;
      padding: 20px 40px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
      margin-bottom: 30px;
      font-family: 'Segoe UI', sans-serif;
      flex-wrap: wrap;
      gap: 20px;
    }

    .logo-title {
      display: flex;
      align-items: center;
      flex-shrink: 0;
    }

    .logo-title .logo {
      font-size: 2rem;
      margin-right: 10px;
    }

    .logo-title .title {
      font-size: 1.8rem;
      font-weight: bold;
      color: #66fcf1;
    }

    .logo-title small {
      font-size: 1rem;
      color: #aaa;
      font-weight: normal;
      margin-left: 8px;
    }

    .admin-info {
      text-align: right;
      flex-grow: 1;
    }

    .admin-info h2 {
      margin: 0;
      font-size: 1.2rem;
      color: #ffffff;
    }

    .actions {
      margin-top: 8px;
    }

    .actions a {
      color: #ffffff;
      text-decoration: none;
      margin-left: 10px;
      font-weight: 600;
      padding: 8px 14px;
      border-radius: 6px;
      transition: background-color 0.3s ease;
      display: inline-block;
    }

    .actions a:hover {
      background-color: #66fcf1;
      color: #052523;
    }

    @media (max-width: 768px) {
      .top-bar {
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
      }

      .admin-info {
        text-align: left;
        width: 100%;
      }

      .actions a {
        margin-left: 0;
        margin-right: 10px;
        margin-top: 8px;
      }
    }


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
            <button type="submit">Modifier</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <button onclick="exporterPDF()" style="
    background-color: #66fcf1;
    color: black;
    font-weight: bold;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    margin-bottom: 20px;
    cursor: pointer;
">📄 Exporter en PDF</button>


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
    // Fonction pour exporter le tableau en PDF
    async function exporterPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        const logoUrl = '../assets/img/logo.jpeg'; 

        // Charge le logo en base64
        const logoBase64 = await fetch(logoUrl)
            .then(res => res.blob())
            .then(blob => new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.readAsDataURL(blob);
            }));

        // Ajout du logo + titre
        doc.addImage(logoBase64, 'PNG', 10, 10, 20, 20);
        doc.setFontSize(18);
        doc.setTextColor('#66fcf1');
        doc.setFont('helvetica', 'bold');
        doc.text("BEN'ART - Liste des Artisans", 35, 22);

        doc.setFontSize(11);
        doc.setTextColor(50);
        doc.text(`Date : ${new Date().toLocaleDateString('fr-FR')}`, 150, 22);

        // Récupération des données depuis le tableau HTML
        const table = document.querySelector("table");
        const headers = [...table.querySelectorAll("thead th")].map(th => th.innerText.trim());
        const rows = [...table.querySelectorAll("tbody tr")].map(tr =>
            [...tr.querySelectorAll("td")].map(td => td.innerText.trim())
        );

        // Génération du tableau dans le PDF
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
