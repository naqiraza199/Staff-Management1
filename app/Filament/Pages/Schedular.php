<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\Company;
use App\Models\PayGroup;
use App\Models\PriceBook;
use App\Models\ShiftType;
use App\Models\StaffProfile;
use App\Models\Team;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Spatie\Permission\Traits\HasRoles;
use Filament\Forms\Form;
use App\Models\Shift;
use Filament\Notifications\Notification;
use Filament\Forms\Get;
use App\Models\Event;
use App\Models\BillingReport;
use Carbon\Carbon;
use App\Models\PriceBookDetail;
use App\Models\TimesheetReport;
use App\Models\Timesheet;

use Carbon\CarbonInterval;


class Schedular extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-calendar';
    protected static string $view = 'filament.pages.schedular';
    protected static ?string $title = 'Schedular';

    public ?array $data = [
        'add_to_job_board' => null,
        'shift_section' => [
            'additional_shift_types' => null,
            'shift_type_id' => null,
            'allowance_id' => null,
        ],
        'instruction' => [
            'description' => null,
        ],
        'job_section' => [
            'team_id' => null,
        ],
    ];

public $users; // List of users for the calendar
public $shifts;
public $clientNames;
public $shiftTypeNames;
public $display_name;

public bool $showTaskModal = false;
public ?int $shiftId = null;
    public ?string $selectedDate = null;

    public function loadShiftDetails()
    {
        $this->dispatch('show-shift-details', [
            'shiftId' => $this->shiftId,
            'selectedDate' => $this->selectedDate,
        ]);
    }

    

public function mount()
{
    $authUser = Auth::user();
    $companyId = Company::where('user_id', $authUser->id)->value('id');

    if (!$companyId) {
        $this->users = [];
    } else {
        $staffUserIds = StaffProfile::where('company_id', $companyId)
            ->where('is_archive', 'Unarchive')
            ->pluck('user_id');

        $usersQuery = User::whereIn('id', $staffUserIds)->role('staff');

        // Fetch users as a key-value array [user_id => name]
        $allUsers = $usersQuery->get()->pluck('name', 'id')->toArray();
        if (!array_key_exists($authUser->id, $allUsers)) {
            $allUsers[$authUser->id] = $authUser->name;
        }

        $this->users = $allUsers;
    }

    // Fetch shifts filtered by company_id
    $this->shifts = Shift::where('company_id', $companyId)
        ->get()
        ->map(function ($shift) {
            $timeAndLocation = is_string($shift->time_and_location)
                ? json_decode($shift->time_and_location, true)
                : ($shift->time_and_location ?? []);

            $clientSection = is_string($shift->client_section)
                ? json_decode($shift->client_section, true)
                : ($shift->client_section ?? []);

            $shiftSection = is_string($shift->shift_section)
                ? json_decode($shift->shift_section, true)
                : ($shift->shift_section ?? []);

            $carerSection = is_string($shift->carer_section)
                ? json_decode($shift->carer_section, true)
                : ($shift->carer_section ?? []);

            // Common fields for all shifts
            $base = [
                'id' => $shift->id,
                'start_date' => $timeAndLocation['start_date'] ?? null,
                'end_date' => $timeAndLocation['end_date'] ?? null,
                'start_time' => $timeAndLocation['start_time'] ?? null,
                'end_time' => $timeAndLocation['end_time'] ?? null,
                'repeat' => $timeAndLocation['repeat'] ?? false,
                'recurrance' => $timeAndLocation['recurrance'] ?? 'None',
                'repeat_every_daily' => $timeAndLocation['repeat_every_daily'] ?? null,
                'repeat_every_weekly' => $timeAndLocation['repeat_every_weekly'] ?? null,
                'repeat_every_monthly' => $timeAndLocation['repeat_every_monthly'] ?? null,
                'occurs_on_monthly' => $timeAndLocation['occurs_on_monthly'] ?? null,
                'occurs_on_weekly' => $timeAndLocation['occurs_on_weekly'] ?? [],
                'shift_type_id' => $shiftSection['shift_type_id'] ?? null,
                'add_to_job_board' => (bool) $shift->add_to_job_board,
                'is_advanced_shift' => (bool) $shift->is_advanced_shift,
                'is_vacant' => (int) $shift->is_vacant,
                'is_cancelled' => (bool) $shift->is_cancelled,
                'is_approved' => (int) $shift->is_approved,
                'status' => $shift->status ?? 'Unknown',
            ];

            // Handle shifts based on is_advanced_shift
            if (!$shift->is_advanced_shift) {
                // Simple shift: single client_id and user_id
                $base['client_id'] = $clientSection['client_id'] ?? null;
                $base['user_id'] = $carerSection['user_id'] ?? null;
                return [$base];
            }

            // Advanced shift: handle multiple clients from client_section
            $clientIds = $clientSection['client_id'] ?? [];
            $clientIds = is_array($clientIds) ? $clientIds : [$clientIds];
            $userIds = $carerSection['user_id'] ?? [];
            $userIds = is_array($userIds) ? $userIds : [$userIds];

            $records = [];
            if (empty($clientIds)) {
                // No clients, create a single record with clientIds
                $records[] = [
                    ...$base,
                    'clientIds' => [],
                    'user_id' => $userIds[0] ?? null,
                ];
            } elseif ($shift->is_vacant) {
                // Vacant advanced shift: one record with all client IDs
                $records[] = [
                    ...$base,
                    'clientIds' => $clientIds,
                    'user_id' => null,
                ];
            } else {
                // Non-vacant advanced shift: one record per user, with all client IDs
                foreach ($userIds as $userId) {
                    $records[] = [
                        ...$base,
                        'clientIds' => $clientIds,
                        'user_id' => $userId,
                    ];
                }
                // If no user IDs, create a single record with all client IDs
                if (empty($userIds)) {
                    $records[] = [
                        ...$base,
                        'clientIds' => $clientIds,
                        'user_id' => null,
                    ];
                }
            }

            return $records;
        })
        ->flatten(1)
        ->values()
        ->toArray();

    $this->clients = Client::where('user_id', $authUser->id)
        ->where('is_archive', 'Unarchive')
        ->get();

    // Map client_id to display_name
    $this->clientNames = $this->clients->pluck('display_name', 'id')->toArray();

    $this->priceBooks = PriceBook::with('priceBookDetails')
        ->where('company_id', $companyId)
        ->orderByDesc('id')
        ->get();

    $this->shiftTypes = ShiftType::where('user_id', $authUser->id)->get();

    // Map shift_type_id to name
    $this->shiftTypeNames = $this->shiftTypes->pluck('name', 'id')->toArray();
}


    public function getUsersProperty()
    {
        return $this->users ?? [];
    }



