# Vite & Gourmand

## Présentation du projet

Vite & Gourmand est un site de traiteur réalisé dans le cadre de mon ECF Développeur Web et Web Mobile chez Studi. L'entreprise est située à Bordeaux. L'objectif est de présenter ses menus et de permettre aux clients de commander en ligne.

## État du projet

La partie front-end est en place. Elle comprend plusieurs pages HTML et PHP, 13 feuilles CSS, 15 fichiers JavaScript et les images du site.

Le site permet déjà de consulter les menus, de les filtrer, de voir les plats et les allergènes, et de calculer le prix d'une commande. Les espaces client, employé et administrateur sont également présentés. Certaines interactions sont encore des démonstrations réalisées avec JavaScript et `localStorage`.

Côté back-end, la base MariaDB et ses tables ont été créées. La connexion PHP avec PDO fonctionne et l'inscription des clients enregistre désormais des comptes dans la table `utilisateurs`, avec un mot de passe haché.

La connexion et la déconnexion réelles sont maintenant disponibles avec contrôle des rôles. Les commandes, les espaces de gestion reliés à SQL et l'envoi des e-mails restent à réaliser. Les espaces existants affichent encore des données fictives et le signalent clairement. Les tests doivent utiliser uniquement des informations fictives pour le moment.

## Technologies utilisées

- HTML5 pour les pages ;
- CSS3 pour la présentation et l'affichage sur mobile ;
- JavaScript pour les filtres et les interactions ;
- PHP avec PDO pour accéder aux données ;
- MariaDB pour la base de données relationnelle ;
- XAMPP et Apache pour faire fonctionner le projet en local.

Une base NoSQL sera ajoutée pour les statistiques demandées dans l'énoncé.

## Lancer le site en local

1. Installer XAMPP et placer le dossier `vite-et-gourmand` dans `C:\xampp\htdocs`.
2. Démarrer Apache dans XAMPP.
3. Ouvrir `http://localhost/vite-et-gourmand/site-web/index.html` dans le navigateur.

Pour tester l'inscription et la connexion :

4. Démarrer MySQL dans XAMPP.
5. Depuis phpMyAdmin, importer `base-de-donnees/01_creation.sql`, puis `base-de-donnees/02_donnees.sql` dans la base `vite_et_gourmand`.
6. Dans `site-web/php`, copier `config.local.exemple.php` en `config.local.php`, puis renseigner les identifiants MariaDB de son ordinateur.
7. Ouvrir `http://localhost/vite-et-gourmand/site-web/inscription.php` pour créer un compte.
8. Se connecter ensuite sur `http://localhost/vite-et-gourmand/site-web/connexion.php`.
9. Vérifier les accès aux espaces selon les rôles puis la déconnexion.

Le fichier `config.local.php` contient des informations privées : il ne faut pas le publier sur GitHub ni l'inclure dans les ZIP transmis.

## Organisation des fichiers

Le dossier `site-web` contient les pages HTML et PHP, dont `inscription.php`, `connexion.php` et `deconnexion.php`. Les ressources sont rangées dans `assets/css`, `assets/js` et `assets/images`. Le dossier `site-web/php` contient la connexion PDO, la vérification de session, un exemple de configuration et une page de test de connexion à la base.

Les scripts JavaScript sont regroupés par fonctionnalité pour faciliter la lecture du code.

## Vérifications réalisées

Les fichiers HTML, CSS, JavaScript et PHP ont été contrôlés. L'inscription a été testée en local et un compte a été retrouvé dans phpMyAdmin. Il reste des tests à réaliser sur le site complet, notamment sur mobile, sur les droits d'accès et sur l'accessibilité.

## Suite du projet

Il reste notamment à développer le mot de passe oublié, les commandes réelles, la gestion des menus et des avis, les statistiques NoSQL, les e-mails et le déploiement.

Le projet est suivi avec Git et GitHub. Chaque fonctionnalité est développée dans une branche, puis intégrée à `develop` après vérification. La branche principale sera mise à jour lorsque l'ensemble de l'application sera prêt.
