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
use Filament\Facades\Filament;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $navigationGroup = 'Staff Management';

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?int $navigationSort = 3;

    
                public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('manage-teams');
        }

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

        // ✅ Fetch staff under this company
        $staffUserIds = \App\Models\StaffProfile::where('company_id', $companyId)
            ->where('is_archive', 'Unarchive')
            ->pluck('user_id')
            ->toArray();

        // ✅ Include logged-in user
        if (!in_array($authUser->id, $staffUserIds)) {
            $staffUserIds[] = $authUser->id;
        }

        // ✅ Return names keyed by ID
        return \App\Models\User::whereIn('id', $staffUserIds)
            ->pluck('name', 'id')
            ->toArray();
    })
    ->required(),

Forms\Components\Select::make('clients')
    ->label('Select Client')
    ->multiple()
    ->searchable()
    ->options(function () {
        $authUser = auth()->user();

        if (!$authUser) return [];

        $companyId = \App\Models\Company::where('user_id', $authUser->id)->value('id');

        return \App\Models\Client::where('user_id', $authUser->id)
            ->pluck('display_name', 'id')
            ->toArray();
    })



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

    Tables\Columns\TextColumn::make('clients')
    ->label('Clients')
    ->badge()
    ->color('info')
    ->formatStateUsing(function ($state) {
        if (blank($state)) return '-';

        // Normalize $state to an array of IDs
        if (is_string($state)) {
            $ids = json_decode($state, true);
            $ids = is_array($ids) ? $ids : [$state];
        } elseif (is_int($state)) {
            $ids = [$state];
        } elseif (is_array($state)) {
            $ids = $state;
        } else {
            return '-';
        }

        // Fetch and join client names
        $names = \App\Models\Client::whereIn('id', $ids)
            ->pluck('display_name')
            ->toArray();

        return implode(', ', $names);
    })
    ->limit(50),

                
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
