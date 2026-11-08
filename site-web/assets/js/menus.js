
const champPrixMax = document.getElementById("prix-max");
const champPrixMin = document.getElementById("prix-min");
const champFourchetteMax = document.getElementById("prix-fourchette-max");

const champTheme = document.getElementById("theme");
const champRegime = document.getElementById("regime");
const champPersonnes = document.getElementById("personnes");

const menuClassique = document.getElementById("menu-classique");
const menuVegetarien = document.getElementById("menu-vegetarien");
const messageAucunMenu = document.getElementById("aucun-menu");
const formulaireFiltres = document.getElementById("formulaire-filtres");

// Vérifier si un menu correspond aux filtres
function verifierMenu(carte, prixMenu, themeMenu, regimeMenu, minimumMenu) {

    let afficher = true;

    // Prix maximum
    if (champPrixMax.value !== "" && prixMenu > Number(champPrixMax.value)) {
        afficher = false;
    }

    // Prix minimum de la fourchette
    if (champPrixMin.value !== "" && prixMenu < Number(champPrixMin.value)) {
        afficher = false;
    }

    // Prix maximum de la fourchette
    if (champFourchetteMax.value !== "" && prixMenu > Number(champFourchetteMax.value)) {
        afficher = false;
    }

    // Thème
    if (champTheme.value !== "" && champTheme.value !== themeMenu) {
        afficher = false;
    }

    // Régime alimentaire
    if (champRegime.value !== "" && champRegime.value !== regimeMenu) {
        afficher = false;
    }

    // Nombre de personnes
    if (champPersonnes.value !== "" && Number(champPersonnes.value) < minimumMenu) {
        afficher = false;
    }

    // Afficher ou masquer la carte
    if (afficher) {
        carte.classList.remove("menu-cache");
    } else {
        carte.classList.add("menu-cache");
    }

    return afficher;
}

// Appliquer les filtres aux deux menus
function filtrerMenus() {

    const afficherClassique = verifierMenu(
        menuClassique, 250, "classique", "classique", 10
    );

    const afficherVegetarien = verifierMenu(
        menuVegetarien, 200, "classique", "vegetarien", 8
    );

    // Message lorsqu'aucun menu ne correspond
    if (afficherClassique === false && afficherVegetarien === false) {
        messageAucunMenu.classList.remove("menu-cache");
    } else {
        messageAucunMenu.classList.add("menu-cache");
    }
}

// Réagir aux changements des champs de prix
champPrixMax.addEventListener("input", filtrerMenus);
champPrixMin.addEventListener("input", filtrerMenus);
champFourchetteMax.addEventListener("input", filtrerMenus);

// Réagir au choix du thème et du régime
champTheme.addEventListener("change", filtrerMenus);
champRegime.addEventListener("change", filtrerMenus);

// Réagir au nombre de personnes
champPersonnes.addEventListener("input", filtrerMenus);

// Éviter le rechargement lors de la validation du formulaire
formulaireFiltres.addEventListener("submit", function(evenement) {
    evenement.preventDefault();
});

// Afficher les bons menus au chargement de la page
filtrerMenus();
