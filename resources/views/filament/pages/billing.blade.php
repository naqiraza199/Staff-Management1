<!-- NOTE: Tailwind CDN and configuration have been removed -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<style>
    /* 1. Base Setup and Variables */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');

    :root {
        --primary-color: #10b981; /* Emerald 600 */
        --background-color: #f9fafb; /* gray-50 */
        --card-background: #ffffff;
        --text-dark: #1f2937;
        --text-medium: #6b7280;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', sans-serif;
        background-color: var(--background-color);
        min-height: 100vh;
    }

    .app-container {
        padding: 1.5rem; /* p-6 */
    }

    /* 2. Typography and Structure */
    .header {
        font-size: 1.875rem; /* text-3xl */
        font-weight: 800; /* font-extrabold */
        color: var(--text-dark);
        margin-bottom: 1.5rem; /* mb-6 */
        padding-bottom: 1rem; /* pb-4 */
        border-bottom: 1px solid #e5e7eb;
    }
    .primary-text {
        color: var(--primary-color);
    }
    .card {
        background-color: var(--card-background);
        border-radius: 0.75rem; /* rounded-xl */
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* shadow-lg */
    }

    /* 3. Controls Row (Filters) */
    .controls-row {
        padding: 1.5rem; /* p-6 */
        margin-bottom: 2rem; /* mb-8 */
        display: flex;
        flex-direction: column;
        gap: 1rem; /* gap-4 */
    }
    @media (min-width: 768px) { /* md breakpoint */
        .controls-row {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }
    }

    /* Toggle Switch Styling */
    .toggle-group {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem; /* gap-3 */
    }
    @media (min-width: 640px) { /* sm breakpoint */
        .toggle-group {
            flex-direction: row;
            align-items: center;
        }
    }

    .toggle-label-view {
        font-size: 0.875rem; /* text-sm */
        font-weight: 600; /* font-semibold */
        color: var(--text-medium);
        min-width: 50px;
    }

    .toggle-container {
        display: flex;
        background-color: #f3f4f6; /* gray-100 */
        padding: 4px;
        border-radius: 0.5rem; /* rounded-lg */
    }

    .toggle-radio {
        display: none;
    }

    .toggle-button {
        padding: 0.5rem 1rem; /* px-4 py-2 */
        font-size: 0.875rem; /* text-sm */
        font-weight: 500; /* font-medium */
        border-radius: 0.375rem; /* rounded-lg */
        cursor: pointer;
        transition: background-color 0.2s, color 0.2s, box-shadow 0.2s;
        text-align: center;
    }

    .toggle-radio:checked + .toggle-button {
        background-color: var(--primary-color);
        color: var(--card-background);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
    }
    .toggle-radio:not(:checked) + .toggle-button {
        background-color: transparent;
        color: var(--text-medium);
    }
    .toggle-radio:not(:checked) + .toggle-button:hover {
        background-color: #e5e7eb; /* gray-200 */
    }
    
    /* Dropdowns and Inputs */
    .filter-inputs {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    @media (min-width: 640px) {
        .filter-inputs {
            flex-direction: row;
            gap: 1rem;
            flex-wrap: wrap;
        }
    }
    .filter-input {
        min-width: 120px;
        padding: 0.625rem; /* p-2.5 */
        font-size: 0.875rem; /* text-sm */
        border: 1px solid #d1d5db; /* gray-300 */
        border-radius: 0.5rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
        transition: border-color 0.15s, box-shadow 0.15s;
        width: 100%;
    }
    .filter-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
        outline: none;
    }
    .date-input {
        min-width: 200px;
    }
    @media (min-width: 640px) {
        .filter-input {
            width: auto;
        }
    }

    /* 4. Charts Section Layout */
    .charts-section {
        gap: 1.5rem; /* gap-6 */
        display: flex;
        justify-content: space-between;
        margin-bottom:20xp;
    }

    
    /* Chart Card Styles */
    .chart-card {
        padding: 1.5rem;
    }
    .chart-title {
        font-size: 1.25rem; /* text-xl */
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 1rem;
    }
    .chart-canvas-container {
        position: relative;
        height: 18rem; /* h-72 */
    }
    .legend-custom {
        margin-top: 1rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .legend-dot {
        width: 0.75rem;
        height: 0.75rem;
        border-radius: 50%;
    }
    .dot-booked { background-color: #059669; /* green-600 */ }
    .dot-pending { background-color: #f59e0b; /* amber-500 */ }
    .dot-cancelled { background-color: #ef4444; /* red-500 */ }
    
    /* Donut Chart Specifics */
    .donut-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .total-value-box {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }
    .donut-canvas-container {
   position: relative;
  height: 21rem;
  width: 12rem;
  margin-bottom: 1.5rem;
    }
    .donut-center-text {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    .donut-percentage {
        font-size: 1.5rem; /* text-2xl */
        font-weight: 700;
    }
    .text-green-700 { color: #047857; }
    .text-gray-500 { color: #6b7280; }


    /* 5. Data Table */
    .table-container {
        padding: 0;
        overflow: hidden;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .data-table {
        min-width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .table-head {
        background-color: #f9fafb; /* gray-50 */
        border-bottom: 1px solid #e5e7eb;
    }
    .table-head th {
        padding: 0.75rem 1.5rem; /* px-6 py-3 */
        text-align: left;
        font-size: 0.75rem; /* text-xs */
        font-weight: 600;
        color: var(--text-medium);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .table-head th:not(:first-child) {
        text-align: center;
    }

    .data-table tbody tr {
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.15s;
    }
    .data-table tbody tr:hover {
        background-color: #f9fafb; /* gray-50 */
    }

    .data-table td {
        padding: 1rem 1.5rem; /* px-6 py-4 */
        font-size: 0.875rem; /* text-sm */
        white-space: nowrap;
    }
    .data-table td:first-child {
        font-weight: 500; /* font-medium */
        color: var(--text-dark);
    }
    .data-table td:not(:first-child) {
        text-align: center;
        color: var(--text-medium);
    }
    .data-table .total-cell {
        font-weight: 700;
        color: var(--text-dark);
    }

    /* Hide Absent column on small screens (sm:table-cell) */
    .col-absent-sm-hidden {
        display: none;
    }
    @media (min-width: 640px) {
        .col-absent-sm-hidden {
            display: table-cell;
        }
    }
    
    /* 6. Footer Actions */
    .table-footer {
        padding: 1rem;
        border-top: 1px solid #f3f4f6; /* gray-100 */
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem; /* gap-3 */
        color: var(--text-medium);
    }
    .action-button {
        padding: 0.5rem;
        border-radius: 9999px; /* rounded-full */
        transition: background-color 0.15s;
    }
    .action-button:hover {
        background-color: #f3f4f6; /* gray-100 */
    }
    .action-button svg {
        height: 1.5rem;
        width: 1.5rem;
        stroke: currentColor;
        stroke-width: 2;
    }
    .action-buttons{
        padding: 0.5rem;
        border-radius: 9999px;
        transition: background-color 0.15s;
        background: #13a9d7;
        color: white;
    }
    .action-buttons:hover{
        background-color: #1099ce;
    }
</style>

<div id="app" class="app-container">
    <!-- Header -->
    <h1 class="header">
        Report <span class="primary-text">Billing</span>
    </h1>

    <!-- Controls Row Card (Top Filters) -->
    <div class="card controls-row">
        
        <!-- Toggle (Staff/Client) - Crucial Element -->
        <div class="toggle-group">
            <span class="toggle-label-view">View:</span>
            <div class="toggle-container">
                <!-- Staff Radio Button -->
                <input type="radio" id="toggle-staff" name="data-view" class="toggle-radio" value="staff" checked  onclick="updateView('staff')">
                <label for="toggle-staff" class="toggle-button">
                    Staff
                </label>
                
                <!-- Client Radio Button -->
                <input type="radio" id="toggle-client" name="data-view" class="toggle-radio" value="client"  onclick="updateView('client')">
                <label for="toggle-client" class="toggle-button">
                    Client
                </label>
            </div>
        </div>
        <!-- Staff Table -->
<!-- Staff Table -->


        <!-- Dropdowns and Date Picker -->
       <!-- Time Range Dropdown + Apply Button -->
<div class="filter-inputs">


<!-- Status Filter Dropdown -->
<select class="filter-input" id="status-filter">
    <option value="all">All Statuses</option>
    <option value="Booked">Booked</option>
    <option value="Pending">Pending</option>
    <option value="Cancelled">Cancelled</option>
</select>

<!-- Date Range Picker: Separate Start and End -->
<input type="date" id="start-date" class="filter-input" placeholder="Start Date" value="2025-10-13">
<input type="date" id="end-date" class="filter-input" placeholder="End Date" value="2025-10-19">


    <button id="apply-metric" class="filter-input action-buttons">Apply</button>

</div>

    </div>
    
    <!-- Charts Section: 2/3 + 1/3 layout on large screens -->
    <div class="charts-section">
        
        <!-- Left Column: Line/Area Chart Card -->
        <div class="chart-card card" style="width:100%"> <!-- Default to full width on mobile -->
            <h2 id="chart-title" class="chart-title">Activity for Staff Service Hours</h2>
            <div class="chart-canvas-container">
                <canvas id="activityChart"></canvas>
            </div>
            <!-- Custom Legend -->
            <div class="legend-custom">
                <div class="legend-item">
                    <span class="legend-dot dot-booked"></span>
                    <span id="legend-booked">Booked (70)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot dot-pending"></span>
                    <span id="legend-pending">Pending (0)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot dot-cancelled"></span>
                    <span id="legend-cancelled">Cancelled (0)</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Total/Donut Chart Card -->
        <div class="donut-card chart-card card">
            <div class="total-value-box">Total <span id="total-value">70.00</span></div>
            <div class="donut-canvas-container">
                <canvas id="donutChart"></canvas>
                <!-- Center Text Overlay -->
                <!-- <div class="donut-center-text">
                    <span id="donut-percentage" class="donut-percentage text-green-700">100%</span>
                </div> -->
            </div>
            <!-- Single Dominant Legend -->
            <div class="legend-item" style="font-size: 1rem;">
            </div>
        </div>
    </div>




    <!-- Data Table Card -->
    <div class="card table-container" style="margin-top: 45px;">
        <div class="table-responsive">
           <!-- STAFF TABLE -->
<!-- STAFF TABLE -->
        <table id="staff-table" class="data-table">
            <thead class="table-head">
                <tr>
                    <th>Staff Member</th>
                    <th>Booked</th>
                    <th>Pending</th>
                    <th>Cancelled</th>
                    <th class="col-absent-sm-hidden">Absent</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
              @foreach ($staff as $member)
            <tr data-date="{{ $member->date ?? now()->format('Y-m-d') }}">
                <td>{{ $member->name }}</td>
                <td data-hours="{{ $member->booked }}" data-mileage="{{ $member->booked_mileage }}" data-expense="{{ $member->booked_expense }}">${{ $member->booked }}</td>
                <td data-hours="{{ $member->pending }}" data-mileage="{{ $member->pending_mileage }}" data-expense="{{ $member->pending_expense }}">${{ $member->pending }}</td>
                <td data-hours="{{ $member->cancelled }}" data-mileage="{{ $member->cancelled_mileage }}" data-expense="{{ $member->cancelled_expense }}">${{ $member->cancelled }}</td>
                <td>${{ $member->absent }}</td>
                <td>${{ $member->total }}</td>
            </tr>
            @endforeach

            </tbody>
        </table>

        <!-- CLIENT TABLE -->
        <table id="client-table" class="data-table hidden">
            <thead class="table-head">
                <tr>
                    <th>Client Name</th>
                    <th>Booked</th>
                    <th>Pending</th>
                    <th>Cancelled</th>
                    <th class="col-absent-sm-hidden">Absent</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $client)
                <tr data-date="{{ $client->date ?? now()->format('Y-m-d') }}">
                    <td>{{ $client->name }}</td>
                    <td data-hours="{{ $client->booked }}" data-mileage="{{ $client->booked_mileage }}" data-expense="{{ $client->booked_expense }}">${{ $client->booked }}</td>
                    <td data-hours="${{ $client->pending }}" data-mileage="${{ $client->pending_mileage }}" data-expense="${{ $client->pending_expense }}">${{ $client->pending }}</td>
                    <td data-hours="${{ $client->cancelled }}" data-mileage="${{ $client->cancelled_mileage }}" data-expense="${{ $client->cancelled_expense }}">${{ $client->cancelled }}</td>
                    <td>${{ $client->absent }}</td>
                    <td>${{ $client->total }}</td>
                </tr>
                @endforeach

                </tbody>

        </table>


        </div>


<!-- Client Table -->
<table id="client-table" class="hidden">
    <thead>
        <tr>
            <th>Client Name</th>
            <th>Booked</th>
            <th>Pending</th>
            <th>Cancelled</th>
            <th>Absent</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($clients as $client)
            <tr>
                <td>{{ $client->name }}</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
                <td>0</td>
            </tr>
        @endforeach
    </tbody>
</table>
        
        <!-- Table Footer Actions (Print/Download) -->
       
    </div>
</div>

<script>
let activityChartInstance;
let donutChartInstance;

function generateChartLabelsFromRange(startDate, endDate) {
    const labels = [];
    const current = new Date(startDate);
    const end = new Date(endDate);

    while (current <= end) {
        const dayName = current.toLocaleDateString('en-US', { weekday: 'short' });
        const dayNum = current.getDate();
        const monthName = current.toLocaleDateString('en-US', { month: 'short' });
        labels.push(`${dayName}, ${dayNum} ${monthName}`);
        current.setDate(current.getDate() + 1);
    }

    return labels;
}

// Example usage:
const chartLabels = generateChartLabelsFromRange('2025-10-13', '2025-10-19')

// 🔹 Parse data from HTML table
let selectedMetric = 'hours'; // default metric

// 🔹 Apply Metric Button
document.getElementById('apply-metric').addEventListener('click', () => {
    selectedMetric = document.getElementById('metric-select').value;

    const activeTableId = document.getElementById('staff-table').classList.contains('hidden') ? 'client-table' : 'staff-table';

    // 1️⃣ Update table values
    updateTableValues(activeTableId);

    // 2️⃣ Apply status + date filters (hide rows)
    filterTableRows(activeTableId);

    // 3️⃣ Update charts based on visible rows
    updateChartsFromTable(activeTableId);
});



// 🔹 Helper: Parse table for metric
function parseTableData(tableId, onlyVisible = false) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tbody tr');
    const data = [];

    rows.forEach(row => {
        // Skip invisible rows if filtering charts
        if (onlyVisible && row.style.display === 'none') return;

        const cells = row.querySelectorAll('td');
        if (cells.length >= 6) {
            const booked = Number(cells[1].dataset[selectedMetric]) || 0;
            const pending = Number(cells[2].dataset[selectedMetric]) || 0;
            const cancelled = Number(cells[3].dataset[selectedMetric]) || 0;
            const absent = Number(cells[4].textContent.trim()) || 0;

            data.push({
                name: cells[0].textContent.trim(),
                booked,
                pending,
                cancelled,
                absent,
                total: booked + pending + cancelled + absent,
            });
        }
    });

    return data;
}


function updateTableValues(tableId) {
    const table = document.getElementById(tableId);
    table.querySelectorAll('tbody tr').forEach(row => {
        const cells = row.querySelectorAll('td');

        // Update Booked / Pending / Cancelled
        cells[1].textContent = Number(cells[1].dataset[selectedMetric]) || 0;
        cells[2].textContent = Number(cells[2].dataset[selectedMetric]) || 0;
        cells[3].textContent = Number(cells[3].dataset[selectedMetric]) || 0;

        // Update Total
        const booked = Number(cells[1].textContent);
        const pending = Number(cells[2].textContent);
        const cancelled = Number(cells[3].textContent);
        const absent = Number(cells[4].textContent);
        cells[5].textContent = booked + pending + cancelled + absent;
    });
}

function filterTableRows(tableId) {
    const statusFilter = document.getElementById('status-filter').value;
    const startDate = new Date(document.getElementById('start-date').value);
    const endDate = new Date(document.getElementById('end-date').value);

    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');

        // Metric columns
        const booked = Number(cells[1].textContent);
        const pending = Number(cells[2].textContent);
        const cancelled = Number(cells[3].textContent);

        // Date filtering: assume you store a data-date attribute for each row
        const rowDateStr = row.dataset.date; // format "YYYY-MM-DD"
        const rowDate = rowDateStr ? new Date(rowDateStr) : null;

        // Status filtering
        let statusPass = false;
        if (statusFilter === 'all') {
            statusPass = true;
        } else if (statusFilter === 'Booked' && booked > 0) {
            statusPass = true;
        } else if (statusFilter === 'Pending' && pending > 0) {
            statusPass = true;
        } else if (statusFilter === 'Cancelled' && cancelled > 0) {
            statusPass = true;
        }

        // Date filtering
        let datePass = true;
        if (rowDate) {
            datePass = rowDate >= startDate && rowDate <= endDate;
        }

        row.style.display = statusPass && datePass ? '' : 'none';
    });
}

// 🔹 Update Charts
function updateChartsFromTable(tableId) {
    const data = parseTableData(tableId, true); // only visible rows
    const totals = calculateTotals(data);

    const bookedData = data.map(i => i.booked);
    const pendingData = data.map(i => i.pending);
    const cancelledData = data.map(i => i.cancelled);

    if (activityChartInstance && donutChartInstance) {
        // Update line chart
        activityChartInstance.data.datasets[0].data = bookedData;
        activityChartInstance.data.datasets[1].data = pendingData;
        activityChartInstance.data.datasets[2].data = cancelledData;
        activityChartInstance.update();

        // Update donut chart
        donutChartInstance.data.datasets[0].data = [totals.booked, totals.pending, totals.cancelled];
        donutChartInstance.update();
    }

    // Update donut summary text
    updateDonutSummary(totals);
}


// 🔹 Update table cells with data-metric for JS access
window.onload = () => {
    // Inject data attributes for each metric
    ['staff-table', 'client-table'].forEach(tableId => {
        const table = document.getElementById(tableId);
        table.querySelectorAll('tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 6) {
                const name = cells[0].textContent.trim();
                const booked = Number(cells[1].textContent.trim()) || 0;
                const pending = Number(cells[2].textContent.trim()) || 0;
                const cancelled = Number(cells[3].textContent.trim()) || 0;

                // Example: fetch mileage/expense from some JS object or preloaded data
                // For simplicity, using same values; you can replace with actual column values
                cells[1].dataset.mileage = booked;    // replace with actual billing_reports.mileage
                cells[2].dataset.mileage = pending;
                cells[3].dataset.mileage = cancelled;

                cells[1].dataset.expense = booked;    // replace with actual billing_reports.expense
                cells[2].dataset.expense = pending;
                cells[3].dataset.expense = cancelled;
            }
        });
    });

    initializeCharts('staff');
}

// 🔹 Totals calculator
function calculateTotals(data) {
    const total = data.reduce((sum, item) => sum + item.total, 0);
    const booked = data.reduce((sum, item) => sum + item.booked, 0);
    const pending = data.reduce((sum, item) => sum + item.pending, 0);
    const cancelled = data.reduce((sum, item) => sum + item.cancelled, 0);
    return { total, booked, pending, cancelled };
}

// 🔹 Initialize Charts
function initializeCharts(initialType = 'staff') {
    const ctxActivity = document.getElementById('activityChart')?.getContext('2d');
    const ctxDonut = document.getElementById('donutChart')?.getContext('2d');

    if (!ctxActivity || !ctxDonut) return console.error("Chart canvases not found.");

    const data = parseTableData(initialType === 'staff' ? 'staff-table' : 'client-table');
    const totals = calculateTotals(data);
    const bookedData = data.map(i => i.booked);
    const pendingData = data.map(i => i.pending);
    const cancelledData = data.map(i => i.cancelled);

    // 🟢 LINE CHART (Booked, Pending, Cancelled)
    activityChartInstance = new Chart(ctxActivity, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [
                {
                    label: 'Booked',
                    data: bookedData,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(16, 185, 129)',
                },
                {
                    label: 'Pending',
                    data: pendingData,
                    borderColor: 'rgb(251, 191, 36)',
                    backgroundColor: 'rgba(251, 191, 36, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(251, 191, 36)',
                },
                {
                    label: 'Cancelled',
                    data: cancelledData,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.2)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgb(239, 68, 68)',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: true, position: 'bottom' } },
            scales: {
                y: { beginAtZero: true, grid: { drawBorder: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // 🟡 DONUT CHART (Booked, Pending, Cancelled)
    donutChartInstance = new Chart(ctxDonut, {
        type: 'doughnut',
        data: {
            labels: ['Booked', 'Pending', 'Cancelled'],
            datasets: [{
                data: [totals.booked, totals.pending, totals.cancelled],
                backgroundColor: [
                    'rgb(16, 185, 129)',   // Booked - green
                    'rgb(251, 191, 36)',   // Pending - yellow
                    'rgb(239, 68, 68)'     // Cancelled - red
                ],
                hoverOffset: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: { legend: { display: true, position: 'bottom' } },
        }
    });

    updateDonutSummary(totals);
}

// 🔹 Donut Summary
function updateDonutSummary(totals) {
    const totalEl = document.getElementById('total-value');
    const percentEl = document.getElementById('donut-percentage');
    const legendEl = document.getElementById('donut-legend');

    totalEl.textContent = totals.total.toFixed(2);

    if (totals.total > 0) {
        const bookedPercentage = Math.round((totals.booked / totals.total) * 100);
        const pendingPercentage = Math.round((totals.pending / totals.total) * 100);
        const cancelledPercentage = Math.round((totals.cancelled / totals.total) * 100);

        // Show main percentage as booked for simplicity
        percentEl.textContent = `${bookedPercentage}%`;
        percentEl.className = 'donut-percentage text-green-700';

        // Update legend text dynamically
        legendEl.innerHTML = `
            <span class="text-green-700">Booked: ${bookedPercentage}%</span> |
            <span class="text-yellow-500">Pending: ${pendingPercentage}%</span> |
            <span class="text-red-600">Cancelled: ${cancelledPercentage}%</span>
        `;
    } else {
        percentEl.textContent = '0%';
        percentEl.className = 'donut-percentage text-gray-500';
        legendEl.textContent = 'No Data';
    }

    // Update individual legends (if you have them)
    document.getElementById('legend-booked').textContent = `Booked (${totals.booked})`;
    document.getElementById('legend-pending').textContent = `Pending (${totals.pending})`;
    document.getElementById('legend-cancelled').textContent = `Cancelled (${totals.cancelled})`;
}


// 🔹 View Switcher
function updateView(type) {
    // Hide both tables
    document.getElementById('staff-table').classList.add('hidden');
    document.getElementById('client-table').classList.add('hidden');

    // Show selected one
    const activeTableId = type === 'staff' ? 'staff-table' : 'client-table';
    document.getElementById(activeTableId).classList.remove('hidden');

    // Parse and update charts
    const data = parseTableData(activeTableId);
    const totals = calculateTotals(data);

    const bookedData = data.map(i => i.booked);
    const pendingData = data.map(i => i.pending);
    const cancelledData = data.map(i => i.cancelled);

    if (activityChartInstance && donutChartInstance) {
        activityChartInstance.data.datasets[0].data = bookedData;
        activityChartInstance.data.datasets[1].data = pendingData;
        activityChartInstance.data.datasets[2].data = cancelledData;

        donutChartInstance.data.datasets[0].data = [totals.booked, totals.pending, totals.cancelled];

        activityChartInstance.update();
        donutChartInstance.update();
    }

    document.getElementById('chart-title').textContent =
        type === 'staff' ? 'Activity for Staff Service Hours' : 'Activity for Client Engagements';

    updateDonutSummary(totals);
}

window.onload = () => initializeCharts('staff');
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.initCustomDatePicker) return;

        ['start-date','end-date'].forEach(function (id) {
            window.initCustomDatePicker(id);
        });
    });
</script>
