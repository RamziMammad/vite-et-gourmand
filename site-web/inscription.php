<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$compte_cree = false;
$mail_bienvenue_envoye = false;
$nom = '';
$prenom = '';
$telephone = '';
$email = '';
$adresse = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !is_string($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $message = 'Le formulaire a expiré. Veuillez actualiser la page et réessayer.';
    } else {
        $nom = isset($_POST['nom']) && is_string($_POST['nom']) ? trim($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) && is_string($_POST['prenom']) ? trim($_POST['prenom']) : '';
        $telephone = isset($_POST['telephone']) && is_string($_POST['telephone']) ? trim($_POST['telephone']) : '';
        $email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
        $adresse = isset($_POST['adresse']) && is_string($_POST['adresse']) ? trim($_POST['adresse']) : '';
        $mot_de_passe_client = isset($_POST['mot_de_passe']) && is_string($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';

        if ($nom === '' || $prenom === '' || $telephone === '' || $email === '' || $adresse === '' || $mot_de_passe_client === '') {
            $message = 'Veuillez remplir tous les champs.';
        } elseif (strlen($nom) > 100 || strlen($prenom) > 100 || strlen($telephone) > 30 || strlen($email) > 190 || strlen($adresse) > 255) {
            $message = 'Certains champs sont trop longs.';
        } elseif (!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $email)) {
            $message = 'Veuillez saisir une adresse mail valide.';
        } elseif (strlen($mot_de_passe_client) < 10 ||
            !preg_match('/[a-z]/', $mot_de_passe_client) ||
            !preg_match('/[A-Z]/', $mot_de_passe_client) ||
            !preg_match('/[0-9]/', $mot_de_passe_client) ||
            !preg_match('/[^a-zA-Z0-9]/', $mot_de_passe_client)) {
            $message = 'Le mot de passe doit avoir 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
        } else {
            require __DIR__ . '/php/connexion.php';

            try {
                // On vérifie si cette adresse mail possède déjà un compte.
                $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
                $requete->execute([$email]);

                if ($requete->fetch()) {
                    $message = 'Cette adresse mail est déjà utilisée.';
                } else {
                    $mot_de_passe_protege = password_hash($mot_de_passe_client, PASSWORD_DEFAULT);
                    $requete = $pdo->prepare('INSERT INTO utilisateurs (nom, prenom, telephone, email, adresse_postale, mot_de_passe) VALUES (?, ?, ?, ?, ?, ?)');
                    $requete->execute([$nom, $prenom, $telephone, $email, $adresse, $mot_de_passe_protege]);
                    $compte_cree = true;
                    require __DIR__ . '/php/parametres-site.php';
                    $texte = "Bonjour " . $prenom . ",\n\nBienvenue chez Vite & Gourmand !\nVotre compte a bien été créé. Vous pouvez maintenant vous connecter et commander nos menus.\n\nVite & Gourmand";
                    $entetes = "From: " . $email_expediteur . "\r\nContent-Type: text/plain; charset=UTF-8";
                    $mail_bienvenue_envoye = filter_var($email_expediteur, FILTER_VALIDATE_EMAIL) &&
                        strpbrk($email_expediteur, "\r\n") === false &&
                        mail($email, 'Vite et Gourmand - Bienvenue', $texte, $entetes);
                }
            } catch (PDOException $erreur) {
                $message = 'L’inscription n’a pas pu être effectuée. Veuillez réessayer plus tard.';
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
    <meta name="description" content="Créez votre compte Vite & Gourmand pour commander nos menus.">

    <title>Inscription - Vite & Gourmand</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/inscription.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
</head>

<body class="page-inscription">

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

        <section class="section-inscription">

            <?php if ($compte_cree) { ?>
                <h1>Inscription réussie</h1>
            <?php } else { ?>
                <h1>Créer un compte</h1>
                <p class="introduction-inscription">
                    Inscrivez-vous pour commander nos menus.
                </p>
            <?php } ?>

            <?php if (!$compte_cree) { ?>
            <form class="formulaire-inscription" id="formulaire-inscription" action="inscription.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="champ-inscription">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="champ-inscription">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="champ-inscription">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($telephone, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="champ-inscription">
                    <label for="email">Adresse mail</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="champ-inscription">
                    <label for="adresse">Adresse postale</label>
                    <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($adresse, ENT_QUOTES, 'UTF-8'); ?>" required>
                </div>

                <div class="champ-inscription">
                    <label for="mot-de-passe">Mot de passe</label>

                    <input
                        type="password"
                        id="mot-de-passe"
                        name="mot_de_passe"
                        minlength="10"
                        pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{10,}"
                        title="10 caractères minimum, avec une majuscule, une minuscule, un chiffre et un caractère spécial"
                        required
                    >

                    <p class="aide-mot-de-passe">
                        Au moins 10 caractères, avec une majuscule,
                        une minuscule, un chiffre et un caractère spécial.
                    </p>
                </div>

                <button type="submit" class="bouton-inscription">
                    Créer mon compte
                </button>

            </form>
            <?php } ?>

            <p class="message-formulaire" id="message-inscription" aria-live="polite">
                <?php if ($compte_cree && $mail_bienvenue_envoye) { ?>
                    Votre compte a été créé. Un e-mail de bienvenue vous a été envoyé.
                <?php } elseif ($compte_cree) { ?>
                    Votre compte a été créé, mais l'e-mail de bienvenue n'a pas pu être envoyé. Vous pouvez vous connecter.
                <?php } else { echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); } ?>
            </p>

            <?php if ($compte_cree) { ?>
                <p class="lien-connexion">
                    <a href="connexion.php">Se connecter</a>
                </p>
            <?php } else { ?>
                <p class="lien-connexion">
                    Vous avez déjà un compte ?
                    <a href="connexion.php">Se connecter</a>
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
