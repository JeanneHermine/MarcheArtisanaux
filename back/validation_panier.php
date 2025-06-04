<?php
session_start();
require_once './config.php';
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['client_id']) || empty($_POST['panier'])) {
    header("Location: ../front/templates/boutique.php");
    exit();
}

$id_client = $_SESSION['client_id'];
$panier = $_POST['panier']; // JSON contenant les produits, quantités, etc.
$panier = json_decode($panier, true);

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer l’artisan à partir des produits (on suppose un artisan unique)
    $id_artisan = null;
    foreach ($panier as $item) {
        $stmt = $pdo->prepare("SELECT p.id_catalogue, c.id_artisan 
                               FROM produits p JOIN catalogues c ON p.id_catalogue = c.id_catalogue 
                               WHERE p.id_produit = ?");
        $stmt->execute([$item['id']]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_artisan = $res['id_artisan'];
        break; // on suppose un seul artisan par commande
    }

    // 1. Créer la commande
    $stmt = $pdo->prepare("INSERT INTO commandes (id_client, id_artisan) VALUES (?, ?)");
    $stmt->execute([$id_client, $id_artisan]);
    $id_commande = $pdo->lastInsertId();

    // 2. Ajouter les détails
    $stmt = $pdo->prepare("INSERT INTO articles_commandes (id_commande, id_produit, quantite, prix_unitaire) 
                           VALUES (?, ?, ?, ?)");
    foreach ($panier as $item) {
        $stmt->execute([$id_commande, $item['id'], $item['quantite'], $item['prix']]);
    }

    // 3. Créer une conversation
    $stmt = $pdo->prepare("INSERT INTO conversations (id_client, id_artisan) VALUES (?, ?)");
    $stmt->execute([$id_client, $id_artisan]);
    $id_conversation = $pdo->lastInsertId();

    // 4. Ajouter un message d’intro
    $message_intro = "Bonjour, je viens de passer une commande. Merci de me dire comment procéder pour le paiement et la livraison.";
    $stmt = $pdo->prepare("INSERT INTO messages (id_conversation, expediteur, contenu) VALUES (?, 'client', ?)");
    $stmt->execute([$id_conversation, $message_intro]);

    // 5. Envoyer les mails
    $stmt = $pdo->prepare("SELECT nom, email FROM clients WHERE id_client = ?");
    $stmt->execute([$id_client]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT nom, email FROM artisans WHERE id_artisan = ?");
    $stmt->execute([$id_artisan]);
    $artisan = $stmt->fetch(PDO::FETCH_ASSOC);

    // Construction du contenu HTML pour l'artisan
    ob_start();
    ?>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; background-color: #f9f9f9;">
        <h2 style="color: #333;">Nouvelle commande reçue</h2>
        <p style="font-size: 16px;">Bonjour <?= htmlspecialchars($artisan['nom']) ?>,</p>
        
        <p style="font-size: 15px;">
            Le client <strong><?= htmlspecialchars($client['nom']) ?></strong> 
            a passé une commande. Voici les détails :
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="background-color: #eee;">
                    <th style="padding: 8px; border: 1px solid #ccc;">Produit</th>
                    <th style="padding: 8px; border: 1px solid #ccc;">Quantité</th>
                    <th style="padding: 8px; border: 1px solid #ccc;">Prix unitaire</th>
                    <th style="padding: 8px; border: 1px solid #ccc;">Sous-total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($panier as $item): ?>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ccc;"><?= htmlspecialchars($item['titre']) ?></td>
                        <td style="padding: 8px; border: 1px solid #ccc; text-align: center;"><?= $item['quantite'] ?></td>
                        <td style="padding: 8px; border: 1px solid #ccc;"><?= number_format($item['prix'], 2, ',', ' ') ?> €</td>
                        <td style="padding: 8px; border: 1px solid #ccc;"><?= number_format($item['quantite'] * $item['prix'], 2, ',', ' ') ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p style="font-weight: bold; font-size: 16px; text-align: right; margin-top: 20px;">
            Total : <?= number_format(array_sum(array_map(fn($p) => $p['quantite'] * $p['prix'], $panier)), 2, ',', ' ') ?> €
        </p>

        <p style="margin-top: 20px;">Merci de prendre contact avec le client pour organiser le paiement et la livraison.</p>
    </div>
    <?php
    $emailArtisan = ob_get_clean();
    // Construction du contenu HTML pour le client
    ob_start();
    ?>
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px; background-color: #ffffff;">
        <h2 style="color: #28a745;">Merci pour votre commande !</h2>
        
        <p style="font-size: 16px;">Bonjour <?= htmlspecialchars($client['nom']) ?>,</p>
        
        <p style="font-size: 15px;">
            Nous avons bien reçu votre commande. Voici un récapitulatif :
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th style="padding: 8px; border: 1px solid #ccc;">Produit</th>
                    <th style="padding: 8px; border: 1px solid #ccc;">Quantité</th>
                    <th style="padding: 8px; border: 1px solid #ccc;">Prix unitaire</th>
                    <th style="padding: 8px; border: 1px solid #ccc;">Sous-total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($panier as $item): ?>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ccc;"><?= htmlspecialchars($item['titre']) ?></td>
                        <td style="padding: 8px; border: 1px solid #ccc; text-align: center;"><?= $item['quantite'] ?></td>
                        <td style="padding: 8px; border: 1px solid #ccc;"><?= number_format($item['prix'], 2, ',', ' ') ?> €</td>
                        <td style="padding: 8px; border: 1px solid #ccc;"><?= number_format($item['quantite'] * $item['prix'], 2, ',', ' ') ?> €</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p style="font-weight: bold; font-size: 16px; text-align: right; margin-top: 20px;">
            Total : <?= number_format(array_sum(array_map(fn($p) => $p['quantite'] * $p['prix'], $panier)), 2, ',', ' ') ?> €
        </p>

        <p style="margin-top: 20px;">
            L'artisan prendra contact avec vous prochainement pour vous indiquer les modalités de paiement et de livraison.
        </p>

        <p style="margin-top: 30px; font-size: 14px; color: #555;">
            Merci pour votre confiance,<br>
            <strong>Marché des Artisans</strong>
        </p>
    </div>
    <?php
    $emailClient = ob_get_clean();



    function envoyerMail($destinataire, $nom, $sujet, $contenuHTML) {
        if (empty($destinataire) || !filter_var($destinataire, FILTER_VALIDATE_EMAIL)) {
            return false; 
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'gradinesama@gmail.com'; 
            $mail->Password = 'rwgx oups lusy fkqq';     
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('no-reply@mev.com', 'Test Mail');
            $mail->addAddress($destinataire, $nom);
            $mail->isHTML(true);
            $mail->Subject = $sujet;
            $mail->Body = $contenuHTML;
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur d'envoi de mail à $destinataire : " . $e->getMessage());
            return false;
        }
    }



    if (!empty($client['email'])) {
        envoyerMail($client['email'], $client['nom'], 'Confirmation de votre commande', $emailClient);
    }

    if (!empty($artisan['email'])) {
        envoyerMail($artisan['email'], $artisan['nom'], 'Nouvelle commande reçue', $emailArtisan);
    }

    header("Location: ../front/templates/messagerie.php?id_conversation=" . $id_conversation);
    exit();

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
