-- Structure de la base de données de Vite & Gourmand.
CREATE DATABASE IF NOT EXISTS vite_et_gourmand
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE vite_et_gourmand;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(30) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL DEFAULT 1,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(30) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    adresse_postale VARCHAR(255) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE reinitialisations_mdp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    jeton_hash CHAR(64) NOT NULL UNIQUE,
    date_expiration DATETIME NOT NULL,
    date_utilisation DATETIME NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
) ENGINE=InnoDB;

CREATE TABLE themes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE regimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    titre VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    theme_id INT NOT NULL,
    regime_id INT NOT NULL,
    personnes_minimum INT NOT NULL,
    prix_minimum DECIMAL(10,2) NOT NULL,
    stock_disponible INT NOT NULL DEFAULT 0,
    delai_heures INT NOT NULL DEFAULT 72,
    conditions_menu TEXT NOT NULL,
    photo_principale VARCHAR(255) NOT NULL,
    actif TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (theme_id) REFERENCES themes(id),
    FOREIGN KEY (regime_id) REFERENCES regimes(id)
) ENGINE=InnoDB;

CREATE TABLE plats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    type_plat VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE menus_plats (
    menu_id INT NOT NULL,
    plat_id INT NOT NULL,
    PRIMARY KEY (menu_id, plat_id),
    FOREIGN KEY (menu_id) REFERENCES menus(id),
    FOREIGN KEY (plat_id) REFERENCES plats(id)
) ENGINE=InnoDB;

CREATE TABLE allergenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE plats_allergenes (
    plat_id INT NOT NULL,
    allergene_id INT NOT NULL,
    PRIMARY KEY (plat_id, allergene_id),
    FOREIGN KEY (plat_id) REFERENCES plats(id),
    FOREIGN KEY (allergene_id) REFERENCES allergenes(id)
) ENGINE=InnoDB;

CREATE TABLE photos_menus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_id INT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    position INT NOT NULL,
    FOREIGN KEY (menu_id) REFERENCES menus(id)
) ENGINE=InnoDB;

CREATE TABLE horaires (
    id INT PRIMARY KEY,
    jour VARCHAR(20) NOT NULL UNIQUE,
    heure_ouverture TIME NULL,
    heure_fermeture TIME NULL,
    ferme TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    menu_id INT NOT NULL,
    date_commande DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_prestation DATE NOT NULL,
    adresse_prestation VARCHAR(255) NOT NULL,
    date_livraison DATE NOT NULL,
    heure_livraison TIME NOT NULL,
    adresse_livraison VARCHAR(255) NOT NULL,
    hors_bordeaux TINYINT(1) NOT NULL DEFAULT 0,
    distance_km DECIMAL(8,2) NOT NULL DEFAULT 0,
    nombre_personnes INT NOT NULL,
    prix_menu DECIMAL(10,2) NOT NULL,
    reduction DECIMAL(10,2) NOT NULL DEFAULT 0,
    frais_livraison DECIMAL(10,2) NOT NULL DEFAULT 0,
    prix_total DECIMAL(10,2) NOT NULL,
    statut VARCHAR(40) NOT NULL DEFAULT 'en_attente',
    mode_contact_annulation VARCHAR(20) NULL,
    motif_annulation TEXT NULL,
    materiel_prete TINYINT(1) NOT NULL DEFAULT 0,
    date_notification_materiel DATETIME NULL,
    date_retour_materiel DATETIME NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (menu_id) REFERENCES menus(id)
) ENGINE=InnoDB;

CREATE TABLE historique_commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    utilisateur_id INT NULL,
    statut VARCHAR(40) NOT NULL,
    date_changement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    commentaire VARCHAR(255) NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
) ENGINE=InnoDB;

CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL UNIQUE,
    note INT NOT NULL,
    commentaire TEXT NOT NULL,
    statut VARCHAR(20) NOT NULL DEFAULT 'en_attente',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_validation DATETIME NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id)
) ENGINE=InnoDB;
