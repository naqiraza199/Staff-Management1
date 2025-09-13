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
