<?php
require __DIR__ . '/php/authentification.php';
verifierAcces([3]);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Administration Vite & Gourmand.">

    <title>Espace administrateur - Vite & Gourmand</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/gestion.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
    <link rel="stylesheet" href="./assets/css/authentification.css">
</head>

<body class="page-administration">

    <a class="lien-evitement" href="#contenu-principal">Aller au contenu principal</a>

    <header>
        <a href="index.html">
            Vite <span class="texte-dore">&amp;</span> Gourmand
        </a>

        <nav class="navigation-pc" aria-label="Navigation principale">
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="menus.html">Tous les menus</a></li>
                <li><form action="deconnexion.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="lien-deconnexion">Déconnexion</button>
                </form></li>
                <li><a href="contact.html">Contact</a></li>
            </ul>
        </nav>

        <details class="menu-mobile">
            <summary>☰ Menu</summary>

            <nav aria-label="Navigation mobile">
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="menus.html">Tous les menus</a></li>
                    <li><form action="deconnexion.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="lien-deconnexion">Déconnexion</button>
                </form></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </nav>
        </details>
    </header>

    <main id="contenu-principal">
        <p class="bandeau-demonstration">Les fonctions de cet espace utilisent encore des données fictives. Elles seront reliées à votre compte et à la base de données dans les prochaines étapes.</p>

        <h1>Espace administrateur</h1>

        <p>
            Comptes employés et statistiques de l'entreprise.
        </p>

        <section class="bloc-administration">

            <h2>Gestion des employés</h2>

            <p>Créer ou désactiver un compte employé.</p>

            <form id="form-employe" class="grille-gestion">

                <label>
                    Adresse mail
                    <input
                        id="admin-email"
                        type="email"
                        required
                    >
                </label>

                <label>
                    Mot de passe initial
                    <input
                        id="admin-motdepasse"
                        type="password"
                        minlength="10"
                        pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{10,}"
                        title="Au moins dix caractères, avec majuscule, minuscule, chiffre et caractère spécial"
                        required
                    >
                </label>


                <button
                    type="submit"
                    class="bouton-administration"
                >
                    Préparer le compte employé
                </button>

            </form>

            <p
                id="message-admin-employe"
                class="message-administration"
                aria-live="polite"
            ></p>

            <div id="liste-employes" class="liste-employes"></div>

        </section>

        <section class="bloc-administration">

            <h2>Gestion de l'entreprise</h2>

            <p>
                L'administrateur doit également disposer
                des fonctionnalités de l'employé.
            </p>

            <p>
                <a class="lien-gestion" href="espace-employe.php">
                    Gérer les commandes, menus, plats et avis
                </a>
            </p>

        </section>

        <section class="bloc-administration">

            <h2>Statistiques des commandes</h2>

            <p>
                Comparaison du nombre de commandes par menu.
            </p>


            <div
                id="graphique-commandes"
                aria-label="Nombre de commandes par menu"
            ></div>

        </section>

        <section class="bloc-administration">

            <h2>Chiffre d'affaires par menu</h2>

            <form id="form-statistiques" class="grille-gestion">

                <label>
                    Menu
                    <select id="filtre-menu-admin">
                        <option value="tous">Tous les menus</option>
                        <option value="classique">Menu Classique</option>
                        <option value="vegetarien">Menu Végétarien</option>
                    </select>
                </label>

                <label>
                    Du
                    <input
                        type="date"
                        id="date-debut-admin"
                    >
                </label>

                <label>
                    Au
                    <input
                        type="date"
                        id="date-fin-admin"
                    >
                </label>

                <button
                    type="submit"
                    class="bouton-administration"
                >
                    Afficher les résultats
                </button>

            </form>

            <p id="chiffre-affaires-admin" aria-live="polite"></p>
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
                <p><a href="mentions-legales.html">Mentions légales</a></p>

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
    <script src="./assets/js/administrateur.js"></script>
</body>
</html>
