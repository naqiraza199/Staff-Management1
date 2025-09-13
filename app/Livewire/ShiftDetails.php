<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Shift;
use App\Models\Client;
use App\Models\ShiftType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Models\Company;
use App\Models\PayGroup;
use App\Models\PriceBook;
use App\Models\StaffProfile;
use App\Models\Team;
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
use Illuminate\Support\HtmlString;
use Spatie\Permission\Traits\HasRoles;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions;


class ShiftDetails extends Component implements Forms\Contracts\HasForms
{
    
    use Forms\Concerns\InteractsWithForms;
    public $shiftId = null;
    public $selectedDate = null;
    public $shift = null;
    public $userName = '';
    public $clientName = '';
    public $shiftTypeName = '';
    public $timeset = '';
    public $enddate = '';
    public $startDateFormatted = '';
    public $endDateFormatted   = '';
    public $display_name   = '';
     public static ?string $title = '';



    public $isEditing = false;

    public $formData = [];

    protected $listeners = ['updateShift'];


    public function updateShift($shiftId, $selectedDate)
    {
        // reset state
         $this->reset(['isEditing', 'formData', 'shift', 'userName', 'clientName']);
        $this->shiftId = $shiftId;
        $this->selectedDate = $selectedDate;
        $this->loadShiftDetails();
    }

public $clientSection = [];
public $carerSection = [];
public $timeAndLocation = [];

public function loadShiftDetails()
{
    if (!$this->shiftId) {
        return;
    }

    $this->shift = Shift::find($this->shiftId);
    if (!$this->shift) {
        return;
    }

    // Decode helper
    $decode = fn($data) => is_string($data)
        ? (json_decode($data, true) ?: [])
        : (is_array($data) ? $data : []);

    // Decode sections
    $this->clientSection = $decode($this->shift->client_section);
    $this->carerSection  = $decode($this->shift->carer_section);
    $shiftSection        = $decode($this->shift->shift_section);
    $this->timeAndLocation = $decode($this->shift->time_and_location);

    // ✅ Normalize clients
    $clients = [];
    if ($this->shift->is_advanced_shift && !empty($this->clientSection['client_details'])) {
        foreach ($this->clientSection['client_details'] as $detail) {
            $clients[] = [
                'client'       => \App\Models\Client::find((int)($detail['client_id'] ?? 0)),
                'priceBook'    => \App\Models\PriceBook::find((int)($detail['price_book_id'] ?? 0)),
                'start'        => $detail['client_start_time'] ?? null,
                'end'          => $detail['client_end_time'] ?? null,
                'hours'        => $detail['hours'] ?? '1:1',
            ];
        }
    } elseif (!$this->shift->is_advanced_shift && !empty($this->clientSection['client_id'])) {
        $clients[] = [
            'client'    => \App\Models\Client::find((int)$this->clientSection['client_id']),
            'priceBook' => \App\Models\PriceBook::find((int)($this->clientSection['price_book_id'] ?? 0)),
            'start'     => $this->timeAndLocation['start_time'] ?? null,
            'end'       => $this->timeAndLocation['end_time'] ?? null,
            'hours'     => '1:1',
        ];
    }
    $this->clients = $clients;

    // ✅ Normalize carers
    $carers = [];
    if ($this->shift->is_advanced_shift && !empty($this->carerSection['user_details'])) {
        foreach ($this->carerSection['user_details'] as $detail) {
            $carers[] = [
                'carer'  => \App\Models\User::find((int)($detail['user_id'] ?? 0)),
                'rate'   => $detail['rate'] ?? null,
                'start'  => $detail['carer_start_time'] ?? null,
                'end'    => $detail['carer_end_time'] ?? null,
                'hours'  => $detail['hours'] ?? '1:1',
            ];
        }
    } elseif (!$this->shift->is_advanced_shift && !empty($this->carerSection['user_id'])) {
        $carers[] = [
            'carer' => \App\Models\User::find((int)$this->carerSection['user_id']),
            'rate'  => null,
            'start' => $this->timeAndLocation['start_time'] ?? null,
            'end'   => $this->timeAndLocation['end_time'] ?? null,
            'hours' => '1:1',
        ];
    }
    $this->carers = $carers;

    // Dates
    $startDate = $this->timeAndLocation['start_date'] ?? null;
    $endDate   = $this->timeAndLocation['end_date'] ?? null;

    $this->startDateFormatted = $startDate
        ? \Carbon\Carbon::parse($startDate)->format('M d, Y')
        : 'Not defined';

    $this->endDateFormatted = $endDate
        ? \Carbon\Carbon::parse($endDate)->format('M d, Y')
        : 'Ongoing';

    // Times
    $startTime = $this->timeAndLocation['start_time'] ?? null;
    $endTime   = $this->timeAndLocation['end_time'] ?? null;

    if ($startTime && $endTime) {
        $this->timeset = \Carbon\Carbon::parse($startTime)->format('h:i a')
            . ' - ' . \Carbon\Carbon::parse($endTime)->format('h:i a');
    } elseif ($startTime) {
        $this->timeset = \Carbon\Carbon::parse($startTime)->format('h:i a') . ' - ?';
    } elseif ($endTime) {
        $this->timeset = '? - ' . \Carbon\Carbon::parse($endTime)->format('h:i a');
    } else {
        $this->timeset = 'Time not defined';
    }

    // Shift type
    $shiftTypeId = $shiftSection['shift_type_id'] ?? null;
    $this->shiftTypeName = $shiftTypeId
        ? \App\Models\ShiftType::find($shiftTypeId)?->name
        : 'Unknown Shift';
}

public function advertiseBySms() { /* ... */ }
public function approveTimesheet()
{
    if (! $this->shift) {
        return;
    }
    // dd($this->shift);

    $this->shift->update([
        'is_approved' => 1,
    ]);

   Notification::make()
        ->title('Timesheet Approved')
        ->success()
        ->send();

    // Refresh shift data
       $this->redirect('/admin/schedular');

}

public function copy() { /* ... */ }
public function cancel() { /* ... */ }
public function addNotes() { /* ... */ }
public function delete() { /* ... */ }



