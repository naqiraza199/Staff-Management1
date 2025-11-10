<x-filament-panels::page>


    <style>
        /* --- CSS Variables for Dark Theme (Matching Tailwind Configuration) --- */
        :root {
            --color-primary: #06B6D4; /* Teal 500 */
            --color-secondary: #1F2937; /* Card/Surface Background (Gray 800) */
            --color-background: #111827; /* Main Background (Gray 900) */
            --color-text-light: #F3F4F6; /* Gray 100 */
            --color-text-dark: #1F2937; /* For Primary Button Text (Contrast) */
            --color-text-medium: #9CA3AF; /* Gray 400/500 */
            --color-border: #374151; /* Gray 700/600 (Dark Borders) */
            --color-alert: #EF4444; /* Red 500 */
        }


        /* Custom Scrollbar for a sleek dark look */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--color-secondary); }
        ::-webkit-scrollbar-thumb { background: var(--color-primary); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #0E7490; }

        /* --- Global Layout Utilities --- */
        .max-w-7xl { max-width: 1280px; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .w-full { width: 100%; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .space-x-4 > * + * { margin-left: 1rem; }
        .flex-col { flex-direction: column; }

        /* Main content area padding to prevent content from hiding behind fixed header/footer */
        .main-content {
             background: #ECF0F5;
            position: relative;
            left: 270px;
            padding-right: 295px;
            transition: all 0.3s ease-in-out;
        }

        /* --- Fixed Header and Footer Styling --- */
       

    
        /* --- Timeline Styling --- */

        .timeline-container {
                position: relative;
                background: #ffffff;
                padding: 30px 30px 20px 0px;
                border-radius: 10px
        }

        /* Timeline vertical line */
        .timeline-container::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 1px;
            background-color: #b3b3b3;
            height:100%;
        }

        .timeline-item {
            margin-bottom: 3rem;
        }

        /* --- Card Styling --- */
        .content-card {
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid #c5c5c5;
            transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s;
                margin-bottom: 50px;
    margin-left: 50px;

        }

        .content-card:hover {
            transform: scale(1.01);
            background-color: #e1e1e1; /* Slightly lighter secondary on hover */
        }

        /* High Priority Card Border */
        .card-border-alert {
            border: 1px solid rgba(239, 68, 68, 0.5);
        }


        /* Fixed Footer Styling */
        .custom-footer-form {
            background-color: #ededed;
            backdrop-filter: blur(8px);
            box-shadow: 0 -8px 16px rgba(0, 0, 0, 0.3);
            padding: 1.5rem 0;
            z-index: 50;
        }
        
        /* --- Grid & Responsiveness (Mobile First) --- */
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .gap-4 { gap: 1rem; }

        @media (min-width: 768px) {
            /* Tablet/Desktop Styles */
            .grid-cols-md-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .col-span-md-2 { grid-column: span 2 / span 2; }
            .p-md-4 { padding: 1rem; }
            .p-md-6 { padding: 1.5rem; }
        }

        /* Specific mobile padding for header/footer content */
        .px-mobile { padding-left: 1rem; padding-right: 1rem; }
        @media (min-width: 640px) {
            .px-mobile { padding-left: 1.5rem; padding-right: 1.5rem; }
        }

            
         .timeline-item {
        position: relative;
        margin-bottom: 2rem;
      
    }

    .timeline-dot {
        position: absolute;
        left: 14px;
        top: 0.25rem;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: var(--color-primary, #06b6d4);
        box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.3);
        z-index: 10;
    }


    hr {
        border: none;
        border-top: 1px solid #e5e7eb;
        margin: 1.5rem 0;
    }
section#data\.add-notes {
    margin-left: 50px;
}

/* Modal overlay */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    animation: fadeIn 0.3s ease forwards;
}

/* Modal box */
.modal-container {
    background: #ffffff;
    border-radius: 12px;
    padding: 30px 35px;
    width: 500px;
    max-width: 95%;
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
    position: relative;
    transform: translateY(30px);
    opacity: 0;
    animation: slideIn 0.4s ease forwards;
}

/* Title */
.modal-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1e293b;
    text-align: center;
    margin-bottom: 20px;
}

/* Input fields */
.input-field {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s;
}

.input-field:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
}

/* Form groups */
.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #374151;
}

/* Attachments grid */
.attachments-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.attachment-item {
    position: relative;
    transition: transform 0.2s ease;
}

.attachment-item:hover {
    transform: scale(1.05);
}

.attachment-thumb {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #cbd5e1;
}

.remove-btn {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    cursor: pointer;
    font-size: 13px;
    line-height: 1;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

/* Buttons */
.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 25px;
}

.btn {
    padding: 9px 18px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.cancel-btn {
    background: #e2e8f0;
    color: #334155;
}

.cancel-btn:hover {
    background: #cbd5e1;
}

.save-btn {
    background: #2563eb;
    color: #fff;
}

.save-btn:hover {
    background: #1d4ed8;
}

/* Close button */
.close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    background: none;
    border: none;
    color: #475569;
    font-size: 24px;
    cursor: pointer;
    transition: color 0.2s;
}

