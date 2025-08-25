<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AllowanceResource\Pages;
use App\Filament\Resources\AllowanceResource\RelationManagers;
use App\Models\Allowance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;


class AllowanceResource extends Resource
{
    protected static ?string $model = Allowance::class;

    protected static ?string $navigationIcon = 'heroicon-s-adjustments-horizontal';

    protected static ?string $navigationGroup = 'Pay Items';

    public static function getEloquentQuery(): Builder
    {
        $authUser = Auth::user();

        return Allowance::where('user_id', $authUser->id)->where('status', 1);
    }

    public static function form(Form $form): Form
    {
        return $form
                    ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('type')
                    ->options([
                        'Expense' => 'Expense',
                        'Mileage/Travel' => 'Mileage/Travel',
                        'Override payitems' => 'Override payitems',
                        'Override hours' => 'Override hours',
                        'One-off' => 'One-off',
                        'Permanent' => 'Permanent',
                        'Sleepover' => 'Sleepover',
                    ])
                    ->required()
                    ->live(), // 👈 makes it reactive

                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric()
                    ->default('N/A')
                    ->hidden(fn (callable $get) => !in_array($get('type'), ['Override hours', 'One-off', 'Permanent'])) // disable for other types
                    ->dehydrated(fn (callable $get) => in_array($get('type'), ['Override hours', 'One-off', 'Permanent'])), // only save to DB if relevant
            ])->columns(3);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => is_numeric($state) ? '$' . number_format($state, 2) : $state),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAllowances::route('/'),
            // 'create' => Pages\CreateAllowance::route('/create'),
            // 'edit' => Pages\EditAllowance::route('/{record}/edit'),
        ];
    }
}
