<?php
session_start();
require_once './config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['identifiant']);
    $mot_de_passe = $_POST['mot_de_passe'];

    try {
        // Requête pour chercher par email OU téléphone
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = :identifiant OR telephone = :identifiant LIMIT 1");
        $stmt->execute(['identifiant' => $identifiant]);
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification du mot de passe
        if ($client && password_verify($mot_de_passe, $client['mot_de_passe'])) {
            $_SESSION['client_id'] = $client['id_client'];
            $_SESSION['identifiant'] = $client['email'] ?? $client['telephone'];

            header("Location: ../front/templates/boutique.php");
            exit();
        } else {
            $_SESSION['error_client'] = "Identifiant ou mot de passe incorrect (client).";
            header("Location: ../front/templates/connexion_client.php");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error_client'] = "Erreur : " . $e->getMessage();
        header("Location: ../front/templates/connexion_client.php");
        exit();
    }
}
