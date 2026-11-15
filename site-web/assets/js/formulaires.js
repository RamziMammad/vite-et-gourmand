/* Vite et Gourmand - formulaires.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Formulaires =====

/* Messages des formulaires */

function preparerFormulaire(nom, message) {
    const formulaire = document.getElementById("formulaire-" + nom);
    const zone = document.getElementById("message-" + nom);
    if (!formulaire || !zone) return;

    formulaire.addEventListener("submit", function(event) {
        event.preventDefault();
        zone.textContent = message;
    });
}

preparerFormulaire("inscription", "Le formulaire est correct. Le compte pourra être créé lorsque le serveur sera prêt.");
preparerFormulaire("connexion", "La connexion sera activée avec le serveur.");
preparerFormulaire("contact", "Votre message est prêt. Aucun e-mail n'a encore été envoyé.");
preparerFormulaire("mot-de-passe-oublie", "Le formulaire est correct. Aucun lien de réinitialisation n'a encore été envoyé.");


// Le mode démonstration sert uniquement à tester le parcours de commande.
const boutonDemo = document.getElementById("bouton-connexion-demo");

if (boutonDemo) {
    boutonDemo.addEventListener("click", function() {
        localStorage.setItem("vg_mode_demo", "oui");

        let code = "classique";
        const adresse = window.location.search;
        if (adresse.indexOf("menu=") !== -1) {
            code = adresse.split("menu=")[1].split("&")[0];
        }
        if (!VG.menus()[code]) code = "classique";

        window.location.href = "commande.html?menu=" + code;
    });
}

const boutonSortir = document.getElementById("bouton-sortir-demo");
if (boutonSortir) {
    boutonSortir.addEventListener("click", function(event) {
        event.preventDefault();
        localStorage.setItem("vg_mode_demo", "non");
        document.getElementById("message-connexion").textContent =
            "Mode démonstration quitté.";
    });
}
