# Vite & Gourmand

Projet réalisé pour l'ECF du titre professionnel Développeur Web et Web Mobile (Studi).

## Présentation

Vite & Gourmand est une application web conçue pour un traiteur fictif à Bordeaux. Elle présente les menus et permet de passer une commande, de la suivre et de donner un avis. Elle prévoit trois types de comptes : client, employé et administrateur.

**Site en ligne :** https://vitegourmandecf2027.alwaysdata.net/

## Fonctionnalités

- **Visiteur** : consulter les menus, utiliser cinq filtres dynamiques, lire le détail des menus et contacter l'entreprise.
- **Client** : s'inscrire, se connecter, réinitialiser son mot de passe, commander, consulter et modifier ses commandes lorsque leur statut le permet, les annuler et déposer un avis après leur achèvement.
- **Employé** : consulter et filtrer les commandes, mettre à jour leur statut, gérer menus, plats et horaires, et modérer les avis.
- **Administrateur** : gérer les comptes employés, accéder aux fonctions de gestion et consulter des statistiques par menu.

Les principaux parcours ont été testés en local avec XAMPP, Brave et Mailpit, puis sur le site hébergé. En ligne, les essais ont notamment confirmé l'inscription et la connexion, les e-mails de commande et de réinitialisation, les changements de statut d'une commande jusqu'à « Terminée », le dépôt et la validation d'un avis ainsi que les statistiques administrateur. Lors du test, celles-ci affichaient une commande et 250 € de chiffre d'affaires après livraison.

## Technologies

- **Front-end** : HTML5, CSS3 et JavaScript.
- **Back-end** : PHP et PDO.
- **Base SQL** : MariaDB, pour les comptes, menus, commandes, historiques et avis.
- **Base NoSQL** : Apache CouchDB en local et IBM Cloudant (compatible avec l'API CouchDB) en ligne, pour les statistiques administrateur.
- **Développement** : Visual Studio Code, XAMPP et Mailpit pour les courriels de test.
- **Hébergement** : alwaysdata pour PHP, MariaDB et les courriels du site ; IBM Cloudant Lite pour les données statistiques.
- **Versionnement** : Git et GitHub.

## Installer le projet en local

1. Installer **XAMPP** et placer le dépôt dans `C:\xampp\htdocs\vite-et-gourmand`.
2. Démarrer **Apache** et **MySQL** (MariaDB) depuis XAMPP.
3. Sur une base de test vide, exécuter `base-de-donnees/01_creation.sql`, puis `base-de-donnees/02_donnees.sql`. **Ne pas réimporter ces scripts dans une base contenant déjà des données.**
4. Dans `site-web/php/`, copier `config.local.exemple.php` vers `config.local.php` et renseigner les paramètres SQL locaux.
5. Installer et démarrer **Apache CouchDB**. Copier `nosql.local.exemple.php` vers `nosql.local.php` et renseigner l'adresse et les identifiants locaux ; les statistiques utilisent la base `vite_gourmand_stats`.
6. Démarrer **Mailpit** si l'on souhaite contrôler les courriels de test (SMTP `1025`, interface `http://localhost:8025`) et configurer l'envoi de PHP vers ce service.
7. Si nécessaire, créer `parametres-site.local.php` et `contact.local.php` à partir de leurs fichiers d'exemple, avec des valeurs adaptées à l'environnement local.
8. Ouvrir `http://localhost/vite-et-gourmand/site-web/index.php`.

Un compte client peut être créé depuis le formulaire d'inscription. Le compte administrateur initial ne peut pas être créé par l'inscription publique.

## Organisation

- `site-web/` : pages du site, styles, scripts et images.
- `site-web/php/` : traitements serveur, contrôles d'accès et accès aux bases.
- `base-de-donnees/01_creation.sql` et `base-de-donnees/02_donnees.sql` : structure SQL et données initiales.
- `site-web/DEPLOIEMENT.md` : procédure et environnement de déploiement.
- `site-web/VERIFICATIONS_FINALES.md` : points de contrôle.
- `documentation/` : manuel public, documentation technique, gestion de projet, charte graphique et maquettes.

## Sécurité

Les mots de passe des utilisateurs sont hachés, les accès sont contrôlés selon les rôles et les requêtes SQL utilisent PDO et des requêtes préparées. Les actions sensibles emploient des jetons CSRF. Les paramètres de connexion privés (`*.local.php`) ne doivent pas être publiés sur GitHub ni placés dans les archives de code accessibles publiquement. Le site est accessible en HTTPS ; cela ne remplace pas un audit complet de sécurité ou d'accessibilité.

## Déploiement et état des vérifications

Le site est accessible publiquement sur **alwaysdata**, avec sa base MariaDB en ligne. Les statistiques utilisent **IBM Cloudant**, via une connexion HTTPS configurée côté serveur. Un contrôle en production a confirmé le fonctionnement des graphiques et du chiffre d'affaires. Les résultats des essais connus sont consignés dans les documents de gestion de projet remis avec l'ECF. Les calculs particuliers, l'accessibilité et les autres points réglementaires restent à examiner séparément lorsqu'ils n'ont pas fait l'objet d'une vérification complète.

## Livrables et suivi

- **Dépôt GitHub** : https://github.com/RamziMammad/vite-et-gourmand
- **Tableau GitHub Projects** : https://github.com/users/RamziMammad/projects/1
- **Site déployé** : https://vitegourmandecf2027.alwaysdata.net/

Les PDF publics et les maquettes sont disponibles dans le dépôt GitHub. Les identifiants des comptes de démonstration figurent dans la copie Word remise à Studi.

## Git

Le travail est intégré sur `develop`, puis réuni dans `main` après vérification. Les paramètres propres aux environnements sont conservés hors du dépôt public.
