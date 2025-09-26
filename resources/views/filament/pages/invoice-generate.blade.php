<x-filament-panels::page>
    {{ $this->form }}

    {{ $this->table }}


</x-filament-panels::page>

@push('styles')
    <style>
        .fi-fo-grid { /* Tighten form grid spacing */
            gap: 0.5rem;
        }
        .filament-toggle { /* Style toggles like buttons */
            display: inline-flex !important;
        }
        .filament-table-container { 
            border: 1px solid #e5e7eb; 
            margin-top: 1rem;
        }
    </style>
@endpush