<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use App\Models\StaffProfile;
use Filament\Infolists;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Carbon\Carbon;
use Filament\Infolists\Components\View as InfolistView;
use Illuminate\Support\Facades\Cache;
use App\Models\StaffContact;
use App\Models\JobTitle;
use App\Models\PayGroup;
use App\Models\StaffPayrollSetting;
use Filament\Forms\Components\Textarea;

class UserResource extends Resource
{
    protected static ?string $model = User::class;


        protected static ?string $navigationGroup = 'Staff Management';

    protected static ?string $navigationIcon = 'heroicon-s-user';

    public static function getModelLabel(): string
    {
        return 'Staff';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Staff';
    }

public static function getEloquentQuery(): Builder
{
    $authUser = Auth::user();

    $companyId = Cache::remember("user:{$authUser->id}:company_id", now()->addMinutes(10), function () use ($authUser) {
        return Company::where('user_id', $authUser->id)->value('id');
    });

    if (! $companyId) {
        return User::whereRaw('0 = 1');
    }

    // ✅ Get staff user IDs for this company
    $staffUserIds = StaffProfile::where('company_id', $companyId)
        ->where('is_archive', 'Unarchive')
        ->pluck('user_id')
        ->toArray();

    // ✅ Include the logged-in user
    if (!in_array($authUser->id, $staffUserIds)) {
        $staffUserIds[] = $authUser->id;
    }

    // ✅ Return the query including the logged-in user
    return User::with(['staffProfile'])
        ->select(['id', 'name', 'email', 'created_at', 'updated_at'])
        ->whereIn('id', $staffUserIds)
        ->where(function ($query) {
            $query->role('staff')
                  ->orWhereHas('roles', function ($q) {
                      $q->where('name', '!=', 'staff'); // ensures logged-in non-staff users are also included
                  });
        });
}


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Staff Detail')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 5])
                            ->schema([
                                Forms\Components\Fieldset::make('Salutation')
                                    ->schema([
                                        Forms\Components\Checkbox::make('use_salutation')
                                            ->label('Use salutation')
                                            ->inline()
                                            ->default(true)
                                            ->reactive()
                                            ->columnSpanFull(),
                                        Forms\Components\Select::make('salutation')
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
                                            ->placeholder('Select')
                                            ->disabled(fn($get) => !$get('use_salutation')),
                                    ])
                                    ->columnSpan(2),
                                Forms\Components\Fieldset::make('Staff Info')
                                    ->schema([
                                        Forms\Components\TextInput::make('first_name')->label('First name')->placeholder('Enter First Name'),
                                        Forms\Components\TextInput::make('middle_name')->label('Middle name')->placeholder('Enter Middle Name'),
                                        Forms\Components\TextInput::make('last_name')->label('Last name')->placeholder('Enter Last/Family Name'),
                                        Forms\Components\TextInput::make('password')->label('Password')->password()->required()->maxLength(255)->placeholder('Enter Password')
                              ->hidden(fn () => request()->routeIs('filament.admin.resources.users.edit')),
                                    ])
                                    ->columnSpan(3)
                                    ->columns(2),
                            ]),
                                   Forms\Components\Fieldset::make('Display Name')
                                    ->schema([
                                    Forms\Components\TextInput::make('name')->label('')->placeholder('Enter Display Name')->columnSpanFull(),
                            ]),
                                Forms\Components\Fieldset::make('Email Address')
                                    ->schema([
                        Forms\Components\TextInput::make('email')->email()->label('')->placeholder('Enter Email')->columnSpanFull(),
                            ]),

