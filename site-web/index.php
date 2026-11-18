<?php
require __DIR__ . '/php/connexion.php';
require __DIR__ . '/php/catalogue-public.php';
$catalogue_public = chargerCataloguePublic($pdo);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Vite & Gourmand, traiteur à Bordeaux. Découvrez nos menus pour vos événements, préparés avec 25 ans d'expérience.">

    <title>Accueil Vite & Gourmand</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
</head>

<body class="page-accueil">

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

        <section class="presentation">

            <img
                class="fond-presentation-mobile"
                src="assets/images/buffet-fond.png"
                alt=""
            >

            <div class="presentation-texte">

                <h1>
                    Vite <span class="texte-dore">&amp; Gourmand</span>
                </h1>

                <p class="sous-titre">Traiteur à Bordeaux</p>

                <p class="description">
                    Julie et José proposent depuis 25 ans des menus
                    pour vos événements à Bordeaux.
                </p>

                <a href="menus.php">Voir les menus</a>

            </div>

            <img
                class="photo-accueil-pc"
                src="assets/images/plat-accueil.png"
                alt="Poulet rôti accompagné de légumes et de pommes de terre"
            >
        </section>

        <img
            class="photo-buffet-mobile"
            src="assets/images/buffet-traiteur.png"
            alt="Buffet traiteur préparé pour une réception"
        >

        <section class="savoir-faire">

            <h2>Notre savoir-faire</h2>

            <ul class="savoir-faire-liste">

                <li>
                    <img
                        src="assets/images/Médaille dorée.png"
                        alt=""
                    >
                    <p>25 ans d'expérience</p>
                </li>

                <li class="separateur">
                    <img
                        src="assets/images/icone_menus_evenements.png"
                        alt=""
                    >
                    <p>Menus pour vos événements</p>
                </li>

                <li class="separateur">
                    <img
                        src="assets/images/icone_service_traiteur.png"
                        alt=""
                    >
                    <p>Service traiteur</p>
                </li>

            </ul>
        </section>

        <section class="avis-clients">

            <h2>Avis de nos clients</h2>

            <div class="liste-avis">
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

    <script>
        window.VG_DONNEES_SQL = <?php echo json_encode($catalogue_public, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    </script>
    <script src="./assets/js/donnees.js"></script>
    <script src="./assets/js/catalogue-public.js"></script>
    <script src="./assets/js/commun.js"></script>
    <script src="./assets/js/horaires-public.js"></script>
</body>
</html>
