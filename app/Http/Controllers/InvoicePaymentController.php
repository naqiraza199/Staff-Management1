<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class InvoicePaymentController extends BaseController
{
    public function store(Request $request, Invoice $invoice)
{
    $validated = $request->validate([
        'paid_amount'  => 'nullable',
        'reference'    => 'nullable',
        'payment_date' => 'nullable',
    ]);

    DB::transaction(function () use ($invoice, $validated) {
        $payment = InvoicePayment::create([
            'invoice_id'   => $invoice->id,
            'paid_amount'  => $validated['paid_amount'] ?? 0,
            'reference'    => $validated['reference'] ?? null,
            'payment_date' => $validated['payment_date'] ?? now()->toDateString(),
        ]);

        $newBalance = max(0, (float) $invoice->balance - (float) $payment->paid_amount);
        $invoice->balance = $newBalance;
        if ($newBalance == 0.0) {
            $invoice->status = 'Paid';
        }
        $invoice->save();

        Event::create([
            'invoice_id' => $invoice->id, 
            'invoice_payment_id' => $payment->id,
            'title'      => auth()->user()->name . ' Received Payment',
            'from'       => 'Invoice Payment'. $payment->paid_amount . ". ",
            'body' => auth()->user()->name . " received payment of $" . $payment->paid_amount . ". " .
                    "Reference: " . ($payment->reference ?? 'N/A') . ". " .
                    "Payment Date: " . \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') . ". " .
                    "Remaining Balance: $" . $invoice->balance . ".",

        ]);
    });

    Notification::make()
        ->title('Payment Added')
        ->body('Payment added successfully')
        ->success()
        ->send();

    return back()->with('success', 'Payment recorded successfully.');
}




 public function destroy(InvoicePayment $invoicePayment)
{
    $invoice = $invoicePayment->invoice;

    DB::transaction(function () use ($invoicePayment, $invoice) {
        $amount = (float) $invoicePayment->paid_amount;

        // Delete the event linked to this payment
        \App\Models\Event::where('invoice_payment_id', $invoicePayment->id)->delete();

        // Delete the payment
        $invoicePayment->delete();

        // Update invoice balance
        $invoice->balance = (float) $invoice->balance + $amount;
        if ($invoice->balance == 0.0) {
            $invoice->status = 'Paid';
        }
        $invoice->save();
    });

    Notification::make()
        ->title('Payment Deleted')
        ->body('Payment and related event deleted successfully')
        ->success()
        ->send();

    return back()->with('success', 'Payment deleted successfully.');
}

public function addNote(Request $request, Invoice $invoice)
{
    $request->validate([
        'note' => 'required|string|max:1000',
    ]);

    \App\Models\Event::create([
        'invoice_id' => $invoice->id,
        'title'      => auth()->user()->name . ' added a note',
        'from'       => 'Invoice Note',
        'body'       => $request->note,
    ]);

    Notification::make()
        ->title('Note Added')
        ->body('Your note has been saved to the invoice.')
        ->success()
        ->send();

    return back()->with('success', 'Note added successfully.');
}
 public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'additional_contact_id' => 'nullable|exists:additional_contacts,id',
            'payment_due'           => 'nullable|date',
            'ref_no'                => 'nullable|string|max:255',
            'purchase_order'        => 'nullable|string|max:255',
        ]);

        $invoice->update($validated);

        Event::create([
            'invoice_id' => $invoice->id,
            'title'      => Auth::user()->name . ' Updated Invoice',
            'from'       => 'Invoice',  
            'body'       => 'Invoice updated',
        ]);

        Notification::make()
            ->title('Invoice Updated')
            ->body('Invoice updated successfully.')
            ->success()
            ->send();

        return back()->with('success', 'Invoice updated successfully.');
    }

}


