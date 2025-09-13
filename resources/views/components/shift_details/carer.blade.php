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