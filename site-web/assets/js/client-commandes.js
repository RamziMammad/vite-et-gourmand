/* Vite et Gourmand - client-commandes.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Client base =====

/* Commandes et avis du client */

function clientElement(id) {
    return document.getElementById(id);
}

function prixClient(nombre) {
    return Number(nombre).toFixed(2).replace(".", ",") + " €";
}

function dateClient(texte) {
    if (!texte) return "Non renseignée";

    return new Date(texte + "T12:00:00").toLocaleDateString("fr-FR");
}

const commandesClient = VG.commandes();
let commandeActive = null;

const noteDemonstration = document.querySelector(".information-demo");

if (noteDemonstration) {
    noteDemonstration.textContent =
        "Utilisez uniquement des informations fictives : les commandes d'essai sont conservées dans ce navigateur.";
}

const listeClient = clientElement("choix-commande-client");
const messagesClient = clientElement("message-commande");
const modifierClient = clientElement("formulaire-modification-commande");
const annulerClient = clientElement("confirmation-annulation");

const nomsEtats = {
    attente: "En attente",
    acceptee: "Acceptée",
    preparation: "En préparation",
    livraison: "En cours de livraison",
    livree: "Livrée",
    materiel: "Retour du matériel attendu",
    terminee: "Terminée",
    annulee: "Annulée"
};

for (let i = 0; i < commandesClient.length; i++) {
    if (commandesClient[i].client === "camille") {
        const choix = document.createElement("option");

        choix.value = commandesClient[i].id;
        choix.textContent = "Commande " + commandesClient[i].id;

        listeClient.appendChild(choix);
    }
}

// Affichage des commandes


// ===== Client affichage =====

function afficherCommande() {
    if (!commandeActive) return;

    const c = commandeActive;
    const menu = VG.menus()[c.menu];

    document.querySelector("#mes-commandes .carte-commande h3").textContent =
        "Commande " + c.id;

    document.querySelector("#mes-commandes .carte-commande p strong")
        .parentNode.lastChild.textContent =
        " " + (menu ? menu.nom : c.menu);

    document.querySelector("#suivi-commande .suivi-contenu h3").textContent =
        "Suivi de la commande " + c.id;

    clientElement("statut-commande").textContent = nomsEtats[c.statut];
    clientElement("statut-suivi").textContent = nomsEtats[c.statut];
    clientElement("commande-personnes").textContent = c.personnes;

    clientElement("commande-date-prestation").textContent =
        dateClient(c.date);

    clientElement("commande-date-livraison").textContent =
        dateClient(c.dateLivraison || c.date);

    clientElement("commande-heure-livraison").textContent =
        c.heureLivraison || "12:00";

    clientElement("commande-adresse-prestation").textContent =
        c.adressePrestation || "Bordeaux";

    clientElement("commande-adresse-livraison").textContent =
        c.adresseLivraison || "Bordeaux";

    clientElement("commande-zone-livraison").textContent =
        c.zoneLivraison === "hors-bordeaux" ? "Hors Bordeaux" : "Bordeaux";

    clientElement("commande-montant").textContent = prixClient(c.montant);

    const enAttente = c.statut === "attente";

    clientElement("bouton-modifier-commande").disabled = !enAttente;
    clientElement("bouton-annuler-commande").disabled = !enAttente;

    const suivi = clientElement("texte-suivi");

    if (enAttente) {
        suivi.textContent = "La commande attend sa validation.";
    } else if (c.statut === "annulee") {
        suivi.textContent = "Cette commande est annulée.";
    } else {
        suivi.textContent = "Historique de la commande :";
    }

    const liste = clientElement("historique-client-dynamique");
    liste.textContent = "";

    if (c.historique) {
        for (let i = 0; i < c.historique.length; i++) {
            const ligne = document.createElement("li");

            ligne.textContent = c.historique[i];

            liste.appendChild(ligne);
        }
    }
}

function choisirCommande() {
    for (let i = 0; i < commandesClient.length; i++) {
        if (commandesClient[i].id === listeClient.value) {
            commandeActive = commandesClient[i];
        }
    }

    modifierClient.hidden = true;
    annulerClient.hidden = true;
    messagesClient.textContent = "";

    afficherCommande();
}

listeClient.addEventListener("change", choisirCommande);
