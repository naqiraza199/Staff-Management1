<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvoiceSetting;
use App\Models\Company;
use App\Models\Tax;
use Filament\Notifications\Notification;


class InvoiceSettingsController extends Controller
{


    public function store(Request $request)
    {
        $authUser = auth()->user();
        $company = Company::where('user_id', $authUser->id)->first();

        $data = $request->validate([
            'abn' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'payment_terms' => 'required|string',
            'contact_email' => 'required|email',
        ]);

        $data['company_id'] = $company->id;

        InvoiceSetting::create($data + $request->all());

          Notification::make()
        ->title('Created')
        ->body('Invoice settings created successfully!')
        ->success()
        ->send();


        return redirect()->back()->with('success', 'Invoice settings created successfully!');
    }

    public function update(Request $request, $id)
    {
        $record = InvoiceSetting::findOrFail($id);

        $record->update($request->all());

          Notification::make()
        ->title('Updated')
        ->body('Invoice settings updated successfully!')
        ->success()
        ->send();


        return redirect()->back()->with('success', 'Invoice settings updated successfully!');
    }

     public function taxSaving(Request $request)
    {
        $authUser = auth()->user();
        $company = Company::where('user_id', $authUser->id)->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        $validated['company_id'] = $company->id;

        if ($request->id) {
            Tax::where('id', $request->id)->update($validated);

            Notification::make()
                ->title('Tax Updated')
                ->body('Tax Updated successfully!')
                ->success()
                ->send();

            return back()->with('success', 'Tax updated successfully.');
        } else {
            Tax::create($validated);

               Notification::make()
                ->title('Tax Created')
                ->body('Tax Created successfully!')
                ->success()
                ->send();

            return back()->with('success', 'Tax created successfully.');
        }
    }
}
