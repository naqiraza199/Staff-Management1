<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentExpireResource\Pages;
use App\Filament\Resources\DocumentExpireResource\RelationManagers;
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


class DocumentExpireResource extends Resource
{
   protected static ?string $model = Document::class;

  protected static ?string $navigationIcon = 'heroicon-s-clipboard-document';

      protected static ?string $navigationGroup = 'Staff Management';
      
             public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('see-staff-expire-documents');
        }

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
            $authUser = auth()->user();

            $companyId = \App\Models\Company::where('user_id', $authUser->id)->value('id');

            if (! $companyId) {
                return parent::getEloquentQuery()->whereRaw('1 = 0'); // returns no results
            }

            $staffUserIds = \App\Models\StaffProfile::where('company_id', $companyId)
                ->where('is_archive', 'Unarchive')
                ->pluck('user_id');

            return parent::getEloquentQuery()
                ->with(['user:id,name', 'documentCategory:id,name'])
                ->select(['id','user_id','document_category_id','type','name','expired_at','created_at','updated_at'])
                ->whereIn('user_id', $staffUserIds) // restrict to staff users
                ->whereDate('expired_at', '<', \Carbon\Carbon::now())
                ->whereNull('client_id');
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
            ->deferLoading()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->columns([

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff')
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



                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListDocumentExpires::route('/'),
        ];
    }
}
