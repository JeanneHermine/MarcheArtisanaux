<?php
require_once '../../../back/config.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: ../../connexion.html");
    exit();
}

$id_client = $_SESSION['client_id'];

// Traitement du formulaire d'avis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_avis'])) {
    $id_produit = $_POST['id_produit'];
    $note = $_POST['note'];
    $commentaire = $_POST['commentaire'];

    $stmt = $pdo->prepare("INSERT INTO avis (id_client, id_produit, note, commentaire, date_avis) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$id_client, $id_produit, $note, $commentaire]);
    $message = "Avis enregistré !";
}

// Récupération des commandes du client
$stmt = $pdo->prepare("
    SELECT c.id_commande, c.statut, ac.id_produit, ac.quantite, ac.prix_unitaire, p.nom_produit
    FROM commandes c
    JOIN articles_commandes ac ON c.id_commande = ac.id_commande
    JOIN produits p ON ac.id_produit = p.id_produit
    WHERE c.id_client = ?
    ORDER BY c.id_commande DESC
");
$stmt->execute([$id_client]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Mes commandes</h2>

<?php if (isset($message)) echo "<p style='color:green;'>$message</p>"; ?>

<?php if ($commandes): ?>
    <table border="1" cellpadding="5">
        <tr>
            <th>Commande</th>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix</th>
            <th>Statut</th>
            <th>Avis</th>
        </tr>
        <?php foreach ($commandes as $c): ?>
            <tr>
                <td><?= $c['id_commande'] ?></td>
                <td><?= htmlspecialchars($c['nom_produit']) ?></td>
                <td><?= $c['quantite'] ?></td>
                <td><?= $c['prix_unitaire'] ?> FCFA</td>
                <td><?= $c['statut'] ?></td>
                <td>
                    <?php
                    // Vérifier si un avis existe déjà
                    $stmtAvis = $pdo->prepare("SELECT * FROM avis WHERE id_client = ? AND id_produit = ?");
                    $stmtAvis->execute([$id_client, $c['id_produit']]);
                    $avis = $stmtAvis->fetch(PDO::FETCH_ASSOC);

                    if ($c['statut'] === 'expédiée') {
                        if (!$avis): ?>
                            <form method="POST">
                                <input type="hidden" name="id_produit" value="<?= $c['id_produit'] ?>">
                                <label>Note :
                                    <select name="note" required>
                                        <option value="5">5 ⭐</option>
                                        <option value="4">4 ⭐</option>
                                        <option value="3">3 ⭐</option>
                                        <option value="2">2 ⭐</option>
                                        <option value="1">1 ⭐</option>
                                    </select>
                                </label><br>
                                <textarea name="commentaire" placeholder="Votre avis" required></textarea><br>
                                <input type="submit" name="submit_avis" value="Envoyer">
                            </form>
                        <?php else: ?>
                            <p>Note : <?= $avis['note'] ?>/5<br>Commentaire : <?= htmlspecialchars($avis['commentaire']) ?></p>
                        <?php endif;
                    } else {
                        echo "Commande non livrée.";
                    }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Vous n'avez passé aucune commande.</p>
<?php endif; ?>
