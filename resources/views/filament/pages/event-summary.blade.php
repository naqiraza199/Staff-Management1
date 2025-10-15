<x-filament-panels::page>
<style>
    #staff-table {
  justify-content: space-between;
}
    #client-table {
  justify-content: space-between;
}
#staffNotesChart {
  width: 300px !important;
  height: 300px !important;
}

.report-container {
width: 100%;
  background: white;
  padding: 30px;
  border-radius: 10px;
}

/* Header Styling */
.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 15px;
    margin-bottom: 25px;
    border-bottom: 2px solid #007bff; /* Primary color separator */
}

.report-header h1 {
    font-size: 1.8rem;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.header-subtitle {
    font-weight: 300;
    color: #666;
}

.header-controls {
    display: flex;
    align-items: center;
    gap: 25px;
}

.toggle-button {
    cursor: pointer;
    padding: 10px 20px;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-right: 5px;
    transition: all 0.2s ease;
}

.toggle-button.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.data-view {
    margin-top: 20px;
    width: 100%;
}


.date-range {
    font-size: 0.95rem;
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f8f9fa;
    font-weight: 500;
}

/* Main Body Layout */
.report-body {
    display: flex;
    gap: 30px;
}

.table-section {
    flex: 3; /* More space for the wide table */
}

.chart-section {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding-top: 15px;
}

/* Table Controls */
.table-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    font-size: 0.9rem;
}

.entries-filter select, .search-box input {
    padding: 7px 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 0.9rem;
    outline: none;
}

/* Data Table Styling */
.data-view.hidden {
    display: none;
}

.data-table-wrapper {
    overflow-x: auto;
    border-radius: 5px;
    margin-bottom: 10px;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px; /* Ensures full width feel */
}

.data-table th, .data-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.data-table th {
background-color: #e9f0f7;
  color: #555;
  font-size: 14px;
}

.data-table tbody tr:hover {
    background-color: #f7faff;
}

.data-table td {
   font-size: 12px;
}

.data-table td:first-child {
    color: #333435;
  font-size: 14px;
}


/* Table Footer/Pagination */
.table-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.9rem;
    color: #666;
    padding: 10px 0;
}

.pagination-btn {
    padding: 8px 12px;
    margin-left: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
    cursor: pointer;
    transition: background-color 0.2s, box-shadow 0.2s;
}

.pagination-btn:not(.active):hover {
    background-color: #f0f0f0;
}

.pagination-btn.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
    font-weight: 600;
}

/* Scrollbar Placeholder */
.horizontal-scrollbar {
    height: 6px;
    background-color: #e0e0e0;
    position: relative;
    border-radius: 3px;
    margin-bottom: 10px;
}

.horizontal-scrollbar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 5%;
    width: 40%;
    height: 100%;
    background-color: #888;
    border-radius: 3px;
}

/* Chart Area Styling (Working Placeholder) */
.pie-chart-placeholder {
    position: relative;
    width: 250px;
    height: 250px;
    border-radius: 50%;
    /* Gradient for the purple color shown in the image */
    background: conic-gradient(#8a2be2 0%, #a020f0 100%); 
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 0 20px rgba(138, 43, 226, 0.4);
}

.pie-chart-inner {
    width: 70%;
    height: 70%;
    background-color: white;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    /* This creates the inner white ring border */
    border: 15px solid white;
    box-sizing: border-box;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05) inset;
}

.pie-chart-text {
    font-size: 1.5rem;
    font-weight: 800;
    color: #6a5acd; /* Darker purple */
}

.progress-note-label {
    position: absolute;
    top: 5px;
    right: -100px;
    background-color: white;
    padding: 6px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    font-size: 0.9rem;
    white-space: nowrap;
}

