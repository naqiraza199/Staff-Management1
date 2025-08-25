<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\Company;
use App\Models\StaffProfile;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles; 
use App\Models\Client;
use App\Models\PriceBook;
use App\Models\ShiftType;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use App\Models\PayGroup;
use App\Models\Team;
use Filament\Forms\Components\Checkbox;


class Schedular extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-calendar';
    protected static string $view = 'filament.pages.schedular';
    protected static ?string $title = 'Schedular';

      public ?array $data = [];
    public bool $showTaskModal = false;



    public function mount()
    {
        $authUser = Auth::user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');


        if (!$companyId) {
            $this->users = [];
        } else {
            $staffUserIds = StaffProfile::where('company_id', $companyId)->where('is_archive', 'Unarchive')->pluck('user_id');
            $usersQuery = User::whereIn('id', $staffUserIds)->role('staff');

            $allUsers = $usersQuery->get()->pluck('name')->toArray();
            if (!in_array($authUser->name, $allUsers)) {    
                $allUsers[] = $authUser->name;
            }

            $this->users = $allUsers; 
        }
        $this->clients = Client::where('user_id', $authUser->id)->where('is_archive', 'Unarchive')->get();

        $this->priceBooks = PriceBook::with('priceBookDetails')
            ->where('company_id', $companyId)
            ->orderByDesc('id')
            ->get();

        $this->shiftTypes = ShiftType::where('user_id', $authUser->id)->get();
    }

    public function getUsersProperty()
    {
        return $this->users ?? [];
    }


