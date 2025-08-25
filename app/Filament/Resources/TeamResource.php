<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamResource\Pages;
use App\Filament\Resources\TeamResource\RelationManagers;
use App\Models\Team;
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
use App\Models\User;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationGroup = 'Staff Management';

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?int $navigationSort = 3;

public static function getEloquentQuery(): Builder
{
    $authUser = Auth::user();

    return Team::query()->withCount('assignees')->with('assignees:id,name')
        ->where('user_id', $authUser->id);
}


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                       Forms\Components\Hidden::make('user_id')
                       ->default(User::where('id',Auth::id())->value('id')),
                // Forms\Components\TextInput::make('status')
                //     ->required(),


                 Forms\Components\Select::make('assignees')
                ->label('Select Staff')
                ->multiple()
                ->searchable()
                ->options(function () {
                    $authUser = Auth::user();

                    $companyId = \App\Models\Company::where('user_id', $authUser->id)->value('id');

                    if (! $companyId) {
                        return [];
                    }

                    $staffUserIds = \App\Models\StaffProfile::where('company_id', $companyId)
                        ->where('is_archive', 'Unarchive')
                        ->pluck('user_id');

                    return \App\Models\User::whereIn('id', $staffUserIds)
                        ->role('staff')
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->required()



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
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('assignees_count')
                    ->label('Count')
                    ->counts('assignees')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('assignees.name')
                    ->label('Staff')
                    ->badge()
                    ->color('success')
                    ->separator(','),
                
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
                Tables\Actions\EditAction::make()->button()->color('stripe')->label('')->iconbutton()->tooltip('Edit Team'),
                Tables\Actions\DeleteAction::make()->button()->color('darkk')->label('')->iconbutton()->tooltip('Delete Team'),
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
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
        ];
    }
}
