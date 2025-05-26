<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $date_inscription = date('Y-m-d');

    try {
        $stmt = $pdo->prepare("INSERT INTO clients (nom, prenom, email, mot_de_passe, adresse, telephone, date_inscription) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $mot_de_passe, $adresse, $telephone, $date_inscription]);

        $_SESSION['message_client'] = "Inscription client réussie !";
        $_SESSION['type_client'] = "success";
        // Optionnel : rediriger vers une page de connexion ou de bienvenue
        header("Location: connexion.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message_client'] = "Erreur : " . $e->getMessage();
        $_SESSION['type_client'] = "error";
    }

    header("Location: inscription.php");
    exit();
}
