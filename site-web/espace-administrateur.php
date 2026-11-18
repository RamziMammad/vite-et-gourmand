<?php
require __DIR__ . '/php/espace-administrateur.php';
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
        <a href="index.php">
            Vite <span class="texte-dore">&amp;</span> Gourmand
        </a>

        <nav class="navigation-pc" aria-label="Navigation principale">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menus.php">Tous les menus</a></li>
                <li>
                    <form action="deconnexion.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="lien-deconnexion">Déconnexion</button>
                    </form>
                </li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>

        <details class="menu-mobile">
            <summary>☰ Menu</summary>

            <nav aria-label="Navigation mobile">
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="menus.php">Tous les menus</a></li>
                    <li>
                        <form action="deconnexion.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="lien-deconnexion">Déconnexion</button>
                        </form>
                    </li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </details>
    </header>

    <main id="contenu-principal">
        <h1>Espace administrateur</h1>
        <p>Gérez les employés et consultez les statistiques de Vite & Gourmand.</p>

        <section class="bloc-administration" id="gestion-employes">
            <h2>Gestion des employés</h2>
            <p>Créer un accès pour un employé ou désactiver un compte existant.</p>

            <?php if ($message_admin !== '') { ?>
                <p class="message-administration" role="status"><?php echo affichageAdmin($message_admin); ?></p>
            <?php } ?>

            <h3>Créer un compte employé</h3>
            <form action="espace-administrateur.php" method="post" class="grille-gestion">
                <input type="hidden" name="csrf_token" value="<?php echo affichageAdmin($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_admin" value="creer_employe">

                <label for="admin-email">Adresse e-mail</label>
                <input id="admin-email" name="email" type="email" maxlength="190" autocomplete="off" required>

                <label for="admin-motdepasse">Mot de passe initial</label>
                <input id="admin-motdepasse" name="mot_de_passe" type="password" minlength="10" autocomplete="new-password" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{10,}" title="10 caractères minimum, avec majuscule, minuscule, chiffre et caractère spécial" required>

                <button class="bouton-administration" type="submit">Créer le compte employé</button>
            </form>

            <h3 class="sous-titre-administration">Comptes employés</h3>
            <div class="liste-employes">
                <?php if (!$employes) { ?>
                    <p>Aucun employé enregistré.</p>
                <?php } ?>
                <?php foreach ($employes as $employe) { ?>
                    <article class="employe-administration">
                        <div>
                            <strong><?php echo affichageAdmin($employe['email']); ?></strong>
                            <p><?php echo (int) $employe['actif'] === 1 ? 'Compte actif' : 'Compte désactivé'; ?></p>
                        </div>
                        <?php if ((int) $employe['actif'] === 1) { ?>
                            <form action="espace-administrateur.php" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo affichageAdmin($_SESSION['csrf_token']); ?>">
                                <input type="hidden" name="action_admin" value="desactiver_employe">
                                <input type="hidden" name="employe_id" value="<?php echo (int) $employe['id']; ?>">
                                <button class="bouton-secondaire-gestion" type="submit">Désactiver</button>
                            </form>
                        <?php } ?>
                    </article>
                <?php } ?>
            </div>
        </section>

        <section class="bloc-administration">
            <h2>Gestion de l'entreprise</h2>
            <p>Vous pouvez aussi gérer les commandes, les menus, les plats, les horaires et les avis.</p>
            <a class="lien-gestion" href="espace-employe.php">Accéder à la gestion des commandes et du catalogue</a>
        </section>

        <section class="bloc-administration" id="statistiques">
            <h2>Nombre de commandes par menu</h2>
            <?php if (!$statistiques_disponibles) { ?>
                <p role="status">Les statistiques sont momentanément indisponibles.</p>
            <?php } elseif (!$nombre_par_menu) { ?>
                <p>Aucune commande enregistrée pour le moment.</p>
            <?php } else { ?>
                <div class="graphique-administration" aria-label="Nombre de commandes par menu">
                    <?php foreach ($nombre_par_menu as $ligne) { ?>
                        <div class="graphique-ligne">
                            <strong><?php echo affichageAdmin($ligne['nom']); ?></strong>
                            <div class="barre-fond" aria-hidden="true">
                                <div class="barre-valeur" style="width: <?php echo $maximum_commandes > 0 ? round($ligne['nombre'] * 100 / $maximum_commandes) : 0; ?>%;"></div>
                            </div>
                            <span><?php echo (int) $ligne['nombre']; ?> commandes</span>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>

        <section class="bloc-administration" id="chiffre-affaires">
            <h2>Chiffre d'affaires par menu</h2>
            <p>Commandes livrées ou terminées, sur la période sélectionnée.</p>
            <form class="grille-gestion" action="espace-administrateur.php#chiffre-affaires" method="get">
                <label for="filtre-menu-admin">Menu</label>
                <select id="filtre-menu-admin" name="menu">
                    <option value="tous">Tous les menus</option>
                    <?php foreach ($menus_admin as $menu) { ?>
                        <option value="<?php echo (int) $menu['id']; ?>" <?php echo $filtre_menu === (string) $menu['id'] ? 'selected' : ''; ?>><?php echo affichageAdmin($menu['titre']); ?></option>
                    <?php } ?>
                </select>

                <label for="date-debut-admin">Du</label>
                <input type="date" id="date-debut-admin" name="debut" value="<?php echo affichageAdmin($debut); ?>">

                <label for="date-fin-admin">Au</label>
                <input type="date" id="date-fin-admin" name="fin" value="<?php echo affichageAdmin($fin); ?>">

                <button type="submit" class="bouton-administration">Afficher les résultats</button>
            </form>

            <?php if ($statistiques_disponibles) { ?>
                <p class="total-administration"><strong>Total : <?php echo number_format($revenu_total, 2, ',', ' '); ?> €</strong></p>
                <?php foreach ($revenu_par_menu as $ligne) { ?>
                    <p><?php echo affichageAdmin($ligne['nom']); ?> : <?php echo number_format($ligne['total'], 2, ',', ' '); ?> €</p>
                <?php } ?>
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
                <p><a href="mentions-legales.html">Mentions légales</a></p>

                <p>
                    <a href="conditions-generales-de-vente.html">
                        Conditions générales de vente
                    </a>
                </p>
            </div>

        </div>
    </footer>

    <script src="./assets/js/commun.js"></script>
    <script src="./assets/js/horaires-public.js"></script>
</body>
</html>
