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
use App\Models\ShiftCancel;
use App\Models\ShiftNote;
use App\Models\Event;
use Filament\Actions\Concerns\InteractsWithActions;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use App\Models\ApprovedShift;
use App\Models\BillingReport;
use App\Models\PriceBookDetail;


class ShiftDetails extends Component implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use InteractsWithActions;
    use WithFileUploads;
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
    public $attachments = [];
    public $activeStep = 1;
    protected $allowances = [];
    public $allowance = '';
    public $carers = [];
    public $mileage;   // Initialize as null
    public $expense;



    public $isEditing = false;

    public $formData = [];

    protected $listeners = ['updateShift',
                            'cancelShift',
                            'saveNotes',
                            'uploadAttachments',
                        ];

#[On('updateIncidentHeader')]
public function updateIncidentHeader($body)
{
    \App\Models\Note::where('type', 'Incident')->update([
        'body' => $body,
    ]);

    // Sync frontend copy
    $this->dispatch('incident-header-updated', body: $body);

    // Show Filament notification
    Notification::make()
        ->title('Incident header updated successfully!')
        ->success()
        ->send();
}



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

           $this->mileage = $this->shift->shift_section['mileage'] ?? null;
           $this->expense = $this->shift->shift_section['additional_cost'] ?? null;
        $this->clientSection = $decode($this->shift->client_section);
        $this->carerSection = $decode($this->shift->carer_section);
        $shiftSection = $decode($this->shift->shift_section);
        $this->timeAndLocation = $decode($this->shift->time_and_location);

        // ✅ Normalize clients
        $clients = [];
        if ($this->shift->is_advanced_shift && !empty($this->clientSection['client_details'])) {
            foreach ($this->clientSection['client_details'] as $detail) {
                $clients[] = [
                    'client' => \App\Models\Client::find((int)($detail['client_id'] ?? 0)),
                    'priceBook' => \App\Models\PriceBook::find((int)($detail['price_book_id'] ?? 0)),
                    'start' => $detail['client_start_time'] ?? null,
                    'end' => $detail['client_end_time'] ?? null,
                    'hours' => $detail['hours'] ?? '1:1',
                ];
            }
        } elseif (!$this->shift->is_advanced_shift && !empty($this->clientSection['client_id'])) {
            $clients[] = [
                'client' => \App\Models\Client::find((int)$this->clientSection['client_id']),
                'priceBook' => \App\Models\PriceBook::find((int)($this->clientSection['price_book_id'] ?? 0)),
                'start' => $this->timeAndLocation['start_time'] ?? null,
                'end' => $this->timeAndLocation['end_time'] ?? null,
                'hours' => '1:1',
            ];
        }
        $this->clients = $clients;

        // ✅ Normalize carers
       $carers = [];
        if ($this->shift->is_advanced_shift && !empty($this->carerSection['user_details'])) {
            foreach ($this->carerSection['user_details'] as $detail) {
                $carers[] = [
                    'carer' => \App\Models\User::find((int)($detail['user_id'] ?? 0)),
                    'rate' => $detail['rate'] ?? null,
                    'start' => $detail['carer_start_time'] ?? null,
                    'end' => $detail['carer_end_time'] ?? null,
                    'hours' => $detail['hours'] ?? '1:1',
                ];
            }
        } elseif (!$this->shift->is_advanced_shift && !empty($this->carerSection['user_id'])) {
            $carers[] = [
                'carer' => \App\Models\User::find((int)$this->carerSection['user_id']),
                'rate' => null,
                'start' => $this->timeAndLocation['start_time'] ?? null,
                'end' => $this->timeAndLocation['end_time'] ?? null,
                'hours' => '1:1',
            ];
        }
        $this->carers = $carers;

        // Dates
        $startDate = $this->timeAndLocation['start_date'] ?? null;
        $endDate = $this->timeAndLocation['end_date'] ?? null;

        $this->startDateFormatted = $startDate
            ? Carbon::parse($startDate)->format('M d, Y')
            : 'Not defined';

        $this->endDateFormatted = $endDate
            ? Carbon::parse($endDate)->format('M d, Y')
            : 'Ongoing';

        // Times
        $startTime = $this->timeAndLocation['start_time'] ?? null;
        $endTime = $this->timeAndLocation['end_time'] ?? null;

        if ($startTime && $endTime) {
            $this->timeset = Carbon::parse($startTime)->format('h:i a')
                . ' - ' . Carbon::parse($endTime)->format('h:i a');
        } elseif ($startTime) {
            $this->timeset = Carbon::parse($startTime)->format('h:i a') . ' - ?';
        } elseif ($endTime) {
            $this->timeset = '? - ' . Carbon::parse($endTime)->format('h:i a');
        } else {
            $this->timeset = 'Time not defined';
        }

        // Shift type
        $shiftTypeId = $shiftSection['shift_type_id'] ?? null;
        $this->shiftTypeName = $shiftTypeId
            ? \App\Models\ShiftType::find($shiftTypeId)?->name
            : 'Unknown Shift';
    }

