<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Models\Client;
use App\Models\Company;
use App\Models\PayGroup;
use App\Models\PriceBook;
use Filament\Forms\Components\Actions\Action as NewAction;
use App\Models\ShiftType;
use App\Models\StaffProfile;
use App\Models\Team;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Spatie\Permission\Traits\HasRoles;
use Filament\Forms\Form;
use App\Models\Shift;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Actions\Action;
use App\Models\Language;
use App\Models\DocumentCategory;
use App\Models\Event;
use App\Models\PriceBookDetail;
use App\Models\BillingReport;
use Carbon\Carbon;

use App\Models\TimesheetReport;
use App\Models\Timesheet;

class AdvancedShiftForm extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.advanced-shift-form';
    protected static ?string $title = 'Advanced Shift Form';
    protected static bool $shouldRegisterNavigation = false;


    public ?Shift $shift = null;


       public ?array $data = [
        'add_to_job_board' => null,
        'shift_section' => [
            'additional_shift_types' => null,
            'shift_type_id' => null,
            'allowance_id' => null,
            'invoice_mileage' => null,
        ],
         'client_section' => [
            'client_id' => null,
        ],
        'instruction' => [
            'description' => null,
        ],
        'job_section' => [
            'team_id' => null,
            'language_id' => null,
            'compilance_id' => null,
            'competencies_id' => null,
            'kpi_id' => null,
        ],
        'carer_section' => [
            'user_id' => null,
        ],
    ];

