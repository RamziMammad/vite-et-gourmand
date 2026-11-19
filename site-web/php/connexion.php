<?php

// Connexion a MariaDB : XAMPP en local, alwaysdata en ligne.
require __DIR__ . '/config.local.php';

if (!isset($hote_bdd)) {
    $hote_bdd = 'localhost';
}
if (!isset($nom_bdd)) {
    $nom_bdd = 'vite_et_gourmand';
}

$dsn = 'mysql:host=' . $hote_bdd . ';dbname=' . $nom_bdd . ';charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

try {
    $pdo = new PDO($dsn, $utilisateur, $mot_de_passe, $options);
} catch (PDOException $erreur) {
    die('Le site est momentanement indisponible. Veuillez reessayer plus tard.');
}
