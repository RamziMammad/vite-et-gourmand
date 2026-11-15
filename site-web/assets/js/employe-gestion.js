/* Vite et Gourmand - employe-gestion.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Employe gestion menus =====

/* Gestion des menus, plats et horaires */

function contenuElement(id) { return document.getElementById(id); }

let gestionMenus = VG.menus();
let gestionPlats = VG.plats();
let gestionHoraires = VG.horaires();
let gestionAvis = VG.avis();

const listeMenus = contenuElement("gestion-menu");
listeMenus.textContent = "";
for (const code in gestionMenus) {
    const option = document.createElement("option");
    option.value = code;
    option.textContent = gestionMenus[code].nom;
    listeMenus.appendChild(option);
}

function chargerMenu() {
    const m = gestionMenus[listeMenus.value];
    if (!m) return;
    contenuElement("gestion-menu-nom").value = m.nom;
    contenuElement("gestion-menu-description").value = m.description;
    contenuElement("gestion-menu-theme").value = m.theme;
    contenuElement("gestion-menu-regime").value = m.regime;
    contenuElement("gestion-menu-min").value = m.minimum;
    contenuElement("gestion-menu-prix").value = m.prix;
    contenuElement("gestion-menu-stock").value = m.stock;
    contenuElement("gestion-menu-conditions").value = m.conditions;
    contenuElement("gestion-menu-delai").value = m.delaiHeures || 72;
    contenuElement("desactiver-menu").textContent = m.actif ? "Retirer le menu" : "Rétablir le menu";
}
listeMenus.addEventListener("change", chargerMenu);

contenuElement("form-gestion-menu").addEventListener("submit", function(event) {
    event.preventDefault();
    const m = gestionMenus[listeMenus.value];
    const minimum = Number(contenuElement("gestion-menu-min").value);
    const prix = Number(contenuElement("gestion-menu-prix").value);
    const stock = Number(contenuElement("gestion-menu-stock").value);
    const delai = Number(contenuElement("gestion-menu-delai").value);
    if (!Number.isInteger(minimum) || minimum < 1 || prix <= 0 ||
        !Number.isInteger(stock) || stock < 0 || !Number.isInteger(delai) || delai < 1) {
        contenuElement("message-gestion-menu").textContent = "Vérifiez le minimum, le prix, le stock et le délai.";
        return;
    }
    m.nom = contenuElement("gestion-menu-nom").value;
    m.description = contenuElement("gestion-menu-description").value;
    m.theme = contenuElement("gestion-menu-theme").value;
    m.regime = contenuElement("gestion-menu-regime").value;
    m.minimum = minimum;
    m.prix = prix;
    m.stock = stock;
    m.delaiHeures = delai;
    m.conditions = contenuElement("gestion-menu-conditions").value;
    VG.ecrire("menus", gestionMenus);
    contenuElement("message-gestion-menu").textContent = "Menu enregistré.";
});

contenuElement("desactiver-menu").addEventListener("click", function() {
    const m = gestionMenus[listeMenus.value];
    m.actif = !m.actif;
    VG.ecrire("menus", gestionMenus);
    chargerMenu();
});


// ===== Employe gestion plats =====

function remplirPlatsEmploye() {
    const liste = contenuElement("gestion-plat");
    liste.textContent = "";
    for (let i = 0; i < gestionPlats.length; i++) {
        const option = document.createElement("option");
        option.value = i;
        option.textContent = gestionPlats[i].nom;
        liste.appendChild(option);
    }
    chargerPlat();
}

function chargerPlat() {
    const p = gestionPlats[Number(contenuElement("gestion-plat").value)];
    if (!p) return;
    contenuElement("gestion-plat-nom").value = p.nom;
    contenuElement("gestion-plat-type").value = p.type;
    contenuElement("gestion-plat-allergenes").value = p.allergenes;
}

contenuElement("gestion-plat").addEventListener("change", chargerPlat);

contenuElement("form-gestion-plat").addEventListener("submit", function(event) {
    event.preventDefault();
    const p = gestionPlats[Number(contenuElement("gestion-plat").value)];
    if (!p) return;
    p.nom = contenuElement("gestion-plat-nom").value;
    p.type = contenuElement("gestion-plat-type").value;
    p.allergenes = contenuElement("gestion-plat-allergenes").value;
    VG.ecrire("plats", gestionPlats);
    remplirPlatsEmploye();
    contenuElement("message-gestion-plat").textContent = "Plat enregistré.";
});

