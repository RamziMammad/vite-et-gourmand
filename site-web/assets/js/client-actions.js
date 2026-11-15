/* Vite et Gourmand - client-actions.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Client modification formulaire =====

clientElement("bouton-modifier-commande").addEventListener("click", function() {
    if (commandeActive.statut !== "attente") return;

    const c = commandeActive;

    clientElement("modif-personnes").value = c.personnes;
    clientElement("modif-date-prestation").value = c.date;

    clientElement("modif-date-livraison").value =
        c.dateLivraison || c.date;

    clientElement("modif-heure-livraison").value =
        c.heureLivraison || "12:00";

    clientElement("modif-adresse-prestation").value =
        c.adressePrestation || "Bordeaux";

    clientElement("modif-adresse-livraison").value =
        c.adresseLivraison || "Bordeaux";

    clientElement("modif-zone-livraison").value =
        c.zoneLivraison || "bordeaux";

    clientElement("modif-distance").value = c.distance || 0;

    annulerClient.hidden = true;
    modifierClient.hidden = false;
});

clientElement("bouton-retour-modification").addEventListener("click", function() {
    modifierClient.hidden = true;
});


// ===== Client modification =====

// Modification d'une commande

modifierClient.addEventListener("submit", function(event) {
    event.preventDefault();

    if (commandeActive.statut !== "attente") return;

    const c = commandeActive;
    const menu = VG.menus()[c.menu];

    const personnes = clientElement("modif-personnes").value;
    const prestation = clientElement("modif-date-prestation").value;
    const livraison = clientElement("modif-date-livraison").value;
    const heure = clientElement("modif-heure-livraison").value;
    const zone = clientElement("modif-zone-livraison").value;
    const distance = Number(clientElement("modif-distance").value);

    if (!VF.verifierPersonnes(menu, personnes)) {
        messagesClient.textContent =
            "Minimum : " + menu.minimum + " personnes.";
        return;
    }

    const erreur = VF.verifierDates(
        prestation,
        livraison,
        heure,
        menu.delaiHeures
    );

    if (erreur !== "") {
        messagesClient.textContent = erreur;
        return;
    }

    if (zone === "hors-bordeaux" && distance <= 0) {
        messagesClient.textContent = "Indiquez une distance valide.";
        return;
    }

    if (
        !clientElement("modif-adresse-prestation").value.trim() ||
        !clientElement("modif-adresse-livraison").value.trim()
    ) {
        messagesClient.textContent =
            "Renseignez les deux adresses.";
        return;
    }

    const montant = VF.calcul(menu, Number(personnes), zone, distance);

    c.personnes = Number(personnes);
    c.date = prestation;
    c.dateLivraison = livraison;
    c.heureLivraison = heure;
    c.zoneLivraison = zone;

    c.distance = zone === "bordeaux" ? 0 : distance;
    c.montant = montant.total;
    c.fraisLivraison = montant.livraison;

    c.adressePrestation =
        clientElement("modif-adresse-prestation").value.trim();

    c.adresseLivraison =
        clientElement("modif-adresse-livraison").value.trim();

    if (!c.historique) {
        c.historique = [];
    }

    c.historique.push("Modifiée par le client — " + new Date().toLocaleString("fr-FR"));

    VG.ecrire("commandes", commandesClient);

    modifierClient.hidden = true;

    afficherCommande();

    messagesClient.textContent =
        "Modification enregistrée sur cet appareil.";
});


// ===== Client annulation =====

clientElement("bouton-annuler-commande").addEventListener("click", function() {
    if (commandeActive.statut !== "attente") return;

    modifierClient.hidden = true;
    annulerClient.hidden = false;
});

clientElement("bouton-garder-commande").addEventListener("click", function() {
    annulerClient.hidden = true;
});

clientElement("bouton-confirmer-annulation").addEventListener("click", function() {
    if (commandeActive.statut !== "attente") return;

    const menus = VG.menus();

    if (commandeActive.simulation && menus[commandeActive.menu]) {
        menus[commandeActive.menu].stock =
            Number(menus[commandeActive.menu].stock) + 1;

        VG.ecrire("menus", menus);
    }

    commandeActive.statut = "annulee";

    if (!commandeActive.historique) {
        commandeActive.historique = [];
    }

    commandeActive.historique.push(
        "Annulée par le client — " + new Date().toLocaleString("fr-FR")
    );

    VG.ecrire("commandes", commandesClient);

    annulerClient.hidden = true;

    afficherCommande();

    messagesClient.textContent = "Commande annulée.";
});


// ===== Client avis =====

// Avis du client

const choixAvis = clientElement("choix-avis-commande");

// Seules les commandes terminées du client sont proposées.
for (let i = 0; i < commandesClient.length; i++) {
    const c = commandesClient[i];

    if (c.client === "camille" && c.statut === "terminee") {
        const option = document.createElement("option");

        option.value = c.id;
        option.textContent = "Commande " + c.id;

        choixAvis.appendChild(option);
    }
}

clientElement("formulaire-avis").addEventListener("submit", function(event) {
    event.preventDefault();

    const note = Number(clientElement("note-avis").value);
    const texte = clientElement("commentaire-avis").value.trim();
    const message = clientElement("message-avis");
    let commandeTrouvee = false;

    for (let i = 0; i < commandesClient.length; i++) {
        const c = commandesClient[i];
        if (c.id === choixAvis.value && c.client === "camille" &&
            c.statut === "terminee") commandeTrouvee = true;
    }

    if (!commandeTrouvee) {
        message.textContent = "Choisissez une commande terminée.";
        return;
    }
    if (!Number.isInteger(note) || note < 1 || note > 5 || texte === "") {
        message.textContent = "Indiquez une note de 1 à 5 et un commentaire.";
        return;
    }

    const avis = VG.avis();
    for (let i = 0; i < avis.length; i++) {
        if (avis[i].source === choixAvis.value) {
            message.textContent = "Vous avez déjà donné votre avis sur cette commande.";
            return;
        }
    }

    avis.push({
        note: note,
        commentaire: texte,
        source: choixAvis.value,
        statut: "attente"
    });

    VG.ecrire("avis", avis);
    message.textContent = "Avis ajouté, en attente de validation.";

    clientElement("note-affichee").textContent = note;
    clientElement("commentaire-affiche").textContent = texte;
    clientElement("apercu-avis").hidden = false;
});


// ===== Client informations =====

clientElement("formulaire-informations").addEventListener("submit", function(event) {
    event.preventDefault();

    const champs = ["nom", "prenom", "email", "telephone", "adresse"];

    for (let i = 0; i < champs.length; i++) {
        clientElement("apercu-" + champs[i]).textContent =
            clientElement(champs[i]).value;
    }

    clientElement("apercu-informations").hidden = false;
});

document.querySelector(".commande-suivante h3").textContent =
    "Exemple d'archive terminée";

if (listeClient.options.length > 0) {
    choisirCommande();
}
