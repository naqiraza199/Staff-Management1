<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
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
use App\Models\ShiftType;
use App\Models\StaffProfile;
use App\Models\Team;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Form;
use App\Models\Shift;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextArea;
use App\Models\Language;
use App\Models\DocumentCategory;
use App\Models\Event;

class EditAdvancedShiftForm extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.edit-advanced-shift-form';
    protected static ?string $title = 'Edit Advanced Shift Form';

    public ?Shift $shift = null;
    public ?array $data = [];

    public function mount(): void
    {
        $shiftId = request()->query('shiftId');

        if (!$shiftId) {
            abort(404, 'Shift ID is required.');
        }

        $this->shift = Shift::findOrFail($shiftId);

        // Safely decode JSON fields
        $clientSection = $this->safeDecode($this->shift->client_section);
        $shiftSection = $this->safeDecode($this->shift->shift_section);
        $timeAndLocation = $this->safeDecode($this->shift->time_and_location);
        $carerSection = $this->safeDecode($this->shift->carer_section);
        $jobSection = $this->safeDecode($this->shift->job_section);
        $instruction = $this->safeDecode($this->shift->instruction);

        // Normalize multi-select fields to arrays
        $clientIds = $this->ensureArray(data_get($clientSection, 'client_id'));
        $userIds = $this->ensureArray(data_get($carerSection, 'user_id'));
        $additionalShiftTypes = $this->ensureArray(data_get($shiftSection, 'additional_shift_types'));
        $allowanceIds = $this->ensureArray(data_get($shiftSection, 'allowance_id'));
        $invoiceMileage = $this->ensureArray(data_get($shiftSection, 'invoice_mileage'));
        $teamIds = $this->ensureArray(data_get($jobSection, 'team_id'));
        $languageIds = $this->ensureArray(data_get($jobSection, 'language_id'));
        $compilanceIds = $this->ensureArray(data_get($jobSection, 'compilance_id'));
        $competenciesIds = $this->ensureArray(data_get($jobSection, 'competencies_id'));
        $kpiIds = $this->ensureArray(data_get($jobSection, 'kpi_id'));
        $occursOnWeekly = $this->ensureArray(data_get($timeAndLocation, 'occurs_on_weekly'));

        // Initialize client_details and user_details based on IDs
        $clientDetails = [];

            if (!empty($clientIds)) {
                $clients = Client::whereIn('id', $clientIds)->get();
                $existingDetails = $this->ensureArray(data_get($clientSection, 'client_details'));

                foreach ($clients as $client) {
                    // find ALL existing rows for this client_id
                    $matches = collect($existingDetails)->where('client_id', $client->id);

                    if ($matches->isNotEmpty()) {
                        // keep each row separately (preserve duplicates)
                        foreach ($matches as $existingDetail) {
                            $clientDetails[] = [
                                'client_id'         => $client->id,
                                'client_name'       => $client->display_name,
                                'client_start_time' => $existingDetail['client_start_time'] ?? '02:00 AM',
                                'client_end_time'   => $existingDetail['client_end_time'] ?? '03:00 AM',
                                'price_book_id'     => $existingDetail['price_book_id'] ?? null,
                                'hours'             => $existingDetail['hours'] ?? null,
                            ];
                        }
                    } else {
                        // no existing rows → add one default
                        $clientDetails[] = [
                            'client_id'         => $client->id,
                            'client_name'       => $client->display_name,
                            'client_start_time' => '02:00 AM',
                            'client_end_time'   => '03:00 AM',
                            'price_book_id'     => null,
                            'hours'             => null,
                        ];
                    }
                }
            }


        $userDetails = [];
        if (!empty($userIds)) {
            $users = User::whereIn('id', $userIds)->get();
            $existingDetails = $this->ensureArray(data_get($carerSection, 'user_details'));
            foreach ($users as $user) {
                $existingDetail = collect($existingDetails)->firstWhere('user_id', $user->id) ?? [];
                $userDetails[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_start_time' => $existingDetail['user_start_time'] ?? '02:00 AM',
                    'user_end_time' => $existingDetail['user_end_time'] ?? '03:00 AM',
                    'pay_group_id' => $existingDetail['pay_group_id'] ?? null,
                ];
            }
        }


            $taskSection = $this->safeDecode($this->shift->task_section);

            // Normalize tasks into array for Repeater
            $tasks = [];
            if (isset($taskSection[0])) {
                // Already array of tasks
                $tasks = $taskSection;
            } elseif (isset($taskSection['task_description'])) {
                // Old single-task structure
                $tasks = [[
                    'task_description' => $taskSection['task_description'] ?? null,
                    'mandatory'        => $taskSection['mandatory'] ?? false,
                ]];
            }


        // Construct flat data array
        $this->data = [
            'add_to_job_board' => $this->shift->add_to_job_board ?? false,
            'client_id' => $clientIds,
            'client_details' => $clientDetails, 
            'shift_type_id' => data_get($shiftSection, 'shift_type_id'),
            'additional_shift_types' => $additionalShiftTypes,
            'allowance_id' => $allowanceIds,
            'invoice_mileage' => $invoiceMileage,
            'mileage' => data_get($shiftSection, 'mileage'),
            'additional_cost' => data_get($shiftSection, 'additional_cost'),
            'ignore_staff_count' => data_get($shiftSection, 'ignore_staff_count', false),
            'confirmation_required' => data_get($shiftSection, 'confirmation_required', false),
            'start_date' => data_get($timeAndLocation, 'start_date'),
            'shift_finishes_next_day' => data_get($timeAndLocation, 'shift_finishes_next_day', false),
            'start_time' => data_get($timeAndLocation, 'start_time'),
            'end_time' => data_get($timeAndLocation, 'end_time'),
            'break_time' => data_get($timeAndLocation, 'break_time'),
            'repeat' => data_get($timeAndLocation, 'repeat', false),
            'recurrance' => data_get($timeAndLocation, 'recurrance'),
            'repeat_every_daily' => data_get($timeAndLocation, 'repeat_every_daily'),
            'repeat_every_weekly' => data_get($timeAndLocation, 'repeat_every_weekly'),
            'repeat_every_monthly' => data_get($timeAndLocation, 'repeat_every_monthly'),
            'occurs_on_monthly' => data_get($timeAndLocation, 'occurs_on_monthly'),
            'occurs_on_weekly' => $occursOnWeekly,
            'end_date' => data_get($timeAndLocation, 'end_date'),
            'address' => data_get($timeAndLocation, 'address'),
            'unit_apartment_number' => data_get($timeAndLocation, 'unit_apartment_number'),
            'drop_off_address' => data_get($timeAndLocation, 'drop_off_address', false),
            'drop_address' => data_get($timeAndLocation, 'drop_address'),
            'drop_unit_apartment_number' => data_get($timeAndLocation, 'drop_unit_apartment_number'),
            'user_id' => $userIds,
            'notify' => data_get($carerSection, 'notify', false),
            'user_details' => $userDetails,
            'shift_assignment' => data_get($jobSection, 'shift_assignment'),
            'team_id' => $teamIds,
            'language_id' => $languageIds,
            'compilance_id' => $compilanceIds,
            'competencies_id' => $competenciesIds,
            'kpi_id' => $kpiIds,
            'distance_shift' => data_get($jobSection, 'distance_shift'),
            'tasks' => $tasks,
            'description' => data_get($instruction, 'description'),
        ];

        \Log::info('EditAdvancedShiftForm: Form Fill Data', ['shiftId' => $shiftId, 'data' => $this->data]);

        $this->form->fill($this->data);
    }

    protected function safeDecode($value): array
    {
        try {
            return is_string($value) ? json_decode($value, true, 512, JSON_THROW_ON_ERROR) : ($value ?? []);
        } catch (\Exception $e) {
            \Log::error('JSON Decode Error', ['value' => $value, 'error' => $e->getMessage()]);
            return [];
        }
    }

    protected function ensureArray($value): array
    {
        if (is_array($value)) {
            return array_filter($value, fn($item) => !is_null($item));
        }
        return is_null($value) ? [] : [$value];
    }

    public function form(Form $form): Form
    {
        $authUser = Auth::user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');

        return $form
            ->schema([
                 Grid::make(2)
                    ->schema([
                Section::make()
                    ->schema([
   Section::make('Client')
                    ->schema([
                        Select::make('client_id')
                            ->label('Select Clients')
                            ->searchable()
                            ->placeholder('Type to search clients by name.')
                            ->options(
                                Client::where('user_id', auth()->id())
                                    ->where('is_archive', 'Unarchive')
                                    ->pluck('display_name', 'id')
                            )
                            ->multiple()
                            ->preload()
                            ->default($this->data['client_id'] ?? [])
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $details = [];
                                if (!empty($state)) {
                                    $clients = Client::whereIn('id', $state)->get();
                                    foreach ($clients as $client) {
                                        $existingDetail = collect($this->data['client_details'])->firstWhere('client_id', $client->id) ?? [];
                                        $details[] = [
                                            'client_id' => $client->id,
                                            'client_name' => $client->display_name,
                                            'client_start_time' => $existingDetail['client_start_time'] ?? '02:00 AM',
                                            'client_end_time' => $existingDetail['client_end_time'] ?? '03:00 AM',
                                            'price_book_id' => $existingDetail['price_book_id'] ?? null,
                                            'hours' => $existingDetail['hours'] ?? null,
                                        ];
                                    }
                                }
                                $set('client_details', $details);
                            }),
                        Repeater::make('client_details')
                            ->label(fn (array $state): ?string => $state['client_name'] ?? 'Client')
                            ->schema([
                                TextInput::make('client_name')
                                    ->label('Client Name')
                                    ->disabled()
                                    ->default(fn ($get) => $get('client_name')),
                                TimePicker::make('client_start_time')
                                    ->label('Start Time')
                                    ->default(fn ($get) => $get('client_start_time')),
                                TimePicker::make('client_end_time')
                                    ->label('End Time')
                                    ->default(fn ($get) => $get('client_end_time')),
                                Select::make('price_book_id')
                                    ->label('Price Book')
                                    ->options(
                                        PriceBook::where('company_id', $companyId)
                                            ->orderByDesc('id')
                                            ->pluck('name', 'id')
                                    )
                                    ->default(fn ($get) => $get('price_book_id')),
                                Select::make('hours')
                                    ->label('Hours')
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
                                    ->default(fn ($get) => $get('hours')),
                            ])
                             ->cloneable()
                                                    ->cloneAction(
                                                        fn (\Filament\Forms\Components\Actions\Action $action) =>
                                                            $action->icon('heroicon-m-scissors')
                                                                ->button()
                                                                ->label('Split')
                                                                ->color('info')
                                                    )
                            ->columns(5)
                            ->addable(false)
                            ->visible(fn ($get) => !empty($get('client_id')))
                            ->default($this->data['client_details'] ?? []),
                    ])
                    ->collapsible(),

                Section::make('Time & Location')
                    ->schema([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->default($this->data['start_date'] ?? null),
                        Checkbox::make('shift_finishes_next_day')
                            ->label('Shift Finishes Next Day')
                            ->default($this->data['shift_finishes_next_day'] ?? false),
                        TimePicker::make('start_time')
                            ->label('Start Time')
                            ->default($this->data['start_time'] ?? null),
                        TimePicker::make('end_time')
                            ->label('End Time')
                            ->default($this->data['end_time'] ?? null),
                        TextInput::make('break_time')
                            ->label('Break Time (minutes)')
                            ->numeric()
                            ->default($this->data['break_time'] ?? null),
                        Checkbox::make('repeat')
                            ->label('Repeat')
                            ->default($this->data['repeat'] ?? false)
                            ->live(),
                        Select::make('recurrance')
                            ->label('Recurrance')
                            ->options(['Daily' => 'Daily', 'Weekly' => 'Weekly', 'Monthly' => 'Monthly'])
                            ->default($this->data['recurrance'] ?? null)
                            ->visible(fn ($get) => $get('repeat')),
                        Select::make('repeat_every_daily')
                            ->label('Repeat Every')
                            ->options(array_combine($days = range(1, 15), $days))
                            ->default($this->data['repeat_every_daily'] ?? null)
                            ->visible(fn ($get) => $get('repeat') && $get('recurrance') === 'Daily'),
                        Grid::make(4)
                            ->schema([
                                Select::make('repeat_every_weekly')
                                    ->label('Repeat Every')
                                    ->options(array_combine($weeks = range(1, 12), $weeks))
                                    ->default($this->data['repeat_every_weekly'] ?? null),
                                Checkbox::make('occurs_on_weekly.sunday')->label('Sun')->default($this->data['occurs_on_weekly']['sunday'] ?? false),
                                Checkbox::make('occurs_on_weekly.monday')->label('Mon')->default($this->data['occurs_on_weekly']['monday'] ?? false),
                                Checkbox::make('occurs_on_weekly.tuesday')->label('Tue')->default($this->data['occurs_on_weekly']['tuesday'] ?? false),
                                Checkbox::make('occurs_on_weekly.wednesday')->label('Wed')->default($this->data['occurs_on_weekly']['wednesday'] ?? false),
                                Checkbox::make('occurs_on_weekly.thursday')->label('Thu')->default($this->data['occurs_on_weekly']['thursday'] ?? false),
                                Checkbox::make('occurs_on_weekly.friday')->label('Fri')->default($this->data['occurs_on_weekly']['friday'] ?? false),
                                Checkbox::make('occurs_on_weekly.saturday')->label('Sat')->default($this->data['occurs_on_weekly']['saturday'] ?? false),
                            ])
                            ->visible(fn ($get) => $get('repeat') && $get('recurrance') === 'Weekly'),
                        Grid::make(2)
                            ->schema([
                                Select::make('repeat_every_monthly')
                                    ->label('Repeat Every')
                                    ->options([1 => 1, 2 => 2, 3 => 3])
                                    ->default($this->data['repeat_every_monthly'] ?? null),
                                Select::make('occurs_on_monthly')
                                    ->label('Occurs on Day')
                                    ->options(array_combine($days = range(1, 31), $days))
                                    ->default($this->data['occurs_on_monthly'] ?? null),
                            ])
                            ->visible(fn ($get) => $get('repeat') && $get('recurrance') === 'Monthly'),
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->default($this->data['end_date'] ?? null)
                            ->visible(fn ($get) => $get('repeat')),
                        TextInput::make('address')
                            ->label('Address')
                            ->default($this->data['address'] ?? null),
                        TextInput::make('unit_apartment_number')
                            ->label('Unit/Apartment Number')
                            ->default($this->data['unit_apartment_number'] ?? null),
                        Checkbox::make('drop_off_address')
                            ->label('Drop Off Address')
                            ->default($this->data['drop_off_address'] ?? false)
                            ->live(),
                        TextInput::make('drop_address')
                            ->label('Drop Off Address')
                            ->default($this->data['drop_address'] ?? null)
                            ->visible(fn ($get) => $get('drop_off_address')),
                        TextInput::make('drop_unit_apartment_number')
                            ->label('Drop Off Unit/Apartment Number')
                            ->default($this->data['drop_unit_apartment_number'] ?? null)
                            ->visible(fn ($get) => $get('drop_off_address')),
                    ])
                    ->collapsible(),

                Section::make('Shift')
                    ->schema([
                        Select::make('shift_type_id')
                            ->label('Shift Type')
                            ->options(ShiftType::pluck('name', 'id'))
                            ->required()
                            ->default($this->data['shift_type_id'] ?? null),
                        Select::make('additional_shift_types')
                            ->label('Additional Shift Types')
                            ->multiple()
                            ->options(ShiftType::where('user_id', auth()->id())->pluck('name', 'id'))
                            ->default($this->data['additional_shift_types'] ?? []),
                        Select::make('allowance_id')
                            ->label('Allowance')
                            ->multiple()
                            ->options(\App\Models\Allowance::where('user_id', auth()->id())->pluck('name', 'id'))
                            ->default($this->data['allowance_id'] ?? []),
                        Select::make('invoice_mileage')
                            ->label('Invoice of Mileage')
                            ->multiple()
                            ->options(Client::where('user_id', auth()->id())->where('is_archive', 'Unarchive')->pluck('display_name', 'id'))
                            ->default($this->data['invoice_mileage'] ?? []),
                        TextInput::make('mileage')
                            ->label('Mileage')
                            ->default($this->data['mileage'] ?? null),
                        TextInput::make('additional_cost')
                            ->label('Additional Cost ($)')
                            ->default($this->data['additional_cost'] ?? null),
                        Toggle::make('ignore_staff_count')
                            ->label('Ignore Staff Count')
                            ->default($this->data['ignore_staff_count'] ?? false),
                        Toggle::make('confirmation_required')
                            ->label('Confirmation Required')
                            ->default($this->data['confirmation_required'] ?? false),
                    ])
                    ->collapsible(),

                    ])->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;'])
                    ->columns(1)
                    ->columnSpan(1),

                     Section::make()
                    ->schema([
  Section::make('Carer')
                    ->schema([
                        Toggle::make('add_to_job_board')
                            ->label('Add to Job Board')
                            ->default($this->data['add_to_job_board'] ?? false)
                            ->live(),
                        Select::make('user_id')
                            ->label('Select Carer')
                            ->searchable()
                            ->options(function () {
                                $companyId = Company::where('user_id', auth()->id())->value('id');
                                $staffUserIds = $companyId
                                    ? StaffProfile::where('company_id', $companyId)->where('is_archive', 'Unarchive')->pluck('user_id')->toArray()
                                    : [];
                                $staffUserIds[] = auth()->id();
                                return User::whereIn('id', array_unique($staffUserIds))->pluck('name', 'id')->toArray();
                            })
                            ->multiple()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $details = [];
                                if (!empty($state)) {
                                    $users = User::whereIn('id', (array) $state)->get();
                                    foreach ($users as $user) {
                                        $existingDetail = collect($this->data['user_details'])->firstWhere('user_id', $user->id) ?? [];
                                        $details[] = [
                                            'user_id' => $user->id,
                                            'user_name' => $user->name,
                                            'user_start_time' => $existingDetail['user_start_time'] ?? '02:00 AM',
                                            'user_end_time' => $existingDetail['user_end_time'] ?? '03:00 AM',
                                            'pay_group_id' => $existingDetail['pay_group_id'] ?? null,
                                        ];
                                    }
                                }
                                $set('user_details', $details);
                            })
                            ->default($this->data['user_id'] ?? [])
                            ->visible(fn ($get) => !$get('add_to_job_board')),
                        Repeater::make('user_details')
                            ->label('Carer Details')
                            ->schema([
                                TextInput::make('user_name')
                                    ->label('Carer Name')
                                    ->disabled()
                                    ->default(fn ($get) => $get('user_name')),
                                TimePicker::make('user_start_time')
                                    ->label('Start Time')
                                    ->default(fn ($get) => $get('user_start_time')),
                                TimePicker::make('user_end_time')
                                    ->label('End Time')
                                    ->default(fn ($get) => $get('user_end_time')),
                                Select::make('pay_group_id')
                                    ->label('Pay Group')
                                    ->options(PayGroup::where('user_id', auth()->id())->where('is_archive', 0)->pluck('name', 'id'))
                                    ->default(fn ($get) => $get('pay_group_id')),
                            ])
                            ->columns(4)
                            ->addable(false)
                            ->visible(fn ($get) => !empty($get('user_id')) && !$get('add_to_job_board'))
                            ->default($this->data['user_details'] ?? []),
                        Checkbox::make('notify')
                            ->label('Notify Carer')
                            ->default($this->data['notify'] ?? false)
                            ->visible(fn ($get) => !$get('add_to_job_board')),
                        Section::make('Job Board Criteria')
                            ->schema([
                                Select::make('shift_assignment')
                                    ->label('Shift Assignment')
                                    ->options(['Approve automatically' => 'Approve automatically', 'Require approval' => 'Require approval'])
                                    ->default($this->data['shift_assignment'] ?? null),
                                Select::make('team_id')
                                    ->label('Teams')
                                    ->multiple()
                                    ->options(Team::where('user_id', auth()->id())->pluck('name', 'id'))
                                    ->default($this->data['team_id'] ?? []),
                                Select::make('language_id')
                                    ->label('Languages')
                                    ->multiple()
                                    ->options(Language::pluck('name', 'id'))
                                    ->default($this->data['language_id'] ?? []),
                                Select::make('compilance_id')
                                    ->label('Compliance')
                                    ->multiple()
                                    ->options(DocumentCategory::where('is_staff_doc', 1)->where('is_compliance', 1)->pluck('name', 'id'))
                                    ->default($this->data['compilance_id'] ?? []),
                                Select::make('competencies_id')
                                    ->label('Competencies')
                                    ->multiple()
                                    ->options(DocumentCategory::where('is_staff_doc', 1)->where('is_competencies', 1)->pluck('name', 'id'))
                                    ->default($this->data['competencies_id'] ?? []),
                                Select::make('kpi_id')
                                    ->label('KPIs')
                                    ->multiple()
                                    ->options(DocumentCategory::where('is_staff_doc', 1)->where('is_kpi', 1)->pluck('name', 'id'))
                                    ->default($this->data['kpi_id'] ?? []),
                                Select::make('distance_shift')
                                    ->label('Distance from Shift Location')
                                    ->options([
                                        'Any Distance' => 'Any Distance',
                                        '10 km' => '10 km', '20 km' => '20 km', '30 km' => '30 km',
                                        '40 km' => '40 km', '50 km' => '50 km', '60 km' => '60 km',
                                        '70 km' => '70 km', '80 km' => '80 km', '90 km' => '90 km', '100 km' => '100 km',
                                    ])
                                    ->default($this->data['distance_shift'] ?? null),
                            ])
                            ->visible(fn ($get) => $get('add_to_job_board')),
                    ])
                    ->collapsible(),

                Section::make('Tasks')
                    ->schema([
                        Repeater::make('tasks')
                            ->label('Tasks')
                            ->schema([
                                TextArea::make('task_description')
                                    ->label('Task Description')
                                    ->default(fn ($get) => $get('task_description')),
                                Checkbox::make('mandatory')
                                    ->label('Mandatory')
                                    ->default(fn ($get) => $get('mandatory')),
                            ])
                            ->columns(2)
                            ->addActionLabel('Add Task')
                            ->default($this->data['tasks'] ?? []),
                    ])
                    ->collapsible(),

                Section::make('Instruction')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Instruction')
                            ->default($this->data['description'] ?? null),
                    ])
                    ->collapsible(),
                    ])->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;'])
                    ->columns(1)
                    ->columnSpan(1),

                    ]),
             
              
            ])
            ->statePath('data');
    }

    public function updateShift()
    {
        $data = $this->form->getState();
        $authUser = Auth::user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');
        // dd($data);

         $carerSection = empty($data['add_to_job_board']) ? [
            'user_id' => $data['user_id'] ?? [],
            'notify' => $data['notify'] ?? false,
            'user_details' => $data['user_details'] ?? [],
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
$previousAddToJobBoard = $this->shift->add_to_job_board;

       $shiftData = [
    'client_section' => json_encode([
        'client_id' => $data['client_id'] ?? [],
        'client_details' => $data['client_details'] ?? [],
    ]),
    'time_and_location' => json_encode([
        'start_date' => $data['start_date'] ?? null,
        'shift_finishes_next_day' => $data['shift_finishes_next_day'] ?? false,
        'start_time' => $data['start_time'] ?? null,
        'end_time' => $data['end_time'] ?? null,
        'break_time' => $data['break_time'] ?? null,
        'repeat' => $data['repeat'] ?? false,
        'recurrance' => $data['recurrance'] ?? null,
        'repeat_every_daily' => $data['repeat_every_daily'] ?? null,
        'repeat_every_weekly' => $data['repeat_every_weekly'] ?? null,
        'repeat_every_monthly' => $data['repeat_every_monthly'] ?? null,
        'occurs_on_monthly' => $data['occurs_on_monthly'] ?? null,
        'occurs_on_weekly' => $data['occurs_on_weekly'] ?? [],
        'end_date' => $data['end_date'] ?? null,
        'address' => $data['address'] ?? null,
        'unit_apartment_number' => $data['unit_apartment_number'] ?? null,
        'drop_off_address' => $data['drop_off_address'] ?? false,
        'drop_address' => $data['drop_address'] ?? null,
        'drop_unit_apartment_number' => $data['drop_unit_apartment_number'] ?? null,
    ]),
    'shift_section' => json_encode([
        'shift_type_id' => $data['shift_type_id'] ?? null,
        'additional_shift_types' => $data['additional_shift_types'] ?? [],
        'allowance_id' => $data['allowance_id'] ?? [],
        'invoice_mileage' => $data['invoice_mileage'] ?? [],
        'mileage' => $data['mileage'] ?? null,
        'additional_cost' => $data['additional_cost'] ?? null,
        'ignore_staff_count' => $data['ignore_staff_count'] ?? false,
        'confirmation_required' => $data['confirmation_required'] ?? false,
    ]),
    'add_to_job_board' => $data['add_to_job_board'] ?? false,
    'carer_section' => empty($data['add_to_job_board']) ? json_encode([
        'user_id' => $data['user_id'] ?? [],
        'notify' => $data['notify'] ?? false,
        'user_details' => $data['user_details'] ?? [],
    ]) : null,
    'job_section' => !empty($data['add_to_job_board']) ? json_encode([
        'shift_assignment' => $data['shift_assignment'] ?? null,
        'team_id' => $data['team_id'] ?? [],
        'language_id' => $data['language_id'] ?? [],
        'compilance_id' => $data['compilance_id'] ?? [],
        'competencies_id' => $data['competencies_id'] ?? [],
        'kpi_id' => $data['kpi_id'] ?? [],
        'distance_shift' => $data['distance_shift'] ?? null,
    ]) : null,
     'status' => !empty($data['add_to_job_board'])
    ? 'Job Board'
    : 'Pending',
    // ✅ FIXED: save flat array of tasks
    'task_section' => json_encode($data['tasks'] ?? []),
    'instruction' => json_encode(['description' => $data['description'] ?? null]),
    'company_id' => $companyId,
    'is_advanced_shift' => true,
        'is_vacant'  => $isVacant, 

];

$shiftDate = \Carbon\Carbon::parse($data['start_date']);
$dayOfWeek = $shiftDate->format('l');
$dayType = match ($dayOfWeek) {
    'Saturday' => 'Saturday',
    'Sunday'   => 'Sunday',
    default    => 'Weekdays - I',
};

$clientDetails = $data['client_details'] ?? [];

$expectedBillingKeys = [];

foreach ($clientDetails as $detail) {
    $clientId     = $detail['client_id'];
    $priceBookId  = $detail['price_book_id'];
    $shiftStart   = \Carbon\Carbon::parse($detail['client_start_time']);
    $shiftEnd     = \Carbon\Carbon::parse($detail['client_end_time']);
    $hours        = $shiftStart->floatDiffInHours($shiftEnd);

        $priceDetail = \App\Models\PriceBookDetail::where('price_book_id', $priceBookId)
            ->where('day_of_week', $dayType)
            ->where(function ($q) use ($shiftStart, $shiftEnd) {
                $q->where(function ($sub) use ($shiftStart, $shiftEnd) {
                    $sub->whereTime('start_time', '<=', $shiftStart->format('H:i'))
                        ->where(function ($inner) use ($shiftEnd) {
                            $inner->whereTime('end_time', '>=', $shiftEnd->format('H:i'))
                                ->orWhere('end_time', '00:00:00'); // midnight means end of day
                        });
                })
                ->orWhere(function ($sub) {
                    $sub->whereTime('start_time', '00:00:00')
                        ->whereTime('end_time', '00:00:00');
                });
            })
            ->first();


    $rate         = $priceDetail?->per_hour ?? 0;
    $per_km_price = $priceDetail?->per_km ?? 0;

    // ❗ If you track actual distance, replace this 0.0 with the real km
    $distance     = $data['mileage'] ?? 0.0;
    $additionalCostPrice = $data['additional_cost'] ?? 0.0;

    $hoursXRate    = number_format($hours, 1) . ' x $' . number_format($rate, 2);
    $distanceXRate = $distance . ' x $' . number_format($per_km_price, 2);
    $totalCost     = ($hours * $rate) + ($distance * $per_km_price) + $additionalCostPrice;

    // ✅ Add start_time and end_time to uniqueness check so multiple records per client can exist
    $billing = \App\Models\BillingReport::updateOrCreate(
        [
            'shift_id'     => $this->shift->id,
            'client_id'    => $clientId,
            'price_book_id'=> $priceBookId,
            'start_time'   => $shiftStart->format('H:i'),
            'end_time'     => $shiftEnd->format('H:i'),
        ],
        [
            'date'            => $shiftDate->toDateString(),
            'staff'           => data_get($data, 'user_id.0'),
            'hours_x_rate'    => $hoursXRate,
            'additional_cost' => $data['additional_cost'] ?? 0.0,
            'distance_x_rate' => $distanceXRate,
            'total_cost'      => $totalCost,
            'running_total'   => null,
        ]
    );

    $expectedBillingKeys[] = $billing->id;
}

// ✅ Delete records no longer in client_details
\App\Models\BillingReport::where('shift_id', $this->shift->id)
    ->whereNotIn('id', $expectedBillingKeys)
    ->delete();

$shiftData['status'] = !empty($data['add_to_job_board'])
    ? 'Job Board'
    : 'Pending';


        $this->shift->update($shiftData);

        $authUser = Auth::user();

        // Always create "Updated Shift"
        Event::create([
            'shift_id' => $this->shift->id,
            'title'    => $authUser->name . ' Updated Shift',
            'from'     => 'Update',
            'body'     => 'Shift updated',
        ]);

        // If removed from job board
        if ($previousAddToJobBoard && empty($data['add_to_job_board'])) {
            Event::create([
                'shift_id' => $this->shift->id,
                'title'    => 'Shift Unpinned by ' . $authUser->name,
                'from'     => 'No Job',
                'body'     => 'Shift is no longer available on Job Board',
            ]);
        }

        // If added to job board
        if (empty($previousAddToJobBoard) && !empty($data['add_to_job_board'])) {
            Event::create([
                'shift_id' => $this->shift->id,
                'title'    => 'Job Listed by ' . $authUser->name,
                'from'     => 'Job',
                'body'     => 'Job listed on Job Board',
            ]);
        }

        Notification::make()
            ->title('Shift updated successfully')
            ->success()
            ->send();

        $this->redirect('/admin/schedular');
    }

    public function submit()
    {
        $this->updateShift();
    }
}