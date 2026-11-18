<?php
require __DIR__ . '/php/espace-client.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Consultez vos commandes et gérez votre compte Vite & Gourmand.">

    <title>Mon espace - Vite & Gourmand</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/espace-utilisateur.css">
    <link rel="stylesheet" href="./assets/css/suivi-avis.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
    <link rel="stylesheet" href="./assets/css/authentification.css">
</head>

<body class="page-espace-utilisateur">

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
        <section class="introduction-espace">
            <h1>Mon espace</h1>
            <p>Retrouvez vos commandes et gérez vos informations personnelles.</p>
        </section>

        <?php if ($message_client !== '') { ?>
            <p class="message-espace-client" role="status"><?php echo protegerClient($message_client); ?></p>
        <?php } ?>

        <div class="organisation-espace">
            <nav class="navigation-espace" aria-label="Mon espace utilisateur">
                <h2>Mon compte</h2>
                <ul>
                    <li><a href="#mes-commandes">Mes commandes</a></li>
                    <li><a href="#mes-informations">Mes informations</a></li>
                    <li><a href="#suivi-commande">Suivi de commande</a></li>
                    <li><a href="#avis-client">Mes avis</a></li>
                </ul>
            </nav>

            <div class="sections-espace">
                <section id="mes-commandes" class="bloc-espace">
                    <h2>Mes commandes</h2>
                    <?php if (!$commandes_client) { ?>
                        <p>Vous n'avez pas encore passé de commande.</p>
                        <a class="lien-espace-client" href="menus.php">Découvrir nos menus</a>
                    <?php } else { ?>
                        <form method="get" action="espace-utilisateur.php" class="choix-commande-formulaire">
                            <label for="choix-commande-client">Choisir une commande</label>
                            <div class="choix-commande-ligne">
                                <select id="choix-commande-client" name="commande" class="select-commande-client">
                                    <?php foreach ($commandes_client as $commande) { ?>
                                        <option value="<?php echo (int) $commande['id']; ?>" <?php if ((int) $commande_selectionnee['id'] === (int) $commande['id']) echo 'selected'; ?>>
                                            Commande n°<?php echo (int) $commande['id']; ?> — <?php echo protegerClient($commande['nom_menu']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <button type="submit" class="bouton-action">Afficher</button>
                            </div>
                        </form>

                        <?php $c = $commande_selectionnee; ?>
                        <article class="carte-commande">
                            <div class="entete-commande">
                                <h3>Commande n°<?php echo (int) $c['id']; ?></h3>
                                <span class="statut-commande"><?php echo protegerClient($statuts_client[$c['statut']] ?? $c['statut']); ?></span>
                            </div>
                            <p><strong>Menu :</strong> <?php echo protegerClient($c['nom_menu']); ?></p>
                            <p><strong>Nombre de personnes :</strong> <?php echo (int) $c['nombre_personnes']; ?></p>
                            <p><strong>Date de la prestation :</strong> <?php echo dateClientPHP($c['date_prestation']); ?></p>
                            <p><strong>Adresse de la prestation :</strong> <?php echo protegerClient($c['adresse_prestation']); ?></p>
                            <p><strong>Date de livraison :</strong> <?php echo dateClientPHP($c['date_livraison']); ?></p>
                            <p><strong>Heure de livraison :</strong> <?php echo protegerClient(substr($c['heure_livraison'], 0, 5)); ?></p>
                            <p><strong>Adresse de livraison :</strong> <?php echo protegerClient($c['adresse_livraison']); ?></p>
                            <p><strong>Lieu de livraison :</strong> <?php echo $c['hors_bordeaux'] ? 'Hors Bordeaux' : 'Dans Bordeaux'; ?></p>
                            <div class="detail-prix-client">
                                <p><strong>Menu :</strong> <?php echo prixClientPHP($c['prix_menu']); ?></p>
                                <p><strong>Réduction :</strong> −<?php echo prixClientPHP($c['reduction']); ?></p>
                                <p><strong>Livraison :</strong> <?php echo prixClientPHP($c['frais_livraison']); ?></p>
                                <p><strong>Total :</strong> <?php echo prixClientPHP($c['prix_total']); ?></p>
                            </div>

                            <?php if (in_array($c['statut'], ['en_attente', 'attente'], true)) { ?>
                                <details class="details-action-client">
                                    <summary>Modifier cette commande</summary>
                                    <form action="espace-utilisateur.php?commande=<?php echo (int) $c['id']; ?>" method="post" class="formulaire-profil">
                                        <input type="hidden" name="csrf_token" value="<?php echo protegerClient($_SESSION['csrf_token']); ?>">
                                        <input type="hidden" name="action_client" value="modifier">
                                        <input type="hidden" name="commande_id" value="<?php echo (int) $c['id']; ?>">
                                        <p>Le menu choisi reste inchangé. Le montant sera recalculé après validation.</p>
                                        <div class="champ-profil">
                                            <label for="modif-personnes">Nombre de personnes (minimum <?php echo (int) $c['personnes_minimum']; ?>)</label>
                                            <input type="number" id="modif-personnes" name="nombre_personnes" min="<?php echo (int) $c['personnes_minimum']; ?>" max="5000" value="<?php echo (int) $c['nombre_personnes']; ?>" required>
                                        </div>
                                        <div class="champ-profil">
                                            <label for="modif-date-prestation">Date de la prestation</label>
                                            <input type="date" id="modif-date-prestation" name="date_prestation" value="<?php echo protegerClient($c['date_prestation']); ?>" required>
                                        </div>
                                        <div class="champ-profil">
                                            <label for="modif-adresse-prestation">Adresse de la prestation</label>
                                            <input type="text" id="modif-adresse-prestation" name="adresse_prestation" maxlength="255" value="<?php echo protegerClient($c['adresse_prestation']); ?>" required>
                                        </div>
                                        <div class="ligne-profil">
                                            <div class="champ-profil">
                                                <label for="modif-date-livraison">Date de livraison</label>
                                                <input type="date" id="modif-date-livraison" name="date_livraison" value="<?php echo protegerClient($c['date_livraison']); ?>" required>
                                            </div>
                                            <div class="champ-profil">
                                                <label for="modif-heure-livraison">Heure de livraison</label>
                                                <input type="time" id="modif-heure-livraison" name="heure_livraison" value="<?php echo protegerClient(substr($c['heure_livraison'], 0, 5)); ?>" required>
                                            </div>
                                        </div>
                                        <div class="champ-profil">
                                            <label for="modif-adresse-livraison">Adresse de livraison</label>
                                            <input type="text" id="modif-adresse-livraison" name="adresse_livraison" maxlength="255" value="<?php echo protegerClient($c['adresse_livraison']); ?>" required>
                                        </div>
                                        <div class="ligne-profil">
                                            <div class="champ-profil">
                                                <label for="modif-zone-livraison">Lieu de livraison</label>
                                                <select id="modif-zone-livraison" name="ville_livraison" required>
                                                    <option value="bordeaux" <?php if (!$c['hors_bordeaux']) echo 'selected'; ?>>Dans Bordeaux</option>
                                                    <option value="hors-bordeaux" <?php if ($c['hors_bordeaux']) echo 'selected'; ?>>Hors Bordeaux</option>
                                                </select>
                                            </div>
                                            <div class="champ-profil">
                                                <label for="modif-distance">Distance en km (hors Bordeaux)</label>
                                                <input type="number" id="modif-distance" name="distance" min="0" max="10000" step="0.01" value="<?php echo protegerClient($c['distance_km']); ?>" required>
                                            </div>
                                        </div>
                                        <button type="submit" class="bouton-enregistrer">Enregistrer les modifications</button>
                                    </form>
                                </details>

                                <details class="details-action-client">
                                    <summary>Annuler cette commande</summary>
                                    <form action="espace-utilisateur.php?commande=<?php echo (int) $c['id']; ?>" method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo protegerClient($_SESSION['csrf_token']); ?>">
                                        <input type="hidden" name="action_client" value="annuler">
                                        <input type="hidden" name="commande_id" value="<?php echo (int) $c['id']; ?>">
                                        <p>Confirmez-vous l'annulation de la commande n°<?php echo (int) $c['id']; ?> ?</p>
                                        <button type="submit" class="bouton-secondaire">Confirmer l'annulation</button>
                                    </form>
                                </details>
                            <?php } else { ?>
                                <p class="information-commande-client">Cette commande n'est plus modifiable depuis votre espace.</p>
                            <?php } ?>
                        </article>
                    <?php } ?>
                </section>

                <section id="mes-informations" class="bloc-espace">
                    <h2>Mes informations</h2>
                    <form action="espace-utilisateur.php" method="post" class="formulaire-profil">
                        <input type="hidden" name="csrf_token" value="<?php echo protegerClient($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="action_client" value="profil">
                        <div class="ligne-profil">
                            <div class="champ-profil">
                                <label for="nom">Nom</label>
                                <input type="text" id="nom" name="nom" maxlength="100" value="<?php echo protegerClient($profil_client['nom']); ?>" required>
                            </div>
                            <div class="champ-profil">
                                <label for="prenom">Prénom</label>
                                <input type="text" id="prenom" name="prenom" maxlength="100" value="<?php echo protegerClient($profil_client['prenom']); ?>" required>
                            </div>
                        </div>
                        <div class="champ-profil">
                            <label for="email">Adresse mail</label>
                            <input type="email" id="email" name="email" maxlength="190" value="<?php echo protegerClient($profil_client['email']); ?>" required>
                        </div>
                        <div class="champ-profil">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" maxlength="30" value="<?php echo protegerClient($profil_client['telephone']); ?>" required>
                        </div>
                        <div class="champ-profil">
                            <label for="adresse">Adresse postale</label>
                            <input type="text" id="adresse" name="adresse" maxlength="255" value="<?php echo protegerClient($profil_client['adresse_postale']); ?>" required>
                        </div>
                        <button type="submit" class="bouton-enregistrer">Enregistrer mes informations</button>
                    </form>
                </section>

                <section id="suivi-commande" class="bloc-espace">
                    <h2>Suivi de commande</h2>
                    <?php if (!$commande_selectionnee) { ?>
                        <p>Le suivi apparaîtra ici après votre première commande.</p>
                    <?php } else { ?>
                        <div class="suivi-contenu">
                            <h3>Commande n°<?php echo (int) $commande_selectionnee['id']; ?></h3>
                            <p><strong>Statut :</strong> <?php echo protegerClient($statuts_client[$commande_selectionnee['statut']] ?? $commande_selectionnee['statut']); ?></p>
                            <?php if (in_array($commande_selectionnee['statut'], ['en_attente', 'attente'], true)) { ?>
                                <p>Votre commande est en attente de validation.</p>
                            <?php } elseif (!$historique_client) { ?>
                                <p>Aucune évolution de statut n'est enregistrée.</p>
                            <?php } else { ?>
                                <ol class="historique-statut">
                                    <?php foreach ($historique_client as $evenement) { ?>
                                        <li>
                                            <strong><?php echo protegerClient($statuts_client[$evenement['statut']] ?? $evenement['statut']); ?></strong>
                                            — <?php echo protegerClient(date('d/m/Y à H:i', strtotime($evenement['date_changement']))); ?>
                                            <?php if ($evenement['commentaire']) { ?>
                                                — <?php echo protegerClient($evenement['commentaire']); ?>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                </ol>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </section>

                <section id="avis-client" class="bloc-espace">
                    <h2>Mes avis</h2>
                    <?php $avis_deja_envoyes = false; ?>
                    <?php foreach ($commandes_client as $commande) { ?>
                        <?php if ($commande['avis_id']) { $avis_deja_envoyes = true; ?>
                            <p>Commande n°<?php echo (int) $commande['id']; ?> — Note : <?php echo (int) $commande['avis_note']; ?>/5 — <?php echo $commande['avis_statut'] === 'valide' ? 'Publié' : 'En cours de validation'; ?></p>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($commandes_a_evaluer) { ?>
                        <p>Donnez votre avis sur une commande terminée.</p>
                        <form action="espace-utilisateur.php" method="post" class="formulaire-profil">
                            <input type="hidden" name="csrf_token" value="<?php echo protegerClient($_SESSION['csrf_token']); ?>">
                            <input type="hidden" name="action_client" value="avis">
                            <div class="champ-profil">
                                <label for="choix-avis-commande">Commande terminée</label>
                                <select id="choix-avis-commande" name="commande_id" required>
                                    <?php foreach ($commandes_a_evaluer as $commande) { ?>
                                        <option value="<?php echo (int) $commande['id']; ?>">Commande n°<?php echo (int) $commande['id']; ?> — <?php echo protegerClient($commande['nom_menu']); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="champ-profil">
                                <label for="note-avis">Votre note (de 1 à 5)</label>
                                <input type="number" id="note-avis" name="note" min="1" max="5" step="1" required>
                            </div>
                            <div class="champ-profil">
                                <label for="commentaire-avis">Votre commentaire</label>
                                <textarea id="commentaire-avis" name="commentaire" minlength="3" maxlength="1500" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="bouton-enregistrer">Envoyer mon avis</button>
                        </form>
                    <?php } elseif (!$avis_deja_envoyes) { ?>
                        <p>Vous pourrez déposer un avis une fois votre commande terminée.</p>
                    <?php } ?>
                </section>
            </div>
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
    <script src="./assets/js/horaires-public.js"></script>
</body>
</html>