public function confirm()
{
    $authUser = auth()->user();

    // Save approved shift
    $approvedShift = ApprovedShift::create([
        'shift_id' => $this->shift->id,
        'allowance' => $this->allowance,
        'mileage'   => $this->mileage ?? 0,
        'expense'   => $this->expense ?? 0, 
    ]);

    $this->shift->update([
        'is_approved' => 1,
        'status'      => 'Booked',
    ]);

    if (! $this->shift->is_advanced_shift) {
        // ✅ Normal shift: existing logic
        $billingRecord = BillingReport::where('shift_id', $this->shift->id)->first();

        [$hours, $rateStr] = explode(' x $', $billingRecord->hours_x_rate);
        $hours = (float) $hours;
        $rate  = (float) str_replace(',', '', $rateStr); // clean number

        $perKmPrice = $per_km_price ?? 1; // define how you store per_km_price
        $mileage    = $approvedShift->mileage;
        $distanceCost = $mileage * $perKmPrice;
        $distanceXRate = $mileage . ' x $' . number_format($perKmPrice, 2);

        $additionalCost = $approvedShift->expense;

        $totalCost = ($hours * $rate) + $additionalCost + $distanceCost;

        $billingRecord->update([
            'additional_cost' => $additionalCost,
            'distance_x_rate' => $distanceXRate,
            'total_cost'      => $totalCost,
            'mileage'         => $approvedShift->mileage,
            'expense'         => $approvedShift->expense,
        ]);
    } else {
        // ✅ Advanced shift: update all existing billing records (no create/delete)
        $billingRecords = BillingReport::where('shift_id', $this->shift->id)->get();

        foreach ($billingRecords as $billingRecord) {
            [$hours, $rateStr] = explode(' x $', $billingRecord->hours_x_rate);
            $hours = (float) $hours;
            $rate  = (float) str_replace(',', '', $rateStr);

            $perKmPrice = $per_km_price ?? 1;
            $mileage    = $approvedShift->mileage;
            $distanceCost = $mileage * $perKmPrice;
            $distanceXRate = $mileage . ' x $' . number_format($perKmPrice, 2);

            $additionalCost = $approvedShift->expense;

            $totalCost = ($hours * $rate) + $additionalCost + $distanceCost;

            $billingRecord->update([
                'additional_cost' => $additionalCost,
                'distance_x_rate' => $distanceXRate,
                'total_cost'      => $totalCost,
                'mileage'         => $approvedShift->mileage,
                'expense'         => $approvedShift->expense,
            ]);
        }
    }

        $timeLocation = $this->shift->time_and_location;

        // Decode only if it’s not already an array
        if (is_string($timeLocation)) {
            $timeLocation = json_decode($timeLocation, true);
        }

        $startTime = \Carbon\Carbon::createFromFormat('H:i:s', $timeLocation['start_time'])->format('h:i A');
        $endTime   = \Carbon\Carbon::createFromFormat('H:i:s', $timeLocation['end_time'])->format('h:i A');
        $timeRange = $startTime . ' - ' . $endTime;


    Event::create([
        'shift_id' => $this->shift->id,
        'title'    => $authUser->name . ' approved timesheet',
        'from'     => 'Approved',
        'body'     => "Scheduled time approved for: {$timeRange} {$authUser->name}. "
                     ."Mileage: {$approvedShift->mileage} km. "
                     ."Expense: \${$approvedShift->expense}. "
                     ."Allowance: {$approvedShift->allowance}. "
                     ."Comment:",
    ]);

    Notification::make()
        ->title('Timesheet Approved')
        ->success()
        ->send();

    $this->redirect('/admin/schedular');
    $this->dispatch('close-modal', id: 'approved-shift');
}




public function nextStep()
    {
        $this->activeStep = 2;
    }

    public function previousStep()
    {
        $this->activeStep = 1;
    }

    public function cancel()
    {
        $this->activeStep = 1;
        $this->dispatch('close-modal', id: 'approved-shift');
    }

public function advertiseBySms() { /* ... */ }


