<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Client;
use App\Models\AdditionalContact;
use Filament\Tables\Columns\TextInputColumn;
use Carbon\Carbon;
use App\Models\BillingReport;
use Filament\Tables;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use App\Filament\Widgets\BillingStats;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use App\Models\PriceBook;
use App\Models\PriceBookDetail;
use App\Models\Company;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use App\Models\Invoice;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use App\Models\Shift;
use Filament\Facades\Filament;
use Filament\Forms\Components\View;
use Illuminate\Support\Facades\Log;

class InvoiceGenerate extends Page 
{

    protected static ?string $navigationIcon = 'heroicon-s-document-chart-bar';
    protected static string $view = 'filament.pages.invoice-generate';
    protected static ?string $navigationGroup = 'Invoices';
     public ?string $group_by = 'client';
     public array $selectedRows = [];
    public $clients;
    public $count;
    public array $selectedClients = [];


                       public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('generate-invoices');
        }
    protected static ?int $navigationSort = 3;

    public function getTitle(): string
    {
        return 'Invoices Generate';
    }

    public ?array $data = [];

  public function mount(): void
{
    $authUser = auth()->user();

    $this->clients = Client::with([
            // load billing reports that are NOT Paid (so JS filters those by date)
            'billingReports' => function ($q) {
                $q->where('status', '!=', 'Paid')
                  ->orderBy('date', 'asc');
            },
        ])
        ->withSum(
            ['billingReports as unpaid_total_cost' => function ($query) {
                $query->where('status', 'Unpaid');
            }],
            'total_cost'
        )
        ->withCount([
            'billingReports as not_paid_reports_count' => function ($query) {
                $query->where('status', '!=', 'Paid');
            }
        ])
        ->where('is_archive', 'Unarchive')
        ->where('user_id', $authUser->id)
        ->having('unpaid_total_cost', '>', 0)
        ->get();
}


