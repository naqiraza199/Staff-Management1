<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\Pages;
use App\Filament\SuperAdmin\Resources\SubscriptionPlanResource\RelationManagers;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';

    protected static ?string $navigationGroup = 'Subscriptions';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Create Plan')
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(10)
                    ->default('usd'),
                Forms\Components\Select::make('status')
                    ->options([
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ])
                    ->required(),
                Forms\Components\Select::make('duration')
                ->options([
                    'Weekly' => 'Weekly',
                    'Monthly' => 'Monthly',
                    'Yearly' => 'Yearly',
                ])
                ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
            ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                            ->formatStateUsing(fn ($state) => strtoupper($state)) // 👈 makes it uppercase
                            ->badge()
                            ->color('info')
                            ->icon('heroicon-s-currency-dollar')
                            ->label('Currency')
                            ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                        ->label('Status')
                        ->colors([
                            'success' => 'Active',
                            'danger' => 'Inactive',
                        ])
                        ->icons([
                            'heroicon-s-check-circle' => 'Active',
                            'heroicon-s-x-circle' => 'Inactive',
                        ])
                        ->sortable(),
                    Tables\Columns\TextColumn::make('duration')
                                        ->searchable(),
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
                Tables\Actions\ViewAction::make()->button()->color('warning')->label('')->iconbutton()->tooltip('View Plan'),
                  Tables\Actions\EditAction::make()->button()->color('stripe')->label('')->iconbutton()->tooltip('Edit Plan'),
                  Tables\Actions\DeleteAction::make()->button()->color('danger')->label('')->iconbutton()->tooltip('Delete Plan'),
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
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
