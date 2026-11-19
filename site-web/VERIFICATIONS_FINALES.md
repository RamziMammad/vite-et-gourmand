# Vérifications du projet — Vite & Gourmand

## À contrôler sur XAMPP et Mailpit

- Inscription d'un client et réception de l'e-mail de bienvenue dans Mailpit
- Connexion, déconnexion, mot de passe oublié (lien valable une heure, usage unique)
- Page menus, filtres sans rechargement, photos, plats, allergènes, détails et conditions
- Commande : minimum de personnes, réduction 10 %, frais hors Bordeaux, délai, stock et e-mail de confirmation
- Modification et annulation client, historique et contrôle d'accès à ses propres commandes
- Employé : filtres, changements de statuts, annulation et modification après contact, e-mails et modération d'avis
- Administrateur : compte employé, désactivation effective, statistiques CouchDB et chiffre d'affaires selon menu et période
- Formulaire de contact : titre, e-mail et description, message reçu dans Mailpit, refus d'un formulaire invalide et des envois trop rapprochés
- Sécurité : injections SQL, échappement XSS, protections CSRF, accès interdits, fichiers privés non accessibles par HTTP
- Responsive et parcours clavier : en-têtes, étiquettes de champs, focus, navigation sans souris, contrastes et médias alternatifs (RGAA)
- Production : HTTPS, SMTP, base SQL et CouchDB, configuration URL, e-mails réels, liens et permissions
- Mentions légales et CGV validées avec les informations réelles

## Livrables demandés par Studi

- README local + scripts SQL importables sur une base neuve
- Branches Git `main` / `develop` et fonctionnalités, après tests
- URL du dépôt GitHub public, URL du site déployé et lien outil de gestion de projet
- Manuel utilisateur PDF avec parcours et identifiants **de démonstration**, jamais les secrets de production
- Charte graphique PDF, 3 maquettes bureau et 3 mobiles
- Documentation de projet et technique : MCD, cas d'utilisation, séquence et déploiement

Les contrôles de syntaxe automatiques ne prouvent pas le fonctionnement des services XAMPP, CouchDB, SMTP ou de l'hébergeur.
