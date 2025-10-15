<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Company;
use App\Models\InvoiceCategory;

class TaxFree extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.tax-free';

      protected static ?string $title = null;
    public ?int $client_id = null;
    public $contactOptions = [];
    public $company;
    public $billing_ids = [];
    public $invoiceCategories;
    public $additional_contact_id;
    public $issue_date;
    public $payment_due;
    public $purchase_order;
    public $ref_no;
    public $ndis;
    public $include_tax = true;
    public $billingReports;
    public $taxCheckedIds = []; // store checked billing ids
    public $subtotal = 0;
    public $tax = 0;
    public $grandTotal = 0;
    public $taxableBillingIds = [];
    
    // Properties to capture HTML values
    public $htmlSubtotal = 0;
    public $htmlTax = 0;
    public $htmlGrandTotal = 0;

   public static function shouldRegisterNavigation(): bool
{
    return false;
}


        public function getTitle(): string
        {
            return 'New Invoice';
        }

      public function mount()
{
    $authUser = auth()->user();
    $this->client_id = request()->query('client_id');
    $this->company = Company::where('user_id', $authUser->id)->first();
    $this->billing_ids = explode(',', request()->query('billing_ids', ''));

    $this->invoiceCategories = InvoiceCategory::get();

    if ($this->client_id) {
        $contacts = \App\Models\AdditionalContact::where('client_id', $this->client_id)
            ->get()
            ->mapWithKeys(function ($contact) {
                return [$contact->id => $contact->first_name . ' ' . $contact->last_name];
            })
            ->toArray();

        $this->contactOptions = ['client' => 'Client'] + $contacts;
    } else {
        $this->contactOptions = ['client' => 'Client'];
    }

    if (!empty($this->billing_ids)) {
        $this->billingReports = \App\Models\BillingReport::whereIn('id', $this->billing_ids)
            ->get()
            ->map(function ($report) {
                if (!empty($report->hours_x_rate) && strpos($report->hours_x_rate, 'x') !== false) {
                    [$hours, $rate] = array_map('trim', explode('x', $report->hours_x_rate, 2));
                    

                    $hours = (float) $hours;
                    $rateValue = (float) str_replace(['$', ','], '', $rate);

                    $report->hours = $hours;    
                    $report->rate = $rate; 
                    $report->hours_total = $hours * $rateValue;    
                } else {
                    $report->hours = null;
                    $report->rate = null;
                    $report->hours_total = null;
                }

                if (!empty($report->distance_x_rate) && strpos($report->distance_x_rate, 'x') !== false) {
                    [$distance, $rate] = array_map('trim', explode('x', $report->distance_x_rate, 2));

                    $distance = (float) $distance;
                    $rateValue = (float) str_replace(['$', ','], '', $rate);

                    $report->distance = $distance;        
                    $report->distance_rate = $rate;       
                    $report->distance_total = $distance * $rateValue;
                } else {
                    $report->distance = null;
                    $report->distance_rate = null;
                    $report->distance_total = null;
                }

                if (!empty($report->price_book_id) && !empty($report->rate)) {
                    $numericRate = (float) str_replace(['$', ','], '', $report->rate);

                    $detail = \App\Models\PriceBookDetail::where('price_book_id', $report->price_book_id)
                        ->where('per_hour', $numericRate)
                        ->first();

                    if ($detail) {
                        $report->matched_price_book_detail = $detail; 
                        $report->rate = $detail->per_hour;           
                    }
                }

                return $report;
            });
    }

                if (!empty($this->billingReports)) {
                // Only include unpaid records in the calculation
                $this->subtotal = \App\Models\BillingReport::whereIn('id', $this->billing_ids)
                    ->where('status', '!=', 'Paid')
                    ->sum('total_cost');   
                $this->tax = $this->subtotal * 0.10;                        
                $this->grandTotal = $this->subtotal + $this->tax;          
            } else {
                $this->subtotal = 0;
                $this->tax = 0;
                $this->grandTotal = 0;
            }

            $this->issue_date = now()->format('Y-m-d');
            $this->payment_due = now()->addDays(14)->format('Y-m-d');
            $this->ref_no = str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);


}

