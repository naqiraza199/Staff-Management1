<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Client;
use App\Models\AdditionalContact;
use App\Models\BillingReport;
use App\Models\PriceBookDetail;

class InvoicePrintController extends Controller
{
    public function show(int $invoice)
    {
        $invoice = Invoice::findOrFail($invoice);

        $authUserId = auth()->id();
        $company = Company::where('user_id', $authUserId)->first();

        $billingIds =  $this->billing_ids = is_string($invoice->billing_reports_ids)
        ? json_decode($invoice->billing_reports_ids, true)
        : ($invoice->billing_reports_ids ?? []);

        $billingReports = collect();
        if (!empty($billingIds)) {
            $billingReports = BillingReport::whereIn('id', $billingIds)
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
                        $detail = PriceBookDetail::where('price_book_id', $report->price_book_id)
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

        // Client and additional contact
        $client = Client::find($invoice->client_id);
        $additionalContact = $invoice->additional_contact_id
            ? AdditionalContact::find($invoice->additional_contact_id)
            : null;

        $client_name = $client->display_name ?? '-';
        $client_email = $client->email ?? null;
        $client_phone = $client->phone_number ?? null;
        $client_address = $client->address ?? null;

        $additional_name = $additionalContact
            ? trim(($additionalContact->first_name ?? '') . ' ' . ($additionalContact->last_name ?? ''))
            : null;
        $additional_email = $additionalContact->email ?? null;
        $additional_phone = $additionalContact->phone_number ?? null;
        $additional_address = $additionalContact->address ?? null;

        $invoicePayments = \App\Models\InvoicePayment::where('invoice_id', $invoice->id);
        $totalPaid = $invoicePayments->sum('paid_amount'); 
        $latestDate = $invoicePayments->latest('payment_date')->value('payment_date'); 

        return view('filament.pages.invoice-print', compact(
            'invoice',
            'company',
            'billingReports',
            'client_name',
            'client_email',
            'client_phone',
            'client_address',
            'additional_name',
            'additional_email',
            'additional_phone',
            'additional_address',
            'totalPaid',
            'latestDate'
        ));
    }
}