    public function render()
    {
        return view('livewire.shift-details');
    }

    public function startEditing()
    {
        if (!$this->shift) return;

        $this->form->fill([
            'client_section'   => $this->shift->client_section ?? [],
            'shift_section'    => $this->shift->shift_section ?? [],
            'time_and_location'=> $this->shift->time_and_location ?? [],
            'carer_section'    => $this->shift->carer_section ?? [],
            'job_section'      => $this->shift->job_section ?? [],
            'instruction'      => $this->shift->instruction ?? [],
            'add_to_job_board' => $this->shift->add_to_job_board ?? false,
        ]);

        $this->isEditing = true;
    }



    public function form(Forms\Form $form): Forms\Form
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
                ->reactive()
                ->columnSpan(1),


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
                            ->reactive()
                            ->columnSpan(2),
                    ])
                    ->visible(fn (callable $get) => $get('repeat') === true),

               
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
                       ->visible(fn (callable $get) => 
                                $get('repeat') === true && $get('recurrance') === 'Daily'
                            ),


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
                       ->visible(fn (callable $get) => 
                                    $get('repeat') === true && $get('recurrance') === 'Weekly'
                                ),



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
                        ->visible(fn (callable $get) => 
                                $get('repeat') === true && $get('recurrance') === 'Monthly'
                            ),



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
                ->label('Add To Job Board')
                ->reactive(),

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

                ])->statePath('formData');
    }

        public function save()
    {
        $data = $this->form->getState();
        $shift = Shift::find(21);
        dd($shift);
        // dd($data);

        $this->shift->update([
            'client_section'   => $data['client_section'] ?? [],
            'shift_section'    => $data['shift_section'] ?? [],
            'time_and_location'=> $data['time_and_location'] ?? [],
            'carer_section'    => empty($data['add_to_job_board']) ? $data['carer_section'] : null,
            'job_section'      => !empty($data['add_to_job_board']) ? $data['job_section'] : null,
            'instruction'      => $data['instruction'] ?? [],
            'add_to_job_board' => $data['add_to_job_board'] ?? false,
        ]);

        Notification::make()
            ->title('Shift updated successfully!')
            ->success()
            ->send();

    $this->redirect('/admin/schedular');


        $this->isEditing = false;
        $this->loadShiftDetails();
    }
}
