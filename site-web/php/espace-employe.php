<?php
require __DIR__ . '/authentification.php';
verifierAcces([2, 3]);
require __DIR__ . '/connexion.php';

function texteEmploye($texte) {
    return htmlspecialchars((string) $texte, ENT_QUOTES, 'UTF-8');
}

function montantEmploye($montant) {
    return number_format((float) $montant, 2, ',', ' ') . ' €';
}

function saisieEmploye($nom) {
    return isset($_POST[$nom]) && is_string($_POST[$nom]) ? trim($_POST[$nom]) : '';
}

function erreurEmploye($message) {
    throw new InvalidArgumentException($message);
}

$statuts_employe = [
    'en_attente' => 'En attente',
    'attente' => 'En attente',
    'acceptee' => 'Acceptée',
    'preparation' => 'En préparation',
    'livraison' => 'En cours de livraison',
    'livree' => 'Livrée',
    'materiel' => 'En attente du retour de matériel',
    'terminee' => 'Terminée',
    'annulee' => 'Annulée'
];

$transitions_employe = [
    'en_attente' => ['acceptee'],
    'attente' => ['acceptee'],
    'acceptee' => ['preparation'],
    'preparation' => ['livraison'],
    'livraison' => ['livree'],
    'livree' => ['materiel', 'terminee'],
    'materiel' => ['terminee']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action_employe = saisieEmploye('action_employe');
    $destination_employe = '#gestion-commandes';
    $message_employe = '';
    $courriel_employe = null;

    if (!isset($_POST['csrf_token']) || !is_string($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message_employe = 'Votre formulaire a expiré. Actualisez la page puis recommencez.';
    } else {
        try {
            if (in_array($action_employe, ['statut', 'annuler', 'modifier'], true)) {
                $commande_id = (int) saisieEmploye('commande_id');
                $pdo->beginTransaction();
                $requete = $pdo->prepare('SELECT c.*, u.email, u.nom, u.prenom, m.personnes_minimum, m.prix_minimum, m.delai_heures FROM commandes c INNER JOIN utilisateurs u ON u.id = c.utilisateur_id INNER JOIN menus m ON m.id = c.menu_id WHERE c.id = ? FOR UPDATE');
                $requete->execute([$commande_id]);
                $commande_employe = $requete->fetch(PDO::FETCH_ASSOC);
                if (!$commande_employe) {
                    erreurEmploye('Cette commande est introuvable.');
                }

                if ($action_employe === 'statut') {
                    $nouveau_statut = saisieEmploye('nouveau_statut');
                    if (!in_array($nouveau_statut, $transitions_employe[$commande_employe['statut']] ?? [], true)) {
                        erreurEmploye('Le changement de statut demandé n’est pas autorisé.');
                    }
                    if ($commande_employe['statut'] === 'materiel' && $nouveau_statut === 'terminee' &&
                        saisieEmploye('materiel_restitue') !== 'oui') {
                        erreurEmploye('Confirmez le retour du matériel avant de terminer cette commande.');
                    }

                    $materiel_prete = (int) $commande_employe['materiel_prete'];
                    $date_notification = $commande_employe['date_notification_materiel'];
                    $date_retour = $commande_employe['date_retour_materiel'];
                    if ($nouveau_statut === 'materiel') {
                        $materiel_prete = 1;
                        $date_notification = date('Y-m-d H:i:s');
                    }
                    if ($commande_employe['statut'] === 'materiel' && $nouveau_statut === 'terminee') {
                        $date_retour = date('Y-m-d H:i:s');
                    }

                    $requete = $pdo->prepare('UPDATE commandes SET statut = ?, materiel_prete = ?, date_notification_materiel = ?, date_retour_materiel = ? WHERE id = ?');
                    $requete->execute([$nouveau_statut, $materiel_prete, $date_notification, $date_retour, $commande_id]);
                    $requete = $pdo->prepare('INSERT INTO historique_commandes (commande_id, utilisateur_id, statut, commentaire) VALUES (?, ?, ?, ?)');
                    $requete->execute([$commande_id, (int) $_SESSION['utilisateur_id'], $nouveau_statut, 'Statut mis à jour par l’équipe']);
                    $pdo->commit();
                    $message_employe = 'Le statut de la commande n°' . $commande_id . ' a été mis à jour.';

                    if ($nouveau_statut === 'materiel') {
                        $courriel_employe = [
                            $commande_employe['email'],
                            'Vite & Gourmand - Retour du materiel',
                            "Bonjour,\n\nVotre commande n°" . $commande_id . " est en attente du retour du matériel prêté.\nMerci de contacter Vite & Gourmand pour organiser le retour sous 10 jours ouvrés. Après ce délai, des frais de 600 euros peuvent être appliqués conformément aux conditions générales de vente.\n\nVite & Gourmand"
                        ];
                    } elseif ($nouveau_statut === 'terminee') {
                        $courriel_employe = [
                            $commande_employe['email'],
                            'Vite & Gourmand - Votre commande est terminee',
                            "Bonjour,\n\nVotre commande n°" . $commande_id . " est terminée. Vous pouvez vous connecter à votre espace client et donner votre avis.\n\nVite & Gourmand"
                        ];
                    }
                } elseif ($action_employe === 'annuler') {
                    if (in_array($commande_employe['statut'], ['annulee', 'livree', 'materiel', 'terminee'], true)) {
                        erreurEmploye('Cette commande ne peut plus être annulée.');
                    }
                    $mode = saisieEmploye('mode_contact');
                    $motif = saisieEmploye('motif');
                    if (!in_array($mode, ['telephone', 'email'], true) || strlen($motif) < 5 || strlen($motif) > 500 ||
                        saisieEmploye('contact_effectue') !== 'oui') {
                        erreurEmploye('Confirmez le contact avec le client, son mode et un motif précis.');
                    }
                    $requete = $pdo->prepare("UPDATE commandes SET statut = 'annulee', mode_contact_annulation = ?, motif_annulation = ? WHERE id = ?");
                    $requete->execute([$mode, $motif, $commande_id]);
                    $requete = $pdo->prepare('UPDATE menus SET stock_disponible = stock_disponible + 1 WHERE id = ?');
                    $requete->execute([(int) $commande_employe['menu_id']]);
                    $requete = $pdo->prepare('INSERT INTO historique_commandes (commande_id, utilisateur_id, statut, commentaire) VALUES (?, ?, ?, ?)');
                    $requete->execute([$commande_id, (int) $_SESSION['utilisateur_id'], 'annulee', 'Après contact (' . $mode . ') : ' . substr($motif, 0, 190)]);
                    $pdo->commit();
                    $message_employe = 'La commande n°' . $commande_id . ' a été annulée après contact avec le client.';
                } else {
                    if (in_array($commande_employe['statut'], ['annulee', 'livree', 'materiel', 'terminee'], true)) {
                        erreurEmploye('Cette commande ne peut plus être modifiée.');
                    }
                    $mode = saisieEmploye('mode_contact');
                    $motif = saisieEmploye('motif');
                    if (!in_array($mode, ['telephone', 'email'], true) || strlen($motif) < 5 || strlen($motif) > 190 ||
                        saisieEmploye('contact_effectue') !== 'oui') {
                        erreurEmploye('Le contact avec le client et le motif de modification sont obligatoires.');
                    }
                    $personnes_texte = saisieEmploye('nombre_personnes');
                    $personnes = (int) $personnes_texte;
                    $prestation = saisieEmploye('date_prestation');
                    $adresse_prestation = saisieEmploye('adresse_prestation');
                    $livraison = saisieEmploye('date_livraison');
                    $heure = saisieEmploye('heure_livraison');
                    $adresse_livraison = saisieEmploye('adresse_livraison');
                    $zone = saisieEmploye('ville_livraison');
                    $distance_texte = saisieEmploye('distance_km');
                    $distance = (float) $distance_texte;
                    $hors_bordeaux = $zone === 'hors-bordeaux';
                    $date_prestation = strtotime($prestation);
                    $date_livraison = strtotime($livraison . ' ' . $heure);
                    $dates_valides = $date_prestation !== false && $date_livraison !== false &&
                        date('Y-m-d', $date_prestation) === $prestation &&
                        date('Y-m-d H:i', $date_livraison) === $livraison . ' ' . $heure;

                    if (!ctype_digit($personnes_texte) || $personnes < (int) $commande_employe['personnes_minimum'] || $personnes > 5000 ||
                        $adresse_prestation === '' || $adresse_livraison === '' || strlen($adresse_prestation) > 255 || strlen($adresse_livraison) > 255 ||
                        !in_array($zone, ['bordeaux', 'hors-bordeaux'], true) ||
                        ($hors_bordeaux && (!is_numeric($distance_texte) || !is_finite($distance) || $distance <= 0 || $distance > 10000)) ||
                        !$dates_valides || $livraison > $prestation || $date_livraison < time() ||
                        $date_livraison < time() + (int) $commande_employe['delai_heures'] * 3600) {
                        erreurEmploye('Vérifiez les dates, le délai, les adresses, les personnes et la livraison.');
                    }

                    $prix_menu = round((float) $commande_employe['prix_minimum'] * $personnes / (int) $commande_employe['personnes_minimum'], 2);
                    $reduction = $personnes >= (int) $commande_employe['personnes_minimum'] + 5 ? round($prix_menu * 0.10, 2) : 0;
                    $frais = $hors_bordeaux ? round(5 + $distance * 0.59, 2) : 0;
                    $total = round($prix_menu - $reduction + $frais, 2);
                    if ($total > 99999999.99) {
                        erreurEmploye('Le montant de cette commande dépasse la limite autorisée.');
                    }
                    $requete = $pdo->prepare('UPDATE commandes SET nombre_personnes = ?, date_prestation = ?, adresse_prestation = ?, date_livraison = ?, heure_livraison = ?, adresse_livraison = ?, hors_bordeaux = ?, distance_km = ?, prix_menu = ?, reduction = ?, frais_livraison = ?, prix_total = ? WHERE id = ?');
                    $requete->execute([$personnes, $prestation, $adresse_prestation, $livraison, $heure, $adresse_livraison, (int) $hors_bordeaux, $hors_bordeaux ? $distance : 0, $prix_menu, $reduction, $frais, $total, $commande_id]);
                    $requete = $pdo->prepare('INSERT INTO historique_commandes (commande_id, utilisateur_id, statut, commentaire) VALUES (?, ?, ?, ?)');
                    $requete->execute([$commande_id, (int) $_SESSION['utilisateur_id'], $commande_employe['statut'], 'Modification après contact (' . $mode . ') : ' . $motif]);
                    $pdo->commit();
                    $message_employe = 'La commande n°' . $commande_id . ' a été modifiée. Nouveau total : ' . montantEmploye($total) . '.';
                }
            } elseif ($action_employe === 'avis') {
                $destination_employe = '#avis';
                $avis_id = (int) saisieEmploye('avis_id');
                $decision = saisieEmploye('decision');
                if (!in_array($decision, ['valide', 'refuse'], true)) {
                    erreurEmploye('Choisissez une décision valide pour cet avis.');
                }
                $requete = $pdo->prepare("UPDATE avis SET statut = ?, date_validation = NOW() WHERE id = ? AND statut = 'en_attente'");
                $requete->execute([$decision, $avis_id]);
                if ($requete->rowCount() !== 1) {
                    erreurEmploye('Cet avis a déjà été traité ou n’existe plus.');
                }
                $message_employe = $decision === 'valide' ? 'L’avis a été validé.' : 'L’avis a été refusé.';
            } elseif (in_array($action_employe, ['menu', 'menu_actif'], true)) {
                $destination_employe = '#menus';
                $menu_id = (int) saisieEmploye('menu_id');
                $requete = $pdo->prepare('SELECT id, actif FROM menus WHERE id = ?');
                $requete->execute([$menu_id]);
                $menu = $requete->fetch(PDO::FETCH_ASSOC);
                if (!$menu) {
                    erreurEmploye('Ce menu est introuvable.');
                }
                if ($action_employe === 'menu_actif') {
                    $requete = $pdo->prepare('UPDATE menus SET actif = ? WHERE id = ?');
                    $requete->execute([$menu['actif'] ? 0 : 1, $menu_id]);
                    $message_employe = $menu['actif'] ? 'Le menu a été retiré.' : 'Le menu a été rétabli.';
                } else {
                    $titre = saisieEmploye('titre');
                    $description = saisieEmploye('description');
                    $conditions = saisieEmploye('conditions');
                    $theme_id = (int) saisieEmploye('theme_id');
                    $regime_id = (int) saisieEmploye('regime_id');
                    $minimum_texte = saisieEmploye('personnes_minimum');
                    $stock_texte = saisieEmploye('stock_disponible');
                    $delai_texte = saisieEmploye('delai_heures');
                    $prix_texte = saisieEmploye('prix_minimum');
                    $prix = (float) $prix_texte;
                    if ($titre === '' || strlen($titre) > 100 || $description === '' || $conditions === '' ||
                        strlen($description) > 5000 || strlen($conditions) > 5000 ||
                        !ctype_digit($minimum_texte) || (int) $minimum_texte < 1 || (int) $minimum_texte > 5000 ||
                        !ctype_digit($stock_texte) || (int) $stock_texte > 100000 ||
                        !ctype_digit($delai_texte) || (int) $delai_texte < 1 || (int) $delai_texte > 10000 ||
                        !is_numeric($prix_texte) || !is_finite($prix) || $prix <= 0 || $prix > 1000000) {
                        erreurEmploye('Vérifiez les informations du menu.');
                    }
                    $requete = $pdo->prepare('SELECT id FROM themes WHERE id = ?');
                    $requete->execute([$theme_id]);
                    if (!$requete->fetch()) {
                        erreurEmploye('Le thème sélectionné n’existe pas.');
                    }
                    $requete = $pdo->prepare('SELECT id FROM regimes WHERE id = ?');
                    $requete->execute([$regime_id]);
                    if (!$requete->fetch()) {
                        erreurEmploye('Le régime sélectionné n’existe pas.');
                    }
                    $requete = $pdo->prepare('UPDATE menus SET titre = ?, description = ?, theme_id = ?, regime_id = ?, personnes_minimum = ?, prix_minimum = ?, stock_disponible = ?, delai_heures = ?, conditions_menu = ? WHERE id = ?');
                    $requete->execute([$titre, $description, $theme_id, $regime_id, (int) $minimum_texte, $prix, (int) $stock_texte, (int) $delai_texte, $conditions, $menu_id]);
                    $message_employe = 'Les modifications du menu ont été enregistrées.';
                }
            } elseif (in_array($action_employe, ['plat', 'plat_supprimer'], true)) {
                $destination_employe = '#plats';
                $plat_id = (int) saisieEmploye('plat_id');
                $requete = $pdo->prepare('SELECT id FROM plats WHERE id = ?');
                $requete->execute([$plat_id]);
                if (!$requete->fetch()) {
                    erreurEmploye('Ce plat est introuvable.');
                }
                if ($action_employe === 'plat_supprimer') {
                    $requete = $pdo->prepare('SELECT plat_id FROM menus_plats WHERE plat_id = ? LIMIT 1');
                    $requete->execute([$plat_id]);
                    if ($requete->fetch()) {
                        erreurEmploye('Ce plat appartient à un menu. Retirez son association avant de le supprimer.');
                    }
                    $pdo->beginTransaction();
                    $requete = $pdo->prepare('DELETE FROM plats_allergenes WHERE plat_id = ?');
                    $requete->execute([$plat_id]);
                    $requete = $pdo->prepare('DELETE FROM plats WHERE id = ?');
                    $requete->execute([$plat_id]);
                    $pdo->commit();
                    $message_employe = 'Le plat a été supprimé.';
                } else {
                    $nom = saisieEmploye('nom');
                    $type = saisieEmploye('type_plat');
                    $allergenes = $_POST['allergenes'] ?? [];
                    if ($nom === '' || strlen($nom) > 200 || !in_array($type, ['entree', 'plat', 'dessert'], true) || !is_array($allergenes)) {
                        erreurEmploye('Vérifiez le nom, le type et les allergènes du plat.');
                    }
                    $ids_allergenes = [];
                    foreach ($allergenes as $id) {
                        if (!ctype_digit((string) $id)) {
                            erreurEmploye('La liste des allergènes est invalide.');
                        }
                        $ids_allergenes[] = (int) $id;
                    }
                    if (count($ids_allergenes) > 100 || count($ids_allergenes) !== count(array_unique($ids_allergenes))) {
                        erreurEmploye('La liste des allergènes est invalide.');
                    }
                    $pdo->beginTransaction();
                    $requete = $pdo->prepare('UPDATE plats SET nom = ?, type_plat = ? WHERE id = ?');
                    $requete->execute([$nom, $type, $plat_id]);
                    $requete = $pdo->prepare('DELETE FROM plats_allergenes WHERE plat_id = ?');
                    $requete->execute([$plat_id]);
                    $requete = $pdo->prepare('INSERT INTO plats_allergenes (plat_id, allergene_id) VALUES (?, ?)');
                    foreach ($ids_allergenes as $id) {
                        $requete->execute([$plat_id, $id]);
                    }
                    $pdo->commit();
                    $message_employe = 'Le plat et ses allergènes ont été enregistrés.';
                }
            } elseif ($action_employe === 'horaire') {
                $destination_employe = '#horaires';
                $jour_id = (int) saisieEmploye('jour_id');
                $ferme = saisieEmploye('ferme') === 'oui' ? 1 : 0;
                $ouverture = saisieEmploye('ouverture');
                $fermeture = saisieEmploye('fermeture');
                if ($jour_id < 1 || $jour_id > 7 || (!$ferme &&
                    (!preg_match('/^\d{2}:\d{2}$/', $ouverture) || !preg_match('/^\d{2}:\d{2}$/', $fermeture) ||
                    strtotime($ouverture) === false || strtotime($fermeture) === false || $ouverture >= $fermeture))) {
                    erreurEmploye('Vérifiez les heures d’ouverture et de fermeture.');
                }
                $requete = $pdo->prepare('UPDATE horaires SET heure_ouverture = ?, heure_fermeture = ?, ferme = ? WHERE id = ?');
                $requete->execute([$ferme ? null : $ouverture, $ferme ? null : $fermeture, $ferme, $jour_id]);
                if ($requete->rowCount() === 0) {
                    $requete = $pdo->prepare('SELECT id FROM horaires WHERE id = ?');
                    $requete->execute([$jour_id]);
                    if (!$requete->fetch()) {
                        erreurEmploye('Ce jour n’existe pas dans la base.');
                    }
                }
                $message_employe = 'Les horaires ont été enregistrés.';
            } else {
                erreurEmploye('Cette action n’est pas reconnue.');
            }
        } catch (InvalidArgumentException $erreur) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $message_employe = $erreur->getMessage();
        } catch (Throwable $erreur) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $message_employe = 'Impossible d’enregistrer cette modification. Réessayez.';
        }
    }

    if ($courriel_employe !== null) {
        require __DIR__ . '/parametres-site.php';
        $envoye = filter_var($email_expediteur, FILTER_VALIDATE_EMAIL) &&
            strpbrk($email_expediteur, "\r\n") === false &&
            mail($courriel_employe[0], $courriel_employe[1], $courriel_employe[2],
                'From: ' . $email_expediteur . "\r\nContent-Type: text/plain; charset=UTF-8");
        if (!$envoye) {
            $message_employe .= ' L’e-mail de notification n’a pas pu être envoyé.';
        }
    }
    $_SESSION['message_employe'] = $message_employe;
    header('Location: espace-employe.php');
    exit;
}

