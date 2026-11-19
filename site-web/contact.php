<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$titre = '';
$email = '';
$description = '';
$message = '';
$envoi_reussi = false;

if (!empty($_SESSION['contact_envoye'])) {
    $message = 'Votre message a bien été envoyé. Nous vous répondrons dès que possible.';
    $envoi_reussi = true;
    unset($_SESSION['contact_envoye']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jeton = $_POST['csrf_token'] ?? '';
    if (!is_string($jeton) || !hash_equals($_SESSION['csrf_token'], $jeton)) {
        $message = 'Le formulaire a expiré. Actualisez la page puis réessayez.';
    } else {
        $titre = isset($_POST['titre']) && is_string($_POST['titre']) ? trim($_POST['titre']) : '';
        $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
        $description = isset($_POST['description']) && is_string($_POST['description']) ? trim($_POST['description']) : '';

        if ($titre === '' || $email === '' || $description === '') {
            $message = 'Veuillez remplir tous les champs.';
        } elseif (strlen($titre) > 150 || strlen($email) > 190 || strlen($description) > 5000) {
            $message = 'Votre message est trop long. Vérifiez les champs.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strpbrk($email, "\r\n") !== false) {
            $message = 'Veuillez saisir une adresse e-mail valide.';
        } elseif (!empty($_SESSION['dernier_contact']) && time() - $_SESSION['dernier_contact'] < 30) {
            $message = 'Veuillez patienter avant d’envoyer un autre message.';
        } else {
            $hote = $_SERVER['SERVER_NAME'] ?? '';
            $est_local = in_array($hote, ['localhost', '127.0.0.1'], true);
            $email_contact = $est_local ? 'contact@vite-et-gourmand.test' : '';
            $email_expediteur = $est_local ? 'noreply@vite-et-gourmand.test' : '';
            if (is_file(__DIR__ . '/php/contact.local.php')) {
                require __DIR__ . '/php/contact.local.php';
            }

            if (!filter_var($email_contact, FILTER_VALIDATE_EMAIL) ||
                !filter_var($email_expediteur, FILTER_VALIDATE_EMAIL) ||
                strpbrk($email_contact, "\r\n") !== false ||
                strpbrk($email_expediteur, "\r\n") !== false) {
                $message = 'L’envoi du message est momentanément indisponible.';
            } else {
                $contenu = "Nouveau message pour Vite & Gourmand\n\n" .
                    "Titre : " . $titre . "\n" .
                    "Adresse e-mail : " . $email . "\n\n" .
                    "Message :\n" . $description;
                $entetes = "From: " . $email_expediteur . "\r\n" .
                    "Reply-To: " . $email . "\r\n" .
                    "Content-Type: text/plain; charset=UTF-8";

                if (mail($email_contact, 'Vite et Gourmand - Nouveau message de contact', $contenu, $entetes)) {
                    $_SESSION['dernier_contact'] = time();
                    $_SESSION['contact_envoye'] = true;
                    header('Location: contact.php');
                    exit;
                }
                $message = 'Votre message n’a pas pu être envoyé. Veuillez réessayer plus tard.';
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
    <meta name="description" content="Contactez Vite & Gourmand, traiteur à Bordeaux.">

    <title>Contact - Vite & Gourmand</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/contact.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
</head>

<body class="page-contact">

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

        <section class="section-contact">

            <h1>Contactez-nous</h1>

            <p class="introduction-contact">
                Vous avez une question ?
                Envoyez-nous un message.
            </p>

            <form class="formulaire-contact" id="formulaire-contact" action="contact.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="champ-contact">
                    <label for="titre">Titre</label>

                    <input
                        type="text"
                        id="titre"
                        name="titre"
                        maxlength="150"
                        value="<?php echo htmlspecialchars($titre, ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="champ-contact">
                    <label for="email">Adresse mail</label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="190"
                        value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >
                </div>

                <div class="champ-contact">
                    <label for="description">Votre message</label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        maxlength="5000"
                        required
                    ><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <p class="information-donnees">
                    Votre adresse e-mail et votre message servent à traiter votre demande.
                    Pour en savoir plus, consultez les
                    <a href="mentions-legales.html">mentions légales</a>.
                </p>

                <button type="submit" class="bouton-contact">
                    Envoyer le message
                </button>

            </form>

            <?php if ($message !== '') { ?>
                <p class="message-formulaire" id="message-contact" role="<?php echo $envoi_reussi ? 'status' : 'alert'; ?>">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php } ?>

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
