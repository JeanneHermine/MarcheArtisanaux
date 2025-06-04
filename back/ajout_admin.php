<?php
require_once './config.php';


// Récupération des données JSON envoyées
$data = json_decode(file_get_contents("php://input"), true);

// Vérification des champs requis
if (!isset($data['nom'], $data['prenom'], $data['email'], $data['mot_de_passe'], $data['role'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Champs requis manquants.']);
    exit;
}

// Hachage du mot de passe
$motDePasseHashe = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);

// Insertion dans la base
try {
    $sql = "INSERT INTO administrateurs (nom, prenom, email, mot_de_passe, role)
            VALUES (:nom, :prenom, :email, :mot_de_passe, :role)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $data['nom'],
        ':prenom' => $data['prenom'],
        ':email' => $data['email'],
        ':mot_de_passe' => $motDePasseHashe,
        ':role' => $data['role']
    ]);

    echo json_encode(['message' => 'Administrateur enregistré avec succès.']);
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        http_response_code(409);
        echo json_encode(['error' => 'Email déjà utilisé.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de l\'enregistrement.']);
    }
}
