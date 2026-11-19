# Guide de déploiement de Vite & Gourmand

Ce guide décrit l'installation locale et la mise en ligne réalisée pour le projet ECF. Le site est accessible en HTTPS à l'adresse : https://vitegourmandecf2027.alwaysdata.net/.

## Environnement local

1. Placer le projet `vite-et-gourmand` dans `C:\xampp\htdocs\`, avec les dossiers `base-de-donnees`, `site-web` et `documentation`. Le dossier `.git` est utile au suivi du projet, pas au fonctionnement du site.
2. Démarrer Apache et MySQL dans XAMPP. Créer une base de test `vite_et_gourmand`, puis exécuter `base-de-donnees/01_creation.sql` et `base-de-donnees/02_donnees.sql` dans cet ordre, **uniquement sur une base vide**. Sauvegarder toute base existante avant une importation.
3. Dans `site-web/php/`, copier `config.local.exemple.php` vers `config.local.php`, puis renseigner les paramètres MariaDB locaux.
4. Démarrer Apache CouchDB sur `127.0.0.1:5984`, copier `nosql.local.exemple.php` vers `nosql.local.php` et renseigner ses identifiants. Le module statistique utilise la base `vite_gourmand_stats`.
5. Démarrer Mailpit (SMTP `1025`, interface `http://localhost:8025`) et configurer l'envoi des courriels de test depuis PHP si nécessaire.
6. Ouvrir `http://localhost/vite-et-gourmand/site-web/index.php` pour effectuer les tests locaux.

Les scripts SQL servent à initialiser une installation neuve, et non à réimporter des données sur le site de production. Les comptes de démonstration sont documentés séparément dans le manuel remis à Studi, sans publier leurs mots de passe.

## Hébergement public : alwaysdata

Le projet a été déployé sur **alwaysdata**. L'environnement utilisé comprend :

- **Serveur web et PHP** : site accessible à `https://vitegourmandecf2027.alwaysdata.net/` ; racine configurée vers `/home/vitegourmandecf2027/www/site-web/`.
- **Transfert** : fichiers du dossier local `site-web/` envoyés avec FileZilla vers `/home/vitegourmandecf2027/www/site-web/`.
- **MariaDB** : base `vitegourmandecf2027_vitegourmand`, sur l'hôte `mysql-vitegourmandecf2027.alwaysdata.net`. La présence des 15 tables a été contrôlée dans phpMyAdmin.
- **Configuration SQL** : informations de connexion dans `site-web/php/config.local.php` sur le serveur, non destinées au dépôt public.
- **Courriels** : paramètres de l'environnement de production adaptés pour envoyer les confirmations de commande et les liens de réinitialisation de mot de passe ; ces deux parcours ont été testés en ligne.
- **HTTPS** : site consultable avec une connexion HTTPS.

## Statistiques NoSQL : IBM Cloudant

L'hébergement alwaysdata utilisé ne fournit pas de serveur CouchDB permanent. Pour les statistiques en ligne, le projet utilise **IBM Cloudant Lite**, un service NoSQL compatible avec les requêtes CouchDB employées par le code PHP.

- **Instance Cloudant** : `vite-gourmand-stats`, dans la région Francfort (`eu-de`).
- **Base documentaire** : `vite_gourmand_stats`.
- **Configuration privée** : fichier `site-web/php/nosql.local.php` sur alwaysdata ; il contient l'adresse HTTPS Cloudant et les identifiants de connexion. Il ne doit pas être publié sur GitHub.
- **Module PHP** : `site-web/php/statistiques-nosql.php` regroupe les commandes issues de MariaDB dans un document JSON utilisé par l'espace administrateur.

Un essai en production a confirmé l'affichage d'une commande dans le graphique « Nombre de commandes par menu ». Une fois la commande livrée, le chiffre d'affaires affiché a été de **250 €**. Les statistiques restent dépendantes de la disponibilité et des limites du service Cloudant.

## Tests réalisés sur le site public

Les essais réalisés ont notamment porté sur l'accueil et les menus, l'inscription et la connexion, la réinitialisation du mot de passe, la création d'une commande et son courriel de confirmation, les changements de statut jusqu'à « Terminée », le dépôt et la validation d'un avis visible sur l'accueil, les statistiques administrateur et un contrôle de refus d'accès à l'administration pour un compte client.

Ces essais ne signifient pas que tous les cas particuliers ont été audités : les remises, les frais de livraison, l'accessibilité, les mentions réglementaires et les autres scénarios de sécurité doivent être contrôlés selon la grille de vérification et l'énoncé officiel.

## Configuration et sécurité

1. Garder les fichiers `config.local.php`, `nosql.local.php`, `parametres-site.local.php` et `contact.local.php` **privés**, avec les valeurs adaptées à chaque environnement.
2. Vérifier que les fichiers sensibles ne sont pas accessibles depuis le navigateur et qu'ils ne sont pas inclus dans le dépôt public ou dans une archive de remise publique.
3. Conserver une sauvegarde de la base SQL et des fichiers utiles avant une modification ou une réinstallation.
4. Utiliser les accès de démonstration uniquement pour la remise privée aux évaluateurs. Les secrets qui ont déjà été rendus publics doivent être remplacés.
5. Vérifier les droits d'accès, les CGV, les mentions légales et l'information sur les données personnelles, sans considérer HTTPS comme une validation complète de la sécurité.

## Suivi du projet et remise Studi

- **Dépôt GitHub** : https://github.com/RamziMammad/vite-et-gourmand
- **Tableau GitHub Projects** : https://github.com/users/RamziMammad/projects/1
- **Site en ligne** : https://vitegourmandecf2027.alwaysdata.net/

Les trois PDF publics sont dans le dossier documentation du dépôt GitHub. Les identifiants de démonstration sont transmis dans la copie Word déposée sur Studi.

Une archive Git ne comprend que les fichiers suivis : les configurations et documents exclus du suivi doivent être ajoutés uniquement à la remise privée lorsqu'ils sont réellement nécessaires et sans exposer les accès de production.
