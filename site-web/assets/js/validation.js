/* Vérification des dates et des montants */

const VF = {
    lireDate: function(texte) {
        if (!texte) return null;

        const morceaux = texte.split("-");
        if (morceaux.length !== 3) return null;

        const annee = Number(morceaux[0]);
        const mois = Number(morceaux[1]);
        const jour = Number(morceaux[2]);
        const date = new Date(annee, mois - 1, jour);

        if (date.getFullYear() !== annee) return null;
        if (date.getMonth() !== mois - 1) return null;
        if (date.getDate() !== jour) return null;
        return date;
    },

    verifierDates: function(prestation, livraison, heure, delai) {
        const datePrestation = this.lireDate(prestation);
        const dateLivraison = this.lireDate(livraison);

        if (!datePrestation || !dateLivraison || !heure) {
            return "Indiquez des dates et une heure valides.";
        }

        const morceauxHeure = heure.split(":");
        if (morceauxHeure.length !== 2) return "Indiquez une heure valide.";

        const heures = Number(morceauxHeure[0]);
        const minutes = Number(morceauxHeure[1]);
        if (heures < 0 || heures > 23 || minutes < 0 || minutes > 59) {
            return "Indiquez une heure valide.";
        }

        const maintenant = new Date();
        const heuresMinimum = Number(delai) || 72;
        const minimum = heuresMinimum * 60 * 60 * 1000;

        // Le délai se calcule avec la date et l'heure de livraison.
        dateLivraison.setHours(heures, minutes, 0, 0);
        if (dateLivraison.getTime() <= maintenant.getTime()) {
            return "La livraison doit être dans le futur.";
        }
        if (dateLivraison.getTime() - maintenant.getTime() < minimum) {
            return "Réservez au moins " + heuresMinimum + " heures à l'avance.";
        }
        if (livraison > prestation) {
            return "La livraison ne peut pas avoir lieu après la prestation.";
        }
        return "";
    },

    verifierPersonnes: function(menu, personnes) {
        const nombre = Number(personnes);
        return Number.isInteger(nombre) && nombre >= Number(menu.minimum);
    },

    calcul: function(menu, personnes, zone, distance) {
        const prix = Number(menu.prix) * Number(personnes) / Number(menu.minimum);
        let reduction = 0;
        let livraison = 0;

        if (Number(personnes) >= Number(menu.minimum) + 5) {
            reduction = prix * 0.10;
        }
        if (zone === "hors-bordeaux") {
            livraison = 5 + Number(distance) * 0.59;
        }
        const total = Math.round((prix - reduction + livraison) * 100) / 100;
        return { prix: prix, reduction: reduction, livraison: livraison, total: total };
    }
};
