<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Log;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ShiftNote;

use Livewire\Attributes\On;
use Livewire\Component;

class ClientCommunication extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.client-communication';

            protected static ?string $title = null;
            public ?int $clientId = null;
            public ?array $data = [];
            public $editingNoteId = null;
            public $editForm;
public $visibleNotes = 5; // show 5 by default

            public $isEditModalOpen = false;

            public $showEditModal = false;
            public $newAttachments = [];

                // Edit modal state
                public $editData = [
                    'title' => '',
                    'note_type' => '',
                    'note_body' => '',
                    'attachments' => [],
                    'keep_private' => false,
                ];


            public $existingAttachments = []; 
        public $noteType = '';
public $startDate = '';
public $endDate = '';





        public function getTitle(): string
        {
            $clientId = request()->query('client_id');

            if ($clientId) {
                $client = \App\Models\Client::find($clientId);

                if ($client) {
                    return "{$client->display_name} Communications";
                }
            }

            return 'Client Communications';
        }

    
       public static function shouldRegisterNavigation(): bool
        {
            return false;
        }

         public static function getRoutePath(): string
        {
            return '/client-communication';
        }



    public function mount(): void
    {
            $this->clientId = request()->query('client_id');

        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [

              Section::make('Add Notes')
                ->schema([
            Grid::make(4)
                ->schema([
                   Select::make('note_type')
                        ->label('Note Type')
                        ->options(\App\Models\Note::pluck('type', 'type')) 
                        ->searchable()
                        ->preload()
                        ->columnSpan(2),


                    DatePicker::make('created_at')
                        ->label('Date')
                        ->placeholder('31/10/2025')
                        ->default(now())
                        ->displayFormat('d/m/Y')
                        ->columnSpan(2),
                ]),
            
            Grid::make(4)
                ->schema([
                    TextInput::make('title')
                        ->label('Enter Subject')
                        ->placeholder('Enter Subject')
                        ->columnSpan(3),

                    Checkbox::make('is_private')
                        ->label('Private Note')
                        ->columnSpan(1)
                        ->inline(true),
                ]),

            RichEditor::make('note_body')
                ->label('Notes')
                ->placeholder('Notes')
                ->toolbarButtons([
                    'bold',
                    'italic',
                    'preview',
                    'link',
                    'bulletList',
                ])
                ->required()
                ->columnSpanFull(),

           FileUpload::make('attachments')
                ->label('Choose Files')
                ->multiple()
                ->disk('public')
                ->directory('shift_notes')
                ->hiddenLabel()
                ->columnSpanFull(),

                ]),

        ];
    }

    
    
 public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data');
    }

 

public function getClientNotes()
{
    if (!$this->clientId) {
        return collect();
    }

    // 🔹 Find all shift IDs where this client appears
    $shiftIds = \App\Models\Shift::query()
        ->whereJsonContains('client_section->client_id', (string) $this->clientId)
        ->orWhereJsonContains('client_section->client_id', $this->clientId)
        ->pluck('id');

    // 🔹 Build query for notes related to this client (direct OR via shift)
    $query = \App\Models\ShiftNote::with('shift')
        ->where(function ($query) use ($shiftIds) {
            $query->whereIn('shift_id', $shiftIds)
                  ->orWhere('client_id', $this->clientId);
        });

    // 🔹 Apply filters (without breaking your logic)
    if (!empty($this->noteType)) {
        $query->where('note_type', $this->noteType);
    }

    if (!empty($this->startDate) && !empty($this->endDate)) {
        $query->whereBetween('created_at', [
            \Carbon\Carbon::parse($this->startDate)->startOfDay(),
            \Carbon\Carbon::parse($this->endDate)->endOfDay(),
        ]);
    }

    // 🔹 Default: latest first
    return $query->latest()->get();
}
public function resetFilters()
{
    $this->noteType = '';
    $this->startDate = '';
    $this->endDate = '';
}




public function saveNote()
{
    $data = $this->form->getState();

    $attachmentData = [];

    // ✅ When using Filament FileUpload, attachments are already saved on disk
    if (!empty($data['attachments'])) {
        foreach ($data['attachments'] as $filePath) {
            // $filePath will be something like "shift_notes/filename.pdf"
            $attachmentData[] = ['file_path' => $filePath];
        }
    }




    $authUser = Auth::user();

    ShiftNote::create([
        'user_id'    => $authUser->id,
        'client_id'  => $this->clientId, // ✅ store client_id here
        'note_type'  => $data['note_type'] ?? 'note',
        'note_body'  => $data['note_body'] ?? '',
        'title'      => $data['title'] ?? null,
        'attachments'=> $attachmentData,
        'keep_private' => !empty($data['is_private']) ? 1 : 0,
        'created_at' => $data['created_at'] ?? now(),
    ]);

    Notification::make()
        ->title('Note added successfully!')
        ->success()
        ->send();

    // refresh page to see new note
    return redirect()->route('filament.admin.pages.client-communication', ['client_id' => $this->clientId]);
}

public function openEditModal($noteId)
{
    $note = ShiftNote::findOrFail($noteId);
    $this->editingNoteId = $noteId;

    $this->editData = [
        'title' => $note->title,
        'note_type' => $note->note_type,
        'note_body' => $note->note_body,
        'attachments' => [], // Only for new uploads
        'keep_private' => (bool)$note->keep_private,
    ];

    // Load existing files (from DB JSON)
    $this->existingAttachments = $note->attachments ?? [];

    $this->showEditModal = true;
}


public function removeExistingAttachment($index)
{
    if (isset($this->existingAttachments[$index])) {
        unset($this->existingAttachments[$index]);
        $this->existingAttachments = array_values($this->existingAttachments); // reindex array
    }
}


   public function updateNote()
{
    $note = ShiftNote::findOrFail($this->editingNoteId);
    $data = $this->editData;

    // Start with remaining existing attachments
    $attachmentData = $this->existingAttachments ?? [];

    // Add any new uploaded files
    if (!empty($data['attachments'])) {
        foreach ($data['attachments'] as $file) {
            if ($file instanceof \Illuminate\Http\UploadedFile) {
                $path = $file->store('shift_notes', 'public');
                $attachmentData[] = ['file_path' => $path];
            }
        }
    }

    $note->update([
        'title' => $data['title'],
        'note_type' => $data['note_type'],
        'note_body' => $data['note_body'],
        'attachments' => $attachmentData,
        'keep_private' => !empty($data['keep_private']) ? 1 : 0,
    ]);

    Notification::make()
        ->title('Note updated successfully!')
        ->success()
        ->send();

    $this->showEditModal = false;
}


    

    public function closeModal()
    {
        $this->showEditModal = false;
    }

public function loadMore()
{
    $this->visibleNotes += 5;
}

public function showLess()
{
    $this->visibleNotes = max(5, $this->visibleNotes - 5);
}



}
