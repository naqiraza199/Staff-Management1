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
use Illuminate\Support\Facades\Cache;





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

            $staffUserIds = StaffProfile::where('company_id', $companyId)->where('is_archive','Unarchive')->pluck('user_id');

            return User::with(['staffProfile'])
                ->select(['id', 'name', 'email', 'created_at', 'updated_at'])
                ->whereIn('id', $staffUserIds)
                ->role('staff');
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
    Forms\Components\Grid::make(['default' => 3])
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
                            ]),
                            ]),

                                    Forms\Components\Fieldset::make('Address')
                                    ->schema([
                        Forms\Components\Textarea::make('address')->label('')->placeholder('Enter Address')->columnSpanFull(),
                            
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
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
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
                  Action::make('Archive')->button()->color('darkk')->icon('heroicon-s-archive-box')->label('')->iconbutton()->tooltip('Goes To Archive')
               ->action(function ($record) {
                         $staffProfile = StaffProfile::where('user_id', $record->id)->first();

                        if ($staffProfile) {
                            $staffProfile->is_archive = 'Archive';
                            $staffProfile->save();

                        Notification::make()
                        ->success()
                        ->title('Success')
                        ->body('Staff Deleted Successfully')
                        ->send();
                        }

                        else{
                            Notification::make()
                            ->error()
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
                                            Forms\Components\TextArea::make('about')
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
                
                             TextEntry::make('dstatus')
                                        ->label('Status')
                                        ->weight('bold')
                                        ->icon('heroicon-m-user')
                                        ->size('lg'),
                            TextEntry::make('status')
                                ->label('')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'Active' => 'info',
                                    'Awaiting Response', 'Pending Review' => 'warning',
                                    'No access' => 'danger',
                                    default => 'gray',
                                }),

                            TextEntry::make('drole')
                                        ->label('Role')
                                        ->weight('bold')
                                        ->icon('heroicon-m-user')
                                        ->size('lg'),
                            TextEntry::make('role')
                                ->label('')
                                ->badge()
                                ->default(fn ($record) => $record->getRoleNames()->first() ?? 'No role'),

                            TextEntry::make('dteam')
                                        ->label('Teams')
                                        ->weight('bold')
                                        ->icon('heroicon-m-user')
                                        ->size('lg'),

                                    
                                        RepeatableEntry::make('teams')
                                        ->label('')
                                        ->schema([
                                            TextEntry::make('name')
                                                ->badge()
                                                ->label('')
                                                ->color('info'),
                                        ])
                                        ->visible(fn ($record) => $record->teams->count() > 0)
                                        ->default(fn ($record) => $record->teams->map(fn ($team) => ['name' => $team->name])->toArray()),

                                TextEntry::make('djtype')
                                        ->label('Job Title')
                                        ->weight('bold')
                                        ->icon('heroicon-m-user')
                                        ->size('lg'),

                            TextEntry::make('job_type')
                                ->label('')
                                ->badge()
                                ->default(fn ($record) => $record->job_type ?? '-')

                           
                        ])->columns(2)
                        ])
                        ->columnSpan(1)
                    ->extraAttributes(['style' => 'background: transparent; border: none; box-shadow: none;']),



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
