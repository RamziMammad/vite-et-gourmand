
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

// Pour la démonstration, on passe d'abord par la page Connexion.
if (localStorage.getItem("vg_mode_demo") !== "oui") {
    window.location.href = "connexion.html?menu=" + menuDemande;
}

// Identité fictive pour essayer le formulaire sans serveur.
if (localStorage.getItem("vg_mode_demo") === "oui") {
    document.getElementById("nom").value = "Durand";
    document.getElementById("prenom").value = "Camille";
    document.getElementById("email").value = "camille@example.test";
    document.getElementById("telephone").value = "0600000000";
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

/* Enregistrement d'une commande d'essai */

const formulaireEssai = document.getElementById("formulaire-commande");
const messageEssai = document.getElementById("message-validation-commande");

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


// ===== Commande enregistrement =====

formulaireEssai.addEventListener("submit", function(event) {
    event.preventDefault();

    if (localStorage.getItem("vg_mode_demo") !== "oui") {
        messageEssai.textContent = "Passez par la connexion avant de commander.";
        return;
    }

    const erreur = verifierCommandeEssai();
    messageEssai.textContent = erreur;

    if (erreur !== "") return;

    if (
        !lireChamp("adresse-prestation").trim() ||
        !lireChamp("adresse-livraison").trim()
    ) {
        messageEssai.textContent =
            "Renseignez les deux adresses de démonstration.";
        return;
    }

    const code = lireChamp("menu-choisi");
    const menu = VG.menus()[code];
    const personnes = Number(lireChamp("nombre-personnes"));
    const zone = lireChamp("ville-livraison");

    const distance = zone === "bordeaux"
        ? 0
        : Number(lireChamp("distance"));

    const montant = VF.calcul(menu, personnes, zone, distance);

    const commandes = VG.commandes();
    const identifiant = "essai-" + Date.now();

    commandes.push({
        id: identifiant,
        client: "camille",
        menu: code,
        personnes: personnes,
        statut: "attente",
        montant: montant.total,
        fraisLivraison: montant.livraison,
        date: lireChamp("date-prestation"),
        dateLivraison: lireChamp("date-livraison"),
        heureLivraison: lireChamp("heure-livraison"),
        adressePrestation: lireChamp("adresse-prestation").trim(),
        adresseLivraison: lireChamp("adresse-livraison").trim(),
        zoneLivraison: zone,
        distance: distance,
        historique: ["Commande d'essai créée — " + new Date().toLocaleString("fr-FR")],
        simulation: true
    });

    VG.ecrire("commandes", commandes);

    const menus = VG.menus();

    menus[code].stock = Number(menus[code].stock) - 1;

    VG.ecrire("menus", menus);

    messageEssai.textContent =
        "Commande d'essai enregistrée : " + identifiant +
        ". Vous pouvez la consulter dans Mon espace. Aucun paiement ni e-mail envoyé.";

    document.getElementById("lien-espace-apres-commande").hidden = false;
    document.getElementById("bouton-essai-commande").disabled = true;
});
