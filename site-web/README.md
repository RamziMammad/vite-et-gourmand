# Vite & Gourmand

## Présentation du projet

Vite & Gourmand est un site de traiteur réalisé dans le cadre de mon ECF Développeur Web et Web Mobile chez Studi. L'entreprise est située à Bordeaux. L'objectif est de présenter ses menus et de permettre aux clients de commander en ligne.

## État du projet — 17 novembre 2026

Le site comporte les pages publiques, les menus, les filtres JavaScript et le responsive. La base MariaDB, l'inscription, la connexion, la déconnexion et la réinitialisation du mot de passe via Mailpit ont été testées en local.

**Étape 6 :** `commande.php` enregistre réellement les commandes dans MariaDB, met à jour le stock et l'historique, et tente l'envoi d'un e-mail de confirmation. Après enregistrement, `confirmation-commande.php` présente le récapitulatif. Le parcours a été testé sur XAMPP avec une première commande. Les cas d'erreur et les règles métier complètes doivent encore être vérifiés.

**Étape 7 :** `espace-utilisateur.php` lit désormais les commandes du client connecté dans MariaDB. Les modifications et annulations d’une commande en attente, la mise à jour des informations personnelles, les avis sur les commandes terminées et l’historique des statuts sont traités par `php/espace-client.php` avec PDO. Les modifications sont recalculées côté PHP et les annulations rendent l’unité de stock. Les contrôles de syntaxe ont été effectués ; les scénarios complets restent à valider dans XAMPP avant GitHub.

**Étape 10 :** `espace-administrateur.php` et `php/espace-administrateur.php` gèrent les comptes employés dans MariaDB. L'administrateur peut créer un compte avec une adresse et un mot de passe sécurisé puis le désactiver. Un e-mail de notification est tenté via la configuration PHP, sans mot de passe. Les graphiques et le chiffre d'affaires proviennent d'Apache CouchDB (NoSQL) grâce à `php/statistiques-nosql.php`. La connexion et le graphique ont été observés sur XAMPP ; le chiffre d’affaires filtré nécessite encore des tests réels.

**Étape 8 :** `espace-employe.php` et `php/espace-employe.php` lisent les commandes, historiques, menus, plats, horaires et avis dans MariaDB. Les changements de statut, annulations après contact, modifications après contact, modération des avis et modification des données des menus/plats/horaires sont traités côté serveur. Les anciennes commandes fictives de cet espace ont été retirées. Les contrôles automatiques ont été effectués ; les scénarios réels sous XAMPP restent à tester.

Les mentions légales et les CGV nécessitent également la validation de données légales et contractuelles réelles avant tout déploiement public. Ne pas inventer d'identité d'entreprise, de SIRET, de coordonnées ou de conditions commerciales manquantes.

## Étapes 11 et 12 — contact, e-mails et préparation du déploiement

Le formulaire `contact.php` est traité côté PHP : contrôles des champs, jeton CSRF, envoi par `mail()` et redirection après succès. La réception réelle dans Mailpit reste à confirmer sur le poste XAMPP.

