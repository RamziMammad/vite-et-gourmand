<?php

// Connexion à la base MariaDB de Vite & Gourmand.
require __DIR__ . '/config.local.php';

$dsn = 'mysql:host=localhost;dbname=vite_et_gourmand;charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
];

try {
    $pdo = new PDO($dsn, $utilisateur, $mot_de_passe, $options);
} catch (PDOException $erreur) {
    die('Connexion impossible. Vérifie MySQL et les identifiants de la base.');
}
