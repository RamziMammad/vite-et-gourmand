/* Vite et Gourmand - donnees-exemples.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Donnees exemples =====

/* Contient les images, les horaires, les avis et les commandes fictives. */

VG.images = function() {
    return [
        "assets/images/menus/Menu classique _ poulet roti.png",
        "assets/images/menus/végétarien gourmand aux champignons.png",
        "assets/images/detail-menu/Poulet rôti, légumes et jus doré.png",
        "assets/images/detail-menu/Salade caprese au basilic frais.png",
        "assets/images/detail-menu/Tarte aux pommes caramélisées.png",
        "assets/images/detail-menu/Menu végétarien – Quiche aux légumes.png",
        "assets/images/detail-menu/menu.png",
        "assets/images/detail-menu/Gratin végétal et tarte citron meringuée.png"
    ];
};

VG.horaires = function() {
    return this.lire("horaires", [
        "09h00 - 18h00",
        "09h00 - 18h00",
        "09h00 - 18h00",
        "09h00 - 18h00",
        "09h00 - 18h00",
        "09h00 - 17h00",
        "Fermé"
    ]);
};

VG.avis = function() {
    return this.lire("avis", [
        {
            note: 5,
            commentaire: "Excellent repas et service très professionnel !",
            statut: "valide"
        },
        {
            note: 5,
            commentaire: "Des plats délicieux pour notre événement !",
            statut: "valide"
        },
        {
            note: 4,
            commentaire: "Très bon repas et équipe agréable.",
            statut: "attente"
        }
    ]);
};

VG.commandes = function() {
    const depart = [
        {
            id: "commande-001",
            client: "camille",
            statut: "attente",
            menu: "classique",
            personnes: 10,
            montant: 250,
            date: "2026-11-20"
        },
        {
            id: "commande-002",
            client: "thomas",
            statut: "acceptee",
            menu: "vegetarien",
            personnes: 8,
            montant: 200,
            date: "2026-11-21"
        },
        {
            id: "commande-003",
            client: "lea",
            statut: "preparation",
            menu: "classique",
            personnes: 15,
            montant: 337.5,
            date: "2026-11-24"
        },
        {
            id: "archive-demo",
            client: "camille",
            statut: "terminee",
            menu: "vegetarien",
            personnes: 8,
            montant: 200,
            date: "2026-11-03",
            historique: [
                "Acceptée — 02/11/2026 à 09:00 (exemple)",
                "En préparation — 02/11/2026 à 10:00 (exemple)",
                "Livrée — 03/11/2026 à 12:00 (exemple)",
                "Terminée — 03/11/2026 à 15:00 (exemple)"
            ]
        }
    ];

    const commandes = this.lire("commandes", depart);
    let archiveTrouvee = false;
    let modifie = false;

    for (let i = 0; i < commandes.length; i++) {
        if (commandes[i].id === "archive-demo") archiveTrouvee = true;

        // Les anciennes commandes n'avaient pas toujours de suivi daté.
        if (!commandes[i].historique || commandes[i].historique.length === 0) {
            commandes[i].historique = [
                "Suivi commencé au statut " + commandes[i].statut +
                " — " + new Date().toLocaleString("fr-FR")
            ];
            modifie = true;
        }
    }

    if (!archiveTrouvee) {
        commandes.push(depart[3]);
        modifie = true;
    }
    if (modifie) this.ecrire("commandes", commandes);

    return commandes;
};
