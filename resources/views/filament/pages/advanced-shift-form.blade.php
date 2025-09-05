<x-filament-panels::page>
    <style>
        nav{
            display: none !important;
        }
        aside{
            display: none !important;
        }
        h1{
            display: none !important;
        }
        body{
            background-color: #EAEAEA !important;
        }
        .biti{
            position: absolute; /* keep it visible even when scrolling */
            top: 20px;
            left: 20px;
            background: #086BAC;
            color: white;
            padding: 12px 24px;
            border-radius: 20px;
            z-index: 50;
        }
        .fi-main, 
        .fi-page {
            max-width: 100% !important;
        }
    </style>

    <button class="biti" onclick="window.history.back()">Close</button>

    <div class="w-screen min-h-screen px-6" x-data="{ repeatChecked: false, jobBoardActive: false, recurrance: '' }">
        {{ $this->form }}
                <x-filament::button style=" position: absolute;
                                            top: 3%;
                                            right: 2%;
                                            padding: 15px 25px 15px 25px;
                                            border-radius: 20px;
" color="primary" wire:click="createShift">SAVE</x-filament::button>
    </div>
</x-filament-panels::page>
