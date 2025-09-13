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
