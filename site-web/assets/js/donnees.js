// Les données du catalogue sont fournies par PHP et MariaDB.
const VG = {
    menus: function() { return {}; },
    plats: function() { return []; },
    horaires: function() { return []; },
    avis: function() { return []; }
};

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
