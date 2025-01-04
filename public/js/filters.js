document.addEventListener('DOMContentLoaded', () => {
    const filtersForm = document.getElementById('filters-form');
    const applyFiltersButton = document.getElementById('apply-filters');
    const resetFiltersButton = document.getElementById('reset-filters');
    const resultsContainer = document.getElementById('results-container');
    const starRating = document.querySelectorAll('.star-rating i');

    // Appliquer les filtres
    applyFiltersButton.addEventListener('click', () => {
        const filters = new FormData(filtersForm);

        fetch('/search/filter', {
            method: 'POST',
            body: filters,
        })
            .then(response => response.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                if (data.length === 0) {
                    resultsContainer.innerHTML = '<p>Aucun covoiturage trouvé.</p>';
                } else {
                    data.forEach(covoiturage => {
                        const item = document.createElement('div');
                        item.className = 'result-item';
                        item.innerHTML = `
                            <p><strong>Départ:</strong> ${covoiturage.lieuDepart}</p>
                            <p><strong>Arrivée:</strong> ${covoiturage.lieuArrivee}</p>
                            <p><strong>Prix:</strong> ${covoiturage.prixPersonne}€</p>
                            <p><strong>Énergie:</strong> ${covoiturage.voiture.energie}</p>
                        `;
                        resultsContainer.appendChild(item);
                    });
                }
            })
            .catch(error => {
                console.error('Erreur lors de l’application des filtres :', error);
            });
    });

    // Réinitialiser les filtres
    resetFiltersButton.addEventListener('click', () => {
        // Réinitialiser les champs du formulaire
        filtersForm.reset();

        // Réinitialiser les étoiles pour la note minimale
        starRating.forEach(
