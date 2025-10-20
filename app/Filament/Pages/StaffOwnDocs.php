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
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use App\Models\DocumentCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentSignatureRequest;
use Filament\Tables\Actions\ActionGroup;

class StaffOwnDocs extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.staff-own-docs';
    protected static ?string $title = null;

        public function getTitle(): string
        {
            $userId = request()->query('user_id');

            if ($userId) {
                $user = \App\Models\User::find($userId);

                if ($user) {
                    return "{$user->name} Documents";
                }
            }

            return 'Staff Documents';
        }


       public static function shouldRegisterNavigation(): bool
{
    return false;
}

    public ?int $userId = null;

  
        public static function getRoutePath(): string
        {
            return '/staff-own-docs';
        }

        public function mount(): void
        {
            $this->userId = request()->query('user_id');
        }


    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder =>
                Document::query()->where('user_id', $this->userId)
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

                Tables\Columns\TextColumn::make('name')->label('Document')->searchable(),
                Tables\Columns\TextColumn::make('documentCategory.name')->label('Category')->searchable(),
                Tables\Columns\TextColumn::make('expired_at')->label('Expired At')->date('d/m/Y')->searchable(),
                Tables\Columns\IconColumn::make('no_expiration')->boolean()->label('No Expiration')->searchable(),
                   Tables\Columns\IconColumn::make('is_verified')
                        ->label('Signature')
                        ->boolean() 
                        ->trueIcon('heroicon-s-document-check') 
                        ->falseIcon(null), 
                Tables\Columns\TextColumn::make('created_at')->label('Last Update')->since()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])->headerActions([
    Tables\Actions\Action::make('Upload Document')
        ->icon('heroicon-o-arrow-up-tray')
        ->form([
            Grid::make(12)
                ->schema([
               Forms\Components\Hidden::make('user_id')
                ->default(fn ($livewire) => $livewire->userId)
                ->columnSpan(6),


              Forms\Components\Checkbox::make('no_expiration')
                    ->label('No Expiration')
                    ->reactive() // 👈 important so Filament listens for changes
                    ->columnSpan(6),

                    Select::make('document_category_id')
                        ->label('Document Category')
                        ->required()
                        ->columnSpan(6)
                        ->searchable()
                        ->options(function () {
                            $companyId = \App\Models\Company::where('user_id', Auth::id())->value('id');
                            $grouped = [];

                            $grouped['Competencies'] = \App\Models\DocumentCategory::query()
                                ->where('is_staff_doc', 1)
                                ->where('is_competencies', 1)
                                ->where('company_id', $companyId)
                                ->pluck('name', 'id')
                                ->toArray();

                            $grouped['Qualifications'] = \App\Models\DocumentCategory::query()
                                ->where('is_staff_doc', 1)
                                ->where('is_qualifications', 1)
                                ->where('company_id', $companyId)
                                ->pluck('name', 'id')
                                ->toArray();

                            $grouped['Compliance'] = \App\Models\DocumentCategory::query()
                                ->where('is_staff_doc', 1)
                                ->where('is_compliance', 1)
                                ->where('company_id', $companyId)
                                ->pluck('name', 'id')
                                ->toArray();

                            $grouped['KPI'] = \App\Models\DocumentCategory::query()
                                ->where('is_staff_doc', 1)
                                ->where('is_kpi', 1)
                                ->where('company_id', $companyId)
                                ->pluck('name', 'id')
                                ->toArray();

                            $grouped['Other'] = \App\Models\DocumentCategory::query()
                                ->where('is_staff_doc', 1)
                                ->where('is_other', 1)
                                ->where('company_id', $companyId)
                                ->pluck('name', 'id')
                                ->toArray();

                            return $grouped;
                        }),

                        Forms\Components\DatePicker::make('expired_at')
                            ->label('Expires At')
                            ->required(fn (callable $get) => ! $get('no_expiration')) // required only if unchecked
                            ->hidden(fn (callable $get) => $get('no_expiration')) // hide if checked
                            ->columnSpan(6),
                ]),

            Forms\Components\FileUpload::make('file')
                ->label('Upload Document')
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/plain'
                ])
                ->helperText('Accepted file types: PDF, DOC, DOCX, XLS, XLSX, TXT')
                ->required()
                ->directory('documents')
                ->preserveFilenames()
                ->disk('public')
                ->maxSize(2048),

                Forms\Components\TextArea::make('details')
                                ->label('Content')
                                ->rows(5)
                                ->placeholder('Enter Content Here'),
        ])
        ->action(function (array $data, Tables\Actions\Action $action) {
            $filePath = $data['file']; // relative path on 'public' disk
            $extension = strtoupper(pathinfo($filePath, PATHINFO_EXTENSION));

          $staffDoc =  \App\Models\Document::create([
                'user_id'              => $data['user_id'],
                'name'                 => $filePath,
                'type'                 => $extension,
                'document_category_id' => $data['document_category_id'],
                'no_expiration'        => $data['no_expiration'] ?? 0,
                'expired_at'           => $data['expired_at'] ?? null,
                'signature_token'      => Str::uuid(),
                'details' => $data['details'],
                         
                    ]);

                            Mail::to($staffDoc->user->email)->send(new DocumentSignatureRequest($staffDoc));

            // ✅ Close modal
            $action->success();

            // ✅ Show notification
            \Filament\Notifications\Notification::make()
                ->title('Document uploaded successfully')
                ->success()
                ->send();

            // ✅ Refresh table data
            $this->dispatch('refreshTable');
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
                ->modalHeading('Staff Signature')
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
                ->icon('heroicon-s-pencil-square')
                ->label('Edit')
                ->modalHeading('Edit Document')
                ->hidden(fn ($record) => $record->is_verified)
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
                            ->columnSpan(6),



                                Select::make('document_category_id')
                                    ->label('Document Category')
                                    ->required()
                                    ->columnSpan(6)
                                    ->searchable()
                                    ->default($record->document_category_id)
                                    ->options(function () {
                                            $companyId = \App\Models\Company::where('user_id', Auth::id())->value('id');
                                            $grouped = [];

                                            // Competencies
                                            $grouped['Competencies'] = \App\Models\DocumentCategory::query()
                                                ->where('is_staff_doc', 1)
                                                ->where('is_competencies', 1)
                                                ->where('company_id', $companyId)
                                                ->pluck('name', 'id')
                                                ->toArray();

                                            // Qualifications
                                            $grouped['Qualifications'] = \App\Models\DocumentCategory::query()
                                                ->where('is_staff_doc', 1)
                                                ->where('is_qualifications', 1)
                                                ->where('company_id', $companyId)
                                                ->pluck('name', 'id')
                                                ->toArray();

                                            // Compliance
                                            $grouped['Compliance'] = \App\Models\DocumentCategory::query()
                                                ->where('is_staff_doc', 1)
                                                ->where('is_compliance', 1)
                                                ->where('company_id', $companyId)
                                                ->pluck('name', 'id')
                                                ->toArray();

                                            // KPI
                                            $grouped['KPI'] = \App\Models\DocumentCategory::query()
                                                ->where('is_staff_doc', 1)
                                                ->where('is_kpi', 1)
                                                ->where('company_id', $companyId)
                                                ->pluck('name', 'id')
                                                ->toArray();

                                            // Other
                                            $grouped['Other'] = \App\Models\DocumentCategory::query()
                                                ->where('is_staff_doc', 1)
                                                ->where('is_other', 1)
                                                ->where('company_id', $companyId)
                                                ->pluck('name', 'id')
                                                ->toArray();

                                            return $grouped;
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

                              Forms\Components\TextArea::make('details')
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
                        'expired_at'           => $data['expired_at'] ?? null,
                        'no_expiration'        => $data['no_expiration'],
                        'type'                 => $extension,
                        'details'              => $data['details'],
                        'signature_token'      => Str::uuid(),
                    ]);

                    Mail::to($record->user->email)->send(new DocumentSignatureRequest($record));


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
