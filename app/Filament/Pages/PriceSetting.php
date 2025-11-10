<?php

namespace App\Filament\Pages;

use App\Models\PriceBook;
use App\Models\PriceBookDetail;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use App\Models\Price;
use App\Models\User;
use App\Models\Company;
use Filament\Facades\Filament;

class PriceSetting extends Page
{

 use \Filament\Forms\Concerns\InteractsWithForms;


    protected static ?string $navigationIcon = 'heroicon-s-document-text';
    protected static ?string $title = 'Price Setting';
    protected static string $view = 'filament.pages.price-setting';
    protected static ?string $navigationGroup = 'Account';
    
   public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('manage-price-items');
        }
     public $showEditModal = false;
    public $editForm = [];
    public function getPriceBooksProperty(): Collection
    {
        $companyId = Company::where('user_id', auth()->id())->value('id');

        return PriceBook::with('priceBookDetails')
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->get();
    }
    

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addPriceBook')
                ->label('Add Price Book')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Add Price Book')
                ->form([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required(),

                    \Filament\Forms\Components\Grid::make(12)
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('external_id')
                                ->label('External ID')
                                ->columnSpan(6),

                            \Filament\Forms\Components\TextInput::make('xero_invoice_prefix')
                                ->label('Xero Invoice Prefix')
                                ->columnSpan(6),
                        ]),

                    \Filament\Forms\Components\Toggle::make('fixed_price')
                        ->label('Fixed Price Only'),

                    \Filament\Forms\Components\Toggle::make('provider_travel')
                        ->label('Provider Travel'),

                    \Filament\Forms\Components\Toggle::make('national_pricing')
                        ->label('Notional Pricing'),
                ])

                ->action(function (array $data): void {

                    $authUser = User::where('id',auth()->id())->first();
                    $getCompanyId = Company::where('user_id',$authUser->id)->first();

                    PriceBook::create([
                        'company_id' => $getCompanyId->id,
                        'name' => $data['name'] ?? null,
                        'external_id' => $data['external_id'] ?? null,
                        'xero_invoice_prefix' => $data['xero_invoice_prefix'] ?? null,
                        'fixed_price' => (bool) ($data['fixed_price'] ?? false),
                        'provider_travel' => (bool) ($data['provider_travel'] ?? false),
                        'national_pricing' => (bool) ($data['national_pricing'] ?? false),
                    ]);

                    Notification::make()
                        ->title('Price Book created')
                        ->success()
                        ->send();
                })
                ->modalSubmitActionLabel('Create'),
                

                
        ];
    }


    public ?int $selectedPriceBookId = null;
    public ?array $priceData = [];

    public function openNewPriceModal(int $priceBookId): void
    {
        $this->selectedPriceBookId = $priceBookId;
        $this->priceData = [];

        // Trigger modal open
        $this->dispatch('open-modal', id: 'new-price-modal');
    }

    public function closeNewPriceModal(): void
    {
        $this->dispatch('close-modal', id: 'new-price-modal');

        $this->selectedPriceBookId = null;
        $this->priceData = [];
    }

    public function savePriceRow(): void
    {
        if (! $this->selectedPriceBookId) {
            return;
        }

        PriceBookDetail::create([
            'price_book_id' => $this->selectedPriceBookId,
            'day_of_week' => $this->priceData['day_of_week'] ?? '',
            'start_time' => $this->priceData['start_time'] ?? '',
            'end_time' => $this->priceData['end_time'] ?? '',
            'per_hour' => $this->priceData['per_hour'] ?? 0,
            'ref_hour' => $this->priceData['ref_hour'] ?? '',
            'per_km' => $this->priceData['per_km'] ?? 0,
            'ref_km' => $this->priceData['ref_km'] ?? '',
            'effective_date' => $this->priceData['effective_date'] ?? '',
        ]);

        Notification::make()
            ->title('Price row saved')
            ->success()
            ->send();

        $this->closeNewPriceModal();
    }

        // Make sure these properties exist:
public ?int $editingPriceDetailId = null;
public array $editingPriceData = [];

// OPEN the edit modal by id and preload the form
public function openEditPriceModal(int $priceBookDetailId): void
{
    $detail = PriceBookDetail::findOrFail($priceBookDetailId);

    $this->editingPriceDetailId = $priceBookDetailId;
    $this->editingPriceData = [
        'day_of_week'    => $detail->day_of_week,
        'start_time'     => $detail->start_time ? \Carbon\Carbon::parse($detail->start_time)->format('H:i') : '',
        'end_time'       => $detail->end_time ? \Carbon\Carbon::parse($detail->end_time)->format('H:i') : '',
        'per_hour'       => $detail->per_hour,
        'ref_hour'       => $detail->ref_hour,
        'per_km'         => $detail->per_km,
        'ref_km'         => $detail->ref_km,
        'effective_date' => $detail->effective_date ? \Carbon\Carbon::parse($detail->effective_date)->format('Y-m-d') : '',
    ];

    // show the modal by id (same pattern as your New Price modal)
    $this->dispatch('open-modal', id: 'edit-price-modal');
}

