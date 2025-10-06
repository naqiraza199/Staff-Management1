<x-filament-panels::page>

    <div class="filament-widget dashboard-widget">
          <div style="    display: flex
;
    width: 100%;
    justify-content: space-between;">
            <div class="summary-card">
            <h3 class="summary-title">New Incidents</h3>
            <p class="summary-value">0</p>
        </div>
        <div class="summary-card">
            <h3 class="summary-title">Late Clock-ins</h3>
            <p class="summary-value">0</p>
        </div>
        <div class="summary-card">
            <h3 class="summary-title">Late Clock-outs</h3>
            <p class="summary-value">0</p>
        </div>
        <div class="summary-card">
            <h3 class="summary-title">Forms Awaiting Review</h3>
            <p class="summary-value">0</p>
        </div>
        <div class="summary-card">
            <h3 class="summary-title">Shifts Cancelled Today</h3>
            <p class="summary-value">0</p>
        </div>
    </div>
    <div class="dashboard-grid">
        <!-- Summary Cards -->
  

        <!-- Charts Section -->
        <div class="chart-card">
            <h3 class="chart-title">Utilisation</h3>
            <div class="chart-wrapper">
                <canvas id="utilisationChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Vacant Hours vs Availability Forecast <a href="#" class="view-report">View Report</a></h3>
            <div class="chart-wrapper">
                <canvas id="vacantHoursChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Shift Cancellations <a href="#" class="view-report">View Report</a></h3>
            <div class="chart-wrapper">
                <canvas id="shiftCancellationsChart"></canvas>
            </div>
        </div>
        <div class="chart-card wide">
            <h3 class="chart-title">Rostered vs Actual Shift Time Variance <a href="#" class="view-report">View Report</a></h3>
            <div class="chart-wrapper">
                <div class="chart-half">
                    <canvas id="shiftVarianceChart"></canvas>
                </div>
                <div class="chart-half">
                    <table class="variance-table">
                        <tr><td>Staff Name</td><td>Variance Rate</td><td>Variance Trend</td></tr>
                        <tr><td>Ahmed Mustajab Shah</td><td>0%</td><td><hr class="trend-line"></td></tr>
                        <tr><td>Ravalz ali</td><td>0%</td><td><hr class="trend-line"></td></tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="chart-card">
            <h3 class="chart-title">Late Clock In Times <a href="#" class="view-report">View Report</a></h3>
            <div class="chart-wrapper">
                <canvas id="lateClockInChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</div>

<style>
    .dashboard-widget {
        font-family: 'Poppins', sans-serif;
        border-radius: 15px;
    }
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-top: 50px;
    }
    .summary-card {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: linear-gradient(135deg, #F3F5F9 0%, #c9c9c9 100%);
        width: 19%;
        height: 120px;
    }
    .summary-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
    .summary-card.wide {
        grid-column: span 2;
    }
    .summary-title {
        color: #444;
        font-size: 14px;
        margin-bottom: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .summary-value {
        color: #2c3e50;
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }
        .chart-card
        {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: linear-gradient(135deg, #F3F5F9 0%, #c9c9c9 100%);
            width: 99% !important;
        }
    .chart-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
    .chart-card.wide {
        grid-column: span 2;
    }
    .chart-title {
        color: #34495e;
        font-size: 16px;
        margin-bottom: 15px;
        font-weight: 600;
    }
    .view-report {
        color: #e74c3c;
        text-decoration: none;
        margin-left: 10px;
        font-weight: 500;
        transition: color 0.3s ease;
    }
    .view-report:hover {
        color: #c0392b;
        text-decoration: underline;
    }
    .chart-wrapper {
        display: flex;
        justify-content: space-between;
        height: 250px;
    }
    .chart-half {
        width: 48%;
    }
    canvas {
        width: 100%;
        height: 100%;
        border-radius: 5px;
    }
    .variance-table {
        width: 100%;
        border-collapse: collapse;
    }
    .variance-table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
        text-align: left;
        font-size: 14px;
        color: #7f8c8d;
    }
    .trend-line {
        border: 0;
        height: 3px;
        background-color: #e74c3c;
        margin: 5px 0;
        border-radius: 2px;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded. Please check the CDN link.');
        return;
    }

    // Utilisation Chart
    const utilisationChart = new Chart(document.getElementById('utilisationChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Ravalz ali', 'Ahmed Mustajab'],
            datasets: [{
                label: 'Assigned Hours',
                data: [50, 60],
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'Contractual Hours',
                data: [100, 100],
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { position: 'top', labels: { font: { size: 14 } } } },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Vacant Hours vs Availability Forecast
    const vacantHoursChart = new Chart(document.getElementById('vacantHoursChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Oct 04', 'Oct 06', 'Oct 08', 'Oct 10', 'Oct 12', 'Oct 14', 'Oct 16', 'Oct 18', 'Oct 20', 'Oct 22', 'Oct 24', 'Oct 26', 'Oct 28', 'Oct 30', 'Nov 01'],
            datasets: [{
                label: 'Vacant Hours',
                data: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 90, 80, 70, 60],
                borderColor: 'rgba(255, 159, 64, 1)',
                fill: false,
                tension: 0.4
            }, {
                label: 'Availability (%)',
                data: [100, 90, 80, 70, 60, 50, 40, 30, 20, 10, 0, 10, 20, 30, 40],
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: false,
                tension: 0.4
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { position: 'top', labels: { font: { size: 14 } } } },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Shift Cancellations
    const shiftCancellationsChart = new Chart(document.getElementById('shiftCancellationsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Sep 04', 'Sep 06', 'Sep 08', 'Sep 10', 'Sep 12', 'Sep 14', 'Sep 16', 'Sep 18', 'Sep 20', 'Sep 22', 'Sep 24', 'Sep 26', 'Sep 28', 'Oct 01', 'Oct 03'],
            datasets: [{
                label: 'Staff',
                data: [0, 5, 10, 15, 20, 25, 20, 15, 10, 5, 0, 5, 10, 15, 20],
                borderColor: 'rgba(54, 162, 235, 1)',
                fill: false,
                tension: 0.4
            }, {
                label: 'Client',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                borderColor: 'rgba(255, 99, 132, 1)',
                fill: false,
                tension: 0.4
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { position: 'top', labels: { font: { size: 14 } } } },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Rostered vs Actual Shift Time Variance
    const shiftVarianceChart = new Chart(document.getElementById('shiftVarianceChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Ahmed Mustajab Shah', 'Ravalz ali'],
            datasets: [{
                label: 'Variance Rate',
                data: [0, 0],
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { position: 'top', labels: { font: { size: 14 } } } },
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Late Clock In Times
    const lateClockInChart = new Chart(document.getElementById('lateClockInChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Sep 04', 'Sep 06', 'Sep 08', 'Sep 10', 'Sep 12', 'Sep 14', 'Sep 16', 'Sep 18', 'Sep 20', 'Sep 22', 'Sep 24', 'Sep 26', 'Sep 28', 'Oct 01', 'Oct 03'],
            datasets: [{
                label: 'Staff',
                data: [0, 5, 10, 15, 20, 25, 20, 15, 10, 5, 0, 5, 10, 15, 20],
                borderColor: 'rgba(255, 159, 64, 1)',
                fill: false,
                tension: 0.4
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { position: 'top', labels: { font: { size: 14 } } } },
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>


</x-filament-panels::page>
