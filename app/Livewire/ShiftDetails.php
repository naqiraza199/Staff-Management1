<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Shift;
use App\Models\Client;
use App\Models\ShiftType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ShiftDetails extends Component
{
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

    protected $listeners = ['updateShift'];

    public function updateShift($shiftId, $selectedDate)
    {
        $this->shiftId = $shiftId;
        $this->selectedDate = $selectedDate;
        $this->loadShiftDetails();
    }


public function loadShiftDetails()
{
    if ($this->shiftId) {
        $this->shift = Shift::find($this->shiftId);

        if ($this->shift) {
            // Safely extract IDs from JSON fields
            $clientId    = data_get($this->shift->client_section, 'client_id');
            $shiftTypeId = data_get($this->shift->shift_section, 'shift_type_id');
            $userId      = data_get($this->shift->carer_section, 'user_id');
            $startTime = data_get($this->shift->time_and_location, 'start_time');
            $endTime   = data_get($this->shift->time_and_location, 'end_time');
            $endDate   = data_get($this->shift->time_and_location, 'end_date');
            $startDate = data_get($this->shift->time_and_location, 'start_date');
            $endDate   = data_get($this->shift->time_and_location, 'end_date');

            if ($startDate) {
                $this->startDateFormatted = \Carbon\Carbon::parse($startDate)->format('M d, Y');
            } else {
                $this->startDateFormatted = 'Not defined';
            }

            if ($endDate) {
                $this->endDateFormatted = \Carbon\Carbon::parse($endDate)->format('M d, Y');
            } else {
                $this->endDateFormatted = 'Ongoing';
            }


            if ($startTime && $endTime) {
                $this->timeset = \Carbon\Carbon::parse($startTime)->format('h:i a')
                    . ' - ' .
                    \Carbon\Carbon::parse($endTime)->format('h:i a');
            } elseif ($startTime) {
                $this->timeset = \Carbon\Carbon::parse($startTime)->format('h:i a') . ' - ?';
            } elseif ($endTime) {
                $this->timeset = '? - ' . \Carbon\Carbon::parse($endTime)->format('h:i a');
            } else {
                $this->timeset = 'Time not defined';
            }


            // Lookup related models
            $this->clientName    = $clientId ? Client::find($clientId)?->display_name : 'Unknown Client';
            $this->shiftTypeName = $shiftTypeId ? ShiftType::find($shiftTypeId)?->name : 'Unknown Shift';
            $this->userName      = $userId ? User::find($userId)?->name : 'Unknown Staff';
            $this->userName      = $userId ? User::find($userId)?->name : 'Unknown Staff';
            $this->enddate      = $userId ? User::find($userId)?->name : 'Ongoing';
        }
    }
}



    public function render()
    {
        return view('livewire.shift-details');
    }
}