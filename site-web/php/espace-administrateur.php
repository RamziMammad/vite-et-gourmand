<?php
require __DIR__ . '/authentification.php';
verifierAcces([3]);
require __DIR__ . '/connexion.php';
require __DIR__ . '/statistiques-nosql.php';

function affichageAdmin($valeur) {
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

function motDePasseValideAdmin($mot_de_passe) {
    return strlen($mot_de_passe) >= 10 &&
        preg_match('/[a-z]/', $mot_de_passe) &&
        preg_match('/[A-Z]/', $mot_de_passe) &&
        preg_match('/[0-9]/', $mot_de_passe) &&
        preg_match('/[^a-zA-Z0-9]/', $mot_de_passe);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_admin = '';
    if (!isset($_POST['csrf_token']) || !is_string($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message_admin = 'Votre formulaire a expiré. Actualisez la page.';
    } else {
        $action = isset($_POST['action_admin']) && is_string($_POST['action_admin']) ? $_POST['action_admin'] : '';
        try {
            if ($action === 'creer_employe') {
                $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
                $mot_de_passe_saisi = isset($_POST['mot_de_passe']) && is_string($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';
                if (strlen($email) > 190 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !motDePasseValideAdmin($mot_de_passe_saisi)) {
                    throw new InvalidArgumentException('Vérifiez l’adresse e-mail et le mot de passe (10 caractères, majuscule, minuscule, chiffre et caractère spécial).');
                }
                $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
                $requete->execute([$email]);
                if ($requete->fetchColumn()) {
                    throw new InvalidArgumentException('Un compte utilise déjà cette adresse e-mail.');
                }

                $hash = password_hash($mot_de_passe_saisi, PASSWORD_DEFAULT);
                $requete = $pdo->prepare("INSERT INTO utilisateurs (role_id, nom, prenom, telephone, email, adresse_postale, mot_de_passe, actif) VALUES (2, '', '', '', ?, '', ?, 1)");
                $requete->execute([$email, $hash]);

                $texte = "Bonjour,\n\nUn compte employé Vite & Gourmand a été créé pour cette adresse.\nContactez votre administrateur pour obtenir votre mot de passe.\n\nVite & Gourmand";
                require __DIR__ . '/parametres-site.php';
                $entetes = "From: " . $email_expediteur . "\r\nContent-Type: text/plain; charset=UTF-8";
                $email_envoye = filter_var($email_expediteur, FILTER_VALIDATE_EMAIL) &&
                    strpbrk($email_expediteur, "\r\n") === false &&
                    mail($email, 'Vite & Gourmand - Votre compte employe', $texte, $entetes);
                $message_admin = $email_envoye ? 'Le compte employé a été créé et l’e-mail a été envoyé.' : 'Le compte employé a été créé, mais l’e-mail n’a pas pu être envoyé.';
            } elseif ($action === 'desactiver_employe') {
                $id_texte = isset($_POST['employe_id']) && is_string($_POST['employe_id']) ? $_POST['employe_id'] : '';
                if (!ctype_digit($id_texte) || (int) $id_texte < 1) {
                    throw new InvalidArgumentException('Compte employé invalide.');
                }
                $requete = $pdo->prepare('UPDATE utilisateurs SET actif = 0 WHERE id = ? AND role_id = 2 AND actif = 1');
                $requete->execute([(int) $id_texte]);
                $message_admin = $requete->rowCount() === 1 ? 'Le compte employé a été désactivé.' : 'Ce compte est déjà désactivé ou introuvable.';
            } else {
                throw new InvalidArgumentException('Action non reconnue.');
            }
        } catch (InvalidArgumentException $erreur) {
            $message_admin = $erreur->getMessage();
        } catch (PDOException $erreur) {
            $message_admin = 'Cette opération n’a pas pu être enregistrée.';
        }
    }
    $_SESSION['message_admin'] = $message_admin;
    header('Location: espace-administrateur.php#gestion-employes');
    exit;
}

$message_admin = $_SESSION['message_admin'] ?? '';
unset($_SESSION['message_admin']);

$employes = $pdo->query('SELECT id, email, actif FROM utilisateurs WHERE role_id = 2 ORDER BY actif DESC, email')->fetchAll(PDO::FETCH_ASSOC);
$menus_admin = $pdo->query('SELECT id, titre FROM menus ORDER BY titre')->fetchAll(PDO::FETCH_ASSOC);

$filtre_menu = isset($_GET['menu']) && is_string($_GET['menu']) ? $_GET['menu'] : 'tous';
$debut = isset($_GET['debut']) && is_string($_GET['debut']) ? $_GET['debut'] : '';
$fin = isset($_GET['fin']) && is_string($_GET['fin']) ? $_GET['fin'] : '';
if ($debut !== '' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $debut) || strtotime($debut) === false)) {
    $debut = '';
}
if ($fin !== '' && (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fin) || strtotime($fin) === false)) {
    $fin = '';
}

$statistiques_disponibles = true;
$nombre_par_menu = [];
$revenu_par_menu = [];
$revenu_total = 0;
try {
    $commandes_nosql = mettreAJourStatistiquesNoSQL($pdo);
    foreach ($commandes_nosql as $commande) {
        $id_menu = (int) $commande['menu_id'];
        $nom_menu = (string) $commande['menu'];
        if (!isset($nombre_par_menu[$id_menu])) {
            $nombre_par_menu[$id_menu] = ['nom' => $nom_menu, 'nombre' => 0];
        }
        $nombre_par_menu[$id_menu]['nombre']++;

        $date_commande = (string) $commande['date'];
        $statut = (string) $commande['statut'];
        if (($filtre_menu === 'tous' || $filtre_menu === (string) $id_menu) &&
            ($debut === '' || $date_commande >= $debut) &&
            ($fin === '' || $date_commande <= $fin) &&
            in_array($statut, ['livree', 'materiel', 'terminee'], true)) {
            if (!isset($revenu_par_menu[$id_menu])) {
                $revenu_par_menu[$id_menu] = ['nom' => $nom_menu, 'total' => 0];
            }
            $revenu_par_menu[$id_menu]['total'] += (float) $commande['total'];
            $revenu_total += (float) $commande['total'];
        }
    }
} catch (Throwable $erreur) {
    $statistiques_disponibles = false;
}

$maximum_commandes = 0;
foreach ($nombre_par_menu as $statistique) {
    if ($statistique['nombre'] > $maximum_commandes) {
        $maximum_commandes = $statistique['nombre'];
    }
}
