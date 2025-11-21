<x-filament-panels::page>

    <!-- Load Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <!-- Load Chart.js for the Performance Graph -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* -------------------------------------------
           1. BASE STYLES & LAYOUT
           ------------------------------------------- */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        
        :root {
            --color-primary: #3b82f6; /* Blue */
            --color-bg-light: #f7f9fc;
            --color-text-dark: #1f2937;
            --color-cyan: #06b6d4;
            --color-green: #10b981;
            --color-amber: #f59e0b;
            --color-red: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-bg-light);
            display: flex;
            min-height: 100vh;
            margin: 0;
        }

        /* Reusable Card Styling */
        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 24px;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

       
        /* -------------------------------------------
           3. MAIN CONTENT & HEADER
           ------------------------------------------- */
        #main-content {
            flex: 1;
            overflow-y: auto;
        }


        .search-container {
            position: relative;
            display: none; /* hidden on mobile */
        }

        .search-container input {
            padding: 8px 10px 8px 40px;
            border: 1px solid #e5e7eb;
            border-radius: 9999px; /* rounded-full */
            transition: border-color 0.15s, box-shadow 0.15s;
            width: 192px; /* w-48 */
        }

        .search-container input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        }

        .search-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af; /* text-gray-400 */
        }

        .icon-button {
            padding: 0.75rem; /* p-3 */
            color: #4b5563; /* text-gray-600 */
            background-color: #f3f4f6; /* bg-gray-100 */
            border-radius: 9999px;
            transition: color 0.15s;
            border: none;
            cursor: pointer;
        }

        .icon-button:hover {
            color: var(--color-primary);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem; /* space-x-2 */
            cursor: pointer;
        }

        .user-profile-avatar {
            width: 40px;
            height: 40px;
            background-color: #bfdbfe; /* bg-blue-200 */
            color: #1e40af; /* text-blue-800 */
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-profile span {
            color: #374151; /* text-gray-700 */
            display: none; /* hidden on mobile */
        }

        /* -------------------------------------------
           4. KPI CARDS (METRICS)
           ------------------------------------------- */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem; /* gap-6 */
        }
        
        .kpi-card {
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            background-color: white;
            border-bottom: 4px solid;
        }

        .kpi-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .kpi-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280; /* text-gray-500 */
            margin-bottom: 4px;
        }

        .kpi-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--color-text-dark);
        }

        /* KPI Specific Colors */
        .kpi-invoiced { border-color: var(--color-cyan); }
        .kpi-invoiced .kpi-icon { background-color: #ccf7fe; color: var(--color-cyan); }

        .kpi-booked { border-color: var(--color-green); }
        .kpi-booked .kpi-icon { background-color: #d1fae5; color: var(--color-green); }

        .kpi-pending { border-color: var(--color-amber); }
        .kpi-pending .kpi-icon { background-color: #fef3c7; color: var(--color-amber); }

        .kpi-cancelled { border-color: var(--color-red); }
        .kpi-cancelled .kpi-icon { background-color: #fee2e2; color: var(--color-red); }

        /* -------------------------------------------
           5. CHART & FILTERS
           ------------------------------------------- */
    

        .chart-header {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .chart-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .filter-controls {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .filter-controls select,
        .filter-controls input {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: border-color 0.15s;
        }

        .filter-controls input {
            text-align: center;
            width: 160px;
            color: #4b5563;
        }

        .filter-controls select:focus,
        .filter-controls input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 1px var(--color-primary);
        }

        #performance-chart-container {
            position: relative;
            height: 300px;
        }


        /* -------------------------------------------
           6. DATA TABLE
           ------------------------------------------- */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px; /* Ensure horizontal scrolling on small screens */
        }

        .data-table thead {
            background-color: #f9fafb;
        }

        .data-table th {
            padding: 12px 24px;
            text-align: left;
            font-size: 0.75rem; /* text-xs */
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table td {
            padding: 16px 24px;
            white-space: nowrap;
            font-size: 0.875rem;
            color: #4b5563;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table td.font-medium {
            font-weight: 500;
            color: #1f2937;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            padding: 6px 12px;
            font-size: 0.75rem;
            line-height: 1;
            font-weight: 600;
            border-radius: 5px;
        }

        .buti{
            display: inline-flex;
            padding: 13px 17px;
            font-size: 0.75rem;
            line-height: 1;
            font-weight: 600;
            border-radius: 5px;
            background-color: #06B6D4; color: #ffffffff;
        }

        .status-invoiced { background-color: #06B6D4; color: #ffffffff; } /* blue */
        .status-booked { background-color: #10B981; color: #ffffffff; } /* green */
        .status-cancelled { background-color: #EF4444; color: #ffffffff; } /* green */
        .status-pending { background-color: #F59E0B; color: #ffffffff; } /* green */

        /* -------------------------------------------
           7. MEDIA QUERIES (RESPONSIVENESS)
           ------------------------------------------- */
        
        /* Tablet/Small Desktop (sm breakpoint: 640px) */
        @media (min-width: 640px) {
    

            .search-container {
                display: block;
            }
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .chart-header {
                flex-direction: row;
                align-items: center;
            }
            .chart-header h3 {
                margin-bottom: 0;
            }
        }

        /* Desktop (lg breakpoint: 1024px) */
        @media (min-width: 1024px) {
          
            body {
                display: flex;
            }
            .kpi-grid {
                grid-template-columns: repeat(4, 1fr);
            }
            .user-profile span {
                display: inline;
            }
            #header h1 {
                font-size: 1.875rem;
            }
        }
        
      
    </style>


    <!-- 2. MAIN CONTENT AREA -->
    <main id="main-content">
        
        <!-- Report Section -->
        <section style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- 3. METRIC CARDS (KPIs) -->
            <div class="kpi-grid">

        <div class="kpi-card kpi-invoiced">
                <div class="kpi-content">
                    <div class="kpi-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <p class="kpi-label">INVOICED</p>
                        <p class="kpi-value">${{ number_format($this->totals['Invoiced'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="kpi-card kpi-booked">
                <div class="kpi-content">
                    <div class="kpi-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <p class="kpi-label">BOOKED</p>
                        <p class="kpi-value">${{ number_format($this->totals['Booked'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="kpi-card kpi-pending">
                <div class="kpi-content">
                    <div class="kpi-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <p class="kpi-label">PENDING</p>
                        <p class="kpi-value">${{ number_format($this->totals['Pending'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="kpi-card kpi-cancelled">
                <div class="kpi-content">
                    <div class="kpi-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <p class="kpi-label">CANCELLED</p>
                        <p class="kpi-value">${{ number_format($this->totals['Cancelled'], 2) }}</p>
                    </div>
                </div>
            </div>



            </div>

            <!-- 4. PERFORMANCE CHART AND FILTERS -->
            <div class="card chart-container-wrapper">
             <div class="chart-header">
                            <h3>Performance over Time</h3>
                            <form method="GET" action="{{ route('filament.admin.pages.performance') }}" class="filter-controls flex gap-2 items-center">
                                <!-- Status Filter -->
                                <select name="status" class="border rounded px-2 py-1">
                                    <option value="">Filter by Status...</option>
                                    <option value="Invoiced" {{ request('status') === 'Invoiced' ? 'selected' : '' }}>Invoiced</option>
                                    <option value="Booked" {{ request('status') === 'Booked' ? 'selected' : '' }}>Booked</option>
                                    <option value="Cancelled" {{ request('status') === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                                </select>

                                <!-- Date Range Inputs -->
                                <input
                                    type="date"
                                    name="start_date"
                                    id="start-date"
                                    value="{{ request('start_date') }}"
                                    class="border rounded px-2 py-1"
                                >

                                <input
                                    type="date"
                                    id="end-date"
                                    name="end_date"
                                    value="{{ request('end_date') }}"
                                    class="border rounded px-2 py-1"
                                >

                                <!-- Apply Button -->
                                <button type="submit" class="buti">
                                    Apply
                                </button>
                            </form>
                        </div>


                
                <div id="performance-chart-container">
                     <canvas id="performanceChart" height="300"></canvas>
                </div>
            </div>

            <!-- 5. DATA TABLE -->
            <div class="card">
                <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Recent Shifts</h3>
                
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shift</th>
                                <th>Client</th>
                                <th>Area</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                             @foreach ($billingRecords as $record)
                                    <tr>
                                        <td class="font-medium">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                                        <td>{{ $record->client_name }}</td>
                                        <td>{{ $record->client_name ?? 'N/A' }}</td>
                                        <td>Archived</td>
                                        <td>
                                            <span class="status-badge status-{{ strtolower($record->status) }}">
                                                {{ $record->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            const chartData = @json($chartData);

            const colorMap = {
                'Booked': '#10b981',
                'Invoiced': '#06b6d4',
                'Cancelled': '#EF4444',
                'Pending': '#f59e0b'
            };

            const createGradient = (color) => {
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                const hexToRgba = (hex, alpha) => {
                    const r = parseInt(hex.slice(1, 3), 16);
                    const g = parseInt(hex.slice(3, 5), 16);
                    const b = parseInt(hex.slice(5, 7), 16);
                    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                };
                gradient.addColorStop(0, hexToRgba(color, 0.5));
                gradient.addColorStop(1, hexToRgba(color, 0));
                return gradient;
            };

            const datasets = Object.keys(chartData.datasets).map(status => ({
                label: status + ' ($)',
                data: chartData.datasets[status],
                borderColor: colorMap[status],
                backgroundColor: createGradient(colorMap[status]),
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: colorMap[status],
            }));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { usePointStyle: true, padding: 20 }
                        },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(200, 200, 200, 0.2)' },
                            ticks: { callback: value => '$' + value }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.initCustomDatePicker) return;

        ['start-date','end-date'].forEach(function (id) {
            window.initCustomDatePicker(id);
        });
    });
</script>



</x-filament-panels::page>
