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
    <style>
      html, body {
          height: 100%;
          margin: 0;
          font-family: Arial, sans-serif;
          background-color: #f4f4f4;
      }

      .message-box {
          display: flex;
          flex-direction: column;
          height: 100vh;
          max-width: 600px;
          margin: auto;
          background: white;
          border-radius: 8px;
          box-shadow: 0 0 10px #ccc;
          overflow: hidden;
      }

      .messages-container {
          flex: 1;
          overflow-y: auto;
          padding: 20px;
          display: flex;
          flex-direction: column;
          gap: 10px;
      }

      .message {
          padding: 10px;
          border-radius: 5px;
          max-width: 80%;
      }

      .client {
          background-color: #e7f5ff;
          align-self: flex-start;
      }

      .artisan {
          background-color: #fff3cd;
          align-self: flex-end;
      }

      .form-message {
          padding: 15px;
          border-top: 1px solid #ddd;
          background-color: #fafafa;
      }

      textarea {
          width: 100%;
          height: 80px;
          padding: 10px;
          resize: none;
      }

      button {
          padding: 10px 20px;
          margin-top: 10px;
          background-color: #007bff;
          color: white;
          border: none;
          border-radius: 5px;
      }

    </style>
</head>
<body>

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
