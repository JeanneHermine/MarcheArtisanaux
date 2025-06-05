<?php
session_start();
require_once '../../back/config.php';

if (!isset($_GET['id_conversation'])) {
    echo "Conversation introuvable.";
    exit;
}

$id_conversation = $_GET['id_conversation'];
$utilisateur = isset($_SESSION['client_id']) ? 'client' : (isset($_SESSION['artisan_id']) ? 'artisan' : null);

if (!$utilisateur) {
    echo "Accès non autorisé.";
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les messages
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id_conversation = ? ORDER BY date_envoi ASC");
    $stmt->execute([$id_conversation]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie</title>
    <link rel="stylesheet" href="../../front/assets/css/messagerie.css">
    <link rel="icon" href="../../front/assets/img/logo.jpeg" type="image/x-icon">
</head>
<body>
    <header>
    <h1>Messagerie</h1>
    <nav>
        <a href="./boutique.php">Accueil</a>
        <a href="./chat.php">Chat</a>
        <a href="javascript:history.back()" class="btn-retour"> Retour</a>
    </header>

<div class="message-box">
    <div class="messages-container" id="messages">
        <?php foreach ($messages as $msg): ?>
            <div class="message <?= htmlspecialchars($msg['expediteur']) ?>">
                <strong><?= ucfirst($msg['expediteur']) ?> :</strong><br>
                <?= nl2br(htmlspecialchars($msg['contenu'])) ?><br>
                <small><?= date('d/m/Y H:i', strtotime($msg['date_envoi'])) ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <form class="form-message" action="../../back/envoyer_message.php" method="POST">
        <input type="hidden" name="id_conversation" value="<?= $id_conversation ?>">
        <input type="hidden" name="expediteur" value="<?= $utilisateur ?>">
        <textarea name="contenu" placeholder="Votre message..." required></textarea>
        <button type="submit">Envoyer</button>
    </form>
</div>


</body>
<script>
    const container = document.getElementById('messages');
    container.scrollTop = container.scrollHeight;
</script>

</html>