public function copy() { /* ... */ }
public function cancelShift($reason, $type = null, $notes = null, $notifyCarer = false)
{
    $shiftCancelData =  ShiftCancel::create([
        'shift_id'     => $this->shift->id,
        'type'         => $reason === 'client' ? 'Cancelled by clients' : 'Cancelled by us',
        'ndis'         => $type ?? null,
        'reason'       => $notes ?? null,
        'notify_carer' => $notifyCarer ?? false,
    ]);


     $authUser = Auth::user();
    

    $this->shift->update([
        'is_cancelled' => 1,
        'status' => 'Cancelled',
    ]);

        Event::create([
        'shift_id' => $this->shift->id,
        'title'    => $authUser->name . '  Updated Shift',
        'from'     => 'Cancelled',
        'body'     => 'Cancelled Shift. Reason: ' . $shiftCancelData->reason,
        ]);

    Notification::make()
        ->title('Shift cancelled successfully!')
        ->success()
        ->send();

    $this->dispatch('shift-cancelled', message: 'Shift cancelled successfully!');

    $this->redirect('/admin/schedular');
}
  public function addNotes()
    {
        $this->dispatch('open-add-notes-modal');
    }

  public function saveNotes($noteType, $noteBody, $keepPrivate, $mileage = null)
{
    $attachmentData = [];

    if (!empty($this->attachments)) {
        foreach ($this->attachments as $file) {
            $filePath = $file->store('shift_notes', 'public');
            $attachmentData[] = [
                'file_path' => $filePath,
            ];
        }
        $this->attachments = [];
    }

     $authUser = Auth::user();

    $shiftNote = ShiftNote::create([
        'shift_id' => $this->shift->id,
        'user_id' => auth()->id(),
        'note_type' => $noteType,
        'note_body' => $noteBody,
        'keep_private' => $keepPrivate,
        'mileage' => $mileage,
        'attachments' => $attachmentData,
    ]);

    Event::create([
        'shift_id' => $this->shift->id,
        'title'    => $authUser->name . ' added a Note',
        'from'     => 'Note',
        'body'     => $shiftNote->note_body,
        'note_attachments'     => $shiftNote->attachments,
    ]);

    Notification::make()->title('Note added successfully!')->success()->send();
    $this->dispatch('note-added', message: 'Note added successfully!');

if ($authUser->hasPermissionTo('all-schedulers')) {
    $this->redirect('/admin/schedular');
} else {
    $this->redirect('/admin/own-staff-scheduler?user_id=' . $authUser->id);
}

}


 public bool $confirmingShiftDeletion = false;

public function delete(): void
{
    if (!$this->confirmingShiftDeletion) {
        $this->confirmingShiftDeletion = true;
        return;
    }

    $this->shift->delete();

    Notification::make()
        ->title('Shift deleted successfully!')
        ->success()
        ->send();

    $this->redirect('/admin/schedular');
}

   
public function rebook()
{
    $shiftCancel = ShiftCancel::where('shift_id', $this->shift->id)->first();
    if ($shiftCancel) {
        $shiftCancel->delete();
    }

    $updateData = [
        'is_cancelled' => 0,
    ];

    // ✅ If shift is on Job Board, set status = Job Board, otherwise Pending
    if ($this->shift->add_to_job_board) {
        $updateData['status'] = 'Job Board';
    } else {
        $updateData['status'] = 'Pending';
    }

    $this->shift->update($updateData);

    Notification::make()
        ->title('Shift rebooked successfully!')
        ->success()
        ->send();

    $this->redirect('/admin/schedular');
}

