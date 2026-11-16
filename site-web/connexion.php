<?php
require __DIR__ . '/php/authentification.php';

if (isset($_SESSION['utilisateur_id'], $_SESSION['role_id'])) {
    header('Location: ' . pagePourRole((int) $_SESSION['role_id']));
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Le formulaire a expiré. Veuillez actualiser la page et réessayer.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $mot_de_passe_saisi = $_POST['mot_de_passe'] ?? '';

        if ($email === '' || $mot_de_passe_saisi === '') {
            $message = 'Veuillez remplir tous les champs.';
        } else {
            require __DIR__ . '/php/connexion.php';

            try {
                $requete = $pdo->prepare('SELECT id, prenom, role_id, actif, mot_de_passe FROM utilisateurs WHERE email = ?');
                $requete->execute([$email]);
                $compte = $requete->fetch(PDO::FETCH_ASSOC);

                if ($compte && (int) $compte['actif'] === 1 && password_verify($mot_de_passe_saisi, $compte['mot_de_passe'])) {
                    session_regenerate_id(true);
                    $_SESSION['utilisateur_id'] = (int) $compte['id'];
                    $_SESSION['role_id'] = (int) $compte['role_id'];
                    $_SESSION['prenom'] = $compte['prenom'];
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    header('Location: ' . pagePourRole($_SESSION['role_id']));
                    exit;
                }

                $message = 'Adresse mail ou mot de passe incorrect.';
            } catch (PDOException $erreur) {
                $message = 'La connexion est momentanément indisponible. Veuillez réessayer plus tard.';
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
    <meta name="description" content="Connectez-vous à votre compte Vite & Gourmand.">

    <title>Connexion - Vite & Gourmand</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/connexion.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
</head>

<body class="page-connexion">

    <a class="lien-evitement" href="#contenu-principal">Aller au contenu principal</a>

    <header>
        <a href="index.html">
            Vite <span class="texte-dore">&amp;</span> Gourmand
        </a>

        <nav class="navigation-pc" aria-label="Navigation principale">
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="menus.html">Tous les menus</a></li>
                <li><a href="connexion.php">Connexion</a></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </nav>

        <details class="menu-mobile">
            <summary>☰ Menu</summary>

            <nav aria-label="Navigation mobile">
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="menus.html">Tous les menus</a></li>
                    <li><a href="connexion.php">Connexion</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </nav>
        </details>
    </header>

    <main id="contenu-principal">

        <section class="section-connexion">

            <h1>Connexion</h1>

            <p class="introduction-connexion">
                Connectez-vous à votre compte Vite & Gourmand.
            </p>

            <?php if ($message !== '') { ?>
                <p class="message-formulaire" id="message-connexion" role="alert"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php } ?>

            <?php if (isset($_GET['reinitialisation']) && $_GET['reinitialisation'] === 'ok') { ?>
                <p class="message-formulaire" role="status">Votre mot de passe a bien été modifié. Vous pouvez vous connecter.</p>
            <?php } ?>

            <?php if (isset($_GET['deconnexion'])) { ?>
                <p class="message-formulaire" role="status">Vous avez été déconnecté.</p>
            <?php } ?>

            <form class="formulaire-connexion" id="formulaire-connexion" action="connexion.php" method="post">
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

                <div class="champ-connexion">
                    <label for="mot-de-passe">Mot de passe</label>

                    <input
                        type="password"
                        id="mot-de-passe"
                        name="mot_de_passe"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <p class="lien-mot-de-passe">
                    <a href="mot-de-passe-oublie.php">
                        Mot de passe oublié ?
                    </a>
                </p>

                <button type="submit" class="bouton-connexion">
                    Se connecter
                </button>

            </form>


            <p class="lien-inscription">
                Vous n'avez pas de compte ?
                <a href="inscription.php">Créer un compte</a>
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
    <script src="./assets/js/donnees-exemples.js"></script>
    <script src="./assets/js/commun.js"></script>
</body>
</html>
