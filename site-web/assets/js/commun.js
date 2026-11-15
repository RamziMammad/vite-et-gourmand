/* Vite et Gourmand - commun.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Accessibilite =====

/* Le lien d'évitement est placé dans les pages HTML. */
const principal = document.getElementById("contenu-principal");
if (principal) principal.setAttribute("tabindex", "-1");


// ===== Avis accueil =====

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
    }
}


// ===== Horaires =====

/* Affiche les horaires modifiés dans le pied de page. */

if (localStorage.getItem("vg_horaires") !== null) {
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


// ===== Lien catalogue =====

// Lien vers le catalogue

if (
    document.body.classList.contains("page-espace-employe") ||
    document.body.classList.contains("page-administration")
) {
    if (
        window.location.pathname.indexOf("gestion-catalogue.html") === -1
    ) {
        let zone = document.getElementById("menus");

        if (!zone) {
            zone = document.querySelector(".bloc-administration");
        }

        if (zone) {
            const lien = document.createElement("a");

            lien.href = "gestion-catalogue.html";
            lien.textContent = "Gérer le catalogue et les photos";

            const paragraphe = document.createElement("p");

            paragraphe.appendChild(lien);
            zone.appendChild(paragraphe);
        }
    }
}