public function unapprove()
{
    $shiftApproved = ApprovedShift::where('shift_id', $this->shift->id)->first();
    if ($shiftApproved) {
        $shiftApproved->delete();
    }

    $updateData = [
        'is_approved' => 0,
    ];

    if ($this->shift->add_to_job_board) {
        $updateData['status'] = 'Job Board';
    } else {
        $updateData['status'] = 'Pending';
    }

    $this->shift->update($updateData);

    // --- Billing reset logic ---
    if ($this->shift->is_advanced_shift) {
        // For advanced shifts: update ALL billing records for this shift
        $billingRecords = \App\Models\BillingReport::where('shift_id', $this->shift->id)->get();

        foreach ($billingRecords as $billingRecord) {
            // Extract the per-km rate from existing distance_x_rate (handles formats like "55 x $1.00" or "55 x $1.00 = $55.00")
            $ratePart = '0.00';
            if (!empty($billingRecord->distance_x_rate)) {
                if (preg_match('/x\s*\$\s*([0-9\.,]+)/', $billingRecord->distance_x_rate, $m)) {
                    $ratePart = $m[1];
                } elseif (preg_match('/\$\s*([0-9\.,]+)/', $billingRecord->distance_x_rate, $m2)) {
                    $ratePart = $m2[1];
                }
            }

            // normalize and format the rate
            $rateNumeric = (float) str_replace(',', '', $ratePart);
            $rateFormatted = number_format($rateNumeric, 2);

            // Reset mileage part to 0.0 but keep the per-km price
            $billingRecord->update([
                'additional_cost' => 0.0,
                'distance_x_rate' => '0.0 x $' . $rateFormatted,
                'total_cost'      => 0.0,
                'running_total'   => null,
                'mileage'         => null,
                'expense'         => null,
            ]);
        }
    } else {
        // Non-advanced (original logic preserved)
        $billingRecord = \App\Models\BillingReport::where('shift_id', $this->shift->id)->first();
        if ($billingRecord) {
            $ratePart = '';
            if (!empty($billingRecord->distance_x_rate) && str_contains($billingRecord->distance_x_rate, 'x $')) {
                [, $ratePart] = explode('x $', $billingRecord->distance_x_rate);
                $ratePart = trim($ratePart); // e.g. "1.00"
            }

            $billingRecord->update([
                'additional_cost' => 0.0,
                'distance_x_rate' => '0.0 x $' . $ratePart, // ✅ reset only mileage part
                'total_cost'      => 0.0,
                'running_total'   => null,
                'mileage'         => null,
                'expense'         => null,
            ]);
        }
    }

    $authUser = auth()->user();

    // Safe decode of time_and_location (avoid "offset on string" errors)
    $timeLocation = $this->shift->time_and_location;
    if (is_string($timeLocation)) {
        $timeLocation = json_decode($timeLocation, true) ?: [];
    }

    $start = $timeLocation['start_time'] ?? null;
    $end = $timeLocation['end_time'] ?? null;

    // if times are present, format them, otherwise use placeholders
    $startTime = $start ? \Carbon\Carbon::createFromFormat('H:i:s', $start)->format('h:i A') : '--';
    $endTime   = $end ? \Carbon\Carbon::createFromFormat('H:i:s', $end)->format('h:i A') : '--';
    $timeRange = "{$startTime} - {$endTime}";

    $bodyMessage = "Timesheet verification cancelled for: {$timeRange} {$authUser->name}. Comment:";

    Event::create([
        'shift_id' => $this->shift->id,
        'title'    => $authUser->name . ' unapproved timesheet',
        'from'     => 'Unapproved',
        'body'     => $bodyMessage,
    ]);

    Notification::make()
        ->title('Timesheet Unapproved')
        ->success()
        ->send();

    $this->redirect('/admin/schedular');
}






        public function render()
        {
            $events = \App\Models\Event::where('shift_id', $this->shiftId)->orderByDesc('created_at')->take(3)->get();
            $noteTypes = \App\Models\Note::select('type', 'body')->get();
            $allowances = \App\Models\Allowance::get();

            return view('livewire.shift-details', [
                'noteTypes' => $noteTypes,
                'events' => $events,
                'allowances' => $allowances,
            ]);
        }

   public bool $showAllEvents = false;
public $allEvents = [];

