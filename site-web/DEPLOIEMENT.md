# Préparer la mise en ligne de Vite & Gourmand — étape 12

Ce guide explique le déploiement ; il ne signifie pas que le site est déjà en ligne.

## Environnement local

1. Placer `vite-et-gourmand` dans `C:\xampp\htdocs\`. Conserver le dossier `base-de-donnees` et le dossier `.git` d'origine.
2. Démarrer Apache et MySQL dans XAMPP. Créer `vite_et_gourmand` puis exécuter les fichiers `base-de-donnees/01_creation.sql` et `02_donnees.sql` dans cet ordre (sur une base de test VIDE ; faire une sauvegarde avant toute importation).
3. Dans `site-web/php`, copier `config.local.exemple.php` vers `config.local.php`, puis remplir les identifiants MariaDB. Ne jamais partager le fichier local.
4. Démarrer CouchDB sur `127.0.0.1:5984`. Copier `nosql.local.exemple.php` en `nosql.local.php` et renseigner les identifiants administrateur. La base `vite_gourmand_stats` est créée lors de l'ouverture de l'espace administrateur.
5. Démarrer Mailpit (port SMTP 1025 et interface 8025), puis configurer l'envoi de PHP par SMTP local dans `php.ini` si nécessaire. Redémarrer Apache après modification.
6. Accéder à `http://localhost/vite-et-gourmand/site-web/index.php`.

## Mise en ligne : les prérequis

1. Choisir un hébergeur prenant en charge PHP, MariaDB et Apache, et rendre CouchDB accessible uniquement depuis le serveur applicatif via une connexion protégée. Conserver une sauvegarde des bases. Ne pas exposer CouchDB directement sur Internet.
2. Importer les fichiers SQL sur une base neuve ; protéger les identifiants SQL et CouchDB. Vérifier la version de PHP et ses extensions PDO MySQL et HTTPS.
3. Sous `site-web/php`, créer les fichiers PRIVÉS `config.local.php`, `nosql.local.php`, `parametres-site.local.php` et `contact.local.php` à partir des exemples. Dans `parametres-site.local.php`, remplacer `https://votre-domaine.fr` par la véritable URL du dossier contenant `index.php`, et définir une vraie adresse d'expédition. Dans `contact.local.php`, définir le destinataire et l'expéditeur réels. Ne pas conserver les domaines `.test` en production.
4. Configurer l'envoi SMTP réellement utilisable sur l'hébergement. La fonction PHP `mail()` peut nécessiter un relais SMTP du fournisseur. La réception des messages doit être testée avant publication.
5. Configurer HTTPS, les accès à la base, les sauvegardes, les permissions et les fichiers `.htaccess`. Vérifier que les fichiers de configuration privés ne sont pas accessibles par HTTP et qu'aucun fichier `.local.php` n'est commité.
6. Créer le compte administrateur de façon sécurisée à partir des données prévues dans la base. L'application ne propose volontairement pas d'inscription administrateur.
7. Effectuer tous les parcours réels : inscription avec mail de bienvenue, connexion, réinitialisation, commande, suivi, employé, administration, contact, graphiques, droits d'accès. Consigner les résultats.
8. Vérifier l'accessibilité conformément au RGAA, les mentions légales, les CGV, la protection des données et l'hébergeur. Ces informations réelles ne doivent pas être inventées.

## Important

- Le projet local fonctionne avec `localhost`, mais ce nom est inaccessible à un visiteur extérieur : il faut un hébergeur et une URL publique.
- Les fichiers PHP de configuration du dossier `php` sont protégés par `.htaccess` sur Apache, mais doivent également rester hors des archives publiques et de Git.
- Le ZIP de livraison ne contient **aucun** mot de passe ; conserver les fichiers locaux existants lors de la fusion sous Windows.
- Sans hébergeur retenu, URL publique et boîte e-mail réelles, une mise en ligne fonctionnelle à 100 % ne peut pas être certifiée.
- Il reste à rédiger la copie ECF, le manuel PDF, la charte PDF, les diagrammes et la documentation de gestion de projet demandés dans l'énoncé (pages 11-12).
