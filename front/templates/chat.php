<?php
session_start();
require_once '../../back/config.php';

$utilisateur = null;
$id_utilisateur = null;

if (isset($_SESSION['client_id'])) {
    $utilisateur = 'client';
    $id_utilisateur = $_SESSION['client_id'];
} elseif (isset($_SESSION['artisan_id'])) {
    $utilisateur = 'artisan';
    $id_utilisateur = $_SESSION['artisan_id'];
} else {
    echo "Accès non autorisé.";
    exit;
}

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($utilisateur === 'client') {
        $stmt = $pdo->prepare("SELECT c.id_conversation, a.nom AS nom_autre 
                               FROM conversations c 
                               JOIN artisans a ON c.id_artisan = a.id_artisan 
                               WHERE c.id_client = ?");
    } else {
        $stmt = $pdo->prepare("SELECT c.id_conversation, cl.nom AS nom_autre 
                               FROM conversations c 
                               JOIN clients cl ON c.id_client = cl.id_client 
                               WHERE c.id_artisan = ?");
    }

    $stmt->execute([$id_utilisateur]);
    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes conversations</title>
    <link rel="stylesheet" href="../../front/assets/css/chat.css">
</head>
<body>
    <header>
        <a href="./boutique.php">Accueil</a>
    </header>

<div class="chat-list">
    <h2>Mes conversations</h2>
    <?php if (count($conversations) === 0): ?>
        <p>Aucune conversation pour le moment.</p>
    <?php else: ?>
        <?php foreach ($conversations as $conv): ?>
            <div class="chat-item">
                <a href="./messagerie.php?id_conversation=<?= $conv['id_conversation'] ?>">
                    Conversation avec <?= htmlspecialchars($conv['nom_autre']) ?>
                </a>
            </div>
            <?php if ($utilisateur === 'artisan'): ?>
              <form action="../../back/supprimer_conversation.php" method="POST" onsubmit="return confirm('Supprimer cette conversation ?');">
                  <input type="hidden" name="id_conversation" value="<?= $conv['id_conversation'] ?>">
                  <button type="submit" style="background-color: red; color: white; border: none; padding: 5px 10px; margin-bottom: 15px;">Supprimer la conversation</button>
              </form>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>
