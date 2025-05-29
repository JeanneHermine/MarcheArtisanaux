<?php
require_once '../../back/config.php';

if (!isset($_GET['id_produit'])) {
    echo json_encode([]);
    exit();
}

$id_produit = $_GET['id_produit'];
//verifier que l'id existe dans la base de données
$stmt = $pdo->prepare("SELECT COUNT(*) FROM produits WHERE id_produit = ?");
$stmt->execute([$id_produit]);
if ($stmt->fetchColumn() == 0) {
    echo json_encode([]);
    exit();
}

$stmt = $pdo->prepare("
    SELECT a.note, a.commentaire, a.date_avis, c.nom, c.prenom 
    FROM avis a
    LEFT JOIN clients c ON a.id_client = c.id_client
    WHERE a.id_produit = ?
    ORDER BY a.date_avis DESC
");
$stmt->execute([$id_produit]);
$avis = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($avis);
