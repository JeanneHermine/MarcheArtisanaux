<?php
session_start();
require_once './config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['identifiant']);
    $mot_de_passe = $_POST['mot_de_passe'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE email = :identifiant LIMIT 1");
        $stmt->execute(['identifiant' => $identifiant]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($mot_de_passe, $admin['mot_de_passe'])) {
            $_SESSION['admin_id'] = $admin['id_admin'];


            header("Location: ../front/templates/gestion_artisan.php");
            exit();
        }

        $stmt = $pdo->prepare("
            SELECT * FROM artisans 
            WHERE email = :identifiant OR numero = :identifiant
            LIMIT 1
        ");
        $stmt->execute(['identifiant' => $identifiant]);
        $artisan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($artisan && password_verify($mot_de_passe, $artisan['mot_de_passe'])) {
            $_SESSION['artisan_id'] = $artisan['id_artisan'];
            $_SESSION['identifiant_artisan'] = $artisan['nom'] . ' ' . $artisan['prenom'];

            header("Location: ../front/templates/catalogue_art.php");
            exit();
        } else {
            $_SESSION['message_artisan'] = "Identifiants invalides.";
            $_SESSION['type_artisan'] = "error";
            header("Location: ../front/templates/connexion_artisan.php");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['message_artisan'] = "Erreur : " . $e->getMessage();
        $_SESSION['type_artisan'] = "error";
        header("Location: ../front/templates/connexion_artisan.php");
        exit();
    }
}