/* Utility Icons (Print/Download) */
.report-utilities {
    text-align: right;
    margin-top: 25px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.report-utilities i {
    font-size: 1.4rem;
    color: #666;
    margin-left: 20px;
    cursor: pointer;
    transition: color 0.2s;
}

.report-utilities i:hover {
    color: #007bff;
}
</style>

  <div class="report-container">
        <header class="report-header">
            <h1> <span class="header-subtitle">Event</span></h1>
            <div class="header-controls">
               <div class="toggle-group" id="entity-toggle">
                    <label class="toggle-button active" data-target="client">
                        <input type="radio" name="entity" value="client" checked>
                        <i class="fas fa-user-tie"></i> Client
                    </label>
                    <label class="toggle-button" data-target="staff">
                        <input type="radio" name="entity" value="staff">
                        <i class="fas fa-users"></i> Staff
                    </label>
                </div>
            </div>
        </header>

        <div class="report-body">
            <div class="table-section">
                <div class="table-controls">
                    <div class="search-box">
                        <label for="search">Search:</label>
                        <input type="text" id="search">
                    </div>
                </div>

         
<div id="client-table" class="data-view active" style="display:flex;">
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Injuries</th>
                        <th>Feedbacks</th>
                        <th>Enquiries</th>
                        <th>Incidents</th>
                        <th>Progress Notes</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr class="name-row">
                            <td>{{ $client->display_name }}</td>
                            <td>{{ $client->note_counts['Injury'] }}</td>
                            <td>{{ $client->note_counts['Feedback'] }}</td>
                            <td>{{ $client->note_counts['Enquiry'] }}</td>
                            <td>{{ $client->note_counts['Incident'] }}</td>
                            <td>{{ $client->note_counts['Progress Notes'] }}</td>
                            <td>{{ $client->note_counts['Total'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No Clients Found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
                 
    </div>

    {{-- STAFF TABLE --}}
    <div id="staff-table" class="data-view" style="display:none;">
        <div class="data-table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Staff Name</th>
                        <th>Injuries</th>
                        <th>Feedbacks</th>
                        <th>Enquiries</th>
                        <th>Incidents</th>
                        <th>Progress Notes</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                        <tr class="name-row">
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->note_counts['Injury'] }}</td>
                            <td>{{ $member->note_counts['Feedback'] }}</td>
                            <td>{{ $member->note_counts['Enquiry'] }}</td>
                            <td>{{ $member->note_counts['Incident'] }}</td>
                            <td>{{ $member->note_counts['Progress Notes'] }}</td>
                            <td>{{ $member->note_counts['Total'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No Staff Found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
          
    </div>

                <div class="table-footer" style="display:none">
                    <div class="entries-info">
                        Showing 1 to 3 of 3 entries
                    </div>
                    <div class="pagination">
                        <button class="pagination-btn">Previous</button>
                        <button class="pagination-btn active">1</button>
                        <button class="pagination-btn">Next</button>
                    </div>
                </div>
            </div>


<div class="mt-10 flex justify-center">
    <div >
        <h3 id="chartTitle" class="text-lg font-semibold mb-4 text-center">Client Notes Overview</h3>
        <canvas id="notesChart" height="300"></canvas>
    </div>
</div>
          

        </div>

        <div class="report-utilities">
            <i class="fas fa-print"></i>
            <i class="fas fa-download"></i>
        </div>
    </div> 
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const noteTypes = ['Injury', 'Feedback', 'Enquiry', 'Incident', 'Progress Notes'];
    const colors = ['#ef4444', '#3b82f6', '#f59e0b', '#10b981', '#8b5cf6'];

    // 🟢 Client totals from backend
    const clientTotals = @json(
        $clients->reduce(function ($carry, $client) {
            foreach ($client->note_counts as $type => $count) {
                $carry[$type] = ($carry[$type] ?? 0) + $count;
            }
            return $carry;
        }, [])
    );

    // 🔵 Staff totals from backend
    const staffTotals = @json(
        $staff->reduce(function ($carry, $user) {
            foreach ($user->note_counts as $type => $count) {
                $carry[$type] = ($carry[$type] ?? 0) + $count;
            }
            return $carry;
        }, [])
    );

    const ctx = document.getElementById('notesChart').getContext('2d');
    const chartTitle = document.getElementById('chartTitle');

    // 🥧 initialize with client data
    const clientData = noteTypes.map(t => clientTotals[t] || 0);
    const staffData = noteTypes.map(t => staffTotals[t] || 0);

    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: noteTypes,
            datasets: [{
                data: clientData,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: (context) => {
                            const dataset = context.chart.data.datasets[0].data;
                            const total = dataset.reduce((a, b) => a + b, 0);
                            const value = context.raw || 0;
                            const percent = total ? ((value / total) * 100).toFixed(1) : 0;
                            return `${context.label}: ${value} (${percent}%)`;
                        }
                    }
                }
            }
        }
    });

    // 🟣 Toggle logic — match your tabs/buttons
    const clientBtn = document.querySelector('[data-target="client"]');
    const staffBtn = document.querySelector('[data-target="staff"]');

    clientBtn.addEventListener('click', () => {
        updateChart(clientData, 'Client Notes Overview');
    });

    staffBtn.addEventListener('click', () => {
        updateChart(staffData, 'Staff Notes Overview');
    });

    // 🔁 Update chart function
    function updateChart(newData, title) {
        chart.data.datasets[0].data = newData;
        chartTitle.textContent = title;
        chart.update();
    }
});
</script>


  <script>
document.querySelectorAll('.toggle-button').forEach(button => {
    button.addEventListener('click', () => {
        // Remove active class from all buttons
        document.querySelectorAll('.toggle-button').forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        button.classList.add('active');

        // Hide all data views
        document.querySelectorAll('.data-view').forEach(view => view.style.display = 'none');

        // Show target view
        const target = button.getAttribute('data-target');
        const targetDiv = document.getElementById(`${target}-table`);
        if (targetDiv) targetDiv.style.display = 'flex';
    });
});
</script>

</x-filament-panels::page>
