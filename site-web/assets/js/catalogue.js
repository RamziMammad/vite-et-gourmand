/* Vite et Gourmand - catalogue.js
   Les parties de ce fichier sont regroupées par fonctionnalité. */


// ===== Catalogue base =====

/* Création et modification des menus */

function catalogueElement(id) {
    return document.getElementById(id);
}

let catalogue = VG.menus();
const choixCatalogue = catalogueElement("catalogue-choix");
const messageCatalogue = catalogueElement("catalogue-message");

function remplirOptions(id, choix) {
    const liste = catalogueElement(id);
    liste.textContent = "";
    for (let i = 0; i < choix.length; i++) {
        const option = document.createElement("option");
        option.value = choix[i].valeur;
        option.textContent = choix[i].texte;
        liste.appendChild(option);
    }
}

function remplirPhotos() {
    const images = VG.images();
    const choix = [];
    const choixAvecVide = [{ valeur: "", texte: "Aucune image" }];
    for (let i = 0; i < images.length; i++) {
        const ligne = { valeur: images[i], texte: images[i].split("/").pop() };
        choix.push(ligne);
        choixAvecVide.push(ligne);
    }
    remplirOptions("catalogue-photo", choix);
    for (let i = 1; i <= 3; i++) remplirOptions("catalogue-image-" + i, choixAvecVide);
}

function remplirListePlats() {
    const types = ["Entrée", "Plat", "Dessert"];
    const ids = ["catalogue-entree", "catalogue-plat", "catalogue-dessert"];
    const plats = VG.plats();
    for (let i = 0; i < types.length; i++) {
        const choix = [{ valeur: "", texte: "Choisir " + types[i] }];
        for (let j = 0; j < plats.length; j++) {
            if (plats[j].type === types[i]) {
                choix.push({ valeur: plats[j].id, texte: plats[j].nom });
            }
        }
        const ancien = catalogueElement(ids[i]).value;
        remplirOptions(ids[i], choix);
        catalogueElement(ids[i]).value = ancien;
    }
}


// ===== Catalogue affichage =====

// Liste des menus
function remplirListeMenus() {
    const ancien = choixCatalogue.value;
    const choix = [{ valeur: "nouveau", texte: "+ Créer un menu" }];
    for (const code in catalogue) choix.push({ valeur: code, texte: catalogue[code].nom });
    remplirOptions("catalogue-choix", choix);
    if (catalogue[ancien]) choixCatalogue.value = ancien;
    chargerMenuCatalogue();
}

function chargerMenuCatalogue() {
    let menu = catalogue[choixCatalogue.value];
    if (!menu) {
        menu = {
            nom: "", description: "", theme: "classique", regime: "classique",
            minimum: 8, prix: 200, stock: 5, delaiHeures: 72,
            conditions: "Réservation au moins 72 h à l'avance.",
            photo: VG.images()[0], galerie: [], actif: true,
            plats: { entree: "", plat: "", dessert: "" }
        };
    }

    catalogueElement("catalogue-nom").value = menu.nom;
    catalogueElement("catalogue-description").value = menu.description;
    catalogueElement("catalogue-theme").value = menu.theme;
    catalogueElement("catalogue-regime").value = menu.regime;
    catalogueElement("catalogue-minimum").value = menu.minimum;
    catalogueElement("catalogue-prix").value = menu.prix;
    catalogueElement("catalogue-stock").value = menu.stock;
    catalogueElement("catalogue-delai").value = menu.delaiHeures || 72;
    catalogueElement("catalogue-conditions").value = menu.conditions;
    catalogueElement("catalogue-photo").value = menu.photo;
    catalogueElement("catalogue-entree").value = menu.plats.entree;
    catalogueElement("catalogue-plat").value = menu.plats.plat;
    catalogueElement("catalogue-dessert").value = menu.plats.dessert;
    for (let i = 1; i <= 3; i++) {
        const image = menu.galerie[i - 1] || "";
        catalogueElement("catalogue-image-" + i).value = image;
    }
    catalogueElement("catalogue-actif").checked = menu.actif;
    catalogueElement("catalogue-retirer").disabled = !catalogue[choixCatalogue.value];
    messageCatalogue.textContent = "";
}