public function updateHtmlValues($subtotal, $tax, $grandTotal)
{
    $this->htmlSubtotal = (float) str_replace(['$', ','], '', $subtotal);
    $this->htmlTax = (float) str_replace(['$', ','], '', $tax);
    $this->htmlGrandTotal = (float) str_replace(['$', ','], '', $grandTotal);
}

public function createInvoice()
{
    $authUser = auth()->user();

    // ✅ Get billing reports (excluding already paid)
    $billingReports = \App\Models\BillingReport::whereIn('id', $this->billing_ids)
        ->where('status', '!=', 'Paid')
        ->get();

    if ($billingReports->isEmpty()) {
        \Filament\Notifications\Notification::make()
            ->title('No unpaid billing reports found.')
            ->danger()
            ->send();
        return;
    }

    // ✅ Collect related shift IDs
    $shiftIds = $billingReports->pluck('shift_id')->filter()->unique()->toArray();

    // ✅ Check if any related shift is not approved
    $unapprovedShiftExists = \App\Models\Shift::whereIn('id', $shiftIds)
        ->where('is_approved', false)
        ->exists();

    if ($unapprovedShiftExists) {
        \Filament\Notifications\Notification::make()
            ->title('Please approve all related shifts before creating the invoice.')
            ->danger()
            ->send();

        return; // Stop invoice creation
    }

    // ✅ Recalculate totals excluding paid records
    $subtotalFromDB = $billingReports->sum('total_cost');

    // ✅ Use HTML or DB values
    $subtotalValue = $this->htmlSubtotal > 0 ? $this->htmlSubtotal : $subtotalFromDB;
    $taxValue = $this->htmlTax > 0 ? $this->htmlTax : 0.0;
    $grandTotalValue = $this->htmlGrandTotal > 0
        ? $this->htmlGrandTotal
        : ($subtotalFromDB + ($subtotalFromDB * 0.10));

    // ✅ Create invoice
    $invoice = \App\Models\Invoice::create([
        'company_id'            => $this->company->id,
        'client_id'             => $this->client_id,
        'billing_reports_ids'   => json_encode($this->billing_ids),
        'invoice_no'            => '#' . rand(1000000, 9999999),
        'issue_date'            => now()->toDateString(),
        'payment_due'           => $this->payment_due,
        'purchase_order'        => $this->purchase_order,
        'additional_contact_id' => $this->additional_contact_id === 'client' ? null : $this->additional_contact_id,
        'ndis'                  => $this->ref_no,
        'ref_no'                => $this->ref_no,
        'status'                => 'Unpaid/Overdue',
        'amount'                => $subtotalValue,
        'tax'                   => $taxValue,
        'balance'               => $grandTotalValue,
    ]);

    // ✅ Mark billing reports as Paid
    \App\Models\BillingReport::whereIn('id', $this->billing_ids)->update([
        'status' => 'Paid',
    ]);

    // ✅ Update related shifts to Invoiced
    if (!empty($shiftIds)) {
        \App\Models\Shift::whereIn('id', $shiftIds)->update([
            'status' => 'Invoiced',
        ]);
    }

    // ✅ Create event log
    \App\Models\Event::create([
        'invoice_id' => $invoice->id,
        'title'      => $authUser->name . ' Created Invoice',
        'from'       => 'Invoice',
        'body'       => 'Invoice created',
    ]);

    // ✅ Show success notification
    \Filament\Notifications\Notification::make()
        ->title('Invoice Created')
        ->body('Invoice ' . $invoice->invoice_no . ' created successfully.')
        ->success()
        ->send();

    // ✅ Redirect
    return redirect()->route('filament.admin.pages.tax-free', [
        'client_id'   => $this->client_id,
        'billing_ids' => implode(',', $this->billing_ids),
    ]);
}


}
