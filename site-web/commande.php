
<?php
require __DIR__ . '/php/authentification.php';

if (!isset($_SESSION['utilisateur_id'])) {
    $menu_demande = isset($_GET['menu']) && is_string($_GET['menu']) ? trim($_GET['menu']) : '';
    header('Location: connexion.php?menu=' . urlencode($menu_demande));
    exit;
}

verifierAcces([1]);
require __DIR__ . '/php/connexion.php';

$requete = $pdo->prepare('SELECT nom, prenom, email, telephone FROM utilisateurs WHERE id = ? AND actif = 1');
$requete->execute([$_SESSION['utilisateur_id']]);
$client = $requete->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    $_SESSION = [];
    session_destroy();
    header('Location: connexion.php');
    exit;
}

$requete = $pdo->query('SELECT id, code, titre, prix_minimum, personnes_minimum, stock_disponible, delai_heures FROM menus WHERE actif = 1 AND stock_disponible > 0 ORDER BY id');
$menus = $requete->fetchAll(PDO::FETCH_ASSOC);

$message = '';

$valeurs = [
    'adresse_prestation' => '',
    'date_prestation' => '',
    'date_livraison' => '',
    'heure_livraison' => '',
    'adresse_livraison' => '',
    'ville_livraison' => 'bordeaux',
    'distance' => '0',
    'nombre_personnes' => '10'
];

