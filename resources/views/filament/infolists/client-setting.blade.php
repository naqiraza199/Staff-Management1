@php
    $client = $getRecord();
@endphp


<div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4 text-sm">
    <!-- NDIS Number -->
    <div>
        <span class="text-gray-500 font-medium">NDIS Number</span>
        <div class="flex items-center justify-between mt-1">
            <span class="font-semibold text-gray-800">
                {{ $client->NDIS_number ?? '—' }}
            </span>
            <button class="text-gray-400 hover:text-gray-600" title="Copy">
                <i class="lucide-copy"></i>
            </button>
        </div>
    </div>

    <!-- Aged Care Recipient ID -->
    <div>
        <span class="text-gray-500 font-medium">Aged Care Recipient ID</span>
        <p class="mt-1 text-gray-700 font-semibold">
            {{ $client->aged_care_recipient_ID ?? '—' }}
        </p>
    </div>

    <!-- Reference Number -->
    <div>
        <span class="text-gray-500 font-medium">Reference Number</span>
        <p class="mt-1 text-gray-700 font-semibold">
            {{ $client->reference_number ?? '—' }}
        </p>
    </div>

    <!-- Custom Field -->
    <div>
        <span class="text-gray-500 font-medium">Custom Field</span>
        <p class="mt-1 text-gray-700 font-semibold">
            {{ $client->custom_field ?? '—' }}
        </p>
    </div>

    <!-- P.O. Number -->
    <div>
        <span class="text-gray-500 font-medium">P.O. Number</span>
        <p class="mt-1 text-gray-700 font-semibold">
            {{ $client->PO_number ?? '—' }}
        </p>
    </div>

    <!-- Client Type -->
    <div>
        <span class="text-gray-500 font-medium">Client Type</span>
        <p class="mt-1 text-gray-700 font-semibold">
            {{ $client->clientType->name ?? '—' }}
        </p>
    </div>
</div>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
