/* Vite et Gourmand - detail-menu.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Detail informations =====

/* Remplit la présentation d'un menu à partir des données. */
function afficherInformationsDetail(menu, code) {
    document.title = menu.nom + " - Vite & Gourmand";
    texteMenu(".introduction-detail h1", menu.nom);
    texteMenu(".introduction-detail p", menu.description);

    const photo = document.querySelector(".photo-principale img");
    photo.src = menu.photo;
    photo.alt = menu.nom;

    ajouterTexteAvecTitre(document.querySelector(".theme-menu"),
        "Thème", titreTheme(menu.theme));
    ajouterTexteAvecTitre(document.querySelector(".regime-menu"),
        "Régime", titreRegime(menu.regime));
    ajouterTexteAvecTitre(document.querySelector(".minimum-personnes span"),
        "Minimum", menu.minimum + " personnes");
    ajouterTexteAvecTitre(document.querySelector(".stock-menu span"),
        "Stock", menu.stock + " commandes");

    iconPersonnes(document.querySelector(".minimum-personnes img"), menu.minimum);
    iconPersonnes(document.getElementById("icone-condition-minimum"), menu.minimum);

    texteMenu(".montant-menu strong", menu.prix + " €");
    texteMenu(".precision-prix", "Prix pour " + menu.minimum + " personnes");
    document.getElementById("condition-minimum").textContent =
        "Minimum : " + menu.minimum + " personnes.";

    texteMenu(".liste-conditions li span",
        "Réservation : au moins " + menu.delaiHeures + " h à l'avance.");
    texteMenu(".condition-complementaire span", menu.conditions);
}


// ===== Detail composition =====

/* Affiche l'entrée, le plat et le dessert du menu. */
function afficherCompositionDetail(menu) {
    const zone = document.querySelector(".composition-menu");
    zone.textContent = "";

    const titre = document.createElement("h2");
    titre.textContent = "Composition du menu";
    zone.appendChild(titre);

    const types = ["entree", "plat", "dessert"];
    const titres = ["Entrée", "Plat", "Dessert"];
    const plats = VG.plats();

    for (let i = 0; i < types.length; i++) {
        let platChoisi = null;

        for (let j = 0; j < plats.length; j++) {
            if (menu.plats && plats[j].id === menu.plats[types[i]] &&
                plats[j].type === titres[i]) {
                platChoisi = plats[j];
            }
        }

        if (!platChoisi) {
            const erreur = document.createElement("p");
            erreur.textContent = titres[i] + " : plat indisponible.";
            zone.appendChild(erreur);
            continue;
        }

        const bloc = document.createElement("div");
        bloc.className = i === 2 ? "plat-menu dernier-plat" : "plat-menu";

        const titrePlat = document.createElement("h3");
        titrePlat.textContent = titres[i];
        const nom = document.createElement("p");
        nom.textContent = platChoisi.nom;
        const allergenes = document.createElement("p");
        allergenes.textContent = "Allergènes : " +
            (platChoisi.allergenes || "non renseignés");

        bloc.appendChild(titrePlat);
        bloc.appendChild(nom);
        bloc.appendChild(allergenes);
        zone.appendChild(bloc);
    }
}


// ===== Detail galerie =====

/* Trois photos fixes de la galerie du menu sélectionné. */
function afficherGalerieDetail(menu) {
    const galerie = document.querySelector(".galerie-photos");
    galerie.textContent = "";

    if (!menu.galerie) return;

    for (let i = 0; i < menu.galerie.length; i++) {
        const photo = document.createElement("img");
        photo.src = menu.galerie[i];
        photo.alt = "Photo " + (i + 1) + " du " + menu.nom;
        galerie.appendChild(photo);
    }
}


// ===== Detail menu =====

/* Choisit le menu demandé dans l'adresse de la page. */
let codeDetail = "classique";
const adresseDetail = window.location.search;

if (adresseDetail.indexOf("menu=") !== -1) {
    codeDetail = adresseDetail.split("menu=")[1].split("&")[0];
}

const menuDetail = VG.menus()[codeDetail];
const boutonDetail = document.querySelector(".bouton-commander");

if (!menuDetail) {
    texteMenu(".introduction-detail h1", "Menu introuvable");
    texteMenu(".introduction-detail p", "Ce menu n'est plus proposé.");
    document.querySelector(".presentation-detail").hidden = true;
    document.querySelector(".composition-et-conditions").hidden = true;
    document.querySelector(".autres-photos").hidden = true;
    boutonDetail.removeAttribute("href");
} else {
    afficherInformationsDetail(menuDetail, codeDetail);
    afficherCompositionDetail(menuDetail);
    afficherGalerieDetail(menuDetail);

    if (VG.menuDisponible(codeDetail)) {
        if (localStorage.getItem("vg_mode_demo") === "oui") {
            boutonDetail.href = "commande.html?menu=" + codeDetail;
            boutonDetail.textContent = "Commander";
        } else {
            boutonDetail.href = "connexion.html?menu=" + codeDetail;
            boutonDetail.textContent = "Se connecter pour commander";
        }
        boutonDetail.removeAttribute("aria-disabled");
    } else {
        boutonDetail.removeAttribute("href");
        boutonDetail.textContent = "Menu indisponible";
        boutonDetail.setAttribute("aria-disabled", "true");
    }
}
