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
use Filament\Infolists\Components\View as InfolistView;
use App\Models\DocumentCategory;
use Filament\Forms;
use Illuminate\Support\Arr;
use App\Models\ClientType;
use App\Models\PublicHoliday;
use App\Models\JobTitle;
use Filament\Forms\Components\Textarea;

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
                             Section::make('Quotes')
                                    ->schema([
                                        InfolistView::make('filament.infolists.setting-quotes')
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull()
                                    ->headerActions([
                                        InfolistAction::make('edit_quotes')
                                            ->label('EDIT')
                                            ->size('sm')
                                            ->icon('heroicon-s-pencil-square')
                                            ->form(function (Forms\Form $form) {
                                                return $form->schema([
                                                    Forms\Components\TextInput::make('quote_title')
                                                        ->label('Quote Title')
                                                        ->maxLength(255),

                                                   Textarea::make('quote_terms')
                                                        ->label('Quote Terms')
                                                        ->rows(6),
                                                ]);
                                            })
                                            ->action(function (array $data) {
                                                $user = Auth::user();

                                                $company = Company::where('user_id', $user->id)->first();

                                                if (! $company) {
                                                    Notification::make()
                                                        ->title('Company not found!')
                                                        ->danger()
                                                        ->send();
                                                    return;
                                                }

                                                $company->update([
                                                    'quote_title' => $data['quote_title'],
                                                    'quote_terms' => $data['quote_terms'],
                                                ]);

                                                Notification::make()
                                                    ->title('Quote details updated successfully!')
                                                    ->success()
                                                    ->send();
                                            })
                                            ->mountUsing(function (Forms\Form $form) {
                                                $user = Auth::user();
                                                $company = Company::where('user_id', $user->id)->first();

                                                if ($company) {
                                                    $form->fill([
                                                        'quote_title' => $company->quote_title,
                                                        'quote_terms' => $company->quote_terms,
                                                    ]);
                                                }
                                            }),
                                        ]),

                                         Section::make('Scheduler')
                                                            ->schema([
                                                                InfolistView::make('filament.infolists.setting-schedular')
                                                                    ->columnSpanFull(),
                                                            ])
                                                            ->columnSpanFull()
                                                            ->headerActions([
                                                                InfolistAction::make('edit_schedular')
                                                                    ->label('EDIT')
                                                                    ->size('sm')
                                                                    ->icon('heroicon-s-pencil-square')
                                                                    ->form(function (Forms\Form $form) {
                                                                        $user = Auth::user();
                                                                        $company = Company::where('user_id', $user->id)->first();

                                                                        $clientTypes = ClientType::where('company_id', $company?->id)
                                                                            ->pluck('name')
                                                                            ->toArray();

                                                                        return $form->schema([
                                                                            Forms\Components\Select::make('timezone')
                                                                                ->label('Timezone')
                                                                                 ->options([
                                                                                        'International Date Line West' => 'International Date Line West',
                                                                                        'American Samoa' => 'American Samoa',
                                                                                        'Midway Island' => 'Midway Island',
                                                                                        'Hawaii' => 'Hawaii',
                                                                                        'Alaska' => 'Alaska',
                                                                                        'Pacific Time (US & Canada)' => 'Pacific Time (US & Canada)',
                                                                                        'Tijuana' => 'Tijuana',
                                                                                        'Arizona' => 'Arizona',
                                                                                        'Mazatlan' => 'Mazatlan',
                                                                                        'Mountain Time (US & Canada)' => 'Mountain Time (US & Canada)',
                                                                                        'Central America' => 'Central America',
                                                                                        'Central Time (US & Canada)' => 'Central Time (US & Canada)',
                                                                                        'Chihuahua' => 'Chihuahua',
                                                                                        'Guadalajara' => 'Guadalajara',
                                                                                        'Mexico City' => 'Mexico City',
                                                                                        'Monterrey' => 'Monterrey',
                                                                                        'Saskatchewan' => 'Saskatchewan',
                                                                                        'Bogota' => 'Bogota',
                                                                                        'Eastern Time (US & Canada)' => 'Eastern Time (US & Canada)',
                                                                                        'Indiana (East)' => 'Indiana (East)',
                                                                                        'Lima' => 'Lima',
                                                                                        'Quito' => 'Quito',
                                                                                        'Atlantic Time (Canada)' => 'Atlantic Time (Canada)',
                                                                                        'Caracas' => 'Caracas',
                                                                                        'Georgetown' => 'Georgetown',
                                                                                        'La Paz' => 'La Paz',
                                                                                        'Puerto Rico' => 'Puerto Rico',
                                                                                        'Santiago' => 'Santiago',
                                                                                        'Newfoundland' => 'Newfoundland',
                                                                                        'Brasilia' => 'Brasilia',
                                                                                        'Buenos Aires' => 'Buenos Aires',
                                                                                        'Montevideo' => 'Montevideo',
                                                                                        'Greenland' => 'Greenland',
                                                                                        'Mid-Atlantic' => 'Mid-Atlantic',
                                                                                        'Azores' => 'Azores',
                                                                                        'Cape Verde Is.' => 'Cape Verde Is.',
                                                                                        'Edinburgh' => 'Edinburgh',
                                                                                        'Lisbon' => 'Lisbon',
                                                                                        'London' => 'London',
                                                                                        'Monrovia' => 'Monrovia',
                                                                                        'UTC' => 'UTC',
                                                                                        'Amsterdam' => 'Amsterdam',
                                                                                        'Belgrade' => 'Belgrade',
                                                                                        'Berlin' => 'Berlin',
                                                                                        'Bern' => 'Bern',
                                                                                        'Bratislava' => 'Bratislava',
                                                                                        'Brussels' => 'Brussels',
                                                                                        'Budapest' => 'Budapest',
                                                                                        'Casablanca' => 'Casablanca',
                                                                                        'Copenhagen' => 'Copenhagen',
                                                                                        'Dublin' => 'Dublin',
                                                                                        'Ljubljana' => 'Ljubljana',
                                                                                        'Madrid' => 'Madrid',
                                                                                        'Paris' => 'Paris',
                                                                                        'Prague' => 'Prague',
                                                                                        'Rome' => 'Rome',
                                                                                        'Sarajevo' => 'Sarajevo',
                                                                                        'Skopje' => 'Skopje',
                                                                                        'Stockholm' => 'Stockholm',
                                                                                        'Vienna' => 'Vienna',
                                                                                        'Warsaw' => 'Warsaw',
                                                                                        'West Central Africa' => 'West Central Africa',
                                                                                        'Zagreb' => 'Zagreb',
                                                                                        'Zurich' => 'Zurich',
                                                                                        'Athens' => 'Athens',
                                                                                        'Bucharest' => 'Bucharest',
                                                                                        'Cairo' => 'Cairo',
                                                                                        'Harare' => 'Harare',
                                                                                        'Helsinki' => 'Helsinki',
                                                                                        'Jerusalem' => 'Jerusalem',
                                                                                        'Kaliningrad' => 'Kaliningrad',
                                                                                        'Kyiv' => 'Kyiv',
                                                                                        'Pretoria' => 'Pretoria',
                                                                                        'Riga' => 'Riga',
                                                                                        'Sofia' => 'Sofia',
                                                                                        'Tallinn' => 'Tallinn',
                                                                                        'Vilnius' => 'Vilnius',
                                                                                        'Baghdad' => 'Baghdad',
                                                                                        'Istanbul' => 'Istanbul',
                                                                                        'Kuwait' => 'Kuwait',
                                                                                        'Minsk' => 'Minsk',
                                                                                        'Moscow' => 'Moscow',
                                                                                        'Nairobi' => 'Nairobi',
                                                                                        'Riyadh' => 'Riyadh',
                                                                                        'St. Petersburg' => 'St. Petersburg',
                                                                                        'Volgograd' => 'Volgograd',
                                                                                        'Tehran' => 'Tehran',
                                                                                        'Abu Dhabi' => 'Abu Dhabi',
                                                                                        'Baku' => 'Baku',
                                                                                        'Muscat' => 'Muscat',
                                                                                        'Samara' => 'Samara',
                                                                                        'Tbilisi' => 'Tbilisi',
                                                                                        'Yerevan' => 'Yerevan',
                                                                                        'Kabul' => 'Kabul',
                                                                                        'Almaty' => 'Almaty',
                                                                                        'Ekaterinburg' => 'Ekaterinburg',
                                                                                        'Islamabad' => 'Islamabad',
                                                                                        'Karachi' => 'Karachi',
                                                                                        'Tashkent' => 'Tashkent',
                                                                                        'Chennai' => 'Chennai',
                                                                                        'Kolkata' => 'Kolkata',
                                                                                        'Mumbai' => 'Mumbai',
                                                                                        'New Delhi' => 'New Delhi',
                                                                                        'Sri Jayawardenepura' => 'Sri Jayawardenepura',
                                                                                        'Kathmandu' => 'Kathmandu',
                                                                                        'Astana' => 'Astana',
                                                                                        'Dhaka' => 'Dhaka',
                                                                                        'Urumqi' => 'Urumqi',
                                                                                        'Rangoon' => 'Rangoon',
                                                                                        'Bangkok' => 'Bangkok',
                                                                                        'Hanoi' => 'Hanoi',
                                                                                        'Jakarta' => 'Jakarta',
                                                                                        'Krasnoyarsk' => 'Krasnoyarsk',
                                                                                        'Novosibirsk' => 'Novosibirsk',
                                                                                        'Beijing' => 'Beijing',
                                                                                        'Chongqing' => 'Chongqing',
                                                                                        'Hong Kong' => 'Hong Kong',
                                                                                        'Irkutsk' => 'Irkutsk',
                                                                                        'Kuala Lumpur' => 'Kuala Lumpur',
                                                                                        'Perth' => 'Perth',
                                                                                        'Singapore' => 'Singapore',
                                                                                        'Taipei' => 'Taipei',
                                                                                        'Ulaanbaatar' => 'Ulaanbaatar',
                                                                                        'Osaka' => 'Osaka',
                                                                                        'Sapporo' => 'Sapporo',
                                                                                        'Seoul' => 'Seoul',
                                                                                        'Tokyo' => 'Tokyo',
                                                                                        'Yakutsk' => 'Yakutsk',
                                                                                        'Adelaide' => 'Adelaide',
                                                                                        'Darwin' => 'Darwin',
                                                                                        'Brisbane' => 'Brisbane',
                                                                                        'Canberra' => 'Canberra',
                                                                                        'Guam' => 'Guam',
                                                                                        'Hobart' => 'Hobart',
                                                                                        'Melbourne' => 'Melbourne',
                                                                                        'Port Moresby' => 'Port Moresby',
                                                                                        'Sydney' => 'Sydney',
                                                                                        'Vladivostok' => 'Vladivostok',
                                                                                        'Magadan' => 'Magadan',
                                                                                        'New Caledonia' => 'New Caledonia',
                                                                                        'Solomon Is.' => 'Solomon Is.',
                                                                                        'Srednekolymsk' => 'Srednekolymsk',
                                                                                        'Auckland' => 'Auckland',
                                                                                        'Fiji' => 'Fiji',
                                                                                        'Kamchatka' => 'Kamchatka',
                                                                                        'Marshall Is.' => 'Marshall Is.',
                                                                                        'Wellington' => 'Wellington',
                                                                                        'Chatham Is.' => 'Chatham Is.',
                                                                                        "Nuku'alofa" => "Nuku'alofa",
                                                                                        'Samoa' => 'Samoa',
                                                                                        'Tokelau Is.' => 'Tokelau Is.',
                                                                                    ])
                                                                                    ->searchable()
                                                                                    ->preload()
                                                                                ->default($company?->timezone)
                                                                                ->columnSpanFull(),

                                                                            Forms\Components\Select::make('minute_interval')
                                                                                ->label('Minute Interval')
                                                                                ->options([
                                                                                    '5' => '5 minutes',
                                                                                    '10' => '10 minutes',
                                                                                    '15' => '15 minutes',
                                                                                    '30' => '30 minutes',
                                                                                    '60' => '60 minutes',
                                                                                ])
                                                                                ->default($company?->minute_interval)
                                                                                ->columnSpanFull(),

                                                                            Forms\Components\Select::make('pay_run')
                                                                                ->label('Pay Run')
                                                                                 ->options([
                                                                                    'Weekly' => 'Weekly',
                                                                                    'Fortnightly' => 'Fortnightly',
                                                                                ])
                                                                                ->placeholder('Enter pay run (e.g., Weekly, Fortnightly, Monthly)')
                                                                                ->default($company?->pay_run)
                                                                                ->columnSpanFull(),

                                                                            Forms\Components\TagsInput::make('client_types')
                                                                                ->label('Client Types')
                                                                                ->placeholder('Add or remove client types')
                                                                                ->default($clientTypes)
                                                                                ->columnSpanFull(),
                                                                        ]);
                                                                    })
                                                                    ->action(function (array $data) {
                                                                        $user = Auth::user();
                                                                        $company = Company::where('user_id', $user->id)->first();

                                                                        if (!$company) {
                                                                            Notification::make()
                                                                                ->title('Company not found for this user!')
                                                                                ->danger()
                                                                                ->send();
                                                                            return;
                                                                        }

                                                                        // ✅ Update company fields
                                                                        $company->update([
                                                                            'timezone' => $data['timezone'] ?? null,
                                                                            'minute_interval' => $data['minute_interval'] ?? null,
                                                                            'pay_run' => $data['pay_run'] ?? null,
                                                                        ]);

                                                                        // ✅ Refresh client types
                                                                        ClientType::where('company_id', $company->id)->delete();

                                                                        foreach ($data['client_types'] ?? [] as $type) {
                                                                            ClientType::create([
                                                                                'company_id' => $company->id,
                                                                                'name' => $type,
                                                                            ]);
                                                                        }

                                                                        Notification::make()
                                                                            ->title('Scheduler settings updated successfully!')
                                                                            ->success()
                                                                            ->send();
                                                                    })
                                                                    ->mountUsing(function (Forms\Form $form) {
                                                                        $user = Auth::user();
                                                                        $company = Company::where('user_id', $user->id)->first();

                                                                        $form->fill([
                                                                            'timezone' => $company?->timezone,
                                                                            'minute_interval' => $company?->minute_interval,
                                                                            'pay_run' => $company?->pay_run,
                                                                            'client_types' => ClientType::where('company_id', $company?->id)
                                                                                ->pluck('name')
                                                                                ->toArray(),
                                                                        ]);
                                                                    }),
                                                                ]),

                                                        Section::make('Public holidays')
                                                                    ->schema([
                                                                        InfolistView::make('filament.infolists.public-holidays')
                                                                            ->columnSpanFull(),
                                                                    ])
                                                                    ->columnSpanFull()
                                                                    ->headerActions([
                                                                        InfolistAction::make('edit_holiday')
                                                                            ->label('EDIT')
                                                                            ->size('sm')
                                                                            ->icon('heroicon-s-pencil-square')
                                                                            ->form(function (Forms\Form $form) {
                                                                                $user = Auth::user();
                                                                                $company = Company::where('user_id', $user->id)->first();

                                                                                return $form->schema([
                                                                                    Forms\Components\Repeater::make('holidays')
                                                                                        ->label('Add Public Holidays')
                                                                                        ->schema([
                                                                                            Forms\Components\DatePicker::make('date')
                                                                                                ->label('Holiday Date')
                                                                                                ->required()
                                                                                                ->unique(
                                                                                                    table: PublicHoliday::class,
                                                                                                    column: 'date',
                                                                                                    ignoreRecord: true
                                                                                                ),
                                                                                        ])
                                                                                        ->columns(1)
                                                                                        ->addActionLabel('Add another holiday')
                                                                                        ->defaultItems(1)
                                                                                        ->disableItemDeletion() // ✅ No delete allowed
                                                                                        ->disableLabel(),
                                                                                ]);
                                                                            })
                                                                            ->action(function (array $data) {
                                                                                $user = Auth::user();
                                                                                $company = Company::where('user_id', $user->id)->first();

                                                                                if (!$company) {
                                                                                    Notification::make()
                                                                                        ->title('Company not found!')
                                                                                        ->danger()
                                                                                        ->send();
                                                                                    return;
                                                                                }

                                                                                foreach ($data['holidays'] ?? [] as $holiday) {
                                                                                    // ✅ Check if date already exists
                                                                                    $exists = PublicHoliday::where('company_id', $company->id)
                                                                                        ->whereDate('date', $holiday['date'])
                                                                                        ->exists();

                                                                                    if (!$exists) {
                                                                                        PublicHoliday::create([
                                                                                            'company_id' => $company->id,
                                                                                            'date' => $holiday['date'],
                                                                                        ]);
                                                                                    }
                                                                                }

                                                                                Notification::make()
                                                                                    ->title('Public holidays added successfully!')
                                                                                    ->success()
                                                                                    ->send();
                                                                            })
                                                                            
                                                                        ]),

                                                                    Section::make('Job Titles')
                                                                                ->schema([
                                                                                    InfolistView::make('filament.infolists.setting-job-titles')
                                                                                        ->columnSpanFull(),
                                                                                ])
                                                                                ->columnSpanFull()
                                                                                ->headerActions([
                                                                                    InfolistAction::make('edit_job_titles')
                                                                                        ->label('EDIT')
                                                                                        ->size('sm')
                                                                                        ->icon('heroicon-s-pencil-square')
                                                                                        ->form(function (Forms\Form $form) {
                                                                                            $user = Auth::user();
                                                                                            $company = Company::where('user_id', $user->id)->first();

                                                                                            $jobTitles = JobTitle::where('company_id', $company?->id)
                                                                                                ->pluck('name')
                                                                                                ->toArray();

                                                                                            return $form->schema([
                                                                                                Forms\Components\TagsInput::make('job_titles')
                                                                                                    ->label('Job Titles')
                                                                                                    ->placeholder('Add or remove job titles')
                                                                                                    ->default($jobTitles)
                                                                                                    ->columnSpanFull(),
                                                                                            ]);
                                                                                        })
                                                                                        ->action(function (array $data) {
                                                                                            $user = Auth::user();
                                                                                            $company = Company::where('user_id', $user->id)->first();

                                                                                            if (!$company) {
                                                                                                Notification::make()
                                                                                                    ->title('Company not found!')
                                                                                                    ->danger()
                                                                                                    ->send();
                                                                                                return;
                                                                                            }

                                                                                            $titles = $data['job_titles'] ?? [];

                                                                                            $existingTitles = JobTitle::where('company_id', $company->id)
                                                                                                ->pluck('name')
                                                                                                ->toArray();

                                                                                            $toDelete = array_diff($existingTitles, $titles);
                                                                                            if (!empty($toDelete)) {
                                                                                                JobTitle::where('company_id', $company->id)
                                                                                                    ->whereIn('name', $toDelete)
                                                                                                    ->delete();
                                                                                            }

                                                                                            foreach ($titles as $title) {
                                                                                                if (!in_array($title, $existingTitles)) {
                                                                                                    JobTitle::create([
                                                                                                        'company_id' => $company->id,
                                                                                                        'name' => $title,
                                                                                                    ]);
                                                                                                }
                                                                                            }

                                                                                            Notification::make()
                                                                                                ->title('Job titles updated successfully!')
                                                                                                ->success()
                                                                                                ->send();
                                                                                        })
                                                                                        ->mountUsing(function (Forms\Form $form) {
                                                                                            $user = Auth::user();
                                                                                            $company = Company::where('user_id', $user->id)->first();

                                                                                            $form->fill([
                                                                                                'job_titles' => JobTitle::where('company_id', $company?->id)
                                                                                                    ->pluck('name')
                                                                                                    ->toArray(),
                                                                                            ]);
                                                                                        }),
                                                                                    ]),



                    ])
                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;'])
                    ->columns(1)
                    ->columnSpan(5),

                // 🟧 Other sections
                Section::make('')
                    ->schema([
                       



                                            Section::make('Client Document Categories')
                                                ->schema([
                                                    InfolistView::make('filament.infolists.setting-client-docs')
                                                        ->columnSpanFull(),
                                                ])
                                                ->columnSpanFull()
                                                ->headerActions([
                                                    InfolistAction::make('edit_docs')
                                                        ->label('EDIT')
                                                        ->size('sm')
                                                        ->icon('heroicon-s-pencil-square')
                                                        ->form(function (Forms\Form $form) {
                                                            $user = Auth::user();
                                                            $companyId = Company::where('user_id', $user->id)->value('id');

                                                            // Get all category names for this company (client ones only)
                                                            $existingCategories = DocumentCategory::where('company_id', $companyId)
                                                                ->where('is_staff_doc', 0)
                                                                ->pluck('name')
                                                                ->toArray();

                                                            return $form->schema([
                                                                Forms\Components\TagsInput::make('categories')
                                                                    ->label('Client Document Categories')
                                                                    ->placeholder('Type category name and press Enter...')
                                                                    ->helperText('Press Enter to add multiple categories.')
                                                                    ->default($existingCategories)
                                                                    ->columnSpanFull(),
                                                            ]);
                                                        })
                                                        ->action(function (array $data) {
                                                            $user = Auth::user();
                                                            $companyId = Company::where('user_id', $user->id)->value('id');

                                                            if (! $companyId) {
                                                                Notification::make()
                                                                    ->title('Company not found!')
                                                                    ->danger()
                                                                    ->send();
                                                                return;
                                                            }

                                                            // ✅ Remove only existing client categories
                                                            DocumentCategory::where('company_id', $companyId)
                                                                ->where('is_staff_doc', 0)
                                                                ->delete();

                                                            // ✅ Add new ones from tags input
                                                            foreach ($data['categories'] as $categoryName) {
                                                                DocumentCategory::create([
                                                                    'name' => $categoryName,
                                                                    'status' => 'active',
                                                                    'company_id' => $companyId,
                                                                    'is_staff_doc' => 0,
                                                                ]);
                                                            }

                                                            Notification::make()
                                                                ->title('Client document categories updated successfully!')
                                                                ->success()
                                                                ->send();
                                                        })
                                                        ->mountUsing(function (Forms\Form $form) {
                                                            $user = Auth::user();
                                                            $companyId = Company::where('user_id', $user->id)->value('id');

                                                            $existingCategories = DocumentCategory::where('company_id', $companyId)
                                                                ->where('is_staff_doc', 0)
                                                                ->pluck('name')
                                                                ->toArray();

                                                            $form->fill([
                                                                'categories' => $existingCategories,
                                                            ]);
                                                        }),
                                                    ]),


                                        Section::make('Staff Competency & Qualification Categories')
                                            ->schema([
                                                InfolistView::make('filament.infolists.staff-comp-qualif-docs')
                                                    ->columnSpanFull(),
                                            ])
                                            ->columnSpanFull()
                                            ->headerActions([
                                                InfolistAction::make('edit_staff_docs')
                                                    ->label('EDIT')
                                                    ->size('sm')
                                                    ->icon('heroicon-s-pencil-square')
                                                    ->form(function (Forms\Form $form) {
                                                        $user = Auth::user();
                                                        $companyId = Company::where('user_id', $user->id)->value('id');

                                                        // ✅ Get current staff competencies and qualifications
                                                        $competencies = DocumentCategory::where('company_id', $companyId)
                                                            ->where('is_staff_doc', 1)
                                                            ->where('is_competencies', 1)
                                                            ->pluck('name')
                                                            ->toArray();

                                                        $qualifications = DocumentCategory::where('company_id', $companyId)
                                                            ->where('is_staff_doc', 1)
                                                            ->where('is_qualifications', 1)
                                                            ->pluck('name')
                                                            ->toArray();

                                                        return $form->schema([
                                                            Forms\Components\TagsInput::make('competencies')
                                                                ->label('Competency Categories')
                                                                ->placeholder('Type a competency name and press Enter...')
                                                                ->helperText('Press Enter to add multiple competencies.')
                                                                ->default($competencies)
                                                                ->columnSpanFull(),

                                                            Forms\Components\TagsInput::make('qualifications')
                                                                ->label('Qualification Categories')
                                                                ->placeholder('Type a qualification name and press Enter...')
                                                                ->helperText('Press Enter to add multiple qualifications.')
                                                                ->default($qualifications)
                                                                ->columnSpanFull(),
                                                        ]);
                                                    })
                                                    ->action(function (array $data) {
                                                        $user = Auth::user();
                                                        $companyId = Company::where('user_id', $user->id)->value('id');

                                                        if (! $companyId) {
                                                            Notification::make()
                                                                ->title('Company not found!')
                                                                ->danger()
                                                                ->send();
                                                            return;
                                                        }

                                                        // ✅ Delete old staff categories (competencies & qualifications only)
                                                        DocumentCategory::where('company_id', $companyId)
                                                            ->where('is_staff_doc', 1)
                                                            ->where(function ($q) {
                                                                $q->where('is_competencies', 1)
                                                                ->orWhere('is_qualifications', 1);
                                                            })
                                                            ->delete();

                                                        // ✅ Add new competencies
                                                        foreach ($data['competencies'] ?? [] as $name) {
                                                            DocumentCategory::create([
                                                                'name' => $name,
                                                                'is_staff_doc' => 1,
                                                                'is_competencies' => 1,
                                                                'is_qualifications' => 0,
                                                                'company_id' => $companyId,
                                                            ]);
                                                        }

                                                        // ✅ Add new qualifications
                                                        foreach ($data['qualifications'] ?? [] as $name) {
                                                            DocumentCategory::create([
                                                                'name' => $name,
                                                                'is_staff_doc' => 1,
                                                                'is_competencies' => 0,
                                                                'is_qualifications' => 1,
                                                                'company_id' => $companyId,
                                                            ]);
                                                        }

                                                        Notification::make()
                                                            ->title('Staff competency and qualification categories updated successfully!')
                                                            ->success()
                                                            ->send();
                                                    })
                                                    ->mountUsing(function (Forms\Form $form) {
                                                        $user = Auth::user();
                                                        $companyId = Company::where('user_id', $user->id)->value('id');

                                                        $competencies = DocumentCategory::where('company_id', $companyId)
                                                            ->where('is_staff_doc', 1)
                                                            ->where('is_competencies', 1)
                                                            ->pluck('name')
                                                            ->toArray();

                                                        $qualifications = DocumentCategory::where('company_id', $companyId)
                                                            ->where('is_staff_doc', 1)
                                                            ->where('is_qualifications', 1)
                                                            ->pluck('name')
                                                            ->toArray();

                                                        $form->fill([
                                                            'competencies' => $competencies,
                                                            'qualifications' => $qualifications,
                                                        ]);
                                                    }),
                                                ]),

                                           Section::make('Report Headings')
                                                            ->schema([
                                                                InfolistView::make('filament.infolists.staff-other-docs-category')
                                                                    ->columnSpanFull(),
                                                            ])
                                                            ->columnSpanFull()
                                                            ->headerActions([
                                                                InfolistAction::make('edit_staff_docs')
                                                                    ->label('EDIT')
                                                                    ->size('sm')
                                                                    ->icon('heroicon-s-pencil-square')
                                                                    ->form(function (Forms\Form $form) {
                                                                        $user = Auth::user();
                                                                        $companyId = Company::where('user_id', $user->id)->value('id');

                                                                        $compliances = DocumentCategory::where('company_id', $companyId)
                                                                            ->where('is_staff_doc', 1)
                                                                            ->where('is_compliance', 1)
                                                                            ->pluck('name')
                                                                            ->toArray();

                                                                        $kpis = DocumentCategory::where('company_id', $companyId)
                                                                            ->where('is_staff_doc', 1)
                                                                            ->where('is_kpi', 1)
                                                                            ->pluck('name')
                                                                            ->toArray();

                                                                        $others = DocumentCategory::where('company_id', $companyId)
                                                                            ->where('is_staff_doc', 1)
                                                                            ->where('is_other', 1)
                                                                            ->pluck('name')
                                                                            ->toArray();

                                                                        return $form->schema([
                                                                            Forms\Components\TagsInput::make('compliances')
                                                                                ->label('Compliance')
                                                                                ->default($compliances)
                                                                                ->placeholder('Add compliance category...')
                                                                                ->columnSpanFull(),

                                                                            Forms\Components\TagsInput::make('kpis')
                                                                                ->label('KPI')
                                                                                ->default($kpis)
                                                                                ->placeholder('Add KPI category...')
                                                                                ->columnSpanFull(),

                                                                            Forms\Components\TagsInput::make('others')
                                                                                ->label('Other')
                                                                                ->default($others)
                                                                                ->placeholder('Add other category...')
                                                                                ->columnSpanFull(),
                                                                        ]);
                                                                    })
                                                                    ->action(function (array $data) {
                                                                        $user = Auth::user();
                                                                        $companyId = Company::where('user_id', $user->id)->value('id');

                                                                        // Remove existing records first (scoped properly)
                                                                        DocumentCategory::where('company_id', $companyId)
                                                                            ->where('is_staff_doc', 1)
                                                                            ->where(function ($query) {
                                                                                $query->where('is_compliance', 1)
                                                                                    ->orWhere('is_kpi', 1)
                                                                                    ->orWhere('is_other', 1);
                                                                            })
                                                                            ->delete();

                                                                        // Recreate: Compliance
                                                                        foreach ($data['compliances'] ?? [] as $name) {
                                                                            DocumentCategory::create([
                                                                                'name' => $name,
                                                                                'is_staff_doc' => 1,
                                                                                'is_compliance' => 1,
                                                                                'company_id' => $companyId,
                                                                            ]);
                                                                        }

                                                                        // Recreate: KPI
                                                                        foreach ($data['kpis'] ?? [] as $name) {
                                                                            DocumentCategory::create([
                                                                                'name' => $name,
                                                                                'is_staff_doc' => 1,
                                                                                'is_kpi' => 1,
                                                                                'company_id' => $companyId,
                                                                            ]);
                                                                        }

                                                                        // Recreate: Other
                                                                        foreach ($data['others'] ?? [] as $name) {
                                                                            DocumentCategory::create([
                                                                                'name' => $name,
                                                                                'is_staff_doc' => 1,
                                                                                'is_other' => 1,
                                                                                'company_id' => $companyId,
                                                                            ]);
                                                                        }

                                                                        Notification::make()
                                                                            ->title('Report heading categories updated successfully!')
                                                                            ->success()
                                                                            ->send();
                                                                    })
                                                                    ->mountUsing(function (Forms\Form $form) {
                                                                        $user = Auth::user();
                                                                        $companyId = Company::where('user_id', $user->id)->value('id');

                                                                        $form->fill([
                                                                            'compliances' => DocumentCategory::where('company_id', $companyId)
                                                                                ->where('is_staff_doc', 1)
                                                                                ->where('is_compliance', 1)
                                                                                ->pluck('name')
                                                                                ->toArray(),
                                                                            'kpis' => DocumentCategory::where('company_id', $companyId)
                                                                                ->where('is_staff_doc', 1)
                                                                                ->where('is_kpi', 1)
                                                                                ->pluck('name')
                                                                                ->toArray(),
                                                                            'others' => DocumentCategory::where('company_id', $companyId)
                                                                                ->where('is_staff_doc', 1)
                                                                                ->where('is_other', 1)
                                                                                ->pluck('name')
                                                                                ->toArray(),
                                                                        ]);
                                                                    }),
                                                                ]),
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