$message_employe = $_SESSION['message_employe'] ?? '';
unset($_SESSION['message_employe']);

$filtre_statut = isset($_GET['statut']) && is_string($_GET['statut']) ? $_GET['statut'] : 'tous';
if ($filtre_statut === 'attente') {
    $filtre_statut = 'en_attente';
}
$filtre_client = isset($_GET['client']) && is_string($_GET['client']) && ctype_digit($_GET['client']) ? (int) $_GET['client'] : 0;
if ($filtre_statut !== 'tous' && !array_key_exists($filtre_statut, $statuts_employe)) {
    $filtre_statut = 'tous';
}

$sql = 'SELECT c.*, u.nom, u.prenom, u.email, u.telephone, m.titre AS titre_menu FROM commandes c INNER JOIN utilisateurs u ON u.id = c.utilisateur_id INNER JOIN menus m ON m.id = c.menu_id WHERE 1 = 1';
$parametres = [];
if ($filtre_statut === 'en_attente') {
    $sql .= ' AND c.statut IN (?, ?)';
    $parametres[] = 'en_attente';
    $parametres[] = 'attente';
} elseif ($filtre_statut !== 'tous') {
    $sql .= ' AND c.statut = ?';
    $parametres[] = $filtre_statut;
}
if ($filtre_client > 0) {
    $sql .= ' AND c.utilisateur_id = ?';
    $parametres[] = $filtre_client;
}
$sql .= ' ORDER BY c.date_commande DESC, c.id DESC';
$requete = $pdo->prepare($sql);
$requete->execute($parametres);
$commandes_employe = $requete->fetchAll(PDO::FETCH_ASSOC);

