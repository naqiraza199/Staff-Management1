<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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
use App\Models\User;

use Livewire\Attributes\On;
use Livewire\Component;


class StaffCommunication extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.staff-communication';

     protected static ?string $title = null;
            public ?int $staffId = null;
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
            $staffId = request()->query('staff_id');

            if ($staffId) {
                $staff = \App\Models\User::find($staffId);

                if ($staff) {
                    return "{$staff->name} Communications";
                }
            }

            return 'Staff Communications';
        }

    
       public static function shouldRegisterNavigation(): bool
        {
            return false;
        }

         public static function getRoutePath(): string
        {
            return '/staff-communication';
        }



    public function mount(): void
    {
            $this->staffId = request()->query('staff_id');

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

 

public function getStaffNotes()
{
    if (!$this->staffId) {
        return collect();
    }

    $query = \App\Models\ShiftNote::query()
        ->whereNull('client_id')
        ->where('user_id', $this->staffId)
        ->where('staff_note', true);

    // 🔹 Apply filters (note type and date range)
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
        'user_id'    => $this->staffId,
        'note_type'  => $data['note_type'] ?? 'note',
        'note_body'  => $data['note_body'] ?? '',
        'attachments'=> $attachmentData,
        'keep_private' => !empty($data['is_private']) ? 1 : 0,
        'staff_note' => 1,
    ]);

    Notification::make()
        ->title('Note added successfully!')
        ->success()
        ->send();

    // refresh page to see new note
    return redirect()->route('filament.admin.pages.staff-communication', ['staff_id' => $this->staffId]);
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
