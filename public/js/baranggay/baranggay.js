/**
 * Barangay Settings Page
 * Handles filtering of official dropdowns by resident name and purok.
 */
(function() {
    'use strict';

    function init() {
        const searchInput = document.getElementById('resident_search');
        const purokSelect = document.getElementById('purok_filter');
        const dropdowns = document.querySelectorAll('.official-select');

        if (!searchInput || !purokSelect || !dropdowns.length) {
            console.warn('Required elements not found');
            return;
        }

        function filterDropdowns() {
            const term = searchInput.value.toLowerCase();
            const purok = purokSelect.value;

            dropdowns.forEach(dd => {
                const keptValue = dd.value;
                Array.from(dd.options).forEach(opt => {
                    if (!opt.value) return;

                    const nameMatch = opt.getAttribute('data-name').includes(term);
                    const purokMatch = (purok === 'all' || opt.getAttribute('data-purok') === purok);
                    const show = nameMatch && purokMatch;

                    opt.style.display = show ? '' : 'none';
                });

                // Ensure the currently selected option remains visible if it would have been hidden
                if (keptValue) {
                    const selectedOpt = dd.querySelector(`option[value="${keptValue}"]`);
                    if (selectedOpt) selectedOpt.style.display = '';
                }
            });
        }

        searchInput.addEventListener('keyup', filterDropdowns);
        purokSelect.addEventListener('change', filterDropdowns);

        // Initial filter run to apply any pre-selected purok (if any)
        filterDropdowns();
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();