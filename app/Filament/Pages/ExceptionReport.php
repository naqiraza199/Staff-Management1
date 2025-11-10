<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Company;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\BillingReport;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class ExceptionReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-chart-bar-square';
    protected static string $view = 'filament.pages.exception-report';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Exception Report';
    protected static ?string $title = 'Delivered Vs Invoiced';

    public $clients = [];
    public $totals = [];

                          public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('see-execption-reports');
        }

   public function mount()
{
    $authUser = Auth::user();
    if (!$authUser) return;

    $companyId = Company::where('user_id', $authUser->id)->value('id');

    // Fetch all clients for this user
    $clients = Client::where('user_id', $authUser->id)
        ->select('id', 'display_name as name')
        ->get();

    $clientsData = [];

    // Initialize total counters
    $totalDelivered = $totalInvoiced = $totalCancelled = $totalNonInvoiced = 0;
    $totalDeliveredHrs = $totalInvoicedHrs = $totalCancelledHrs = $totalNonInvoicedHrs = 0;

    foreach ($clients as $client) {

        /* ====================== AMOUNTS ====================== */

        // 1️⃣ Delivered Amount
        $deliveredAmount = \App\Models\InvoicePayment::whereIn('invoice_id', function ($query) use ($client) {
            $query->select('id')->from('invoices')->where('client_id', $client->id);
        })->sum('paid_amount');

        // 2️⃣ Invoiced Amount
        $invoicedAmount = Invoice::where('client_id', $client->id)
            ->where('status', '!=', 'Paid')
            ->sum('balance');

        // 3️⃣ Cancelled Amount
        $cancelledAmount = BillingReport::whereHas('shift', function ($query) {
            $query->where('status', 'Cancelled');
        })->where('client_id', $client->id)
          ->sum('total_cost');

        // 4️⃣ Non Invoiced Amount
        $nonInvoicedAmount = BillingReport::whereHas('shift', function ($query) {
            $query->whereIn('status', ['Pending', 'Booked']);
        })->where('client_id', $client->id)
          ->sum('total_cost');

        /* ====================== HOURS ====================== */

        // 1️⃣ Delivered Hours → from Paid invoices' billing_reports
        $deliveredHours = 0;
        $paidInvoices = Invoice::where('client_id', $client->id)
            ->where('status', 'Paid')
            ->pluck('billing_reports_ids')
            ->toArray();

        foreach ($paidInvoices as $idsJson) {
            $ids = json_decode($idsJson, true);
            if (!is_array($ids)) continue;

            $reports = BillingReport::whereIn('id', $ids)->get();
            foreach ($reports as $report) {
                $hours = $this->extractHours($report->hours_x_rate);
                $deliveredHours += $hours;
            }
        }

        // 2️⃣ Invoiced Hours → shifts.status = 'Invoiced' (exclude reports already in Paid invoices)
        $invoicedHours = 0;

        // Collect billing report IDs tied to Paid invoices (to exclude)
        $paidInvoiceReportIds = Invoice::where('client_id', $client->id)
            ->where('status', 'Paid')
            ->pluck('billing_reports_ids')
            ->flatMap(function ($idsJson) {
                $ids = json_decode($idsJson, true);
                return is_array($ids) ? $ids : [];
            })
            ->toArray();

        // Now get invoiced hours excluding Paid invoice reports
        $invoicedReports = BillingReport::where('client_id', $client->id)
            ->whereHas('shift', fn($q) => $q->where('status', 'Invoiced'))
            ->when(!empty($paidInvoiceReportIds), fn($q) => $q->whereNotIn('id', $paidInvoiceReportIds))
            ->get();

        foreach ($invoicedReports as $report) {
            $invoicedHours += $this->extractHours($report->hours_x_rate);
        }

        // 3️⃣ Cancelled Hours → shifts.status = 'Cancelled'
        $cancelledHours = BillingReport::where('client_id', $client->id)
            ->whereHas('shift', fn($q) => $q->where('status', 'Cancelled'))
            ->get()
            ->sum(fn($r) => $this->extractHours($r->hours_x_rate));

        // 4️⃣ Non Invoiced Hours → shifts.status IN ('Pending', 'Booked')
        $nonInvoicedHours = BillingReport::where('client_id', $client->id)
            ->whereHas('shift', fn($q) => $q->whereIn('status', ['Pending', 'Booked']))
            ->get()
            ->sum(fn($r) => $this->extractHours($r->hours_x_rate));

        /* ====================== TOTALS ====================== */
        $totalDelivered += $deliveredAmount;
        $totalInvoiced += $invoicedAmount;
        $totalCancelled += $cancelledAmount;
        $totalNonInvoiced += $nonInvoicedAmount;

        $totalDeliveredHrs += $deliveredHours;
        $totalInvoicedHrs += $invoicedHours;
        $totalCancelledHrs += $cancelledHours;
        $totalNonInvoicedHrs += $nonInvoicedHours;

        $clientsData[] = [
            'id' => $client->id,
            'name' => $client->name,
            'delivered_amount' => $deliveredAmount,
            'invoiced_amount' => $invoicedAmount,
            'cancelled_amount' => $cancelledAmount,
            'non_invoiced_amount' => $nonInvoicedAmount,
            'delivered_hours' => $deliveredHours,
            'invoiced_hours' => $invoicedHours,
            'cancelled_hours' => $cancelledHours,
            'non_invoiced_hours' => $nonInvoicedHours,
        ];
    }

    $this->clients = $clientsData;
    $this->totals = [
        'delivered' => $totalDelivered,
        'invoiced' => $totalInvoiced,
        'cancelled' => $totalCancelled,
        'non_invoiced' => $totalNonInvoiced,
        'delivered_hours' => $totalDeliveredHrs,
        'invoiced_hours' => $totalInvoicedHrs,
        'cancelled_hours' => $totalCancelledHrs,
        'non_invoiced_hours' => $totalNonInvoicedHrs,
    ];
}


    /**
     * Extract numeric hours from a string like "8.0 x $75.98"
     */
    private function extractHours($value)
    {
        if (!$value) return 0;
        if (preg_match('/([\d\.]+)\s*x/', $value, $match)) {
            return (float)$match[1];
        }
        return 0;
    }

    public function downloadReport()
{
    $clients = collect($this->clients);

    if ($clients->isEmpty()) {
        $this->notify('warning', 'No client records found to export.');
        return;
    }

    $filename = 'exception_report_' . now()->format('Y_m_d_His') . '.csv';

    // Define headers
    $headers = [
        'Client',
        'Delivered Amount',
        'Invoiced Amount',
        'Cancelled Amount',
        'Non Invoiced Amount',
        'Delivered Hours',
        'Invoiced Hours',
        'Cancelled Hours',
        'Non Invoiced Hours',
    ];

    // Create CSV stream
    $callback = function () use ($clients, $headers) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $headers);

        foreach ($clients as $client) {
            fputcsv($file, [
                $client['name'],
                '$' . number_format($client['delivered_amount'], 2),
                '$' . number_format($client['invoiced_amount'], 2),
                '$' . number_format($client['cancelled_amount'], 2),
                '$' . number_format($client['non_invoiced_amount'], 2),
                number_format($client['delivered_hours'], 2),
                number_format($client['invoiced_hours'], 2),
                number_format($client['cancelled_hours'], 2),
                number_format($client['non_invoiced_hours'], 2),
            ]);
        }

        // Totals row
        fputcsv($file, [
            'Total',
            '$' . number_format($clients->sum('delivered_amount'), 2),
            '$' . number_format($clients->sum('invoiced_amount'), 2),
            '$' . number_format($clients->sum('cancelled_amount'), 2),
            '$' . number_format($clients->sum('non_invoiced_amount'), 2),
            number_format($clients->sum('delivered_hours'), 2),
            number_format($clients->sum('invoiced_hours'), 2),
            number_format($clients->sum('cancelled_hours'), 2),
            number_format($clients->sum('non_invoiced_hours'), 2),
        ]);

        fclose($file);
    };

    // Stream download
    return response()->streamDownload($callback, $filename, [
        'Content-Type' => 'text/csv',
    ]);
}

}