contenuElement("supprimer-plat").addEventListener("click", function() {
    if (!confirm("Supprimer ce plat ?")) return;
    gestionPlats.splice(Number(contenuElement("gestion-plat").value), 1);
    VG.ecrire("plats", gestionPlats);
    remplirPlatsEmploye();
    contenuElement("message-gestion-plat").textContent = "Plat supprimé. Vérifiez les menus concernés.";
});


// ===== Employe horaires =====

function chargerHoraire() {
    contenuElement("gestion-horaire").value = gestionHoraires[Number(contenuElement("gestion-jour").value)];
}

contenuElement("gestion-jour").addEventListener("change", chargerHoraire);

contenuElement("form-horaires").addEventListener("submit", function(event) {
    event.preventDefault();
    gestionHoraires[Number(contenuElement("gestion-jour").value)] = contenuElement("gestion-horaire").value;
    VG.ecrire("horaires", gestionHoraires);
    contenuElement("message-horaires").textContent = "Horaires enregistrés.";
});


// ===== Employe avis =====

function afficherAvis() {
    const zone = contenuElement("liste-gestion-avis");
    zone.textContent = "";
    for (let i = 0; i < gestionAvis.length; i++) {
        const article = document.createElement("article");
        article.className = "fiche-commande";
        const texte = document.createElement("p");
        texte.textContent = gestionAvis[i].note + "/5 — " + gestionAvis[i].commentaire + " (" + gestionAvis[i].statut + ")";
        article.appendChild(texte);
        const etats = ["valide", "refuse"];
        const boutons = ["Valider", "Refuser"];
        for (let j = 0; j < 2; j++) {
            const bouton = document.createElement("button");
            bouton.type = "button";
            bouton.textContent = boutons[j];
            bouton.addEventListener("click", function() {
                gestionAvis[i].statut = etats[j];
                VG.ecrire("avis", gestionAvis);
                afficherAvis();
            });
            article.appendChild(bouton);
        }
        zone.appendChild(article);
    }
}


// ===== Employe modifier =====

const listeCommandes = contenuElement("modifier-commande-id");

function chargerCommandeEmploye() {
    const c = trouverCommande(listeCommandes.value);
    if (!c) return;
    contenuElement("modifier-commande-personnes").value = c.personnes;
    contenuElement("modifier-commande-date").value = c.date;
}

listeCommandes.addEventListener("change", chargerCommandeEmploye);

contenuElement("form-modifier-commande-employe").addEventListener("submit", function(event) {
    event.preventDefault();
    const c = trouverCommande(listeCommandes.value);
    const message = contenuElement("message-modifier-commande");
    if (!c || c.statut === "annulee" || c.statut === "terminee") return;
    if (!contenuElement("modifier-contact-confirmation").checked ||
        !contenuElement("modifier-contact-mode").value ||
        !contenuElement("modifier-commande-motif").value.trim()) {
        message.textContent = "Indiquez le mode de contact et le motif.";
        return;
    }
    const menu = VG.menus()[c.menu];
    const personnes = Number(contenuElement("modifier-commande-personnes").value);
    const prestation = contenuElement("modifier-commande-date").value;
    if (!VF.verifierPersonnes(menu, personnes)) {
        message.textContent = "Minimum : " + menu.minimum + " personnes.";
        return;
    }
    const dates = VF.verifierDates(prestation, c.dateLivraison || prestation,
        c.heureLivraison || "12:00", menu.delaiHeures);
    if (dates) { message.textContent = dates; return; }
    const montant = VF.calcul(menu, personnes, c.zoneLivraison || "bordeaux", c.distance || 0);
    c.personnes = personnes;
    c.date = prestation;
    c.montant = montant.total;
    ajouterHistorique(c, "Modification employé après contact : " + contenuElement("modifier-commande-motif").value);
    message.textContent = "Commande modifiée.";
});

chargerMenu();
remplirPlatsEmploye();
chargerHoraire();
afficherAvis();
chargerCommandeEmploye();
