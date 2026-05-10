/* ═══════════════════════════════════════════════
   BMIS Dashboard Charts — Chart.js 3.9.1
   Uses design system palette
   ═══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
    if (typeof DASHBOARD_DATA === 'undefined') return;

    const palette = ['#4F46E5','#10B981','#8B5CF6','#F43F5E','#F59E0B','#22C55E'];
    const paletteBg = ['#EEF2FF','#D1FAE5','#EDE9FE','#FFE4E6','#FEF3C7','#DCFCE7'];

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 11.5;
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
                    backgroundColor: ['#4F46E5', '#F43F5E'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6,
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
        // Create subtle gradient
        let ctx = purokEl.getContext('2d');
        let gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, '#4F46E5'); // Indigo
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.1)');

        new Chart(purokEl, {
            type: 'bar',
            data: {
                labels: DASHBOARD_DATA.purokLabels,
                datasets: [{
                    label: 'Residents',
                    data: DASHBOARD_DATA.purokValues,
                    backgroundColor: gradient,
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 32,
                    hoverBackgroundColor: '#8B5CF6' // Violet on hover
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.85)',
                        backdropFilter: 'blur(4px)',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { size: 13, family: "'Inter', sans-serif", weight: 'bold' },
                        bodyFont: { size: 12, family: "'Inter', sans-serif" }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)', drawBorder: false }, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                },
                animation: {
                    y: { duration: 1500, easing: 'easeOutQuart' }
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
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    cutout: '65%',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                animation: { animateScale: true, animateRotate: true }
            }
        });
    }

    // ── Age Distribution Bar ──
    const ageEl = document.getElementById('ageChart');
    if (ageEl && DASHBOARD_DATA.ageLabels) {
        const ageColors = ['#6366f1', '#185FA5', '#1D9E75', '#854F0B', '#A32D2D'];
        new Chart(ageEl, {
            type: 'bar',
            data: {
                labels: DASHBOARD_DATA.ageLabels,
                datasets: [{
                    label: 'Residents',
                    data: DASHBOARD_DATA.ageValues,
                    backgroundColor: ageColors,
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.85)',
                        padding: 12,
                        cornerRadius: 10,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(ctx) {
                                var total = ctx.dataset.data.reduce(function(a,b){ return a+b; }, 0);
                                var pct = total > 0 ? Math.round((ctx.parsed.y / total) * 100) : 0;
                                return ctx.parsed.y + ' residents (' + pct + '%)';
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)', drawBorder: false }, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                },
                animation: { y: { duration: 1200, easing: 'easeOutQuart' } }
            }
        });
    }

    // ── AJAX Case Filtering ──
    const caseFilter = document.getElementById('caseOverviewFilter');
    if (caseFilter) {
        caseFilter.addEventListener('change', function() {
            const val = this.value;
            // Add fade out class
            const statNums = document.querySelectorAll('.case-overview-num');
            statNums.forEach(el => el.style.opacity = '0.3');

            fetch(DASHBOARD_DATA.baseUrl + 'admin/dashboard/filterCases?range=' + val)
                .then(res => res.json())
                .then(data => {
                    setTimeout(() => {
                        document.getElementById('openCasesCount').innerText = data.openCases;
                        document.getElementById('hearingsCount').innerText = data.hearingsToday;
                        document.getElementById('settledCount').innerText = data.settledThisMonth;
                        document.getElementById('blotterTotalCount').innerText = data.blotterCount;
                        statNums.forEach(el => el.style.opacity = '1');
                    }, 200);
                }).catch(err => {
                    console.error(err);
                    statNums.forEach(el => el.style.opacity = '1');
                });
        });
    }
});