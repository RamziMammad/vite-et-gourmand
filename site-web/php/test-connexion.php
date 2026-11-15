<?php

// Vérifie la connexion en affichant les menus enregistrés.
require __DIR__ . '/connexion.php';

$requete = $pdo->prepare('SELECT titre, personnes_minimum, prix_minimum FROM menus ORDER BY id');
$requete->execute();
$menus = $requete->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test de la base - Vite & Gourmand</title>
</head>
<body>
    <h1>Connexion à la base réussie</h1>
    <?php
    foreach ($menus as $menu) {
        echo '<p>';
        echo htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8');
        echo ' : ' . $menu['personnes_minimum'] . ' personnes, ';
        echo $menu['prix_minimum'] . ' euros';
        echo '</p>';
    }
    ?>
</body>
</html>
