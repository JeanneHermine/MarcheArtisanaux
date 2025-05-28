<?php
session_start();
require_once './config.php';

if (!isset($_POST['id_conversation'], $_POST['expediteur'], $_POST['contenu'])) {
    header('Location: ../front/templates/boutique.php');
    exit;
}

$id_conversation = $_POST['id_conversation'];
$expediteur = $_POST['expediteur'];
$contenu = trim($_POST['contenu']);

if ($contenu === '') {
    header("Location: ../front/templates/messagerie.php?id_conversation=$id_conversation");
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO messages (id_conversation, expediteur, contenu, date_envoi) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$id_conversation, $expediteur, $contenu]);

    header("Location: ../front/templates/messagerie.php?id_conversation=$id_conversation");
    exit;

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