public function mount(): void
{
        $this->form->fill();
}




    public function form(Form $form): Form
    {
        $authUser = Auth::user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');

        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('')
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
                                            Select::make('client_id')
                                                    ->label('Select Clients')
                                                    ->searchable()
                                                    ->placeholder('Type to search clients by name.')
                                                    ->columnSpanFull()
                                                    ->options(
                                                        Client::where('user_id', auth()->id())
                                                            ->where('is_archive', 'Unarchive')
                                                            ->pluck('display_name', 'id')
                                                    )
                                                    ->multiple()
                                                    ->preload()
                                              
                                                    ->live()
                                                ->afterStateUpdated(function ($state, $set) {
                                                    if (!empty($state)) {
                                                        $clients = Client::whereIn('id', $state)->get();

                                                        $details = [];
                                                        foreach ($clients as $client) {
                                                            $details[] = [
                                                                'client_id' => $client->id,
                                                                'client_name' => $client->display_name,
                                                            ];
                                                        }

                                                        $set('client_details', $details);
                                                    } else {
                                                        $set('client_details', []);
                                                    }
                                                }),
                                                    Repeater::make('client_details')
                                                        ->label(fn ($get, $state) => $get('client_name'))
                                                        ->schema([
                                                            Grid::make(10)
                                                                ->schema([
                                                                    TimePicker::make('client_start_time')
                                                                        ->label('')
                                                                        ->default('02:00 AM')
                                                                        ->columnSpan(2),

                                                                    TimePicker::make('client_end_time')
                                                                        ->label('')
                                                                        ->default('03:00 AM')
                                                                        ->columnSpan(2),

                                                                    Placeholder::make('pla')
                                                                        ->label('')
                                                                        ->columnSpan(1),

                                                                    Select::make('price_book_id')
                                                                        ->label('')
                                                                        ->options(
                                                                            PriceBook::where('company_id', $companyId)
                                                                                ->orderByDesc('id')
                                                                                ->pluck('name', 'id')
                                                                        )
                                                                        ->columnSpan(5),

                                                                    Select::make('hours')
                                                                        ->label('')
                                                                        ->placeholder('1:1')
                                                                        ->options([
                                                                            '1:1' => '1:1',
                                                                            '1:2' => '1:2',
                                                                            '1:3' => '1:3',
                                                                            '1:4' => '1:4',
                                                                            '1:5' => '1:5',
                                                                            '1:6' => '1:6',
                                                                            '1:7' => '1:7',
                                                                            '1:8' => '1:8',
                                                                            '1:9' => '1:9',
                                                                            '1:10' => '1:10',
                                                                            '1:11' => '1:11',
                                                                            '1:12' => '1:12',
                                                                            '1:13' => '1:13',
                                                                            '1:14' => '1:14',
                                                                            '1:15' => '1:15',
                                                                            '1:16' => '1:16',
                                                                            '1:17' => '1:17',
                                                                            '1:18' => '1:18',
                                                                            '1:19' => '1:19',
                                                                            '1:20' => '1:20',
                                                                        ])
                                                                        ->columnSpan(2),
                                                                ])
                                                        ])

                                           ->cloneable()
                                                    ->cloneAction(
                                                        fn (\Filament\Forms\Components\Actions\Action $action) =>
                                                            $action->icon('heroicon-m-scissors')
                                                                ->button()
                                                                ->label('Split')
                                                                ->color('info')
                                                    )
                                                ->addable(false)
                                                ->columnSpan(3)
                                                ->itemLabel(fn (array $state): ?string => $state['client_name'] ?? 'Client')
                                                ->visible(fn ($get) => !empty($get('client_id')))

                                        ]),
                                ])
                                ->statePath('client_section')
                                ->extraAttributes(['style' => 'margin-top:70px'])
                                ->collapsible()
                                ->columnSpan(1),

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
                            Grid::make(3)
                                ->schema([
                                    Placeholder::make('break_lab')
                                        ->label('Break time in minutes')
                                        ->columnSpan(1),
                                    TextInput::make('break_time')
                                        ->label('')
                                        ->numeric()
                                        ->columnSpan(2),
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
                            Grid::make(5)
                                ->schema([
                                    Placeholder::make('')
                                        ->label('')
                                        ->columnSpan(4),
                                    Checkbox::make('drop_off_address')
                                        ->label('Drop Off Address')
                                        ->columnSpan(1)
                                        ->reactive(),
                                ]),
                            Grid::make(3)
                                ->schema([
                                    Placeholder::make('drop_address_lab')
                                        ->label('Drop off Address')
                                        ->columnSpan(1),
                                    TextInput::make('drop_address')
                                        ->label('')
                                        ->placeholder('Enter Address')
                                        ->columnSpan(2),
                                ])
                                ->visible(fn ($get) => $get('drop_off_address')),
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
                                ])
                                ->visible(fn ($get) => $get('drop_off_address')),
                            Grid::make(3)
                                ->schema([
                                    Placeholder::make('drop_unit_lab')
                                        ->label('Drop Off Unit/Apartment Number')
                                        ->columnSpan(1),
                                    TextInput::make('drop_unit_apartment_number')
                                        ->label('')
                                        ->prefixIcon('heroicon-s-building-office')
                                        ->placeholder('Enter Unit/Apartment Number')
                                        ->columnSpan(2),
                                ])
                                ->visible(fn ($get) => $get('drop_off_address')),
                        ])
                        ->statePath('time_and_location')
                        ->extraAttributes(['style' => 'margin-top:10px'])
                        ->collapsible()
                        ->columnSpan(1),

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
                                    Select::make('shift_type_id')
                                           ->options(
                                            ShiftType::where('user_id', Auth::id())
                                                ->pluck('name', 'id')
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->label('')
                                        ->columnSpan(2),
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
                                    Placeholder::make('invoice_lab')
                                        ->label('Invoice of mileage')
                                        ->columnSpan(1),
                                    Select::make('invoice_mileage')
                                        ->label('')
                                        ->multiple()
                                        ->preload()
                                        ->searchable()
                                        ->options(
                                            Client::where('user_id', $authUser->id)
                                                ->where('is_archive', 'Unarchive')
                                                ->pluck('display_name', 'id')
                                        )
                                        ->columnSpan(2),
                                    Placeholder::make('mileage_lab')
                                        ->label('Mileage')
                                        ->columnSpan(1),
                                    TextInput::make('mileage')
                                        ->label('')
                                        ->columnSpan(2),
                                    Placeholder::make('additional_cost_lab')
                                        ->label('Additional Cost ($)')
                                        ->columnSpan(1),
                                    TextInput::make('additional_cost')
                                        ->label('')
                                        ->columnSpan(2),
                                    Placeholder::make('ignore_staff_count_lab')
                                        ->label('Ignore Staff Count')
                                        ->columnSpan(1),
                                    Toggle::make('ignore_staff_count')
                                        ->label('')
                                        ->columnSpan(2),
                                    Placeholder::make('confirmation_required_lab')
                                        ->label('Confirmation Required')
                                        ->columnSpan(1),
                                    Toggle::make('confirmation_required')
                                        ->label('')
                                        ->columnSpan(2),
                                ]),
                        ])
                        ->statePath('shift_section')
                        ->collapsible()
                        ->columnSpan(1),
                    ])
                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;'])
                    ->columns(1)
                    ->columnSpan(1),

                Section::make()
                    ->schema([
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
                            Toggle::make('add_to_job_board')
                                ->label('Add to job board')
                                ->reactive(),
                                Section::make()
                                ->schema([
                                     Grid::make(8)
                                ->schema([
                                    Placeholder::make('')
                                        ->label('Suggested Carer')
                                        ->columnSpan(8),
                                    Placeholder::make('suggested_carer')
                                        ->label('')
                                        ->content(function () {
                                            $authUser = Auth::user();
                                            return new HtmlString('
                                                <span
                                                    id="suggested-carer"
                                                    style="text-decoration: none;color:#0D76CA;margin-top:-20px"
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
                                        ->columnSpan(8),
                                    ]),
                            Grid::make(3)
                                ->schema([
                                    Select::make('user_id')
                                        ->label('Select Carer')
                                        ->searchable()
                                        ->placeholder('Choose Carer')
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
                                        ->live()
                                        ->afterStateUpdated(function ($state, $set) {
                                            if (!empty($state)) {
                                                $users = User::whereIn('id', (array) $state)->get();

                                                $details = [];
                                                foreach ($users as $user) {
                                                    $details[] = [
                                                        'user_id' => $user->id,
                                                        'user_name' => $user->name,
                                                    ];
                                                }

                                                $set('user_details', $details);
                                            } else {
                                                $set('user_details', []);
                                            }
                                        })
                                        ->multiple()
                                        ->columnSpan(3),
                                   Repeater::make('user_details')
                                                ->label('')
                                                ->schema([
                                                    Grid::make(10)->schema([

                                                        TimePicker::make('user_start_time')
                                                            ->label('')
                                                            ->default('02:00 AM')
                                                            ->columnSpan(2),

                                                        TimePicker::make('user_end_time')
                                                            ->label('')
                                                            ->default('03:00 AM')
                                                            ->columnSpan(2),

                                                        Placeholder::make('lkadfad')
                                                            ->label('')
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
                                                            ->columnSpan(5),

                                                    ])
                                                ])

                                        ->addable(false)
                                        ->itemLabel(fn (array $state): ?string => $state['user_name'] ?? 'Carer')
                                        ->visible(fn ($get) => !empty($get('user_id')))
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
                                ])
                                    ->statePath('carer_section')
                                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;'])
                                    ->visible(fn ($get) => !$get('add_to_job_board')),

                              Section::make()
                                ->schema([
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

                                Section::make('Job Board Criteria')
                                    ->schema([
                                              Grid::make(3)
                                          ->schema([
                                            Placeholder::make('open_to')
                                                ->label('Teams')
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

                                                 Placeholder::make('language_lab')
                                                ->label('Languages')
                                                ->columnSpan(1),
                                            Select::make('language_id')
                                                ->label('')
                                                ->multiple()
                                                ->preload()
                                                ->searchable()
                                                ->options(function () {
                                                    return Language::pluck('name', 'id')
                                                        ->toArray();
                                                })
                                                ->columnSpan(2),


                                                     Placeholder::make('compilance_lab')
                                                ->label('Compilance')
                                                ->columnSpan(1),
                                            Select::make('compilance_id')
                                                ->label('')
                                                ->multiple()
                                                ->preload()
                                                ->searchable()
                                                 ->options(function () {
                                                        $user = Auth::user();
                                                        $companyId = Company::where('user_id', $user->id)->value('id');

                                                        return DocumentCategory::where('is_staff_doc', 1)
                                                            ->where('company_id', $companyId)
                                                            ->where('is_compliance', 1)
                                                            ->pluck('name', 'id')
                                                            ->toArray();
                                                    })
                                                ->columnSpan(2),

                                                           Placeholder::make('competencies_lab')
                                                ->label('Competencies')
                                                ->columnSpan(1),
                                            Select::make('competencies_id')
                                                ->label('')
                                                ->multiple()
                                                ->preload()
                                                ->searchable()
                                                ->options(function () {
                                                        $user = Auth::user();
                                                        $companyId = Company::where('user_id', $user->id)->value('id');

                                                    return DocumentCategory::where('is_staff_doc', 1)
                                                        ->where('company_id',$companyId)
                                                        ->where('is_competencies', 1)
                                                        ->pluck('name', 'id')
                                                        ->toArray();
                                                })
                                                ->columnSpan(2),



                                                           Placeholder::make('kpi_lab')
                                                ->label("KPI's")
                                                ->columnSpan(1),
                                            Select::make('kpi_id')
                                                ->label('')
                                                ->multiple()
                                                ->preload()
                                                ->searchable()
                                                ->options(function () {
                                                    $user = Auth::user();
                                                        $companyId = Company::where('user_id', $user->id)->value('id');

                                                    return DocumentCategory::where('is_staff_doc', 1)
                                                        ->where('company_id',$companyId)
                                                        ->where('is_kpi', 1)
                                                        ->pluck('name', 'id')
                                                        ->toArray();
                                                })
                                                ->columnSpan(2),


                                                           Placeholder::make('distance_lab')
                                                ->label("Distance from shift location")
                                                ->columnSpan(1),
                                            Select::make('distance_shift')
                                                ->label('')
                                                ->options([
                                                    'Any Distance' => 'Any Distance',
                                                    '10 km' => '10 km',
                                                    '20 km' => '20 km',
                                                    '30 km' => '30 km',
                                                    '40 km' => '40 km',
                                                    '50 km' => '50 km',
                                                    '60 km' => '60 km',
                                                    '70 km' => '70 km',
                                                    '80 km' => '80 km',
                                                    '90 km' => '90 km',
                                                    '100 km' => '100 km',
                                                ])
                                                ->columnSpan(2),
                                ]),
                                    ])
                                ])
                                ->visible(fn ($get) => $get('add_to_job_board'))
                                     ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;'])
                                    ->statePath('job_section')
                                ,


                        ])
                        ->extraAttributes(['style' => 'margin-top:70px'])
                        ->columnSpan(1),



                        Section::make(
                            new HtmlString('
                                <span class="flex items-center gap-2">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 64 64" fill="none">
                                        <path d="M8 14 L14 20 L24 8" stroke="black" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                        <rect x="28" y="10" width="28" height="4" fill="black" rx="2"/>

                                        <path d="M8 34 L14 40 L24 28" stroke="black" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                        <rect x="28" y="30" width="28" height="4" fill="black" rx="2"/>

                                        <circle cx="14" cy="54" r="6" fill="black"/>
                                        <rect x="28" y="50" width="28" height="4" fill="black" rx="2"/>
                                    </svg>
                                    <span>Tasks</span>
                                </span>
                            ')
                        )
                        ->schema([
                            Grid::make(1)
                                ->schema([
                                    Repeater::make('tasks')
                                    ->label('')
                                    ->schema([
                                        Grid::make(10)
                                        ->schema([
                                        Textarea::make('task_description')
                                        ->label('')
                                        ->placeholder('Task Description')
                                        ->columnSpan(8),
                                          Checkbox::make('mandatory')
                                        ->label('Mandotary')
                                        ->columnSpan(2),
                                        ])
                                    ])->columns(2)
                                    ->addActionLabel('Add Task')
                                ]),
                        ])
                        ->statePath('task_section')
                        ->extraAttributes(['style' => 'margin-top:10px;margin-bottom:30px'])
                        ->collapsible(),

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
                        ->collapsible(),
                    ])
                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;'])
                    ->columns(1)
                    ->columnSpan(1),
            ])
            ])->statePath('data');

    }
