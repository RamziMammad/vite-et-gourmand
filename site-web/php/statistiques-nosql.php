<?php
function requeteNoSQL($methode, $chemin, $contenu = null) {
    if (!is_file(__DIR__ . '/nosql.local.php')) {
        throw new RuntimeException('Connexion NoSQL indisponible.');
    }

    require __DIR__ . '/nosql.local.php';
    if (empty($couchdb_url) || empty($couchdb_utilisateur) || !isset($couchdb_mot_de_passe)) {
        throw new RuntimeException('Configuration NoSQL incomplète.');
    }

    $identifiants = base64_encode($couchdb_utilisateur . ':' . $couchdb_mot_de_passe);
    $options = [
        'method' => $methode,
        'timeout' => 8,
        'ignore_errors' => true,
        'header' => "Authorization: Basic " . $identifiants . "\r\n" .
            "Accept: application/json\r\nContent-Type: application/json\r\n"
    ];
    if ($contenu !== null) {
        $options['content'] = json_encode($contenu, JSON_UNESCAPED_UNICODE);
    }

    $contexte = stream_context_create(['http' => $options]);
    $reponse = @file_get_contents(rtrim($couchdb_url, '/') . $chemin, false, $contexte);
    $code = 0;
    if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $resultat)) {
        $code = (int) $resultat[1];
    }
    if ($reponse === false || $code === 0) {
        throw new RuntimeException('Le serveur NoSQL ne répond pas.');
    }
    return [$code, json_decode($reponse, true)];
}

function mettreAJourStatistiquesNoSQL(PDO $pdo) {
    $base = '/vite_gourmand_stats';
    [$code] = requeteNoSQL('PUT', $base);
    if (!in_array($code, [201, 202, 412], true)) {
        throw new RuntimeException('Impossible de préparer la base NoSQL.');
    }

    $requete = $pdo->query('SELECT c.id, c.date_commande, c.prix_total, c.statut, m.id AS menu_id, m.titre AS menu_titre FROM commandes c INNER JOIN menus m ON m.id = c.menu_id ORDER BY c.id');
    $commandes = [];
    foreach ($requete->fetchAll(PDO::FETCH_ASSOC) as $ligne) {
        $commandes[] = [
            'id' => (int) $ligne['id'],
            'date' => substr($ligne['date_commande'], 0, 10),
            'menu_id' => (int) $ligne['menu_id'],
            'menu' => $ligne['menu_titre'],
            'statut' => $ligne['statut'],
            'total' => (float) $ligne['prix_total']
        ];
    }

    for ($essai = 0; $essai < 2; $essai++) {
        [$code, $ancien] = requeteNoSQL('GET', $base . '/commandes');
        if (!in_array($code, [200, 404], true)) {
            throw new RuntimeException('Lecture NoSQL impossible.');
        }
        $document = ['_id' => 'commandes', 'commandes' => $commandes];
        if ($code === 200 && isset($ancien['_rev'])) {
            $document['_rev'] = $ancien['_rev'];
        }
        [$code] = requeteNoSQL('PUT', $base . '/commandes', $document);
        if (in_array($code, [201, 202], true)) {
            [$code, $donnees] = requeteNoSQL('GET', $base . '/commandes');
            if ($code !== 200 || !isset($donnees['commandes']) || !is_array($donnees['commandes'])) {
                throw new RuntimeException('Statistiques NoSQL indisponibles.');
            }
            return $donnees['commandes'];
        }
        if ($code !== 409) {
            break;
        }
    }
    throw new RuntimeException('Impossible de synchroniser les statistiques NoSQL.');
}
