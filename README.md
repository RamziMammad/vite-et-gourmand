# Vite & Gourmand

## Présentation du projet

Vite & Gourmand est un site de traiteur imaginé pour l'ECF Développeur Web et Web Mobile de Studi. L'entreprise se trouve à Bordeaux. Le but du site est de présenter les menus et de permettre aux clients de préparer une commande en ligne.

Le projet avance en deux parties : d'abord les pages et les interactions côté navigateur, puis le développement côté serveur et les bases de données.

## État du projet

La partie front-end est en place, avec 14 pages HTML, les feuilles CSS, les images et 15 fichiers JavaScript regroupés par fonctionnalité.

On peut notamment :

- consulter les menus et utiliser cinq filtres sans recharger la page ;
- afficher la composition d'un menu, ses allergènes, ses conditions et ses photos ;
- préparer une commande et voir le calcul du prix, de la remise et des frais de livraison ;
- tester les espaces utilisateur, employé et administrateur avec des données de démonstration ;
- simuler le suivi des commandes, les avis clients et la gestion du catalogue.

Pour le moment, les comptes et les commandes ne sont **pas enregistrés sur un vrai serveur**. Certaines données sont gardées dans le navigateur avec `localStorage` pour tester les écrans. Le mode démonstration est indiqué sur la page de connexion. Il ne faut pas utiliser de vraies données personnelles dans ce mode.

## Technologies

- HTML5 pour les pages ;
- CSS3 pour la mise en forme et l'affichage sur mobile ;
- JavaScript pour les filtres, les formulaires et les interactions ;
- XAMPP et Apache pour lancer le site en local.

Pour le back-end, la solution envisagée est PHP avec PDO, MySQL pour les données relationnelles et une base NoSQL pour la partie demandée dans l'énoncé.

## Lancer le site en local

1. Installer XAMPP si ce n'est pas déjà fait.
2. Placer le dossier du projet `vite-et-gourmand` dans `C:\xampp\htdocs\`.
3. Vérifier que les pages se trouvent bien dans `C:\xampp\htdocs\vite-et-gourmand\site-web\`.
4. Ouvrir XAMPP et démarrer **Apache**.
5. Dans le navigateur, ouvrir :

   `http://localhost/vite-et-gourmand/site-web/index.html`

Il n'y a pas encore de base de données à importer pour utiliser cette version du front-end.

Si le dossier a un autre nom, il faut adapter l'adresse dans le navigateur.

## Organisation des fichiers

Le dossier `site-web` contient les pages HTML et un dossier `assets` :

- `assets/css` : les styles des pages ;
- `assets/images` : les images et les icônes ;
- `assets/js` : les scripts JavaScript.

Les scripts sont séparés par sujet : menus, commande, espace client, espace employé, administration et données de démonstration. Cette organisation permet de retrouver plus facilement une partie du code.

## Vérifications du front-end

Les principaux points à contrôler dans Brave sont les filtres des menus, le détail des deux menus, les calculs de commande, le parcours en démonstration, le suivi des statuts et l'affichage sur mobile.

Le site doit encore être vérifié en conditions réelles dans le navigateur, notamment pour l'accessibilité et les différents formats d'écran. La présence des pages ne signifie pas à elle seule que toute l'application est terminée.

## Suite du projet

La prochaine étape est de relier les pages à un vrai back-end : inscription et connexion sécurisées, rôles utilisateur/employé/administrateur, enregistrement des menus et commandes, envoi des e-mails, bases de données SQL et NoSQL, puis tests et déploiement.

Le dépôt Git sert à conserver l'historique du travail. Les changements sont réalisés sur des branches de fonctionnalités, puis intégrés dans `develop` après vérification et dans la branche principale lorsque la version est prête.
