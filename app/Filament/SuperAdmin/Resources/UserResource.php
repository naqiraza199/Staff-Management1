<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\UserResource\Pages;
use App\Filament\SuperAdmin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\StaffProfile;
use App\Models\Company;

class UserResource extends Resource

{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'System Management';

    protected static ?string $navigationIcon = 'heroicon-s-user';


        public static function getEloquentQuery(): Builder
{
    $superAdminRoleId = Role::where('name', 'superadmin')->value('id');
    $adminRoleId = Role::where('name', 'Admin')->value('id');

    return parent::getEloquentQuery()
        ->with('roles')
        ->leftJoin('model_has_roles as mhr', function ($join) {
            $join->on('users.id', '=', 'mhr.model_id')
                ->where('mhr.model_type', '=', User::class);
        })
        ->whereIn('mhr.role_id', [$superAdminRoleId, $adminRoleId]) // ✅ show only these roles
        ->orderByRaw("
            CASE 
                WHEN mhr.role_id = ? THEN 0
                WHEN mhr.role_id = ? THEN 1
                ELSE 2
            END
        ", [$superAdminRoleId, $adminRoleId])
        ->select('users.*')
        ->distinct(); // ✅ ensures no duplicate rows if multiple roles exist
}
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                  Forms\Components\Section::make('User Detail')
                    ->schema([
                        Forms\Components\Grid::make(['default' => 3])
                            ->schema([
                         
                                Forms\Components\Fieldset::make('Info')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')->label('Name')->placeholder('Enter Name')->required(),
                                        Forms\Components\TextInput::make('email')->email()->label('Email Address')->placeholder('Enter Email')->required(),
                                       Forms\Components\TextInput::make('password')
                                            ->label('Password')
                                            ->password()
                                            ->maxLength(255)
                                            ->placeholder('Enter Password')
                                            ->dehydrated(fn ($state) => filled($state)) 
                                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateUser),

                                    ])
                                    ->columnSpan(3)
                                    ->columns(3),
                            ]),
                                  

                               Forms\Components\Fieldset::make('Role Info')
                                    ->schema([
    Forms\Components\Grid::make(['default' => 2])
                            ->schema([
                           
                              
            Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options(Role::pluck('name', 'name')) // Use name for Spatie
                            ->searchable()
                            ->preload()
                            ->default(fn($record) => $record?->roles?->pluck('name')->first()) // show existing
                            ->afterStateHydrated(function ($component, $record) {
                                if ($record) {
                                    $component->state($record->roles->pluck('name')->first());
                                }
                            })
                            ->afterStateUpdated(function ($state, $record) {
                                if ($record && $state) {
                                    $record->syncRoles([$state]);
                                }
                            })
                            ->helperText('Select a role for this user.'),
                            ]),
                            ]),

                                      Forms\Components\Fieldset::make('Other Info')
                                    ->schema([
    Forms\Components\Grid::make(['default' => 3])
                            ->schema([
                                        Forms\Components\TextInput::make('contact_number')->label('Contact Number')->placeholder('Enter Contact Number'),
                                        Forms\Components\TextInput::make('country')->label('Enter Country')->placeholder('Enter Country'),
                                        Forms\Components\Select::make('status')
                                        ->label('Status')
                                        ->options([
                                            'Active' => 'Active',
                                            'Awaiting Response' => 'Awaiting Response',
                                            'Pending Review' => 'Pending Review',
                                            'No access' => 'No access',
                                        ])
                                        ->searchable()
                                        ->preload()
                                        ->helperText('Select the current status of the user.'),
                            ]),
                            ]),


                                        Forms\Components\Fieldset::make('Profile Picture')
                                    ->schema([
                        Forms\Components\FileUpload::make('image')->label('')->placeholder('Profile Picture')->columnSpanFull(),
                                    ]),




            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table  ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('contact_number')
                        ->label('Contact Number')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                        Tables\Columns\TextColumn::make('roles')
                                ->label('Role')
                                ->badge()
                                ->formatStateUsing(fn ($state, $record) => ucfirst($record->getRoleNames()->first() ?? 'No Role'))
                                ->icon(fn ($record) => match ($record->getRoleNames()->first()) {
                                    'superadmin' => 'heroicon-s-globe-alt',
                                    'Admin' => 'heroicon-s-user',
                                    default => 'heroicon-s-tag',
                                })
                                ->color(fn ($record) => match ($record->getRoleNames()->first()) {
                                    'superadmin' => 'success',
                                    'Admin' => 'info',
                                    'editor' => 'warning',
                                    default => 'ngree',
                                }),



                Tables\Columns\TextColumn::make('created_at')
                        ->label('Created At')
                        ->dateTime('d M Y') 
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('country')
                        ->label('Country')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),

                        Tables\Columns\TextColumn::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->icons([
                                       'heroicon-s-check-circle' => 'Active',
                                       'heroicon-s-clock' => 'Awaiting Response',
                                       'heroicon-s-eye' => 'Pending Review',
                                       'heroicon-s-x-circle' => 'No access',
                                    ])
                                    ->colors([
                                        'rado' => 'Active',
                                        'warning' => 'Awaiting Response',
                                        'info' => 'Pending Review',
                                        'danger' => 'No access',
                                    ])
                                    ->toggleable(isToggledHiddenByDefault: false),

                                     Tables\Columns\IconColumn::make('has_company')
                                        ->label('Has Company')
                                        ->boolean() 
                                        ->getStateUsing(function ($record) {
                                            return Company::where('user_id', $record->id)->exists();
                                        })
                                        ->trueIcon('heroicon-s-identification')   
                                        ->falseIcon('heroicon-s-x-circle')         
                                        ->trueColor('success')
                                        ->falseColor('danger'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                  Tables\Actions\ViewAction::make()->button()->color('warning')->label('')->iconbutton()->tooltip('View User'),
                  Tables\Actions\EditAction::make()->button()->color('stripe')->label('')->iconbutton()->tooltip('Edit User'),
                  Tables\Actions\DeleteAction::make()->button()->color('danger')->label('')->iconbutton()->tooltip('Delete User'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
        ];
    }
}
