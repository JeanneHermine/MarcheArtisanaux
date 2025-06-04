<?php
require_once '../../../back/config.php';

if (!isset($_SESSION['artisan_id'])) {
    header("Location: ../../connexion.html");
    exit();
}

$id = $_SESSION['artisan_id'];

// Vérification du statut de l’artisan
$req = $pdo->prepare("SELECT statut FROM artisans WHERE id_artisan = ?");
$req->execute([$id]);
$artisan = $req->fetch(PDO::FETCH_ASSOC);

// Si l'artisan est inactif, afficher un message et bloquer l'accès
if (!$artisan || $artisan['statut'] !== 'actif') {
    echo "<h2 style='color: red;'>Votre compte est actuellement inactif.</h2>";
    echo "<p>Veuillez contacter le service client pour plus d’informations.</p>";
    echo "<p>⚠️ Si vous avez reçu des commandes récemment, nous vous prions de <strong>contacter rapidement le service client</strong>.</p>";
    exit(); 
}

// Traitement du formulaire AVANT toute sortie HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_statuts'])) {
    foreach ($_POST['statut'] as $id_commande => $new_statut) {
        $stmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id_commande = ? AND id_artisan = ?");
        $stmt->execute([$new_statut, $id_commande, $id]);
    }
    header("Refresh:1");
    exit();
}

// Récupération des commandes
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id_artisan = ?");
$stmt->execute([$id]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h3>Commandes reçues</h3>

<?php if ($commandes): ?>
<form method="POST">
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Statut</th>
                <th>Modifier</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($commandes as $commande): ?>
            <tr>
                <td><?= $commande['id_commande'] ?></td>
                <td>
                    <?php
                    $stmtC = $pdo->prepare("SELECT nom FROM clients WHERE id_client = ?");
                    $stmtC->execute([$commande['id_client']]);
                    $client = $stmtC->fetch(PDO::FETCH_ASSOC);
                    echo htmlspecialchars($client['nom'] ?? 'Inconnu');
                    ?>
                </td>
                <td><?= $commande['statut'] ?></td>
                <td>
                    <select name="statut[<?= $commande['id_commande'] ?>]">
                        <option value="en cours" <?= $commande['statut'] === 'en cours' ? 'selected' : '' ?>>en cours</option>
                        <option value="expédiée" <?= $commande['statut'] === 'expédiée' ? 'selected' : '' ?>>expédiée</option>
                        <option value="annulée" <?= $commande['statut'] === 'annulée' ? 'selected' : '' ?>>annulée</option>
                    </select>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <input type="submit" name="update_statuts" value="Mettre à jour les statuts">
</form>
<?php else: ?>
<p>Aucune commande reçue.</p>
<?php endif; ?>