public function viewAllEvents()
{
    $this->allEvents = \App\Models\Event::where('shift_id', $this->shiftId)
        ->orderByDesc('created_at')
        ->get();

    $this->showAllEvents = true;
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

    // --- Carer Section + Vacant logic ---
    $carerSection = empty($data['add_to_job_board']) ? [
        'user_id'      => data_get($data, 'carer_section.user_id'),
        'pay_group_id' => data_get($data, 'carer_section.pay_group_id'),
        'user_details' => data_get($data, 'carer_section.user_details', []),
        'notify'       => data_get($data, 'carer_section.notify', false),
    ] : null;

    // Default
    $isVacant = 0;

    if (
        empty($data['add_to_job_board']) && (
            ($carerSection['user_id'] === null && $carerSection['pay_group_id'] === null) ||
            ($carerSection['user_id'] === [] && $carerSection['user_details'] === [] && $carerSection['notify'] === false)
        )
    ) {
        $isVacant = 1;
    }

    // --- Get previous value ---
    $previousAddToJobBoard = $this->shift->add_to_job_board;

    // --- Update Shift ---
 $updateData = [
    'client_section'    => $data['client_section'] ?? [],
    'shift_section'     => $data['shift_section'] ?? [],
    'time_and_location' => $data['time_and_location'] ?? [],
    'carer_section'     => $carerSection,
    'job_section'       => !empty($data['add_to_job_board']) ? $data['job_section'] : null,
    'instruction'       => $data['instruction'] ?? [],
    'add_to_job_board'  => $data['add_to_job_board'] ?? false,
    'is_vacant'         => $isVacant,
];

// ✅ Only add "status" to the update if add_to_job_board is true
$updateData['status'] = !empty($data['add_to_job_board'])
    ? 'Job Board'
    : 'Pending';

    // ✅ Handle BillingReport recalculation only if not vacant + not on job board
if (($data['add_to_job_board'] == 0) && ($isVacant == 0)) {
    $shiftDate   = Carbon::parse(data_get($data, 'time_and_location.start_date'));
    $shiftStart  = Carbon::parse(data_get($data, 'time_and_location.start_time'));
    $shiftEnd    = Carbon::parse(data_get($data, 'time_and_location.end_time'));
    $priceBookId = data_get($data, 'client_section.price_book_id');
    $clientId    = data_get($data, 'client_section.client_id');

    // ✅ Handle overnight shift (e.g., 11PM → 3AM)
        if ($shiftEnd->lessThanOrEqualTo($shiftStart)) {
            $shiftEnd = $shiftEnd->addDay();
        }

        $hours = $shiftStart->floatDiffInHours($shiftEnd);


    $dayOfWeek = $shiftDate->format('l');
    $dayType = match ($dayOfWeek) {
        'Saturday' => 'Saturday',
        'Sunday'   => 'Sunday',
        default    => 'Weekdays - I',
    };

    // ✅ Use same corrected price detail query as create
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
                // over-midnight ranges
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
    $totalCost    = $hours * $rate;

    $hoursXRate    = number_format($hours, 1) . ' x $' . number_format($rate, 2);
    $distanceXRate = 0.0 . ' x $' . number_format($per_km_price, 2);

    // ✅ Update or create billing record
    $billingRecord = BillingReport::where('shift_id', $this->shift->id)->first();

    if ($billingRecord) {
        $billingRecord->update([
            'date'            => $shiftDate->toDateString(),
            'staff'           => data_get($data, 'carer_section.user_id'),
            'start_time'      => $shiftStart->format('H:i'),
            'end_time'        => $shiftEnd->format('H:i'),
            'hours_x_rate'    => $hoursXRate,
            'additional_cost' => $billingRecord->additional_cost ?? 0.0, // keep if any
            'distance_x_rate' => $distanceXRate,
            'total_cost'      => $totalCost, // ✅ recalc
            'running_total'   => $billingRecord->running_total,
            'price_book_id'   => $priceBookId,
            'client_id'       => $clientId,
        ]);
    } else {
        BillingReport::create([
            'date'            => $shiftDate->toDateString(),
            'shift_id'        => $this->shift->id,
            'staff'           => data_get($data, 'carer_section.user_id'),
            'start_time'      => $shiftStart->format('H:i'),
            'end_time'        => $shiftEnd->format('H:i'),
            'hours_x_rate'    => $hoursXRate,
            'additional_cost' => 0.0,
            'distance_x_rate' => $distanceXRate,
            'total_cost'      => $totalCost,
            'running_total'   => null,
            'price_book_id'   => $priceBookId,
            'client_id'       => $clientId,
        ]);
    }
}




$this->shift->update($updateData);

    $authUser = Auth::user();

    // Always create "Updated Shift"
    Event::create([
        'shift_id' => $this->shift->id,
        'title'    => $authUser->name . ' Updated Shift',
        'from'     => 'Update',
        'body'     => 'Shift updated',
    ]);

    // If it was on Job Board, but now removed
    if ($previousAddToJobBoard && empty($data['add_to_job_board'])) {
        Event::create([
            'shift_id' => $this->shift->id,
            'title'    => 'Shift Unpinned by ' . $authUser->name,
            'from'     => 'No Job',
            'body'     => 'Shift is no longer available on Job Board',
        ]);
    }

    // If it was not on Job Board, but now added
    if (empty($previousAddToJobBoard) && !empty($data['add_to_job_board'])) {
        Event::create([
            'shift_id' => $this->shift->id,
            'title'    => 'Job Listed by ' . $authUser->name,
            'from'     => 'Job',
            'body'     => 'Job listed on Job Board',
        ]);
    }

    Notification::make()
        ->title('Shift updated successfully!')
        ->success()
        ->send();

    $this->redirect('/admin/schedular');

    $this->isEditing = false;
    $this->loadShiftDetails();
}



}
