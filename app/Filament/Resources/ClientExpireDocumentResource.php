<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientExpireDocumentResource\Pages;
use App\Filament\Resources\ClientExpireDocumentResource\RelationManagers;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class ClientExpireDocumentResource extends Resource
{
    protected static ?string $model = Document::class;

  protected static ?string $navigationIcon = 'heroicon-s-clipboard-document';

                 public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('see-client-expire-documents');
        }
      protected static ?string $navigationGroup = 'Client Management';

          public static function getModelLabel(): string
    {
        return 'Expired Documents';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Expired Documents';
    }

    protected static ?int $navigationSort = 5;

              public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->where('user_id', auth()->id())
        ->whereDate('expired_at', '<', Carbon::now())
        ->whereNotNull('client_id');
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
                 Tables\Columns\TextColumn::make('client.display_name')
                    ->label('Client Name')
                    ->searchable(),
                 Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->icon('heroicon-s-document-text')
                    ->colors([
                        'primary' => 'PDF',       // Blue
                         'brown' => 'DOC',         // #8f6232
                        'lightgreen' => 'DOCX',   // #86de28
                        'yee' => 'XLS',           // #f5dd02
                        'stripe' => 'XLSX',         // #008000
                        'darkk' => 'TXT',         // #BE3144
                    ])
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('documentCategory.name')
                    ->label('Category')
                    ->searchable(),

                Tables\Columns\TextColumn::make('expired_at')
                    ->label('Expired At')
                    ->date('d/m/Y')
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListClientExpireDocuments::route('/'),
        ];
    }
}