L'inscription envoie maintenant un e-mail de bienvenue après l'enregistrement SQL (obligation de l'énoncé ECF). Si l'envoi échoue, le compte reste créé et le message affiché est explicite.

Le fichier `php/parametres-site.php` configure l'URL du site et l'adresse d'expédition des e-mails. Pour un hébergement public, copier `php/parametres-site.local.exemple.php` en `php/parametres-site.local.php` et remplacer les valeurs exemples par des données réelles. La page contact utilise également `php/contact.local.php`. Ces fichiers privés sont exclus du ZIP et de Git. Une règle Apache `.htaccess` bloque leur consultation directe.

Consulter `DEPLOIEMENT.md` pour la mise en ligne et `VERIFICATIONS_FINALES.md` pour les tests restant à réaliser. La configuration d'un hébergeur, la délivrabilité réelle des e-mails et les obligations RGAA / légales restent à faire avant le rendu final.

## Technologies utilisées

- HTML5 pour les pages ;
- CSS3 pour la présentation et l'affichage sur mobile ;
- JavaScript pour les filtres et les interactions ;
- PHP avec PDO pour accéder aux données ;
- MariaDB pour la base de données relationnelle ;
- Apache CouchDB pour la base de statistiques non relationnelle ;
- XAMPP et Apache pour faire fonctionner le projet en local.

Apache CouchDB (NoSQL) est utilisé pour stocker un instantané des commandes destiné aux statistiques de l'administrateur.

## Lancer le site en local

1. Installer XAMPP et placer le dossier `vite-et-gourmand` dans `C:\xampp\htdocs`.
2. Démarrer Apache dans XAMPP.
3. Ouvrir `http://localhost/vite-et-gourmand/site-web/index.php` dans le navigateur.

Pour tester l'inscription et la connexion :

4. Démarrer MySQL dans XAMPP.
5. Depuis phpMyAdmin, importer `base-de-donnees/01_creation.sql`, puis `base-de-donnees/02_donnees.sql` dans la base `vite_et_gourmand`.
6. Dans `site-web/php`, copier `config.local.exemple.php` en `config.local.php`, puis renseigner les identifiants MariaDB de son ordinateur.
7. Ouvrir `http://localhost/vite-et-gourmand/site-web/inscription.php` pour créer un compte.
8. Se connecter ensuite sur `http://localhost/vite-et-gourmand/site-web/connexion.php`.
9. Vérifier les accès aux espaces selon les rôles puis la déconnexion.

Le fichier `config.local.php` contient des informations privées : il ne faut pas le publier sur GitHub ni l'inclure dans les ZIP transmis.

## Organisation des fichiers

Le dossier `site-web` contient les pages HTML et PHP, dont `inscription.php`, `connexion.php`, `deconnexion.php`, `commande.php` et `confirmation-commande.php`. Les ressources sont dans `assets/css`, `assets/js` et `assets/images`. Le dossier `site-web/php` contient la connexion PDO, le contrôle des sessions et un exemple de configuration. Les fichiers locaux de test et `config.local.php` doivent être exclus des archives diffusées.

Les scripts JavaScript sont regroupés par fonctionnalité pour faciliter la lecture du code.

## Vérifications réalisées

Les fichiers HTML, CSS, JavaScript et PHP ont été contrôlés. L'inscription a été testée en local et un compte a été retrouvé dans phpMyAdmin. Il reste des tests à réaliser sur le site complet, notamment sur mobile, sur les droits d'accès et sur l'accessibilité.

## Suite du projet

Vérifier l’envoi du formulaire de contact, le catalogue, les espaces des trois rôles et les messages, puis tester les fonctionnalités, l’accessibilité, la sécurité et le déploiement. Compléter les mentions légales et les CGV après validation des renseignements nécessaires.

Chaque étape fait l’objet d’une vérification automatique. Les tests fonctionnels complets dans Brave et XAMPP seront réalisés avant la validation finale et les opérations GitHub après accord du propriétaire du projet.

## Réinitialisation du mot de passe (étape 5)

Le formulaire `mot-de-passe-oublie.php` prépare un lien envoyé par mail. Le lien est valable une heure et une seule utilisation est autorisée.
La page `reinitialiser-mot-de-passe.php` enregistre le nouveau mot de passe de manière protégée.
En local, il faut configurer un serveur de mails de test (comme Mailpit). L’envoi des e-mails a été testé avec Mailpit en local ; l’envoi depuis un hébergement public reste à configurer et à tester.


## Commandes PHP — étape 6 (parcours local testé, vérifications à poursuivre)

La page `commande.php` nécessite une connexion avec le rôle `utilisateur`. Le formulaire reprend les informations du compte depuis MariaDB, récupère les menus actifs dans SQL et recalcule les montants côté serveur avant toute insertion. Les commandes sont enregistrées dans `commandes`, et le premier statut dans `historique_commandes`. Le stock est diminué d'une unité dans la même transaction. Un e-mail de confirmation est envoyé avec `mail()` (Mailpit pour les essais locaux).

Pour tester : ouvrir `detail-menu.php?menu=classique`, cliquer sur Commander, se connecter si nécessaire, choisir une livraison future en respectant les 72 h minimum, puis vérifier dans phpMyAdmin les tables `commandes`, `historique_commandes` et `menus`. Ouvrir ensuite Mailpit sur `http://localhost:8025` pour consulter le message. Refaire le test avec un nombre de personnes inférieur au minimum : le serveur doit refuser. Ne pas conserver les commandes de test dans la base de production.

Le calcul applique un tarif proportionnel au nombre de personnes, une remise de 10 % pour au moins cinq personnes supplémentaires et, hors Bordeaux, 5 € + 0,59 € par kilomètre. Le nombre de kilomètres est renseigné manuellement : il ne s'agit pas d'une vérification automatique de l'adresse. Cette règle de proportionnalité et la distance manuelle sont des choix techniques à expliquer, car l'énoncé ne détaille pas ces calculs. La liste et le détail des menus utilisent encore en partie des données JavaScript locales : leur synchronisation complète avec SQL est une étape ultérieure.

## Qualité des textes visibles

Les bandeaux de suivi interne du type « données fictives » / « dans les prochaines étapes » ont été retirés des pages publiques et des espaces protégés. La documentation conserve volontairement la liste des fonctionnalités non reliées au serveur pour que leur état soit clair pendant le développement. Aucun fichier de configuration privé ne doit être partagé.

## Espace client — étape 7

L’utilisateur connecté voit uniquement les commandes associées à son identifiant SQL. La liste et le détail sont lus dans `commandes` et `menus`. Le suivi est lu dans `historique_commandes`. Les données du profil viennent de `utilisateurs`. Les avis sont stockés dans `avis` seulement pour une commande terminée.

Un client peut modifier les dates, lieux et nombre de personnes, mais pas le menu, tant que le statut est « en attente ». Le calcul des prix reprend les mêmes règles côté PHP que lors de la commande initiale. Il peut annuler une commande en attente ; la base met à jour le statut, l’historique et le stock dans une transaction. Toutes les actions passent par un formulaire POST protégé et vérifient l’appartenance de la commande au client connecté.

Tests à réaliser localement : vérifier qu’une commande SQL apparaît après connexion, que la modification change son montant en SQL, que l’annulation change son statut et restaure le stock, que les actions sont bloquées une fois la commande acceptée, que les comptes sont isolés et qu’un avis ne peut être donné qu’à une commande terminée. Les statuts et avis approuvés par les employés seront implémentés lors de l’étape suivante.

## Espace employé — étape 8

L’espace employé est désormais alimenté par les vraies tables SQL. Le rôle 2 (employé) et le rôle 3 (administrateur) peuvent accéder à cette page. La recherche par statut/client interroge `commandes` et `utilisateurs`. L’historique vient de `historique_commandes`. Les commandes ne sont plus inventées par JavaScript.

L’employé peut avancer dans l’ordre des statuts (en attente, acceptée, préparation, livraison, livrée, retour éventuel de matériel, terminée). Les annulations et les modifications de commandes exigent de confirmer un contact avec le client et d’indiquer le moyen de contact et le motif. Toute mise à jour du montant est recalculée en PHP. Une annulation restaure une unité du stock. L’historique conserve l’identifiant de l’employé.

Le passage à « en attente de retour de matériel » tente d’envoyer un e-mail sur le délai de 10 jours ouvrés et les frais de 600 euros prévus par l’énoncé ; la commande terminée déclenche un e-mail invitant le client à donner un avis. Les avis de clients sont validés ou refusés en SQL. Les menus, plats (y compris leurs allergènes) et horaires existants sont modifiables en SQL. Un plat associé à un menu ne peut pas être supprimé directement pour éviter de casser les liens existants.

**Limites restantes :** l’ancienne page de gestion des galeries de photos `gestion-catalogue.php` et certaines interfaces administrateur ne sont pas encore reliées au serveur. Les menus, horaires et avis affichés sur les pages publiques utilisent encore en partie des données JavaScript/HTML non synchronisées avec les changements effectués en SQL. Les prochaines étapes s’en chargeront. Ne pas présenter ces interfaces comme fonctionnelles avant leur connexion à MariaDB.

La syntaxe PHP/JavaScript, les liens locaux, la structure des formulaires et l’exclusion des secrets de l’archive sont contrôlés automatiquement. La vraie connexion SQL, les droits et les changements de statut restent à vérifier sur XAMPP à la fin du back-end.

## Étape 9 — catalogue public et gestion SQL

Les pages `index.php`, `menus.php` et `detail-menu.php` affichent les données réelles de `menus`, `plats`, `menus_plats`, `plats_allergenes`, `photos_menus`, `themes`, `regimes`, `horaires` et des avis validés dans `avis`. Les cinq filtres JavaScript de `menus.php` restent instantanés après le chargement initial depuis SQL. Le site ne doit plus utiliser les menus et avis conservés dans le navigateur pour les pages publiques.

La page `gestion-catalogue.php`, accessible uniquement aux employés et administrateurs, crée et met à jour les menus, enregistre les plats et allergènes et associe les entrées/plats/desserts aux menus. Elle gère les photos déjà présentes dans `assets/images` ou les nouveaux fichiers JPG/PNG/WebP contrôlés et enregistrés dans `assets/images/catalogue`. Pour préserver les anciennes commandes, « retirer un menu » désactive la vente sans le supprimer physiquement.

Les horaires affichés dans les pieds de page sont récupérés par `php/horaires-public.php` depuis MariaDB. Les anciennes adresses `.html` de l'accueil, des menus et de leur détail redirigent vers les nouvelles pages `.php`.

Aucune nouvelle table SQL n'est nécessaire. Le dossier `assets/images/catalogue` peut accueillir des photos déposées depuis l'interface : sauvegarder ces photos avec le reste du projet. Le code et les chemins ont été vérifiés automatiquement, mais les opérations réelles sur MariaDB et les droits doivent encore être testés localement.

La partie déploiement et les documents légaux restent à finaliser dans les étapes suivantes. Les scénarios locaux des fonctionnalités administrateur et du contact restent à vérifier avant livraison. Les textes juridiques doivent être confirmés par des informations authentiques avant la mise en ligne.

## Administration et base NoSQL — étape 10

- Après installation de CouchDB sous Windows, ouvrir `http://127.0.0.1:5984/_utils#setup` et sélectionner la configuration **Single Node**. Conserver les identifiants administrateur dans un emplacement privé.
- Copier `site-web/php/nosql.local.exemple.php` vers `site-web/php/nosql.local.php` et remplacer les exemples par les vrais identifiants CouchDB. Ne jamais publier ce fichier (un `.gitignore` est prévu dans `site-web/php`).
- La page administrateur synchronise un instantané des commandes SQL dans un document NoSQL `vite_gourmand_stats/commandes`. Les graphiques et le chiffre d'affaires sont ensuite calculés **à partir de ce document CouchDB**, et non de données fictives.
- Le chiffre d'affaires correspond aux commandes livrées, en attente du retour du matériel ou terminées, classées d'après leur **date de commande**. Les commandes annulées ou en attente n'entrent pas dans ce montant.
- Si CouchDB n'est pas accessible, l'administration des employés reste utilisable, mais les statistiques ne sont pas disponibles. Aucun faux résultat n'est affiché.
- Un administrateur doit être créé en dehors de l'inscription publique, par exemple en attribuant manuellement le rôle `3` à un compte préexistant via phpMyAdmin ; aucune page du site ne crée de rôle administrateur.
- Pour un déploiement public, prévoir CouchDB hébergé et sécurisé, avec HTTPS et des accès limités. `localhost` est réservé à XAMPP.

Documentation CouchDB : https://docs.couchdb.org/en/stable/install/windows.html et https://docs.couchdb.org/en/stable/setup/single-node.html


## Contact — étape 11

La page `contact.php` reprend la présentation de l'ancien formulaire de contact. Elle reçoit par `POST` un titre, une adresse e-mail et une description. Les champs sont contrôlés côté serveur et un jeton CSRF protège le formulaire. L'e-mail est envoyé avec la fonction PHP `mail()`. Le message de réussite ne s'affiche que si `mail()` accepte l'envoi ; le formulaire empêche aussi l'envoi répété dans les trente secondes après une réussite. La page `contact.html` renvoie vers la vraie page PHP.

### En local avec Mailpit

Le fichier `contact.php` envoie automatiquement les messages destinés à l'entreprise à **`contact@vite-et-gourmand.test`** quand le site est ouvert sur `localhost`. C'est une adresse fictive réservée au test local, pas l'adresse officielle de l'entreprise. La réception se consulte dans Mailpit à `http://localhost:8025`. Démarrer Apache, Mailpit et la configuration SMTP de PHP utilisée lors des autres étapes. Aucune nouvelle base SQL n'est nécessaire.

### Sur un hébergement public

Créer sur le serveur `site-web/php/contact.local.php` à partir de `contact.local.exemple.php`, avec **la véritable adresse e-mail de l'entreprise** et une adresse expéditrice correspondant au domaine autorisé. Ce fichier est privé et ignoré par Git. Configurer le service de messagerie du serveur : les adresses `.test` et le Mailpit local ne doivent pas être utilisés en production. Pour l'instant l'adresse officielle n'est pas fournie ; aucune adresse réelle n'a été inventée.

### Vérifications restantes

Tester un envoi réel dans Mailpit, une adresse invalide, un champ vide, le délai entre deux envois et la non-répétition après actualisation. Les essais complets du chiffre d'affaires et du refus d'accès des employés désactivés restent prévus pour la phase finale. Les comptes et les statistiques CouchDB ne sont pas modifiés par l'étape 11.

### Correctifs de l'étape 10 inclus

- `commande.html` ne présente plus l'ancien formulaire : redirection vers `commande.php`.
- Les formulaires d'authentification, de réinitialisation et de commande vérifient les types reçus en `POST`.
- `php/parametres-site.php` centralise l'URL du site pour le lien de réinitialisation (à adapter avant déploiement public).
- `php/test-connexion.php`, cinq scripts JavaScript d'ancienne simulation et `assets/js/formulaires.js` sont supprimés dans la livraison. Les fichiers privés `config.local.php`, `nosql.local.php` et `contact.local.php` sont exclus du ZIP.
