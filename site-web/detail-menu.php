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
    <meta name="description" content="Découvrez les menus de Vite & Gourmand, traiteur à Bordeaux.">

    <title>Menu Classique - Vite & Gourmand</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/detail-menu.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
</head>

<body class="page-detail-menu">

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

        <section class="introduction-detail">

            <h1>Menu Classique</h1>

            <p>
                Un repas convivial composé d'une entrée,
                d'un plat et d'un dessert
            </p>

        </section>

        <section
            class="presentation-detail"
            aria-label="Présentation du Menu Classique"
        >

            <div class="photo-principale">
                <img
                    src="assets/images/menus/Menu classique _ poulet roti.png"
                    alt="Poulet rôti avec tomates mozzarella et tarte aux pommes"
                >
            </div>

            <div class="informations-detail">

                <p class="theme-menu">
                    <strong>Thème :</strong> Classique
                </p>

                <p class="regime-menu">
                    <strong>Régime :</strong> Classique
                </p>

                <p class="minimum-personnes">
                    <img
                        src="assets/images/menus/Icône d’équipe avec badge 10+.png"
                        alt=""
                    >

                    <span>
                        <strong>Minimum</strong> : 10 personnes
                    </span>
                </p>

                <p class="stock-menu">
                    <img
                        src="assets/images/detail-menu/Icône de stock en boîtes empilées.png"
                        alt=""
                    >

                    <span>
                        <strong>Stock</strong> : 5 commandes
                    </span>
                </p>

                <div class="prix-detail">

                    <p class="montant-menu">
                        <strong>250 €</strong>
                    </p>

                    <p class="precision-prix">
                        <span class="mot-prix">Prix </span>
                        pour 10 personnes
                    </p>

                </div>

                <a class="bouton-commander" href="commande.php">
                    Commander
                </a>

            </div>
        </section>

        <div class="composition-et-conditions">

            <section class="composition-menu">

                <h2>Composition du menu</h2>

                <div class="plat-menu">
                    <h3>Entrée</h3>
                    <p>Tomates, mozzarella et basilic</p>
                    <p>Allergène : lait</p>
                </div>

                <div class="plat-menu">
                    <h3>Plat</h3>
                    <p>Poulet rôti, pommes de terre au beurre</p>
                    <p>Allergène : lait</p>
                </div>

                <div class="plat-menu dernier-plat">
                    <h3>Dessert</h3>
                    <p>Tarte aux pommes</p>
                    <p>Allergènes : blé, œufs, lait</p>
                </div>

            </section>

            <section class="conditions-menu">

                <h2>Conditions du menu</h2>

                <ul class="liste-conditions">

                    <li>
                        <img
                            src="assets/images/detail-menu/Icône dorée de réservation et horloge.png"
                            alt=""
                        >

                        <span>
                            Réservation : au moins 72 h à l'avance.
                        </span>
                    </li>

                    <li>
                        <img
                            src="assets/images/detail-menu/Icône de boîte de stockage réfrigérée.png"
                            alt=""
                        >

                        <span>
                            Conservation : respecter les consignes
                            fournies à la livraison.
                        </span>
                    </li>

                    <li>
                        <img
                            id="icone-condition-minimum"
                            src="assets/images/detail-menu/Icône  avec badge 10+.png"
                            alt=""
                        >

                        <span id="condition-minimum">
                            Minimum : 10 personnes.
                        </span>
                    </li>

                    <li class="condition-complementaire">
                        <img
                            src="assets/images/detail-menu/Icône checklist avec loupe d’information.png"
                            alt=""
                        >

                        <span>
                            Conditions à consulter avant de commander.
                        </span>
                    </li>

                </ul>
            </section>
        </div>

        <section class="autres-photos">

            <h2>Autres photos du menu</h2>

            <div class="galerie-photos">

                <img
                    src="assets/images/detail-menu/Poulet rôti, légumes et jus doré.png"
                    alt="Poulet rôti avec pommes de terre et légumes"
                >

                <img
                    src="assets/images/detail-menu/Salade caprese au basilic frais.png"
                    alt="Tomates et mozzarella accompagnées de basilic"
                >

                <img
                    src="assets/images/detail-menu/Tarte aux pommes caramélisées.png"
                    alt="Tarte aux pommes dorée"
                >

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
    <script src="./assets/js/affichage.js"></script>
    <script src="./assets/js/detail-menu.js"></script>
    <script src="./assets/js/horaires-public.js"></script>
</body>
</html>
