<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use App\Models\DocumentCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentSignatureRequest;
use App\Models\Company;
use Filament\Forms\Components\Textarea;


class ClientOwnDocs extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.client-own-docs';

        protected static ?string $title = null;
        public ?int $clientId = null;


        public function getTitle(): string
        {
            $clientId = request()->query('client_id');

            if ($clientId) {
                $client = \App\Models\Client::find($clientId);

                if ($client) {
                    return "{$client->display_name} Documents";
                }
            }

            return 'Client Documents';
        }

    
       public static function shouldRegisterNavigation(): bool
        {
            return false;
        }

         public static function getRoutePath(): string
        {
            return '/client-own-docs';
        }


        public function mount(): void
        {
            $this->clientId = request()->query('client_id');
        }

          public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder =>
                Document::query()->where('client_id', $this->clientId)
            )
            ->columns([
                // Tables\Columns\TextColumn::make('user.name')->label('Name')->searchable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->icon('heroicon-s-document-text')
                    ->colors([
                        'primary' => 'PDF',
                        'brown' => 'DOC',
                        'lightgreen' => 'DOCX',
                        'yee' => 'XLS',
                        'stripe' => 'XLSX',
                        'darkk' => 'TXT',
                    ])
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->sortable(),

                // Tables\Columns\TextColumn::make('client.display_name')->label('name')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Document')->searchable(),
                Tables\Columns\TextColumn::make('documentCategory.name')->label('Category')->searchable(),
                Tables\Columns\TextColumn::make('expired_at')->label('Expired At')->date('d/m/Y')->searchable(),
                Tables\Columns\IconColumn::make('no_expiration')->boolean()->label('No Expiration')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Last Update')->since()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_verified')
                        ->label('Signature')
                        ->boolean() 
                        ->trueIcon('heroicon-s-document-check') 
                        ->falseIcon(null), 
            ])
            ->headerActions([ // ✅ use this instead of ->actions()
            Tables\Actions\Action::make('Upload Document')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Grid::make(12)
                        ->schema([
                             Forms\Components\Hidden::make('client_id')
                                    ->default(fn ($livewire) => $livewire->clientId),

                                         Forms\Components\Checkbox::make('no_expiration')
                                            ->label('No Expiration')
                                            ->reactive() // 👈 important so Filament listens for changes
                                            ->columnSpan(12),

                            Forms\Components\Select::make('document_category_id')
                            ->label('Document Category')
                            ->options(function () {
                                $user = Auth::user();
                                $companyId = Company::where('user_id', $user->id)->value('id');

                                return DocumentCategory::where('is_staff_doc', '!=', 1)
                                    ->where('company_id', $companyId)
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(6),

                            DatePicker::make('expired_at')
                                ->label('Expires At')
                                 ->required(fn (callable $get) => ! $get('no_expiration')) 
                                ->hidden(fn (callable $get) => $get('no_expiration'))
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

                        Textarea::make('details')
                                ->label('Content')
                                ->rows(5)
                                ->placeholder('Enter Content Here'),
                ])
                ->action(function (array $data): void {
                    $file = $data['file'];
                    $doCategory = $data['document_category_id'];
                    $expires = $data['expired_at'] ?? null;
                    $extension = strtoupper(pathinfo($file, PATHINFO_EXTENSION));

                   $clientDoc = \App\Models\Document::create([
                        'user_id' => auth()->id(),
                        'client_id' => $data['client_id'],
                        'name' => $file,
                        'type' => $extension,
                        'document_category_id' => $doCategory,
                        'no_expiration'        => $data['no_expiration'] ?? 0,
                        'expired_at' => $expires,
                        'signature_token'      => Str::uuid(),
                        'details' => $data['details'],
                            ]);

                            Mail::to($clientDoc->client->email)->send(new DocumentSignatureRequest($clientDoc));

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

                 ActionGroup::make([

                     Action::make('viewSignature')
                 ->label('Verfied')
                 ->color('lightgreen')
                ->tooltip('View Signature')
                ->icon('heroicon-s-check-badge')
                ->modalHeading('Client Signature')
                ->modalContent(fn ($record) => view('documents.signature-modal', [
                    'record' => $record,
                ]))
                ->modalSubmitAction(false) 
                ->visible(fn ($record) => $record->is_verified),
  Action::make('View')
                ->icon('heroicon-s-eye')
                ->label('View')
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
                ->hidden(fn ($record) => $record->is_verified)
                ->icon('heroicon-s-pencil-square')
                ->label('Edit')
                ->modalHeading('Edit Document')
                ->color('stripe')
                ->form(function (\Filament\Tables\Actions\Action $action): array {
                    /** @var \App\Models\Document $record */
                    $record = $action->getRecord();

                    return [
                          Grid::make(12)
                        ->schema([
                                     

                                    
                            Forms\Components\Checkbox::make('no_expiration')
                            ->label('No Expiration')
                            ->reactive()
                            ->default(fn ($record) => $record?->no_expiration ?? false)
                            ->columnSpan(12),

                        Select::make('document_category_id')
                                        ->label('Document Category')
                                        ->required()
                                        ->default(fn ($record) => $record?->document_category_id)
                                        ->columnSpan(6)
                                        ->searchable()
                                          ->options(function () {
                                                $user = Auth::user();
                                                $companyId = Company::where('user_id', $user->id)->value('id');

                                                return DocumentCategory::query()
                                                    ->where('is_staff_doc', '!=', 1) // ✅ exclude staff docs
                                                    ->where('company_id', $companyId)
                                                    ->pluck('name', 'id')
                                                    ->toArray();
                                            }),



                            DatePicker::make('expired_at')
                                ->label('Expires At')
                                ->default($record->expired_at)
                                 ->required(fn (callable $get) => ! $get('no_expiration')) 
                                ->hidden(fn (callable $get) => $get('no_expiration'))
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

                            Textarea::make('details')
                                ->label('Content')
                                ->rows(5)
                                ->default($record->details)
                                ->placeholder('Enter Content Here'),
                    ];
                })
                ->action(function (array $data, $record): void {
                    $extension = strtoupper(pathinfo($data['name'], PATHINFO_EXTENSION));

                     $record->update([
                        'name' => $data['name'],
                        'document_category_id' => $data['document_category_id'],
                        'no_expiration' => $data['no_expiration'],
                        'expired_at'           => $data['expired_at'] ?? null,
                        'type' => $extension,
                        'details' => $data['details'],
                        'signature_token'      => Str::uuid(),
                    ]);

                            Mail::to($record->client->email)->send(new DocumentSignatureRequest($record));


                    \Filament\Notifications\Notification::make()
                        ->title('Document updated successfully')
                        ->success()
                        ->send();
                }),
                Tables\Actions\DeleteAction::make()->color('danger')->label('Delete')
                
                ,

                Action::make('Download')
                    ->icon('heroicon-s-cloud-arrow-down')
                    ->label('Download')
                    ->color('rado')
                    ->action(function ($record): StreamedResponse {
                        $filePath = $record->name; // 'documents/my.pdf'
                        $fileName = basename($filePath);

                        return response()->streamDownload(function () use ($filePath) {
                            echo Storage::disk('public')->get($filePath);
                        }, $fileName);
                    })
                 ]),

         


            ])
            ->defaultSort('id', 'desc');
    }
}
