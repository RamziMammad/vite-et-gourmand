/* Vite et Gourmand - employe-commandes.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Employe base =====

/* Gestion des commandes par l’employé */

const commandes = VG.commandes();
const nomsStatuts = {
    attente: "En attente", acceptee: "Acceptée", preparation: "En préparation",
    livraison: "En cours de livraison", livree: "Livrée",
    materiel: "Retour du matériel attendu", terminee: "Terminée", annulee: "Annulée"
};

const suiteStatuts = {
    attente: "acceptee", acceptee: "preparation", preparation: "livraison",
    livraison: "livree", livree: "terminee", materiel: "terminee"
};

function employeElement(id) {
    return document.getElementById(id);
}

function trouverCommande(id) {
    for (let i = 0; i < commandes.length; i++) {
        if (commandes[i].id === id) return commandes[i];
    }
    return null;
}

function ajouterHistorique(commande, message) {
    if (!commande.historique) commande.historique = [];
    commande.historique.push(message + " — " + new Date().toLocaleString("fr-FR"));
    VG.ecrire("commandes", commandes);
    afficherFiche(commande);
    filtrerCommandes();
}

function ajouterLigneFiche(fiche, titre, texte, id) {
    const ligne = document.createElement("p");
    const etiquette = document.createElement("strong");
    etiquette.textContent = titre + " : ";
    const valeur = document.createElement("span");
    valeur.textContent = texte;
    if (id) valeur.id = id;
    ligne.appendChild(etiquette);
    ligne.appendChild(valeur);
    fiche.appendChild(ligne);
}

function ajouterChoix(selectId, commande) {
    const option = document.createElement("option");
    option.value = commande.id;
    option.textContent = "Commande " + commande.id;
    employeElement(selectId).appendChild(option);
}


// ===== Employe commandes =====

for (let i = 0; i < commandes.length; i++) {
    const c = commandes[i];
    if (!employeElement(c.id)) {
        const fiche = document.createElement("article");
        fiche.className = "fiche-commande";
        fiche.id = c.id;

        const titre = document.createElement("h3");
        titre.textContent = "Commande " + c.id;
        fiche.appendChild(titre);
        const etat = document.createElement("strong");
        etat.id = "etat-" + c.id;
        fiche.appendChild(etat);

        ajouterLigneFiche(fiche, "Client", c.client);
        ajouterLigneFiche(fiche, "Menu", c.menu);
        ajouterLigneFiche(fiche, "Personnes", "", "personnes-" + c.id);
        ajouterLigneFiche(fiche, "Prestation", "", "prestation-" + c.id);
        ajouterLigneFiche(fiche, "Montant", "", "montant-" + c.id);
        const hist = document.createElement("ul");
        hist.id = "historique-" + c.id;
        fiche.appendChild(hist);
        employeElement("commandes").appendChild(fiche);

        ajouterChoix("statut-commande", c);
        ajouterChoix("annulation-commande", c);
        ajouterChoix("modifier-commande-id", c);
    }

    const filtre = employeElement("filtre-client");
    let existe = false;
    for (let j = 0; j < filtre.options.length; j++) {
        if (filtre.options[j].value === c.client) existe = true;
    }
    if (!existe) {
        const option = document.createElement("option");
        option.value = c.client;
        option.textContent = c.client;
        filtre.appendChild(option);
    }
}

// Affichage des commandes
function afficherFiche(c) {
    employeElement("etat-" + c.id).textContent = nomsStatuts[c.statut];
    employeElement("personnes-" + c.id).textContent = c.personnes;
    employeElement("montant-" + c.id).textContent = Number(c.montant).toFixed(2).replace(".", ",") + " €";
    employeElement("prestation-" + c.id).textContent = new Date(c.date + "T12:00:00").toLocaleDateString("fr-FR");
    const zone = employeElement("historique-" + c.id);
    zone.textContent = "";
    const historique = c.historique || ["En attente"];
    for (let i = 0; i < historique.length; i++) {
        const ligne = document.createElement("li");
        ligne.textContent = historique[i];
        zone.appendChild(ligne);
    }
}

function filtrerCommandes() {
    const statut = employeElement("filtre-statut").value;
    const client = employeElement("filtre-client").value;
    let nombre = 0;
    for (let i = 0; i < commandes.length; i++) {
        const c = commandes[i];
        const visible = (statut === "tous" || statut === c.statut) &&
            (client === "tous" || client === c.client);
        employeElement(c.id).classList.toggle("masque", !visible);
        if (visible) nombre++;
    }
    employeElement("nombre-resultats").textContent = nombre + " commande(s) affichée(s)";
}

employeElement("filtre-statut").addEventListener("change", filtrerCommandes);
employeElement("filtre-client").addEventListener("change", filtrerCommandes);


// ===== Employe statut =====

// Changement des statuts
employeElement("formulaire-statut").addEventListener("submit", function(event) {
    event.preventDefault();
    const c = trouverCommande(employeElement("statut-commande").value);
    const statut = employeElement("nouveau-statut").value;
    const message = employeElement("message-statut");
    if (!c) return;
    let autorise = suiteStatuts[c.statut] === statut;
    if (c.statut === "livree" && statut === "materiel") autorise = true;
    if (!autorise) {
        message.textContent = "Ce changement de statut n'est pas autorisé.";
        return;
    }
    c.statut = statut;
    ajouterHistorique(c, nomsStatuts[statut]);
    message.textContent = "Statut mis à jour.";
});


// ===== Employe annulation =====

employeElement("formulaire-annulation").addEventListener("submit", function(event) {
    event.preventDefault();
    const c = trouverCommande(employeElement("annulation-commande").value);
    const message = employeElement("message-annulation");
    if (!c) return;
    if (c.statut === "annulee" || c.statut === "terminee" || c.statut === "livree") {
        message.textContent = "Cette commande ne peut plus être annulée.";
        return;
    }
    if (!employeElement("contact-effectue").checked ||
        employeElement("mode-contact").value === "" ||
        employeElement("motif-annulation").value.trim() === "") {
        message.textContent = "Confirmez le contact et indiquez le motif.";
        return;
    }
    const menus = VG.menus();
    if (c.simulation && menus[c.menu]) {
        menus[c.menu].stock = Number(menus[c.menu].stock) + 1;
        VG.ecrire("menus", menus);
    }
    c.statut = "annulee";
    ajouterHistorique(c, "Annulation après contact : " + employeElement("motif-annulation").value);
    message.textContent = "Commande annulée.";
});

for (let i = 0; i < commandes.length; i++) afficherFiche(commandes[i]);
filtrerCommandes();
