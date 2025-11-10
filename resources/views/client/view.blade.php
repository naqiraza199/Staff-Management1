<!DOCTYPE html>

    <!-- Importing a modern font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <style>
        /* Base Reset and Typography */
        :root {
            --primary-color: #3b82f6; /* Modern Blue */
            --secondary-color: #6b7280; /* Gray for borders/text */
            --background-color: #f9fafb;
            --surface-color: #ffffff;
            --shadow-light: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-medium: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-radius: 0.5rem;
        }



        /* --- Layout --- */
        .page-header {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            margin-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .page-content {
           margin-top: -25px;
        }

        .main-content {
           display: flex;
            flex-direction: column;
            gap: 20px;
           width: 100%;
            margin-left: 21px;
        }

        /* --- Reusable Card Component --- */
        .card {
            background-color: var(--surface-color);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-medium);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #f3f4f6;
            padding-bottom: 10px;
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
        }

        /* --- Buttons & Actions --- */
        .btn {
            padding: 8px 16px;
            border-radius: 0.375rem;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--surface-color);
            border: 1px solid var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #2563eb; /* Darker primary */
            box-shadow: var(--shadow-light);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-outline:hover {
            background-color: #eff6ff; /* Very light primary background */
        }

        .btn-text {
            background: none;
            border: none;
            color: var(--primary-color);
            font-weight: 500;
            padding: 5px;
        }

        .actions-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        /* --- Information Layouts --- */
        .info-section {
            display: flex;
            flex-direction: column;
            gap: 10px;
            color: #4b5563;
            font-size: 0.9rem;
        }
        
        .info-row {
            display: flex;
            align-items: center;
        }
        
        .info-row strong {
            font-weight: 600;
            color: #1f2937;
            margin-right: 5px;
        }
        
        .placeholder-text {
            color: #9ca3af;
            font-style: italic;
        }

        /* --- Table Styling --- */
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.9rem;
            text-align: left;
        }

        .data-table th, .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table th {
            font-weight: 600;
            color: #4b5563;
            background-color: #f9fafb;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.05em;
        }
        
        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover {
            background-color: #f3f4f6;
            cursor: pointer;
        }
        
        .data-table td a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .data-table td a:hover {
            text-decoration: underline;
        }

        /* Status Pills */
        .pill {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .pill-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        
        .pill-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .pill-neutral {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        .action-link {
            color: var(--primary-color);
            cursor: pointer;
            font-weight: 500;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            gap: 5px;
        }
        
        .page-num {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            border: 1px solid #e5e7eb;
            cursor: pointer;
        }
        
        .page-num.active {
            background-color: var(--primary-color);
            color: var(--surface-color);
            border-color: var(--primary-color);
        }

        /* Archive Specific Styling */
        .archive-card {
            background-color: #fff7ed; /* Light warning color */
            border: 1px solid #fed7aa;
            box-shadow: var(--shadow-light);
            margin-left: 21px;
            margin-top: 25px;
            width: 100%;
        }
        
        .archive-card .btn-danger {
            background-color: #dc2626;
            color: var(--surface-color);
            border: 1px solid #dc2626;
        }
        
        .archive-card .btn-danger:hover {
            background-color: #b91c1c;
        }
        
        /* Font Awesome Icons (Simulated with simple text) */
        .icon {
            margin-right: 5px;
            font-size: 1.1em;
        }

        /* Sidebar-view Specific */
        .sidebar-view .card {
            padding: 15px;
        }
        
        .sidebar-view .card-header {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 10px;
        }
        
        .sidebar-view .info-row {
            margin-bottom: 8px;
        }
        
        .dropdown {
            padding: 8px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            width: 100%;
            margin-top: 5px;
            background-color: var(--surface-color);
        }
        
        .dropdown-row {
            margin-top: 10px;
        }

        /* Responsive adjustments for mobile */
        @media (max-width: 900px) {
            .page-content {
                grid-template-columns: 1fr;
            }
        }
        .sidebar-view {
  width: 100%;
}


.pill {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #fff;
    text-transform: uppercase;
}

.pill-green { background-color: #16a34a; }
.pill-yellow { background-color: #facc15; color: #000; }
.pill-orange { background-color: #fb923c; }
.pill-red { background-color: #dc2626; }
.pill-gray { background-color: #9ca3af; }

/* Table Container */



    </style>

    <main class="page-content">
        <!-- Main Content Area -->
        <div class="main-content">

       

           
           <!-- 3. Documents -->
             <section class="card">
                    <div class="card-header flex justify-between items-center">
                        <h2 class="text-lg font-semibold">Documents</h2>
                        <a href="{{ route('filament.admin.pages.client-own-docs', ['client_id' => $client->id]) }}"
                        class="text-blue-600 hover:underline text-sm">
                            VIEW ALL
                        </a>
                    </div>

                    <table class="data-table w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-left">
                                <th class="p-2 w-1/4">Name</th>
                                <th class="p-2 w-1/5">Category</th>
                                <th class="p-2 w-1/10">Staff Visibility</th>
                                <th class="p-2 w-1/6">Expires At</th>
                                <th class="p-2 w-1/6">No Expiration</th>
                                <th class="p-2 w-1/6">Last Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($documents as $doc)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">
                                        <a href="{{ $doc->file_url ?? '#' }}" class="text-blue-600 hover:underline">
                                            {{ $doc->name }}
                                        </a>
                                    </td>
                                    <td class="p-2">{{ $doc->documentCategory->name ?? '-' }}</td>
                                    <td class="p-2 text-center">
                                        {!! $doc->staff_visibility ? '&#10003;' : '&#10005;' !!}
                                    </td>
                                    <td class="p-2">{{ optional($doc->expired_at)->format('d.m.Y') ?? '-' }}</td>
                                    <td class="p-2 text-center">
                                        {!! $doc->no_expiration ? '&#10003;' : '&#10005;' !!}
                                    </td>
                                    <td class="p-2">{{ $doc->updated_at->format('d.m.Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-gray-500 p-4">
                                        No documents found for this client.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $documents->links() }}
                    </div>
                </section>



            <!-- 4. Invoices -->
           <section class="card mt-8">
                    <div class="card-header flex justify-between items-center">
                        <h2 class="text-lg font-semibold">Invoices</h2>
                        <a href="{{ route('filament.admin.pages.invoice-list') }}"
                        class="text-blue-600 hover:underline text-sm">
                            VIEW ALL
                        </a>
                    </div>

                    <div class="overflow-x-auto" >
                        <table class="data-table w-full text-sm border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-left">
                                    <th class="p-2">Invoice Number</th>
                                    <th class="p-2">Issued Date</th>
                                    <th class="p-2">To</th>
                                    <th class="p-2">Amount</th>
                                    <th class="p-2">Tax</th>
                                    <th class="p-2">Balance</th>
                                    <th class="p-2">Due Date</th>
                                    <th class="p-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    @php
                                        $contactName = '-';
                                        if ($invoice->additional_contact_id) {
                                            $contact = \App\Models\AdditionalContact::find($invoice->additional_contact_id);
                                            $contactName = $contact ? trim($contact->first_name . ' ' . $contact->last_name) : '-';
                                        } else {
                                            $contactName = $invoice->client->display_name ?? '-';
                                        }

                                        $statusText = $invoice->status;
                                        $statusColor = 'gray';
                                        if ($invoice->status === 'Paid') {
                                            $statusColor = 'green';
                                            $statusText 
                                                ? 'Paid ' 
                                                : 'Paid';
                                        } elseif ($invoice->status === 'Unpaid/Overdue' && $invoice->payment_due) {
                                            $dueDate = \Carbon\Carbon::parse($invoice->payment_due)->startOfDay();
                                            $today = \Carbon\Carbon::now()->startOfDay();
                                            $daysRemaining = $today->diffInDays($dueDate, false);
                                            if ($daysRemaining > 0) {
                                                $statusText = "Due in {$daysRemaining} " . \Illuminate\Support\Str::plural('day', $daysRemaining);
                                                $statusColor = 'yellow';
                                            } elseif ($daysRemaining === 0) {
                                                $statusText = "Due Today";
                                                $statusColor = 'orange';
                                            } else {
                                                $statusText = "Overdue";
                                                $statusColor = 'red';
                                            }
                                        } elseif ($invoice->status === 'Overdue') {
                                            $statusColor = 'red';
                                            $statusText = 'Overdue';
                                        }
                                    @endphp

                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2">
                                            <a href="#" class="text-blue-600 hover:underline">
                                                {{ $invoice->invoice_no }}
                                            </a>
                                        </td>
                                        <td class="p-2">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('F d, Y') }}</td>
                                        <td class="p-2">{{ $contactName }}</td>
                                        <td class="p-2">${{ number_format($invoice->amount, 2) }}</td>
                                        <td class="p-2">${{ number_format($invoice->tax, 2) }}</td>
                                        <td class="p-2">${{ number_format($invoice->balance, 2) }}</td>
                                        <td class="p-2">
                                            {{ $invoice->payment_due ? \Carbon\Carbon::parse($invoice->payment_due)->format('F d, Y') : '-' }}
                                        </td>
                                        <td class="p-2">
                                            @php
                                                $status = strtoupper($invoice->status);

                                                $statusColor = match($invoice->status) {
                                                    'Paid' => 'green',
                                                    'Unpaid', 'Unpaid/Overdue' => 'yellow',
                                                    'Overdue' => 'red',
                                                    default => 'grey',
                                                };
                                            @endphp

                                            <span class="pill pill-{{ $statusColor }}">{{ $status }}</span>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-gray-500 p-4">
                                            No invoices found for this client.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $invoices->links('pagination::tailwind') }}
                    </div>
                </section>

            
         
            
     

        </div>

        
 <section class="card archive-card">
                <div class="info-section">
                    <p style="margin-bottom: 10px; color: #9a3412;">
                        This will archive the client and yes will not able to see client to your list. If you do wish to scann the client, please go to Archive sele menu.
                    </p>
                   <!-- Archive Client Button -->
                    <form id="archiveForm" action="{{ route('filament.admin.resources.clients.archive', $client->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="button" class="btn btn-danger" style="width: fit-content;" onclick="confirmArchive(event)">
                            <span class="icon">&#128452;</span>
                            Archive Client
                        </button>
                    </form>

                    <script>
                    function confirmArchive(event) {
                        event.preventDefault();
                        if (confirm('Are you sure you want to archive this client?')) {
                            document.getElementById('archiveForm').submit();
                        }
                    }
                    </script>

                </div>
            </section>  
    </main>

    <!-- 6. Archive Client -->
           