/* Vite et Gourmand - affichage.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Affichage commun =====

/* Réunit les petites fonctions utilisées sur plusieurs pages. */

const menusDemo = VG.menus();

function texteMenu(nom, valeur) {
    const element = document.querySelector(nom);

    if (element) element.textContent = valeur;
}

function ajouterTexteAvecTitre(element, titre, valeur) {
    if (!element) return;

    element.textContent = "";

    const gras = document.createElement("strong");
    gras.textContent = titre + " : ";

    element.appendChild(gras);

    const texte = document.createElement("span");
    texte.textContent = String(valeur);

    element.appendChild(texte);
}

function iconPersonnes(image, minimum) {
    if (!image) return;

    image.style.display = "";

    if (Number(minimum) === 8) {
        image.src = "assets/images/menus/Icône de groupe avec badge 8+.png";
    } else if (Number(minimum) === 10) {
        image.src = "assets/images/menus/Icône d’équipe avec badge 10+.png";
    } else {
        image.style.display = "none";
    }
}

function titreTheme(theme) {
    const noms = {
        classique: "Classique",
        noel: "Noël",
        paques: "Pâques",
        evenement: "Événement"
    };

    return noms[theme] || theme;
}

function titreRegime(regime) {
    const noms = {
        classique: "Classique",
        vegetarien: "Végétarien",
        vegan: "Végan"
    };

    return noms[regime] || regime;
}
