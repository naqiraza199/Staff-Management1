
    <style>
        /* --- CSS Variables (Modern Light Theme Palette) --- */
        :root {
            --color-bg-light: #F0F2F5; /* Very Light Gray Background */
            --color-card-bg: #FFFFFF;
            --color-primary-dark: #2C3E50; /* Deep Blue/Gray for primary text */
            --color-accent: #3498DB; /* Vibrant Blue Accent */
            --color-accent-light: #EAF2F8; /* Lighter accent for backgrounds */
            --color-text-subtle: #7F8C8D; /* Muted Gray for labels */
            
            /* Enhanced, multi-layered shadow for depth */
            --shadow-card: 0 12px 25px rgba(0, 0, 0, 0.08), 
                            0 4px 8px rgba(0, 0, 0, 0.04); 

            --font-main: 'Poppins', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* --- Base & Typography --- */
     


        /* --- Content Grid Layout --- */
        .card-content {
            display: grid;
            grid-template-columns: 3fr 1.5fr;
            gap: 20px;
        }
        
        /* --- Detail Rows (No borders, just spacing) --- */
        .detail-item {
            display: grid;
            grid-template-columns: 180px 1fr; /* Slightly adjusted label width */
            align-items: center;
            padding: 16px 0; 
        }


        
        .detail-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--color-text-subtle);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .detail-value {
            font-size: 11px;
            font-weight: 500;
            color: var(--color-primary-dark);
            word-break: break-word;
        }

        /* --- Contact Grouping --- */
        .contact-group-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 5px 0;
        }

        .contact-link {
display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 0px;
    border-radius: 25px;
    font-weight: 500;
    text-decoration: none;
        }


        .contact-link svg {
            width: 16px;
            height: 16px;
            color: var(--color-accent);
        }
        

        /* --- Language Tags --- */
        .language-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
        }

        .tag {
            background-color: #3498db42;
                color: #126ff9;
                padding: 3px 9px;
                border-radius: 5px;
                font-size: 9px;
                font-weight: 500;
                border: none;
                cursor: pointer;
                transition: background-color 0.2s;
        }

        .tag:hover {
            background-color: #2980B9;
            color:white;
        }

        /* --- Right Section: Profile Image Area --- */
        .profile-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 15px;
        }

        .profile-container {
            position: relative;
            width: 180px;
            height: auto;
            /* Placeholder for an actual image */
            background-image: url('https://via.placeholder.com/180/3498DB/FFFFFF?text=Logo'); /* Placeholder Image */
            background-size: cover;
            background-position: center;
            border: 2px solid #6e6e6e0d; 
            box-shadow: 0 0 0 4px rgb(70 70 70 / 20%), 0 8px 20px rgba(0, 0, 0, 0.15);
            display: flex; /* Centering if image is smaller */
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 25px;
        }
        
        .profile-name {
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--color-primary-dark);
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        
        .profile-title {
            font-size: 0.9rem;
            color: var(--color-text-subtle);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .camera-overlay {
            position: absolute;
            bottom: 8px;
            right: 8px;
            width: 38px;
            height: 38px;
            background-color: var(--color-accent); 
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 3px solid white;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
        }

        .camera-overlay:hover {
            background-color: #2980B9;
            transform: scale(1.05);
        }

        .camera-overlay svg {
            width: 18px;
            height: 18px;
            color: white; 
        }

        /* --- Responsiveness --- */
        @media (max-width: 992px) {
            .card-content {
                grid-template-columns: 1fr;
                padding: 25px;
            }
            .profile-area {
                order: -1;
                padding-bottom: 20px;
                border-radius: 20px 20px 0 0;
            }
            .card-header {
                padding: 20px 25px;
            }
            .detail-item {
                grid-template-columns: 1fr;
                padding: 10px 0;
            }
            .profile-container {
                width: 150px;
                height: 150px;
            }
        }
    </style>
        @php
        use App\Models\User;
        use App\Models\Company;
        use App\Models\StaffProfile;
        use Illuminate\Support\Facades\Auth;

        $authUser = Auth::user();
        $staff = $getRecord();

        $staffData = StaffProfile::where('user_id', $staff->id)->first();

        @endphp

    <!-- Main Card Container --><div class="profile-card">

    

        <!-- Card Content Grid --><div class="card-content">
            
            <!-- Left Section: Details List --><div class="detail-list">

                <!-- Name --><div class="detail-item">
                    <p class="detail-label">Name:</p>
                    <p class="detail-value">{{ $staff->name ?? '-' }}</p>
                </div>

                <!-- Contact --><div class="detail-item">
                    <p class="detail-label">Contact:</p>
                    <div class="detail-value contact-group-wrapper">
                      <a href="tel:{{ $staffData->mobile_number ?? '-' }}" class="contact-link flex items-center gap-1 text-blue-600 hover:underline">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 18h2" />
                                </svg>
                                {{ $staffData->mobile_number ?? '-' }}
                            </a>
                        <a href="tel:{{ $staffData->phone_number ?? '-' }}" class="contact-link">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $staffData->phone_number ?? '-' }}
                        </a>
                       <a href="mailto:{{ $staff->email ?? '-' }}" class="contact-link flex items-center gap-1 text-blue-600 hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l9 6 9-6M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z" />
                        </svg>
                        {{ $staff->email ?? '-' }}
                    </a>

                    </div>
                </div>

                <!-- Address --><div class="detail-item">
                    <p class="detail-label">Address:</p>
                    <p class="detail-value">{{ $staffData->address ?? '-' }}</p>
                </div>

                <!-- Gender --><div class="detail-item">
                    <p class="detail-label">Gender:</p>
                    <p class="detail-value" style="text-transform: capitalize;">{{ $staffData->gender ?? '-' }}</p>
                </div>

                <!-- Employment Type --><div class="detail-item">
                    <p class="detail-label">Employment Type:</p>
                    <p class="detail-value">{{ $staffData->employment_type ?? '-' }}</p>
                </div>

                <!-- DOB --><div class="detail-item">
                    <p class="detail-label">DOB:</p>
              <p class="detail-value">
                            {{ $staffData && $staffData->dob
                                ? \Carbon\Carbon::parse($staffData->dob)->format('d F Y')
                                : '-' }}
                        </p>

                </div>

                <!-- Language Spoken --><div class="detail-item">
                    <p class="detail-label">Language Spoken:</p>
                    <div class="detail-value language-tags">
                        @forelse($staff->languages ?? [] as $language)
                            <span class="tag">{{ $language }}</span>
                        @empty
                            <span class="text-gray-500">No languages specified</span>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Right Section: Profile Image --><div class="profile-area">
                        <div class="profile-container">
                        @if(!empty($staffData->profile_pic) && file_exists(public_path('storage/' . $staffData->profile_pic)))
                            <img 
                                src="{{ asset('storage/' . $staffData->profile_pic) }}" 
                                alt="Profile Picture" 
                                class="profile-image">
                        @else
                            <img 
                                src="{{ asset('profile1.jpg') }}" 
                                alt="Default Profile" 
                                class="profile-image">
                        @endif
                    </div>


            </div>
            
        </div>
    </div>