public function createShift()
{
    //  $shifti = Shift::find(69);
    // dd($shifti);
     $data = $this->form->getState();
     $authUser = Auth::user();
     $shiftCompanyID = Company::where('user_id', $authUser->id)->value('id');
    // dd($data);
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
                'client_id'      => $data['client_section']['client_id'] ?? [],
                'client_details' => $data['client_section']['client_details'] ?? [],
            ],

        'time_and_location' => [
                'start_date'              => data_get($data, 'time_and_location.start_date'),
                'shift_finishes_next_day' => data_get($data, 'time_and_location.shift_finishes_next_day', false),
                'start_time'              => data_get($data, 'time_and_location.start_time'),
                'end_time'                => data_get($data, 'time_and_location.end_time'),
                'break_time'                => data_get($data, 'time_and_location.break_time'),
                'repeat'                  => data_get($data, 'time_and_location.repeat', false),
                'recurrance'              => data_get($data, 'time_and_location.recurrance'),
                'repeat_every_daily'            => data_get($data, 'time_and_location.repeat_every_daily'),
                'repeat_every_weekly'            => data_get($data, 'time_and_location.repeat_every_weekly'),
                'repeat_every_monthly'            => data_get($data, 'time_and_location.repeat_every_monthly'),
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
                'drop_off_address'               => data_get($data, 'time_and_location.drop_off_address', false),
                'drop_address'               => data_get($data, 'time_and_location.drop_address'),
                'drop_unit_apartment_number' => data_get($data, 'time_and_location.drop_unit_apartment_number'),
            ],

        'shift_section' => [
            'shift_type_id'          => data_get($data, 'shift_section.shift_type_id'),
            'additional_shift_types' => data_get($data, 'shift_section.additional_shift_types', []),
            'allowance_id'           => data_get($data, 'shift_section.allowance_id', []),
            'invoice_mileage'     => data_get($data, 'client_section.client_id', []),
            'mileage'          => data_get($data, 'shift_section.mileage'),
            'additional_cost'          => data_get($data, 'shift_section.additional_cost'),
            'ignore_staff_count'          => data_get($data, 'shift_section.ignore_staff_count', false),
            'confirmation_required'          => data_get($data, 'shift_section.confirmation_required', false),
        ],

        'add_to_job_board' => data_get($data, 'add_to_job_board', false),
           'carer_section'     => $carerSection,
        'job_section' => !empty($data['add_to_job_board']) ? [
            'shift_assignment'=> data_get($data, 'job_section.shift_assignment'),
            'team_id'         => data_get($data, 'job_section.team_id' , []),
            'language_id'         => data_get($data, 'job_section.language_id' , []),
            'compilance_id'         => data_get($data, 'job_section.compilance_id' , []),
            'competencies_id'         => data_get($data, 'job_section.competencies_id' , []),
            'kpi_id'         => data_get($data, 'job_section.kpi_id' , []),
            'distance_shift'=> data_get($data, 'job_section.distance_shift'),
        ] : null,
         'status' => !empty($data['add_to_job_board'])
            ? 'Job Board'
            : 'Pending',
          'task_section' => $data['task_section']['tasks'] ?? [],
        'instruction' => [
            'description' => data_get($data, 'instruction.description'),
        ],
        'company_id' => $shiftCompanyID,
        'is_advanced_shift' => true,
        'is_vacant'  => $isVacant, // ✅ set here

    ]);

     if (($newShift->add_to_job_board == 0) && ($newShift->is_vacant == 0)) {

    $shiftDate     = Carbon::parse(data_get($data, 'time_and_location.start_date'));
    $shiftEndDate  = Carbon::parse(data_get($data, 'time_and_location.end_date'));
    $authUser      = Auth::user();
    $companyId     = Company::where('user_id', $authUser->id)->value('id');
    $carerUsers    = data_get($newShift, 'carer_section.user_details', []);

    $allReports    = [];
    $allTimesheets = [];

    if (!empty($data['carer_section']['user_details'])) {

        foreach ($newShift->carer_section['user_details'] as $carer) {

            $userId     = $carer['user_id'];
            $shiftStart = Carbon::parse($carer['user_start_time']);
            $shiftEnd   = Carbon::parse($carer['user_end_time']);

            // ✅ Calculate total hours
            $totalHours = round($shiftEnd->floatDiffInHours($shiftStart), 2);

            // ------------------------------
            // Weekday Segmentation Logic
            // ------------------------------
            $dayOfWeek = $shiftDate->format('l');
            $isPublicHoliday = false;

            $weekday_12a_6a  = 0;
            $weekday_6a_8p   = 0;
            $weekday_8p_10p  = 0;
            $weekday_10p_12a = 0;
            $saturday        = 0;
            $sunday          = 0;
            $public_holidays = 0;

            if ($dayOfWeek === 'Saturday') {
                $saturday = $totalHours;
            } elseif ($dayOfWeek === 'Sunday') {
                $sunday = $totalHours;
            } elseif ($isPublicHoliday) {
                $public_holidays = $totalHours;
            } else {
                $startMinutes = ($shiftStart->hour * 60) + $shiftStart->minute;
                $endMinutes   = ($shiftEnd->hour * 60) + $shiftEnd->minute;

                if ($endMinutes <= $startMinutes) {
                    $endMinutes += 24 * 60;
                }

                $segments = [
                    '12a_6a'  => [0, 360],
                    '6a_8p'   => [360, 1200],
                    '8p_10p'  => [1200, 1320],
                    '10p_12a' => [1320, 1440],
                ];

                $calcOverlap = function ($rangeStart, $rangeEnd) use ($startMinutes, $endMinutes) {
                    $overlap = max(0, min($endMinutes, $rangeEnd) - max($startMinutes, $rangeStart));
                    return round($overlap / 60, 2);
                };

                $weekday_12a_6a  = $calcOverlap(...$segments['12a_6a']);
                $weekday_6a_8p   = $calcOverlap(...$segments['6a_8p']);
                $weekday_8p_10p  = $calcOverlap(...$segments['8p_10p']);
                $weekday_10p_12a = $calcOverlap(...$segments['10p_12a']);
            }

            $standard_hours =
                $weekday_12a_6a +
                $weekday_6a_8p +
                $weekday_8p_10p +
                $weekday_10p_12a +
                $saturday +
                $sunday +
                $public_holidays;

            $total = $standard_hours;

            // ✅ Create Timesheet Report
            $report = TimesheetReport::create([
                'user_id'    => $userId,
                'shift_id'   => $newShift->id,
                'date'       => $shiftDate->toDateString(),
                'clients'    => $data['client_section']['client_details'] ?? [],
                'start_time' => $shiftStart->format('H:i:s'),
                'end_time'   => $shiftEnd->format('H:i:s'),
                'break_time' => $newShift->time_and_location['break_time'] ?? 0,
                'hours'      => $total,
                'distance'   => $newShift->shift_section['mileage'] ?? 0,
                'expense'    => $newShift->shift_section['additional_cost'] ?? 0,
                'allowances' => data_get($data, 'shift_section.allowance_id', []),
                'status'     => 'Pending',
            ]);

            $isApproved     = $newShift->is_approved ?? false;
            $approvedStatus = $isApproved ? 0 : 1; // 1 = pending approval

            // ✅ Create Timesheet
            $timesheet = Timesheet::create([
                'user_id'             => $userId,
                'shift_id'            => $newShift->id,
                'timesheet_report_id' => $report->id,
                'company_id'          => $companyId,
                'approved_status'     => $approvedStatus,
                'break_time'          => $newShift->time_and_location['break_time'] ?? 0,
                'weekday_12a_6a'      => $weekday_12a_6a,
                'weekday_6a_8p'       => $weekday_6a_8p,
                'weekday_8p_10p'      => $weekday_8p_10p,
                'weekday_10p_12a'     => $weekday_10p_12a,
                'saturday'            => $saturday,
                'sunday'              => $sunday,
                'public_holidays'     => $public_holidays,
                'standard_hours'      => $standard_hours,
                'total'               => $total,
                'mileage'             => $newShift->shift_section['mileage'] ?? 0,
                'expense'             => $newShift->shift_section['additional_cost'] ?? 0,
            ]);

            // Collect results for debugging
            $allReports[]    = $report;
            $allTimesheets[] = $timesheet;
        }
    }

    // ✅ Debug multiple results
    // dd([
    //     'TIMESHEET REPORTS' => $allReports,
    //     'TIMESHEETS'        => $allTimesheets,
    // ]);
}
 

    if (($newShift->add_to_job_board == 0) && ($newShift->is_vacant == 0)) {
    $shiftDate = Carbon::parse(data_get($data, 'time_and_location.start_date'));

    foreach ($newShift->client_section['client_details'] as $clientDetail) {
        $clientId    = $clientDetail['client_id'];
        $priceBookId = $clientDetail['price_book_id'];

        $shiftStart  = Carbon::createFromFormat('H:i:s', $clientDetail['client_start_time']);
        $shiftEnd    = Carbon::createFromFormat('H:i:s', $clientDetail['client_end_time']);

        // ✅ Handle overnight shift (e.g., 11PM → 3AM)
        if ($shiftEnd->lessThanOrEqualTo($shiftStart)) {
            $shiftEnd = $shiftEnd->addDay();
        }

        // ✅ Always positive duration
        $hours = $shiftStart->floatDiffInHours($shiftEnd);

        $dayOfWeek = $shiftDate->format('l');
        $dayType = match ($dayOfWeek) {
            'Saturday' => 'Saturday',
            'Sunday'   => 'Sunday',
            default    => 'Weekdays - I',
        };

        // ✅ price lookup (same corrected logic as before)
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
                    // over-midnight
                    $sub->whereColumn('end_time', '<', 'start_time')
                        ->where(function ($wrap) use ($endTime) {
                            $wrap->where('start_time', '<=', $endTime)
                                 ->orWhere('end_time', '>=', $endTime);
                        });
                })
                ->orWhere(function ($sub) {
                    // all-day
                    $sub->whereTime('start_time', '00:00:00')
                        ->whereTime('end_time', '00:00:00');
                });
            })
            ->orderBy('start_time')
            ->first();

        $rate         = $priceDetail?->per_hour ?? 0;
        $per_km_price = $priceDetail?->per_km ?? 0;

        $totalCost = $hours * $rate;

        $hoursXRate    = number_format($hours, 1) . ' x $' . number_format($rate, 2);
        $distanceXRate = 0.0 . ' x $' . number_format($per_km_price, 2);

        BillingReport::create([
            'date'            => $shiftDate->toDateString(),
            'shift_id'        => $newShift->id,
            'staff' => implode(',', data_get($data, 'carer_section.user_id', [])),
            'start_time'      => $shiftStart->format('H:i'),
            'end_time'        => $shiftEnd->format('H:i'),
            'hours_x_rate'    => $hoursXRate,
            'additional_cost' => $newShift->shift_section['additional_cost'] ?? 0.0,
            'distance_x_rate' => $distanceXRate,
            'total_cost'      => $totalCost,
            'running_total'   => null,
            'price_book_id'   => $priceBookId,
            'client_id'       => $clientId,
        ]);
    }
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

    public function submit()
    {
        $data = $this->form->getState();
        // Add your submission logic here
        dd($data); // Debug the form data
    }
}
