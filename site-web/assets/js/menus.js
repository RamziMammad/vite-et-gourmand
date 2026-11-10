
const champPrixMax = document.getElementById("prix-max");
const champPrixMin = document.getElementById("prix-min");
const champFourchetteMax = document.getElementById("prix-fourchette-max");
const champTheme = document.getElementById("theme");
const champRegime = document.getElementById("regime");
const champPersonnes = document.getElementById("personnes");

// Les deux menus présents dans le HTML
const menuClassique = document.getElementById("menu-classique");
const menuVegetarien = document.getElementById("menu-vegetarien");

const messageAucunMenu = document.getElementById("aucun-menu");
const formulaireFiltres = document.getElementById("formulaire-filtres");

// Vérifier si un menu correspond aux filtres
function verifierMenu(carte, prixMenu, themeMenu, regimeMenu, minimumMenu) {
    let afficher = true;

    if (champPrixMax.value !== "" && prixMenu > Number(champPrixMax.value)) {
        afficher = false;
    }

    if (champPrixMin.value !== "" && prixMenu < Number(champPrixMin.value)) {
        afficher = false;
    }

    if (champFourchetteMax.value !== "" && prixMenu > Number(champFourchetteMax.value)) {
        afficher = false;
    }

    if (champTheme.value !== "" && champTheme.value !== themeMenu) {
        afficher = false;
    }

    if (champRegime.value !== "" && champRegime.value !== regimeMenu) {
        afficher = false;
    }

    if (champPersonnes.value !== "" && Number(champPersonnes.value) < minimumMenu) {
        afficher = false;
    }

    if (afficher) {
        carte.classList.remove("menu-cache");
    } else {
        carte.classList.add("menu-cache");
    }

    return afficher;
}

// Vérifier les deux menus
function filtrerMenus() {
    const afficherClassique = verifierMenu(
        menuClassique, 250, "classique", "classique", 10
    );

    const afficherVegetarien = verifierMenu(
        menuVegetarien, 200, "classique", "vegetarien", 8
    );

    if (afficherClassique === false && afficherVegetarien === false) {
        messageAucunMenu.classList.remove("menu-cache");
    } else {
        messageAucunMenu.classList.add("menu-cache");
    }
}

// Réagir aux changements des filtres
champPrixMax.addEventListener("input", filtrerMenus);
champPrixMin.addEventListener("input", filtrerMenus);
champFourchetteMax.addEventListener("input", filtrerMenus);
champTheme.addEventListener("change", filtrerMenus);
champRegime.addEventListener("change", filtrerMenus);
champPersonnes.addEventListener("input", filtrerMenus);

// Empêcher le rechargement du formulaire
formulaireFiltres.addEventListener("submit", function(evenement) {
    evenement.preventDefault();
});

// Afficher les menus au chargement
filtrerMenus();
