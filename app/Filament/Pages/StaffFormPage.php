<?php

namespace App\Filament\Pages;

use App\Models\Company;
use App\Models\StaffProfile;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Textarea;

class StaffFormPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.staff-form-page';
    protected ?string $heading = 'Add Staff';

    public static function shouldRegisterNavigation(): bool
{
    return false;
}


    public array $staffFormData = [];

    protected $listeners = ['closeModal' => 'closeModal'];

    public function mount(): void
    {
        $this->form->fill([]);
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('salutation')
                            ->label('Salutation')
                            ->options([
                                'Mr' => 'Mr',
                                'Mrs' => 'Mrs',
                                'Miss' => 'Miss',
                                'Ms' => 'Ms',
                                'Mx' => 'Mx',
                                'Doctor' => 'Doctor',
                                'Them' => 'Them',
                                'They' => 'They',
                            ])
                            ->placeholder('Select'),
                    ]),

                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->placeholder('Enter Display Name')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->placeholder('Enter Email')
                            ->required()
                            ->unique(User::class, 'email') 
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('mobile_number')
                            ->label('Mobile Number')
                            ->placeholder('Enter Mobile Number')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('phone_number')
                            ->placeholder('Phone Number')
                            ->columnSpan(1),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('role_type')
                            ->options([
                                'Carer' => 'Carer',
                                'Office User' => 'Office User',
                            ])
                            ->label('Role type')
                            ->reactive()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('role_id')
                            ->label('Role')
                            ->options(fn () => Role::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required()
                            ->columnSpan(1),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Select::make('gender')
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                                'Intersex' => 'Intersex',
                                'Non-binary' => 'Non-binary',
                                'Unspecified' => 'Unspecified',
                                'Prefer not to say' => 'Prefer not to say',
                            ])
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('dob')
                            ->label('Date Of Birth')
                            ->columnSpan(1),

                        Forms\Components\Select::make('employment_type')
                            ->options([
                                'Casual' => 'Casual',
                                'Part-Time' => 'Part-Time',
                                'Full-Time' => 'Full-Time',
                                'Contractor' => 'Contractor',
                                'Others' => 'Others',
                            ])
                            ->columnSpan(1),
                    ]),

                Forms\Components\Grid::make(1)
                    ->schema([
                        Textarea::make('address')
                            ->placeholder('Enter Address')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('staffFormData');
    }


    public function save(): void
    {
        $data = $this->form->getState();

        $companyId = Company::where('user_id', Auth::id())->value('id');

        DB::transaction(function () use ($data, $companyId) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt('12345678'),
            ]);

            if (!empty($data['role_id'])) {
                $role = Role::find($data['role_id']);
                if ($role) {
                    $user->assignRole($role->name);
                }
            }

            $staffProfileFields = [
                'salutation', 'mobile_number', 'phone_number',
                'role_type', 'role_id', 'gender', 'dob', 'employment_type', 'address', 'profile_pic'
            ];

            $profileData = array_intersect_key($data, array_flip($staffProfileFields));
            $profileData['user_id'] = $user->id;
            $profileData['company_id'] = $companyId;

            if (count(array_filter($profileData)) > 0) {
                StaffProfile::create($profileData);
            }

            Notification::make()
                ->title('New staff created successfully')
                ->success()
                ->send();

               $this->redirect('/admin/schedular');

        });

        $this->form->fill([]);
    }


}
