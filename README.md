# Vite & Gourmand

Projet réalisé pour l'ECF du titre professionnel Développeur Web et Web Mobile (Studi).

## Présentation

Vite & Gourmand est une application web de traiteur située dans le contexte fictif de Bordeaux. Elle présente les menus et permet de passer une commande, de la suivre et de donner un avis. Elle prévoit trois types de comptes : client, employé et administrateur.

## Fonctionnalités

- **Visiteur** : consulter les menus, utiliser cinq filtres dynamiques, lire le détail des menus et contacter l'entreprise.
- **Client** : s'inscrire, se connecter, réinitialiser son mot de passe, commander, consulter et modifier ses commandes autorisées, les annuler et déposer un avis après leur achèvement.
- **Employé** : consulter et filtrer les commandes, mettre à jour leur statut, gérer menus, plats et horaires, et modérer les avis.
- **Administrateur** : gérer les comptes employés, accéder aux fonctions de gestion et consulter des statistiques par menu.

Les parcours principaux ont été testés localement avec XAMPP, Brave et Mailpit. Ces tests ne remplacent pas un contrôle complet en hébergement public.

## Technologies

HTML5, CSS3, JavaScript, PHP, PDO, MariaDB (SQL) et Apache CouchDB (NoSQL). Le projet est développé en local avec XAMPP ; Mailpit sert à recevoir les e-mails de test.

## Installer le projet en local

1. Installer **XAMPP** et placer ce dépôt dans `C:\xampp\htdocs\vite-et-gourmand`.
2. Dans XAMPP, démarrer **Apache** et **MySQL** (MariaDB).
3. Ouvrir `http://localhost/phpmyadmin`. Sur une installation neuve uniquement, exécuter `base-de-donnees/01_creation.sql`, puis `base-de-donnees/02_donnees.sql`. **Ne pas réimporter ces scripts sur une base déjà utilisée**, pour ne pas écraser ou dupliquer les données.
4. Dans `site-web/php/`, copier `config.local.exemple.php` vers `config.local.php`, puis renseigner les paramètres de la base SQL locale.
5. Installer et démarrer **Apache CouchDB**. Copier `nosql.local.exemple.php` en `nosql.local.php` et renseigner la connexion CouchDB. Les statistiques de l'administrateur utilisent cette base NoSQL.
6. Pour les e-mails de test, démarrer **Mailpit** (SMTP 1025, interface `http://localhost:8025`) et configurer l'envoi des e-mails de PHP vers ce service selon l'environnement XAMPP.
7. Si nécessaire, créer `parametres-site.local.php` et `contact.local.php` à partir de leurs fichiers `.exemple.php`, dans `site-web/php/`, en conservant des coordonnées de test adaptées à l'environnement local.
8. Ouvrir `http://localhost/vite-et-gourmand/site-web/index.php`.

Un compte client peut être créé depuis l'interface. Le compte administrateur initial doit être mis en place de façon sécurisée dans la base : l'application **ne permet pas** l'inscription publique d'un administrateur. Aucun identifiant administrateur réel ou mot de passe n'est fourni dans ce dépôt.

## Organisation

- `site-web/` : pages publiques et espaces privés PHP, CSS, JavaScript et images.
- `site-web/php/` : connexion aux bases, contrôles d'accès et traitement des actions côté serveur.
- `base-de-donnees/01_creation.sql` : structure relationnelle.
- `base-de-donnees/02_donnees.sql` : données de départ.
- `site-web/DEPLOIEMENT.md` : prérequis et procédure de déploiement.
- `site-web/VERIFICATIONS_FINALES.md` : points de contrôle.

## Sécurité

La connexion utilise des sessions PHP et des mots de passe hachés. L'accès aux espaces dépend du rôle. Les données SQL sont traitées avec PDO et des requêtes préparées ; les formulaires sensibles utilisent des contrôles côté serveur. Les fichiers `*.local.php` contiennent des paramètres **privés** : ils ne doivent être ni committés ni publiés.

## État de la livraison

Le projet fonctionne en local sur la configuration de développement testée. **La mise en ligne publique n'est pas encore attestée**, et les mentions légales, les CGV, l'accessibilité RGAA et les paramètres d'envoi de courriels devront être validés dans l'environnement de déploiement. Aucune donnée de contact ou d'entreprise non confirmée ne doit être inventée.

Les livrables complémentaires demandés par l'ECF (manuel utilisateur PDF, charte graphique PDF et maquettes, documentation technique, gestion du projet, diagrammes, lien du projet déployé et lien de l'outil de suivi) sont à joindre au dossier de remise.

## Git

Les développements sont intégrés sur `develop`, puis réunis dans `main` après vérification. Les fichiers de configuration locale sont exclus du suivi Git.
