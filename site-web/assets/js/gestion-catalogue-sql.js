const formulaireMenu = document.getElementById("form-modifier-menu");
const choixMenu = formulaireMenu.querySelector('select[name="menu_id"]');

function remplirMenu() {
    const option = choixMenu.selectedOptions[0];
    if (!option) return;
    const valeurs = {
        titre: option.dataset.titre,
        description: option.dataset.description,
        theme_id: option.dataset.theme,
        regime_id: option.dataset.regime,
        personnes_minimum: option.dataset.minimum,
        prix_minimum: option.dataset.prix,
        stock_disponible: option.dataset.stock,
        delai_heures: option.dataset.delai,
        conditions_menu: option.dataset.conditions,
        photo_principale: option.dataset.photo
    };
    for (const nom in valeurs) {
        const champ = formulaireMenu.elements.namedItem(nom);
        if (champ) champ.value = valeurs[nom];
    }
    formulaireMenu.elements.namedItem("actif").checked = option.dataset.actif === "1";
}
choixMenu.addEventListener("change", remplirMenu);
remplirMenu();

const formulairePlat = document.getElementById("form-modifier-plat");
const choixPlat = formulairePlat.querySelector('select[name="plat_id"]');

function remplirPlat() {
    const option = choixPlat.selectedOptions[0];
    if (!option) return;
    formulairePlat.elements.namedItem("nom_plat").value = option.dataset.nom;
    formulairePlat.elements.namedItem("type_plat").value = option.dataset.type;
    const ids = option.dataset.allergenes.split(",");
    const cases = formulairePlat.querySelectorAll('input[name="allergenes[]"]');
    for (let i = 0; i < cases.length; i++) {
        cases[i].checked = ids.includes(cases[i].value);
    }
}
choixPlat.addEventListener("change", remplirPlat);
remplirPlat();
