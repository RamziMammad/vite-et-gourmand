/* Messages des formulaires encore sans traitement PHP. */

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
