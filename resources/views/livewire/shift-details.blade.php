<div>
            

    {{-- Dropdown menu --}}
    <div class="flex justify-end mb-4">
        <x-filament::dropdown>
            <x-slot name="trigger">
                <x-filament::icon-button
                    style="margin-right: 16px;
                            margin-top: -23px;
                            border: 1px #cfc7c7 groove;
                            height: 47px;
                            width: 50px;"
                    icon="heroicon-m-cog-6-tooth"
                    color="gray"
                />
            </x-slot>

            <x-filament::dropdown.list>

                @if($shift && $shift->is_approved == 0 && !$shift->add_to_job_board && !$shift->is_vacant)
                    <x-filament::dropdown.list.item 
                        icon="heroicon-m-hand-thumb-up" 
                        color="success"
                        x-on:click="$dispatch('open-modal', { id: 'approved-shift' })">
                        Approve timesheet
                    </x-filament::dropdown.list.item>
                @endif

                @if($shift && $shift->is_approved == 1 && !$shift->add_to_job_board && !$shift->is_vacant)
                    <x-filament::dropdown.list.item 
                        icon="heroicon-m-hand-thumb-down" 
                        color="info"
                        wire:click="unapprove"
                       >
                        Unapprove timesheet
                    </x-filament::dropdown.list.item>
                @endif




                @if($shift && $shift->is_cancelled == 0)
                <x-filament::dropdown.list.item
                    icon="heroicon-m-x-circle"
                    color="warning"
                    x-data
                    @click="$dispatch('open-cancel-modal')"
                >
                    Cancel
                </x-filament::dropdown.list.item>
                @endif

                    @if($shift && $shift->is_cancelled == 1)
                <x-filament::dropdown.list.item
                    icon="heroicon-m-arrow-path"
                    color="rado"
                    wire:click="rebook"
                >
                    Reebok
                </x-filament::dropdown.list.item>
                @endif

            <x-filament::dropdown.list.item 
                icon="heroicon-m-document-text" 
                color="success"
                x-data
                @click="$dispatch('open-add-notes-modal')"
            >
                Add Notes
            </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item 
                    icon="heroicon-m-trash" 
                    color="danger" 
                    wire:click="delete"
                    x-on:click="$dispatch('open-modal', { id: 'confirm-shift-deletion' })">
                    Delete
                </x-filament::dropdown.list.item>

            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </div>
    @if($shift && $shift->is_cancelled == 1)
                <h4>The shift was cancelled.</h4>
                @endif
    {{-- Filament modal renderer --}}
    <x-filament-actions::modals />

    {{-- Extra styling --}}
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: none;
            margin-top: 50px;
        }
        .card-header {
            background: #fff;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
        }
        .row-flex {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .row-flex:last-child {
            border-bottom: none;
        }
        .label {
            color: #555;
        }
        .value {
            text-align: right;
            color: #333;
            flex-shrink: 0;
        }
        .value b {
            color: #4B8CF7;
        }
        .value-c b {
            color: #000000ff;
        }
    </style>

    {{-- Edit form --}}
    @if($isEditing)
        <div class="flex justify-start mb-4">
            <x-filament::button color="success" style="padding: 10px 15px;" wire:click="$set('isEditing', false)">
                Back
            </x-filament::button>
            <x-filament::button 
                color="brown" 
                style="padding: 10px 15px;margin-left: 10px;" 
                x-on:click="window.location.href = '{{ route('filament.admin.pages.edit-advanced-shift-form', ['shiftId' => $shift->id]) }}'"
            >
                Advanced Edit
            </x-filament::button>
        </div>

        <form wire:submit.prevent="save">
            <div x-data="{ repeatChecked: false, jobBoardActive: false, recurrance: '' }">
                {{ $this->form }}
            </div>

            <div class="mt-4 flex gap-2">
                <x-filament::button type="submit" color="success">
                    Save
                </x-filament::button>
            </div>
        </form>
    @else
        {{-- View mode --}}
        
        @if($shift)
            <div class="mt-4 gap-2">
            @if($shift && $shift->is_cancelled == 0)
                @if($shift->is_advanced_shift == 0)
                    <x-filament::button color="primary" wire:click="startEditing" style="padding: 10px 15px;">
                        Edit Shift
                    </x-filament::button>
                @endif
                <x-filament::button 
                    color="brown" 
                    style="padding: 10px 15px;margin-left: 10px;" 
                    x-on:click="window.location.href = '{{ route('filament.admin.pages.edit-advanced-shift-form', ['shiftId' => $shift->id]) }}'"
                >
                    Advanced Edit
                </x-filament::button>

            </div>
                @endif
            {{-- Client Section --}}
            @include('components.shift_details.client')

            {{-- Time & Location Section --}}
            @include('components.shift_details.time_location')

            {{-- Carer Section --}}
            @include('components.shift_details.carer')

            {{-- Instruction Section --}}
            @include('components.shift_details.instruction')

            @include('components.events')

        @else
            <p>No shift selected.</p>
        @endif
    @endif



<x-filament::modal 
    id="confirm-shift-deletion"
    width="sm"
    heading="Confirm Deletion"
    description="Are you sure you want to delete this shift? This action cannot be undone."
    wire:model.defer="confirmingShiftDeletion">
    
    <x-slot name="footer">
        <x-filament::button 
            color="danger"
            wire:click="delete"
            wire:loading.attr="disabled">
            Yes, Delete
        </x-filament::button>
        
        <x-filament::button 
            color="gray"
            x-on:click="$dispatch('close-modal', { id: 'confirm-shift-deletion' })">
            Cancel
        </x-filament::button>
    </x-slot>
</x-filament::modal>

<x-filament::modal 
    id="all-events-modal"
    width="xl"
    heading="All Events"
    wire:model="showAllEvents"
>
    <div class="timeline">
        @foreach($allEvents as $event)
            <div class="timeline-item">
                <div>{{ $event->created_at->format('D, d M Y') }}</div>
                <div class="timeline-icon green">✓</div>
                <div class="timeline-date">
                    {{ $event->created_at->diffForHumans() }} | {{ $event->created_at->format('h:i a') }}
                </div>
                <div class="timeline-card">
                    <b>{{ $event->title }}</b>
                    <div class="timeline-text">{{ $event->body }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <x-slot name="footer">
        <x-filament::button color="secondary" wire:click="$set('showAllEvents', false)">
            Close
        </x-filament::button>
    </x-slot>
</x-filament::modal>

<!-- approved modal  -->

<x-filament::modal 
    id="approved-shift"
    width="lg"
    heading="Approve timesheet"
>
<span style="font-size: 12px;
    color: grey;
    position: absolute;
    top: 50px;">{{ $startDateFormatted }}  @  {{ $timeset }}</span>
    @if ($activeStep === 1)
        <div >
            <div class="space-y-4">
                <div>
                    <span style="font-size: 12px;
    color: grey;
    position: absolute;
    top: 50px;">{{ $startDateFormatted }}  @  {{ $timeset }}</span>
                   <div style="margin-top: 12px;" class="flex">
                     <input type="radio" name="accept_type" wire:model="acceptType" value="scheduled" class="mr-2" checked>
                    <label class="block text-sm font-medium text-gray-700" style="margin-left:10px">Accept shift with scheduled times</label>
                   </div>
                    <div class="mt-2">
                        @foreach ($carers as $index => $carer)
                           <div class="flex items-center justify-between work">
                            <label class="flex items-center user">
                                <span>{{ $carer['carer']?->name ?? 'Unknown Carer' }}</span>
                            </label>
                       @if($shift && $shift->is_advanced_shift == 1)
                                    <p class="text-sm text-gray-500 timee">
                                        {{ !empty($carer['user_start_time']) ? \Carbon\Carbon::parse($carer['user_start_time'])->format('h:i a') : '--' }}
                                        -
                                        {{ !empty($carer['user_end_time']) ? \Carbon\Carbon::parse($carer['user_end_time'])->format('h:i a') : '--' }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500 timee">{{ $timeset ?? '--' }}</p>
                                @endif

                        </div>
                        @endforeach
                        <p style="font-size: 13px;color: grey;margin-top: 20px;" class="text-sm text-red-500">Clock in and out times not available because staff did not clock in or out of this shift.</p>
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <x-filament::button color="secondary" wire:click="cancel">Cancel</x-filament::button>
                    <x-filament::button color="primary" wire:click="nextStep">Next</x-filament::button>
                </div>
            </div>
        </div>
      
    @elseif ($activeStep === 2)
        <div >
                     <div style="margin-top: 12px;" class="flex">
                     <input type="radio" name="accept_type" wire:model="acceptType" value="scheduled" class="mr-2" checked>
                    <label class="block text-sm font-medium text-gray-700" style="margin-left:10px">Accept shift with scheduled times</label>
                   </div>
            <div class="space-y-4">
                        @foreach ($carers as $index => $carer)

                       <div style="margin-top:10px" class="flex items-center justify-between work">
                            <label class="flex items-center user">
                                <span>{{ $carer['carer']?->name ?? 'Unknown Carer' }}</span>
                            </label>
                           @if($shift->is_advanced_shift == 1)
                                <p class="text-sm text-gray-500 timee">{{ !empty($carer['user_start_time']) ? \Carbon\Carbon::parse($carer['user_start_time'])->format('h:i a') : '--' }}
                                                -
                                                {{ !empty($carer['user_end_time']) ? \Carbon\Carbon::parse($carer['user_end_time'])->format('h:i a') : '--' }}</p>
                            @else
                                <p class="text-sm text-gray-500 timee">{{ $timeset }}</p>
                            @endif
                        </div>
                        @endforeach


                <div>
                    <label class="block text-sm font-medium text-gray-700">Allowances</label>
                    <select wire:model="allowance" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">Select</option>
                        @foreach ($allowances as $allowance)
                            <option value="{{ $allowance->name }}">{{ $allowance->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
             <div>
                    <label class="block text-sm font-medium text-gray-700">Mileage</label>
                    <input type="number" wire:model.debounce.500ms="mileage" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Expense</label>
                    <input type="number" wire:model.debounce.500ms="expense" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                </div>
                </div>

             
                <p class="text-sm text-blue-600">Allowance, Mileage and Expense <br> you can update allowance, mileage and expense here. Simply hit the confirm button below if you are satisfied with the data above.</p>
                <div style="float: right;gap: 7px;" class="flex justify-between space-x-2">
                    <x-filament::button color="warning" wire:click="previousStep">Previous section</x-filament::button>
                    <x-filament::button color="danger" wire:click="cancel">Cancel</x-filament::button>
                    <x-filament::button color="primary" wire:click="confirm">Confirm</x-filament::button>
                </div>
            </div>
        </div>
    @endif
</x-filament::modal>

    {{-- Include cancel modal INSIDE root --}}
</div>
@include('components.cancel_modal')
@include('components.shift_notes')


