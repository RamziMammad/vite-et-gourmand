
// ===== Commande =====

/* Calcule le montant du menu et de la livraison. */
const champMenu = document.getElementById("menu-choisi");
const champPersonnes = document.getElementById("nombre-personnes");
const champVille = document.getElementById("ville-livraison");
const champDistance = document.getElementById("distance");

function montantTexte(nombre) {
    return Number(nombre).toFixed(2).replace(".", ",") + " €";
}

function calculerCommande() {
    const menu = VG.menus()[champMenu.value];
    const message = document.getElementById("message-minimum");

    if (!menu || !VG.menuDisponible(champMenu.value)) {
        message.textContent = "Ce menu n'est pas disponible.";
        document.getElementById("prix-total").textContent = "—";
        return;
    }

    const personnes = Number(champPersonnes.value);
    if (!VF.verifierPersonnes(menu, champPersonnes.value) ||
        champPersonnes.value === "") {
        message.textContent = "Minimum : " + menu.minimum + " personnes.";
        document.getElementById("prix-total").textContent = "—";
        return;
    }

    message.textContent = "Minimum : " + menu.minimum + " personnes.";
    const zone = champVille.value;
    const distance = Number(champDistance.value);
    const montant = VF.calcul(menu, personnes, zone, distance);

    document.getElementById("recap-menu").textContent = menu.nom;
    document.getElementById("recap-personnes").textContent = personnes;
    document.getElementById("prix-menu").textContent = montantTexte(montant.prix);
    document.getElementById("reduction").textContent = montantTexte(montant.reduction);

    if (zone === "hors-bordeaux" && (champDistance.value === "" ||
        !Number.isFinite(distance) || distance <= 0)) {
        document.getElementById("prix-livraison").textContent = "Distance à préciser";
        document.getElementById("prix-total").textContent = "—";
        return;
    }

    document.getElementById("prix-livraison").textContent =
        montantTexte(montant.livraison);
    document.getElementById("prix-total").textContent = montantTexte(montant.total);
}

function changerMenu() {
    const menu = VG.menus()[champMenu.value];
    if (!menu) return;
    champPersonnes.min = menu.minimum;
    champPersonnes.value = menu.minimum;
    calculerCommande();
}


// ===== Commande choix menu =====

/* Prépare les options du menu, avec le bon menu sélectionné. */
const choixCommande = document.getElementById("menu-choisi");
const rechercheCommande = window.location.search;
let menuDemande = "classique";

if (rechercheCommande.indexOf("menu=") !== -1) {
    menuDemande = rechercheCommande.split("menu=")[1].split("&")[0];
}

choixCommande.textContent = "";
let premierMenu = "";
const menusCommande = VG.menus();

for (const code in menusCommande) {
    const option = document.createElement("option");
    option.value = code;
    option.textContent = menusCommande[code].nom;
    option.disabled = !VG.menuDisponible(code);
    choixCommande.appendChild(option);
    if (premierMenu === "" && !option.disabled) premierMenu = code;
}

if (premierMenu === "") {
    choixCommande.disabled = true;
    document.getElementById("nombre-personnes").disabled = true;
    document.getElementById("message-minimum").textContent =
        "Aucun menu disponible actuellement.";
} else {
    choixCommande.value = VG.menuDisponible(menuDemande)
        ? menuDemande : premierMenu;
    changerMenu();
}


// ===== Commande evenements =====

/* Réagit aux changements dans le formulaire de commande. */
champMenu.addEventListener("change", changerMenu);
champPersonnes.addEventListener("input", calculerCommande);
champVille.addEventListener("change", calculerCommande);
champDistance.addEventListener("input", calculerCommande);


// ===== Commande verification =====

/* Vérification des informations du formulaire */

const formulaireCommande = document.getElementById("formulaire-commande");
const messageCommande = document.getElementById("message-validation-commande");

function lireChamp(id) {
    return document.getElementById(id).value;
}

function verifierCommandeEssai() {
    const code = lireChamp("menu-choisi");
    const menu = VG.menus()[code];

    if (!menu || !VG.menuDisponible(code)) {
        return "Ce menu n'est pas disponible.";
    }

    if (!VF.verifierPersonnes(menu, lireChamp("nombre-personnes"))) {
        return "Il faut au moins " + menu.minimum + " personnes.";
    }

    const erreur = VF.verifierDates(
        lireChamp("date-prestation"),
        lireChamp("date-livraison"),
        lireChamp("heure-livraison"),
        menu.delaiHeures
    );

    if (erreur !== "") return erreur;

    if (lireChamp("ville-livraison") === "hors-bordeaux") {
        const distance = Number(lireChamp("distance"));

        if (
            lireChamp("distance") === "" ||
            !Number.isFinite(distance) ||
            distance <= 0
        ) {
            return "Indiquez une distance supérieure à zéro.";
        }
    }

    return "";
}


// ===== Confirmation de commande =====

// Les informations seront enregistrées par PHP quand le back-end sera en place.
formulaireCommande.addEventListener("submit", function(event) {
    event.preventDefault();
    const erreur = verifierCommandeEssai();
    if (erreur !== "") {
        messageCommande.textContent = erreur;
        return;
    }
    if (!lireChamp("adresse-prestation").trim() ||
        !lireChamp("adresse-livraison").trim()) {
        messageCommande.textContent = "Renseignez les deux adresses.";
        return;
    }
    messageCommande.textContent = "Connectez-vous pour confirmer votre commande.";
    window.location.href = "connexion.php?menu=" + lireChamp("menu-choisi");
});
