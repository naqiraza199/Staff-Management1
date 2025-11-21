<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
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
use App\Models\DocumentCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentSignatureRequest;
use Filament\Tables\Actions\ActionGroup;
use App\Models\Company;
use Filament\Forms\Components\Textarea;
use Filament\Facades\Filament;
use Filament\Forms\Components\View;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

  protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-check';

      protected static ?string $navigationGroup = 'Staff Management';

             public static function canAccess(): bool
        {
            $user = Filament::auth()->user();

            return $user && $user->hasPermissionTo('see-staff-documents');
        }

          public static function getModelLabel(): string
    {
        return 'Shared Documents';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Shared Documents';
    }

    protected static ?int $navigationSort = 4;

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
                ->whereIn('user_id', $staffUserIds) // restrict to staff users
                ->where(function ($query) {
                    $query->where('no_expiration', 1) // always include if no_expiration
                        ->orWhereDate('expired_at', '>', \Carbon\Carbon::now()); // or valid date
                })
                ->whereNull('client_id');
        }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('type')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required(),
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
                    ->label('Name')
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
                    ->label('Document')
                    ->searchable(),

                Tables\Columns\TextColumn::make('documentCategory.name')
                    ->label('Category')
                    ->searchable(),

                Tables\Columns\TextColumn::make('expired_at')
                    ->label('Expired')
                    ->date('d/m/Y')
                    ->searchable(),

                Tables\Columns\IconColumn::make('no_expiration')
                    ->boolean()
                    ->label('No Expiration')
                    ->searchable(),

                    Tables\Columns\IconColumn::make('is_verified')
                        ->label('Signature')
                        ->boolean() 
                        ->trueIcon('heroicon-s-document-check') 
                        ->falseIcon(null), 

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

            Forms\Components\Select::make('user_id')
                ->label('Select Staff')
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
                ->columnSpan(6),
                     Forms\Components\Checkbox::make('no_expiration')
                                ->label('No Expiration')
                                ->reactive()
                                ->columnSpan(3),

                
                       


                    Select::make('document_category_id')
                            ->label('Document Category')
                            ->required()
                            ->columnSpan(6)
                            ->searchable()
                            ->options(function () {
                                $companyId = Company::where('user_id', Auth::id())->value('id');
                                $grouped = [];

                                $grouped['Competencies'] = DocumentCategory::query()
                                    ->where('is_staff_doc', 1)
                                    ->where('is_competencies', 1)
                                    ->where('company_id', $companyId)
                                    ->pluck('name', 'id')
                                    ->toArray();

                                $grouped['Qualifications'] = DocumentCategory::query()
                                    ->where('is_staff_doc', 1)
                                    ->where('is_qualifications', 1)
                                    ->where('company_id', $companyId)
                                    ->pluck('name', 'id')
                                    ->toArray();

                                $grouped['Compliance'] = DocumentCategory::query()
                                    ->where('is_staff_doc', 1)
                                    ->where('is_compliance', 1)
                                    ->where('company_id', $companyId)
                                    ->pluck('name', 'id')
                                    ->toArray();

                                $grouped['KPI'] = DocumentCategory::query()
                                    ->where('is_staff_doc', 1)
                                    ->where('is_kpi', 1)
                                    ->where('company_id', $companyId)
                                    ->pluck('name', 'id')
                                    ->toArray();

                                $grouped['Other'] = DocumentCategory::query()
                                    ->where('is_staff_doc', 1)
                                    ->where('is_other', 1)
                                    ->where('company_id', $companyId)
                                    ->pluck('name', 'id')
                                    ->toArray();

                                // 👇 Add special “Other category (type manually)” option at the end
                                $grouped['Other']['__other__'] = 'Other category (type manually)';

                                return $grouped;
                            })
                            ->reactive() 
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state !== '__other__') {
                                    $set('custom_document_category', null);
                                }
                            }),

                            Forms\Components\TextInput::make('custom_document_category')
                                ->label('Custom Category Name')
                                ->placeholder('Enter new category name')
                                ->columnSpan(6)
                                ->required(fn (callable $get) => $get('document_category_id') === '__other__')
                                ->visible(fn (callable $get) => $get('document_category_id') === '__other__'),

                        Forms\Components\DatePicker::make('expired_at')
                            ->label('Expires At')
                            ->required(fn (callable $get) => ! $get('no_expiration')) // required only if unchecked
                            ->hidden(fn (callable $get) => $get('no_expiration')) // hide if checked
                            ->extraInputAttributes(['id' => 'expired-input-create']) // <-- Unique ID is required!
                            ->columnSpan(6),


                                 View::make('js-initializer')
                                    ->view('filament.forms.components.js-initializer')
                                    ->viewData([
                                        'fieldId' => 'expired-input-create'
                                    ]),
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

                                     Forms\Components\Checkbox::make('send_email')
                        ->label('Send email for signature?')
                        ->default(true),
                ])


                                ->action(function (array $data): void {
                                    $file      = $data['file'];
                                    $expires   = $data['expired_at'] ?? null;
                                    $extension = strtoupper(pathinfo($file, PATHINFO_EXTENSION));

                                    // 🔹 If "Other category (type manually)" selected, create new category first
                                    if (($data['document_category_id'] ?? null) === '__other__') {
                                        $companyId = \App\Models\Company::where('user_id', Auth::id())->value('id');

                                        $newCategory = DocumentCategory::create([
                                            'name'              => $data['custom_document_category'],
                                            'status'            => 1,
                                            'is_staff_doc'      => 1,  // staff doc
                                            'is_competencies'   => 0,
                                            'is_qualifications' => 0,
                                            'is_compliance'     => 0,
                                            'is_kpi'            => 0,
                                            'is_other'          => 1,  // mark as "Other"
                                            'company_id'        => $companyId,
                                        ]);

                                        $data['document_category_id'] = $newCategory->id;
                                    }

                                    $doCategory = $data['document_category_id'];

                                    // 🔹 Create document
                                    $staffDoc = Document::create([
                                        'user_id'              => $data['user_id'],
                                        'name'                 => $file,
                                        'type'                 => $extension,
                                        'document_category_id' => $doCategory,
                                        'no_expiration'        => $data['no_expiration'] ?? 0,
                                        'expired_at'           => $expires,
                                        'signature_token'      => Str::uuid(),
                                        'details'              => $data['details'],
                                    ]);

                                    // 🔹 Send email only if send_email is checked (if you added checkbox)
                                    if (!empty($data['send_email'])) {
                                        Mail::to($staffDoc->user->email)->send(new DocumentSignatureRequest($staffDoc));
                                    }

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
                ->hidden(fn ($record) => $record->is_verified)
                ->modalHeading('Edit Document')
                ->color('stripe')
                ->form(function (\Filament\Tables\Actions\Action $action): array {
                    /** @var \App\Models\Document $record */
                    $record = $action->getRecord();

                    return [
                          Grid::make(12)
                        ->schema([
                                        Forms\Components\Select::make('user_id')
                ->label('Select Staff')
                ->searchable()
                ->default($record->user_id)
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
                                ->columnSpan(6),

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
                                            $companyId = Company::where('user_id', Auth::id())->value('id');
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
                                ->extraInputAttributes(['id' => 'expired-input']) // <-- Unique ID is required!
                                ->columnSpan(6),

                                 View::make('js-initializer')
                                    ->view('filament.forms.components.js-initializer')
                                    ->viewData([
                                        'fieldId' => 'expired-input'
                                    ]),
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

                                 Forms\Components\Checkbox::make('send_email')
                                ->label('Send email for signature?')
                                ->default(true),
                    ];
                })
                                        ->action(function (array $data, $record): void {
                                        $extension = strtoupper(pathinfo($data['name'], PATHINFO_EXTENSION));

                                        // 🔹 Update the document
                                        $record->update([
                                            'user_id'              => $data['user_id'],
                                            'name'                 => $data['name'],
                                            'document_category_id' => $data['document_category_id'],
                                            'expired_at'           => $data['expired_at'] ?? null,
                                            'no_expiration'        => $data['no_expiration'],
                                            'type'                 => $extension,
                                            'details'              => $data['details'],
                                            'signature_token'      => Str::uuid(),
                                        ]);

                                        // 🔹 Refresh relation in case user_id changed
                                        $record->refresh();

                                        // 🔹 Send email only if checkbox checked AND user email exists
                                        if (!empty($data['send_email'])) {
                                            $user = $record->user ?? null;   // relationship: Document belongsTo User

                                            if ($user && !empty($user->email)) {
                                                Mail::to($user->email)->send(new DocumentSignatureRequest($record));
                                            }
                                            // else: silently skip sending to avoid "To" header error
                                        }

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
            'index' => Pages\ListDocuments::route('/'),
            // 'create' => Pages\CreateDocument::route('/create'),
            // 'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
