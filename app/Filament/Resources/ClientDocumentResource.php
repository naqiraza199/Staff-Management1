<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientDocumentResource\Pages;
use App\Filament\Resources\ClientDocumentResource\RelationManagers;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use App\Models\Client;



class ClientDocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static ?string $navigationGroup = 'Client Management';

    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-check';

    protected static ?int $navigationSort = 4;

                  public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->where('user_id', auth()->id())
        ->whereDate('expired_at', '>', Carbon::now())
        ->whereNotNull('client_id');
}

        

              public static function getModelLabel(): string
    {
        return 'Shared Documents';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Shared Documents';
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

            Tables\Columns\TextColumn::make('created_at')
                            ->label('Last Update')
                            ->since()
                            ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
             ->headerActions([ // ✅ use this instead of ->actions()
            Tables\Actions\Action::make('Upload Document')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Grid::make(12)
                        ->schema([
                             Select::make('client_id')
                                    ->label('Select Client')
                                            ->options(
                                                Client::where('user_id', Auth::id())
                                                    ->pluck('display_name', 'id') 
                                            )
                                    ->required()
                                    ->columnSpan(12)
                                    ->native(false),

                            Select::make('document_category_id')
                                ->relationship('documentCategory', 'name')
                                ->label('Document Category')
                                ->required()
                                ->columnSpan(6),

                            DatePicker::make('expired_at')
                                ->label('Expires At')
                                ->required()
                                ->columnSpan(6),
                        ]),
                    Forms\Components\FileUpload::make('file')
                        ->label('Upload Document')
                        ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/plain'])
                        ->helperText('Accepted file types: PDF, DOC, DOCX, XLS, XLSX, TXT')
                        ->required()
                        ->directory('documents')
                        ->preserveFilenames()
                        ->disk('public')
                        ->maxSize(2048),
                ])
                ->action(function (array $data): void {
                    $file = $data['file'];
                    $doCategory = $data['document_category_id'];
                    $expires = $data['expired_at'];
                    $extension = strtoupper(pathinfo($file, PATHINFO_EXTENSION));

                    \App\Models\Document::create([
                        'user_id' => auth()->id(),
                        'client_id' => $data['client_id'],
                        'name' => $file,
                        'type' => $extension,
                        'document_category_id' => $doCategory,
                        'expired_at' => $expires,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Document uploaded successfully')
                        ->success()
                        ->send();
                })
                ->modalHeading('Upload New Document')
                ->modalSubmitActionLabel('Upload')
                ->color('primary'),
        ])
            ->actions([

           Action::make('View')
                ->icon('heroicon-s-eye')
                ->iconButton()
                ->tooltip('View Document')
                ->color('warning')
                ->modalHeading('Document Preview')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(function ($record) {
                    $filePath = $record->name;
                    $fileName = basename($filePath);
                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $fileUrl = asset('storage/' . $filePath);

                    if (!Storage::disk('public')->exists($filePath)) {
                        return view('filament.components.document-preview', [
                            'error' => 'File not found',
                            'fileName' => $fileName
                        ]);
                    }

                    // Convert Office file to PDF if needed
                    if (in_array($fileExtension, ['doc', 'docx', 'xls', 'xlsx'])) {
                        $convertedPath = convertOfficeToPdf($filePath);

                        if ($convertedPath) {
                            $fileUrl = asset('storage/' . $convertedPath);
                            $fileExtension = 'pdf';
                        } else {
                            return view('filament.components.document-preview', [
                                'error' => 'File conversion failed',
                                'fileName' => $fileName,
                            ]);
                        }
                    }

                    return view('filament.components.document-preview', [
                        'fileUrl' => $fileUrl,
                        'fileName' => $fileName,
                        'fileExtension' => $fileExtension,
                        'filePath' => $filePath
                    ]);
                }),

                
              Tables\Actions\Action::make('edit')
                ->icon('heroicon-s-pencil-square')
                ->label('')
                ->iconButton()
                ->tooltip('Edit Document')
                ->modalHeading('Edit Document')
                ->color('stripe')
                ->form(function (\Filament\Tables\Actions\Action $action): array {
                    /** @var \App\Models\Document $record */
                    $record = $action->getRecord();

                    return [
                          Grid::make(12)
                        ->schema([
                                                         Select::make('client_id')
                                    ->label('Select Client')
                                            ->options(
                                                Client::where('user_id', Auth::id())
                                                    ->pluck('display_name', 'id') 
                                            )
                                    ->required()
                                    ->default($record->client_id)
                                    ->columnSpan(12)
                                    ->native(false),
                            Select::make('document_category_id')
                                ->relationship('documentCategory', 'name')
                                ->label('Document Category')
                                ->required()
                                ->default($record->document_category_id)
                                ->columnSpan(6),

                            DatePicker::make('expired_at')
                                ->label('Expires At')
                                ->required()
                                ->default($record->expired_at)
                                ->columnSpan(6),
                        ]),
                        Forms\Components\FileUpload::make('name')
                            ->label('Replace Document')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/plain'])
                            ->directory('documents')
                            ->preserveFilenames()
                            ->disk('public')
                            ->helperText('Accepted file types: PDF, DOC, DOCX, XLS, XLSX, TXT')
                            ->maxSize(2048)
                            ->default($record->name)
                            ->required(),
                    ];
                })
                ->action(function (array $data, $record): void {
                    $extension = strtoupper(pathinfo($data['name'], PATHINFO_EXTENSION));

                    $record->update([
                        'name' => $data['name'],
                        'client_id' => $data['client_id'],
                        'document_category_id' => $data['document_category_id'],
                        'expired_at' => $data['expired_at'],
                        'type' => $extension,
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Document updated successfully')
                        ->success()
                        ->send();
                }),
                Tables\Actions\DeleteAction::make()->button()->color('danger')->label('')->tooltip('Delete Document')
                ->iconButton()
                ,

                Action::make('Download')
                    ->icon('heroicon-s-cloud-arrow-down')
                    ->label('')
                    ->iconButton()
                    ->tooltip('Download Document')
                    ->color('rado')
                    ->action(function ($record): StreamedResponse {
                        $filePath = $record->name; // 'documents/my.pdf'
                        $fileName = basename($filePath);

                        return response()->streamDownload(function () use ($filePath) {
                            echo Storage::disk('public')->get($filePath);
                        }, $fileName);
                    })


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
            'index' => Pages\ListClientDocuments::route('/'),
            // 'create' => Pages\CreateClientDocument::route('/create'),
            // 'edit' => Pages\EditClientDocument::route('/{record}/edit'),
        ];
    }
}
