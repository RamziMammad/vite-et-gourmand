Vite & Gourmand


Présentation



Vite & Gourmand est un projet que je réalise dans le cadre de mon ECF chez Studi, pour ma formation de développeur web.


Le but est de créer un site pour une entreprise de traiteur située à Bordeaux. Elle est tenue par Julie et José depuis 25 ans.


Sur le site, les visiteurs pourront consulter les menus, voir les détails des plats et passer des commandes.


Les outils utilisés

Pour ce projet, j'utilise plusieurs outils :

HTML pour construire les pages.

CSS pour la mise en page et les couleurs.

JavaScript pour rendre le site interactif.

PHP pour gérer le fonctionnement côté serveur.

MySQL pour la base de données relationnelle.

Une base NoSQL pour les données demandées dans l'ECF.

Figma pour les maquettes.

VS Code pour écrire le code.

XAMPP pour faire fonctionner le projet en local.

Git et GitHub pour sauvegarder les différentes versions du projet.

Le PHP et les bases de données ne sont pas encore mis en place.



Ce qui a été fait

J'ai commencé par l'analyse des besoins et les maquettes sur Figma.

Ensuite, j'ai réalisé les trois premières pages du site :

index.html : page d'accueil.

menus.html : page avec les menus et les filtres.

detail-menu.html : page qui affiche les informations d'un menu.

J'ai également fait les versions pour ordinateur et téléphone avec le CSS.

Il reste encore à développer les fonctionnalités en JavaScript, les comptes utilisateurs, les commandes et la partie administration.



Comment lancer le site en local

Pour récupérer et ouvrir le projet sur un ordinateur :

Installer Git et XAMPP.

Ouvrir un terminal dans le dossier C:\xampp\htdocs.

Télécharger le projet avec cette commande :

git clone -b develop https://github.com/RamziMammad/vite-et-gourmand.git

Ouvrir XAMPP et démarrer Apache.

Dans le navigateur, aller sur :

http://localhost/vite-et-gourmand/site-web/index.html

Pour le moment, cette méthode permet de consulter les pages HTML et CSS. Les étapes d'installation de la base de données seront ajoutées lorsque le back-end sera terminé.




Organisation du projet

Les fichiers du site se trouvent dans le dossier site-web.

Les fichiers CSS sont dans assets/css, les images dans assets/images et les fichiers JavaScript dans assets/js.

Pour Git, j'utilise une branche main, une branche develop et des branches feature pour travailler sur les différentes fonctionnalités.






Ce qu'il reste à faire

La suite sera de rendre les filtres fonctionnels avec JavaScript, puis de développer la partie PHP et les bases de données.

Il faudra aussi tester le site, préparer les documents demandés pour l'ECF et mettre l'application en ligne.

Ce README sera mis à jour au fur et à mesure de l'avancement du projet.