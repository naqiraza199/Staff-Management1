<x-filament-panels::page>
    {{ $this->table }}

    <script>
        window.addEventListener('print-page', () => {
            window.print();
        });
    </script>
</x-filament-panels::page>