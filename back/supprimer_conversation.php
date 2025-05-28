<?php
session_start();
require_once './config.php';

if (!isset($_SESSION['artisan_id']) || !isset($_POST['id_conversation'])) {
    die("Accès refusé.");
}

$id_conversation = $_POST['id_conversation'];

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Supprimer les messages liés
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id_conversation = ?");
    $stmt->execute([$id_conversation]);

    // Supprimer la conversation
    $stmt = $pdo->prepare("DELETE FROM conversations WHERE id_conversation = ?");
    $stmt->execute([$id_conversation]);

    header("Location: ../front/templates/chat.php");
    exit;

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
