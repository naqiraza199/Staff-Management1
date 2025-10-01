<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Company;
use App\Models\InvoiceCategory;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\AdditionalContact;

class InvoiceView extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.invoice-view';

     public ?int $client_id = null;
     public ?int $invoice_id = null;
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
    public $to_name = null;
    public $client_name = '-';
    public $client_email = null;
    public $client_phone = null;
    public $client_address = null;

    // additional contact fields
    public $additional_name = null;
    public $additional_email = null;
    public $additional_phone = null;
    public $additional_address = null;
    public $invoice = null;

             public static function shouldRegisterNavigation(): bool
{
    return false;
}

  public function mount()
{
    $authUser = auth()->user();

    $this->invoice_id = request()->query('invoice_id');
    $this->invoice = Invoice::find($this->invoice_id);
    $this->company = Company::where('user_id', $authUser->id)->first();
    $invoice = Invoice::find($this->invoice_id);

    if ($invoice) {
        $this->billing_ids = is_string($invoice->billing_reports_ids)
        ? json_decode($invoice->billing_reports_ids, true)
        : ($invoice->billing_reports_ids ?? []);


    } else {
        $this->billing_ids = [];
    } 

    if ($invoice) {
        $client = Client::find($invoice->client_id);

        $additionalContact = null;
        if ($invoice->additional_contact_id) {  
            $additionalContact = AdditionalContact::find($invoice->additional_contact_id);
        }

        $this->client_name = $client?->display_name ?? '-';
        $this->client_email = $client?->email ?? null;
        $this->client_phone = $client?->phone_number ?? null;
        $this->client_address = $client?->address ?? null;

        $this->additional_name = $additionalContact
            ? trim($additionalContact->first_name . ' ' . $additionalContact->last_name)
            : null;
        $this->additional_email = $additionalContact?->email ?? null;
        $this->additional_phone = $additionalContact?->phone_number ?? null;
        $this->additional_address = $additionalContact?->address ?? null;

        $this->to_name = $this->additional_name ?: $this->client_name;
    } else {
        $this->client_name = '-';
        $this->client_email = null;
        $this->client_phone = null;

        $this->additional_name = null;
        $this->additional_email = null;
        $this->additional_phone = null;

        $this->to_name = '-';
    }

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

    // ✅ Added: detect which billing report the invoice tax belongs to
if ($invoice && !empty($this->billingReports)) {
    $invoiceTax = $invoice->tax ?? 0;
    $subtotal = $this->billingReports->sum('total_cost');

    foreach ($this->billingReports as $report) {
        // Distribute tax proportionally by report's cost
        if ($subtotal > 0) {
            $share = ($report->total_cost / $subtotal) * $invoiceTax;
        } else {
            $share = 0;
        }

        $report->allocated_tax = round($share, 2);
    }
}

}


}
