<?php
session_start();
require_once '../../../back/config.php';

if (!isset($_SESSION['client_id']) && !isset($_SESSION['artisan_id'])) {
    header("Location: ../connexion.html");
    exit();
}

$isClient = isset($_SESSION['client_id']);
$id = $isClient ? $_SESSION['client_id'] : $_SESSION['artisan_id'];
$table = $isClient ? 'clients' : 'artisans';
$id_column = $isClient ? 'id_client' : 'id_artisan';

$id_artisan = $_SESSION['artisan_id'];

// Vérification du statut de l’artisan
$stmt = $pdo->prepare("SELECT statut FROM artisans WHERE id_artisan = ?");
$stmt->execute([$id_artisan]);
$artisan = $stmt->fetch(PDO::FETCH_ASSOC);

require_once 'infos_utilisateur.php'; // gestion du formulaire et récupération $user
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil <?= $isClient ? "Client" : "Artisan" ?></title>
    <link rel="stylesheet" href="../../assets/css/profil.css">
</head>
<body>

<div class="header-bar">
  <h2>Profil <?= $isClient ? "Client" : "Artisan" ?></h2>
    <div class="nav-links">
        <a href="../boutique.php">Accueil</a>
        <a href="../../../back/deconnexion.php">Déconnexion</a>

        <?php if ($isClient): ?>
            <a href="../chat.php">Discussions</a>
        <?php elseif ($artisan && $artisan['statut'] === 'actif'): ?>
            <a href="../chat.php">Discussions</a>
        <?php endif; ?>
    </div>

</div>


<form method="POST">
    <!-- champs du formulaire -->
    <?php if ($isClient): ?>
        <label>Nom :</label><br>
        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required><br><br>

        <label>Prénom :</label><br>
        <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required><br><br>

        <label>Email :</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <label>Adresse :</label><br>
        <input type="text" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>"><br><br>

        <label>Téléphone :</label><br>
        <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>"><br><br>
    <?php else: ?>
        <label>Nom :</label><br>
        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required><br><br>

        <label>Prénom :</label><br>
        <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required><br><br>

        <label>Email :</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

        <label>Ville :</label><br>
        <input type="text" name="ville" value="<?= htmlspecialchars($user['ville']) ?>"><br><br>

        <label>Téléphone :</label><br>
        <input type="text" name="telephone" value="<?= htmlspecialchars($user['numero']) ?>"><br><br>
    <?php endif; ?>

    <input type="submit" name="update_infos" value="Mettre à jour">
</form>

<hr>

<?php
if ($isClient) {
    include 'commandes_client.php';
} else {
    include 'commandes_artisan.php';
}
?>

</body>
</html>