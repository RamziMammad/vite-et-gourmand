<?php
session_start();

function pagePourRole($role) {
    if ($role === 3) {
        return 'espace-administrateur.php';
    }
    if ($role === 2) {
        return 'espace-employe.php';
    }
    return 'espace-utilisateur.php';
}

function verifierAcces($rolesAutorises) {
    if (!isset($_SESSION['utilisateur_id'])) {
        header('Location: connexion.php');
        exit;
    }

    require __DIR__ . '/connexion.php';
    $requete = $pdo->prepare('SELECT role_id, actif FROM utilisateurs WHERE id = ?');
    $requete->execute([$_SESSION['utilisateur_id']]);
    $compte = $requete->fetch(PDO::FETCH_ASSOC);

    if (!$compte || (int) $compte['actif'] !== 1) {
        $_SESSION = [];
        session_destroy();
        header('Location: connexion.php');
        exit;
    }

    $_SESSION['role_id'] = (int) $compte['role_id'];
    if (!in_array($_SESSION['role_id'], $rolesAutorises, true)) {
        header('HTTP/1.1 403 Forbidden');
        exit('Accès réservé à un autre type de compte.');
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
