<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$jeton = $_POST['jeton'] ?? ($_GET['jeton'] ?? '');
$message = '';
$lien_valide = false;

if (preg_match('/^[a-f0-9]{64}$/', $jeton)) {
    require __DIR__ . '/php/connexion.php';

    try {
        $jeton_hash = hash('sha256', $jeton);
        $requete = $pdo->prepare('SELECT r.utilisateur_id FROM reinitialisations_mdp r INNER JOIN utilisateurs u ON u.id = r.utilisateur_id WHERE r.jeton_hash = ? AND r.date_expiration > NOW() AND r.date_utilisation IS NULL AND u.actif = 1');
        $requete->execute([$jeton_hash]);
        $compte = $requete->fetch(PDO::FETCH_ASSOC);
        $lien_valide = (bool) $compte;

        if ($lien_valide && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $message = 'Le formulaire a expiré. Veuillez actualiser la page.';
            } else {
                $mot_de_passe = $_POST['mot_de_passe'] ?? '';
                $confirmation = $_POST['confirmation'] ?? '';

                if (strlen($mot_de_passe) < 10 ||
                    !preg_match('/[a-z]/', $mot_de_passe) ||
                    !preg_match('/[A-Z]/', $mot_de_passe) ||
                    !preg_match('/[0-9]/', $mot_de_passe) ||
                    !preg_match('/[^a-zA-Z0-9]/', $mot_de_passe)) {
                    $message = 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.';
                } elseif ($mot_de_passe !== $confirmation) {
                    $message = 'Les deux mots de passe sont différents.';
                } else {
                    $pdo->beginTransaction();
                    $requete = $pdo->prepare('UPDATE reinitialisations_mdp SET date_utilisation = NOW() WHERE jeton_hash = ? AND date_expiration > NOW() AND date_utilisation IS NULL');
                    $requete->execute([$jeton_hash]);

                    if ($requete->rowCount() === 1) {
                        $mot_de_passe_protege = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                        $requete = $pdo->prepare('UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ? AND actif = 1');
                        $requete->execute([$mot_de_passe_protege, $compte['utilisateur_id']]);

                        if ($requete->rowCount() === 1) {
                            $requete = $pdo->prepare('UPDATE reinitialisations_mdp SET date_utilisation = NOW() WHERE utilisateur_id = ? AND date_utilisation IS NULL');
                            $requete->execute([$compte['utilisateur_id']]);
                            $pdo->commit();
                            header('Location: connexion.php?reinitialisation=ok');
                            exit;
                        }
                    }

                    $pdo->rollBack();
                    $lien_valide = false;
                }
            }
        }
    } catch (PDOException $erreur) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $message = 'Une erreur est survenue. Veuillez réessayer plus tard.';
        $lien_valide = false;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Choisissez un nouveau mot de passe Vite & Gourmand.">

    <title>Nouveau mot de passe - Vite & Gourmand</title>

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
            <h1>Nouveau mot de passe</h1>
            <?php if ($lien_valide) { ?>
                <p class="introduction-connexion">Choisissez votre nouveau mot de passe.</p>
                <form class="formulaire-connexion" action="reinitialiser-mot-de-passe.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="jeton" value="<?php echo htmlspecialchars($jeton, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="champ-connexion">
                        <label for="mot-de-passe">Nouveau mot de passe</label>
                        <input type="password" id="mot-de-passe" name="mot_de_passe" minlength="10" autocomplete="new-password" required>
                    </div>
                    <p class="introduction-connexion">10 caractères minimum, avec une majuscule, une minuscule, un chiffre et un caractère spécial.</p>
                    <div class="champ-connexion">
                        <label for="confirmation">Confirmer le mot de passe</label>
                        <input type="password" id="confirmation" name="confirmation" minlength="10" autocomplete="new-password" required>
                    </div>
                    <button type="submit" class="bouton-connexion">Enregistrer le mot de passe</button>
                </form>
            <?php } else { ?>
                <p class="introduction-connexion">Ce lien est invalide, expiré ou déjà utilisé. Vous pouvez demander un nouveau lien.</p>
            <?php } ?>
            <p class="message-formulaire" aria-live="polite"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="lien-inscription"><a href="mot-de-passe-oublie.php">Demander un nouveau lien</a></p>
            <p class="lien-inscription"><a href="connexion.php">Retour à la connexion</a></p>
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
