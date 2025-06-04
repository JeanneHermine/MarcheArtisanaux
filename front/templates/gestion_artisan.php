<?php
require_once '../../back/config.php';
session_start();
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
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        form { display: inline; }
        .actif { color: green; font-weight: bold; }
        .inactif { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Liste des artisans</h1>
    <table>
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
                <td><?= htmlspecialchars($artisan['nom'] ?? '') ?></td>
                <td><?= htmlspecialchars($artisan['prenom'] ?? '') ?></td>
                <td><?= htmlspecialchars($artisan['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($artisan['ville'] ?? '') ?></td>
                <td class="<?= $artisan['statut'] ?>"><?= $artisan['statut'] ?></td>
                <td>
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
</body>
</html>