<?php
require __DIR__ . '/php/authentification.php';
verifierAcces([1]);

if (!isset($_SESSION['confirmation_commande']) ||
    !is_array($_SESSION['confirmation_commande']) ||
    (int) ($_SESSION['confirmation_commande']['utilisateur_id'] ?? 0) !== (int) $_SESSION['utilisateur_id']) {
    header('Location: commande.php');
    exit;
}

$commande = $_SESSION['confirmation_commande'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Confirmation de votre commande Vite & Gourmand.">
    <title>Commande enregistrée - Vite & Gourmand</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
    <link rel="stylesheet" href="./assets/css/confirmation-commande.css">
</head>
<body class="page-confirmation">
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
        <section class="confirmation-carte" aria-labelledby="titre-confirmation">
            <div class="confirmation-icone" aria-hidden="true">✓</div>
            <h1 id="titre-confirmation">Merci pour votre commande !</h1>
            <p class="confirmation-principale">
                Votre commande <strong>n°<?php echo (int) $commande['numero']; ?></strong> a été enregistrée avec succès.
            </p>
            <p class="confirmation-texte">
                Notre équipe va examiner votre commande. Elle est actuellement en attente de validation.
            </p>
            <?php if ($commande['email_envoye']) { ?>
                <p class="confirmation-email" role="status">Un e-mail de confirmation a été envoyé.</p>
            <?php } else { ?>
                <p class="confirmation-email" role="status">Votre commande est enregistrée, mais l'e-mail de confirmation n'a pas pu être envoyé.</p>
            <?php } ?>

            <div class="confirmation-recapitulatif">
                <h2>Récapitulatif de votre commande</h2>
                <dl class="confirmation-details">
                    <div><dt>Numéro de commande</dt><dd>n°<?php echo (int) $commande['numero']; ?></dd></div>
                    <div><dt>Menu choisi</dt><dd><?php echo htmlspecialchars($commande['menu'], ENT_QUOTES, 'UTF-8'); ?></dd></div>
                    <div><dt>Nombre de personnes</dt><dd><?php echo (int) $commande['personnes']; ?></dd></div>
                    <div><dt>Date de livraison</dt><dd><?php echo htmlspecialchars(date('d/m/Y', strtotime($commande['date_livraison'])), ENT_QUOTES, 'UTF-8'); ?></dd></div>
                    <div><dt>Heure de livraison</dt><dd><?php echo htmlspecialchars(substr($commande['heure_livraison'], 0, 5), ENT_QUOTES, 'UTF-8'); ?></dd></div>
                    <div><dt>Statut</dt><dd>En attente de validation</dd></div>
                </dl>
                <p class="confirmation-total">Total de la commande : <strong><?php echo number_format((float) $commande['total'], 2, ',', ' '); ?> €</strong></p>
            </div>

            <div class="confirmation-actions">
                <a class="confirmation-bouton-principal" href="menus.php">Voir les autres menus</a>
                <a class="confirmation-bouton-secondaire" href="espace-utilisateur.php?commande=<?php echo (int) $commande['numero']; ?>#mes-commandes">Accéder à mon espace</a>
            </div>
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
    <script src="./assets/js/horaires-public.js"></script>
</body>
</html>
