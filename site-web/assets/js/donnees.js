/* Vite et Gourmand - donnees.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Stockage local =====

/* Lit et enregistre les données de test dans ce navigateur. */

const VG = {
    lire: function(cle, depart) {
        const valeur = localStorage.getItem("vg_" + cle);

        if (valeur === null) return depart;

        try {
            return JSON.parse(valeur);
        } catch (erreur) {
            return depart;
        }
    },

    ecrire: function(cle, valeur) {
        localStorage.setItem(
            "vg_" + cle,
            JSON.stringify(valeur)
        );
    }
};


// ===== Donnees menus =====

/* Contient les deux menus de départ et leurs informations. */

VG.menus = function() {
    const depart = {
        classique: {
            nom: "Menu Classique",
            description: "Tomates-mozzarella, poulet rôti et tarte aux pommes.",
            theme: "classique",
            regime: "classique",
            minimum: 10,
            prix: 250,
            stock: 5,
            conditions: "Réservation au moins 72 h à l'avance.",
            delaiHeures: 72,
            actif: true,
            photo: "assets/images/menus/Menu classique _ poulet roti.png",
            galerie: [
                "assets/images/detail-menu/Poulet rôti, légumes et jus doré.png",
                "assets/images/detail-menu/Salade caprese au basilic frais.png",
                "assets/images/detail-menu/Tarte aux pommes caramélisées.png"
            ],
            plats: {
                entree: "entree-classique",
                plat: "plat-classique",
                dessert: "dessert-classique"
            }
        },

        vegetarien: {
            nom: "Menu Végétarien",
            description: "Salade de saison, risotto aux champignons et mousse au chocolat.",
            theme: "classique",
            regime: "vegetarien",
            minimum: 8,
            prix: 200,
            stock: 5,
            conditions: "Réservation au moins 72 h à l'avance.",
            delaiHeures: 72,
            actif: true,
            photo: "assets/images/menus/végétarien gourmand aux champignons.png",
            galerie: [
                "assets/images/detail-menu/Menu végétarien – Quiche aux légumes.png",
                "assets/images/detail-menu/menu.png",
                "assets/images/detail-menu/Gratin végétal et tarte citron meringuée.png"
            ],
            plats: {
                entree: "entree-vegetarien",
                plat: "plat-vegetarien",
                dessert: "dessert-vegetarien"
            }
        }
    };

    const donnees = this.lire("menus", depart);

    for (const code in donnees) {
        if (depart[code]) {
            if (!donnees[code].plats) {
                donnees[code].plats = depart[code].plats;
            }

            if (!donnees[code].photo) {
                donnees[code].photo = depart[code].photo;
            }

            if (!donnees[code].galerie) {
                donnees[code].galerie = depart[code].galerie;
            }

            if (!donnees[code].delaiHeures) {
                donnees[code].delaiHeures = 72;
            }
        }
    }

    return donnees;
};


// ===== Donnees plats =====

/* Plats de départ : chacun peut être utilisé dans plusieurs menus. */
VG.plats = function() {
    const depart = [
        {
            id: "entree-classique",
            nom: "Tomates, mozzarella et basilic",
            type: "Entrée",
            allergenes: "lait (mozzarella)"
        },
        {
            id: "plat-classique",
            nom: "Poulet rôti, pommes de terre au beurre",
            type: "Plat",
            allergenes: "lait (beurre)"
        },
        {
            id: "dessert-classique",
            nom: "Tarte aux pommes",
            type: "Dessert",
            allergenes: "blé, œufs et lait"
        },
        {
            id: "entree-vegetarien",
            nom: "Salade de saison : laitue, tomates, concombre, mozzarella et basilic",
            type: "Entrée",
            allergenes: "lait (mozzarella)"
        },
        {
            id: "plat-vegetarien",
            nom: "Risotto aux champignons, beurre et crème",
            type: "Plat",
            allergenes: "lait (beurre et crème)"
        },
        {
            id: "dessert-vegetarien",
            nom: "Mousse au chocolat noir, œufs et crème",
            type: "Dessert",
            allergenes: "œufs et lait (crème)"
        }
    ];

    return this.lire("plats", depart);
};


// ===== Disponibilite menu =====

/* Vérifie si un menu peut encore être commandé. */

VG.menuDisponible = function(code) {
    const menu = this.menus()[code];

    if (
        !menu ||
        !menu.actif ||
        !Number.isInteger(Number(menu.stock)) ||
        Number(menu.stock) <= 0
    ) {
        return false;
    }

    if (!menu.plats) return false;

    const plats = this.plats();
    const choix = [
        menu.plats.entree,
        menu.plats.plat,
        menu.plats.dessert
    ];
    const types = ["Entrée", "Plat", "Dessert"];

    for (let i = 0; i < choix.length; i++) {
        let trouve = false;

        for (let j = 0; j < plats.length; j++) {
            if (
                plats[j].id === choix[i] &&
                plats[j].type === types[i]
            ) {
                trouve = true;
            }
        }

        if (!trouve) return false;
    }

    return true;
};
