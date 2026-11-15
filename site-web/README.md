# Vite & Gourmand — Front-end

Ce dossier contient les pages HTML, les feuilles CSS, les images et les 15 fichiers JavaScript de Vite & Gourmand.

## Pages et fonctionnalités

L'accueil présente l'entreprise et ses menus. On peut filtrer les menus par prix, thème, régime et nombre de personnes, puis consulter les plats, les allergènes, les photos et les conditions.

Le formulaire de commande calcule le montant, la remise de 10 % et les frais de livraison. Le bouton de commande d'une fiche menu mène à la connexion, comme demandé dans l'énoncé.

Les espaces utilisateur, employé et administrateur sont déjà maquettés et possèdent des interactions côté navigateur. Le back-end PHP n'est pas encore branché : aucun véritable compte ou commande n'est créé depuis ces écrans. Certaines données locales servent simplement à prévisualiser les fonctionnalités.

## Lancer le site

1. Placer le dossier `site-web` dans `C:\xampp\htdocs\vite-et-gourmand\`.
2. Démarrer Apache dans XAMPP.
3. Ouvrir `http://localhost/vite-et-gourmand/site-web/index.html` dans le navigateur.

## Organisation

- `assets/css` : mise en page et responsive ;
- `assets/images` : photos et icônes ;
- `assets/js` : filtres, affichage, commandes, données locales et espaces de gestion.

Pour le moment, il faut utiliser uniquement des données fictives lors des essais. Il reste à vérifier les parcours sur ordinateur et mobile, à contrôler l'accessibilité, puis à relier les formulaires et les espaces au serveur.
