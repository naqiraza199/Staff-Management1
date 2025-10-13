<x-filament-panels::page>
        <style>
                 nav{
            display: none !important;
                    }
                    aside{
                        display: none !important;
                    }
                   
                    body{
                        background-color: #EAEAEA !important;
                    }
                    .fi-main, 
                    .fi-page {
                        max-width: 100% !important;
                    }
                    @media (min-width: 640px) {
                    .sm\:flex-row {
                        flex-direction: column-reverse !important;
                    }
                }
                .gap-y-8 {
                            row-gap: 3rem !important;
                        }
                     
                        /* 1) Make sure the page can fill viewport */
                        html, body { height: 100%; margin: 0; }

                        /* 2) Hide everything visually (doesn't remove from layout) */
                        

                        /* 3) Show target and its descendants */
                        .grid.flex-1.auto-cols-fr.gap-y-8,
                        .grid.flex-1.auto-cols-fr.gap-y-8 * {
                        visibility: visible !important;
                        pointer-events: auto !important;
                        }

                        /* 4) Pin the target in the center, on top of everything */
                        .grid.flex-1.auto-cols-fr.gap-y-8 {
                        position: fixed !important;
                        left: 50% !important;
                        top: 50% !important;
                        transform: translate(-50%, -50%) !important;
                        z-index: 99999 !important;
                        max-width: 95vw;
                        max-height: 95vh;
                        overflow: auto;
                        box-sizing: border-box;
                        }

                        
                        .center {
                        text-align: center;
                        }

                        /* Button Style */
                        .awesome-btn {
                           display: inline-block;
                            padding: 8px 15px;
                            font-size: 12px;
                            font-weight: 600;
                            color: #fff;
                            background: linear-gradient(90deg, #084895ff, #0b94baff);
                            border: none;
                            border-radius: 10px;
                            text-decoration: none;
                            box-shadow: 0 4px 15px rgba(0, 114, 255, 0.4);
                            transition: all 0.3s ease;
                            position: relative;
                            overflow: hidden;
                            margin-top:20px;
                        }

                        /* Hover Glow Animation */
                        .awesome-btn:hover {
                        transform: translateY(-3px);
                        background: linear-gradient(90deg, #0b54aeff, #0fa6d0ff);
                        }

                      
        </style>
          <div class="center">
            <a href="{{ route('filament.admin.auth.login') }}" class="awesome-btn">I have an already account?</a>
        </div>
           <form wire:submit.prevent="submit">
        {{ $this->form }}
        </form>
</x-filament-panels::page>
