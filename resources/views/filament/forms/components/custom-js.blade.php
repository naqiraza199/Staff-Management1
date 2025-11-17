<script>
    // Wait for Livewire to finish loading, then call the function for the specific field ID
    document.addEventListener('livewire:load', function () {
        window.initCustomDatePicker('{{ $fieldId }}');
    });

    // Also call it immediately if the script loads late, using Alpine.js's nextTick
    document.addEventListener('alpine:init', () => {
        Alpine.nextTick(() => {
            window.initCustomDatePicker('{{ $fieldId }}');
        })
    });
</script>