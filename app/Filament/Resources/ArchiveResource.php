<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArchiveResource\Pages;
use App\Filament\Resources\ArchiveResource\RelationManagers;
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
use App\Models\StaffProfile;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;


class ArchiveResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-archive-box-arrow-down';

    protected static ?int $navigationSort = 2;

        protected static ?string $navigationGroup = 'Staff Management';

                public static function getEloquentQuery(): Builder
        {
            $authUser = Auth::user();

            $companyId = Company::where('user_id', $authUser->id)->value('id');

            if (! $companyId) {
                return User::whereRaw('0 = 1');
            }

            $staffUserIds = StaffProfile::where('company_id', $companyId)->where('is_archive','Archive')->pluck('user_id');

            return User::whereIn('id', $staffUserIds)->role('staff');
        }
 
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
                  Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                          Tables\Columns\TextColumn::make('staffProfile.mobile_number')
                          ->label('Mobile')
                    ->searchable(),
                             Tables\Columns\TextColumn::make('staffProfile.address')
                          ->label('Address')
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
                Action::make('Unarchive')->button()->color('brown')->icon('heroicon-s-archive-box')
               ->action(function ($record) {
                         $staffProfile = StaffProfile::where('user_id', $record->id)->first();

                        if ($staffProfile) {
                            $staffProfile->is_archive = 'Unarchive';
                            $staffProfile->save();

                        Notification::make()
                        ->success()
                        ->title('Unarchive')
                        ->body('Staff Unarchive Successfully')
                        ->send();
                        }

                        else{
                            Notification::make()
                            ->error()
                            ->title('Error')
                            ->body('Staff Not Found')
                            ->send();
                        }

     
                    }),
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
            'index' => Pages\ListArchives::route('/'),
        ];
    }
}