// CLOSE the edit modal by id
public function closeEditPriceModal(): void
{
    $this->dispatch('close-modal', id: 'edit-price-modal');

    $this->editingPriceDetailId = null;
    $this->editingPriceData = [];
}

// UPDATE the record
public function updatePriceRow(): void
{
    if (! $this->editingPriceDetailId) {
        return;
    }

    $detail = PriceBookDetail::findOrFail($this->editingPriceDetailId);

    $detail->update([
        'day_of_week'    => $this->editingPriceData['day_of_week'] ?? '',
        'start_time'     => $this->editingPriceData['start_time'] ?? '',
        'end_time'       => $this->editingPriceData['end_time'] ?? '',
        'per_hour'       => $this->editingPriceData['per_hour'] ?? 0,
        'ref_hour'       => $this->editingPriceData['ref_hour'] ?? '',
        'per_km'         => $this->editingPriceData['per_km'] ?? 0,
        'ref_km'         => $this->editingPriceData['ref_km'] ?? '',
        'effective_date' => $this->editingPriceData['effective_date'] ?? '',
    ]);

    \Filament\Notifications\Notification::make()
        ->title('Price row updated')
        ->success()
        ->send();

    $this->closeEditPriceModal();
}



    public function deletePriceRow(int $priceBookDetailId): void
    {
        $priceBookDetail = PriceBookDetail::findOrFail($priceBookDetailId);
        $priceBookDetail->delete();

        Notification::make()
            ->title('Price row deleted')
            ->success()
            ->send();
    }

public function deletePriceBook(int $priceBookId)
{
    $priceBook = PriceBook::findOrFail($priceBookId);

    // Delete related price details
    $priceBook->priceBookDetails()->delete();

    // Delete the price book itself
    $priceBook->delete();

    Notification::make()
        ->title('Price book deleted')
        ->success()
        ->send();
}

    protected function getViewData(): array
    {
        return [
            'priceBooks' => $this->priceBooks,
            'page' => $this,
        ];
    }

    public function fetchPerHourFromRefValue(string $target, $value): void
{
    if (! isset($this->{$target}) || ! is_array($this->{$target})) {
        $this->{$target} = [];
    }

    $ref = trim((string) $value);

    $this->{$target}['ref_hour'] = $ref;

    $this->{$target}['per_hour'] = null;

    if ($ref === '') {
        return;
    }

    $price = Price::where('support_item_number', $ref)->first();

    if ($price) {
        $this->{$target}['per_hour'] = $price->act;
    } else {

        \Log::debug("[fetchPerHourFromRefValue] no price found for: {$ref}");
    }
}

public function fetchPerKmFromRefValue(string $target, $value): void
{
    if (! isset($this->{$target}) || ! is_array($this->{$target})) {
        $this->{$target} = [];
    }

    $ref = trim((string) $value);

    $this->{$target}['ref_km'] = $ref;

    $this->{$target}['per_km'] = null;

    if ($ref === '') {
        return;
    }

    $price = Price::where('support_item_number', $ref)->first();

    if ($price) {
        $this->{$target}['per_km'] = $price->act;
    } else {
        \Log::debug("[fetchPerKmFromRefValue] no price found for: {$ref}");
    }
}





     public function editPriceBook($id)
    {
        $book = PriceBook::find($id);

        if (! $book) {
            return;
        }

        $this->editForm = [
            'id' => $book->id,
            'name' => $book->name,
            'external_id' => $book->external_id,
            'xero_invoice_prefix' => $book->xero_invoice_prefix,
            'fixed_price' => (bool) $book->fixed_price,
            'provider_travel' => (bool) $book->provider_travel,
            'national_pricing' => (bool) $book->national_pricing,
        ];

        // This tells Filament to open the modal
        $this->dispatch('open-modal', id: 'editPriceBookModal');
    }

    public function updatePriceBook()
    {
        if (! $this->editForm['id']) {
            return;
        }

        PriceBook::where('id', $this->editForm['id'])->update([
            'name' => $this->editForm['name'],
            'external_id' => $this->editForm['external_id'],
            'xero_invoice_prefix' => $this->editForm['xero_invoice_prefix'],
            'fixed_price' => (bool) $this->editForm['fixed_price'],
            'provider_travel' => (bool) $this->editForm['provider_travel'],
            'national_pricing' => (bool) $this->editForm['national_pricing'],
        ]);

        $this->dispatch('close-modal', id: 'editPriceBookModal');
    }

        public function copyPriceRow(int $detailId): void
{
    $detail = PriceBookDetail::findOrFail($detailId);

    // Duplicate the record
    $newDetail = $detail->replicate();
    $newDetail->created_at = now();
    $newDetail->updated_at = now();
    $newDetail->save();

    Notification::make()
        ->title('Price copied successfully')
        ->success()
        ->send();
}

}