$requete = $pdo->query('SELECT c.id, c.statut, u.nom, u.prenom FROM commandes c INNER JOIN utilisateurs u ON u.id = c.utilisateur_id ORDER BY c.id DESC');
$toutes_commandes = $requete->fetchAll(PDO::FETCH_ASSOC);
$clients_employe = $pdo->query('SELECT DISTINCT u.id, u.nom, u.prenom FROM commandes c INNER JOIN utilisateurs u ON u.id = c.utilisateur_id ORDER BY u.nom, u.prenom')->fetchAll(PDO::FETCH_ASSOC);

$commande_selectionnee_id = isset($_GET['commande']) && is_string($_GET['commande']) && ctype_digit($_GET['commande']) ? (int) $_GET['commande'] : 0;
$commande_selectionnee = null;
foreach ($toutes_commandes as $commande) {
    if ($commande_selectionnee_id === 0 && !in_array($commande['statut'], ['annulee', 'livree', 'materiel', 'terminee'], true)) {
        $commande_selectionnee_id = (int) $commande['id'];
        break;
    }
}
if ($commande_selectionnee_id > 0) {
    $requete = $pdo->prepare('SELECT c.*, m.titre AS titre_menu, u.nom, u.prenom FROM commandes c INNER JOIN menus m ON m.id = c.menu_id INNER JOIN utilisateurs u ON u.id = c.utilisateur_id WHERE c.id = ?');
    $requete->execute([$commande_selectionnee_id]);
    $commande_selectionnee = $requete->fetch(PDO::FETCH_ASSOC);
}

