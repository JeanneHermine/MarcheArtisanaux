<?php
try {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE $id_column = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}

// Mise à jour des infos (hors changement de statut artisan)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_infos'])) {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';

    if ($isClient) {
        $adresse = $_POST['adresse'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $stmt = $pdo->prepare("UPDATE clients SET nom=?, prenom=?, email=?, adresse=?, telephone=? WHERE id_client=?");
        $stmt->execute([$nom, $prenom, $email, $adresse, $telephone, $id]);
    } else {
        $ville = $_POST['ville'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        $stmt = $pdo->prepare("UPDATE artisans SET nom=?, prenom=?, email=?, ville=?, numero=? WHERE id_artisan=?");
        $stmt->execute([$nom, $prenom, $email, $ville, $telephone, $id]);
    }

    echo "<p style='color:green;'>Informations mises à jour !</p>";
    header("Refresh:1");
    exit();
}
?>
