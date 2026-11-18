<?php
require __DIR__ . '/php/espace-employe.php';

$titre_espace = 'Espace employé';
if ($_SESSION['role_id'] === 3) {
    $titre_espace = "Gestion de l'entreprise";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des commandes et du catalogue Vite & Gourmand.">
    <title><?php echo texteEmploye($titre_espace); ?> - Vite &amp; Gourmand</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/espace-employe.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
    <link rel="stylesheet" href="./assets/css/authentification.css">
    <link rel="stylesheet" href="./assets/css/gestion.css">
</head>
<body class="page-espace-employe">
    <a class="lien-evitement" href="#contenu-principal">Aller au contenu principal</a>

    <header>
        <a href="index.php">Vite <span class="texte-dore">&amp;</span> Gourmand</a>

        <nav class="navigation-pc" aria-label="Navigation principale">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menus.php">Tous les menus</a></li>
                <li>
                    <form action="deconnexion.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
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
                            <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
                            <button type="submit" class="lien-deconnexion">Déconnexion</button>
                        </form>
                    </li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </details>
    </header>

    <main id="contenu-principal">
        <section class="introduction-employe">
            <h1><?php echo texteEmploye($titre_espace); ?></h1>
            <p>Retrouvez les commandes et les informations utiles à l'équipe.</p>
        </section>

        <?php if ($message_employe !== ''): ?>
            <p class="message-retour-employe" role="status"><?php echo texteEmploye($message_employe); ?></p>
        <?php endif; ?>

        <nav class="navigation-employe" aria-label="Rubriques employé">
            <a href="#commandes">Commandes</a>
            <a href="#gestion-commandes">Gestion des commandes</a>
            <a href="#menus">Menus</a>
            <a href="#plats">Plats</a>
            <a href="#horaires">Horaires</a>
            <a href="#avis">Avis clients</a>
        </nav>

        <section id="commandes" class="bloc-employe">
            <h2>Commandes</h2>
            <p class="explication-employe">Retrouvez une commande par statut ou par client.</p>

            <form class="filtres-employe" method="get" action="espace-employe.php#commandes">
                <div class="champ-employe">
                    <label for="filtre-statut">Statut</label>
                    <select id="filtre-statut" name="statut">
                        <option value="tous">Tous les statuts</option>
                        <?php foreach ($statuts_employe as $code => $libelle): ?>
                            <?php if ($code === 'attente') continue; ?>
                            <option value="<?php echo texteEmploye($code); ?>" <?php echo $filtre_statut === $code ? 'selected' : ''; ?>><?php echo texteEmploye($libelle); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="champ-employe">
                    <label for="filtre-client">Client</label>
                    <select id="filtre-client" name="client">
                        <option value="0">Tous les clients</option>
                        <?php foreach ($clients_employe as $client): ?>
                            <option value="<?php echo (int) $client['id']; ?>" <?php echo $filtre_client === (int) $client['id'] ? 'selected' : ''; ?>><?php echo texteEmploye($client['prenom'] . ' ' . $client['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="bouton-employe" type="submit">Filtrer</button>
            </form>

            <p id="nombre-resultats" aria-live="polite"><?php echo count($commandes_employe); ?> commande(s) affichée(s)</p>

            <?php if (!$commandes_employe): ?>
                <p>Aucune commande ne correspond aux filtres sélectionnés.</p>
            <?php endif; ?>

            <?php foreach ($commandes_employe as $commande): ?>
                <article class="fiche-commande">
                    <div class="entete-fiche">
                        <h3>Commande n°<?php echo (int) $commande['id']; ?></h3>
                        <strong><?php echo texteEmploye($statuts_employe[$commande['statut']] ?? $commande['statut']); ?></strong>
                    </div>
                    <p><strong>Client :</strong> <?php echo texteEmploye($commande['prenom'] . ' ' . $commande['nom']); ?></p>
                    <p><strong>Contact :</strong> <?php echo texteEmploye($commande['email']); ?> — <?php echo texteEmploye($commande['telephone']); ?></p>
                    <p><strong>Menu :</strong> <?php echo texteEmploye($commande['titre_menu']); ?></p>
                    <p><strong>Personnes :</strong> <?php echo (int) $commande['nombre_personnes']; ?></p>
                    <p><strong>Prestation :</strong> <?php echo texteEmploye(date('d/m/Y', strtotime($commande['date_prestation']))); ?> — <?php echo texteEmploye($commande['adresse_prestation']); ?></p>
                    <p><strong>Livraison :</strong> <?php echo texteEmploye(date('d/m/Y', strtotime($commande['date_livraison']))); ?> à <?php echo texteEmploye(substr($commande['heure_livraison'], 0, 5)); ?> — <?php echo texteEmploye($commande['adresse_livraison']); ?></p>
                    <p><strong>Montant :</strong> <?php echo montantEmploye($commande['prix_total']); ?></p>
                    <p><strong>Historique :</strong></p>
                    <ul class="historique-employe">
                        <?php foreach ($historique_employe[$commande['id']] as $ligne): ?>
                            <li>
                                <?php echo texteEmploye(date('d/m/Y à H:i', strtotime($ligne['date_changement']))); ?> :
                                <?php echo texteEmploye($statuts_employe[$ligne['statut']] ?? $ligne['statut']); ?>
                                <?php if ($ligne['commentaire']): ?> — <?php echo texteEmploye($ligne['commentaire']); ?><?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </section>

        <section id="gestion-commandes" class="bloc-employe">
            <h2>Gestion des commandes</h2>

            <form class="formulaire-employe grille-gestion" action="espace-employe.php" method="post">
                <h3>Mettre à jour un statut</h3>
                <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_employe" value="statut">
                <label for="statut-commande">Commande</label>
                <select id="statut-commande" name="commande_id" required>
                    <?php foreach ($toutes_commandes as $commande): ?>
                        <option value="<?php echo (int) $commande['id']; ?>">Commande n°<?php echo (int) $commande['id']; ?> — <?php echo texteEmploye($commande['prenom'] . ' ' . $commande['nom']); ?> (<?php echo texteEmploye($statuts_employe[$commande['statut']] ?? $commande['statut']); ?>)</option>
                    <?php endforeach; ?>
                </select>
                <label for="nouveau-statut">Nouveau statut</label>
                <select id="nouveau-statut" name="nouveau_statut" required>
                    <option value="">Choisir un statut</option>
                    <?php foreach (['acceptee', 'preparation', 'livraison', 'livree', 'materiel', 'terminee'] as $statut): ?>
                        <option value="<?php echo texteEmploye($statut); ?>"><?php echo texteEmploye($statuts_employe[$statut]); ?></option>
                    <?php endforeach; ?>
                </select>
                <label class="case-gestion">
                    <input type="checkbox" name="materiel_restitue" value="oui">
                    Matériel restitué (obligatoire pour terminer une commande en attente de retour)
                </label>
                <p>Les statuts doivent suivre l'ordre de préparation, livraison et retour éventuel du matériel.</p>
                <button type="submit" class="bouton-employe">Mettre à jour le statut</button>
            </form>

            <form class="formulaire-employe grille-gestion" action="espace-employe.php" method="post">
                <h3>Annuler une commande</h3>
                <p>Contactez d'abord le client, puis renseignez le motif et le moyen de contact.</p>
                <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_employe" value="annuler">
                <label for="annulation-commande">Commande concernée</label>
                <select id="annulation-commande" name="commande_id" required>
                    <?php foreach ($toutes_commandes as $commande): ?>
                        <?php if (!in_array($commande['statut'], ['annulee', 'livree', 'materiel', 'terminee'], true)): ?>
                            <option value="<?php echo (int) $commande['id']; ?>">Commande n°<?php echo (int) $commande['id']; ?> — <?php echo texteEmploye($commande['prenom'] . ' ' . $commande['nom']); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <label for="mode-annulation">Mode de contact</label>
                <select id="mode-annulation" name="mode_contact" required>
                    <option value="">Choisir</option>
                    <option value="telephone">Téléphone</option>
                    <option value="email">E-mail</option>
                </select>
                <label for="motif-annulation">Motif de l'annulation</label>
                <textarea id="motif-annulation" name="motif" rows="3" minlength="5" maxlength="500" required></textarea>
                <label class="case-gestion">
                    <input type="checkbox" name="contact_effectue" value="oui" required>
                    J'ai contacté le client avant l'annulation.
                </label>
                <button type="submit" class="bouton-employe">Confirmer l'annulation</button>
            </form>

            <div class="formulaire-employe">
                <h3>Modifier une commande</h3>
                <form class="grille-gestion" action="espace-employe.php#gestion-commandes" method="get">
                    <label for="modifier-commande-id">Commande</label>
                    <select id="modifier-commande-id" name="commande">
                        <?php foreach ($toutes_commandes as $commande): ?>
                            <option value="<?php echo (int) $commande['id']; ?>" <?php echo $commande_selectionnee_id === (int) $commande['id'] ? 'selected' : ''; ?>>Commande n°<?php echo (int) $commande['id']; ?> — <?php echo texteEmploye($commande['prenom'] . ' ' . $commande['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="bouton-employe">Afficher cette commande</button>
                </form>

                <?php if ($commande_selectionnee && !in_array($commande_selectionnee['statut'], ['annulee', 'livree', 'materiel', 'terminee'], true)): ?>
                    <form class="grille-gestion espacement-employe" action="espace-employe.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="action_employe" value="modifier">
                        <input type="hidden" name="commande_id" value="<?php echo (int) $commande_selectionnee['id']; ?>">
                        <p><strong>Menu :</strong> <?php echo texteEmploye($commande_selectionnee['titre_menu']); ?> (non modifiable)</p>
                        <label>Nombre de personnes
                            <input type="number" name="nombre_personnes" min="1" value="<?php echo (int) $commande_selectionnee['nombre_personnes']; ?>" required>
                        </label>
                        <label>Date de prestation
                            <input type="date" name="date_prestation" value="<?php echo texteEmploye($commande_selectionnee['date_prestation']); ?>" required>
                        </label>
                        <label>Adresse de prestation
                            <input type="text" name="adresse_prestation" maxlength="255" value="<?php echo texteEmploye($commande_selectionnee['adresse_prestation']); ?>" required>
                        </label>
                        <label>Date de livraison
                            <input type="date" name="date_livraison" value="<?php echo texteEmploye($commande_selectionnee['date_livraison']); ?>" required>
                        </label>
                        <label>Heure de livraison
                            <input type="time" name="heure_livraison" value="<?php echo texteEmploye(substr($commande_selectionnee['heure_livraison'], 0, 5)); ?>" required>
                        </label>
                        <label>Adresse de livraison
                            <input type="text" name="adresse_livraison" maxlength="255" value="<?php echo texteEmploye($commande_selectionnee['adresse_livraison']); ?>" required>
                        </label>
                        <label>Zone de livraison
                            <select name="ville_livraison">
                                <option value="bordeaux" <?php echo !$commande_selectionnee['hors_bordeaux'] ? 'selected' : ''; ?>>Dans Bordeaux</option>
                                <option value="hors-bordeaux" <?php echo $commande_selectionnee['hors_bordeaux'] ? 'selected' : ''; ?>>Hors Bordeaux</option>
                            </select>
                        </label>
                        <label>Distance hors Bordeaux (km)
                            <input type="number" name="distance_km" min="0" step="0.01" value="<?php echo texteEmploye($commande_selectionnee['distance_km']); ?>">
                        </label>
                        <label>Mode de contact du client
                            <select name="mode_contact" required>
                                <option value="">Choisir</option>
                                <option value="telephone">Téléphone</option>
                                <option value="email">E-mail</option>
                            </select>
                        </label>
                        <label>Motif de la modification
                            <textarea name="motif" rows="2" minlength="5" maxlength="190" required></textarea>
                        </label>
                        <label class="case-gestion">
                            <input type="checkbox" name="contact_effectue" value="oui" required>
                            J'ai contacté le client.
                        </label>
                        <button type="submit" class="bouton-employe">Enregistrer la modification</button>
                    </form>
                <?php else: ?>
                    <p>Cette commande ne peut pas être modifiée.</p>
                <?php endif; ?>
            </div>
        </section>

        <section id="menus" class="bloc-employe">
            <h2>Menus</h2>
            <form class="formulaire-employe grille-gestion" action="espace-employe.php#edition-menu" method="get">
                <label for="choix-menu">Menu à modifier</label>
                <select id="choix-menu" name="menu">
                    <?php foreach ($menus_employe as $menu): ?>
                        <option value="<?php echo (int) $menu['id']; ?>" <?php echo $menu_selectionne && $menu['id'] === $menu_selectionne['id'] ? 'selected' : ''; ?>><?php echo texteEmploye($menu['titre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bouton-employe">Afficher le menu</button>
            </form>
            <?php if ($menu_selectionne): ?>
                <form id="edition-menu" class="formulaire-employe grille-gestion" action="espace-employe.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="action_employe" value="menu">
                    <input type="hidden" name="menu_id" value="<?php echo (int) $menu_selectionne['id']; ?>">
                    <label>Titre
                        <input type="text" name="titre" maxlength="100" value="<?php echo texteEmploye($menu_selectionne['titre']); ?>" required>
                    </label>
                    <label>Description
                        <textarea name="description" rows="3" required><?php echo texteEmploye($menu_selectionne['description']); ?></textarea>
                    </label>
                    <label>Thème
                        <select name="theme_id">
                            <?php foreach ($themes_employe as $theme): ?>
                                <option value="<?php echo (int) $theme['id']; ?>" <?php echo (int) $theme['id'] === (int) $menu_selectionne['theme_id'] ? 'selected' : ''; ?>><?php echo texteEmploye($theme['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Régime
                        <select name="regime_id">
                            <?php foreach ($regimes_employe as $regime): ?>
                                <option value="<?php echo (int) $regime['id']; ?>" <?php echo (int) $regime['id'] === (int) $menu_selectionne['regime_id'] ? 'selected' : ''; ?>><?php echo texteEmploye($regime['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Personnes minimum
                        <input type="number" name="personnes_minimum" min="1" value="<?php echo (int) $menu_selectionne['personnes_minimum']; ?>" required>
                    </label>
                    <label>Prix pour le nombre minimum (€)
                        <input type="number" name="prix_minimum" min="0.01" step="0.01" value="<?php echo texteEmploye($menu_selectionne['prix_minimum']); ?>" required>
                    </label>
                    <label>Stock disponible
                        <input type="number" name="stock_disponible" min="0" value="<?php echo (int) $menu_selectionne['stock_disponible']; ?>" required>
                    </label>
                    <label>Délai de réservation (heures)
                        <input type="number" name="delai_heures" min="1" value="<?php echo (int) $menu_selectionne['delai_heures']; ?>" required>
                    </label>
                    <label>Conditions
                        <textarea name="conditions" rows="3" required><?php echo texteEmploye($menu_selectionne['conditions_menu']); ?></textarea>
                    </label>
                    <button type="submit" class="bouton-employe">Enregistrer le menu</button>
                </form>
                <form class="formulaire-employe" action="espace-employe.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="action_employe" value="menu_actif">
                    <input type="hidden" name="menu_id" value="<?php echo (int) $menu_selectionne['id']; ?>">
                    <button type="submit" class="bouton-secondaire-gestion"><?php echo $menu_selectionne['actif'] ? 'Retirer le menu' : 'Rétablir le menu'; ?></button>
                </form>
            <?php endif; ?>
        </section>

        <section id="plats" class="bloc-employe">
            <h2>Plats</h2>
            <form class="formulaire-employe grille-gestion" action="espace-employe.php#edition-plat" method="get">
                <label for="choix-plat">Plat à modifier</label>
                <select id="choix-plat" name="plat">
                    <?php foreach ($plats_employe as $plat): ?>
                        <option value="<?php echo (int) $plat['id']; ?>" <?php echo $plat_selectionne && $plat['id'] === $plat_selectionne['id'] ? 'selected' : ''; ?>><?php echo texteEmploye($plat['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bouton-employe">Afficher le plat</button>
            </form>
            <?php if ($plat_selectionne): ?>
                <form id="edition-plat" class="formulaire-employe grille-gestion" action="espace-employe.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="action_employe" value="plat">
                    <input type="hidden" name="plat_id" value="<?php echo (int) $plat_selectionne['id']; ?>">
                    <label>Nom
                        <input name="nom" maxlength="200" value="<?php echo texteEmploye($plat_selectionne['nom']); ?>" required>
                    </label>
                    <label>Type
                        <select name="type_plat">
                            <?php foreach (['entree' => 'Entrée', 'plat' => 'Plat', 'dessert' => 'Dessert'] as $code => $libelle): ?>
                                <option value="<?php echo texteEmploye($code); ?>" <?php echo $code === $plat_selectionne['type_plat'] ? 'selected' : ''; ?>><?php echo texteEmploye($libelle); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <fieldset class="liste-allergenes-employe">
                        <legend>Allergènes</legend>
                        <?php foreach ($allergenes_employe as $allergene): ?>
                            <label class="case-gestion">
                                <input type="checkbox" name="allergenes[]" value="<?php echo (int) $allergene['id']; ?>" <?php echo in_array((int) $allergene['id'], $allergenes_du_plat, true) ? 'checked' : ''; ?>>
                                <?php echo texteEmploye($allergene['nom']); ?>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                    <button type="submit" class="bouton-employe">Enregistrer le plat</button>
                </form>
                <form class="formulaire-employe" action="espace-employe.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="action_employe" value="plat_supprimer">
                    <input type="hidden" name="plat_id" value="<?php echo (int) $plat_selectionne['id']; ?>">
                    <button type="submit" class="bouton-secondaire-gestion">Supprimer le plat</button>
                </form>
            <?php endif; ?>
        </section>

        <section id="horaires" class="bloc-employe">
            <h2>Horaires d'ouverture</h2>
            <form class="formulaire-employe grille-gestion" action="espace-employe.php#edition-horaires" method="get">
                <label for="choix-jour">Jour</label>
                <select id="choix-jour" name="jour">
                    <?php foreach ($horaires_employe as $horaire): ?>
                        <option value="<?php echo (int) $horaire['id']; ?>" <?php echo $horaire_selectionne && $horaire['id'] === $horaire_selectionne['id'] ? 'selected' : ''; ?>><?php echo texteEmploye($horaire['jour']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bouton-employe">Afficher les horaires</button>
            </form>
            <?php if ($horaire_selectionne): ?>
                <form id="edition-horaires" class="formulaire-employe grille-gestion" action="espace-employe.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
                    <input type="hidden" name="action_employe" value="horaire">
                    <input type="hidden" name="jour_id" value="<?php echo (int) $horaire_selectionne['id']; ?>">
                    <p><strong><?php echo texteEmploye($horaire_selectionne['jour']); ?></strong></p>
                    <label>Ouverture
                        <input type="time" name="ouverture" value="<?php echo texteEmploye(substr((string) $horaire_selectionne['heure_ouverture'], 0, 5)); ?>">
                    </label>
                    <label>Fermeture
                        <input type="time" name="fermeture" value="<?php echo texteEmploye(substr((string) $horaire_selectionne['heure_fermeture'], 0, 5)); ?>">
                    </label>
                    <label class="case-gestion">
                        <input type="checkbox" name="ferme" value="oui" <?php echo $horaire_selectionne['ferme'] ? 'checked' : ''; ?>>
                        Fermé ce jour
                    </label>
                    <button type="submit" class="bouton-employe">Enregistrer les horaires</button>
                </form>
            <?php endif; ?>
        </section>

        <section id="avis" class="bloc-employe">
            <h2>Avis clients</h2>
            <?php if (!$avis_employe): ?>
                <p>Aucun avis reçu pour le moment.</p>
            <?php endif; ?>
            <?php foreach ($avis_employe as $avis): ?>
                <article class="fiche-commande">
                    <h3>Commande n°<?php echo (int) $avis['commande_id']; ?> — <?php echo texteEmploye($avis['prenom'] . ' ' . $avis['nom']); ?></h3>
                    <p><strong>Note :</strong> <?php echo (int) $avis['note']; ?>/5</p>
                    <p><?php echo nl2br(texteEmploye($avis['commentaire'])); ?></p>
                    <p><strong>Statut :</strong> <?php echo texteEmploye($avis['statut']); ?></p>
                    <?php if ($avis['statut'] === 'en_attente'): ?>
                        <form class="actions-avis-employe" action="espace-employe.php" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo texteEmploye($_SESSION['csrf_token']); ?>">
                            <input type="hidden" name="action_employe" value="avis">
                            <input type="hidden" name="avis_id" value="<?php echo (int) $avis['id']; ?>">
                            <button class="bouton-employe" type="submit" name="decision" value="valide">Valider</button>
                            <button class="bouton-secondaire-gestion" type="submit" name="decision" value="refuse">Refuser</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <footer>
        <div class="footer-contenu">
            <section>
                <h2>Horaires d'ouverture</h2>
                <ul class="horaires-pc">
                    <?php foreach ($horaires_employe as $horaire): ?>
                        <li><?php echo texteEmploye($horaire['jour']); ?> : <?php echo $horaire['ferme'] ? 'Fermé' : texteEmploye(substr($horaire['heure_ouverture'], 0, 5) . ' - ' . substr($horaire['heure_fermeture'], 0, 5)); ?></li>
                    <?php endforeach; ?>
                </ul>
                <ul class="horaires-mobile">
                    <?php foreach ($horaires_employe as $horaire): ?>
                        <li><?php echo texteEmploye($horaire['jour']); ?> : <?php echo $horaire['ferme'] ? 'Fermé' : texteEmploye(substr($horaire['heure_ouverture'], 0, 5) . ' - ' . substr($horaire['heure_fermeture'], 0, 5)); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <div class="informations-legales">
                <p><a href="mentions-legales.html">Mentions légales</a></p>
                <p><a href="conditions-generales-de-vente.html">Conditions générales de vente</a></p>
            </div>
        </div>
    </footer>
    <script src="./assets/js/horaires-public.js"></script>
</body>
</html>
