<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Company;
use App\Models\InvoiceSetting;
use App\Models\Tax;
use Filament\Facades\Filament;

class InvoiceTaxSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static string $view = 'filament.pages.invoice-tax-settings';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $navigationLabel = 'Invoice Settings';

    public $invoiceSetting;
    public $tax;
    public $taxes;

    
    public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('manage-invoice-settings');
        }
    
    public function mount()
    {
        $authUser = auth()->user();
        $company = Company::where('user_id', $authUser->id)->first();

        if ($company) {
            $this->invoiceSetting = InvoiceSetting::where('company_id', $company->id)->first();
        } else {
            $this->invoiceSetting = null;
        }

        $this->taxes = Tax::where('company_id', $company->id)->get();

        $this->tax = Tax::where('company_id', $company->id)->first();
    }

    public function getTitle(): string
    {
        return 'Invoice Settings';
    }
}
