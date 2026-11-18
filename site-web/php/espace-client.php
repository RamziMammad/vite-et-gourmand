<?php
require __DIR__ . '/authentification.php';
verifierAcces([1]);
require __DIR__ . '/connexion.php';

$utilisateur_id = (int) $_SESSION['utilisateur_id'];

function protegerClient($texte) {
    return htmlspecialchars((string) $texte, ENT_QUOTES, 'UTF-8');
}

function prixClientPHP($montant) {
    return number_format((float) $montant, 2, ',', ' ') . ' €';
}

function dateClientPHP($date) {
    return $date ? date('d/m/Y', strtotime($date)) : '';
}

function champClient($nom) {
    return isset($_POST[$nom]) && is_string($_POST[$nom]) ? trim($_POST[$nom]) : '';
}

function retourClient($message, $destination) {
    $_SESSION['message_client'] = $message;
    header('Location: espace-utilisateur.php' . $destination);
    exit;
}

$statuts_client = [
    'en_attente' => 'En attente de validation',
    'attente' => 'En attente de validation',
    'acceptee' => 'Acceptée',
    'preparation' => 'En préparation',
    'livraison' => 'En cours de livraison',
    'livree' => 'Livrée',
    'materiel' => 'En attente du retour de matériel',
    'terminee' => 'Terminée',
    'annulee' => 'Annulée'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = champClient('action_client');
    $commande_id = (int) champClient('commande_id');
    $destination = $action === 'profil' ? '#mes-informations' : ($action === 'avis' ? '#avis-client' : '?commande=' . $commande_id . '#mes-commandes');

    if (!isset($_POST['csrf_token']) || !is_string($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        retourClient('Votre formulaire a expiré. Actualisez la page puis recommencez.', $destination);
    }

    try {
        if ($action === 'profil') {
            $nom = champClient('nom');
            $prenom = champClient('prenom');
            $email = champClient('email');
            $telephone = champClient('telephone');
            $adresse = champClient('adresse');

            if ($nom === '' || $prenom === '' || $email === '' || $telephone === '' || $adresse === '' ||
                strlen($nom) > 100 || strlen($prenom) > 100 || strlen($email) > 190 ||
                strlen($telephone) > 30 || strlen($adresse) > 255 ||
                !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                retourClient('Vérifiez vos informations personnelles.', $destination);
            }

            $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? AND id <> ?');
            $requete->execute([$email, $utilisateur_id]);
            if ($requete->fetch()) {
                retourClient('Cette adresse e-mail est déjà utilisée par un autre compte.', $destination);
            }

            $requete = $pdo->prepare('UPDATE utilisateurs SET nom = ?, prenom = ?, telephone = ?, email = ?, adresse_postale = ? WHERE id = ? AND actif = 1');
            $requete->execute([$nom, $prenom, $telephone, $email, $adresse, $utilisateur_id]);
            retourClient('Vos informations ont été enregistrées.', $destination);
        }

        if ($action === 'annuler' || $action === 'modifier') {
            $pdo->beginTransaction();
            $requete = $pdo->prepare('SELECT c.*, m.personnes_minimum, m.prix_minimum, m.delai_heures FROM commandes c INNER JOIN menus m ON c.menu_id = m.id WHERE c.id = ? AND c.utilisateur_id = ? FOR UPDATE');
            $requete->execute([$commande_id, $utilisateur_id]);
            $commande = $requete->fetch(PDO::FETCH_ASSOC);

            if (!$commande || !in_array($commande['statut'], ['en_attente', 'attente'], true)) {
                $pdo->rollBack();
                retourClient('Cette commande ne peut plus être modifiée ou annulée.', $destination);
            }

            if ($action === 'annuler') {
                $requete = $pdo->prepare("UPDATE commandes SET statut = 'annulee' WHERE id = ? AND utilisateur_id = ?");
                $requete->execute([$commande_id, $utilisateur_id]);

                $requete = $pdo->prepare('UPDATE menus SET stock_disponible = stock_disponible + 1 WHERE id = ?');
                $requete->execute([$commande['menu_id']]);

                $requete = $pdo->prepare('INSERT INTO historique_commandes (commande_id, utilisateur_id, statut, commentaire) VALUES (?, ?, ?, ?)');
                $requete->execute([$commande_id, $utilisateur_id, 'annulee', 'Annulation par le client']);
                $pdo->commit();
                retourClient('Votre commande n°' . $commande_id . ' a été annulée.', $destination);
            }

            $personnes_texte = champClient('nombre_personnes');
            $date_prestation = champClient('date_prestation');
            $adresse_prestation = champClient('adresse_prestation');
            $date_livraison = champClient('date_livraison');
            $heure_livraison = champClient('heure_livraison');
            $adresse_livraison = champClient('adresse_livraison');
            $zone = champClient('ville_livraison');
            $distance_texte = champClient('distance');
            $personnes = (int) $personnes_texte;
            $distance = (float) $distance_texte;
            $hors_bordeaux = $zone === 'hors-bordeaux';
            $livraison = strtotime($date_livraison . ' ' . $heure_livraison);
            $prestation = strtotime($date_prestation);
            $date_valide = $prestation !== false && $livraison !== false &&
                date('Y-m-d', $prestation) === $date_prestation &&
                date('Y-m-d H:i', $livraison) === $date_livraison . ' ' . $heure_livraison;

            if ($adresse_prestation === '' || $adresse_livraison === '' ||
                strlen($adresse_prestation) > 255 || strlen($adresse_livraison) > 255 ||
                !ctype_digit($personnes_texte) || $personnes < (int) $commande['personnes_minimum'] || $personnes > 5000 ||
                !in_array($zone, ['bordeaux', 'hors-bordeaux'], true) ||
                ($hors_bordeaux && (!is_numeric($distance_texte) || !is_finite($distance) || $distance <= 0 || $distance > 10000)) ||
                !$date_valide || $date_livraison > $date_prestation ||
                $livraison < time() + (int) $commande['delai_heures'] * 3600) {
                $pdo->rollBack();
                retourClient('Vérifiez les adresses, les dates, le nombre de personnes et la distance de livraison.', $destination);
            }

            $prix_menu = round((float) $commande['prix_minimum'] * $personnes / (int) $commande['personnes_minimum'], 2);
            $reduction = $personnes >= (int) $commande['personnes_minimum'] + 5 ? round($prix_menu * 0.10, 2) : 0;
            $frais = $hors_bordeaux ? round(5 + $distance * 0.59, 2) : 0;
            $total = round($prix_menu - $reduction + $frais, 2);
            if ($total > 99999999.99) {
                $pdo->rollBack();
                retourClient('Le montant de cette commande dépasse la limite autorisée.', $destination);
            }

            $requete = $pdo->prepare('UPDATE commandes SET date_prestation = ?, adresse_prestation = ?, date_livraison = ?, heure_livraison = ?, adresse_livraison = ?, hors_bordeaux = ?, distance_km = ?, nombre_personnes = ?, prix_menu = ?, reduction = ?, frais_livraison = ?, prix_total = ? WHERE id = ? AND utilisateur_id = ?');
            $requete->execute([
                $date_prestation, $adresse_prestation, $date_livraison, $heure_livraison,
                $adresse_livraison, (int) $hors_bordeaux, $hors_bordeaux ? $distance : 0,
                $personnes, $prix_menu, $reduction, $frais, $total, $commande_id, $utilisateur_id
            ]);
            $requete = $pdo->prepare('INSERT INTO historique_commandes (commande_id, utilisateur_id, statut, commentaire) VALUES (?, ?, ?, ?)');
            $requete->execute([$commande_id, $utilisateur_id, $commande['statut'], 'Commande modifiée par le client']);
            $pdo->commit();
            retourClient('Les modifications de votre commande n°' . $commande_id . ' ont été enregistrées.', $destination);
        }

        if ($action === 'avis') {
            $note_texte = champClient('note');
            $note = (int) $note_texte;
            $commentaire = champClient('commentaire');
            if (!ctype_digit($note_texte) || $note < 1 || $note > 5 || strlen($commentaire) < 3 || strlen($commentaire) > 1500) {
                retourClient('Indiquez une note de 1 à 5 et un commentaire de 3 à 1 500 caractères.', $destination);
            }
            $requete = $pdo->prepare("SELECT c.id FROM commandes c LEFT JOIN avis a ON a.commande_id = c.id WHERE c.id = ? AND c.utilisateur_id = ? AND c.statut = 'terminee' AND a.id IS NULL");
            $requete->execute([$commande_id, $utilisateur_id]);
            if (!$requete->fetch()) {
                retourClient('Un avis est possible uniquement pour une commande terminée et non encore évaluée.', $destination);
            }
            $requete = $pdo->prepare('INSERT INTO avis (commande_id, note, commentaire, statut) VALUES (?, ?, ?, ?)');
            $requete->execute([$commande_id, $note, $commentaire, 'en_attente']);
            retourClient('Merci pour votre avis. Il a bien été envoyé.', $destination);
        }

        retourClient('Action non reconnue.', '#mes-commandes');
    } catch (PDOException $erreur) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        retourClient('Une erreur est survenue. Veuillez réessayer.', $destination);
    }
}

