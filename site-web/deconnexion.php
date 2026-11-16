<?php
require __DIR__ . '/php/authentification.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('HTTP/1.1 403 Forbidden');
    exit('Déconnexion refusée.');
}

$_SESSION = [];
session_destroy();
header('Location: connexion.php?deconnexion=1');
exit;
