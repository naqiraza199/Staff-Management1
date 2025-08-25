<x-filament::page>
    <div>
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button wire:click="saveAll">
                Save
            </x-filament::button>
        </div>
    </div>
</x-filament::page>
