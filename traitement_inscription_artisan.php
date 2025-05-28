<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message_artisan'] = "Adresse e-mail invalide.";
        $_SESSION['type_artisan'] = "error";
        header("Location: inscription_client.php");
        exit();
    }
    $confirmPassword = $_POST['confirmer_mot_de_passe'];
    if ($_POST['mot_de_passe'] !== $confirmPassword) {
        $_SESSION['message_artisan'] = "Les mots de passe ne correspondent pas.";
        $_SESSION['type_artisan'] = "error";
        header("Location: inscription_artisan.php");
        exit();
    }
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $telephone = $_POST['telephone'];
    $ville = $_POST['ville'];
    $date_inscription = date('Y-m-d');
    // Vérification de l'existence de l'artisan
    $req = $pdo->prepare("SELECT * FROM artisans WHERE email = ?");
    $req->execute([$email]);
    if ($req->rowCount() > 0) {
        $_SESSION['message_artisan'] = "Un artisan avec cette adresse e-mail existe déjà.";
        $_SESSION['type_artisan'] = "error";
        header("Location: inscription_artisan.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO artisans (nom, prenom, email, mot_de_passe, date_inscription,ville,numero) VALUES (?, ?, ?, ?, ?,?,?)");
        $stmt->execute([$nom, $prenom, $email, $mot_de_passe, $date_inscription, $ville, $telephone]);

        $_SESSION['message_artisan'] = "Inscription réussie !";
        $_SESSION['type_artisan'] = "success";
        // Optionnel : rediriger vers une page de connexion ou de bienvenue
        header("Location: connexion.html");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message_artisan'] = "Erreur : " . $e->getMessage();
        $_SESSION['type_artisan'] = "error";
    }

    header("Location: inscription.html");
    exit();
}
