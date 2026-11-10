
const adressePage = window.location.href;

// Vérifier si le visiteur a choisi le menu végétarien
if (adressePage.includes("menu=vegetarien")) {

    // Modifier le titre de l'onglet
    document.querySelector("title").textContent =
        "Menu Végétarien - Vite & Gourmand";

    // Modifier la description de la page
    document.querySelector('meta[name="description"]').setAttribute(
        "content",
        "Découvrez le Menu Végétarien de Vite & Gourmand à Bordeaux."
    );

    // Modifier le titre principal
    document.querySelector(".introduction-detail h1").textContent =
        "Menu Végétarien";

    // Modifier la description du menu
    document.querySelector(".introduction-detail p").textContent =
        "Salade de saison, risotto aux champignons et mousse au chocolat.";

    // Modifier la description de la section principale
    document.querySelector(".presentation-detail").setAttribute(
        "aria-label",
        "Présentation du Menu Végétarien"
    );

    // Modifier la grande photo
    const photo = document.querySelector(".photo-principale img");

    photo.setAttribute(
        "src",
        "assets/images/menus/végétarien gourmand aux champignons.png"
    );

    photo.setAttribute(
        "alt",
        "Salade de saison, risotto aux champignons et desserts au chocolat"
    );

    // Modifier le thème
    document.querySelector(".theme-menu").innerHTML =
        "<strong>Thème :</strong> Classique";

    // Modifier le régime alimentaire
    document.querySelector(".regime-menu").innerHTML =
        "<strong>Régime :</strong> Végétarien";

    // Modifier le nombre minimum de personnes
    document.querySelector(".minimum-personnes span").innerHTML =
        "<strong>Minimum :</strong> 8 personnes";

    // Modifier la première icône en 8+
    document.querySelector(".minimum-personnes img").setAttribute(
        "src",
        "assets/images/menus/Icône de groupe avec badge 8+.png"
    );

    // Modifier le prix
    document.querySelector(".montant-menu strong").textContent =
        "200 €";

    // Modifier le nombre de personnes sous le prix
    document.querySelector(".precision-prix").innerHTML =
        "<span class='mot-prix'>Prix </span>pour 8 personnes";

    // Modifier la composition du menu
    document.querySelector(".composition-menu").innerHTML =
        "<h2>Composition du menu</h2>" +

        '<div class="plat-menu">' +
            "<h3>Entrée</h3>" +
            "<p>Salade de saison : laitue, tomates, concombre, mozzarella, basilic et huile d'olive</p>" +
            "<p>Allergène : lait (mozzarella)</p>" +
        "</div>" +

        '<div class="plat-menu">' +
            "<h3>Plat</h3>" +
            "<p>Risotto aux champignons : riz, champignons, beurre, crème et bouillon de légumes</p>" +
            "<p>Allergène : lait (beurre et crème)</p>" +
        "</div>" +

        '<div class="plat-menu dernier-plat">' +
            "<h3>Dessert</h3>" +
            "<p>Mousse au chocolat noir : chocolat noir, œufs et crème</p>" +
            "<p>Allergènes : œufs et lait (crème)</p>" +
        "</div>";

    // Modifier le minimum dans les conditions
    document.getElementById("condition-minimum").textContent =
        "Minimum : 8 personnes.";

    // Modifier la deuxième icône en 8+
    document.getElementById("icone-condition-minimum").setAttribute(
        "src",
        "assets/images/menus/Icône de groupe avec badge 8+.png"
    );

    // Modifier les trois photos de la galerie végétarienne
    // Chaque image possède sa propre classe CSS

    document.querySelector(".galerie-photos").innerHTML =

        // Photo 1 : quiche aux légumes
        '<div class="vignette-vegetarienne photo-quiche" role="img" aria-label="Menu végétarien avec quiche aux légumes"></div>' +

        // Photo 2 : menu
        '<div class="vignette-vegetarienne photo-menu" role="img" aria-label="Présentation d’un menu végétarien"></div>' +

        // Photo 3 : gratin végétal
        '<div class="vignette-vegetarienne photo-gratin" role="img" aria-label="Gratin végétal et tarte au citron meringuée"></div>';
}
