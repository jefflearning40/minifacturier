document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('sellerChart');

    if (!canvas || typeof Chart === 'undefined') {
        return;
    }

    let chartInstance = null;

    const modal = document.getElementById('statsModal');

    if (modal) {
        modal.addEventListener('shown.bs.modal', function () {
            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: window.sellerNames || [],
                    datasets: [{
                        label: 'Nombre de factures',
                        data: window.sellerInvoices || [],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
    }
});