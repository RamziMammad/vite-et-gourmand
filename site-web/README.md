# Vite & Gourmand

## Présentation du projet

Vite & Gourmand est un projet de site web réalisé dans le cadre de mon ECF Développeur Web et Web Mobile chez Studi.

Le site présente une entreprise de traiteur située à Bordeaux, qui propose différents menus pour des événements ou des repas particuliers.

L'objectif est de permettre aux visiteurs de consulter les menus, de voir leur composition et de passer une commande en ligne.

Le développement du projet se fait en deux parties : le front-end, puis le back-end.

## État du projet

La partie front-end est en place. Le site comprend 14 pages HTML, des fichiers CSS pour la mise en page et 15 fichiers JavaScript pour les fonctionnalités dynamiques.

Les principales fonctionnalités sont :

- consulter les menus proposés par l'entreprise ;
- filtrer les menus selon le prix, le thème, le régime alimentaire et le nombre de personnes ;
- afficher le détail d'un menu avec ses plats, ses allergènes, ses photos et ses conditions ;
- calculer le prix d'une commande selon le nombre de personnes, la remise et les frais de livraison ;
- afficher les espaces utilisateur, employé et administrateur ;
- gérer les menus et les plats depuis les interfaces prévues ;
- afficher le suivi des commandes et les avis clients.

Pour le moment, certaines fonctionnalités utilisent des données d'exemple enregistrées avec `localStorage`. Les comptes et les commandes ne sont pas encore enregistrés dans une véritable base de données.

Cette partie sera développée avec le back-end. Pour les essais actuels, seules des données fictives doivent être utilisées.

## Technologies utilisées

Pour la partie front-end, j'ai utilisé :

- **HTML5** pour créer les pages ;
- **CSS3** pour la mise en forme et l'adaptation aux écrans ;
- **JavaScript** pour les filtres, les formulaires et les interactions ;
- **XAMPP et Apache** pour lancer le site en local.

Pour le back-end, je prévois d'utiliser PHP avec PDO, MySQL pour la base de données relationnelle et une base NoSQL pour les statistiques demandées dans l'énoncé.

## Lancer le site en local

Pour ouvrir le projet sur un ordinateur :

1. Installer XAMPP.
2. Placer le dossier `vite-et-gourmand` dans `C:\xampp\htdocs\`.
3. Ouvrir XAMPP et démarrer Apache.
4. Ouvrir un navigateur comme Brave ou Chrome.
5. Saisir l'adresse suivante :

`http://localhost/vite-et-gourmand/site-web/index.html`

Si le dossier du projet porte un autre nom, il faut adapter l'adresse.

Pour le moment, aucune base de données n'est nécessaire pour consulter la partie front-end.

## Organisation des fichiers

Le projet contient un dossier principal `site-web` avec les pages HTML.

Les autres fichiers sont rangés dans le dossier `assets` :

- `assets/css` : les fichiers CSS des pages ;
- `assets/images` : les photos et les icônes ;
- `assets/js` : les fichiers JavaScript.

Les fichiers JavaScript sont séparés selon les fonctionnalités : menus, commandes, espace utilisateur, espace employé et administration.

Cette organisation me permet de retrouver plus facilement le code lorsque je dois effectuer une modification.

## Vérifications du front-end

Plusieurs vérifications ont été effectuées sur les fichiers HTML, CSS et JavaScript.

Les principaux éléments à tester dans le navigateur sont :

- les filtres et l'affichage des menus ;
- les détails des menus ;
- le calcul des prix et des frais de livraison ;
- la redirection vers la connexion avant une commande ;
- les différentes interfaces de gestion ;
- l'affichage sur ordinateur et sur mobile.

Des vérifications complémentaires restent nécessaires pour l'accessibilité et le fonctionnement de l'application complète.

## Suite du projet

La prochaine étape est le développement du back-end.

Il faudra notamment mettre en place :

- l'inscription et la connexion sécurisées ;
- la gestion des utilisateurs et de leurs rôles ;
- l'enregistrement des menus et des commandes ;
- la connexion aux bases de données SQL et NoSQL ;
- l'envoi des e-mails ;
- les tests et le déploiement de l'application.

Le projet est suivi avec Git et GitHub afin de conserver les différentes étapes du développement.

J'utilise des branches pour les fonctionnalités, puis je les intègre dans `develop` après vérification. La branche principale sera mise à jour lorsque l'application sera prête.