public function form(Form $form): Form
{

       $authUser = Auth::user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');
    return $form
        ->schema([





            Section::make(
                new HtmlString('
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             fill="currentColor"
                             class="w-5 h-5 text-primary-600">
                            <path d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501
                                    20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1
                                    12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                        </svg>
                        <span>Client</span>
                    </span>
                ')
            )
            ->schema([
                Grid::make(3)
                    ->schema([
                        Placeholder::make('choose_client_lab')
                            ->label('Choose Client')
                            ->columnSpan(1),

                        Select::make('client_id')
                            ->label('')
                            ->options(
                                Client::where('user_id', $authUser->id)->where('is_archive', 'Unarchive')
                                    ->pluck('display_name', 'id')
                            )
                            ->columnSpan(2),
                    ]),

                Grid::make(3)
                    ->schema([
                        Placeholder::make('price_book_lab')
                            ->label('Price Book')
                            ->columnSpan(1),

                        Select::make('price_book_id')
                            ->label('')
                            ->options(
                                PriceBook::with('priceBookDetails')
                                    ->where('company_id', $companyId)
                                    ->orderByDesc('id')
                                    ->pluck('name', 'id')
                            )
                            ->columnSpan(2),
                    ]),

                Grid::make(3)
                    ->schema([
                        Placeholder::make('funds_lab')
                            ->label('Funds')
                            ->columnSpan(1),

                        Placeholder::make('funds')
                            ->label('')
                            ->content(function ($record) {
                                return new HtmlString('
                                    <span style="background-color:#FDF6EC;color:#FFA500;padding: 10px 15px 12px;border-radius: 10px;" class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                        No Funds Available
                                    </span>
                                ');
                            })
                            ->disableLabel(),
                    ]),
            ])
            ->statePath('client_section')
            ->extraAttributes(['style' => 'margin-top:100px'])
            ->collapsible(),

            Section::make(
                new HtmlString('
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             fill="currentColor"
                             class="w-5 h-5 text-primary-600">
                            <path fill-rule="evenodd"
                                  d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365
                                        9.75-9.75S17.385 2.25 12 2.25Zm.75 4.5a.75.75 0 0 0-1.5 0v5.25c0
                                        .414.336.75.75.75h3.75a.75.75 0 0 0 0-1.5H12.75V6.75Z"
                                  clip-rule="evenodd" />
                        </svg>
                        <span>Shift</span>
                    </span>
                ')
            )
            ->schema([
                Grid::make(3)
                    ->schema([
                        Placeholder::make('shift_types_lab')
                            ->label('Shift Types')
                            ->columnSpan(1),

                    //    Select::make('shift_type_id')
                    //         ->label('Shift Type')
                    //         ->options(
                    //             ShiftType::where('user_id', auth()->id())
                    //                 ->get()
                    //                 ->mapWithKeys(fn ($shift) => [
                    //                     $shift->id =>
                    //                         '<span class="flex items-center gap-2">
                    //                             <span class="w-3 h-3 rounded-full"
                    //                                 style="background-color:' . $shift->color . '"></span>
                    //                             ' . e($shift->name) . '
                    //                         </span>'
                    //                 ])
                    //                 ->toArray()
                    //         )
                    //         ->allowHtml() // 👈 so colors render
                    //         ->searchable()
                    //         ->preload()
                    //         ->columnSpan(2),

                                Select::make('shift_type_id')
                                    ->options(ShiftType::pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('')
                                    ->columnSpan(2),


                    ]),

                Grid::make(3)
                    ->schema([
                        Placeholder::make('additional_shift_types_lab')
                            ->label('Additional Shift Types')
                            ->columnSpan(1),

                        Select::make('additional_shift_types')
                            ->label('')
                            ->multiple()
                            ->options(
                                ShiftType::where('user_id', auth()->id())
                                    ->pluck('name', 'id')
                            )
                            ->preload()
                            ->searchable()
                            ->columnSpan(2),
                    ]),

                Grid::make(3)
                    ->schema([
                        Placeholder::make('allowance_lab')
                            ->label('Allowance')
                            ->columnSpan(1),

                        Select::make('allowance_id')
                            ->label('')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->options(
                                \App\Models\Allowance::where('user_id', auth()->id())
                                    ->pluck('name', 'id')
                            )
                            ->columnSpan(2),
                    ]),
            ])
            ->statePath('shift_section')
            ->extraAttributes(['style' => 'margin-top:10px'])
            ->collapsible(),

            Section::make(
                new HtmlString('
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             fill="currentColor"
                             class="w-5 h-5 text-primary-600">
                            <path fill-rule="evenodd"
                                  d="M6.75 2.25a.75.75 0 0 1 .75.75V4.5h9V3a.75.75 0 0 1 1.5 0v1.5h.75A2.25
                                  2.25 0 0 1 21.75 6.75v12A2.25 2.25 0 0 1 19.5 21H4.5A2.25
                                  2.25 0 0 1 2.25 18.75v-12A2.25 2.25 0 0 1 4.5 4.5h.75V3a.75.75
                                  0 0 1 .75-.75ZM3.75 9v9.75c0
                                  .414.336.75.75.75h15a.75.75 0 0 0
                                  .75-.75V9H3.75Z"
                                  clip-rule="evenodd" />
                        </svg>
                        <span>Time & Location</span>
                    </span>
                ')
            )
            ->schema([
                Grid::make(3)
                    ->schema([
                        Placeholder::make('date_lab')
                            ->label('Date')
                            ->columnSpan(1),
                    DatePicker::make('start_date')
                    ->label('')
                    ->columnSpan(2),
                    ]),

                Grid::make(5)
                    ->schema([
                        Placeholder::make('')
                            ->label('')
                            ->columnSpan(3),

                        Checkbox::make('shift_finishes_next_day')
                            ->label('Shift finishes the next day')
                            ->columnSpan(2),
                    ]),

                Grid::make(11)
                    ->schema([
                        Placeholder::make('time')
                            ->label('Time')
                            ->columnSpan(3),

                        TimePicker::make('start_time')
                            ->label('')
                            ->columnSpan(4),

                        TimePicker::make('end_time')
                            ->label('')
                            ->columnSpan(4),
                    ]),

                Grid::make(5)
                    ->schema([
                        Placeholder::make('')
                            ->label('')
                            ->columnSpan(4),

                        Checkbox::make('repeat')
                            ->label('Repeat')
                            ->columnSpan(1)
                            ->extraAttributes([
                                'x-model' => 'repeatChecked',
                            ]),
                    ]),

                Grid::make(3)
                    ->schema([
                        Placeholder::make('recurrance_lab')
                            ->label('Recurrance')
                            ->columnSpan(1),

                        Select::make('recurrance')
                            ->options([
                                'Daily' => 'Daily',
                                'Weekly' => 'Weekly',
                                'Monthly' => 'Monthly',
                            ])
                            ->label('')
                            ->columnSpan(2)
                            ->extraAttributes([
                                'x-model' => 'recurrance',
                            ]),
                    ])
                    ->extraAttributes([
                        'x-show' => 'repeatChecked',
                        'x-cloak' => true,
                    ]),

                Grid::make(10)
                    ->schema([
                        Placeholder::make('repeat_every_lab')
                            ->label('Repeat every')
                            ->columnSpan(3),

                        Select::make('repeat_every_daily')
                            ->label('')
                            ->options([
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                                '6' => '6',
                                '7' => '7',
                                '8' => '8',
                                '9' => '9',
                                '10' => '10',
                                '11' => '11',
                                '12' => '12',
                                '13' => '13',
                                '14' => '14',
                                '15' => '15',
                            ])
                            ->columnSpan(5),

                        Placeholder::make('day_lab')
                            ->label('Day')
                            ->columnSpan(2),
                    ])
                    ->extraAttributes([
                        'x-show' => "recurrance === 'Daily'",
                        'x-cloak' => true,
                    ]),

                Grid::make(10)
                    ->schema([
                        Placeholder::make('repeat_every_lab')
                            ->label('Repeat every')
                            ->columnSpan(3),

                        Select::make('repeat_every_weekly')
                            ->label('')
                            ->options([
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                                '6' => '6',
                                '7' => '7',
                                '8' => '8',
                                '9' => '9',
                                '10' => '10',
                                '11' => '11',
                                '12' => '12',
                            ])
                            ->columnSpan(5),

                        Placeholder::make('week_lab')
                            ->label('Week')
                            ->columnSpan(2),

                        Placeholder::make('w_lab_occurs')
                            ->label('Occurs on')
                            ->columnSpan(2),

                        Checkbox::make('occurs_on_weekly.sunday')
                            ->label('Sun')
                            ->columnSpan(2),

                        Checkbox::make('occurs_on_weekly.monday')
                            ->label('Mon')
                            ->columnSpan(2),

                        Checkbox::make('occurs_on_weekly.tuesday')
                            ->label('Tue')
                            ->columnSpan(2),

                        Checkbox::make('occurs_on_weekly.wednesday')
                            ->label('Wed')
                            ->columnSpan(2),

                        Checkbox::make('occurs_on_weekly.thursday')
                            ->label('Thu')
                            ->columnSpan(2),

                        Checkbox::make('occurs_on_weekly.friday')
                            ->label('Fri')
                            ->columnSpan(2),

                        Checkbox::make('occurs_on_weekly.saturday')
                            ->label('Sat')
                            ->columnSpan(2),

                    ])
                    ->extraAttributes([
                        'x-show' => "recurrance === 'Weekly'",
                        'x-cloak' => true,
                        'style' => 'margin-top:-10px',
                    ]),

                Grid::make(10)
                    ->schema([
                        Placeholder::make('repeat_every_lab')
                            ->label('Repeat every')
                            ->columnSpan(3),

                        Select::make('repeat_every_monthly')
                            ->label('')
                            ->options([
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                            ])
                            ->columnSpan(5),

                        Placeholder::make('month_lab')
                            ->label('Month')
                            ->columnSpan(2),

                        Placeholder::make('occurs_on_lab')
                            ->label('Occurs on')
                            ->columnSpan(3),

                        Placeholder::make('day_on_lab')
                            ->label('Day')
                            ->columnSpan(1),

                        Select::make('occurs_on_monthly')
                            ->label('')
                            ->options([
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                                '6' => '6',
                                '7' => '7',
                                '8' => '8',
                                '9' => '9',
                                '10' => '10',
                                '11' => '11',
                                '12' => '12',
                                '13' => '13',
                                '14' => '14',
                                '15' => '15',
                                '16' => '16',
                                '17' => '17',
                                '18' => '18',
                                '19' => '19',
                                '20' => '20',
                                '21' => '21',
                                '22' => '22',
                                '23' => '23',
                                '24' => '24',
                                '25' => '25',
                                '26' => '26',
                                '27' => '27',
                                '28' => '28',
                                '29' => '29',
                                '30' => '30',
                                '31' => '31',
                            ])
                            ->columnSpan(4),

                        Placeholder::make('month_lab')
                            ->label('Of the month')
                            ->columnSpan(2),
                    ])
                    ->extraAttributes([
                        'x-show' => "recurrance === 'Monthly'",
                        'x-cloak' => true,
                        'style' => 'margin-top:-40px',
                    ]),

                Grid::make(3)
                    ->schema([
                        Placeholder::make('end_date_lab')
                            ->label('End Date')
                            ->columnSpan(1),

                        DatePicker::make('end_date')
                            ->label('')
                            ->columnSpan(2),
                    ])
                    ->extraAttributes([
                        'x-show' => 'repeatChecked',
                        'x-cloak' => true,
                    ]),

                Grid::make(3)
                    ->schema([
                        Placeholder::make('address_lab')
                            ->label('Address')
                            ->columnSpan(1),

                        TextInput::make('address')
                            ->label('')
                            ->placeholder('Enter Address')
                            ->columnSpan(2),
                    ]),

                Grid::make(5)
                    ->schema([
                        Placeholder::make('')
                            ->label('')
                            ->columnSpan(3),

                        Placeholder::make('invalid_address')
                            ->label('')
                            ->content(function ($record) {
                                return new HtmlString('
                                    <span style="color:#09090B">
                                        Invalid address, <a style="color:blue" href="">read more</a>
                                    </span>
                                ');
                            })
                            ->disableLabel()
                            ->columnSpan(2),
                    ]),

                Grid::make(3)
                    ->schema([
                        Placeholder::make('unit_lab')
                            ->label('Unit/Apartment Number')
                            ->columnSpan(1),

                        TextInput::make('unit_apartment_number')
                            ->label('')
                            ->prefixIcon('heroicon-s-building-office')
                            ->placeholder('Enter Unit/Apartment Number')
                            ->columnSpan(2),
                    ]),
            ])
            ->statePath('time_and_location')
            ->extraAttributes(['style' => 'margin-top:10px'])
            ->collapsible(),

            Toggle::make('add_to_job_board')
                ->label('Add To Job Board'),

            Section::make(
                new HtmlString('
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 fill="currentColor"
                                 class="w-5 h-5 text-primary-600">
                                <path d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501
                                        20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1
                                        12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                            </svg>
                            <span>Carer</span>
                        </span>
                    </div>
                ')
            )
            ->schema([
                Grid::make(3)
                    ->schema([
                        Placeholder::make('carers_lab')
                            ->label('Choose Carer')
                            ->columnSpan(1),

                        Select::make('user_id')
                            ->label('')
                            ->options(function () {
                                $authUser = Auth::user();

                                $companyId = Company::where('user_id', $authUser->id)->value('id');

                                if (!$companyId) {
                                    return [$authUser->id => $authUser->name];
                                }

                                $staffUserIds = StaffProfile::where('company_id', $companyId)
                                    ->where('is_archive', 'Unarchive')
                                    ->pluck('user_id')
                                    ->toArray();

                                if (!in_array($authUser->id, $staffUserIds)) {
                                    $staffUserIds[] = $authUser->id;
                                }

                                return User::whereIn('id', $staffUserIds)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->columnSpan(2)
                            ->id('carer-select'),
                    ]),

                Grid::make(8)
                    ->schema([
                        Placeholder::make('')
                            ->label('Suggested Carer')
                            ->columnSpan(5),

                        Placeholder::make('suggested_carer')
                            ->label('')
                            ->content(function () {
                                $authUser = Auth::user();
                                return new HtmlString('
                                    <span
                                        id="suggested-carer"
                                        style="text-decoration: none;color:#0D76CA"
                                    >
                                        ' . $authUser->name . '... (28/35hrs)
                                    </span>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            const span = document.getElementById("suggested-carer");
                                            const select = document.getElementById("carer-select");
                                            if(span && select) {
                                                span.addEventListener("click", function() {
                                                    select.value = "' . $authUser->id . '";
                                                    select.dispatchEvent(new Event("change"));
                                                });
                                            }
                                        });
                                    </script>
                                ');
                            })
                            ->disableLabel()
                            ->columnSpan(3),
                    ]),

                Grid::make(8)
                    ->schema([
                        Placeholder::make('')
                            ->label('')
                            ->columnSpan(6),

                        Checkbox::make('notify')
                            ->label('Notify carer')
                            ->columnSpan(2),
                    ]),

                Grid::make(3)
                    ->schema([
                        Placeholder::make('choose_pay_group')
                            ->label('Choose pay group')
                            ->columnSpan(1),

                        Select::make('pay_group_id')
                            ->label('')
                            ->options(function () {
                                $auth = auth()->id();

                                return PayGroup::where('user_id', $auth)
                                    ->where('is_archive', 0)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->columnSpan(2),
                    ]),
            ])
            ->statePath('carer_section')
            ->extraAttributes(['style' => 'margin-top:10px'])
            ->visible(fn (Get $get) => !$get('add_to_job_board')),

            Section::make(
                new HtmlString('
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 24 24"
                                 fill="currentColor"
                                 class="w-5 h-5 text-primary-600">
                                <path d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501
                                        20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1
                                        12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                            </svg>
                            <span>Carer</span>
                        </span>
                    </div>
                ')
            )
            ->schema([
                Grid::make(3)
                    ->schema([
                        Placeholder::make('open_to')
                            ->label('Open To')
                            ->columnSpan(1),

                        Select::make('team_id')
                            ->label('')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->options(function () {
                                $authUser = Auth::user();

                                return Team::where('user_id', $authUser->id)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->columnSpan(2),
                    ]),

                Grid::make(10)
                    ->schema([
                        Placeholder::make('')
                            ->label('')
                            ->columnSpan(7),

                        Placeholder::make('require_carer')
                            ->label('')
                            ->content(function () {
                                return new HtmlString('
                                    <a href=""
                                        style="text-decoration: none;color:#0D76CA"
                                    >
                                        Detail requirements
                                    </a>
                                ');
                            })
                            ->disableLabel()
                            ->columnSpan(3),
                    ]),

                Grid::make(3)
                    ->schema([
                        Placeholder::make('shift_assignment_lab')
                            ->label('Shift Assignment')
                            ->columnSpan(1),

                        Select::make('shift_assignment')
                            ->label('')
                            ->options([
                                'Approve automatically' => 'Approve automatically',
                                'Require approval' => 'Require approval',
                            ])
                            ->columnSpan(2),
                    ]),
            ])
            ->statePath('job_section')
            ->extraAttributes(['style' => 'margin-top:10px'])
            ->visible(fn (Get $get) => $get('add_to_job_board')),

            Section::make(
                new HtmlString('
                    <span class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="1.5"
                             stroke="currentColor"
                             class="w-5 h-5 text-primary-600">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M19.5 14.25v3.75a2.25 2.25 0 01-2.25 2.25h-11.25a2.25
                                     2.25 0 01-2.25-2.25V6.75A2.25 2.25 0 014.5 4.5h7.5l6 6z" />
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  d="M14.25 4.5v6h6" />
                        </svg>
                        <span>Instruction</span>
                    </span>
                ')
            )
            ->schema([
                Grid::make(1)
                    ->schema([
                        RichEditor::make('description')
                            ->label('')
                            ->columnSpan(1),
                    ]),
            ])
            ->statePath('instruction')
            ->extraAttributes(['style' => 'margin-top:10px;margin-bottom:30px'])
            ->collapsible(),

                ])->statePath('data');

    }

public function createShift()
{
     $data = $this->form->getState();
     $authUser = Auth::user();
     $shiftCompanyID = Company::where('user_id', $authUser->id)->value('id');
  


$carerSection = empty($data['add_to_job_board']) ? [
    'user_id'      => data_get($data, 'carer_section.user_id'),
    'pay_group_id' => data_get($data, 'carer_section.pay_group_id'),
    'user_details' => data_get($data, 'carer_section.user_details', []),
    'notify'       => data_get($data, 'carer_section.notify', false),
] : null;

// Default
$isVacant = 0;

// Check conditions for vacant
if (
    empty($data['add_to_job_board']) && (
        ($carerSection['user_id'] === null && $carerSection['pay_group_id'] === null) ||
        ($carerSection['user_id'] === [] && $carerSection['user_details'] === [] && $carerSection['notify'] === false)
    )
) {
    $isVacant = 1;
}

$newShift = Shift::create([
    'client_section' => [
        'client_id'     => data_get($data, 'client_section.client_id'),
        'price_book_id' => data_get($data, 'client_section.price_book_id'),
        'funds'         => data_get($data, 'client_section.funds'),
    ],
    'shift_section' => [
        'shift_type_id'          => data_get($data, 'shift_section.shift_type_id'),
        'additional_shift_types' => data_get($data, 'shift_section.additional_shift_types', []),
        'allowance_id'           => data_get($data, 'shift_section.allowance_id', []),
    ],
    'time_and_location' => [
        'start_date'              => data_get($data, 'time_and_location.start_date'),
        'shift_finishes_next_day' => data_get($data, 'time_and_location.shift_finishes_next_day', false),
        'start_time'              => data_get($data, 'time_and_location.start_time'),
        'end_time'                => data_get($data, 'time_and_location.end_time'),
        'repeat'                  => data_get($data, 'time_and_location.repeat', false),
        'recurrance'              => data_get($data, 'time_and_location.recurrance'),
        'repeat_every_daily'      => data_get($data, 'time_and_location.repeat_every_daily'),
        'repeat_every_weekly'     => data_get($data, 'time_and_location.repeat_every_weekly'),
        'repeat_every_monthly'    => data_get($data, 'time_and_location.repeat_every_monthly'),
        'occurs_on_monthly'       => data_get($data, 'time_and_location.occurs_on_monthly'),
        'occurs_on_weekly' => [
            'sunday'    => data_get($data, 'time_and_location.occurs_on_weekly.sunday', false),
            'monday'    => data_get($data, 'time_and_location.occurs_on_weekly.monday', false),
            'tuesday'   => data_get($data, 'time_and_location.occurs_on_weekly.tuesday', false),
            'wednesday' => data_get($data, 'time_and_location.occurs_on_weekly.wednesday', false),
            'thursday'  => data_get($data, 'time_and_location.occurs_on_weekly.thursday', false),
            'friday'    => data_get($data, 'time_and_location.occurs_on_weekly.friday', false),
            'saturday'  => data_get($data, 'time_and_location.occurs_on_weekly.saturday', false),
        ],
        'end_date'              => data_get($data, 'time_and_location.end_date'),
        'address'               => data_get($data, 'time_and_location.address'),
        'unit_apartment_number' => data_get($data, 'time_and_location.unit_apartment_number'),
    ],
    'add_to_job_board' => data_get($data, 'add_to_job_board', false),
    'carer_section'    => $carerSection,
    'job_section'      => !empty($data['add_to_job_board']) ? [
        'team_id'          => data_get($data, 'job_section.team_id' , []),
        'shift_assignment' => data_get($data, 'job_section.shift_assignment'),
    ] : null,
    'status' => !empty($data['add_to_job_board'])
    ? 'Job Board'
    : 'Pending',
    'instruction' => [
        'description' => data_get($data, 'instruction.description'),
    ],
    'company_id' => $shiftCompanyID,
    'is_vacant'  => $isVacant, // ✅ set here
]);


                  if (($data['add_to_job_board'] == 0) && ($isVacant == 0)) {

    $shiftDate   = Carbon::parse(data_get($data, 'time_and_location.start_date'));
    $shiftStart  = Carbon::parse(data_get($data, 'time_and_location.start_time'));
    $shiftEnd    = Carbon::parse(data_get($data, 'time_and_location.end_time'));
    $priceBookId = data_get($data, 'client_section.price_book_id');

    $hours = $shiftStart->floatDiffInHours($shiftEnd);

    $dayOfWeek = $shiftDate->format('l');
    $dayType = match ($dayOfWeek) {
        'Saturday' => 'Saturday',
        'Sunday'   => 'Sunday',
        default    => 'Weekdays - I',
    };

    // ✅ Handle normal ranges + over-midnight + all-day
    $priceDetail = PriceBookDetail::where('price_book_id', $priceBookId)
        ->where('day_of_week', $dayType)
        ->where(function ($q) use ($shiftEnd) {
            $endTime = $shiftEnd->format('H:i:s');

            $q->where(function ($sub) use ($endTime) {
                // normal ranges
                $sub->whereRaw('? BETWEEN start_time AND end_time', [$endTime])
                    ->whereColumn('end_time', '>', 'start_time');
            })
            ->orWhere(function ($sub) use ($endTime) {
                // over-midnight ranges like 20:00 → 00:00
                $sub->whereColumn('end_time', '<', 'start_time')
                    ->where(function ($wrap) use ($endTime) {
                        $wrap->where('start_time', '<=', $endTime)
                             ->orWhere('end_time', '>=', $endTime);
                    });
            })
            ->orWhere(function ($sub) {
                // all-day case
                $sub->whereTime('start_time', '00:00:00')
                    ->whereTime('end_time', '00:00:00');
            });
        })
        ->orderBy('start_time')
        ->first();

    $rate = $priceDetail?->per_hour ?? 0;
    $per_km_price = $priceDetail?->per_km ?? 0;

    $totalCost = $hours * $rate;

    $hoursXRate = number_format($hours, 1) . ' x $' . number_format($rate, 2);
    $distanceXRate = 0.0 . ' x $' . number_format($per_km_price, 2);

    $billingRecordForClient = BillingReport::create([
        'date'            => $shiftDate->toDateString(),
        'shift_id'        => $newShift->id,
        'staff'           => data_get($data, 'carer_section.user_id'),
        'start_time'      => $shiftStart->format('H:i'),
        'end_time'        => $shiftEnd->format('H:i'),
        'hours_x_rate'    => $hoursXRate,
        'additional_cost' => 0.0,
        'distance_x_rate' => $distanceXRate,
        'total_cost'      => $totalCost,
        'running_total'   => null,
        'price_book_id'   => $priceBookId,
        'client_id'       => data_get($data, 'client_section.client_id'),
    ]);


        $TimesheetReportForStaff = TimesheetReport::create([
            'user_id'    => data_get($data, 'carer_section.user_id'),
            'shift_id'   => $newShift->id,
            'date'       => $shiftDate->toDateString(),
            'clients'    => json_encode($newShift->client_section), // ensure stored as JSON
            'start_time' => $shiftStart->format('H:i:s'),
            'end_time'   => $shiftEnd->format('H:i:s'),
            'break_time' => 0,
            'hours'      => $hours,
            'distance'   => null,
            'expense'    => null,
            'allowances' => json_encode(data_get($data, 'shift_section.allowance_id', [])),
            'clockin'    => null,
            'clockout'   => null,
        ]);



      // -------------------------------------------------------------
        // 🧾 Create Timesheet (Detailed Logic)
        // -------------------------------------------------------------
        $authUser   = Auth::user();
        $companyId  = Company::where('user_id', $authUser->id)->value('id');
        $userId     = data_get($data, 'carer_section.user_id');
        $shiftStart = Carbon::parse(data_get($data, 'time_and_location.start_time'));
        $shiftEnd   = Carbon::parse(data_get($data, 'time_and_location.end_time'));
        $shiftDate  = Carbon::parse(data_get($data, 'time_and_location.start_date'));
        $totalHours = $hours;

        // Determine approval status
        $isApproved = $newShift->is_approved ?? false;
        $approvedStatus = $isApproved ? 0 : 1; // 1 = pending approval

        // -------------------------------------------------------------
        // 🕒 Determine Hours by Time Ranges (Weekdays / Sat / Sun)
        // -------------------------------------------------------------
        $weekday_12a_6a = 0;
        $weekday_6a_8p  = 0;
        $weekday_8p_10p = 0;
        $weekday_10p_12a = 0;
        $saturday = 0;
        $sunday = 0;
        $public_holidays = 0;

        // ✅ Check if date is Saturday / Sunday / Public Holiday
        $dayOfWeek = $shiftDate->format('l');
        $isPublicHoliday = false;

        // Optional: if you have a public holiday table
        // $isPublicHoliday = PublicHoliday::whereDate('date', $shiftDate)->exists();

        if ($dayOfWeek === 'Saturday') {
            $saturday = $totalHours;
        } elseif ($dayOfWeek === 'Sunday') {
            $sunday = $totalHours;
        } elseif ($isPublicHoliday) {
            $public_holidays = $totalHours;
        } else {
            // -------------------------------------------------------------
            // 🕒 Improved Weekday Time Range Logic (Accurate to the minute)
            // -------------------------------------------------------------
            // Convert both start and end into absolute minutes from 00:00
            $startMinutes = ($shiftStart->hour * 60) + $shiftStart->minute;
            $endMinutes   = ($shiftEnd->hour * 60) + $shiftEnd->minute;

            // Handle overnight (e.g., 22:00 → 06:00 next day)
            if ($endMinutes <= $startMinutes) {
                $endMinutes += 24 * 60;
            }

            // Define weekday segments in minutes from midnight
            $segments = [
                '12a_6a'  => [0, 360],     // 00:00 – 06:00
                '6a_8p'   => [360, 1200],  // 06:00 – 20:00
                '8p_10p'  => [1200, 1320], // 20:00 – 22:00
                '10p_12a' => [1320, 1440], // 22:00 – 24:00
            ];

            // Helper closure to calculate overlap in hours
            $calcOverlap = function ($rangeStart, $rangeEnd) use ($startMinutes, $endMinutes) {
                $overlap = max(0, min($endMinutes, $rangeEnd) - max($startMinutes, $rangeStart));
                return round($overlap / 60, 2); // in hours, rounded
            };

            // Apply the overlap logic for each segment
            $weekday_12a_6a  = $calcOverlap(...$segments['12a_6a']);
            $weekday_6a_8p   = $calcOverlap(...$segments['6a_8p']);
            $weekday_8p_10p  = $calcOverlap(...$segments['8p_10p']);
            $weekday_10p_12a = $calcOverlap(...$segments['10p_12a']);
        }

        // -------------------------------------------------------------
        // ✅ Calculate Standard Hours & Total as the Sum of All Hour Columns
        // -------------------------------------------------------------
        $standard_hours = 
            $weekday_12a_6a +
            $weekday_6a_8p +
            $weekday_8p_10p +
            $weekday_10p_12a +
            $saturday +
            $sunday +
            $public_holidays;

        $total = $standard_hours;

        // -------------------------------------------------------------
        // 💾 Create Timesheet Record
        // -------------------------------------------------------------
        $timesheet = Timesheet::create([
            'user_id'             => $userId,
            'company_id'          => $companyId,
            'shift_id'            => $newShift->id,
            'timesheet_report_id' => $TimesheetReportForStaff->id,
            'approved_status'     => $approvedStatus,
            'weekday_12a_6a'      => $weekday_12a_6a,
            'weekday_6a_8p'       => $weekday_6a_8p,
            'weekday_8p_10p'      => $weekday_8p_10p,
            'weekday_10p_12a'     => $weekday_10p_12a,
            'saturday'            => $saturday,
            'sunday'              => $sunday,
            'public_holidays'     => $public_holidays,
            'break_time'          => 0,
            'standard_hours'      => $standard_hours,
            'total'               => $total,
            'mileage'             => 0,
            'expense'             => 0,
            'sleepover'           => 0,
        ]);



    //    dd([
    //         'TIMESHEET REPORT' => $TimesheetReportForStaff,
    //         'TIMESHEET' => $timesheet,
    //     ]);


}



    Event::create([
        'shift_id' => $newShift->id,
        'title'    => $authUser->name . ' Created Shift',
        'from'     => 'Create',
        'body'     => 'Shift created',
    ]);

    if (!empty($data['add_to_job_board'])) {
        Event::create([
            'shift_id' => $newShift->id,
            'title'    => 'Job Listed',
            'from'     => 'Job',
            'body'     => 'Job listed by ' . $authUser->name,
        ]);
    }


    Notification::make()
        ->title('New shift created successfully')
        ->success()
        ->send();

    $this->redirect('/admin/schedular');
}



}
