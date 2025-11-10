<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\PayGroup;
use App\Models\PriceBook;
use App\Models\PriceBookDetail;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use App\Models\Price;
use App\Models\User;
use App\Models\Company;
use App\Models\PayGroupDetail;
use Filament\Facades\Filament;

class PayGroupSetting extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-credit-card';
    protected static ?string $title = 'Pay Group';
    protected static string $view = 'filament.pages.pay-group-setting';
    protected static ?string $navigationGroup = 'Pay Items';
    
   public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('manage-price-items');
        }
     public $showEditModal = false;
    public $editForm = [];
    
    public function getPayGroupsProperty(): Collection
    {
        $auth = auth()->id();

        return PayGroup::with('payGroupDetails')
            ->where('user_id', $auth)
            ->where('is_archive' , 0)
            ->orderByDesc('id')
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addPayGroup')
                ->label('Add Pay Group')
                ->color('primary')
                ->icon('heroicon-m-plus')
                ->modalHeading('Add Pay Group')
                ->form([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required(),
                ])

                ->action(function (array $data): void {

                    $authUser = auth()->id();

                    PayGroup::create([
                        'user_id' => $authUser,
                        'name' => $data['name'] ?? null,
                    ]);

                    Notification::make()
                        ->title('Pay Group created successfully')
                        ->success()
                        ->send();
                })
                ->modalSubmitActionLabel('Create'),
                

                
        ];
    }


    public ?int $selectedPayGroupId = null;
    public ?array $payItemData = [];

    public function openNewPayItem(int $payGroupId): void
    {
        $this->selectedPayGroupId = $payGroupId;
        $this->payItemData = [];

        // Trigger modal open
        $this->dispatch('open-modal', id: 'new-pay-item');
    }

    public function closeNewPayItemModal(): void
    {
        $this->dispatch('close-modal', id: 'new-pay-item');

        $this->selectedPayGroupId = null;
        $this->payItemData = [];
    }

    public function savePriceItemRow(): void
    {
        if (! $this->selectedPayGroupId) {
            return;
        }

        PayGroupDetail::create([
            'pay_group_id'   => $this->selectedPayGroupId,
            'day_of_week'    => $this->payItemData['day_of_week'] ?? 'monday', 
            'start_time'     => $this->payItemData['start_time'] ?? '00:00:00',  
            'end_time'       => $this->payItemData['end_time'] ?? '00:00:00',  
            'effective_date' => $this->payItemData['effective_date'] ?? now()->toDateString(),  
            'price' => $this->payItemData['price'] ?? null,  
        ]);

        Notification::make()
            ->title('Pay Item saved')
            ->success()
            ->send();

        $this->closeNewPayItemModal();
    }


        // Make sure these properties exist:
public ?int $editingPayGroupDetailId = null;
public array $editingpayItemData = [];

// OPEN the edit modal by id and preload the form
public function openEditPayItemModal(int $payGroupDetailId): void
{
    $detail = PayGroupDetail::findOrFail($payGroupDetailId);

    $this->editingPayGroupDetailId = $payGroupDetailId;
    $this->editingpayItemData = [
        'day_of_week'    => $detail->day_of_week,
        'start_time'     => $detail->start_time ? \Carbon\Carbon::parse($detail->start_time)->format('H:i') : '',
        'end_time'       => $detail->end_time ? \Carbon\Carbon::parse($detail->end_time)->format('H:i') : '',
        'effective_date' => $detail->effective_date ? \Carbon\Carbon::parse($detail->effective_date)->format('Y-m-d') : '',
        'price' => $detail->price ?? null,
    ];

    // show the modal by id (same pattern as your New Price modal)
    $this->dispatch('open-modal', id: 'edit-pay-item-modal');
}

// CLOSE the edit modal by id
public function closeEditPayItemModal(): void
{
    $this->dispatch('close-modal', id: 'edit-pay-item-modal');

    $this->editingPayGroupDetailId = null;
    $this->editingpayItemData = [];
}

// UPDATE the record
public function updatePayItemRow(): void
{
    if (! $this->editingPayGroupDetailId) {
        return;
    }

    $detail = PayGroupDetail::findOrFail($this->editingPayGroupDetailId);

    $detail->update([
        'day_of_week'    => $this->editingpayItemData['day_of_week'] ?? '',
        'start_time'     => $this->editingpayItemData['start_time'] ?? '',
        'end_time'       => $this->editingpayItemData['end_time'] ?? '',
        'effective_date' => $this->editingpayItemData['effective_date'] ?? '',
        'price' => $this->editingpayItemData['price'] ?? '',
    ]);

    \Filament\Notifications\Notification::make()
        ->title('Pay Item updated')
        ->success()
        ->send();

    $this->closeEditPayItemModal();
}



    public function deletePayItemRow(int $payGroupDetailId): void
    {
        $payGroupDetail = PayGroupDetail::findOrFail($payGroupDetailId);
        $payGroupDetail->delete();

        Notification::make()
            ->title('Pay Item deleted')
            ->success()
            ->send();
    }

public function deletePayGroup(int $payGroupId): void
{
    $payGroup = PayGroup::findOrFail($payGroupId);

    $payGroup->update([
        'is_archive' => 1,
    ]);

    Notification::make()
        ->title('Pay Group archived')
        ->success()
        ->send();
}

    protected function getViewData(): array
    {
        return [
            'payGroups' => $this->payGroups, // uses computed property
            'page' => $this,
        ];
    }

     public function editPayGroup($id)
    {
        $book = PayGroup::find($id);

        if (! $book) {
            return;
        }

        $this->editForm = [
            'id' => $book->id,
            'name' => $book->name,
        ];

        // This tells Filament to open the modal
        $this->dispatch('open-modal', id: 'editPayGroupModal');
    }

    public function updatePayGroup()
    {
        if (! $this->editForm['id']) {
            return;
        }

        PayGroup::where('id', $this->editForm['id'])->update([
            'name' => $this->editForm['name'],
        ]);

        Notification::make()
        ->success()
        ->title('Pay Group updated')
        ->send();

        $this->dispatch('close-modal', id: 'editPayGroupModal');
    }

    public function copyPayItemRow(int $detailId): void
{
    $detail = PayGroupDetail::findOrFail($detailId);

    // Duplicate the record
    $newDetail = $detail->replicate();
    $newDetail->created_at = now();
    $newDetail->updated_at = now();
    $newDetail->save();

    Notification::make()
        ->title('Pay Item copied successfully')
        ->success()
        ->send();
}

}
