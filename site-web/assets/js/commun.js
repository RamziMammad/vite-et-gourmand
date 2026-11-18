/* Le lien d'évitement est placé dans les pages HTML. */
const principal = document.getElementById("contenu-principal");
if (principal) principal.setAttribute("tabindex", "-1");

/* Affiche seulement les avis validés sur la page Accueil. */

if (document.body.getAttribute("class") === "page-accueil") {
    const zone = document.querySelector(".liste-avis");

    if (zone) {
        zone.textContent = "";

        const avis = VG.avis();

        for (let i = 0; i < avis.length; i++) {
            if (avis[i].statut !== "valide") continue;

            const carte = document.createElement("article");
            carte.className = "avis";

            const note = document.createElement("p");
            note.textContent = "Note : " + avis[i].note + "/5";

            const citation = document.createElement("blockquote");
            const commentaire = document.createElement("p");

            commentaire.textContent = avis[i].commentaire;

            citation.appendChild(commentaire);
            carte.appendChild(note);
            carte.appendChild(citation);

            zone.appendChild(carte);
        }
        if (zone.childElementCount === 0) {
            const message = document.createElement("p");
            message.textContent = "Aucun avis publié pour le moment.";
            zone.appendChild(message);
        }
    }
}

/* Affiche les horaires modifiés dans le pied de page. */

if (window.VG_DONNEES_SQL) {
    const jours = [
        "Lundi",
        "Mardi",
        "Mercredi",
        "Jeudi",
        "Vendredi",
        "Samedi",
        "Dimanche"
    ];

    const horaires = VG.horaires();

    const listes = [
        document.querySelector(".horaires-pc"),
        document.querySelector(".horaires-mobile")
    ];

    for (let j = 0; j < listes.length; j++) {
        const liste = listes[j];

        if (!liste) continue;

        liste.textContent = "";

        for (let i = 0; i < jours.length; i++) {
            const ligne = document.createElement("li");

            ligne.textContent = jours[i] + " : " + horaires[i];

            liste.appendChild(ligne);
        }
    }
}
