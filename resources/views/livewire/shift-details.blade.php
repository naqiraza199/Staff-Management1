<div>   
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

            <x-filament::dropdown.list.item 
                icon="heroicon-m-x-circle" 
                color="warning" 
                wire:click="cancel">
                Cancel
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item 
                icon="heroicon-m-document-text" 
                color="success" 
                wire:click="addNotes">
                Add notes
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

    @if($isEditing)
        <!-- Back button at top -->
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
        @if($shift)
            <div class="mt-4 flex gap-2">
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

            <!-- Client Section -->
            <div class="container mt-3">
                <div class="card">
                    <div class="card-header">
                        <span>Client</span>
                        <span>{{ $shiftTypeName }}</span>
                    </div>
                    <div class="card-body">
                      {{-- Advanced shift clients --}}
                        {{-- ================= Clients ================= --}}
                        @if($shift->is_advanced_shift)
                            @foreach(($clientSection['client_details'] ?? []) as $index => $detail)
                                @php
                                    $client = \App\Models\Client::find((int)($detail['client_id'] ?? 0));
                                    $priceBook = \App\Models\PriceBook::find((int)($detail['price_book_id'] ?? 0));
                                @endphp

                                <div class="row-flex">
                                    <div class="label">Client {{ (int)$index + 1 }}</div>
                                    <div class="value"><b>{{ $client?->display_name ?? 'Unknown Client' }}</b></div>
                                </div>

                                <div class="row-flex">
                                    <div class="label">Price book</div>
                                    <div class="value">{{ $priceBook?->name ?? 'Unknown' }}</div>
                                </div>

                                <div class="row-flex">
                                    <div class="label">Time</div>
                                    <div class="value-c">
                                        <b>
                                            {{ !empty($detail['client_start_time']) ? \Carbon\Carbon::parse($detail['client_start_time'])->format('h:i a') : '--' }}
                                            -
                                            {{ !empty($detail['client_end_time']) ? \Carbon\Carbon::parse($detail['client_end_time'])->format('h:i a') : '--' }}
                                        </b>
                                    </div>
                                </div>

                                <div class="row-flex">
                                    <div class="label">Ratio</div>
                                    <div class="value-c">{{ $detail['hours'] ?? '1:1' }}</div>
                                </div>

                                @if(!$loop->last) <hr class="my-2"> @endif
                            @endforeach
                        @else
                            @php
                                $client = \App\Models\Client::find((int)($clientSection['client_id'] ?? 0));
                                $priceBook = \App\Models\PriceBook::find((int)($clientSection['price_book_id'] ?? 0));
                            @endphp

                            <div class="row-flex">
                                <div class="label">Client</div>
                                <div class="value"><b>{{ $client?->display_name ?? 'Unknown Client' }}</b></div>
                            </div>

                            <div class="row-flex">
                                <div class="label">Price book</div>
                                <div class="value">{{ $priceBook?->name ?? 'Unknown' }}</div>
                            </div>

                            <div class="row-flex">
                                <div class="label">Ratio</div>
                                <div class="value-c">1:1, <b>Ref No.</b> {{ $priceBook?->reference_number ?? 'N/A' }}</div>
                            </div>
                        @endif






                      
                    </div>
                </div>
            </div>

            <!-- Time & Location Section -->
            <div class="container mt-3">
                <div class="card">
                    <div class="card-header">
                        <span>Time & Location</span>
                    </div>
                    <div class="card-body">
                        <div class="row-flex">
                            <div class="label">Time</div>
                            <div class="value-c"><b>{{ $timeset }} ({{ $startDateFormatted }} to {{ $endDateFormatted }})</b></div>
                        </div>

                        <div class="row-flex">
                            <div class="label">Date</div>
                            <div class="value">
                                {{ $startDateFormatted }}
                                <br>
                               @if($timeAndLocation['repeat'] ?? false)
                                    @switch($timeAndLocation['recurrance'])
                                        @case('Daily')
                                            Daily - Every {{ $timeAndLocation['repeat_every_daily'] ?? 1 }} day(s) until {{ $endDateFormatted }}
                                            @break

                                        @case('Weekly')
                                            Weekly - Every {{ $timeAndLocation['repeat_every_weekly'] ?? 1 }} week(s)
                                            @if(!empty($timeAndLocation['occurs_on_weekly']))
                                                on 
                                                @foreach($timeAndLocation['occurs_on_weekly'] as $day => $val)
                                                    @if($val) {{ ucfirst($day) }}@if(!$loop->last), @endif @endif
                                                @endforeach
                                            @endif
                                            until {{ $endDateFormatted }}
                                            @break

                                        @case('Monthly')
                                            Monthly - Every {{ $timeAndLocation['repeat_every_monthly'] ?? 1 }} month(s)
                                            @if($timeAndLocation['occurs_on_monthly'] ?? false)
                                                on day {{ $timeAndLocation['occurs_on_monthly'] }}
                                            @endif
                                            until {{ $endDateFormatted }}
                                            @break

                                        @default
                                            Repeats until {{ $endDateFormatted }}
                                    @endswitch
                                @else
                                    One-off - No repeat
                                @endif

                            </div>
                        </div>

                        <div class="row-flex">
                            <div class="label">Address</div>
                            <div class="value-c">
                                {{ $timeAndLocation['address'] ?? 'N/A' }}
                                @if($timeAndLocation['unit_apartment_number'] ?? false)
                                    , {{ $timeAndLocation['unit_apartment_number'] }}
                                @endif
                                @if($shift->is_advanced_shift && ($timeAndLocation['drop_off_address'] ?? false))
                                    <br>Drop-off: {{ $timeAndLocation['drop_address'] ?? 'N/A' }}
                                    @if($timeAndLocation['drop_unit_apartment_number'] ?? false)
                                        , {{ $timeAndLocation['drop_unit_apartment_number'] }}
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carer Section -->
            <div class="container mt-3">
                <div class="card">
                    <div class="card-header">
                        <span>Carer</span>
                    </div>
                    <div class="card-body">
                   {{-- ================= Carers ================= --}}
                            @if($shift->is_advanced_shift)
                                @foreach(($carerSection['user_details'] ?? []) as $index => $detail)
                                    @php
                                        $carer = \App\Models\User::find((int)($detail['user_id'] ?? 0));
                                    @endphp

                                    <div class="row-flex">
                                        <div class="label">Carer {{ (int)$index + 1 }}</div>
                                        <div class="value"><b>{{ $carer?->name ?? 'Unknown Staff' }}</b></div>
                                    </div>
                                    @php
                                        $paygroup = \App\Models\PayGroup::find((int)($detail['pay_group_id'] ?? 0));
                                    @endphp
                                    <div class="row-flex">
                                        <div class="label">Pay Group</div>
                                        <div class="value">{{ $paygroup->name ?? '--' }}</div>
                                    </div>

                                    <div class="row-flex">
                                        <div class="label">Time</div>
                                        <div class="value-c">
                                            <b>
                                                {{ !empty($detail['user_start_time']) ? \Carbon\Carbon::parse($detail['user_start_time'])->format('h:i a') : '--' }}
                                                -
                                                {{ !empty($detail['user_end_time']) ? \Carbon\Carbon::parse($detail['user_end_time'])->format('h:i a') : '--' }}
                                            </b>
                                        </div>
                                    </div> 
                                 @php
                                        $start = !empty($detail['user_start_time']) ? \Carbon\Carbon::parse($detail['user_start_time']) : null;
                                        $end   = !empty($detail['user_end_time']) ? \Carbon\Carbon::parse($detail['user_end_time']) : null;

                                        if ($start && $end) {
                                            // Handle overnight shift (end before start = next day)
                                            if ($end->lessThan($start)) {
                                                $end->addDay();
                                            }

                                            // Always positive hours
                                            $hours = $end->diffInMinutes($start) / 60;

                                            // Round to 2 decimals (or cast to int if you want whole hours)
                                            $hours = number_format($hours, 2);
                                        } else {
                                            $hours = null;
                                        }
                                    @endphp

                                    <div class="row-flex">
                                        <div class="label">Total hours scheduled on {{ $startDateFormatted }}</div>
                                        <div class="value-c">
                                            {{ $hours !== null ? $hours . ' hours' : '--' }}
                                        </div>
                                    </div>




                                    @if(!$loop->last) <hr class="my-2"> @endif
                                @endforeach
                            @else
                                @php
                                    $carer = \App\Models\User::find((int)($carerSection['user_id'] ?? 0));
                                    $paygroup = \App\Models\PayGroup::find((int)($carerSection['pay_group_id'] ?? 0));

                                @endphp

                                <div class="row-flex">
                                    <div class="label">Carer</div>
                                    <div class="value"><b>{{ $carer?->name ?? 'Unknown Staff' }}</b></div>
                                </div>

                                    <div class="row-flex">
                                        <div class="label">Pay Group</div>
                                        <div class="value">{{ $paygroup->name ?? '--' }}</div>
                                    </div>
                            @endif


                        
                </div>
            </div>

            <!-- Instruction Section -->
            <div class="container mt-3">
                <div class="card">
                    <div class="card-header">
                        <span>Instruction</span>
                    </div>
                    <div class="card-body">
                        @php
                            $instruction = is_string($shift->instruction) ? json_decode($shift->instruction, true) ?? [] : ($shift->instruction ?? []);
                            $description = $instruction['description'] ?? '--';
                        @endphp
                        {!! $description !!}
                    </div>
                </div>
            </div>


            <!-- General Shift Details -->
            <!-- <p><strong>Shift ID:</strong> {{ $shift->id }}</p> -->
        @else
            <p>No shift selected.</p>
        @endif
    @endif
</div>