<?php
require __DIR__ . '/php/authentification.php';
verifierAcces([2, 3]);
require __DIR__ . '/php/connexion.php';

function echapperCatalogue($valeur) {
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

function champCatalogue($nom) {
    return isset($_POST[$nom]) && is_string($_POST[$nom]) ? trim($_POST[$nom]) : '';
}

function nombreCatalogue($nom, $minimum, $maximum) {
    $valeur = champCatalogue($nom);
    if (!ctype_digit($valeur) || (int) $valeur < $minimum || (int) $valeur > $maximum) {
        throw new InvalidArgumentException('Vérifiez les valeurs numériques du formulaire.');
    }
    return (int) $valeur;
}

function choixCatalogue(PDO $pdo, $table, $id) {
    if (!in_array($table, ['menus', 'plats', 'themes', 'regimes'], true)) {
        throw new InvalidArgumentException('Table invalide.');
    }
    $requete = $pdo->prepare('SELECT id FROM ' . $table . ' WHERE id = ?');
    $requete->execute([$id]);
    if (!$requete->fetchColumn()) {
        throw new InvalidArgumentException('Élément introuvable.');
    }
}

$images_catalogue = [];
foreach (['assets/images/*', 'assets/images/*/*'] as $motif) {
    foreach (glob(__DIR__ . '/' . $motif) ?: [] as $chemin) {
        if (is_file($chemin) && in_array(strtolower(pathinfo($chemin, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $images_catalogue[] = str_replace('\\', '/', substr($chemin, strlen(__DIR__) + 1));
        }
    }
}
sort($images_catalogue);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = '';
    $erreur = false;
    try {
        if (!isset($_POST['csrf_token']) || !is_string($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new InvalidArgumentException('Le formulaire a expiré. Actualisez la page.');
        }
        $action = champCatalogue('action_catalogue');
        if ($action === 'menu_creer' || $action === 'menu_modifier') {
            $titre = champCatalogue('titre');
            $description = champCatalogue('description');
            $conditions = champCatalogue('conditions_menu');
            $theme = nombreCatalogue('theme_id', 1, 9999);
            $regime = nombreCatalogue('regime_id', 1, 9999);
            $minimum = nombreCatalogue('personnes_minimum', 1, 5000);
            $stock = nombreCatalogue('stock_disponible', 0, 100000);
            $delai = nombreCatalogue('delai_heures', 1, 10000);
            $prix_texte = champCatalogue('prix_minimum');
            $prix = (float) $prix_texte;
            $photo = champCatalogue('photo_principale');
            if ($titre === '' || strlen($titre) > 100 || $description === '' || strlen($description) > 5000 ||
                $conditions === '' || strlen($conditions) > 5000 || !is_numeric($prix_texte) || !is_finite($prix) ||
                $prix <= 0 || $prix > 999999 || !in_array($photo, $images_catalogue, true)) {
                throw new InvalidArgumentException('Vérifiez le titre, le prix, la description, les conditions et la photo.');
            }
            choixCatalogue($pdo, 'themes', $theme);
            choixCatalogue($pdo, 'regimes', $regime);
            if ($action === 'menu_creer') {
                $code = champCatalogue('code');
                if (!preg_match('/^[a-z0-9-]{2,50}$/', $code)) {
                    throw new InvalidArgumentException('Le code du menu doit contenir des lettres minuscules, chiffres ou tirets.');
                }
                $requete = $pdo->prepare('INSERT INTO menus (code, titre, description, theme_id, regime_id, personnes_minimum, prix_minimum, stock_disponible, delai_heures, conditions_menu, photo_principale, actif) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)');
                $requete->execute([$code, $titre, $description, $theme, $regime, $minimum, $prix, $stock, $delai, $conditions, $photo]);
                $message = 'Le menu a été créé.';
            } else {
                $menu_id = nombreCatalogue('menu_id', 1, 10000000);
                choixCatalogue($pdo, 'menus', $menu_id);
                $actif = champCatalogue('actif') === 'oui' ? 1 : 0;
                $requete = $pdo->prepare('UPDATE menus SET titre = ?, description = ?, theme_id = ?, regime_id = ?, personnes_minimum = ?, prix_minimum = ?, stock_disponible = ?, delai_heures = ?, conditions_menu = ?, photo_principale = ?, actif = ? WHERE id = ?');
                $requete->execute([$titre, $description, $theme, $regime, $minimum, $prix, $stock, $delai, $conditions, $photo, $actif, $menu_id]);
                $message = 'Le menu a été mis à jour.';
            }
        } elseif ($action === 'menu_retirer') {
            $menu_id = nombreCatalogue('menu_id', 1, 10000000);
            choixCatalogue($pdo, 'menus', $menu_id);
            $requete = $pdo->prepare('UPDATE menus SET actif = 0 WHERE id = ?');
            $requete->execute([$menu_id]);
            $message = 'Le menu a été retiré de la vente. Les anciennes commandes sont conservées.';
        } elseif ($action === 'menu_plats') {
            $menu_id = nombreCatalogue('menu_id', 1, 10000000);
            choixCatalogue($pdo, 'menus', $menu_id);
            $selection = [
                'entree' => nombreCatalogue('entree_id', 1, 10000000),
                'plat' => nombreCatalogue('plat_id', 1, 10000000),
                'dessert' => nombreCatalogue('dessert_id', 1, 10000000)
            ];
            if (count(array_unique(array_values($selection))) !== 3) {
                throw new InvalidArgumentException('Choisissez trois plats distincts.');
            }
            $requete = $pdo->prepare('SELECT type_plat FROM plats WHERE id = ?');
            foreach ($selection as $type => $id) {
                $requete->execute([$id]);
                if ($requete->fetchColumn() !== $type) {
                    throw new InvalidArgumentException('Le plat sélectionné ne correspond pas à sa catégorie.');
                }
            }
            $pdo->beginTransaction();
            $requete = $pdo->prepare('DELETE FROM menus_plats WHERE menu_id = ?');
            $requete->execute([$menu_id]);
            $requete = $pdo->prepare('INSERT INTO menus_plats (menu_id, plat_id) VALUES (?, ?)');
            foreach ($selection as $id) {
                $requete->execute([$menu_id, $id]);
            }
            $pdo->commit();
            $message = 'La composition du menu a été enregistrée.';
        } elseif ($action === 'photo_ajouter') {
            $menu_id = nombreCatalogue('menu_id', 1, 10000000);
            choixCatalogue($pdo, 'menus', $menu_id);
            $photo = champCatalogue('chemin');
            if (isset($_FILES['nouvelle_photo']) && $_FILES['nouvelle_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                $fichier = $_FILES['nouvelle_photo'];
                if ($fichier['error'] !== UPLOAD_ERR_OK || $fichier['size'] > 4 * 1024 * 1024) {
                    throw new InvalidArgumentException('La photo doit être une image valide de moins de 4 Mo.');
                }
                $mime = (new finfo(FILEINFO_MIME_TYPE))->file($fichier['tmp_name']);
                $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                if (!isset($extensions[$mime]) || getimagesize($fichier['tmp_name']) === false) {
                    throw new InvalidArgumentException('Seuls les formats JPG, PNG et WebP sont acceptés.');
                }
                $dossier = __DIR__ . '/assets/images/catalogue';
                if (!is_dir($dossier)) {
                    mkdir($dossier, 0755, true);
                }
                $photo = 'assets/images/catalogue/' . bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
                if (!move_uploaded_file($fichier['tmp_name'], __DIR__ . '/' . $photo)) {
                    throw new InvalidArgumentException('La photo n’a pas pu être enregistrée.');
                }
            } elseif (!in_array($photo, $images_catalogue, true)) {
                throw new InvalidArgumentException('Choisissez une photo existante ou importez-en une nouvelle.');
            }
            $requete = $pdo->prepare('SELECT COALESCE(MAX(position), 0) + 1 FROM photos_menus WHERE menu_id = ?');
            $requete->execute([$menu_id]);
            $position = (int) $requete->fetchColumn();
            $requete = $pdo->prepare('INSERT INTO photos_menus (menu_id, chemin, position) VALUES (?, ?, ?)');
            $requete->execute([$menu_id, $photo, $position]);
            $message = 'La photo a été ajoutée à la galerie.';
        } elseif ($action === 'photo_retirer') {
            $photo_id = nombreCatalogue('photo_id', 1, 10000000);
            $requete = $pdo->prepare('DELETE FROM photos_menus WHERE id = ?');
            $requete->execute([$photo_id]);
            $message = $requete->rowCount() ? 'La photo a été retirée de la galerie.' : 'Photo introuvable.';
        } elseif (in_array($action, ['plat_creer', 'plat_modifier', 'plat_supprimer'], true)) {
            $plat_id = $action === 'plat_creer' ? 0 : nombreCatalogue('plat_id', 1, 10000000);
            if ($action !== 'plat_creer') {
                choixCatalogue($pdo, 'plats', $plat_id);
            }
            if ($action === 'plat_supprimer') {
                $requete = $pdo->prepare('SELECT 1 FROM menus_plats WHERE plat_id = ? LIMIT 1');
                $requete->execute([$plat_id]);
                if ($requete->fetchColumn()) {
                    throw new InvalidArgumentException('Retirez le plat des menus avant de le supprimer.');
                }
                $pdo->beginTransaction();
                $requete = $pdo->prepare('DELETE FROM plats_allergenes WHERE plat_id = ?');
                $requete->execute([$plat_id]);
                $requete = $pdo->prepare('DELETE FROM plats WHERE id = ?');
                $requete->execute([$plat_id]);
                $pdo->commit();
                $message = 'Le plat a été supprimé.';
            } else {
                $nom = champCatalogue('nom_plat');
                $type = champCatalogue('type_plat');
                if ($nom === '' || strlen($nom) > 200 || !in_array($type, ['entree', 'plat', 'dessert'], true)) {
                    throw new InvalidArgumentException('Vérifiez le nom et la catégorie du plat.');
                }
                if ($action === 'plat_modifier') {
                    $requete = $pdo->prepare('SELECT type_plat FROM plats WHERE id = ?');
                    $requete->execute([$plat_id]);
                    $ancien_type = $requete->fetchColumn();
                    if ($type !== $ancien_type) {
                        $requete = $pdo->prepare('SELECT 1 FROM menus_plats WHERE plat_id = ? LIMIT 1');
                        $requete->execute([$plat_id]);
                        if ($requete->fetchColumn()) {
                            throw new InvalidArgumentException('La catégorie d’un plat utilisé dans un menu ne peut pas changer.');
                        }
                    }
                }
                $ids_allergenes = $_POST['allergenes'] ?? [];
                if (!is_array($ids_allergenes) || count($ids_allergenes) > 50) {
                    throw new InvalidArgumentException('La sélection des allergènes est invalide.');
                }
                $ids = [];
                foreach ($ids_allergenes as $valeur) {
                    if (!is_string($valeur) || !ctype_digit($valeur)) {
                        throw new InvalidArgumentException('La sélection des allergènes est invalide.');
                    }
                    $id = (int) $valeur;
                    $requete = $pdo->prepare('SELECT 1 FROM allergenes WHERE id = ?');
                    $requete->execute([$id]);
                    if (!$requete->fetchColumn()) {
                        throw new InvalidArgumentException('Un allergène sélectionné n’existe pas.');
                    }
                    $ids[] = $id;
                }
                $ids = array_unique($ids);
                $pdo->beginTransaction();
                if ($action === 'plat_creer') {
                    $requete = $pdo->prepare('INSERT INTO plats (nom, type_plat) VALUES (?, ?)');
                    $requete->execute([$nom, $type]);
                    $plat_id = (int) $pdo->lastInsertId();
                } else {
                    $requete = $pdo->prepare('UPDATE plats SET nom = ?, type_plat = ? WHERE id = ?');
                    $requete->execute([$nom, $type, $plat_id]);
                    $requete = $pdo->prepare('DELETE FROM plats_allergenes WHERE plat_id = ?');
                    $requete->execute([$plat_id]);
                }
                $requete = $pdo->prepare('INSERT INTO plats_allergenes (plat_id, allergene_id) VALUES (?, ?)');
                foreach ($ids as $id) {
                    $requete->execute([$plat_id, $id]);
                }
                $pdo->commit();
                $message = 'Le plat et ses allergènes ont été enregistrés.';
            }
        } else {
            throw new InvalidArgumentException('Action non reconnue.');
        }
    } catch (InvalidArgumentException $exception) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = $exception->getMessage();
        $erreur = true;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $message = 'Impossible d’enregistrer la modification. Vérifiez les informations.';
        $erreur = true;
    }
    $_SESSION['message_catalogue'] = $message;
    $_SESSION['erreur_catalogue'] = $erreur;
    header('Location: gestion-catalogue.php');
    exit;
}

$message = $_SESSION['message_catalogue'] ?? '';
$erreur = $_SESSION['erreur_catalogue'] ?? false;
unset($_SESSION['message_catalogue'], $_SESSION['erreur_catalogue']);
$menus = $pdo->query('SELECT * FROM menus ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
$themes = $pdo->query('SELECT id, nom FROM themes ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
$regimes = $pdo->query('SELECT id, nom FROM regimes ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
$plats = $pdo->query('SELECT id, nom, type_plat FROM plats ORDER BY type_plat, nom')->fetchAll(PDO::FETCH_ASSOC);
$allergenes = $pdo->query('SELECT id, nom FROM allergenes ORDER BY nom')->fetchAll(PDO::FETCH_ASSOC);
$liens_allergenes = $pdo->query('SELECT plat_id, allergene_id FROM plats_allergenes')->fetchAll(PDO::FETCH_ASSOC);
$allergenes_par_plat = [];
foreach ($liens_allergenes as $lien) {
    $allergenes_par_plat[$lien['plat_id']][] = $lien['allergene_id'];
}
$photos = $pdo->query('SELECT pm.id, pm.chemin, m.titre FROM photos_menus pm INNER JOIN menus m ON m.id = pm.menu_id ORDER BY m.id, pm.position')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des menus, des plats et des galeries Vite & Gourmand.">
    <title>Gestion du catalogue - Vite &amp; Gourmand</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/gestion.css">
    <link rel="stylesheet" href="./assets/css/front-finitions.css">
    <link rel="stylesheet" href="./assets/css/authentification.css">
</head>
<body class="page-administration">
    <a class="lien-evitement" href="#contenu-principal">Aller au contenu principal</a>
    <header>
        <a href="index.php">Vite <span class="texte-dore">&amp;</span> Gourmand</a>
        <nav class="navigation-pc" aria-label="Navigation principale">
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menus.php">Tous les menus</a></li>
                <li>
                    <form action="deconnexion.php" method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                        <button class="lien-deconnexion" type="submit">Déconnexion</button>
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
                            <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                            <button class="lien-deconnexion" type="submit">Déconnexion</button>
                        </form>
                    </li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </nav>
        </details>
    </header>
    <main id="contenu-principal">
        <h1>Gestion du catalogue</h1>
        <p>Gérez les menus, les plats, les allergènes et les photos proposés aux clients.</p>
        <?php if ($message !== ''): ?>
            <p role="status" class="message-administration"><?php echo echapperCatalogue($message); ?></p>
        <?php endif; ?>

        <section class="bloc-administration">
            <h2>Créer un menu</h2>
            <form class="grille-gestion" action="gestion-catalogue.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_catalogue" value="menu_creer">
                <label>Code du menu (minuscules, chiffres, tirets)
                    <input name="code" type="text" pattern="[a-z0-9-]{2,50}" maxlength="50" required>
                </label>
                <label>Titre
                    <input name="titre" maxlength="100" required>
                </label>
                <label>Description
                    <textarea name="description" rows="3" required></textarea>
                </label>
                <label>Thème
                    <select name="theme_id" required>
                        <?php foreach ($themes as $theme): ?>
                            <option value="<?php echo (int) $theme['id']; ?>"><?php echo echapperCatalogue($theme['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Régime
                    <select name="regime_id" required>
                        <?php foreach ($regimes as $regime): ?>
                            <option value="<?php echo (int) $regime['id']; ?>"><?php echo echapperCatalogue($regime['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Personnes minimum
                    <input type="number" name="personnes_minimum" min="1" value="10" required>
                </label>
                <label>Prix pour ce minimum (€)
                    <input type="number" name="prix_minimum" step="0.01" min="0.01" required>
                </label>
                <label>Stock disponible
                    <input type="number" name="stock_disponible" min="0" value="5" required>
                </label>
                <label>Délai minimum de réservation (heures)
                    <input type="number" name="delai_heures" min="1" value="72" required>
                </label>
                <label>Conditions du menu
                    <textarea name="conditions_menu" rows="3" required></textarea>
                </label>
                <label>Photo principale
                    <select name="photo_principale" required>
                        <?php foreach ($images_catalogue as $image): ?>
                            <option value="<?php echo echapperCatalogue($image); ?>"><?php echo echapperCatalogue(basename($image)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button class="bouton-administration" type="submit">Créer le menu</button>
            </form>
        </section>

        <section class="bloc-administration">
            <h2>Modifier un menu</h2>
            <p>Sélectionnez un menu, puis renseignez ses informations actualisées.</p>
            <form class="grille-gestion" id="form-modifier-menu" action="gestion-catalogue.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_catalogue" value="menu_modifier">
                <label>Menu à modifier
                    <select name="menu_id" required>
                        <?php foreach ($menus as $menu): ?>
                            <option
                                value="<?php echo (int) $menu['id']; ?>"
                                data-titre="<?php echo echapperCatalogue($menu['titre']); ?>"
                                data-description="<?php echo echapperCatalogue($menu['description']); ?>"
                                data-theme="<?php echo (int) $menu['theme_id']; ?>"
                                data-regime="<?php echo (int) $menu['regime_id']; ?>"
                                data-minimum="<?php echo (int) $menu['personnes_minimum']; ?>"
                                data-prix="<?php echo echapperCatalogue($menu['prix_minimum']); ?>"
                                data-stock="<?php echo (int) $menu['stock_disponible']; ?>"
                                data-delai="<?php echo (int) $menu['delai_heures']; ?>"
                                data-conditions="<?php echo echapperCatalogue($menu['conditions_menu']); ?>"
                                data-photo="<?php echo echapperCatalogue($menu['photo_principale']); ?>"
                                data-actif="<?php echo (int) $menu['actif']; ?>"
                            ><?php echo echapperCatalogue($menu['titre']); ?> (<?php echo echapperCatalogue($menu['code']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Nouveau titre
                    <input name="titre" maxlength="100" required>
                </label>
                <label>Nouvelle description
                    <textarea name="description" rows="3" required></textarea>
                </label>
                <label>Thème
                    <select name="theme_id">
                        <?php foreach ($themes as $theme): ?>
                            <option value="<?php echo (int) $theme['id']; ?>"><?php echo echapperCatalogue($theme['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Régime
                    <select name="regime_id">
                        <?php foreach ($regimes as $regime): ?>
                            <option value="<?php echo (int) $regime['id']; ?>"><?php echo echapperCatalogue($regime['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Personnes minimum
                    <input type="number" name="personnes_minimum" min="1" required>
                </label>
                <label>Prix minimum (€)
                    <input type="number" name="prix_minimum" step="0.01" min="0.01" required>
                </label>
                <label>Stock
                    <input type="number" name="stock_disponible" min="0" required>
                </label>
                <label>Délai (heures)
                    <input type="number" name="delai_heures" min="1" required>
                </label>
                <label>Conditions
                    <textarea name="conditions_menu" rows="3" required></textarea>
                </label>
                <label>Photo principale
                    <select name="photo_principale" required>
                        <?php foreach ($images_catalogue as $image): ?>
                            <option value="<?php echo echapperCatalogue($image); ?>"><?php echo echapperCatalogue(basename($image)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="case-gestion">
                    <input type="checkbox" name="actif" value="oui" checked>
                    Menu proposé à la vente
                </label>
                <button class="bouton-administration" type="submit">Enregistrer les modifications</button>
            </form>
            <form class="grille-gestion espacement-gestion" action="gestion-catalogue.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_catalogue" value="menu_retirer">
                <label>Menu à retirer de la vente
                    <select name="menu_id">
                        <?php foreach ($menus as $menu): ?>
                            <option value="<?php echo (int) $menu['id']; ?>"><?php echo echapperCatalogue($menu['titre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button class="bouton-secondaire-gestion" type="submit">Retirer de la vente</button>
            </form>
        </section>

        <section class="bloc-administration">
            <h2>Composition des menus</h2>
            <form class="grille-gestion" action="gestion-catalogue.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_catalogue" value="menu_plats">
                <label>Menu
                    <select name="menu_id" required>
                        <?php foreach ($menus as $menu): ?>
                            <option value="<?php echo (int) $menu['id']; ?>"><?php echo echapperCatalogue($menu['titre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <?php foreach (['entree' => 'Entrée', 'plat' => 'Plat', 'dessert' => 'Dessert'] as $type => $titre): ?>
                    <label><?php echo echapperCatalogue($titre); ?>
                        <select name="<?php echo $type; ?>_id" required>
                            <?php foreach ($plats as $plat): ?>
                                <?php if ($plat['type_plat'] === $type): ?>
                                    <option value="<?php echo (int) $plat['id']; ?>"><?php echo echapperCatalogue($plat['nom']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </label>
                <?php endforeach; ?>
                <button class="bouton-administration" type="submit">Enregistrer la composition</button>
            </form>
        </section>

        <section class="bloc-administration">
            <h2>Photos des menus</h2>
            <form class="grille-gestion" action="gestion-catalogue.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_catalogue" value="photo_ajouter">
                <label>Menu
                    <select name="menu_id" required>
                        <?php foreach ($menus as $menu): ?>
                            <option value="<?php echo (int) $menu['id']; ?>"><?php echo echapperCatalogue($menu['titre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Choisir une image existante
                    <select name="chemin">
                        <option value="">Choisir une image</option>
                        <?php foreach ($images_catalogue as $image): ?>
                            <option value="<?php echo echapperCatalogue($image); ?>"><?php echo echapperCatalogue(basename($image)); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <p>Vous pouvez aussi importer une nouvelle image.</p>
                <label>Importer une image JPG, PNG ou WebP
                    <input type="file" name="nouvelle_photo" accept="image/jpeg,image/png,image/webp">
                </label>
                <button class="bouton-administration" type="submit">Ajouter à la galerie</button>
            </form>
            <form class="grille-gestion espacement-gestion" action="gestion-catalogue.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_catalogue" value="photo_retirer">
                <label>Retirer une photo de galerie
                    <select name="photo_id" required>
                        <?php foreach ($photos as $photo): ?>
                            <option value="<?php echo (int) $photo['id']; ?>"><?php echo echapperCatalogue($photo['titre'] . ' — ' . basename($photo['chemin'])); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button class="bouton-secondaire-gestion" type="submit">Retirer la photo</button>
            </form>
        </section>

        <section class="bloc-administration">
            <h2>Plats et allergènes</h2>
            <form class="grille-gestion" action="gestion-catalogue.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_catalogue" value="plat_creer">
                <label>Nom du plat
                    <input name="nom_plat" maxlength="200" required>
                </label>
                <label>Catégorie
                    <select name="type_plat">
                        <option value="entree">Entrée</option>
                        <option value="plat">Plat</option>
                        <option value="dessert">Dessert</option>
                    </select>
                </label>
                <fieldset>
                    <legend>Allergènes</legend>
                    <?php foreach ($allergenes as $allergene): ?>
                        <label class="case-gestion">
                            <input type="checkbox" name="allergenes[]" value="<?php echo (int) $allergene['id']; ?>">
                            <?php echo echapperCatalogue($allergene['nom']); ?>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
                <button class="bouton-administration" type="submit">Créer le plat</button>
            </form>
            <form class="grille-gestion espacement-gestion" id="form-modifier-plat" action="gestion-catalogue.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_catalogue" value="plat_modifier">
                <label>Plat à modifier
                    <select name="plat_id">
                        <?php foreach ($plats as $plat): ?>
                            <option value="<?php echo (int) $plat['id']; ?>" data-nom="<?php echo echapperCatalogue($plat['nom']); ?>" data-type="<?php echo echapperCatalogue($plat['type_plat']); ?>" data-allergenes="<?php echo echapperCatalogue(implode(',', $allergenes_par_plat[$plat['id']] ?? [])); ?>"><?php echo echapperCatalogue($plat['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Nouveau nom
                    <input name="nom_plat" maxlength="200" required>
                </label>
                <label>Catégorie
                    <select name="type_plat">
                        <option value="entree">Entrée</option>
                        <option value="plat">Plat</option>
                        <option value="dessert">Dessert</option>
                    </select>
                </label>
                <fieldset>
                    <legend>Allergènes du plat</legend>
                    <?php foreach ($allergenes as $allergene): ?>
                        <label class="case-gestion">
                            <input type="checkbox" name="allergenes[]" value="<?php echo (int) $allergene['id']; ?>">
                            <?php echo echapperCatalogue($allergene['nom']); ?>
                        </label>
                    <?php endforeach; ?>
                </fieldset>
                <button class="bouton-administration" type="submit">Enregistrer le plat</button>
            </form>
            <form class="grille-gestion espacement-gestion" action="gestion-catalogue.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo echapperCatalogue($_SESSION['csrf_token']); ?>">
                <input type="hidden" name="action_catalogue" value="plat_supprimer">
                <label>Plat à supprimer
                    <select name="plat_id">
                        <?php foreach ($plats as $plat): ?>
                            <option value="<?php echo (int) $plat['id']; ?>"><?php echo echapperCatalogue($plat['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button class="bouton-secondaire-gestion" type="submit">Supprimer le plat</button>
            </form>
        </section>
        <p><a href="espace-employe.php">Retour à l'espace employé</a> — <a href="menus.php">Voir les menus</a></p>
    </main>
    <footer>
        <div class="footer-contenu">
            <section>
                <h2>Horaires d'ouverture</h2>
                <ul class="horaires-pc"></ul>
                <ul class="horaires-mobile"></ul>
            </section>
            <div class="informations-legales">
                <p><a href="mentions-legales.html">Mentions légales</a></p>
                <p><a href="conditions-generales-de-vente.html">Conditions générales de vente</a></p>
            </div>
        </div>
    </footer>
    <script src="./assets/js/gestion-catalogue-sql.js"></script>
    <script src="./assets/js/horaires-public.js"></script>
</body>
</html>
