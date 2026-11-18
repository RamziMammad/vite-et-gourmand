<?php
header('Content-Type: application/json; charset=utf-8');
try {
    require __DIR__ . '/connexion.php';
    $requete = $pdo->query('SELECT jour, heure_ouverture, heure_fermeture, ferme FROM horaires ORDER BY id');
    $horaires = $requete->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($horaires, JSON_UNESCAPED_UNICODE);
} catch (Throwable $erreur) {
    http_response_code(503);
    echo json_encode(['erreur' => 'Horaires indisponibles']);
}
