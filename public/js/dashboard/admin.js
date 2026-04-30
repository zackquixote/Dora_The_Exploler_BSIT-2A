/**
 * Dashboard Charts & Interactions
 * Uses Chart.js v3.9.1 and the DASHBOARD_DATA global object from PHP.
 */
(function() {
    'use strict';

    // ── Colour palette ────────────────────────────────────────
    const COLORS = [
        '#3c8dbc', '#e91e8c', '#00a65a', '#f39c12',
        '#605ca8', '#00c0ef', '#d81b60', '#ff7701'
    ];

    // ── Chart.js default options (can be extended) ─────────────
    const defaultLegendOff = {
        plugins: { legend: { display: false } }
    };

    // ── Gender Doughnut Chart ──────────────────────────────────
    const genderCtx = document.getElementById('genderChart');
    if (genderCtx) {
        new Chart(genderCtx, {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [DASHBOARD_DATA.gender.male, DASHBOARD_DATA.gender.female],
                    backgroundColor: ['#3c8dbc', '#e91e8c'],
                    borderWidth: 2
                }]
            },
            options: Object.assign({ responsive: true, maintainAspectRatio: true }, defaultLegendOff)
        });
    }

    // ── Purok Bar Chart ────────────────────────────────────────
    const purokCtx = document.getElementById('purokChart');
    if (purokCtx) {
        const labels = DASHBOARD_DATA.purokLabels.length ?
                       DASHBOARD_DATA.purokLabels : ['No Data'];
        const values = DASHBOARD_DATA.purokValues.length ?
                       DASHBOARD_DATA.purokValues : [0];

        new Chart(purokCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Residents',
                    data: values,
                    backgroundColor: COLORS.slice(0, labels.length || 1),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                ...defaultLegendOff,
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { ticks: { maxRotation: 30 } }
                }
            }
        });
    }

    // ── Civil Status Pie Chart ─────────────────────────────────
    const civilCtx = document.getElementById('civilStatusChart');
    if (civilCtx) {
        const labels = DASHBOARD_DATA.civilLabels.length ?
                       DASHBOARD_DATA.civilLabels : ['No Data'];
        const values = DASHBOARD_DATA.civilValues.length ?
                       DASHBOARD_DATA.civilValues : [1];

        new Chart(civilCtx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: COLORS,
                    borderWidth: 2
                }]
            },
            options: Object.assign({ responsive: true, maintainAspectRatio: true }, defaultLegendOff)
        });
    }

    // ── You can add more dashboard interactivity here later ──

})();