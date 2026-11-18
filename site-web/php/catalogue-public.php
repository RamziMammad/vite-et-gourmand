<?php
function chargerCataloguePublic(PDO $pdo) {
    $menus = [];
    $plats = [];

    $requete = $pdo->query('SELECT p.id, p.nom, p.type_plat FROM plats p ORDER BY p.id');
    foreach ($requete->fetchAll(PDO::FETCH_ASSOC) as $plat) {
        $requete_allergenes = $pdo->prepare('SELECT a.nom FROM allergenes a INNER JOIN plats_allergenes pa ON pa.allergene_id = a.id WHERE pa.plat_id = ? ORDER BY a.nom');
        $requete_allergenes->execute([$plat['id']]);
        $noms_allergenes = $requete_allergenes->fetchAll(PDO::FETCH_COLUMN);
        $types = ['entree' => 'Entrée', 'plat' => 'Plat', 'dessert' => 'Dessert'];
        $plats[] = [
            'id' => 'plat-' . $plat['id'],
            'nom' => $plat['nom'],
            'type' => $types[$plat['type_plat']] ?? $plat['type_plat'],
            'allergenes' => $noms_allergenes ? implode(', ', $noms_allergenes) : 'Aucun allergène déclaré'
        ];
    }

    $requete = $pdo->query('SELECT m.*, t.nom AS theme, r.nom AS regime FROM menus m INNER JOIN themes t ON t.id = m.theme_id INNER JOIN regimes r ON r.id = m.regime_id WHERE m.actif = 1 ORDER BY m.id');
    foreach ($requete->fetchAll(PDO::FETCH_ASSOC) as $menu) {
        $requete_plats = $pdo->prepare('SELECT p.id, p.type_plat FROM menus_plats mp INNER JOIN plats p ON p.id = mp.plat_id WHERE mp.menu_id = ? ORDER BY p.id');
        $requete_plats->execute([$menu['id']]);
        $choix_plats = [];
        foreach ($requete_plats->fetchAll(PDO::FETCH_ASSOC) as $plat) {
            $choix_plats[$plat['type_plat']] = 'plat-' . $plat['id'];
        }
        $requete_photos = $pdo->prepare('SELECT chemin FROM photos_menus WHERE menu_id = ? ORDER BY position, id');
        $requete_photos->execute([$menu['id']]);
        $photos = $requete_photos->fetchAll(PDO::FETCH_COLUMN);
        $menus[$menu['code']] = [
            'nom' => $menu['titre'],
            'description' => $menu['description'],
            'theme' => $menu['theme'],
            'regime' => $menu['regime'],
            'minimum' => (int) $menu['personnes_minimum'],
            'prix' => (float) $menu['prix_minimum'],
            'stock' => (int) $menu['stock_disponible'],
            'conditions' => $menu['conditions_menu'],
            'delaiHeures' => (int) $menu['delai_heures'],
            'actif' => true,
            'photo' => $menu['photo_principale'],
            'galerie' => $photos,
            'plats' => $choix_plats
        ];
    }

    $horaires = [];
    $requete = $pdo->query('SELECT jour, heure_ouverture, heure_fermeture, ferme FROM horaires ORDER BY id');
    foreach ($requete->fetchAll(PDO::FETCH_ASSOC) as $horaire) {
        $horaires[] = (int) $horaire['ferme'] === 1 ? 'Fermé' :
            substr($horaire['heure_ouverture'], 0, 5) . ' - ' . substr($horaire['heure_fermeture'], 0, 5);
    }

    $avis = [];
    $requete = $pdo->query("SELECT note, commentaire FROM avis WHERE statut = 'valide' ORDER BY date_validation DESC, id DESC LIMIT 20");
    foreach ($requete->fetchAll(PDO::FETCH_ASSOC) as $ligne) {
        $avis[] = ['note' => (int) $ligne['note'], 'commentaire' => $ligne['commentaire'], 'statut' => 'valide'];
    }
    return ['menus' => $menus, 'plats' => $plats, 'horaires' => $horaires, 'avis' => $avis];
}
