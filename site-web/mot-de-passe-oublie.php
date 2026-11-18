<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !is_string($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Le formulaire a expiré. Veuillez actualiser la page.';
    } else {
        $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) {
            $message = 'Veuillez saisir une adresse mail valide.';
        } else {
            $message = 'Si un compte correspond à cette adresse, un lien vous sera envoyé par mail.';
            require __DIR__ . '/php/connexion.php';

            try {
                $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ? AND actif = 1');
                $requete->execute([$email]);
                $compte = $requete->fetch(PDO::FETCH_ASSOC);

                if ($compte) {
                    $jeton = bin2hex(random_bytes(32));
                    $jeton_hash = hash('sha256', $jeton);
                    $requete = $pdo->prepare('INSERT INTO reinitialisations_mdp (utilisateur_id, jeton_hash, date_expiration) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))');
                    $requete->execute([$compte['id'], $jeton_hash]);

                    require __DIR__ . '/php/parametres-site.php';
                    $lien = rtrim($url_site, '/') . '/reinitialiser-mot-de-passe.php?jeton=' . $jeton;
                    $texte = "Bonjour,\n\nPour choisir un nouveau mot de passe Vite & Gourmand, ouvrez ce lien :\n" . $lien . "\n\nCe lien expire dans une heure et ne peut servir qu'une fois.\nSi vous n'avez rien demandé, ignorez ce message.";
                    $entetes = "From: Vite et Gourmand <" . $email_expediteur . ">\r\nContent-Type: text/plain; charset=UTF-8";

                    if ($url_site !== '' && filter_var($email_expediteur, FILTER_VALIDATE_EMAIL) &&
                        strpbrk($email_expediteur, "\r\n") === false &&
                        mail($email, 'Vite et Gourmand - Nouveau mot de passe', $texte, $entetes)) {
                        $requete = $pdo->prepare('UPDATE reinitialisations_mdp SET date_utilisation = NOW() WHERE utilisateur_id = ? AND jeton_hash <> ? AND date_utilisation IS NULL');
                        $requete->execute([$compte['id'], $jeton_hash]);
                    } else {
                        $requete = $pdo->prepare('DELETE FROM reinitialisations_mdp WHERE jeton_hash = ?');
                        $requete->execute([$jeton_hash]);
                    }
                }
            } catch (PDOException $erreur) {
                $message = 'Le service est momentanément indisponible. Veuillez réessayer plus tard.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Réinitialisez votre mot de passe Vite & Gourmand.">

    <title>Mot de passe oublié - Vite & Gourmand</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/connexion.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
</head>

<body class="page-connexion">

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

        <section class="section-connexion">

            <h1>Mot de passe oublié ?</h1>

            <p class="introduction-connexion">
                Saisissez votre adresse mail pour recevoir
                un lien de réinitialisation de votre mot de passe.
            </p>

            <form class="formulaire-connexion" id="formulaire-mot-de-passe-oublie" action="mot-de-passe-oublie.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="champ-connexion">
                    <label for="email">Adresse mail</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                        autocomplete="email"
                        required
                    >
                </div>

                <button type="submit" class="bouton-connexion">
                    Envoyer le lien
                </button>

            </form>

            <p
                class="message-formulaire"
                id="message-mot-de-passe-oublie"
                aria-live="polite"
            ><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>

            <p class="lien-inscription">
                <a href="connexion.php">Retour à la connexion</a>
            </p>

        </section>
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
                    <a href="mentions-legales.html">Mentions légales</a>
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
    <script src="./assets/js/horaires-public.js"></script>
</body>
</html>
