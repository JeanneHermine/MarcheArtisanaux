<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['client_id']) && !isset($_SESSION['artisan_id'])) {
    header("Location: connexion.php");
    exit();
}

$isClient = isset($_SESSION['client_id']);
$id = $isClient ? $_SESSION['client_id'] : $_SESSION['artisan_id'];
$table = $isClient ? 'clients' : 'artisans';
$id_column = $isClient ? 'id_client' : 'id_artisan';

try {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $id_column = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    if ($isClient) {
        $adresse = $_POST['adresse'];
        $telephone = $_POST['telephone'];
        $stmt = $pdo->prepare("UPDATE clients SET nom=?, prenom=?, email=?, adresse=?, telephone=? WHERE id_client=?");
        $stmt->execute([$nom, $prenom, $email, $adresse, $telephone, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE artisans SET nom=?, prenom=?, email=? WHERE id_artisan=?");
        $stmt->execute([$nom, $prenom, $email, $id]);
    }

    echo "<p style='color:green;'>Informations mises à jour !</p>";
    // Recharger les données modifiées
    header("Refresh:1"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil <?= $isClient ? "Client" : "Artisan" ?></title>
    <link rel="stylesheet" href="profil.css">
</head>
<body>
<h2>Profil <?= $isClient ? "Client" : "Artisan" ?></h2>

<form method="POST">
    <label>Nom :</label><br>
    <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required><br><br>

    <label>Prénom :</label><br>
    <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required><br><br>

    <label>Email :</label><br>
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>

    <?php if ($isClient): ?>
        <label>Adresse :</label><br>
        <input type="text" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>"><br><br>

        <label>Téléphone :</label><br>
        <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>"><br><br>
    <?php endif; ?>

    <input type="submit" value="Mettre à jour">
</form>

</body>
</html>