$message_client = $_SESSION['message_client'] ?? '';
unset($_SESSION['message_client']);

$requete = $pdo->prepare('SELECT nom, prenom, telephone, email, adresse_postale FROM utilisateurs WHERE id = ? AND actif = 1');
$requete->execute([$utilisateur_id]);
$profil_client = $requete->fetch(PDO::FETCH_ASSOC);

$requete = $pdo->prepare('SELECT c.*, m.titre AS nom_menu, m.personnes_minimum, m.delai_heures, a.id AS avis_id, a.note AS avis_note, a.statut AS avis_statut FROM commandes c INNER JOIN menus m ON m.id = c.menu_id LEFT JOIN avis a ON a.commande_id = c.id WHERE c.utilisateur_id = ? ORDER BY c.date_commande DESC, c.id DESC');
$requete->execute([$utilisateur_id]);
$commandes_client = $requete->fetchAll(PDO::FETCH_ASSOC);

$numero_selectionne = (int) ($_GET['commande'] ?? 0);
$commande_selectionnee = null;
foreach ($commandes_client as $commande) {
    if ($numero_selectionne === (int) $commande['id']) {
        $commande_selectionnee = $commande;
        break;
    }
}
if (!$commande_selectionnee && count($commandes_client) > 0) {
    $commande_selectionnee = $commandes_client[0];
}

$historique_client = [];
if ($commande_selectionnee) {
    $requete = $pdo->prepare('SELECT statut, date_changement, commentaire FROM historique_commandes WHERE commande_id = ? ORDER BY date_changement, id');
    $requete->execute([$commande_selectionnee['id']]);
    $historique_client = $requete->fetchAll(PDO::FETCH_ASSOC);
}

$commandes_a_evaluer = [];
foreach ($commandes_client as $commande) {
    if ($commande['statut'] === 'terminee' && !$commande['avis_id']) {
        $commandes_a_evaluer[] = $commande;
    }
}
