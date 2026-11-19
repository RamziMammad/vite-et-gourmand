# Vite & Gourmand — Application web

Ce dossier contient le site de traiteur Vite & Gourmand réalisé pour l'ECF Studi Développeur Web et Web Mobile.

## Pages et accès

- `index.php` : accueil, horaires et avis validés.
- `menus.php` et `detail-menu.php` : catalogue, filtres et détail des menus.
- `inscription.php`, `connexion.php`, `mot-de-passe-oublie.php` : accès utilisateur.
- `commande.php`, `confirmation-commande.php` : commande et confirmation.
- `espace-utilisateur.php` : suivi et actions du client.
- `espace-employe.php` : gestion des commandes, menus, plats, horaires et avis.
- `espace-administrateur.php` : gestion des employés et statistiques.
- `contact.php` : formulaire de contact.

Les traitements PHP et les contrôles d'accès sont dans `php/`. Les ressources visuelles se trouvent dans `assets/`.

## Exécuter en local

Lire d'abord le `README.md` **à la racine du dépôt** pour installer XAMPP, importer les scripts SQL dans une base neuve, configurer CouchDB et Mailpit ainsi que les fichiers locaux privés. Ouvrir ensuite :

`http://localhost/vite-et-gourmand/site-web/index.php`

Ne pas ouvrir directement les anciens fichiers `.html` pour tester les données réelles : les parcours utilisant MariaDB passent par les pages PHP.

## Documentation

- `DEPLOIEMENT.md` : configuration et déploiement.
- `VERIFICATIONS_FINALES.md` : vérifications restantes.
- `../base-de-donnees/01_creation.sql` et `../base-de-donnees/02_donnees.sql` : création de la base et données de départ.

## Précautions

Ne jamais publier les fichiers `config.local.php`, `nosql.local.php`, `contact.local.php` ou `parametres-site.local.php`. Les e-mails reçus dans Mailpit sont des tests locaux, pas une preuve d'envoi réel sur un hébergement public.

Les principaux parcours ont été testés en local, puis sur alwaysdata. Les contrôles RGAA complets et les cas particuliers restent à approfondir.
