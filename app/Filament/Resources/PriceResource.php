<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PriceResource\Pages;
use App\Filament\Resources\PriceResource\RelationManagers;
use App\Models\Price;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Imports\PriceImporter;
use Filament\Tables\Actions\ImportAction;
use Filament\Facades\Filament;

class PriceResource extends Resource
{
    protected static ?string $model = Price::class;

    protected static ?string $navigationIcon = 'heroicon-s-banknotes';
    protected static ?string $navigationGroup = 'Account';

   public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('manage-prices');
        }
    
    public static function canCreate(): bool
    {
        return false;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('support_item_number')
                    ->maxLength(50)
                    ->default(null),
                Forms\Components\TextInput::make('support_item_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('registration_group_number')
                    ->maxLength(50)
                    ->default(null),
                Forms\Components\TextInput::make('registration_group_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('support_category_name')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('support_category_number')
                    ->maxLength(50)
                    ->default(null),
                Forms\Components\TextInput::make('support_category_number_pace')
                    ->maxLength(50)
                    ->default(null),
                Forms\Components\TextInput::make('support_category_name_pace')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('unit')
                    ->maxLength(10)
                    ->default(null),
                Forms\Components\TextInput::make('quote')
                    ->maxLength(10)
                    ->default(null),
                Forms\Components\TextInput::make('start_date')
                    ->maxLength(20)
                    ->default(null),
                Forms\Components\TextInput::make('end_date')
                    ->maxLength(20)
                    ->default(null),
                Forms\Components\TextInput::make('act')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('nsw')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('nt')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('qld')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('sa')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('tas')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('vic')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('wa')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('remote')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('very_remote')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('non_face_to_face_support_provision')
                    ->maxLength(10)
                    ->default(null),
                Forms\Components\TextInput::make('provider_travel')
                    ->maxLength(10)
                    ->default(null),
                Forms\Components\TextInput::make('short_notice_cancellations')
                    ->maxLength(10)
                    ->default(null),
                Forms\Components\TextInput::make('NDIA_requested_reports')
                    ->maxLength(10)
                    ->default(null),
                Forms\Components\TextInput::make('irregular_SIL_supports')
                    ->maxLength(10)
                    ->default(null),
                Forms\Components\TextInput::make('type')
                    ->maxLength(255)
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\TextColumn::make('support_item_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('support_item_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_group_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('registration_group_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('support_category_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('support_category_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('support_category_number_pace')
                    ->searchable(),
                Tables\Columns\TextColumn::make('support_category_name_pace')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quote')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('act')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nsw')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nt')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qld')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vic')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remote')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('very_remote')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('non_face_to_face_support_provision')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provider_travel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('short_notice_cancellations')
                    ->searchable(),
                Tables\Columns\TextColumn::make('NDIA_requested_reports')
                    ->searchable(),
                Tables\Columns\TextColumn::make('irregular_SIL_supports')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
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
                    ->headerActions([
            ImportAction::make()
                ->importer(PriceImporter::class)
                ->color('info')
        ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPrices::route('/'),
            'create' => Pages\CreatePrice::route('/create'),
            'edit' => Pages\EditPrice::route('/{record}/edit'),
        ];
    }
}