#[On('generateInvoices')]
public function generateInvoices(array $selectedClients): void
{
    try {
$authUser = auth()->user();
    $companyId = Company::where('user_id', $authUser->id)->value('id');

    foreach ($selectedClients as $clientData) {
        $clientId  = $clientData['id'];
        $contactId = $clientData['additional_contact_id'] ?? null;
        $issueDate = now()->toDateString();
        $paymentDue = $clientData['payment_due'];
        $purchaseOrder = $clientData['ref_no'] ?? null;

        // Fetch unpaid billing reports
        $billingReports = BillingReport::where('client_id', $clientId)
            ->where('status', 'Unpaid')
            ->get();

        if ($billingReports->isEmpty()) {
            continue;
        }

        // ✅ Collect shift IDs
        $shiftIds = $billingReports->pluck('shift_id')->filter()->unique()->toArray();

        // ✅ Check if any related shift is not approved
        $unapprovedShiftExists = Shift::whereIn('id', $shiftIds)
            ->where('is_approved', false)
            ->exists();

        if ($unapprovedShiftExists) {
            Notification::make()
                ->title("Please approve all shifts before generating invoices for this client.")
                ->danger()
                ->send();

            // Stop further processing for this client
            continue;
        }

        $totalCost = $billingReports->sum('total_cost');
        $billingReportIds = $billingReports->pluck('id')->toArray();

        // Tax handling
        $isTaxChecked = $clientData['tax_checked'] ?? false;
        $taxAmount = $isTaxChecked ? $totalCost * 0.10 : 0.00;

        // Random invoice no & NDIS/ref_no
        $invoiceNo = random_int(1000000, 9999999);
        $ndisRef = random_int(100000000, 999999999);

        $lastSequence = Invoice::max('invoice_sequence'); // null if no records
        $sequence = $lastSequence ? $lastSequence + 1 : 1;

        // ──────────────────────────────────────────────────────────────
        // 1. FETCH + ENRICH BILLING REPORTS (CRITICAL: gets real ref codes)
        // ──────────────────────────────────────────────────────────────
        $billingReports = BillingReport::with(['shift', 'client'])
            ->where('client_id', $clientId)
            ->where('status', 'Unpaid')
            ->get()
            ->map(function ($report) {
                // Parse hours_x_rate (e.g., "8 x $95.00")
                if (!empty($report->hours_x_rate) && strpos($report->hours_x_rate, 'x') !== false) {
                    [$hours, $rate] = array_map('trim', explode('x', $report->hours_x_rate, 2));
                    $report->hours = (float) $hours;
                    $report->rate  = $rate;
                } else {
                    $report->hours = null;
                    $report->rate  = null;
                }

                // Parse distance_x_rate
                if (!empty($report->distance_x_rate) && strpos($report->distance_x_rate, 'x') !== false) {
                    [$distance, $rate] = array_map('trim', explode('x', $report->distance_x_rate, 2));
                    $report->distance       = (float) $distance;
                    $report->distance_rate  = $rate;
                } else {
                    $report->distance      = null;
                    $report->distance_rate = null;
                }

                // Match PriceBookDetail to get ref_hour & ref_km
                if (!empty($report->price_book_id) && !empty($report->rate)) {
                    $numericRate = (float) str_replace(['$', ','], '', $report->rate);

                    $detail = \App\Models\PriceBookDetail::where('price_book_id', $report->price_book_id)
                        ->where('per_hour', $numericRate)
                        ->first();

                    if ($detail) {
                        $report->matched_price_book_detail = $detail;
                    }
                }

                return $report;
            });

        // ──────────────────────────────────────────────────────────────
        // 2. BUILD DESCRIPTION JSON (using Billing Report ID as key)
        // ──────────────────────────────────────────────────────────────
        $hourShiftDescriptions = [];
        $kmShiftDescriptions   = [];

        foreach ($billingReports as $report) {
            $shift = $report->shift;
            if (!$shift) continue;

            // Decode JSON fields safely
            $clientSection   = is_string($shift->client_section) ? json_decode($shift->client_section, true) : ($shift->client_section ?? []);
            $timeAndLocation = is_string($shift->time_and_location) ? json_decode($shift->time_and_location, true) : ($shift->time_and_location ?? []);

            $clientName = $report->client?->display_name ?? 'Unknown Client';

            // Format date & time
            $startTime = !empty($timeAndLocation['start_time']) ? Carbon::parse($timeAndLocation['start_time'])->format('h:i a') : '';
            $endTime   = !empty($timeAndLocation['end_time'])   ? Carbon::parse($timeAndLocation['end_time'])->format('h:i a') : '';
            $dateText  = !empty($timeAndLocation['start_date']) ? Carbon::parse($timeAndLocation['start_date'])->format('d/m/Y') : '';
            $timeText  = trim("{$dateText} {$startTime} - {$endTime}");

            // Get Price Book Name (simple or advanced shift)
            $priceBookId = null;
            if (!$shift->is_advanced_shift) {
                $priceBookId = $clientSection['price_book_id'] ?? null;
            } else {
                $clientDetails = $clientSection['client_details'][0] ?? null;
                $priceBookId = $clientDetails['price_book_id'] ?? null;
            }

            $priceBookName = $priceBookId
                ? \App\Models\PriceBook::find($priceBookId)?->name ?? 'Unknown Price Book'
                : 'Unknown Price Book';

            // Get real ref codes (now guaranteed to exist)
            $refHour = $report->matched_price_book_detail?->ref_hour ?? '-';
            $refKm   = $report->matched_price_book_detail?->ref_km ?? '-';

            $baseText  = "{$clientName} ({$timeText}) [{$priceBookName}]";
            $billingId = $report->id;

            // Add hour shift
            if ($refHour && trim($refHour) !== '' && trim($refHour) !== '-') {
                $hourShiftDescriptions[$billingId] = "{$baseText} [{$refHour}]";
            }

            // Add km shift (only if different and valid)
            if ($refKm && trim($refKm) !== '' && trim($refKm) !== '-' && $refKm !== $refHour) {
                $kmShiftDescriptions[$billingId] = "{$baseText} [{$refKm}]";
            }
        }

        // Final description array
        $description = [
            'hour_shift' => $hourShiftDescriptions,
            'km_shift'   => $kmShiftDescriptions,
        ];

        if (empty($hourShiftDescriptions) && empty($kmShiftDescriptions)) {
            $description = null;
        }
        // ──────────────────────────────────────────────────────────────
        // NOW USE $description in Invoice::create()
        // ──────────────────────────────────────────────────────────────

        // Create the invoice
        $invoiceCreate = Invoice::create([
            'company_id'            => $companyId,
            'client_id'             => $clientId,
            'additional_contact_id' => $contactId,
            'billing_reports_ids'   => $billingReportIds,
            'invoice_sequence'      => $sequence,
            'invoice_no'            => str_pad($sequence, 7, '0', STR_PAD_LEFT),
            'issue_date'            => $issueDate,
            'payment_due'           => $paymentDue,
            'NDIS'                  => $ndisRef,
            'ref_no'                => $ndisRef,
            'status'                => 'Unpaid/Overdue',
            'amount'                => $totalCost,
            'tax'                   => $taxAmount,
            'balance'               => $totalCost + $taxAmount,
            'description' => $description,
        ]);


        // ✅ Update BillingReports → Paid
        BillingReport::whereIn('id', $billingReportIds)->update([
            'status' => 'Paid',
        ]);

        // ✅ Update related shifts → Invoiced
        Shift::whereIn('id', $shiftIds)->update([
            'status' => 'Invoiced',
        ]);

        // ✅ Log Event
        Event::create([
            'invoice_id' => $invoiceCreate->id,
            'title'    => $authUser->name . ' Created Invoice',
            'from'     => 'Invoice',
            'body'     => 'Invoice created',
        ]);

            // ✅ Final notification if all went well
    Notification::make()
        ->title('Invoices Generated Successfully')
        ->success()
        ->send();
    }


        $this->redirect(request()->header('Referer'));

    } 
    catch (\Throwable $e) {
    Log::error('Invoice generation failed', [
        'message' => $e->getMessage(),
        'trace'   => $e->getTraceAsString(),
    ]);

    Notification::make()
        ->title('Something went wrong while generating invoices.')
        ->body('Please make sure all required fields (like payment due date) are filled correctly, then try again.')
        ->danger()
        ->send();
}

}



    


   
}