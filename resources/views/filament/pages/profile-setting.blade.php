<x-filament::page>
    <div>
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button wire:click="saveAll">
                Save
            </x-filament::button>
        </div>
    </div>
    <script>
    window.addEventListener('redirect-to-stripe', event => {
        window.location.href = event.detail.url;
    });
</script>
</x-filament::page>
