document.addEventListener("DOMContentLoaded", () => {
    const applyFiltersBtn = document.getElementById("apply-filters");
    const resetFiltersBtn = document.getElementById("reset-filters");
    const filtersForm = document.getElementById("filters-form");
    const resultsContainer = document.getElementById("results-container");

    // Fonction pour appliquer les filtres
    applyFiltersBtn.addEventListener("click", () => {
        const formData = new FormData(filtersForm);
        const params = new URLSearchParams(formData);

        fetch(`/search/results?${params.toString()}`, {
            method: "GET",
        })        
        .then((response) => response.text())
        .then((html) => {
            resultsContainer.innerHTML = html;
        })
        .catch((error) => {
            console.error("Erreur lors de l'application des filtres :", error);
        });
    });

    // Fonction pour réinitialiser les filtres
    resetFiltersBtn.addEventListener("click", () => {
        filtersForm.reset();
        applyFiltersBtn.click(); // Recharge les résultats sans filtre
    });

    // Gestion des étoiles pour la note minimale
    const stars = document.querySelectorAll(".star-rating i");
    const noteInput = document.getElementById("noteMinimale");

    stars.forEach((star) => {
        star.addEventListener("click", () => {
            const value = star.getAttribute("data-value");
            noteInput.value = value;

            // Mise à jour des classes pour afficher les étoiles sélectionnées
            stars.forEach((s, index) => {
                s.classList.toggle("fas", index < value);
                s.classList.toggle("far", index >= value);
            });
        });
    });
});
