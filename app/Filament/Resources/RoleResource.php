<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Facades\Filament;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

     protected static ?string $navigationIcon = 'heroicon-s-shield-check';
    protected static ?string $navigationGroup = 'Security Management';

    protected static ?int $navigationSort = 8;


       public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('roles-access');
        }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
    ->schema([
        TextInput::make('name')->required()->unique(ignoreRecord: true),
        Select::make('permissions')
            ->multiple()
            ->relationship('permissions', 'name')
            ->preload(),
        // ...
    ])->columns(2)
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->columns([

                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions Count'),

                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->button()->color('warning')->label('')->iconbutton()->tooltip('View Role'),
                Tables\Actions\EditAction::make()->button()->color('stripe')->label('')->iconbutton()->tooltip('Edit Role')->slideOver(),
                Tables\Actions\DeleteAction::make()->button()->color('darkk')->label('')->iconbutton()->tooltip('Delete Role'),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            // 'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
