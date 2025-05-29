<?php
session_start();
require_once './config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $ville = trim($_POST['ville']);
    $date_inscription = date('Y-m-d');
    $mot = $_POST['mot_de_passe'];
    $confirmPassword = $_POST['confirmer_mot_de_passe'];

    // ⚠️ Au moins email ou téléphone
    if (empty($email) && empty($telephone)) {
        $_SESSION['message_artisan'] = "Veuillez fournir au moins un email ou un numéro de téléphone.";
        $_SESSION['type_artisan'] = "error";
        header("Location: ../front/templates/inscription_artisan.php");
        exit();
    }

    // ✅ Valider email uniquement s'il est présent
    if (!empty($email)) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message_artisan'] = "Adresse e-mail invalide.";
            $_SESSION['type_artisan'] = "error";
            header("Location: ../front/templates/inscription_artisan.php");
            exit();
        }
    } else {
        $email = null;
    }

    // Vérification mot de passe
    if ($mot !== $confirmPassword) {
        $_SESSION['message_artisan'] = "Les mots de passe ne correspondent pas.";
        $_SESSION['type_artisan'] = "error";
        header("Location: ../front/templates/inscription_artisan.php");
        exit();
    }

    $mot_de_passe = password_hash($mot, PASSWORD_DEFAULT);

    try {
        // Vérifier s’il existe déjà un artisan avec le même email ou numéro
        $req = $pdo->prepare("SELECT * FROM artisans WHERE email = ? AND numero = ?");
        $req->execute([$email, $telephone]);
        if ($req->rowCount() > 0) {
            $_SESSION['message_artisan'] = "Un artisan avec cet email et ce numéro existe déjà.";
            $_SESSION['type_artisan'] = "error";
            header("Location: ../front/templates/inscription_artisan.php");
            exit();
        }

        // ✅ Insérer les données (null autorisé pour email ou numéro)
        $stmt = $pdo->prepare("INSERT INTO artisans (nom, prenom, email, mot_de_passe, date_inscription, ville, numero) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $email, $mot_de_passe, $date_inscription, $ville, $telephone ?: null]);

        $_SESSION['message_artisan'] = "Inscription réussie !";
        $_SESSION['type_artisan'] = "success";
        header("Location: ../front/templates/catalogue_art.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['message_artisan'] = "Erreur : " . $e->getMessage();
        $_SESSION['type_artisan'] = "error";
        header("Location: ../front/templates/inscription_artisan.php");
        exit();
    }
}
