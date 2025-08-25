<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Actions\Action;
use Filament\Support\Enums\IconPosition;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use App\Models\ShiftType;
use Livewire\Attributes\On;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Forms\Components\Repeater;
use Carbon\Carbon;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Actions\Action as NewAction; 
use Illuminate\Support\Arr;

class Setting extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-cog-6-tooth';
    protected static string $view = 'filament.pages.setting';
    protected static ?string $navigationGroup = 'Account';

    public function infolist(Infolist $infolist): Infolist
    {
        $authUser = Auth::user();
        $companyData = Company::where('user_id', $authUser->id)->first();

        return $infolist
            ->state([
                'company_logo'          => $companyData->company_logo,
                'name'                  => $companyData->name,
                'country'               => $companyData->country,
                'staff_invitation_link' => $companyData->staff_invitation_link,
                'sms_sent'              => '1',
                'mobile_upload'         => 'Enabled',
                'incident_management'   => 'Enabled',
            ])
            ->schema([

                // 🟩 Logo
                Section::make()
                    ->schema([
                        ImageEntry::make('company_logo')
                            ->label('')
                            ->defaultImageUrl('https://via.placeholder.com/150x150/4ade80/ffffff?text=ESS')
                            ->size(150)
                            ->extraAttributes(['class' => 'mx-auto']),
                    ])
                    ->columnSpan(2)
                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;']),

                // 🟦 Company details + Shift Types
                Section::make('')
                    ->schema([

                        // 🟦 Company Details
                        Section::make('Company details')
                            ->schema([
                                TextEntry::make('name')->label('Name'),
                                TextEntry::make('country')
                                    ->label('Country')
                                    ->suffixAction(
                                        InfolistAction::make('help')
                                            ->icon('heroicon-s-question-mark-circle')
                                            ->iconPosition(IconPosition::After)
                                            ->color('gray')
                                            ->size('sm')
                                    ),
                                TextEntry::make('staff_invitation_link')
                                    ->label('Staff Invitation Link')
                                    ->copyable()
                                    ->copyMessage('Invitation link copied to clipboard!')
                                    ->copyMessageDuration(1500)
                                    ->formatStateUsing(fn() => 'Click to copy')
                                    ->extraAttributes([
                                        'class' => 'ml-auto block w-fit text-sm font-medium text-white bg-primary-600 px-4 py-1.5 rounded-md hover:bg-primary-700 transition text-right',
                                    ]),
                            ])
                            ->headerActions([
                                InfolistAction::make('edit')
                                    ->label('Edit')
                                    ->icon('heroicon-s-pencil')
                                    ->url('/admin/profile-setting')
                                    ->color('primary'),
                            ]),

                        // 🟨 Shift Types Section
                   Section::make('Shift types')
                        ->schema([
                            \Filament\Infolists\Components\ViewEntry::make('shift_types')
                                ->view('infolists.components.shift-types-badges')
                                ->getStateUsing(function () {
                                    return ShiftType::query()
                                        ->where('user_id', Auth::id())
                                        ->where('is_archive', 0)
                                        ->get(['name','color']);
                                }),
                        ])
                            ->headerActions([
                                InfolistAction::make('manageShiftTypes')
                                    ->label('Edit')
                                    ->icon('heroicon-s-pencil')
                                    ->color('primary')
                                    ->slideOver()
                                    ->modalHeading('Manage Shift Types')
                                    ->modalWidth('4xl')
                                    ->form([

                                        Tabs::make('shift_type_tabs')
                                            ->tabs([

                                                // 🔹 Active Tab
                                                Tabs\Tab::make('Active')
                                                    ->schema([
                                                        Repeater::make('shiftTypes')
                                                            ->label('Active Shift Types')
                                                            ->schema([
                                                                Grid::make(4)->schema([
                                                                    TextInput::make('name')
                                                                        ->label('Name')
                                                                        ->required(),
                                                                    TextInput::make('external_id')
                                                                        ->label('External ID'),
                                                                    ColorPicker::make('color')
                                                                        ->label('Color'),
                                                                    Placeholder::make('updated_at')
                                                                        ->label('Updated At')
                                                                        ->content(fn($get) => $get('updated_at')
                                                                            ? Carbon::parse($get('updated_at'))->diffForHumans()
                                                                            : '—'),
                                                                ]),
                                                            ])
                                                            ->addActionLabel('Add Shift Types')
                                                            ->extraItemActions([
                                                                NewAction::make('archive')
                                                                    ->label('Archive')
                                                                    ->icon('heroicon-m-trash')
                                                                    ->color('danger')
                                                                    ->requiresConfirmation()
                                                                    ->action(function ($state) {
                                                                        $record = reset($state);

                                                                        \App\Models\ShiftType::where('id', $record['id'])
                                                                            ->update(['is_archive' => 1]);

                                                                        Notification::make()
                                                                            ->title('Shift type archived successfully!')
                                                                            ->success()
                                                                            ->send();

                                                                        // Refresh the whole page
                                                                        return redirect(request()->header('Referer'));
                                                                    }),
                                                            ])
                                                            ->deletable(false)
                                                            ->columns(4)
                                                            ->default(fn() => ShiftType::where('is_archive', 0)
                                                                ->where('user_id', auth()->id())
                                                                ->get()
                                                                ->toArray()),
                                                    ]),

                                                // 🔹 Archived Tab
                                                Tabs\Tab::make('Archived')
                                                    ->schema([
                                                        Repeater::make('archivedShiftTypes')
                                                            ->label('Archived Shift Types')
                                                            ->schema([
                                                                Hidden::make('id'), // 👈 store DB id in repeater item
                                                                Grid::make(4)->schema([
                                                                    TextInput::make('name')->label('Name')->required(),
                                                                    TextInput::make('external_id')->label('External ID'),
                                                                    ColorPicker::make('color')->label('Color'),
                                                                    Placeholder::make('updated_at')
                                                                        ->label('Updated At')
                                                                        ->content(fn($get) => $get('updated_at')
                                                                            ? \Carbon\Carbon::parse($get('updated_at'))->diffForHumans()
                                                                            : '—'),
                                                                ]),
                                                            ])
                                                            ->disabled()
                                                            ->addable(false)
                                                            ->extraItemActions([
                                                                NewAction::make('unarchive')
                                                                    ->label('Unarchive')
                                                                    ->icon('heroicon-m-arrow-up-tray')
                                                                    ->requiresConfirmation()
                                                                    ->action(function ($state) {
                                                                        $record = reset($state);

                                                                        \App\Models\ShiftType::where('id', $record['id'])
                                                                            ->update(['is_archive' => 0]);

                                                                        Notification::make()
                                                                            ->title('Shift type unarchived successfully!')
                                                                            ->success()
                                                                            ->send();

                                                                        // Refresh the whole page
                                                                        return redirect(request()->header('Referer'));
                                                                    }),
                                                            ])
                                                            ->deletable(false)
                                                            ->columns(4)
                                                            ->default(fn() => \App\Models\ShiftType::where('is_archive', 1)
                                                                ->where('user_id', auth()->id())
                                                                ->get()
                                                                ->toArray()),
                                                    ]),
                                            ]),
                                    ])
                                    ->action(function (array $data): void {
                                        // Collect rows from any repeater key you’re using
                                        $possibleKeys = ['shift_types', 'shiftTypes', 'activeShiftTypes', 'archivedShiftTypes'];

                                        $items = [];
                                        foreach ($possibleKeys as $key) {
                                            if (isset($data[$key]) && is_array($data[$key])) {
                                                $items = array_merge($items, $data[$key]);
                                            }
                                        }

                                        if (empty($items)) {
                                            Notification::make()
                                                ->title('Nothing to save')
                                                ->warning()
                                                ->send();
                                            return;
                                        }

                                        foreach ($items as $row) {
                                            // id may be missing for new rows
                                            $id = $row['id'] ?? null;

                                            // Clean payload (avoid timestamp/id writes)
                                            $payload              = Arr::except($row, ['id', 'created_at', 'updated_at', 'deleted_at']);
                                            $payload['user_id']   = Auth::id();
                                            $payload['is_archive'] = !empty($payload['is_archive']) ? 1 : 0;

                                            if ($id) {
                                                ShiftType::where('id', $id)
                                                    ->where('user_id', Auth::id())
                                                    ->update($payload);
                                            } else {
                                                ShiftType::create($payload);
                                            }
                                        }

                                        Notification::make()
                                            ->title('Shift types updated successfully!')
                                            ->success()
                                            ->send();
                                    }),
                            ]),
                    ])
                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;'])
                    ->columns(1)
                    ->columnSpan(5),

                // 🟧 Other sections
                Section::make('')
                    ->schema([
                        Section::make('Information headings')
                            ->schema([
                                TextEntry::make('need_to_know')
                                    ->label('Need to know information')
                                    ->suffixAction(
                                        InfolistAction::make('manage')
                                            ->label('Manage')
                                            ->icon('heroicon-s-cog-6-tooth')
                                            ->color('primary')
                                            ->size('sm')
                                    ),
                                TextEntry::make('useful_info')
                                    ->label('Useful information')
                                    ->suffixAction(
                                        InfolistAction::make('manage')
                                            ->label('Manage')
                                            ->icon('heroicon-s-cog-6-tooth')
                                            ->color('primary')
                                            ->size('sm')
                                    ),
                            ]),

                        Section::make('Incident Management')
                            ->schema([
                                TextEntry::make('incident_management')
                                    ->label('Enable Incident Management in Carers App')
                                    ->badge('Enabled'),
                            ])
                            ->headerActions([
                                InfolistAction::make('edit')
                                    ->label('Edit')
                                    ->icon('heroicon-s-pencil')
                                    ->color('primary'),
                            ])
                            ->columnSpanFull(),

                        Section::make('Notes headings')
                            ->schema([])
                            ->columnSpanFull(),
                    ])
                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;'])
                    ->columns(1)
                    ->columnSpan(5),
            ])
            ->columns(12);
    }

    #[On('open-edit-shift')]
    public function openEditShift($id)
    {
        $this->dispatchBrowserEvent('filament-open-modal', [
            'id' => 'edit-shift-' . $id,
        ]);
    }
}
