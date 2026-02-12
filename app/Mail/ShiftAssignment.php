<?php

namespace App\Mail;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShiftAssignment extends Mailable
{
    use Queueable, SerializesModels;

    public $shift;
    public $staff;
    public $admin;

    /**
     * Create a new message instance.
     */
    public function __construct(Shift $shift, User $staff, User $admin)
    {
        $this->shift = $shift;
        $this->staff = $staff;
        $this->admin = $admin;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        // Decode the JSON fields
        $timeAndLocation = is_string($this->shift->time_and_location)
            ? json_decode($this->shift->time_and_location, true)
            : ($this->shift->time_and_location ?? []);

        $clientSection = is_string($this->shift->client_section)
            ? json_decode($this->shift->client_section, true)
            : ($this->shift->client_section ?? []);

        $shiftSection = is_string($this->shift->shift_section)
            ? json_decode($this->shift->shift_section, true)
            : ($this->shift->shift_section ?? []);

        $instruction = is_string($this->shift->instruction)
            ? json_decode($this->shift->instruction, true)
            : ($this->shift->instruction ?? []);

        // Get client name - handle both single client_id and array of client_ids
        $clientId = $clientSection['client_id'] ?? null;
        $clientName = 'Unknown Client';
        
        if ($clientId) {
            if (is_array($clientId)) {
                // For advanced shifts with multiple clients, get the first client's name
                $firstClientId = $clientId[0] ?? null;
                if ($firstClientId) {
                    $client = \App\Models\Client::find($firstClientId);
                    $clientName = $client ? $client->display_name : 'Multiple Clients';
                }
            } else {
                $client = \App\Models\Client::find($clientId);
                $clientName = $client ? $client->display_name : 'Unknown Client';
            }
        }

        // Get shift type name
        $shiftType = \App\Models\ShiftType::find($shiftSection['shift_type_id'] ?? null);
        $shiftTypeName = $shiftType ? $shiftType->name : 'General';

        // Format dates and times in American format (MM/DD/YYYY with AM/PM)
        $startDate = $timeAndLocation['start_date'] ?? null;
        $startTime = $timeAndLocation['start_time'] ?? null;
        $endTime = $timeAndLocation['end_time'] ?? null;
        $address = $timeAndLocation['address'] ?? null;
        $unitApartmentNumber = $timeAndLocation['unit_apartment_number'] ?? null;
        $description = $instruction['description'] ?? null;

        // Format date to American format (MM/DD/YYYY)
        $formattedDate = null;
        if ($startDate) {
            try {
                $carbonDate = \Carbon\Carbon::parse($startDate);
                $formattedDate = $carbonDate->format('m/d/Y');
            } catch (\Exception $e) {
                $formattedDate = $startDate;
            }
        }

        // Format time to include AM/PM
        $formattedStartTime = null;
        $formattedEndTime = null;
        if ($startTime) {
            try {
                $carbonStart = \Carbon\Carbon::parse($startTime);
                $formattedStartTime = $carbonStart->format('h:i A');
            } catch (\Exception $e) {
                $formattedStartTime = $startTime;
            }
        }
        if ($endTime) {
            try {
                $carbonEnd = \Carbon\Carbon::parse($endTime);
                $formattedEndTime = $carbonEnd->format('h:i A');
            } catch (\Exception $e) {
                $formattedEndTime = $endTime;
            }
        }

        return $this->subject('New Shift Assignment - ' . $clientName)
            ->view('emails.shift-assignment')
            ->with([
                'staffName' => $this->staff->name,
                'adminName' => $this->admin->name,
                'clientName' => $clientName,
                'shiftType' => $shiftTypeName,
                'date' => $startDate,
                'formattedDate' => $formattedDate,
                'startTime' => $startTime,
                'formattedStartTime' => $formattedStartTime,
                'endTime' => $endTime,
                'formattedEndTime' => $formattedEndTime,
                'address' => $address,
                'unitApartmentNumber' => $unitApartmentNumber,
                'description' => $description,
            ]);
    }
}
