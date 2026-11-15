/* Vite et Gourmand - administrateur.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Admin employes =====

/* Statistiques et gestion des employés */

function adminElement(id) {
    return document.getElementById(id);
}

let employes = VG.lire("employes", []);

function afficherEmployes() {
    const zone = adminElement("liste-employes");
    zone.textContent = "";
    if (employes.length === 0) {
        zone.textContent = "Aucun employé enregistré.";
        return;
    }
    for (let i = 0; i < employes.length; i++) {
        const ligne = document.createElement("p");
        const texte = document.createElement("span");
        texte.textContent = employes[i].email + (employes[i].actif ? " — Actif" : " — Désactivé");
        const bouton = document.createElement("button");
        bouton.type = "button";
        bouton.textContent = employes[i].actif ? "Désactiver" : "Réactiver";
        bouton.addEventListener("click", function() {
            employes[i].actif = !employes[i].actif;
            VG.ecrire("employes", employes);
            afficherEmployes();
        });
        ligne.appendChild(texte);
        ligne.appendChild(bouton);
        zone.appendChild(ligne);
    }
}

adminElement("form-employe").addEventListener("submit", function(event) {
    event.preventDefault();
    const email = adminElement("admin-email").value;
    const message = adminElement("message-admin-employe");
    for (let i = 0; i < employes.length; i++) {
        if (employes[i].email === email) {
            message.textContent = "Cette adresse est déjà utilisée.";
            return;
        }
    }
    employes.push({ email: email, actif: true });
    VG.ecrire("employes", employes);
    adminElement("admin-email").value = "";
    adminElement("admin-motdepasse").value = "";
    afficherEmployes();
    message.textContent = "Employé ajouté à la liste.";
});


// ===== Admin statistiques =====

function afficherStatistiques() {
    const menus = VG.menus();
    const commandes = VG.commandes();
    const graphique = adminElement("graphique-commandes");
    const filtre = adminElement("filtre-menu-admin");
    graphique.textContent = "";
    filtre.textContent = "";

    const tous = document.createElement("option");
    tous.value = "tous";
    tous.textContent = "Tous les menus";
    filtre.appendChild(tous);

    const comptes = {};
    let maximum = 1;
    for (const code in menus) {
        let nombre = 0;
        for (let i = 0; i < commandes.length; i++) {
            if (commandes[i].menu === code && commandes[i].statut !== "annulee") nombre++;
        }
        comptes[code] = nombre;
        if (nombre > maximum) maximum = nombre;
    }

    for (const code in menus) {
        const option = document.createElement("option");
        option.value = code;
        option.textContent = menus[code].nom;
        filtre.appendChild(option);

        const ligne = document.createElement("div");
        ligne.className = "graphique-ligne";
        const titre = document.createElement("strong");
        titre.textContent = menus[code].nom;
        const fond = document.createElement("div");
        fond.className = "barre-fond";
        const barre = document.createElement("div");
        barre.className = "barre-valeur";
        barre.style.width = (comptes[code] / maximum * 100) + "%";
        const texte = document.createElement("span");
        texte.textContent = " " + comptes[code] + " commande(s)";
        fond.appendChild(barre);
        ligne.appendChild(titre);
        ligne.appendChild(fond);
        ligne.appendChild(texte);
        graphique.appendChild(ligne);
    }
}


// ===== Admin evenements =====

function calculerMontantAdmin(event) {
    if (event) event.preventDefault();
    const menu = adminElement("filtre-menu-admin").value;
    const debut = adminElement("date-debut-admin").value;
    const fin = adminElement("date-fin-admin").value;
    const resultat = adminElement("chiffre-affaires-admin");
    if (debut !== "" && fin !== "" && debut > fin) {
        resultat.textContent = "La date de début doit précéder la date de fin.";
        return;
    }
    const commandes = VG.commandes();
    let total = 0;
    let nombre = 0;
    for (let i = 0; i < commandes.length; i++) {
        const c = commandes[i];
        if (c.statut === "annulee") continue;
        if (menu !== "tous" && c.menu !== menu) continue;
        if (debut && c.date < debut) continue;
        if (fin && c.date > fin) continue;
        total += Number(c.montant);
        nombre++;
    }
    resultat.textContent = nombre + " commande(s) ; montant prévisionnel : " +
        total.toFixed(2).replace(".", ",") + " €.";
}

adminElement("form-statistiques").addEventListener("submit", calculerMontantAdmin);

afficherEmployes();
afficherStatistiques();
calculerMontantAdmin();
