document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('statsModal');
    const canvas = document.getElementById('salesEvolutionChart');

    if (!modal || !canvas) {
        return;
    }

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');

    let chart = null;

    modal.addEventListener('shown.bs.modal', function () {
        if (chart) {
            chart.destroy();
        }

        chart = new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: "Chiffre d'affaires TTC",
                    data: values,
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return value + ' €';
                            }
                        }
                    }
                }
            }
        });
    });
});