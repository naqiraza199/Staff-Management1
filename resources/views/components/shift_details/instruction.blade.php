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