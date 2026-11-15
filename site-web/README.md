# Vite & Gourmand — site web

Ce dossier contient la partie front-end du projet Vite & Gourmand réalisé pour l'ECF de Studi.

## Ce qui est disponible

Le site comprend 14 pages HTML, les styles CSS, les images et 15 fichiers JavaScript. Il est possible de consulter les menus, de les filtrer, de voir les plats et les allergènes, puis de préparer une commande avec le calcul du prix. Les espaces utilisateur, employé et administrateur sont aussi présents en version de démonstration.

Les scripts sont rangés dans `assets/js`. Ils sont regroupés par fonctionnalités pour éviter d'avoir trop de petits fichiers. Les feuilles de style se trouvent dans `assets/css` et les images dans `assets/images`.

## Pour lancer le site

1. Placer le dossier `site-web` dans `C:\xampp\htdocs\vite-et-gourmand\`.
2. Démarrer Apache dans XAMPP.
3. Ouvrir `http://localhost/vite-et-gourmand/site-web/index.html` dans Brave.

Si le nom du dossier est différent, adapter l'adresse.

## Pour les tests

Utiliser uniquement des informations fictives. Le site conserve certaines données de démonstration dans `localStorage` : il n'y a pas encore de vraie connexion à une base de données ni d'envoi d'e-mails. Le bouton de démonstration sur la page de connexion permet de tester le parcours de commande.

Avant de considérer le front-end comme définitivement validé, il faut vérifier les parcours dans le navigateur et tester le rendu mobile ainsi que l'accessibilité.

## Prochaine étape

Mettre en place le back-end en PHP, les bases de données et la sécurité, puis préparer le déploiement et les documents demandés dans l'ECF.

Le README placé à la racine du dépôt présente le projet et son organisation de manière plus complète.
