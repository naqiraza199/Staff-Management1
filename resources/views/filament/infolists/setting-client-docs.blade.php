
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- General Setup --- */
        :root {
            --color-background-light: #f4f7f9;
            --color-background-dark: #e0e6ef;
            --color-panel-bg: #ffffff;
            --color-text-primary: #1a202c; /* Dark text */
            --color-text-secondary: #4a5568; /* Gray text for labels */
            --color-accent-blue: #2a65a6; /* Main brand blue */
            --color-accent-blue-light: #e0f2fe; /* Lighter blue for tag hover */
            --color-border: #edf2f7;
            --shadow-subtle: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
            --shadow-hover: 0 8px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --font-family-primary: 'Inter', sans-serif;
            --border-radius-panel: 12px;
            --border-radius-tag: 8px;
            --border-radius-button: 8px;
        }

        .category-wrapper {
            width: 100%;
            max-width: 900px; /* Wider for more tags per line */
        }

        /* --- Panel Styles --- */
        .category-panel {
            background-color: var(--color-panel-bg);
            border-radius: var(--border-radius-panel);
            box-shadow: var(--shadow-subtle);
            padding: 30px;
            border: 1px solid var(--color-border);
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
        }

        .category-panel:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }

        /* --- Panel Header --- */
        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--color-border);
        }

        .panel-header h2 {
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--color-text-primary);
            margin: 0;
        }

        .action-button-edit {
            background: none;
            border: 1px solid var(--color-accent-blue);
            color: var(--color-accent-blue);
            padding: 8px 18px;
            border-radius: var(--border-radius-button);
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s, box-shadow 0.2s, transform 0.1s;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            outline: none;
        }

        .action-button-edit:hover {
            background-color: var(--color-accent-blue);
            color: white;
            box-shadow: 0 4px 10px rgba(42, 101, 166, 0.3);
        }
        
        .action-button-edit:active {
            transform: translateY(1px);
        }

        /* --- Tags Container --- */
        .tags-container {
            display: flex;
            flex-wrap: wrap; /* Allows tags to wrap to the next line */
            gap: 8px; /* Space between tags */
        }

        /* --- Individual Tag Styling --- */
        .category-tag {
                background-color: #f0f2f5;
                color: var(--color-text-primary);
                padding: 6px 12px;
                border-radius: var(--border-radius-tag);
                font-size: 11px;
                font-weight: 500;
                cursor: pointer;
                transition: background-color 0.2s, color 0.2s, box-shadow 0.2s;
                border: 1px solid var(--color-border);
                user-select: none;
        }

        .category-tag:hover {
            background-color: var(--color-accent-blue); /* Blue on hover */
            color: white;
            box-shadow: 0 4px 12px rgba(42, 101, 166, 0.2);
            transform: translateY(-2px); /* Slight lift */
        }

        .category-tag:active {
            transform: translateY(0); /* Press effect */
            background-color: var(--color-button-hover);
            box-shadow: 0 2px 8px rgba(42, 101, 166, 0.3);
        }

        /* --- Responsive Adjustments --- */
        @media (max-width: 600px) {
            .category-panel {
                padding: 20px;
            }
            .panel-header {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 20px;
            }
            .panel-header h2 {
                margin-bottom: 10px;
                font-size: 1.4rem;
            }
            .action-button-edit {
                align-self: flex-start;
            }
            .tags-container {
                gap: 8px; /* Smaller gap on small screens */
            }
            .category-tag {
                padding: 8px 14px;
                font-size: 0.85rem;
            }
        }
    </style>
@php
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\DocumentCategory;

$user = Auth::user();
$companyId = Company::where('user_id', $user->id)->value('id');
$documents = DocumentCategory::where('company_id', $companyId)
    ->where('is_staff_doc', 0)
    ->get();
@endphp


            <div class="tags-container">
                @foreach($documents as $document)
                <div class="category-tag">{{ $document?->name ?? '-' }}</div>
                @endforeach
            </div>

