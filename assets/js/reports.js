document.addEventListener('DOMContentLoaded', function() {
    let revenueChart = null;
    let serviceTypeChart = null;

    loadReportData();

    document.getElementById('reportFilterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        loadReportData();
    });

    document.getElementById('exportExcel').addEventListener('click', exportToExcel);
    document.getElementById('exportPDF').addEventListener('click', exportToPDF);

    function loadReportData() {
        const type = document.getElementById('report_type').value;
        const start = document.getElementById('start_date').value;
        const end = document.getElementById('end_date').value;

        document.getElementById('summaryTable').innerHTML = '<tr><td colspan="2" class="text-center">Loading...</td></tr>';
        document.getElementById('reportData').innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';

        fetch(`ajax/get_report_data.php?report_type=${type}&start_date=${start}&end_date=${end}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateSummary(data.summary);
                    updateCharts(data.chart_data, data.service_data);
                    updateReportTable(data.report_data);
                } else {
                    showToast(data.message || 'Error loading data', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error loading data', 'danger');
            });
    }

    function updateSummary(summary) {
        document.getElementById('summaryTable').innerHTML = ` 
            <tr><td><strong>Total Orders:</strong></td><td>${summary.total_orders}</td></tr>
            <tr><td><strong>Total Revenue:</strong></td><td>${formatCurrency(summary.total_revenue)}</td></tr>
            <tr><td><strong>Average Order Value:</strong></td><td>${formatCurrency(summary.avg_order_value)}</td></tr>
        `;
    }

    function updateCharts(chartData, serviceData) {
        if (revenueChart) revenueChart.destroy();
        if (serviceTypeChart) serviceTypeChart.destroy();

        revenueChart = new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Revenue',
                    data: chartData.values,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Revenue: ' + formatCurrency(ctx.raw)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => formatCurrency(val)
                        }
                    }
                }
            }
        });

        const labels = serviceData.map(s => s.service_type);
        const values = serviceData.map(s => s.total_revenue);
        const colors = ['rgba(78, 115, 223, 0.8)', 'rgba(28, 200, 138, 0.8)', 'rgba(246, 194, 62, 0.8)'];

        serviceTypeChart = new Chart(document.getElementById('serviceTypeChart'), {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    hoverBackgroundColor: colors.map(c => c.replace('0.8', '1'))
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,
                            padding: 15,
                            font: {
                                size: 14,
                                weight: 'bold'
                            },
                            textAlign: 'center', // Membuat teks legenda rata tengah
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const val = ctx.raw;
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = Math.round((val / total) * 100);
                                return `${ctx.label}: ${formatCurrency(val)} (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });

        // Menambahkan pengaturan CSS untuk memastikan label chart berada di tengah
        const chartCanvas = document.getElementById('serviceTypeChart');
        chartCanvas.style.display = 'block';
        chartCanvas.style.margin = '0 auto';
    }

    function updateReportTable(data) {
        const tbody = document.getElementById('reportData');
        tbody.innerHTML = data.length === 0
            ? '<tr><td colspan="4" class="text-center">No data found</td></tr>'
            : data.map(row => `
                <tr>
                    <td>${row.date_label}</td>
                    <td>${row.order_count}</td>
                    <td>${formatCurrency(row.total_revenue)}</td>
                    <td>${formatCurrency(row.avg_order_value)}</td>
                </tr>
            `).join('');
    }

    function exportToExcel() {
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.table_to_sheet(document.getElementById('reportsTable'));
        const title = `${document.getElementById('report_type').value} Report: ${document.getElementById('start_date').value} to ${document.getElementById('end_date').value}`;
        XLSX.utils.sheet_add_aoa(ws, [[title]], { origin: 'A1' });
        XLSX.utils.book_append_sheet(wb, ws, 'Report');
        XLSX.writeFile(wb, `Laundry_Report.xlsx`);
    }

    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        const title = `${document.getElementById('report_type').value} Report`;
        const period = `Period: ${document.getElementById('start_date').value} to ${document.getElementById('end_date').value}`;

        doc.setFontSize(16).text(title, 105, 15, { align: 'center' });
        doc.setFontSize(12).text(period, 105, 22, { align: 'center' });
        doc.setFontSize(14).text('Summary', 14, 35);

        doc.autoTable({ html: '#summaryTable', startY: 40 });
        doc.setFontSize(14).text('Detailed Report', 14, doc.lastAutoTable.finalY + 15);
        doc.autoTable({
            html: '#reportsTable',
            startY: doc.lastAutoTable.finalY + 20,
            didDrawPage: function() {
                doc.setFontSize(10);
                doc.text(`Page ${doc.internal.getNumberOfPages()}`, 195, 287, { align: 'right' });
            }
        });

        doc.save('Laundry_Report.pdf');
    }

    function formatCurrency(value) {
    const formattedValue = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
    return formattedValue.replace(/,00$/, ''); // Menghapus ",00" jika ada
}


    function showToast(msg, type = 'info') {
        alert(`${type.toUpperCase()}: ${msg}`);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    if (window.innerWidth <= 768) {
        document.getElementById('printDetailReport').style.display = 'none';
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const reportType = document.getElementById('report_type');
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    function updateDates() {
        const today = new Date();
        
        if (reportType.value === 'daily') {
            // Set to today's date
            startDate.value = endDate.value = today.toISOString().split('T')[0];
        } else if (reportType.value === 'weekly') {
            // Set to 7 days ago for start date and today for end date
            const start = new Date(today);
            start.setDate(today.getDate() - 7);
            startDate.value = start.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];
        } else if (reportType.value === 'monthly') {
            // Set to the first day of this month for start date and today for end date
            const start = new Date(today.getFullYear(), today.getMonth(), 1);
            startDate.value = start.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];
        }
    }

    // Update dates initially
    updateDates();

    // When the Report Type changes, update the dates
    reportType.addEventListener('change', updateDates);
});

