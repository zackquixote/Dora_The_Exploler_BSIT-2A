/* ═══════════════════════════════════════════════
   BMIS Dashboard Charts — Chart.js 3.9.1
   Uses design system palette
   ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof DASHBOARD_DATA === 'undefined') return;

    const palette = ['#185FA5','#1D9E75','#534AB7','#A32D2D','#854F0B','#3B6D11'];
    const paletteBg = ['#E6F1FB','#E1F5EE','#EEEDFE','#FCEBEB','#FAEEDA','#EAF3DE'];

    Chart.defaults.font.family = "'DM Sans', sans-serif";
    Chart.defaults.font.size = 11;
    Chart.defaults.color = '#64748b';

    // ── Gender Donut ──
    const genderEl = document.getElementById('genderChart');
    if (genderEl && DASHBOARD_DATA.gender) {
        new Chart(genderEl, {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [DASHBOARD_DATA.gender.male, DASHBOARD_DATA.gender.female],
                    backgroundColor: ['#185FA5', '#A32D2D'],
                    borderWidth: 0,
                    cutout: '72%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    // ── Purok Bar ──
    const purokEl = document.getElementById('purokChart');
    if (purokEl && DASHBOARD_DATA.purokLabels) {
        new Chart(purokEl, {
            type: 'bar',
            data: {
                labels: DASHBOARD_DATA.purokLabels,
                datasets: [{
                    label: 'Residents',
                    data: DASHBOARD_DATA.purokValues,
                    backgroundColor: palette.slice(0, DASHBOARD_DATA.purokLabels.length),
                    borderRadius: 6,
                    maxBarThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)', drawBorder: false }, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // ── Civil Status Donut ──
    const civilEl = document.getElementById('civilStatusChart');
    if (civilEl && DASHBOARD_DATA.civilLabels) {
        new Chart(civilEl, {
            type: 'doughnut',
            data: {
                labels: DASHBOARD_DATA.civilLabels,
                datasets: [{
                    data: DASHBOARD_DATA.civilValues,
                    backgroundColor: palette.slice(0, DASHBOARD_DATA.civilLabels.length),
                    borderWidth: 0,
                    cutout: '65%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }
});