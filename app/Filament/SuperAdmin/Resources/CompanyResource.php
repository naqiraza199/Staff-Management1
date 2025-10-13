<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\CompanyResource\Pages;
use App\Filament\SuperAdmin\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;
use App\Models\User;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-s-building-office-2';

    protected static ?string $navigationGroup = 'System Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Info')
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User (Admin only)')
                    ->options(function ($get, $record) {
                        $usedUserIds = \App\Models\Company::pluck('user_id')->toArray();

                        // Get Admin users who do not have a company OR the one assigned to this record
                        return \App\Models\User::role('Admin')
                            ->when($record, function ($query) use ($record, $usedUserIds) {
                                // Allow the current user_id to still appear in edit mode
                                $usedUserIds = array_diff($usedUserIds, [$record->user_id]);
                                return $query->whereNotIn('id', $usedUserIds);
                            }, function ($query) use ($usedUserIds) {
                                // Normal create mode
                                return $query->whereNotIn('id', $usedUserIds);
                            })
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Only Admin users who do not already have a company are shown.')
                    ->columnSpanFull()
                    ->default(fn($record) => $record?->user_id),



                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Company Name')
                    ->maxLength(255),

                Forms\Components\TextInput::make('country')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('company_logo')
                ->required()
                ->columnSpanFull(),
            ])->columns(2),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
            ImageColumn::make('company_logo')
                ->label('Logo')
                ->url(fn ($record) => asset('storage/' . $record->company_logo))
                ->height(50)
                ->width(50)
                ->circular(),
                Tables\Columns\TextColumn::make('company_no')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->label('User Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Company Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country')
                    ->searchable(),
                 Tables\Columns\TextColumn::make('created_at')
                        ->label('Created At')
                        ->dateTime('d M Y') 
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\IconColumn::make('is_subscribed')
                                ->label('Subscribed')
                                ->boolean() 
                                ->trueIcon('heroicon-s-shield-check')   
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
                   Tables\Actions\ViewAction::make()->button()->color('warning')->label('')->iconbutton()->tooltip('View Company'),
                  Tables\Actions\EditAction::make()->button()->color('stripe')->label('')->iconbutton()->tooltip('Edit Company'),
                  Tables\Actions\DeleteAction::make()->button()->color('danger')->label('')->iconbutton()->tooltip('Delete Company'),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
