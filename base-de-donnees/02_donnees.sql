-- Données de départ : rôles, deux menus, plats, allergènes et horaires.
USE vite_et_gourmand;

INSERT INTO roles (id, nom) VALUES
(1, 'utilisateur'),
(2, 'employe'),
(3, 'administrateur');

INSERT INTO themes (id, nom) VALUES
(1, 'classique'),
(2, 'noel'),
(3, 'paques'),
(4, 'evenement');

INSERT INTO regimes (id, nom) VALUES
(1, 'classique'),
(2, 'vegetarien'),
(3, 'vegan');

INSERT INTO menus (
    id, code, titre, description, theme_id, regime_id,
    personnes_minimum, prix_minimum, stock_disponible,
    delai_heures, conditions_menu, photo_principale, actif
) VALUES
(1, 'classique', 'Menu Classique',
 'Tomates-mozzarella, poulet rôti et tarte aux pommes.',
 1, 1, 10, 250.00, 5, 72, 'Réservation au moins 72 h à l''avance.',
 'assets/images/menus/Menu classique _ poulet roti.png', 1),
(2, 'vegetarien', 'Menu Végétarien',
 'Salade de saison, risotto aux champignons et mousse au chocolat.',
 1, 2, 8, 200.00, 5, 72, 'Réservation au moins 72 h à l''avance.',
 'assets/images/menus/végétarien gourmand aux champignons.png', 1);

INSERT INTO plats (id, nom, type_plat) VALUES
(1, 'Tomates, mozzarella et basilic', 'entree'),
(2, 'Poulet rôti, pommes de terre au beurre', 'plat'),
(3, 'Tarte aux pommes', 'dessert'),
(4, 'Salade de saison : laitue, tomates, concombre, mozzarella et basilic', 'entree'),
(5, 'Risotto aux champignons, beurre et crème', 'plat'),
(6, 'Mousse au chocolat noir, œufs et crème', 'dessert');

INSERT INTO menus_plats (menu_id, plat_id) VALUES
(1, 1), (1, 2), (1, 3),
(2, 4), (2, 5), (2, 6);

INSERT INTO allergenes (id, nom) VALUES
(1, 'Lait'),
(2, 'Blé (gluten)'),
(3, 'Œufs');

INSERT INTO plats_allergenes (plat_id, allergene_id) VALUES
(1, 1),
(2, 1),
(3, 1), (3, 2), (3, 3),
(4, 1),
(5, 1),
(6, 1), (6, 3);

INSERT INTO photos_menus (menu_id, chemin, position) VALUES
(1, 'assets/images/detail-menu/Poulet rôti, légumes et jus doré.png', 1),
(1, 'assets/images/detail-menu/Salade caprese au basilic frais.png', 2),
(1, 'assets/images/detail-menu/Tarte aux pommes caramélisées.png', 3),
(2, 'assets/images/detail-menu/Menu végétarien – Quiche aux légumes.png', 1),
(2, 'assets/images/detail-menu/menu.png', 2),
(2, 'assets/images/detail-menu/Gratin végétal et tarte citron meringuée.png', 3);

INSERT INTO horaires (id, jour, heure_ouverture, heure_fermeture, ferme) VALUES
(1, 'Lundi', '09:00:00', '18:00:00', 0),
(2, 'Mardi', '09:00:00', '18:00:00', 0),
(3, 'Mercredi', '09:00:00', '18:00:00', 0),
(4, 'Jeudi', '09:00:00', '18:00:00', 0),
(5, 'Vendredi', '09:00:00', '18:00:00', 0),
(6, 'Samedi', '09:00:00', '17:00:00', 0),
(7, 'Dimanche', NULL, NULL, 1);
