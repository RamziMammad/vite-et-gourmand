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

    <title>Tous nos menus - Vite & Gourmand</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/menus.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
</head>

<body class="page-menus">

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

        <section class="introduction-menus">
            <h1>Tous nos menus</h1>
            <p>Découvrez nos menus pour vos événements à Bordeaux</p>
        </section>

        <div class="contenu-menus">

            <section class="filtres-menus">

                <h2>Filtrer les menus</h2>

                <form id="formulaire-filtres">

                    <div class="champ-filtre">
                        <label for="prix-max">Prix maximum</label>

                        <input
                            type="number"
                            id="prix-max"
                            name="prix_max"
                            min="0"
                            placeholder="Prix en €"
                        >
                    </div>

                    <div class="champ-filtre">
                        <label for="prix-min">Fourchette de prix</label>

                        <div class="fourchette-prix">

                            <input
                                type="number"
                                id="prix-min"
                                name="prix_min"
                                min="0"
                                placeholder="Min €"
                            >

                            <input
                                type="number"
                                id="prix-fourchette-max"
                                name="prix_fourchette_max"
                                min="0"
                                placeholder="Max €"
                                aria-label="Prix maximum de la fourchette"
                            >

                        </div>
                    </div>

                    <div class="champ-filtre">
                        <label for="theme">Thème</label>

                        <select id="theme" name="theme">
                            <option value="">Choisir un thème</option>
                            <option value="classique">Classique</option>
                            <option value="noel">Noël</option>
                            <option value="paques">Pâques</option>
                            <option value="evenement">Événement</option>
                        </select>
                    </div>

                    <div class="champ-filtre">
                        <label for="regime">Régime</label>

                        <select id="regime" name="regime">
                            <option value="">Choisir un régime</option>
                            <option value="classique">Classique</option>
                            <option value="vegetarien">Végétarien</option>
                            <option value="vegan">Végan</option>
                        </select>
                    </div>

                    <div class="champ-filtre">
                        <label for="personnes">Personnes minimum</label>

                        <input
                            type="number"
                            id="personnes"
                            name="personnes"
                            min="1"
                            step="1"
                            placeholder="Nombre de personnes"
                        >
                    </div>

                </form>
            </section>

            <section class="resultats-menus">

                <h2>Nos menus disponibles</h2>

                <div class="cartes-menus">
                </div>

                <p id="aucun-menu" class="menu-cache">
                    Aucun menu ne correspond à votre recherche.
                </p>

            </section>
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

    <script>
        window.VG_DONNEES_SQL = <?php echo json_encode($catalogue_public, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    </script>
    <script src="./assets/js/donnees.js"></script>
    <script src="./assets/js/catalogue-public.js"></script>
    <script src="./assets/js/commun.js"></script>
    <script src="./assets/js/affichage.js"></script>
    <script src="./assets/js/menus.js"></script>
    <script src="./assets/js/horaires-public.js"></script>
</body>
</html>