protected function getFormSchema(): array
{
        $authUser = Auth::user();
        $companyId = Company::where('user_id', $authUser->id)->value('id');

    return [

         Section::make(
            new HtmlString('
                <span class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" 
                        viewBox="0 0 24 24" 
                        fill="currentColor" 
                        class="w-5 h-5 text-primary-600">
                        <path d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 
                                20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 
                                12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                    </svg>
                    <span>Client</span>
                </span>
            ')
        )
        ->schema([
        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('choose_client_lab')
                    ->label('Choose Client')
                    ->columnSpan(1), 

                Forms\Components\Select::make('client')
                    ->label('') 
                    ->options(
                        \App\Models\Client::where('user_id', $authUser->id)->where('is_archive', 'Unarchive')
                            ->pluck('display_name', 'id')
                    )
                    ->columnSpan(2), 
            ]),

        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('price_book_lab')
                    ->label('Price Book')
                    ->columnSpan(1), 

                Forms\Components\Select::make('price_book')
                    ->label('') 
                    ->options(
                        \App\Models\PriceBook::with('priceBookDetails')
                            ->where('company_id', $companyId)
                            ->orderByDesc('id')
                            ->pluck('name', 'id')
                    )
                    ->columnSpan(2), 
            ]),

        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('funds_lab')
                    ->label('Funds')
                    ->columnSpan(1), 

        Forms\Components\Placeholder::make('funds')
            ->label('')
            ->content(function ($record) {
                return new HtmlString('
                    <span style="background-color:#FDF6EC;color:#FFA500;padding: 10px 15px 12px;border-radius: 10px;" class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                        No Funds Available
                    </span>
                ');
            })
            ->disableLabel(),
            ]),

            ])
              ->extraAttributes(['style' => 'margin-top:100px'])
                ->collapsible(),

        Section::make(
            new HtmlString('
                <span class="flex items-center gap-2">
                    <!-- Heroicon: Clock (Solid) -->
                    <svg xmlns="http://www.w3.org/2000/svg" 
                        viewBox="0 0 24 24" 
                        fill="currentColor" 
                        class="w-5 h-5 text-primary-600">
                        <path fill-rule="evenodd" 
                            d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 
                                9.75-9.75S17.385 2.25 12 2.25Zm.75 4.5a.75.75 0 0 0-1.5 0v5.25c0 
                                .414.336.75.75.75h3.75a.75.75 0 0 0 0-1.5H12.75V6.75Z" 
                            clip-rule="evenodd" />
                    </svg>
                    <span>Shift</span>
                </span>
            ')
        )
        ->schema([
        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('shift_types_lab')
                    ->label('Shift Types')
                    ->columnSpan(1), 

                Forms\Components\Select::make('shift_types')
    ->label('') 
    ->options(
        \App\Models\ShiftType::where('user_id', auth()->id())
            ->get()
            ->mapWithKeys(fn($shift) => [
                $shift->id => '
                    <span class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" 
                              style="background-color:' . $shift->color . '"></span>
                        ' . e($shift->name) . '
                    </span>'
            ])
            ->toArray()
    )
    ->allowHtml() // ✅ makes HTML work
    ->searchable()
    ->preload()
    ->columnSpan(2),

            ]),

        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('additional_shift_types_lab')
                    ->label('Additional Shift Types')
                    ->columnSpan(1), 

                Forms\Components\Select::make('additional_shift_types')
                    ->label('') 
                    ->multiple()
                    ->options(
                        \App\Models\ShiftType::where('user_id', auth()->id())
                            ->pluck('name', 'id')
                    )
                    ->preload()
                    ->searchable()
                    ->columnSpan(2), 
            ]),

        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('allowance_lab')
                    ->label('Allowance')
                    ->columnSpan(1), 

                Forms\Components\Select::make('allowance')
                    ->label('') 
                    ->options(
                        \App\Models\Allowance::where('user_id', auth()->id())
                            ->pluck('name', 'id')
                    )
                    ->columnSpan(2), 
            ]),
            ])
              ->extraAttributes(['style' => 'margin-top:10px'])
                ->collapsible(),

             Section::make(
            new HtmlString('
                    <span class="flex items-center gap-2">
                        <!-- Heroicon: Calendar (Solid) -->
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            viewBox="0 0 24 24" 
                            fill="currentColor" 
                            class="w-5 h-5 text-primary-600">
                            <path fill-rule="evenodd" 
                                d="M6.75 2.25a.75.75 0 0 1 .75.75V4.5h9V3a.75.75 0 0 1 1.5 0v1.5h.75A2.25 
                                2.25 0 0 1 21.75 6.75v12A2.25 2.25 0 0 1 19.5 21H4.5A2.25 
                                2.25 0 0 1 2.25 18.75v-12A2.25 2.25 0 0 1 4.5 4.5h.75V3a.75.75 
                                0 0 1 .75-.75ZM3.75 9v9.75c0 
                                .414.336.75.75.75h15a.75.75 0 0 0 
                                .75-.75V9H3.75Z" 
                                clip-rule="evenodd" />
                        </svg>
                        <span>Time & Location</span>
                    </span>
                ')

        )
        ->schema([
        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('date_lab')
                    ->label('Date')
                    ->columnSpan(1), 

                Forms\Components\DatePicker::make('date')
                        ->label('') 
                        ->columnSpan(2),

            ]),

        Forms\Components\Grid::make(5) 
            ->schema([
                Forms\Components\Placeholder::make('')
                    ->label('')
                    ->columnSpan(3), 

                Forms\Components\Checkbox::make('check_shift_time')
                    ->label('Shift finishes the next day') 
                    ->columnSpan(2), 
            ]),

        Forms\Components\Grid::make(11) 
            ->schema([
             Forms\Components\Placeholder::make('time')
                    ->label('Time')
                    ->columnSpan(3), 

                Forms\Components\TimePicker::make('start_time_shift')
                    ->label('')
                    ->columnSpan(4), 

                Forms\Components\TimePicker::make('end_time_shift')
                    ->label('')
                    ->columnSpan(4), 

            ]),

        Forms\Components\Grid::make(5) 
            ->schema([
                Forms\Components\Placeholder::make('')
                    ->label('')
                    ->columnSpan(4), 

                Forms\Components\Checkbox::make('repeat')
                    ->label('Repeat') 
                    ->columnSpan(1), 
            ]),

        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('address_lab')
                    ->label('Address')
                    ->columnSpan(1), 

                Forms\Components\TextInput::make('address')
                        ->label('') 
                        ->placeholder('Enter Address')
                        ->columnSpan(2),

       

            ]),

        Forms\Components\Grid::make(5) 
            ->schema([
                Forms\Components\Placeholder::make('')
                    ->label('')
                    ->columnSpan(3), 

                Forms\Components\Placeholder::make('invalid_address')
                    ->label('')
                    ->content(function ($record) {
                        return new HtmlString('
                            <span  style="color:#09090B">
                               Invalid address, <a style="color:blue" href="">read more</a> </span>
                        ');
                    })
                    ->disableLabel()
                    ->columnSpan(2), 

            ]),

        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('unit_lab')
                    ->label('Unit/Apartment Number')
                    ->columnSpan(1), 

                Forms\Components\TextInput::make('unit')
                        ->label('') 
                        ->prefixIcon('heroicon-s-building-office')
                        ->placeholder('Enter Unit/Apartment Number')
                        ->columnSpan(2),

       

            ]),
            ])
              ->extraAttributes(['style' => 'margin-top:10px'])
                ->collapsible(),


                 Section::make(
    new HtmlString('
        <div class="flex items-center justify-between">
            <span class="flex items-center gap-2">
                <!-- Heroicon: User Group (Solid) -->
               <svg xmlns="http://www.w3.org/2000/svg" 
                        viewBox="0 0 24 24" 
                        fill="currentColor" 
                        class="w-5 h-5 text-primary-600">
                        <path d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 
                                20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 
                                12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                    </svg>
                <span>Carer</span>
            </span>

            <!-- Toggle on the right -->
            <div class="flex items-center gap-2">
                <label for="addToJobBoard" class="text-sm font-medium text-gray-700">Add to job board</label>
                <input type="checkbox" id="addToJobBoard" class="rounded toggle toggle-primary">
            </div>
        </div>
    ')
)->schema([

        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('carers_lab')
                    ->label('Choose Carer')
                    ->columnSpan(1), 

               
           Forms\Components\Select::make('carer')
                ->label('')
                ->options(function () {
                    $authUser = Auth::user();

                    $companyId = Company::where('user_id', $authUser->id)->value('id');

                    if (! $companyId) {
                        return [$authUser->id => $authUser->name];
                    }

                    $staffUserIds = StaffProfile::where('company_id', $companyId)
                        ->where('is_archive', 'Unarchive')
                        ->pluck('user_id')
                        ->toArray();

                    if (! in_array($authUser->id, $staffUserIds)) {
                        $staffUserIds[] = $authUser->id;
                    }

                    return User::whereIn('id', $staffUserIds)
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->columnSpan(2)
                ->id('carer-select')

            ]),

            
        Forms\Components\Grid::make(8) 
            ->schema([
                Forms\Components\Placeholder::make('')
                    ->label('Suggested Carer')
                    ->columnSpan(5), 

             Placeholder::make('suggested_carer')
                    ->label('')
                    ->content(function () {
                        $authUser = Auth::user();
                        return new HtmlString('
                            <span 
                                id="suggested-carer" 
                                style="text-decoration: none;color:#0D76CA"
                            >
                                '.$authUser->name.'... (28/35hrs)
                            </span>
                            <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    const span = document.getElementById("suggested-carer");
                                    const select = document.getElementById("carer-select");
                                    if(span && select) {
                                        span.addEventListener("click", function() {
                                            select.value = "'.$authUser->id.'";
                                            select.dispatchEvent(new Event("change"));
                                        });
                                    }
                                });
                            </script>
                        ');
                    })
                    ->disableLabel()
                    ->columnSpan(3)

            ]),

            Forms\Components\Grid::make(8) 
            ->schema([
                Forms\Components\Placeholder::make('')
                    ->label('')
                    ->columnSpan(6), 

                Forms\Components\Checkbox::make('notify')
                    ->label('Notify carer') 
                    ->columnSpan(2), 
            ]),

        Forms\Components\Grid::make(3) 
            ->schema([
                Forms\Components\Placeholder::make('choose_pay_group')
                    ->label('Choose pay group')
                    ->columnSpan(1), 

              Forms\Components\Select::make('pay_group')
                    ->label('')
                    ->options(function () {
                        $auth = auth()->id();

                        return PayGroup::where('user_id', $auth)
                            ->where('is_archive', 0)
                            ->pluck('name', 'id') 
                            ->toArray();
                    })
                    ->columnSpan(2)
            ]),

        // Forms\Components\Grid::make(3) 
        //     ->schema([
        //         Forms\Components\Placeholder::make('open_to')
        //             ->label('Open To')
        //             ->columnSpan(1), 

        //         Forms\Components\Select::make('team')
        //             ->label('')
        //             ->options(function () {
        //                 $authUser = Auth::user();

        //                 return Team::where('user_id', $authUser->id)
        //                     ->pluck('name', 'id') 
        //                     ->toArray();
        //             })
        //             ->columnSpan(2)
        //     ]),

        // Forms\Components\Grid::make(10) 
        //     ->schema([
        //         Forms\Components\Placeholder::make('')
        //             ->label('')
        //             ->columnSpan(7), 

        //        Placeholder::make('require_carer')
        //             ->label('')
        //             ->content(function () {
        //                 return new HtmlString('
        //                     <a href=""
        //                         style="text-decoration: none;color:#0D76CA"
        //                     >
        //                       Detail requirements
        //                     </a>
                           
        //                 ');
        //             })
        //             ->disableLabel()
        //             ->columnSpan(3)
        //     ]),

        //             Forms\Components\Grid::make(3) 
        //     ->schema([
        //         Forms\Components\Placeholder::make('shift_assignment_lab')
        //             ->label('Shift Assignment')
        //             ->columnSpan(1), 

        //         Forms\Components\Select::make('shift_assignment')
        //             ->label('')
        //             ->options([
        //                 'Approve automatically' => 'Approve automatically',
        //                 'Require approval' => 'Require approval',
        //             ])
        //             ->columnSpan(2)
        //     ]),

        

       
            ])
              ->extraAttributes(['style' => 'margin-top:10px']),

                Section::make(
    new HtmlString('
        <span class="flex items-center gap-2">
            <!-- Heroicon: Document Text (Outline) -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                 fill="none" 
                 viewBox="0 0 24 24" 
                 stroke-width="1.5" 
                 stroke="currentColor" 
                 class="w-5 h-5 text-primary-600">
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      d="M19.5 14.25v3.75a2.25 2.25 0 01-2.25 2.25h-11.25a2.25 
                         2.25 0 01-2.25-2.25V6.75A2.25 2.25 0 014.5 4.5h7.5l6 6z" />
                <path stroke-linecap="round" 
                      stroke-linejoin="round" 
                      d="M14.25 4.5v6h6" />
            </svg>
            <span>Instruction</span>
        </span>
    ')
)
        ->schema([
        Forms\Components\Grid::make(1) 
            ->schema([
                Forms\Components\RichEditor::make('instruction')
                    ->label('')
                    ->columnSpan(1), 
            ]),

            ])
              ->extraAttributes(['style' => 'margin-top:10px;margin-bottom:30px'])
                ->collapsible(),
    ];
}

  protected function getFormStatePath(): string
    {
        return 'data';
    }

}