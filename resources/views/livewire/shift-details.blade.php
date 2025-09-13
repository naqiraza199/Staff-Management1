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
                <x-filament::dropdown.list.item 
                    icon="heroicon-m-chat-bubble-left-right" 
                    wire:click="advertiseBySms">
                    Advertise by SMS
                </x-filament::dropdown.list.item>

                @if($shift && $shift->is_approved == 0)
                    <x-filament::dropdown.list.item 
                        icon="heroicon-m-check-badge" 
                        wire:click="approveTimesheet">
                        Approve timesheet
                    </x-filament::dropdown.list.item>
                @endif

                <x-filament::dropdown.list.item 
                    icon="heroicon-m-document-duplicate" 
                    wire:click="copy">
                    Copy
                </x-filament::dropdown.list.item>

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
                    wire:click="delete">
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
        @else
            <p>No shift selected.</p>
        @endif
    @endif

    {{-- Include cancel modal INSIDE root --}}
</div>
@include('components.cancel_modal')
@include('components.shift_notes')
