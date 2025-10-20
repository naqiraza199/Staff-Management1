
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- General Setup --- */
        :root {
            --color-primary: #1e3a8a; /* Dark Blue */
            --color-secondary: #3b82f6; /* Bright Blue */
            --color-text-dark: #1f2937;
            --color-text-light: #4b5563;
            --color-background-start: #f0f4f8;
            --color-background-end: #e5e7eb;
            --color-divider: rgba(0, 0, 0, 0.08);
            --shadow-subtle: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
            --glass-blur: 10px;
        }

        .container {
            width: 100%;
            max-width: 600px;
        }

        /* --- Card Styles (Advanced Look) --- */
      

    

        /* --- Header & Action --- */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 2px solid var(--color-divider);
            padding-bottom: 15px;
        }

        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--color-primary);
            margin: 0;
        }

        .edit-btn {
            background: none;
            border: 1px solid var(--color-secondary);
            color: var(--color-secondary);
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, color 0.2s, transform 0.1s;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .edit-btn:hover {
            background-color: var(--color-secondary);
            color: white;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.4);
        }
        
        .edit-btn:active {
            transform: scale(0.98);
        }

        /* --- Data Row Layout --- */
        .data-grid {
            display: grid;
            grid-template-columns: 1fr 2fr; /* Label (1 part) and Value (2 parts) */
            gap: 15px 20px;
        }

        .data-label {
            font-weight: 400;
            color: var(--color-text-light);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-value {
            font-weight: 600;
            color: var(--color-text-dark);
            font-size: 12px;
            overflow-wrap: break-word; /* Ensure long words break */
        }

        /* Special styling for Contact/Email to look like links */
        .data-value.linkable {
            color: var(--color-primary);
            cursor: pointer;
            text-decoration: none;
            transition: color 0.2s;
        }

        .data-value.linkable:hover {
            color: var(--color-secondary);
            text-decoration: underline;
        }
        
        .contact-icon {
            margin-right: 8px;
            /* Using a simple inline SVG/Icon placeholder */
            font-style: normal;
            font-weight: 900;
            color: var(--color-secondary);
        }

        /* --- Emergency Contact Specific Styles --- */
        .emergency-header {
            font-size: 17px;
            font-weight: 600;
            color: #000;
            margin-bottom: 20px;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px dashed var(--color-divider);
        }

        .custom-checkbox {
            position: relative;
            cursor: pointer;
            margin-right: 10px;
        }

        .custom-checkbox input[type="checkbox"] {
            opacity: 0;
            position: absolute;
        }

        .checkmark {
            height: 20px;
            width: 20px;
            background-color: #eee;
            border-radius: 5px;
            display: block;
            border: 1px solid var(--color-text-light);
            transition: background-color 0.2s, border-color 0.2s;
        }

        .custom-checkbox input:checked ~ .checkmark {
            background-color: var(--color-secondary);
            border-color: var(--color-secondary);
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }

        .custom-checkbox input:checked ~ .checkmark:after {
            display: block;
        }

        .checkbox-label {
            color: #86878a;
            font-weight: 500;
            font-size: 12px;
        }

        /* --- Responsive Adjustments --- */
        @media (max-width: 480px) {
            .card {
                padding: 20px;
            }
            .data-grid {
                grid-template-columns: 1fr; /* Stack on tiny screens */
                gap: 5px 0;
            }
            .data-label {
                margin-top: 10px; /* Space between stacked rows */
                margin-bottom: 0;
            }
            .card-header h2, .emergency-header {
                font-size: 1.3rem;
            }
        }
    </style>
@php
use App\Models\User;
use App\Models\StaffContact;


    $staff = $getRecord();
    $contact = StaffContact::where('user_id',$staff->id)->first();
@endphp
    <div class="container">

        <!-- Next of Kin Card -->
        <div class="card">
           
            
            <div class="data-grid">
                <div class="data-label">Name</div>
                <div class="data-value">{{ $contact->kin_name ?? '-' }}</div>

                <div class="data-label">Relation</div>
                <div class="data-value">{{ $contact->kin_relation ?? '-' }}</div>

                <div class="data-label">Contact</div>
                <div class="data-value linkable" onclick="console.log('Call contact...')">
                    <span class="contact-icon">&#9742;</span>{{ $contact->kin_contact ?? '-' }}
                </div>

                <div class="data-label">Email</div>
                <div class="data-value linkable" onclick="console.log('Email address...')">
                    {{ $contact->kin_email ?? '-' }}
                </div>
            </div>
        </div>

        <!-- Emergency Contact Card (Separate section within the same container) -->
        <div class="card" style="margin-top: 30px;">
            <h2 class="emergency-header">Emergency Contact</h2>
            <div class="checkbox-row">
                <label class="custom-checkbox">
                        @if($contact)
                            <input type="checkbox" @checked($contact->same_as_kin) disabled>
                        @else
                            <input type="checkbox" disabled>
                        @endif

                    <span class="checkmark"></span>
                </label>
                <span class="checkbox-label">Same as Next of Kin</span>
            </div>

            <div class="data-grid">
                <!-- Note: The data is visible but would typically be disabled/hidden if the checkbox is checked -->
                <div class="data-label">Name</div>
                <div class="data-value">{{ $contact->emergency_contact_name ?? '-' }}</div>

                <div class="data-label">Relation</div>
                <div class="data-value">{{ $contact->emergency_contact_relation ?? '-' }}</div>

                <div class="data-label">Contact</div>
                <div class="data-value linkable" onclick="console.log('Call contact...')">
                    <span class="contact-icon">&#9742;</span>{{ $contact->emergency_contact_contact ?? '-' }}
                </div>

                <div class="data-label">Email</div>
                <div class="data-value linkable" onclick="console.log('Email address...')">
                   {{ $contact->emergency_contact_email ?? '-' }}
                </div>
            </div>
        </div>
    </div>
