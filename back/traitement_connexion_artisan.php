<?php
session_start();
require_once './config.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM artisans WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $artisan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($artisan && password_verify($_POST['mot_de_passe'], $artisan['mot_de_passe'])) {
        $_SESSION['artisan_id'] = $artisan['id_artisan'];
        $_SESSION['email'] = $artisan['email'];
        header("Location: ../front/templates/catalogue_art.php");
        exit();
    } else {
        $_SESSION['error_artisan'] = "Email ou mot de passe incorrect (artisan)";
        header("Location: ../front/templates/connexion_artisan.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error_artisan'] = "Erreur : " . $e->getMessage();
    header("Location: ../front/templates/connexion_artisan.php");
    exit();
}
