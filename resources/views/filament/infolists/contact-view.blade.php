<!-- resources/views/filament/infolists/contact-view.blade.php -->
<div class="grid grid-cols-2 gap-4">
    <div><strong>Full Name:</strong> {{ $contact->salutation }} {{ $contact->first_name }} {{ $contact->last_name }}</div>
    <div><strong>Relation:</strong> {{ $contact->relation }}</div>
    <div><strong>Address:</strong> {{ $contact->address }}</div>
    <div>
        <strong>Contact:</strong>
        {!! $contact->mobile_number ? "📱 $contact->mobile_number " : '' !!}
        {!! $contact->phone_number ? "📞 $contact->phone_number " : '' !!}
        {!! $contact->email ? "✉️ $contact->email " : '' !!}
    </div>
    <div><strong>Company information:</strong> {{ $contact->company_information }}</div>
    <div><strong>Purchase Order:</strong> {{ $contact->purchase_order }}</div>
    <div><strong>Reference Number:</strong> {{ $contact->reference_number }}</div>
    <div><strong>Custom Field:</strong> {{ $contact->custom_field }}</div>
</div>
