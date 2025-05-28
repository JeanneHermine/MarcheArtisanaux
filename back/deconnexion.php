<?php
// deconnexion.php
session_start();
// Détruire la session pour déconnecter l'utilisateur 
session_unset();
session_destroy();
// Rediriger vers la page d'accueil ou de connexion
header("Location: ../front/templates/boutique.php");
exit();
?>