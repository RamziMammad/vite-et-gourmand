// Commande : calcul indicatif et vérifications dans le navigateur.
const champMenu = document.getElementById("menu-choisi");
const champPersonnes = document.getElementById("nombre-personnes");
const champVille = document.getElementById("ville-livraison");
const champDistance = document.getElementById("distance");
const messageCommande = document.getElementById("message-validation-commande");
const titreMessageCommande = document.getElementById("titre-validation-commande");
const texteMessageCommande = document.getElementById("texte-validation-commande");

function menuSelectionne() {
    const option = champMenu.options[champMenu.selectedIndex];
    if (!option) return null;
    return {
        nom: option.textContent.trim(),
        minimum: Number(option.dataset.minimum),
        prix: Number(option.dataset.prix),
        delaiHeures: Number(option.dataset.delai)
    };
}

function montantTexte(nombre) {
    return Number(nombre).toFixed(2).replace(".", ",") + " €";
}

function calculerCommande() {
    const menu = menuSelectionne();
    const message = document.getElementById("message-minimum");
    if (!menu) {
        message.textContent = "Aucun menu disponible actuellement.";
        document.getElementById("prix-total").textContent = "—";
        return;
    }

    const personnes = Number(champPersonnes.value);
    if (!VF.verifierPersonnes(menu, champPersonnes.value) || champPersonnes.value === "") {
        message.textContent = "Minimum : " + menu.minimum + " personnes.";
        document.getElementById("prix-total").textContent = "—";
        return;
    }

    message.textContent = "Minimum : " + menu.minimum + " personnes.";
    const distance = Number(champDistance.value);
    const zone = champVille.value;
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

    document.getElementById("prix-livraison").textContent = montantTexte(montant.livraison);
    document.getElementById("prix-total").textContent = montantTexte(montant.total);
}

function changerMenu() {
    const menu = menuSelectionne();
    if (!menu) return;
    champPersonnes.min = menu.minimum;
    champPersonnes.value = menu.minimum;
    calculerCommande();
}

champMenu.addEventListener("change", changerMenu);
champPersonnes.addEventListener("input", calculerCommande);
champVille.addEventListener("change", calculerCommande);
champDistance.addEventListener("input", calculerCommande);

function afficherErreurCommande(erreur) {
    if (erreur === "La livraison doit être dans le futur.") {
        titreMessageCommande.textContent = "Date de livraison invalide";
        texteMessageCommande.textContent = "La date de livraison sélectionnée est déjà passée. Veuillez choisir une date future pour poursuivre votre commande.";
    } else {
        titreMessageCommande.textContent = "Vérifiez votre commande";
        texteMessageCommande.textContent = erreur;
    }
    messageCommande.hidden = false;
}

// La vérification au clic fonctionne même si le navigateur bloque l'envoi du formulaire.
document.getElementById("bouton-valider-commande").addEventListener("click", function(event) {
    const menu = menuSelectionne();
    if (!menu) return;
    const erreurDate = VF.verifierDates(
        document.getElementById("date-prestation").value,
        document.getElementById("date-livraison").value,
        document.getElementById("heure-livraison").value,
        menu.delaiHeures
    );
    if (erreurDate) {
        event.preventDefault();
        afficherErreurCommande(erreurDate);
    }
});

document.getElementById("formulaire-commande").addEventListener("submit", function(event) {
    const menu = menuSelectionne();
    let erreur = "";

    if (!menu) {
        erreur = "Ce menu n'est pas disponible.";
    } else if (!VF.verifierPersonnes(menu, champPersonnes.value)) {
        erreur = "Il faut au moins " + menu.minimum + " personnes.";
    } else {
        erreur = VF.verifierDates(
            document.getElementById("date-prestation").value,
            document.getElementById("date-livraison").value,
            document.getElementById("heure-livraison").value,
            menu.delaiHeures
        );
    }

    if (!erreur && champVille.value === "hors-bordeaux" &&
        (champDistance.value === "" || Number(champDistance.value) <= 0)) {
        erreur = "Indiquez une distance supérieure à zéro.";
    }

    if (erreur) {
        event.preventDefault();
        afficherErreurCommande(erreur);
    } else {
        messageCommande.hidden = true;
    }
});

if (champMenu.options.length > 0) {
    const menu = menuSelectionne();
    champPersonnes.min = menu.minimum;
    if (Number(champPersonnes.value) < menu.minimum) {
        champPersonnes.value = menu.minimum;
    }
}
calculerCommande();
