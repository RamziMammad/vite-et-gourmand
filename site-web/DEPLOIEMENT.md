# Guide de déploiement de Vite & Gourmand

Ce document présente l'installation en local et les préparatifs d'une future mise en ligne. À ce jour, le site a été testé en local ; il n'est pas encore accessible sur une adresse publique.

## Environnement local

1. Placer le projet `vite-et-gourmand` dans `C:\xampp\htdocs\`, avec les dossiers `base-de-donnees`, `site-web` et `documentation`. Le dossier `.git` n'est utile que pour travailler avec Git ; il n'est pas nécessaire au fonctionnement du site.
2. Démarrer Apache et MySQL dans XAMPP. Créer la base `vite_et_gourmand`, puis exécuter `base-de-donnees/01_creation.sql` et `base-de-donnees/02_donnees.sql` dans cet ordre, **uniquement sur une base de test vide**. Sauvegarder toute base existante avant une importation.
3. Dans `site-web/php`, copier `config.local.exemple.php` en `config.local.php`, puis saisir les paramètres MariaDB propres à l'environnement local. Ne pas partager ce fichier.
4. Démarrer CouchDB sur `127.0.0.1:5984`. Copier `nosql.local.exemple.php` en `nosql.local.php` et renseigner ses identifiants. La base `vite_gourmand_stats` est initialisée lors de l'accès aux statistiques de l'espace administrateur.
5. Démarrer Mailpit (port SMTP `1025`, interface `http://localhost:8025`). Configurer l'envoi de PHP via le serveur SMTP local dans `php.ini` si nécessaire, puis redémarrer Apache après toute modification.
6. Ouvrir `http://localhost/vite-et-gourmand/site-web/index.php` et vérifier les principaux parcours (inscription, connexion, menus, commandes, espaces réservés et e-mails).

Les scripts SQL installent la structure et des données de départ. Les comptes de démonstration utilisés pour les trois rôles doivent être créés et vérifiés séparément. Le manuel avec leurs identifiants est réservé à la remise privée et **ne doit pas être publié sur GitHub**.

## Mise en ligne : préparatifs

La mise en ligne sera réalisée séparément après le choix d'un hébergement adapté. Elle exigera notamment :

1. Un hébergement compatible avec PHP et MariaDB, et une connexion CouchDB sécurisée accessible au serveur applicatif, sans exposer directement CouchDB au public.
2. L'importation des scripts SQL sur une nouvelle base, la configuration des extensions PHP nécessaires (dont PDO MySQL) et la protection des identifiants SQL et NoSQL.
3. La création des fichiers locaux `config.local.php`, `nosql.local.php`, `parametres-site.local.php` et `contact.local.php` à partir des exemples ; les paramètres de domaine et de courrier électronique devront être adaptés à l'hébergement réel.
4. Un service d'envoi d'e-mails fonctionnel, la configuration HTTPS, des sauvegardes, les permissions et la protection des fichiers sensibles par le serveur web.
5. La création sécurisée du compte administrateur, qui ne peut pas être créé via l'inscription publique.
6. Des tests sur l'adresse publique : inscription, e-mails, commandes, suivi, espace employé, espace administrateur, statistiques, contrôles d'accès et affichage mobile.
7. Une vérification de l'accessibilité (RGAA), des mentions légales, des CGV et de l'information sur les données personnelles, en tenant compte des coordonnées et de l'hébergeur réels.

## Suivi du projet et documents

- Dépôt GitHub : https://github.com/RamziMammad/vite-et-gourmand
- Tableau de suivi GitHub Projects (public) : https://github.com/users/RamziMammad/projects/1
- Les maquettes, la charte graphique et les documents techniques se trouvent dans `documentation/`.
- Les comptes de démonstration ont été préparés et documentés dans un manuel séparé réservé à la remise privée. Aucun mot de passe de démonstration ne doit être ajouté aux fichiers publiés sur GitHub.
- L'énoncé Studi mentionne une copie à rendre au format Word ou Excel. Elle devra être récupérée auprès de Studi si elle n'apparaît pas dans l'espace ECF, puis remplie avec les liens réels.
- Une archive créée par `git archive` contient les fichiers suivis par Git, et non les configurations locales ignorées. **Vérifier toutefois son contenu avant de la partager** : les fichiers déjà enregistrés dans Git sont inclus, même s'ils contiennent des données sensibles.

**État actuel :** les vérifications ont été effectuées en local. L'hébergement et l'adresse publique restent à déterminer ; ce guide ne prétend pas que le site est déjà en ligne.
