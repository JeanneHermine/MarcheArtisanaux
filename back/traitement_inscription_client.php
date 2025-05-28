<?php
session_start();
require_once './config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message_artisan'] = "Adresse e-mail invalide.";
        $_SESSION['type_artisan'] = "error";
        header("Location: ../front/templates/inscription_artisan.php");
        exit();
    }
    $ConfirmPassword = $_POST['confirmer_mot_de_passe'];
    if ($_POST['mot_de_passe'] !== $ConfirmPassword) {
        $_SESSION['message_client'] = "Les mots de passe ne correspondent pas.";
        $_SESSION['type_client'] = "error";
        header("Location: ../front/templates/inscription_client.php");
        exit();
    }
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $date_inscription = date('Y-m-d');
    // Vérification de l'existence du client
    $req = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
    $req->execute([$email]);
    if ($req->rowCount() > 0) {
        $_SESSION['message_client'] = "Un client avec cette adresse e-mail existe déjà.";
        $_SESSION['type_client'] = "error";
        header("Location: ../front/templates/inscription_client.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO clients (nom, prenom, email, mot_de_passe, adresse, telephone, date_inscription) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $mot_de_passe, $adresse, $telephone, $date_inscription]);

        $_SESSION['message_client'] = "Inscription client réussie !";
        $_SESSION['type_client'] = "success";
        // Optionnel : rediriger vers une page de connexion ou de bienvenue
        header("Location: ../front/templates/boutique.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message_client'] = "Erreur : " . $e->getMessage();
        $_SESSION['type_client'] = "error";
    }

    header("Location: ../front/templates/inscription_client.php");
    exit();
}