$code_saisi = $_POST['menu_choisi'] ?? ($_GET['menu'] ?? '');
$code_choisi = is_string($code_saisi) ? trim($code_saisi) : '';
if ($code_choisi === '' && count($menus) > 0) {
    $code_choisi = $menus[0]['code'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($valeurs as $nom_champ => $valeur) {
        $valeurs[$nom_champ] = isset($_POST[$nom_champ]) && is_string($_POST[$nom_champ]) ? trim($_POST[$nom_champ]) : '';
    }

    if (!isset($_POST['csrf_token']) || !is_string($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Le formulaire a expiré. Actualisez la page et recommencez.';
    } else {
        $menu_choisi = null;
        foreach ($menus as $menu) {
            if ($menu['code'] === $code_choisi) {
                $menu_choisi = $menu;
            }
        }

        $personnes = (int) $valeurs['nombre_personnes'];
        $distance = (float) $valeurs['distance'];
        $hors_bordeaux = $valeurs['ville_livraison'] === 'hors-bordeaux';
        $livraison = strtotime($valeurs['date_livraison'] . ' ' . $valeurs['heure_livraison']);
        $prestation = strtotime($valeurs['date_prestation']);
        $date_valide = $prestation !== false && $livraison !== false &&
            date('Y-m-d', $prestation) === $valeurs['date_prestation'] &&
            date('Y-m-d H:i', $livraison) === $valeurs['date_livraison'] . ' ' . $valeurs['heure_livraison'];

        if (!$menu_choisi) {
            $message = 'Le menu demandé n’est pas disponible.';
        } elseif ($valeurs['adresse_prestation'] === '' || $valeurs['adresse_livraison'] === '' ||
            strlen($valeurs['adresse_prestation']) > 255 || strlen($valeurs['adresse_livraison']) > 255) {
            $message = 'Indiquez les deux adresses (255 caractères maximum).';
        } elseif (!ctype_digit($valeurs['nombre_personnes']) ||
            $personnes < (int) $menu_choisi['personnes_minimum'] || $personnes > 5000) {
            $message = 'Le nombre de personnes doit respecter le minimum du menu.';
        } elseif (!in_array($valeurs['ville_livraison'], ['bordeaux', 'hors-bordeaux'], true)) {
            $message = 'Choisissez un lieu de livraison valide.';
        } elseif ($hors_bordeaux && (!is_numeric($valeurs['distance']) ||
            !is_finite($distance) || $distance <= 0 || $distance > 10000)) {
            $message = 'Indiquez une distance en kilomètres supérieure à zéro.';
        } elseif (!$date_valide) {
            $message = 'Indiquez des dates et une heure valides.';
        } elseif ($livraison <= time()) {
            $message = 'La date de livraison sélectionnée est déjà passée. Veuillez choisir une date future pour poursuivre votre commande.';
        } elseif ($valeurs['date_livraison'] > $valeurs['date_prestation']) {
            $message = 'La livraison ne peut pas avoir lieu après la prestation.';
        } elseif ($livraison < time() + (int) $menu_choisi['delai_heures'] * 3600) {
            $message = 'Le délai de réservation de ce menu doit être respecté. Veuillez choisir une date de livraison plus tardive.';
        } else {
            try {
                $pdo->beginTransaction();
                $requete = $pdo->prepare('SELECT * FROM menus WHERE code = ? AND actif = 1 FOR UPDATE');
                $requete->execute([$code_choisi]);
                $menu_actuel = $requete->fetch(PDO::FETCH_ASSOC);

                if (!$menu_actuel || (int) $menu_actuel['stock_disponible'] < 1) {
                    $message = 'Ce menu n’a plus de stock disponible.';
                } elseif ($personnes < (int) $menu_actuel['personnes_minimum'] ||
                    $livraison < time() + (int) $menu_actuel['delai_heures'] * 3600) {
                    $message = 'Les conditions de ce menu ont changé. Actualisez la page.';
                } else {
                    $prix_menu = round((float) $menu_actuel['prix_minimum'] * $personnes / (int) $menu_actuel['personnes_minimum'], 2);
                    $reduction = $personnes >= (int) $menu_actuel['personnes_minimum'] + 5 ? round($prix_menu * 0.10, 2) : 0;
                    $frais = $hors_bordeaux ? round(5 + $distance * 0.59, 2) : 0;
                    $prix_total = round($prix_menu - $reduction + $frais, 2);

                    if ($prix_total > 99999999.99) {
                        $message = 'Le montant dépasse la limite autorisée.';
                    } else {
                        $requete = $pdo->prepare('INSERT INTO commandes (utilisateur_id, menu_id, date_prestation, adresse_prestation, date_livraison, heure_livraison, adresse_livraison, hors_bordeaux, distance_km, nombre_personnes, prix_menu, reduction, frais_livraison, prix_total, statut) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                        $requete->execute([
                            $_SESSION['utilisateur_id'], $menu_actuel['id'], $valeurs['date_prestation'],
                            $valeurs['adresse_prestation'], $valeurs['date_livraison'], $valeurs['heure_livraison'],
                            $valeurs['adresse_livraison'], (int) $hors_bordeaux, $hors_bordeaux ? $distance : 0,
                            $personnes, $prix_menu, $reduction, $frais, $prix_total, 'en_attente'
                        ]);
                        $numero = $pdo->lastInsertId();

                        $requete = $pdo->prepare('UPDATE menus SET stock_disponible = stock_disponible - 1 WHERE id = ? AND stock_disponible > 0');
                        $requete->execute([$menu_actuel['id']]);
                        if ($requete->rowCount() !== 1) {
                            throw new PDOException('Stock insuffisant');
                        }

                        $requete = $pdo->prepare('INSERT INTO historique_commandes (commande_id, utilisateur_id, statut) VALUES (?, ?, ?)');
                        $requete->execute([$numero, $_SESSION['utilisateur_id'], 'en_attente']);
                        $pdo->commit();

                        $texte = "Bonjour " . $client['prenom'] . ",\n\n";
                        $texte .= "Votre commande n°" . $numero . " a été enregistrée.\n";
                        $texte .= "Menu : " . $menu_actuel['titre'] . "\n";
                        $texte .= "Personnes : " . $personnes . "\n";
                        $texte .= "Livraison : " . $valeurs['date_livraison'] . " à " . $valeurs['heure_livraison'] . "\n";
                        $texte .= "Total : " . number_format($prix_total, 2, ',', ' ') . " EUR\n";
                        $texte .= "Statut : en attente de validation.\n\nVite & Gourmand";
                        require __DIR__ . '/php/parametres-site.php';
                        $entetes = "From: " . $email_expediteur . "\r\nContent-Type: text/plain; charset=UTF-8";
                        $email_envoye = filter_var($email_expediteur, FILTER_VALIDATE_EMAIL) &&
                            strpbrk($email_expediteur, "\r\n") === false &&
                            mail($client['email'], 'Vite et Gourmand - Confirmation de commande', $texte, $entetes);

                        $_SESSION['confirmation_commande'] = [
                            'utilisateur_id' => (int) $_SESSION['utilisateur_id'],
                            'numero' => (int) $numero,
                            'menu' => $menu_actuel['titre'],
                            'personnes' => $personnes,
                            'date_livraison' => $valeurs['date_livraison'],
                            'heure_livraison' => $valeurs['heure_livraison'],
                            'total' => $prix_total,
                            'email_envoye' => $email_envoye
                        ];
                        header('Location: confirmation-commande.php');
                        exit;
                    }
                }
                $pdo->rollBack();
            } catch (PDOException $erreur) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $message = 'La commande n’a pas pu être enregistrée. Réessayez plus tard.';
            }
        }
    }
}
$titre_message = 'Vérifiez votre commande';
if ($message === 'La date de livraison sélectionnée est déjà passée. Veuillez choisir une date future pour poursuivre votre commande.') {
    $titre_message = 'Date de livraison invalide';
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Commandez un menu chez Vite & Gourmand, traiteur à Bordeaux.">

    <title>Commande - Vite & Gourmand</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/commande.css?v=2">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
</head>

<body class="page-commande">

    <a class="lien-evitement" href="#contenu-principal">Aller au contenu principal</a>

    <header>
        <a href="index.php">
            Vite <span class="texte-dore">&amp;</span> Gourmand
        </a>

        <nav class="navigation-pc" aria-label="Navigation principale">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menus.php">Tous les menus</a></li>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>

        <details class="menu-mobile">
            <summary>☰ Menu</summary>

            <nav aria-label="Navigation mobile">
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="menus.php">Tous les menus</a></li>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </details>
    </header>

    <main id="contenu-principal">

        <section class="introduction-commande">

            <h1>Commander un menu</h1>

            <p>
                Complétez les informations de votre prestation
                pour préparer votre commande.
            </p>

        </section>

        <?php if (count($menus) === 0) { ?>
            <p class="information-commande" role="alert">Aucun menu disponible actuellement.</p>
        <?php } ?>

        <div class="contenu-commande">

            <form id="formulaire-commande" class="formulaire-commande" action="commande.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <section class="bloc-commande">

                    <h2>Vos informations</h2>

                    <div class="ligne-commande">

                        <div class="champ-commande">
                            <label for="nom">Nom</label>

                            <input
                                type="text"
                                id="nom"
                                name="nom"
                                value="<?php echo htmlspecialchars($client['nom'], ENT_QUOTES, 'UTF-8'); ?>"
                                readonly
                                required
                            >
                        </div>

                        <div class="champ-commande">
                            <label for="prenom">Prénom</label>

                            <input
                                type="text"
                                id="prenom"
                                name="prenom"
                                value="<?php echo htmlspecialchars($client['prenom'], ENT_QUOTES, 'UTF-8'); ?>"
                                readonly
                                required
                            >
                        </div>

                    </div>

                    <div class="ligne-commande">

                        <div class="champ-commande">
                            <label for="email">Adresse mail</label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?php echo htmlspecialchars($client['email'], ENT_QUOTES, 'UTF-8'); ?>"
                                readonly
                                required
                            >
                        </div>

                        <div class="champ-commande">
                            <label for="telephone">Téléphone</label>

                            <input
                                type="tel"
                                id="telephone"
                                name="telephone"
                                value="<?php echo htmlspecialchars($client['telephone'], ENT_QUOTES, 'UTF-8'); ?>"
                                readonly
                                required
                            >
                        </div>

                    </div>
                </section>

                <section class="bloc-commande">

                    <h2>Votre prestation</h2>

                    <div class="champ-commande">
                        <label for="adresse-prestation">
                            Adresse de la prestation
                        </label>

                        <input
                            type="text"
                            id="adresse-prestation"
                            name="adresse_prestation"
                            value="<?php echo htmlspecialchars($valeurs['adresse_prestation'], ENT_QUOTES, 'UTF-8'); ?>"
                            required
                        >
                    </div>

                    <div class="champ-commande">
                        <label for="date-prestation">
                            Date de la prestation
                        </label>

                        <input
                            type="date"
                            id="date-prestation"
                            name="date_prestation"
                            value="<?php echo htmlspecialchars($valeurs['date_prestation'], ENT_QUOTES, 'UTF-8'); ?>"
                            required
                        >
                    </div>

                </section>

                <section class="bloc-commande">

                    <h2>Livraison</h2>

                    <div class="ligne-commande">

                        <div class="champ-commande">
                            <label for="date-livraison">
                                Date de livraison
                            </label>

                            <input
                                type="date"
                                id="date-livraison"
                                name="date_livraison"
                            value="<?php echo htmlspecialchars($valeurs['date_livraison'], ENT_QUOTES, 'UTF-8'); ?>"
                                required
                            >
                        </div>

                        <div class="champ-commande">
                            <label for="heure-livraison">
                                Heure de livraison
                            </label>

                            <input
                                type="time"
                                id="heure-livraison"
                                name="heure_livraison"
                            value="<?php echo htmlspecialchars($valeurs['heure_livraison'], ENT_QUOTES, 'UTF-8'); ?>"
                                required
                            >
                        </div>

                    </div>

                    <div class="champ-commande">
                        <label for="adresse-livraison">
                            Adresse de livraison
                        </label>

                        <input
                            type="text"
                            id="adresse-livraison"
                            name="adresse_livraison"
                            value="<?php echo htmlspecialchars($valeurs['adresse_livraison'], ENT_QUOTES, 'UTF-8'); ?>"
                            required
                        >
                    </div>

                    <div class="champ-commande">
                        <label for="ville-livraison">
                            Lieu de livraison
                        </label>

                        <select
                            id="ville-livraison"
                            name="ville_livraison"
                        >
                            <option value="bordeaux" <?php if ($valeurs['ville_livraison'] === 'bordeaux') echo 'selected'; ?>>Bordeaux</option>
                            <option value="hors-bordeaux" <?php if ($valeurs['ville_livraison'] === 'hors-bordeaux') echo 'selected'; ?>>
                                En dehors de Bordeaux
                            </option>
                        </select>
                    </div>

                    <div class="champ-commande">
                        <label for="distance">
                            Distance depuis Bordeaux en km
                            (si livraison hors Bordeaux)
                        </label>

                        <input
                            type="number"
                            id="distance"
                            name="distance"
                            min="0"
                            step="0.1"
                            value="<?php echo htmlspecialchars($valeurs['distance'], ENT_QUOTES, 'UTF-8'); ?>"
                        >

                        <p class="information-commande">
                            Pour une livraison hors de Bordeaux,
                            indiquez la distance en kilomètres.
                        </p>
                    </div>

                </section>

                <section class="bloc-commande">

                    <h2>Votre menu</h2>

                    <div class="champ-commande">
                        <label for="menu-choisi">
                            Menu choisi
                        </label>

                        <select
                            id="menu-choisi"
                            name="menu_choisi"
                        >
                            <?php foreach ($menus as $menu) { ?>
                                <option value="<?php echo htmlspecialchars($menu['code'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-minimum="<?php echo (int) $menu['personnes_minimum']; ?>"
                                    data-prix="<?php echo htmlspecialchars($menu['prix_minimum'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-delai="<?php echo (int) $menu['delai_heures']; ?>"
                                    <?php if ($menu['code'] === $code_choisi) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="champ-commande">
                        <label for="nombre-personnes">
                            Nombre de personnes
                        </label>

                        <input
                            type="number"
                            id="nombre-personnes"
                            name="nombre_personnes"
                            min="10"
                            step="1"
                            required
                            value="<?php echo htmlspecialchars($valeurs['nombre_personnes'], ENT_QUOTES, 'UTF-8'); ?>"
                        >

                        <p
                            id="message-minimum"
                            class="information-commande"
                        >
                            Minimum : 10 personnes.
                        </p>
                    </div>

                    <p class="information-commande">
                        Profitez de 10 % de réduction à partir
                        de 5 personnes de plus que le minimum du menu.
                    </p>

                    <p class="information-commande">
                        Consultez les conditions du menu
                        avant de valider votre commande.
                    </p>

                </section>
            </form>

            <!-- Récapitulatif du prix -->
            <aside class="recapitulatif-commande">

                <h2>Récapitulatif</h2>

                <p>
                    <strong>Menu :</strong>
                    <span id="recap-menu">Menu Classique</span>
                </p>

                <p>
                    <strong>Personnes :</strong>
                    <span id="recap-personnes">10</span>
                </p>

                <hr>

                <p>
                    <strong>Prix du menu :</strong>
                    <span id="prix-menu">250,00 €</span>
                </p>

                <p>
                    <strong>Réduction :</strong>
                    <span id="reduction">0,00 €</span>
                </p>

                <p>
                    <strong>Livraison :</strong>
                    <span id="prix-livraison">0,00 €</span>
                </p>

                <hr>

                <p class="total-commande">
                    <strong>Total estimé :</strong>
                    <span id="prix-total">250,00 €</span>
                </p>

                <p class="information-commande">
                    Les informations de cette commande servent à son organisation et à son suivi.
                    Consultez les <a href="mentions-legales.html">mentions légales</a>
                    pour connaître l'utilisation de vos données et vos droits.
                </p>

                <button
                    id="bouton-valider-commande"
                    type="submit"
                    form="formulaire-commande"
                    class="bouton-commande"
                    <?php if (count($menus) === 0) echo 'disabled'; ?>
                >
                    Valider ma commande
                </button>

                <div id="message-validation-commande" class="alerte-validation-commande" role="alert" <?php if ($message === '') echo 'hidden'; ?>>
                    <strong id="titre-validation-commande"><?php if ($message !== '') echo htmlspecialchars($titre_message, ENT_QUOTES, 'UTF-8'); ?></strong>
                    <p id="texte-validation-commande"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
                </div>

            </aside>
        </div>

    </main>

    <footer>
        <div class="footer-contenu">

            <section>
                <h2>Horaires d'ouverture</h2>

                <ul class="horaires-pc">
                    <li>Lundi : 09h00 - 18h00</li>
                    <li>Mardi : 09h00 - 18h00</li>
                    <li>Mercredi : 09h00 - 18h00</li>
                    <li>Jeudi : 09h00 - 18h00</li>
                    <li>Vendredi : 09h00 - 18h00</li>
                    <li>Samedi : 09h00 - 17h00</li>
                    <li>Dimanche : Fermé</li>
                </ul>

                <ul class="horaires-mobile">
                    <li>Lundi au vendredi : 09h00 - 18h00</li>
                    <li>Samedi : 09h00 - 17h00</li>
                    <li>Dimanche : Fermé</li>
                </ul>
            </section>

            <div class="informations-legales">
                <p>
                    <a href="mentions-legales.html">
                        Mentions légales
                    </a>
                </p>

                <p>
                    <a href="conditions-generales-de-vente.html">
                        Conditions générales de vente
                    </a>
                </p>
            </div>

        </div>
    </footer>

    <script src="./assets/js/donnees.js"></script>
    <script src="./assets/js/commun.js"></script>
    <script src="./assets/js/validation.js"></script>
    <script src="./assets/js/commande.js?v=2"></script>
    <script src="./assets/js/horaires-public.js"></script>
</body>
</html>
