<?php

namespace App\Http\Controllers;

use App\Models\TimesheetReport;
use App\Models\Allowance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 

class TimesheetController extends Controller
{
 public function printReport(Request $request)
{
    $ids = explode(',', $request->get('ids'));
    $records = TimesheetReport::whereIn('id', $ids)->get();

    // ----------------------------------------------------
    // START: REDEFINING THE FORMATTING CLOSURES
    // ----------------------------------------------------

    // Helper function to handle the complex clients data formatting
    $formatClients = function ($clientsData, $includePriceBookLookup = true) {
        if (empty($clientsData)) {
            return '-';
        }

        $clients = is_string($clientsData) ? @json_decode($clientsData, true) : (is_array($clientsData) ? $clientsData : []);

        if (empty($clients)) {
            return '-';
        }

        // Handle the case where clients is a single object, not an array of objects
        if (isset($clients['client_id']) && !isset($clients[0])) {
            $clients = [$clients];
        }

        $formatted = collect($clients)->map(function ($client) use ($includePriceBookLookup) {
            $clientId = data_get($client, 'client_id');
            $clientName = data_get($client, 'client_name');
            $priceBookId = data_get($client, 'price_book_id');

            // Lookup Client Name if missing
            if (!$clientName && $clientId) {
                $clientName = DB::table('clients')->where('id', $clientId)->value('display_name') ?? 'Unknown Client';
            } elseif (!$clientName) {
                 $clientName = 'Unknown Client';
            }

            // Determine Price Book Name
            $priceBookName = 'Community Services'; // Default for shift_id column
            if ($includePriceBookLookup && $priceBookId) {
                // Lookup Price Book Name for the 'clients' column
                $priceBookName = DB::table('price_books')->where('id', $priceBookId)->value('name') ?? 'Unknown Price Book';
            }

            return "{$clientName} - {$priceBookName}";
        });

        // Use comma as separator for CSV/Print
        return $formatted->implode(', ');
    };

    // Helper function to format break time into total minutes string
    $formatBreakTime = function ($state) {
        if (empty($state) || $state === '0') {
            return '0 mins';
        }

        if (is_numeric($state)) {
            return "{$state} mins";
        }

        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $state, $matches)) {
            $hours = (int) ($matches[1] ?? 0);
            $minutes = (int) ($matches[2] ?? 0);
            $totalMinutes = ($hours * 60) + $minutes;
            return "{$totalMinutes} mins";
        }

        return "{$state} mins";
    };

    // ----------------------------------------------------
    // END: REDEFINING THE FORMATTING CLOSURES
    // ----------------------------------------------------

    return view('timesheets.staff-timesheet-print', [
        'records' => $records,
        'formatClients' => $formatClients,
        'formatBreakTime' => $formatBreakTime,
        'allowanceModel' => Allowance::class, // Pass the model for lookup in the view
    ]);
}
}
