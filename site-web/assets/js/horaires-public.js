fetch("./php/horaires-public.php")
    .then(function(reponse) {
        if (!reponse.ok) throw new Error("Horaires indisponibles");
        return reponse.json();
    })
    .then(function(horaires) {
        if (!Array.isArray(horaires)) return;
        const listes = document.querySelectorAll(".horaires-pc, .horaires-mobile");
        for (let i = 0; i < listes.length; i++) {
            listes[i].textContent = "";
            for (let j = 0; j < horaires.length; j++) {
                const jour = horaires[j];
                const item = document.createElement("li");
                const ouverture = jour.heure_ouverture ? jour.heure_ouverture.slice(0, 5) : "";
                const fermeture = jour.heure_fermeture ? jour.heure_fermeture.slice(0, 5) : "";
                item.textContent = jour.jour + " : " + (Number(jour.ferme) === 1 ? "Fermé" : ouverture + " - " + fermeture);
                listes[i].appendChild(item);
            }
        }
    })
    .catch(function() {
        const listes = document.querySelectorAll(".horaires-pc, .horaires-mobile");
        for (let i = 0; i < listes.length; i++) {
            listes[i].textContent = "";
            const item = document.createElement("li");
            item.textContent = "Horaires momentanément indisponibles";
            listes[i].appendChild(item);
        }
    });
