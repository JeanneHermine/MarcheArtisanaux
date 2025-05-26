<?php
session_start();
require_once 'config.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client && password_verify($_POST['mot_de_passe'], $client['mot_de_passe'])) {
        $_SESSION['client_id'] = $client['id_client'];
        $_SESSION['email'] = $client['email'];
        header("Location: boutique.php");
        exit();
    } else {
        $_SESSION['error_client'] = "Email ou mot de passe incorrect (client)";
        header("Location: connexion.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error_client'] = "Erreur : " . $e->getMessage();
    header("Location: connexion.php");
    exit();
}