choixCatalogue.addEventListener("change", chargerMenuCatalogue);


// ===== Catalogue enregistrement =====

catalogueElement("catalogue-formulaire").addEventListener("submit", function(event) {
    event.preventDefault();
    const nom = catalogueElement("catalogue-nom").value.trim();
    const description = catalogueElement("catalogue-description").value.trim();
    const minimum = Number(catalogueElement("catalogue-minimum").value);
    const prix = Number(catalogueElement("catalogue-prix").value);
    const stock = Number(catalogueElement("catalogue-stock").value);
    const delai = Number(catalogueElement("catalogue-delai").value);
    const entree = catalogueElement("catalogue-entree").value;
    const plat = catalogueElement("catalogue-plat").value;
    const dessert = catalogueElement("catalogue-dessert").value;

    if (!nom || !description || !Number.isInteger(minimum) || minimum < 1 ||
        prix <= 0 || !Number.isInteger(stock) || stock < 0 ||
        !Number.isInteger(delai) || delai < 1) {
        messageCatalogue.textContent = "Vérifiez les champs du menu.";
        return;
    }
    if (!entree || !plat || !dessert) {
        messageCatalogue.textContent = "Choisissez une entrée, un plat et un dessert.";
        return;
    }
    const photos = [];
    for (let i = 1; i <= 3; i++) {
        const photo = catalogueElement("catalogue-image-" + i).value;
        if (photo !== "") photos.push(photo);
    }
    if (photos.length === 0) {
        messageCatalogue.textContent = "Ajoutez au moins une photo dans la galerie.";
        return;
    }

    let code = choixCatalogue.value;
    if (code === "nouveau") code = "menu-" + Date.now();
    catalogue[code] = {
        nom: nom, description: description,
        theme: catalogueElement("catalogue-theme").value,
        regime: catalogueElement("catalogue-regime").value,
        minimum: minimum, prix: prix, stock: stock, delaiHeures: delai,
        conditions: catalogueElement("catalogue-conditions").value.trim(),
        actif: catalogueElement("catalogue-actif").checked,
        photo: catalogueElement("catalogue-photo").value, galerie: photos,
        plats: { entree: entree, plat: plat, dessert: dessert }
    };
    VG.ecrire("menus", catalogue);
    remplirListeMenus();
    choixCatalogue.value = code;
    chargerMenuCatalogue();
    messageCatalogue.textContent = "Menu enregistré. Vérifiez-le dans Tous les menus.";
});


// ===== Catalogue retrait =====

catalogueElement("catalogue-retirer").addEventListener("click", function() {
    const menu = catalogue[choixCatalogue.value];
    if (!menu) return;
    menu.actif = !menu.actif;
    VG.ecrire("menus", catalogue);
    messageCatalogue.textContent = menu.actif ? "Menu rétabli." : "Menu retiré du catalogue public.";
    catalogueElement("catalogue-actif").checked = menu.actif;
});


// ===== Catalogue plats =====

catalogueElement("nouveau-plat-formulaire").addEventListener("submit", function(event) {
    event.preventDefault();
    const nom = catalogueElement("nouveau-plat-nom").value.trim();
    if (!nom) return;
    const plats = VG.plats();
    plats.push({
        id: "plat-" + Date.now(), nom: nom,
        type: catalogueElement("nouveau-plat-type").value,
        allergenes: catalogueElement("nouveau-plat-allergenes").value.trim()
    });
    VG.ecrire("plats", plats);
    remplirListePlats();
    catalogueElement("nouveau-plat-nom").value = "";
    catalogueElement("nouveau-plat-allergenes").value = "";
    catalogueElement("nouveau-plat-message").textContent = "Plat ajouté : vous pouvez le choisir dans plusieurs menus.";
});

remplirPhotos();
remplirListePlats();
remplirListeMenus();
