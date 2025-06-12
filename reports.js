let currentChart = null;
function loadReports() {
    loadOverviewStats();
    loadProductsByCategory();
    loadLowStockReport();
    loadMonthlyTrends();
}
function loadOverviewStats() {
    fetch('get_reports_data.php?type=overview')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error loading overview stats:', data.error);
                return;
            }
            document.getElementById('totalProducts').textContent = data.total_products || 0;
            document.getElementById('totalUsers').textContent = data.total_users || 0;
            document.getElementById('totalValue').textContent = `€${parseFloat(data.total_value || 0).toFixed(2)}`;
            document.getElementById('lowStockItems').textContent = data.low_stock_items || 0;
        })
        .catch(error => console.error('Error loading overview stats:', error));
}
function loadProductsByCategory() {
    fetch('get_reports_data.php?type=products_by_category')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error loading category data:', data.error);
                return;
            }
            const ctx = document.getElementById('categoryChart');
            if (!ctx) return;
            if (currentChart) {
                currentChart.destroy();
            }
            currentChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(item => item.category || 'Nezināma'),
                    datasets: [{
                        data: data.map(item => item.count),
                        backgroundColor: [
                            '#667eea', '#764ba2', '#f093fb', '#f5576c',
                            '#4facfe', '#00f2fe', '#43e97b', '#38f9d7'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error loading category chart:', error));
}
function loadLowStockReport() {
    fetch('get_reports_data.php?type=low_stock')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error loading low stock data:', data.error);
                return;
            }
            const tbody = document.getElementById('lowStockTableBody');
            if (!tbody) return;
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Nav produktu ar zemu krājumu</td></tr>';
                return;
            }
            data.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.product_code}</td>
                    <td>${product.product_name}</td>
                    <td>${product.category}</td>
                    <td class="text-center">${product.min_stock_level}</td>
                    <td class="text-right">€${parseFloat(product.unit_price || 0).toFixed(2)}</td>
                `;
                tbody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading low stock report:', error));
}
function loadMonthlyTrends() {
    fetch('get_reports_data.php?type=monthly_trends')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error loading trends data:', data.error);
                return;
            }
            const ctx = document.getElementById('trendsChart');
            if (!ctx) return;
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.month),
                    datasets: [{
                        label: 'Pievienoti produkti',
                        data: data.map(item => item.products_added),
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Vērtība (€)',
                        data: data.map(item => item.value),
                        borderColor: '#f5576c',
                        backgroundColor: 'rgba(245, 87, 108, 0.1)',
                        tension: 0.4,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error loading trends chart:', error));
}
function exportReport(type) {
    alert(`Eksportē ${type} atskaiti... (Funkcionalitāte tiks pievienota)!`);
}
function printReport() {
    window.print();
}
