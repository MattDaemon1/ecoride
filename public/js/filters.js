document.addEventListener("DOMContentLoaded", () => {
    const applyFiltersBtn = document.getElementById("apply-filters");
    const resetFiltersBtn = document.getElementById("reset-filters");
    const filtersForm = document.getElementById("filters-form");
    const resultsContainer = document.getElementById("results-container");

    // Vérification des éléments HTML essentiels
    if (!filtersForm || !resultsContainer) {
        console.error("Formulaire ou conteneur de résultats introuvable.");
        return;
    }

    // Récupérer les critères de recherche de base
    const baseSearchFields = {
        lieuDepart: document.querySelector('input[name="lieuDepart"]')?.value || "",
        lieuArrivee: document.querySelector('input[name="lieuArrivee"]')?.value || "",
        dateDepart: document.querySelector('input[name="dateDepart"]')?.value || "",
    };

    // Fonction pour appliquer les filtres
    const applyFilters = () => {
        const formData = new FormData(filtersForm);

        // Ajouter les critères de recherche de base
        Object.keys(baseSearchFields).forEach((key) => {
            if (!formData.has(key)) {
                formData.set(key, baseSearchFields[key]);
            }
        });

        const params = new URLSearchParams(formData);

        fetch(`/search/results?${params.toString()}`, {
            method: "GET",
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Erreur lors de la récupération des résultats.");
                }
                return response.text();
            })
            .then((html) => {
                resultsContainer.innerHTML = html;
            })
            .catch((error) => {
                console.error("Erreur lors de l'application des filtres :", error);
            });
    };

    // Ajouter un événement au bouton "Appliquer les filtres"
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener("click", (e) => {
            e.preventDefault();
            applyFilters();
        });
    }

    // Réinitialiser les filtres et recharger les résultats de base
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener("click", (e) => {
            e.preventDefault();
            filtersForm.reset();
            applyFilters();
        });
    }

    // Gestion des étoiles pour la note minimale
    const stars = document.querySelectorAll(".star-rating i");
    const noteInput = document.getElementById("noteMinimale");

    if (stars && noteInput) {
        stars.forEach((star) => {
            star.addEventListener("click", () => {
                const value = star.getAttribute("data-value");
                noteInput.value = value;

                // Mise à jour visuelle des étoiles
                stars.forEach((s, index) => {
                    s.classList.toggle("fas", index < value);
                    s.classList.toggle("far", index >= value);
                });
            });
        });
    }
});
