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

preparerFormulaire("contact", "L’envoi de messages est temporairement indisponible.");
preparerFormulaire("mot-de-passe-oublie", "La réinitialisation du mot de passe est temporairement indisponible.");