.close-btn:hover {
    color: #111827;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { transform: translateY(30px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.timeline-item {
    animation: fadeIn 0.4s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">


    <!-- 2. MAIN CONTENT FEED (SCROLLABLE) -->
        <!-- Timeline Container -->
          <div style="background: #fff;
                        padding: 20px;
                        width: 100%;
                        display: flex;
                        gap: 20px;
                        align-items: flex-end;
                        margin-bottom: 20px;">

    <!-- Note Type Filter -->
    <div style="display: flex; flex-direction: column;">
        <label style="font-weight: 600; font-size: 13px; color: #333;">Note Type</label>
        <select wire:model.live="noteType"
            style="padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; min-width: 180px;">
            <option value="">All Types</option>
            @foreach(\App\Models\ShiftNote::select('note_type')->distinct()->pluck('note_type') as $type)
                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>

    <!-- Date From -->
    <div style="display: flex; flex-direction: column;">
        <label style="font-weight: 600; font-size: 13px; color: #333;">Start Date</label>
        <input type="date" wire:model.live="startDate"
            style="padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
    </div>

    <!-- Date To -->
    <div style="display: flex; flex-direction: column;">
        <label style="font-weight: 600; font-size: 13px; color: #333;">End Date</label>
        <input type="date" wire:model.live="endDate"
            style="padding: 8px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">
    </div>

    <!-- Reset Button -->
    <div>
        <button wire:click="resetFilters"
            style="padding: 8px 16px; background: #07899f; color: white; font-weight: 600; border: none; border-radius: 6px; cursor: pointer;">
            Reset Filters
        </button>
    </div>

</div>
        <div class="timeline-container">

    


        @php
            $notes = $this->getStaffNotes();
        @endphp

        @forelse($notes->take($visibleNotes) as $note)

                    <div class="timeline-item">
                        <span class="timeline-dot"></span>
                        <div class="content-card">
                            <div class="flex justify-between items-start" style="margin-bottom: 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); padding-bottom: 0.75rem;">
                                <div>
                                    <span style="font-size: 0.75rem; font-weight: 500; color: var(--color-primary); display: block;">
                                        {{ \Carbon\Carbon::parse($note->created_at)->format('F d, Y') }}
                                    </span>
                               @php
                                        $authorName = \App\Models\User::find($note->user_id)?->name ?? 'Unknown User';
                                        $noteDate = \Carbon\Carbon::parse($note->created_at)->format('d/m/Y');
                                    @endphp

                                    <div style="display:flex;gap:10px">
                                        <h2 style="font-weight: 700; color: #07899f; margin-top: 0.25rem;font-size:20px">{{ $authorName }}</h2> <span style="margin-top: 5px;">added a Note</span>
                                    </div>

                                    <hr style="margin-top: 10px;margin-bottom: 10px;">

                             <h6 style="font-weight: 500; color: #07899f; margin-top: 0.25rem;">
                                    @if(!empty($note->title))
                                        {{ $note->title }}
                                    @endif
                                </h6>


                                </div>
                                <div class="flex items-center" style="color: #2a2a2a;font-size: 12px;">
                                    <span>{{ $note->created_at->diffForHumans() }}</span>
                         


                                </div>
                            </div>
                            <div style="font-size: 11px; color: #2a2a2a;">
                                   {!! $note->note_body !!}
                            </div>
                                            <div class="attachment" style="display: flex;gap: 10px;">

                                       @if(!empty($note->attachments))
                                        <div class="flex flex-wrap gap-3 mt-2 mb-3">
                                            @foreach($note->attachments as $attachment)
                                                @php
                                                    $filePath = $attachment['file_path'] ?? null;
                                                    $fileUrl = $filePath ? Storage::url($filePath) : null;
                                                    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                @endphp

                                                @if($isImage)
                                                    <img 
                                                        src="{{ $fileUrl }}" 
                                                        alt="Attachment" 
                                                        style="max-width: 100px; border: 1px #dfdfdf groove; border-radius: 10px; height: auto;">
                                                @else
                                                    <a 
                                                        href="{{ $fileUrl }}" 
                                                        target="_blank" 
                                                        style="display: inline-block; padding: 5px 10px; background: var(--color-primary); color: white; border-radius: 6px; text-decoration: none; font-size: 12px;height: 30px">
                                                        Download {{ strtoupper($extension) }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <p style="font-size: 12px; color: #555;">No attachments for this note.</p>
                                    @endif

                                    </div>

                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500">No notes found for this client.</p>
                @endforelse
                @if($notes->count() > 5)
                        <div style="text-align: center; margin-top: 1.5rem;">
                            @if($visibleNotes < $notes->count())
                                <button wire:click="loadMore"
                                    style="background: var(--color-primary); color: white; padding: 8px 18px; border: none; border-radius: 6px; cursor: pointer;">
                                    Load More
                                </button>
                            @endif

                            @if($visibleNotes > 5)
                                <button wire:click="showLess"
                                    style="background: #e5e5e5; color: #333; padding: 8px 18px; border: none; border-radius: 6px; cursor: pointer; margin-left: 8px;">
                                    Show Less
                                </button>
                            @endif
                        </div>
                    @endif



            
                <form wire:submit.prevent="saveNote">
                    {{ $this->form }}

                    <div class="mt-4 flex justify-end">
                        <x-filament::button type="submit" color="primary">
                            Add
                        </x-filament::button>
                    </div>
                </form>

        </div>



         

</x-filament-panels::page>
