<x-filament-panels::page>

    <style>
        /* --- Colors & Typography --- */
        :root {
            --primary-blue: #1D4ED8;
            --secondary-slate: #475569;
            --bg-light: #F9FAFB; /* Gray-50 */
            --green-header: #059669; /* Tailwind green-600 */
            --green-text: #047857;  /* Tailwind green-700 */
            --indigo-header: #4F46E5; /* Tailwind indigo-600 */
            --indigo-text: #4338CA;  /* Tailwind indigo-700 */
            --amber-text: #D97706;  /* Tailwind amber-600 */
            --red-text: #EF4444;    /* Tailwind red-500 */
        }

       
        
        /* --- Main Container --- */
        .report-container {
            max-width: 100%;/* max-w-7xl equivalent */
            margin: 0 auto;
            background-color: white;
            border-radius: 0.75rem; /* rounded-xl */
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); /* shadow-2xl */
            overflow: hidden;
            transition: all 0.3s ease-in-out;
        }

        /* --- Header Section --- */
        .header {
            background-color: var(--primary-blue);
            color: white;
            padding: 1.5rem; /* p-6 */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); /* shadow-md */
        }
        .header h1 {
            font-size: 1.5rem; /* text-2xl */
            font-weight: 700;
            letter-spacing: -0.025em; /* tracking-tight */
        }
        .header p {
            font-size: 0.875rem; /* text-sm */
            opacity: 0.8;
            margin-top: 0.25rem; /* mt-1 */
        }

        /* --- Controls Section --- */
        .controls {
            padding: 1.5rem; /* p-6 */
            border-bottom: 1px solid #E5E7EB; /* border-b border-gray-200 */
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: flex-start;
            gap: 1rem; /* space-y-4 */
        }

        /* Custom Input/Select Styling */
        .custom-control {
            position: relative;
            width: 100%;
        }
        .custom-select, .custom-date {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-color: white;
            border: 1px solid #D1D5DB; /* border-gray-300 */
            border-radius: 0.5rem; /* rounded-lg */
            padding: 0.5rem 1rem; /* py-2 px-4 */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
            outline: none;
            width: 100%;
            transition: all 0.15s ease-in-out;
        }
        .custom-select:focus, .custom-date:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 2px rgba(29, 78, 216, 0.5); /* ring-2 ring-primary-blue */
        }
        .icon-container {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            display: flex;
            align-items: center;
            padding-right: 0.5rem;
            pointer-events: none;
            color: #4B5563; /* text-gray-700 */
        }
        .icon-container svg {
            width: 1rem;
            height: 1rem;
        }

        /* --- Desktop Table View --- */
        .table-desktop {
            display: none; /* hidden md:block */
        }
        .table-desktop .overflow-x-auto {
            overflow-x: auto;
        }
        .data-table {
            min-width: 100%;
            border-collapse: collapse;
            border-bottom: 1px solid #E5E7EB;
        }
        .data-table thead {
            background-color: #F9FAFB; /* bg-gray-50 */
        }
        .data-table tbody {
            background-color: white;
            border-top: 1px solid #E5E7EB;
        }
        .data-table th, .data-table td {
            padding: 1rem 1.5rem; /* px-6 py-4 */
            font-size: 0.875rem; /* text-sm */
            white-space: nowrap;
        }
        
        /* Table Header Grouping */
        .header-group th {
            text-align: center;
            padding: 0.5rem 1.5rem; /* px-6 py-2 */
            font-size: 0.875rem; /* text-sm */
            font-weight: 700;
            color: white;
        }
        .header-group .amount-header {
            background-color: var(--green-header);
            border-right: 1px solid #374151; /* border-r border-gray-700 */
            padding: 12px;
        }
        .header-group .hours-header {
            background-color: var(--indigo-header);
        }
        
        /* Table Sub-Headers */
        .data-table thead tr:nth-child(2) th {
            text-align: right;
            font-size: 0.75rem; /* text-xs */
            font-weight: 500;
            color: #6B7280; /* text-gray-500 */
            text-transform: uppercase;
            letter-spacing: 0.05em; /* tracking-wider */
        }
        .data-table thead tr:nth-child(2) th:first-child {
            text-align: left;
            font-weight: 600;
            color: var(--secondary-slate);
            background-color: #F9FAFB;
        }
        .data-table thead tr:nth-child(2) th:nth-child(4) {
            border-right: 1px solid #888080; /* border-r border-gray-200 */
        }

        /* Sticky Column for Client */
        .data-table tbody td:first-child {
            position: sticky;
            left: 0;
            background-color: white;
            font-weight: 500;
            color: #1F2937; /* text-gray-900 */
            z-index: 10;
        }
        
        /* Table Row Styling */
        .data-table tbody tr {
            border-top: 1px solid #E5E7EB;
            transition: background-color 0.15s ease-in-out;
        }
        .data-table tbody tr:hover td:first-child,
        .data-table tbody tr:hover {
            background-color: #EFF6FF; /* hover:bg-blue-50/50 */
        }

        /* Data Colors */
        .data-table .amount-delivered { color: var(--green-text); }
        .data-table .hours-delivered { color: var(--indigo-text); font-weight: 500; }
        .data-table .data-invoiced { color: var(--secondary-slate); }
        .data-table .data-cancelled { color: var(--red-text); }
        .data-table .data-non-invoiced { color: var(--amber-text); }
        
        .data-table .data-non-invoiced.amount { border-right: 1px solid #888080; }
        .data-table .data-non-invoiced.hours { font-weight: 500; }

        /* Total Row */
        .total-row {
            font-weight: 700;
            background-color: #ffffffff; /* bg-blue-50 */
            border-top: 2px solid var(--primary-blue);
        }
        .total-row td:first-child {
            color: var(--primary-blue);
            background-color: #EFF6FF;
        }
        
        /* --- Footer / Actions Section --- */
        .footer {
            padding: 1.5rem; /* p-6 */
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 0.75rem; /* space-x-3 */
            background-color: #F9FAFB; /* bg-gray-50 */
            border-top: 1px solid #E5E7EB; /* border-t border-gray-200 */
        }
        .footer button {
            display: flex;
            align-items: center;
            padding: 0.75rem; /* p-3 */
            border-radius: 9999px; /* rounded-full */
            transition: all 0.15s ease-in-out;
            outline: none;
        }
        .footer button:focus {
            box-shadow: 0 0 0 2px rgba(29, 78, 216, 0.3); /* focus:ring-2 focus:ring-primary-blue/50 */
        }

        .print-btn {
            background-color: white;
            color: #4B5563; /* text-gray-600 */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); /* shadow-md */
        }
        .print-btn:hover {
            color: var(--primary-blue);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1); /* hover:shadow-lg */
        }

        .download-btn {
            background-color: var(--primary-blue);
            color: white;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); /* shadow-xl */
        }
        .download-btn:hover {
            background-color: #1E40AF; /* hover:bg-blue-800 */
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); /* hover:shadow-2xl */
        }

        /* --- Mobile Responsiveness (Pure CSS) --- */
        .table-mobile {
            display: none;
        }

        @media (min-width: 768px) {
            /* Desktop Styles */
         
            .controls {
                flex-direction: row;
                align-items: center;
                gap: 1rem; /* space-x-4 */
            }
            .custom-control {
                width: auto;
            }
            .custom-select {
                width: 8rem; /* md:w-32 */
            }
            .custom-date {
                width: 14rem; /* md:w-56 */
            }
            .table-desktop {
                display: block;
            }
        }
        
        @media (max-width: 767px) {
            /* Mobile Styles */
            .table-desktop {
                display: none;
            }
            .table-mobile {
                display: block;
                padding: 1rem; /* p-4 */
                gap: 1rem; /* space-y-4 */
            }
            
            .mobile-heading {
                font-size: 1.25rem; /* text-xl */
                font-weight: 700;
                color: var(--secondary-slate);
                margin-bottom: 1rem; /* mb-4 */
            }
            
            .client-row {
                display: flex;
                flex-direction: column;
                padding: 1rem;
                margin-bottom: 1rem;
                background-color: #ffffff;
                border-radius: 0.75rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
            }
            .client-row h3 {
                font-size: 1.125rem; /* text-lg */
                font-weight: 700;
                color: #1F2937; /* text-gray-900 */
                margin-bottom: 0.5rem; /* mb-2 */
                padding-bottom: 0.5rem; /* pb-2 */
                border-bottom: 1px solid #E5E7EB;
            }
            .data-cell {
                padding: 0.5rem 0;
                display: flex;
                justify-content: space-between;
                border-bottom: 1px dashed #E5E7EB;
            }
            .data-cell:last-of-type {
                border-bottom: none;
            }
            .data-cell-label {
                font-weight: 500;
                color: #6B7280;
                padding: 0 0.5rem; /* px-2 */
                border-radius: 9999px; /* rounded-full */
            }
            .data-cell-value {
                font-weight: 600;
                text-align: right;
            }
            
            /* Mobile Card Specific Styling */
            .client-row .data-cell:nth-child(2) .data-cell-label { background-color: #D1FAE5; } /* Delivered Amount */
            .client-row .data-cell:nth-child(5) .data-cell-label { background-color: #E0E7FF; } /* Non Invoiced Amount (no label) */
            
            .client-row .data-cell:nth-child(6) .data-cell-label { background-color: #E0E7FF; } /* Delivered Hours */
            .client-row .data-cell:nth-child(9) .data-cell-label { background-color: #FEF3C7; } /* Non Invoiced Hours */
            
            /* Separator */
            .mobile-separator {
                margin-top: 0.75rem; /* my-3 */
                margin-bottom: 0.75rem;
                border: 0;
                border-top: 1px solid #C7D2FE; /* border-indigo-200 */
            }

            /* Total Card */
            .total-card {
                border-top: 4px solid var(--primary-blue);
                background-color: #EFF6FF; /* bg-blue-50 */
            }
            .total-card h3 {
                font-size: 1.25rem; /* text-xl */
                font-weight: 800;
                color: var(--primary-blue);
            }
            .total-card .data-cell:nth-child(2) .data-cell-label { background-color: #BBF7D0; }
            .total-card .data-cell:nth-child(6) .data-cell-label { background-color: #C7D2FE; }

            /* Color mapping for mobile card values */
            .mobile-amount-delivered { color: var(--green-text); }
            .mobile-data-invoiced { color: var(--secondary-slate); }
            .mobile-data-cancelled { color: var(--red-text); }
            .mobile-data-non-invoiced { color: var(--amber-text); }
            .mobile-hours-delivered { color: var(--indigo-text); }
        }
        .desktop-header th {
                text-align: center;
                vertical-align: middle;
            }

            .header-group th {
                background: #403f3c;
                font-weight: 700;
                font-size: 0.9rem;
            }

            .amount-header,
            .hours-header {
                text-align: center;
                font-weight: 600;
                border-bottom: 2px solid #ddd;
            }

            .sub-header {
                background: #fafafa;
                font-weight: 500;
                text-align: center;
                border-bottom: 1px solid #eee;
            }

            .client-header {
                background: #f9fafb;
                text-align: left;
                font-weight: 600;
            }

    </style>
    <!-- Inter Font Import -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icon Library (Lucide Icons) -->
    <script src="https://unpkg.com/lucide@latest"></script>


    <!-- Main Report Container (Awesome Design) -->
    <div class="report-container">



        <!-- Controls Section -->
     
        
        <!-- Desktop Table View (visible on screen size >= 768px) -->
        <div style="padding: 35px;" class="table-desktop">
            <div style="border-radius: 10px;" class="overflow-x-auto">
                <table class="data-table">
                    <!-- Table Header Group --> 
                   <thead class="desktop-header">
                        <tr class="header-group">
                            <th scope="col" rowspan="2" class="client-header">CLIENTS</th>

                            <!-- Group Headers -->
                            <th scope="colgroup" colspan="4" class="amount-header text-center">AMOUNTS</th>
                            <th scope="colgroup" colspan="4" class="hours-header text-center">HOURS</th>
                        </tr>
                        <tr>
                            <!-- Amount Sub-Headers -->
                            <th scope="col" class="sub-header">Delivered Amount</th>
                            <th scope="col" class="sub-header">Invoiced Amount</th>
                            <th scope="col" class="sub-header">Cancelled Amount</th>
                            <th scope="col" class="sub-header">Non Invoiced Amount</th>

                            <!-- Hours Sub-Headers -->
                            <th scope="col" class="sub-header">Delivered Hours</th>
                            <th scope="col" class="sub-header">Invoiced Hours</th>
                            <th scope="col" class="sub-header">Cancelled Hours</th>
                            <th scope="col" class="sub-header">Non Invoiced Hours</th>
                        </tr>
                    </thead>

                    
                    <!-- Table Body -->
                    <tbody>
                         @foreach ($clients as $client)
                                <tr>
                                    <td>{{ $client['name'] }}</td>

                                    <!-- AMOUNTS -->
                                    <td class="amount-delivered">${{ number_format($client['delivered_amount'], 2) }}</td>
                                    <td class="data-invoiced">${{ number_format($client['invoiced_amount'], 2) }}</td>
                                    <td class="data-cancelled">${{ number_format($client['cancelled_amount'], 2) }}</td>
                                    <td class="data-non-invoiced amount">${{ number_format($client['non_invoiced_amount'], 2) }}</td>

                                    <!-- HOURS (static for now, until you define logic) -->
                                    <td class="hours-delivered">{{ number_format($client['delivered_hours'], 2) }}</td>
                                    <td class="data-invoiced">{{ number_format($client['invoiced_hours'], 2) }}</td>
                                    <td class="data-cancelled">{{ number_format($client['cancelled_hours'], 2) }}</td>
                                    <td class="data-non-invoiced hours">{{ number_format($client['non_invoiced_hours'], 2) }}</td>
                                </tr>
                            @endforeach

                       

                        <!-- Total Row -->
                       <!-- Total Row -->
                            <tr class="total-row">
                                <td>Total</td>

                                <!-- Amount Totals -->
                                <td class="amount-delivered">${{ number_format($totals['delivered'], 2) }}</td>
                                <td class="data-invoiced">${{ number_format($totals['invoiced'], 2) }}</td>
                                <td class="data-cancelled">${{ number_format($totals['cancelled'], 2) }}</td>
                                <td class="data-non-invoiced amount">${{ number_format($totals['non_invoiced'], 2) }}</td>

                                <!-- Hours Totals (still static for now) -->
                                <td class="hours-delivered">{{ number_format($totals['delivered_hours'], 2) }}</td>
                                <td class="data-invoiced">{{ number_format($totals['invoiced_hours'], 2) }}</td>
                                <td class="data-cancelled">{{ number_format($totals['cancelled_hours'], 2) }}</td>
                                <td class="data-non-invoiced hours">{{ number_format($totals['non_invoiced_hours'], 2) }}</td>
                            </tr>

                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Mobile Card View (visible on screen size < 768px) -->
        <div class="table-mobile">
            <h2 class="mobile-heading">Client Details</h2>

            <!-- Mobile Card 1: Julie Bridge -->
            <div class="client-row">
                <h3>Julie Bridge</h3>
                <div class="data-cell">
                    <span class="data-cell-label">Delivered Amount</span>
                    <span class="data-cell-value mobile-amount-delivered">$1,419.37</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label">Invoiced Amount</span>
                    <span class="data-cell-value mobile-data-invoiced">$511.63</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-cancelled">Cancelled Amount</span>
                    <span class="data-cell-value mobile-data-cancelled">$0.00</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-non-invoiced">Non Invoiced Amount</span>
                    <span class="data-cell-value mobile-data-non-invoiced">$907.74</span>
                </div>
                <hr class="mobile-separator">
                <div class="data-cell">
                    <span class="data-cell-label">Delivered Hours</span>
                    <span class="data-cell-value mobile-hours-delivered">16</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label">Invoiced Hours</span>
                    <span class="data-cell-value mobile-data-invoiced">7</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-cancelled">Cancelled Hours</span>
                    <span class="data-cell-value mobile-data-cancelled">0</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-non-invoiced">Non Invoiced Hours</span>
                    <span class="data-cell-value mobile-data-non-invoiced">9</span>
                </div>
            </div>

            <!-- Mobile Card 2: Zane Davey-Newman -->
            <div class="client-row">
                <h3>Zane Davey-Newman</h3>
                <div class="data-cell">
                    <span class="data-cell-label">Delivered Amount</span>
                    <span class="data-cell-value mobile-amount-delivered">$2,192.70</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label">Invoiced Amount</span>
                    <span class="data-cell-value mobile-data-invoiced">$0.00</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-cancelled">Cancelled Amount</span>
                    <span class="data-cell-value mobile-data-cancelled">$0.00</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-non-invoiced">Non Invoiced Amount</span>
                    <span class="data-cell-value mobile-data-non-invoiced">$2,192.70</span>
                </div>
                <hr class="mobile-separator">
                <div class="data-cell">
                    <span class="data-cell-label">Delivered Hours</span>
                    <span class="data-cell-value mobile-hours-delivered">30</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label">Invoiced Hours</span>
                    <span class="data-cell-value mobile-data-invoiced">0</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-cancelled">Cancelled Hours</span>
                    <span class="data-cell-value mobile-data-cancelled">0</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-non-invoiced">Non Invoiced Hours</span>
                    <span class="data-cell-value mobile-data-non-invoiced">30</span>
                </div>
            </div>
            
            <!-- Mobile Card 3: Total -->
            <div class="client-row total-card">
                <h3>Total</h3>
                <div class="data-cell">
                    <span class="data-cell-label">Delivered Amount</span>
                    <span class="data-cell-value mobile-amount-delivered">$3,612.07</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label">Invoiced Amount</span>
                    <span class="data-cell-value mobile-data-invoiced">$511.63</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-cancelled">Cancelled Amount</span>
                    <span class="data-cell-value mobile-data-cancelled">$0.00</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-non-invoiced">Non Invoiced Amount</span>
                    <span class="data-cell-value mobile-data-non-invoiced">$3,100.44</span>
                </div>
                <hr class="mobile-separator">
                <div class="data-cell">
                    <span class="data-cell-label">Delivered Hours</span>
                    <span class="data-cell-value mobile-hours-delivered">46</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label">Invoiced Hours</span>
                    <span class="data-cell-value mobile-data-invoiced">7</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-cancelled">Cancelled Hours</span>
                    <span class="data-cell-value mobile-data-cancelled">0</span>
                </div>
                <div class="data-cell">
                    <span class="data-cell-label mobile-data-non-invoiced">Non Invoiced Hours</span>
                    <span class="data-cell-value mobile-data-non-invoiced">39</span>
                </div>
            </div>

        </div>

        <!-- Footer / Actions Section -->
        <footer class="footer">
            <!-- Print Button -->
            <!-- <button class="print-btn">
                <i data-lucide="printer"></i>
            </button> -->
            <!-- Download Button -->
           <button wire:click="downloadReport" class="download-btn ">
                <i data-lucide="download"></i> 
            </button>



        </footer>

    </div>
    <div id="report-section">
    <!-- Your table HTML -->
</div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>



</x-filament-panels::page>
