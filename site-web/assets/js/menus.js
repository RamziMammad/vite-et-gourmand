
/* Filtre les cartes selon les critères du visiteur. */

const champPrixMax = document.getElementById("prix-max");
const champPrixMin = document.getElementById("prix-min");
const champFourchetteMax = document.getElementById("prix-fourchette-max");
const champTheme = document.getElementById("theme");
const champRegime = document.getElementById("regime");
const champPersonnes = document.getElementById("personnes");
const messageAucunMenu = document.getElementById("aucun-menu");
const messageResultats = document.getElementById("resultats-accessibles");
const formulaireFiltres = document.getElementById("formulaire-filtres");

function filtrerMenus() {
    const donnees = VG.menus();
    let nombre = 0;
    let disponibles = 0;

    for (const code in donnees) {
        const menu = donnees[code];
        const carte = document.getElementById("menu-" + code);

        if (!carte) continue;

        const disponible = VG.menuDisponible(code);

        if (disponible) disponibles++;

        let afficher = disponible;

        if (
            champPrixMax.value !== "" &&
            menu.prix > Number(champPrixMax.value)
        ) {
            afficher = false;
        }

        if (
            champPrixMin.value !== "" &&
            menu.prix < Number(champPrixMin.value)
        ) {
            afficher = false;
        }

        if (
            champFourchetteMax.value !== "" &&
            menu.prix > Number(champFourchetteMax.value)
        ) {
            afficher = false;
        }

        if (
            champTheme.value !== "" &&
            menu.theme !== champTheme.value
        ) {
            afficher = false;
        }

        if (
            champRegime.value !== "" &&
            menu.regime !== champRegime.value
        ) {
            afficher = false;
        }

        if (
            champPersonnes.value !== "" &&
            Number(champPersonnes.value) < menu.minimum
        ) {
            afficher = false;
        }

        if (afficher) {
            carte.classList.remove("menu-cache");
        } else {
            carte.classList.add("menu-cache");
        }

        if (afficher) nombre++;
    }

    if (disponibles === 0) {
        messageAucunMenu.textContent =
            "Aucun menu disponible pour le moment.";
    } else {
        messageAucunMenu.textContent =
            "Aucun menu ne correspond à votre recherche.";
    }

    if (nombre === 1) {
        messageResultats.textContent = "1 menu affiché.";
    } else {
        messageResultats.textContent = nombre + " menus affichés.";
    }

    if (nombre > 0) {
        messageAucunMenu.classList.add("menu-cache");
    } else {
        messageAucunMenu.classList.remove("menu-cache");
    }
}

/* Construit les cartes et leurs liens dans Tous les menus. */

if (document.body.getAttribute("class") === "page-menus") {
    const zone = document.querySelector(".cartes-menus");
    zone.textContent = "";

    for (const code in menusCatalogue) {
        const menu = menusCatalogue[code];

        let carte = document.getElementById("menu-" + code);

        if (!carte) {
            carte = document.createElement("article");

            carte.id = "menu-" + code;
            carte.className = "carte-menu";

            carte.innerHTML =
                '<img class="photo-menu" alt="">' +
                '<h3></h3>' +
                '<p class="description-menu"></p>' +
                '<div class="informations-menu">' +
                    '<p><img alt=""><span></span></p>' +
                    '<p class="ligne-prix">' +
                        '<img src="assets/images/menus/Étiquette dorée avec symbole euro.png" alt="">' +
                        '<span>' +
                            '<span class="prix-libelle">Prix : </span>' +
                            '<strong></strong>' +
                        '</span>' +
                    '</p>' +
                '</div>' +
                '<a class="bouton-menu">Voir le menu</a>';

            zone.appendChild(carte);
        }

        carte.querySelector("h3").textContent = menu.nom;

        carte.querySelector(".description-menu").textContent =
            menu.description;

        carte.querySelector(".informations-menu p span").textContent =
            "Minimum : " + menu.minimum + " personnes";

        carte.querySelector(".ligne-prix strong").textContent =
            menu.prix + " €";

        carte.querySelector(".photo-menu").src = menu.photo;

        carte.querySelector(".photo-menu").alt =
            "Présentation du " + menu.nom;

        iconPersonnes(
            carte.querySelector(".informations-menu p img"),
            menu.minimum
        );

        carte.querySelector(".bouton-menu").href =
            "detail-menu.php?menu=" + code;

        carte.querySelector(".bouton-menu").setAttribute(
            "aria-label",
            "Voir le menu " + menu.nom
        );
    }

    filtrerMenus();
}

/* Met à jour les cartes quand un filtre change. */

champPrixMax.addEventListener("input", filtrerMenus);
champPrixMin.addEventListener("input", filtrerMenus);
champFourchetteMax.addEventListener("input", filtrerMenus);
champTheme.addEventListener("change", filtrerMenus);
champRegime.addEventListener("change", filtrerMenus);
champPersonnes.addEventListener("input", filtrerMenus);

formulaireFiltres.addEventListener("submit", function(event) {
    event.preventDefault();
});

filtrerMenus();