                                Forms\Components\Fieldset::make('Contact')
                                    ->schema([
    Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                                Forms\Components\TextInput::make('mobile_number')->label('Mobile Number')->placeholder('Enter Mobile Number')->columnSpan(1),
                                Forms\Components\TextInput::make('phone_number')->placeholder('Phone Number')->columnSpan(1),
                            ]),
                            ]),

                               Forms\Components\Fieldset::make('Role Info')
                                    ->schema([
    Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                                Forms\Components\Select::make('role_type')
                                    ->options([
                                        'Carer' => 'Carer',
                                        'Office User' => 'Office User',
                                    ])
                                    ->label('Role type')
                                    ->reactive()
                                    ->columnSpan(1),
                                Forms\Components\Select::make('role_id')
                                    ->label('Role')
                                         ->relationship('roles', 'name')
                                    ->visible(fn($get) => $get('role_type') === 'Office User')
                                    ->columnSpan(1),
                            ]),
                            ]),

                                      Forms\Components\Fieldset::make('Other Info')
                                    ->schema([
    Forms\Components\Grid::make(['default' => 2])
                            ->schema([
  Forms\Components\Select::make('gender')->options([
                                    'Male' => 'Male',
                                    'Female' => 'Female',
                                    'Intersex' => 'Intersex',
                                    'Non-binary' => 'Non-binary',
                                    'Unspecified' => 'Unspecified',
                                    'Prefer not to say' => 'Prefer not to say',
                                ])->columnSpan(1),
                                Forms\Components\DatePicker::make('dob')->label('Date Of Birth')->columnSpan(1),
                                                        Forms\Components\Select::make('employment_type')->options([
                            'Casual' => 'Casual',
                            'Part-Time' => 'Part-Time',
                            'Full-Time' => 'Full-Time',
                            'Contractor' => 'Contractor',
                            'Ohters' => 'Ohters',
                                                        ]),
                                Forms\Components\Select::make('job_title_id')
                                            ->label('Job Title')
                                            ->placeholder('Select Job Title')
                                            ->options(function () {
                                                $user = Auth::user();
                                                $companyId = Company::where('user_id', $user->id)->value('id');

                                                return JobTitle::where('company_id', $companyId)
                                                    ->where('status', 'Active')
                                                    ->pluck('name', 'id') // 👈 shows name, saves id
                                                    ->toArray();
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan(1),

                            ]),
                            ]),

                                    Forms\Components\Fieldset::make('Address')
                                    ->schema([
                       Textarea::make('address')->label('')->placeholder('Enter Address')->columnSpanFull(),
                            
                                    ]),

                                        Forms\Components\Fieldset::make('Profile Picture')
                                    ->schema([
                        Forms\Components\FileUpload::make('profile_pic')->label('')->placeholder('Profile Picture')->columnSpanFull(),
                                    ]),




                
                           

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('staffProfile.salutation')
                    ->label('Salutation')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('staffProfile.first_name')
                    ->label('First Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('staffProfile.middle_name')
                    ->label('Middle Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('staffProfile.last_name')
                    ->label('Last Name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('staffProfile.mobile_number')
                    ->label('Mobile Number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('staffProfile.phone_number')
                    ->label('Phone Number')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('staffProfile.role_type')
                    ->label('Role Type')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('staffProfile.gender')
                    ->label('Gender')
                    ->badge()
                    ->color('warning')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('staffProfile.dob')
                    ->label('Date of Birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('staffProfile.employment_type')
                    ->label('Employment Type')
                    ->badge()
                    ->color('success')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('staffProfile.address')
                    ->label('Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
  

                Tables\Actions\ViewAction::make()->button()->color('warning')->label('')->iconbutton()->tooltip('View Staff'),
                Tables\Actions\EditAction::make()->button()->color('stripe')->label('')->iconbutton()->tooltip('Edit Staff'),
                // Tables\Actions\DeleteAction::make()->button()->color('danger'),
               Action::make('Archive')
                    ->button()
                    ->color('darkk')
                    ->icon('heroicon-s-archive-box')
                    ->label('')
                    ->iconButton()
                    ->tooltip('Goes To Archive')
                    ->visible(fn ($record) => $record->id !== Auth::id())
               ->action(function ($record) {
                         $staffProfile = StaffProfile::where('user_id', $record->id)->first();

                        if ($staffProfile) {
                            $staffProfile->is_archive = 'Archive';
                            $staffProfile->save();

                        Notification::make()
                        ->success()
                        ->title('Success')
                        ->body('Staff Archived Successfully')
                        ->send();
                        }

                        else{
                            Notification::make()
                            ->danger()
                            ->title('Error')
                            ->body(' Staff Not Found')
                            ->send();
                        }
                    }),

            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Grid::make(3)
                ->schema([
                    // Main Staff Information Section (2/3 width)
                               Section::make('')
                        ->schema([
                    Section::make('Staff Information')
                        ->schema([
                            Grid::make(2)
                                ->schema([
                            
                                    
                                    TextEntry::make('name')
                                        ->label('Name')
                                        ->weight('bold')
                                        ->icon('heroicon-m-user')
                                        ->size('lg'),

                                        ImageEntry::make('staffProfile.profile_pic')
                                            ->label('')
                                            ->circular()
                                            ->size(200)
                                            ->visible(fn ($state) => $state !== null),
                                ]),
                            
                            Grid::make(3)
                                ->schema([
                                     TextEntry::make('email')
                                        ->label('Email Address')
                                        ->icon('heroicon-m-envelope'),
                                  TextEntry::make('staffProfile.phone_number')
                                        ->label('Phone Number')
                                        ->icon('heroicon-m-phone'),
                            TextEntry::make('email')
                                ->label('Email Address')
                                ->icon('heroicon-m-envelope'),
                                ]),
                                 TextEntry::make('staffProfile.address')
                                        ->label('Address')
                                        ->icon('heroicon-m-map-pin'),
                            
                            Grid::make(2)
                                ->schema([
                                   
                            TextEntry::make('staffProfile.gender')
                                ->label('Gender')
                                ->badge()
                                ->color('info'),
                            TextEntry::make('staffProfile.dob')
                                ->label('Date of Birth')
                                ->date()
                                ->icon('heroicon-m-calendar'),
                                  
                                ]),

                            Grid::make(2)
                                ->schema([
                                   
                            TextEntry::make('staffProfile.employment_type')
                                ->label('Employment Type')
                                ->badge()
                                ->color('success'),
                            
                            TextEntry::make('staffProfile.role_type')
                                ->label('Role Type')
                                ->badge()
                                ->color('warning'),
                                  
                                ]),
                        ])->headerActions([
                                InfolistAction::make('edit_staff')
                                    ->label('Edit Staff')
                                    ->icon('heroicon-s-pencil-square')
                                    ->color('primary')
                                    ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record]))
                                    ->openUrlInNewTab(false)
                                        
                        ]),
                            Section::make('About me')
                        ->schema([
                            TextEntry::make('about')
                                ->label('') 
                                ->default(function ($record) {
                                    return optional($record->staffProfile)->about ?: 'No information available.';
                                }),
                        ])->headerActions([
                                InfolistAction::make('about_me')
                                    ->label('Edit')
                                    ->icon('heroicon-s-pencil-square')
                                    ->color('primary')
                                   ->form([
                                           Textarea::make('about')
                                                ->label('About me')
                                                ->rows(5)
                                                ->default(fn ($record) => StaffProfile::where('user_id', $record->id)->value('about'))
                                                ->placeholder('Write something to describe yourself')
                                                ->required(),
                                        ])
                                            ->action(function (array $data, $record): void {

                                                // find staff profile
                                                $getStaff = StaffProfile::where('user_id', $record->id)->firstOrFail();

                                                // update the field(s)
                                                $getStaff->update([
                                                    'about' => $data['about'],
                                                ]);

                                                // Fire success notification
                                                Notification::make()
                                                    ->title('Success')
                                                    ->success()
                                                    ->body('About Updated successfully')
                                                    ->send();
                                            })

                                        
                        ]),
                        Section::make('Compliance')
                            ->schema([
                                InfolistView::make('filament.infolists.staff-compilance')
                                    ->columnSpanFull(),
                            ])
                            ->headerActions([

                            InfolistAction::make('manage_all')
                                ->label('MANAGE ALL')
                                ->size('sm')
                                ->url(fn ($record) => route('filament.admin.pages.staff-own-docs', ['user_id' => $record->id]))
                                ->openUrlInNewTab(),

                        ])
                        ])
                        ->columnSpan(2)
                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;']),




                    // Right Side Section (1/3 width).
                      Section::make('')
                        ->schema([
                    Section::make('Login Info')
                        ->schema([
                
                             TextEntry::make('login')
                                        ->label('Login')
                                        ->weight('bold')
                                        ->icon('heroicon-m-user')
                                        ->size('lg'),
                            TextEntry::make('last_login_at')
                                ->label('')
                                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->diffForHumans() : 'Never logged in'),
                           
                        ])->columns(2),

                        Section::make('Settings')
                            ->schema([
                                InfolistView::make('filament.infolists.staff-setting')
                                    ->columnSpanFull(),
                            ])
                            ->headerActions([

                            InfolistAction::make('edit_setting')
                                ->label('EDIT')
                                ->size('sm')
                                ->url(fn ($record) => UserResource::getUrl('edit', ['record' => $record]))
                                ->openUrlInNewTab(),

                            ]),

                           Section::make('Next of Kin')
                                        ->schema([
                                            InfolistView::make('filament.infolists.staff-contacts')
                                                ->columnSpanFull(),
                                        ])
                                        ->headerActions([
                                            InfolistAction::make('edit_next_of_kin')
                                                ->label('EDIT')
                                                ->size('sm')
                                                    ->modalHeading('Next of Kin Details')
                                                ->icon('heroicon-s-user')
                                                ->form(function (Forms\Form $form) {
                                                    return $form
                                                        ->schema([
                                                            Forms\Components\Section::make('Next of Kin')
                                                                ->schema([
                                                                    Forms\Components\TextInput::make('kin_name')
                                                                        ->label('Name')
                                                                        ->maxLength(255),

                                                                    Forms\Components\TextInput::make('kin_relation')
                                                                        ->label('Relation')
                                                                        ->maxLength(255),

                                                                    Forms\Components\TextInput::make('kin_contact')
                                                                        ->label('Contact'),

                                                                    Forms\Components\TextInput::make('kin_email')
                                                                        ->label('Email')
                                                                        ->email()
                                                                        ->maxLength(255),
                                                                ])
                                                                ->columns(2),

                                                            Forms\Components\Checkbox::make('same_as_kin')
                                                                ->label('Same as Next of Kin')
                                                                ->default(false)
                                                                ->dehydrated(true) // ✅ forces the field to save even if false
                                                                ->reactive(),


                                                            Forms\Components\Section::make('Emergency Contact')
                                                                ->schema([
                                                                    Forms\Components\TextInput::make('emergency_contact_name')
                                                                        ->label('Name')
                                                                        ->maxLength(255),
                                                                    Forms\Components\TextInput::make('emergency_contact_relation')
                                                                        ->label('Relation')
                                                                        ->maxLength(255),
                                                                    Forms\Components\TextInput::make('emergency_contact_contact')
                                                                        ->label('Number'),
                                                                    Forms\Components\TextInput::make('emergency_contact_email')
                                                                        ->label('Email')
                                                                        ->email()
                                                                        ->maxLength(255),
                                                                ])
                                                                ->columns(2)
                                                                ->visible(fn ($get) => ! $get('same_as_kin'))
                                                                ->reactive(),
                                                        ]);
                                                })
                                                ->action(function (array $data, $record) {
                                                    $user = Auth::user();

                                                    // Auto-fill emergency fields if "same_as_kin" is checked
                                                    if (!empty($data['same_as_kin'])) {
                                                        $data['emergency_contact_name'] = $data['kin_name'];
                                                        $data['emergency_contact_relation'] = $data['kin_relation'];
                                                        $data['emergency_contact_contact'] = $data['kin_contact'];
                                                        $data['emergency_contact_email'] = $data['kin_email'];
                                                    }

                                                    // Create or Update record for this user
                                                    StaffContact::updateOrCreate(
                                                        ['user_id' => $record->id],
                                                        $data
                                                    );

                                                    Notification::make()
                                                        ->title('Next of Kin details saved successfully!')
                                                        ->success()
                                                        ->send();
                                                })
                                                ->mountUsing(function (Forms\Form $form, $record) {
                                                    $contact = StaffContact::where('user_id', $record->id)->first();

                                                    if ($contact) {
                                                        $form->fill($contact->toArray());
                                                    }
                                                }),
                                            ]),

                                             Section::make('Payroll Settings')
                                                    ->schema([
                                                        InfolistView::make('filament.infolists.staff-payroll')
                                                            ->columnSpanFull(),
                                                    ])
                                                    ->headerActions([
                                                        InfolistAction::make('payroll_setting')
                                                            ->label('EDIT')
                                                            ->size('sm')
                                                            ->icon('heroicon-s-cog')
                                                            ->form(function (Forms\Form $form) {
                                                                $auth = Auth::id(); // logged-in user to fetch pay groups

                                                                $options = PayGroup::where('user_id', $auth)
                                                                    ->where('is_archive', 0)
                                                                    ->pluck('name', 'id')
                                                                    ->toArray();
                                                                return $form
                                                                    ->schema([
                                                                         Forms\Components\Select::make('pay_group_id')
                                                                            ->label('Pay Group')
                                                                            ->options($options)
                                                                            ->searchable()
                                                                            ->preload()
                                                                            ->nullable()
                                                                            ->default(null),
                                                                        Forms\Components\TextInput::make('allowances')
                                                                            ->label('Allowances')
                                                                            ->maxLength(255),

                                                                        Forms\Components\TextInput::make('daily_hours')
                                                                            ->label('Daily Hours')
                                                                            ->numeric()
                                                                            ->maxLength(255),

                                                                        Forms\Components\TextInput::make('weekly_hours')
                                                                            ->label('Weekly Hours')
                                                                            ->numeric()
                                                                            ->maxLength(255),

                                                                        Forms\Components\TextInput::make('external_system_identifier')
                                                                            ->label('External System ID')
                                                                            ->maxLength(255),
                                                                    ])
                                                                    ->columns(2);
                                                            })
                                                            ->action(function (array $data, $record) {
                                                                    if (! $record) {
                                                                        Notification::make()
                                                                            ->title('Error: User not found')
                                                                            ->danger()
                                                                            ->send();
                                                                        return;
                                                                    }

                                                                    // ✅ Make sure pay_group_id key always exists
                                                                    $data['pay_group_id'] = $data['pay_group_id'] ?? null;

                                                                    StaffPayrollSetting::updateOrCreate(
                                                                        ['user_id' => $record->id],
                                                                        $data
                                                                    );

                                                                    Notification::make()
                                                                        ->title('Payroll settings saved successfully!')
                                                                        ->success()
                                                                        ->send();
                                                                })
                                                            ->mountUsing(function (Forms\Form $form, $record) {
                                                                if (! $record) return;

                                                                $existing = StaffPayrollSetting::where('user_id', $record->id)->first();

                                                                if ($existing) {
                                                                    $form->fill($existing->toArray());
                                                                }
                                                            }),
                                                        ]),

                                                         Section::make('Notes')
                                                                    ->schema([
                                                                         InfolistView::make('filament.infolists.staff-private-notes')
                                                                            ->columnSpanFull(),
                                                                    ])
                                                                    ->headerActions([
                                                                        InfolistAction::make('edit_notes')
                                                                            ->label('EDIT')
                                                                            ->size('sm')
                                                                            ->icon('heroicon-s-pencil-square')
                                                                            ->form(function (Forms\Form $form, $record) {
                                                                                return $form
                                                                                    ->schema([
                                                                                        Textarea::make('private_note')
                                                                                            ->label('Private Notes')
                                                                                            ->rows(6)
                                                                                            ->placeholder('Write private notes about this user...')
                                                                                            ->maxLength(1000)
                                                                                    ]);
                                                                            })
                                                                            ->mountUsing(function (Forms\Form $form, $record) {
                                                                                // Pre-fill textarea with existing data
                                                                                if ($record) {
                                                                                    $form->fill([
                                                                                        'private_note' => $record->private_note,
                                                                                    ]);
                                                                                }
                                                                            })
                                                                            ->action(function (array $data, $record) {
                                                                                if (! $record) {
                                                                                    Notification::make()
                                                                                        ->title('Error: User not found.')
                                                                                        ->danger()
                                                                                        ->send();
                                                                                    return;
                                                                                }

                                                                                // Update the user's private_note
                                                                                $record->update([
                                                                                    'private_note' => $data['private_note'],
                                                                                ]);

                                                                                Notification::make()
                                                                                    ->title('Private info updated successfully!')
                                                                                    ->success()
                                                                                    ->send();
                                                                            }),
                                                                        ]),
                                            
                        ])
                        ->columnSpan(1)
                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;']),
                                                    Section::make('Archive Staff')
                                                    ->description('This will archive the staff and you will not able to see staff in your list. If you do wish to access the staff, please go to  Archived menu.')
                                                                ->schema([
                                                                ])
                                                                  ->visible(fn ($record) => $record->id !== Auth::id())
                                                                ->footerActions([
                                                                     InfolistAction::make('user_archive')
                                                                            ->label('Archive')
                                                                            ->color('darkk')
                                                                            ->icon('heroicon-s-archive-box')
                                                                                ->requiresConfirmation() 
                                                                                ->modalHeading('Archive User?') 
                                                                                ->modalDescription('Are you sure you want to archive this user?')
                                                                                ->modalSubmitActionLabel('Yes, Archive') 
                                                                                ->modalCancelActionLabel('Cancel')
                                                                            ->action(function ($record) {
                                                                                    $staffProfile = StaffProfile::where('user_id', $record->id)->first();

                                                                                    if ($staffProfile) {
                                                                                        $staffProfile->is_archive = 'Archive';
                                                                                        $staffProfile->save();

                                                                                    Notification::make()
                                                                                    ->success()
                                                                                    ->title('Success')
                                                                                    ->body('Staff Archived Successfully')
                                                                                    ->send();
                                                                                    }

                                                                                    else{
                                                                                        Notification::make()
                                                                                        ->danger()
                                                                                        ->title('Error')
                                                                                        ->body(' Staff Not Found')
                                                                                        ->send();
                                                                                    }

                                                                                        return redirect()->route('filament.admin.resources.users.index');
                                                                                }),
                                                            ]),
                ]),



        ]);
}


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}/view'),
        ];
    }
}