$historique_employe = [];
$requete = $pdo->prepare('SELECT statut, date_changement, commentaire FROM historique_commandes WHERE commande_id = ? ORDER BY date_changement, id');
foreach ($commandes_employe as $commande) {
    $requete->execute([$commande['id']]);
    $historique_employe[$commande['id']] = $requete->fetchAll(PDO::FETCH_ASSOC);
}

$avis_employe = $pdo->query("SELECT a.*, u.nom, u.prenom FROM avis a INNER JOIN commandes c ON c.id = a.commande_id INNER JOIN utilisateurs u ON u.id = c.utilisateur_id ORDER BY CASE WHEN a.statut = 'en_attente' THEN 0 ELSE 1 END, a.date_creation DESC")->fetchAll(PDO::FETCH_ASSOC);
$menus_employe = $pdo->query('SELECT * FROM menus ORDER BY titre')->fetchAll(PDO::FETCH_ASSOC);
$themes_employe = $pdo->query('SELECT id, nom FROM themes ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
$regimes_employe = $pdo->query('SELECT id, nom FROM regimes ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
$plats_employe = $pdo->query('SELECT id, nom, type_plat FROM plats ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
$allergenes_employe = $pdo->query('SELECT id, nom FROM allergenes ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
$horaires_employe = $pdo->query('SELECT id, jour, heure_ouverture, heure_fermeture, ferme FROM horaires ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);

$menu_choisi = isset($_GET['menu']) && is_string($_GET['menu']) && ctype_digit($_GET['menu']) ? (int) $_GET['menu'] : 0;
$menu_selectionne = null;
foreach ($menus_employe as $menu) {
    if ($menu_choisi === 0 || (int) $menu['id'] === $menu_choisi) {
        $menu_selectionne = $menu;
        break;
    }
}

$plat_choisi = isset($_GET['plat']) && is_string($_GET['plat']) && ctype_digit($_GET['plat']) ? (int) $_GET['plat'] : 0;
$plat_selectionne = null;
foreach ($plats_employe as $plat) {
    if ($plat_choisi === 0 || (int) $plat['id'] === $plat_choisi) {
        $plat_selectionne = $plat;
        break;
    }
}
$allergenes_du_plat = [];
if ($plat_selectionne) {
    $requete = $pdo->prepare('SELECT allergene_id FROM plats_allergenes WHERE plat_id = ?');
    $requete->execute([(int) $plat_selectionne['id']]);
    $allergenes_du_plat = array_map('intval', $requete->fetchAll(PDO::FETCH_COLUMN));
}

$jour_choisi = isset($_GET['jour']) && is_string($_GET['jour']) && ctype_digit($_GET['jour']) ? (int) $_GET['jour'] : 0;
$horaire_selectionne = null;
foreach ($horaires_employe as $horaire) {
    if ($jour_choisi === 0 || (int) $horaire['id'] === $jour_choisi) {
        $horaire_selectionne = $horaire;
        break;
    }
}
