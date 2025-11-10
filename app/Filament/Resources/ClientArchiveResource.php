<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientArchiveResource\Pages;
use App\Filament\Resources\ClientArchiveResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;


class ClientArchiveResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';

     protected static ?string $navigationGroup = 'Client Management';

                   public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('manage-archive-clients');
        }

    protected static ?int $navigationSort = 3;


                public static function canCreate(): bool
    {
        return false;
    }

        public static function getModelLabel(): string
    {
        return 'Archive';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Archive';
    }

        public static function getEloquentQuery(): Builder
    {
        $authUser = Auth::user();

        return Client::where('user_id', $authUser->id)->where('is_archive', 'Archive');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 Tables\Columns\TextColumn::make('display_name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('Unarchive')->button()->color('brown')->icon('heroicon-s-archive-box')
               ->action(function ($record) {

                            $record->is_archive = 'Unarchive';
                            $record->save();

                        Notification::make()
                        ->success()
                        ->title('Unarchive')
                        ->body('Client Unarchive Successfully')
                        ->send();
     
                    }),
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
            'index' => Pages\ListClientArchives::route('/'),
        ];
    }
}
