<x-filament-panels::page>
    <!-- Ensure CSRF token is available for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        .calendar-container {
            background: #fff;
            backdrop-filter: blur(10px);
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 1900px;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .calendar-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #00000096;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: 180px repeat(7, 1fr);
            border-radius: 12px;
        }
            .calendar-day {
                padding: 0px;
                border: 1px solid rgba(58, 115, 224, 0.57);
                min-height: 140px;
                cursor: pointer;
                background: rgba(255, 255, 255, 0.05);
                transition: transform 0.3s ease, background 0.3s ease;
            }
        .calendar-day:hover {
            transform: scale(1.02);
            background: rgb(20, 139, 209);
            color: #fff;
        }
        .day-header {
            font-weight: 600;
            text-align: center;
            padding: 14px;
            background: linear-gradient(45deg, #2c91ea, #0b89c7);
            color: white;
            font-size: 0.95rem;
        }
        .staff-cell {
            border: 1px solid rgba(4, 168, 248, 0.65);
        }
        .add-staff-cell {
            padding: 16px;
            text-align: center;
        }
        .task {
            background: linear-gradient(45deg, #60a5fa, #a78bfa); /* default purple gradient */
            padding: 8px;
            margin: 8px 0;
            border-radius: 8px;
            font-size: 12px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            height: 90px;
        }

        /* New: style for advanced shift */
            .task-advanced {
                background: linear-gradient(135deg, #4ade80, #22c55e); /* green gradient */
                border-radius: 8px;
                padding: 8px;
                color: #fff;
                font-size: 13px;
            }

            /* client initials circle */
            .task-advanced .client-avatar {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 24px;
                height: 24px;
                margin-right: 4px;
                border-radius: 50%;
                background: #fff;
                color: #22c55e;
                font-size: 11px;
                font-weight: bold;
            }

        .task:hover {
            transform: translateY(-3px);
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: flex-end;
        }
        .task-modal-content, .staff-modal-content {
            background: rgba(255, 255, 255, 0.79);
            backdrop-filter: blur(15px);
            padding: 30px;
            width: 700px;
            max-height: 100vh;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.5s ease-out;
            overflow-y: auto;
            color: #e5e7eb;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
        }
        .staff-modal-content {
            margin: auto;
            animation: popIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        @keyframes popIn {
            from { transform: scale(0.7); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .modal-content h3 {
            margin: 0 0 25px;
            font-size: 1.8rem;
            color: #60a5fa;
            font-weight: 700;
            text-align: center;
            text-shadow: 0 3px 6px rgba(0, 0, 0, 0.3);
        }
        .modal-content div {
            margin-bottom: 15px;
            color: #d1d5db;
            font-size: 1rem;
            font-weight: 600;
        }
        .modal-content input, .modal-content select {
            margin: 5px 0 20px;
            padding: 12px;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.1);
            color: #e5e7eb;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .modal-content input:focus, .modal-content select:focus {
            border-color: #a78bfa;
            box-shadow: 0 0 15px rgba(167, 139, 250, 0.5);
            outline: none;
        }
        .modal-content label {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #d1d5db;
            font-size: 1rem;
        }
        .modal-content label input[type="checkbox"] {
            margin-right: 12px;
            transform: scale(1.3);
        }
        .modal-content .buto {
            margin: 15px 5px 0;
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .modal-content .buto:first-of-type {
            background: linear-gradient(45deg, #10b981, #34d399);
            color: white;
        }
        .modal-content .buto:last-of-type {
            background: linear-gradient(45deg, #ef4444, #f56565);
            color: white;
        }
        .modal-content .buto:hover {
            transform: translateY(-4px);
        }
        .full-view-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            color: #a5b4fc;
            cursor: pointer;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }
        .full-view-btn:hover {
            color: #60a5fa;
        }
        .full-view {
            width: 90vw !important;
            height: 90vh !important;
            border-radius: 20px !important;
            margin: auto !important;
        }
        .buto {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(45deg, #107edf, #03618f);
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .buto:hover {
            transform: translateY(-2px);
        }
        .add-staff-btn {
            background: linear-gradient(45deg, #10b981, #34d399);
            width: 100%;
            padding: 12px;
            font-weight: 600;
        }
        .but-div {
            float: right;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
            margin: 20px auto;
            border: 1px solid #e5e7eb;
            margin-top: 100px;
            width: 100%;
        }
        .card-header {
            background: #f9fafb;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 1rem;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-header .icon {
            font-size: 1.2rem;
            color: #10b981;
        }
        .card-body {
            padding: 16px;
        }
        .form-group {
            margin-bottom: 16px;
            display: flex;
        }
        .form-group label {
            font-weight: 500;
            margin-bottom: 6px;
            color: #374151;
            width: 20%;
            margin-top: 15px;
        }
        .form-group input,
        .form-group select {
            width: 60%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #494747;
            margin-left: 170px;
        }
        .form-groupp input,
        .form-groupp select {
            width: 60%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #494747;
        }
        .funds {
            color: orange;
            background: #FDF6EC;
            font-size: 13px;
            padding: 10px 50px;
            border-radius: 10px;
            margin-left: 225px;
            width: 100%;
            margin-top: 10px;
        }
        .staff-modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 55%;
            margin: auto;
            padding: 65px;
        }
        .staff-heading {
            margin-bottom: 10px;
            color: #222;
        }
        .staff-section-title {
            font-weight: bold;
            margin: 15px 0;
            color: #333;
        }
        .staff-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .staff-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .staff-label {
            font-weight: 500;
            color: #444;
        }
        .staff-input {
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            width: 100%;
            color: #444;
        }
        .staff-flex-row {
            display: flex;
            gap: 10px;
        }
        .staff-flex-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .staff-toggle-btns {
            display: flex;
            gap: 10px;
        }
        .staff-toggle {
            padding: 8px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background: #f9f9f9;
            cursor: pointer;
            color: #444;
        }
        .staff-toggle-active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .staff-check {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            color: #444;
        }
        .staff-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }
        .staff-btn {
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
        }
        .staff-btn-primary {
            background: #007bff;
            color: white;
        }
        .staff-btn-primary:hover {
            background: #0056b3;
        }
        .staff-btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        .staff-btn-fullview {
            background: transparent;
            border: none;
            font-size: 16px;
            float: right;
            cursor: pointer;
        }
        .whiti{
            /* background-color: white; */
        }
        del {
    color: #000000ff; /* Red strikethrough for cancelled shifts */
}
.task-vacant {
                    background: linear-gradient(135deg, #f97316, #facc15);
                    color: white;
                    padding: 8px;
                    margin: 4px 0;
                    border-radius: 4px;
                    cursor: pointer;
                }

                .task-advanced {
                    background: linear-gradient(135deg, #22c55e, #86efac);
                    color: white;
                }

                .client-avatar {
                           width: 24px;
                            height: 24px;
                            background-color: #e5e7eb;
                            color: #f96a04;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 10px;
                            margin-right: 4px;
                            font-weight: 600;
                }
        .slider {
            position: fixed;
            top: 0;
            right: -700px; /* Hidden by default */
            width: 700px;
            height: 100%;
            background-color: #fff;
            box-shadow: -2px 0 5px rgba(0,0,0,0.3);
            transition: right 0.3s ease-in-out;
            z-index: 1000;
            overflow-y: auto;
        }
        .slider.open {
            right: 0; /* Slide in */
        }
        .slider-content {
            padding: 20px;
            
        }
        .close-btn {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }
        .task {
            cursor: pointer;
            padding: 5px;
            margin: 2px 0;
            background-color: #f0f0f0;
            border-radius: 4px;
        }
        .calendar-day {
            min-height: 130px;
            border: 1px solid #ddd;
            padding-bottom: 30px;
        }
        .staff-cell {
            font-weight: bold;
            padding: 10px;
        }
        /* Base style for all selects */
.custom-select {
    appearance: none;               /* remove browser default arrow */
    -webkit-appearance: none;
    -moz-appearance: none;
    background-color: #fff;
    border: 1px solid #d1d5db;      /* gray-300 */
    border-radius: 8px;
    padding: 8px 36px 8px 12px;     /* space for arrow */
    font-size: 14px;
    line-height: 1.4;
    color: #374151;                 /* gray-700 */
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

/* Different sizes */
.custom-select.small {
    width: 120px;
}

.custom-select.large {
    width: 190px;
}

/* Hover & focus */
.custom-select:hover {
    border-color: #9ca3af;          /* gray-400 */
}

.custom-select:focus {
    outline: none;
    border-color: #3b82f6;          /* blue-500 */
    box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
}

/* Custom dropdown arrow */
.custom-select {
    background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px 16px;
}
 .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-btn {
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 6px 12px;
      cursor: pointer;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 6px;
      min-width: 100px;
    }

    /* Small arrow */
    .dropdown-btn::after {
      content: "";
      border: solid #555;
      border-width: 0 2px 2px 0;
      display: inline-block;
      padding: 3px;
      transform: rotate(45deg);
      margin-left: auto;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      top: 110%;
      left: 0;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      width: 220px;
      z-index: 1000;
    }

    .dropdown-content select {
      width: 100%;
      padding: 6px 8px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    .dropdown.show .dropdown-content {
      display: block;
    }
    .today{
            background: #FFFFFF;
            border: 1px #cfcccc groove;
            padding: 7px 25px 6px 21px;
            border-radius: 4px;
            font-size: 14px;
    }
    .calnedr-check{
        background: #FFFFFF;
            border: 1px #cfcccc groove;
            padding: 7px 10px 6px 10px;
            border-radius: 4px;
            font-size: 14px;
    }
    .today:hover{
            background: #d9d9d9;
    }
    ..custom-calendar-btn:hover{
            background: #d9d9d9;
    }
     .task-vacant {
                    background: linear-gradient(135deg, #f97316, #facc15);
                    color: white;
                    padding: 8px;
                    margin: 4px 0;
                    border-radius: 4px;
                    cursor: pointer;
                }

                .task-advanced {
                    background: linear-gradient(135deg, #22c55e, #86efac);
                    color: white;
                }

                .client-avatar {
                           width: 24px;
                            height: 24px;
                            background-color: #e5e7eb;
                            color: #f96a04;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 10px;
                            margin-right: 4px;
                            font-weight: 600;
                }
        .slider {
            position: fixed;
            top: 0;
            right: -700px; /* Hidden by default */
            width: 700px;
            height: 100%;
            background-color: #fff;
            box-shadow: -2px 0 5px rgba(0,0,0,0.3);
            transition: right 0.3s ease-in-out;
            z-index: 1000;
            overflow-y: auto;
        }
        .slider.open {
            right: 0; /* Slide in */
        }
        .slider-content {
            padding: 20px;
            
        }
        .close-btn {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }
        .task {
            cursor: pointer;
            padding: 5px;
            margin: 2px 0;
            background-color: #f0f0f0;
            border-radius: 4px;
        }
        .calendar-day {
            min-height: 130px;
            border: 1px solid #ddd;
            padding-bottom: 30px;
        }
        .staff-cell {
            font-weight: bold;
            padding: 10px;
        }
      .custom-calendar-btn {
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 6px 10px;
      cursor: pointer;
      font-size: 16px;
    }

    .custom-calendar-popup {
      display: none;
      position: absolute;
      margin-top: 8px;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 15px;
      z-index: 1000;
    }

    .custom-calendar-popup input[type="date"] {
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 8px;
      font-size: 14px;
      width: 100%;
    }
    </style>

<div wire:ignore.self x-data="{ calendarType: 'staff' }">
    <!-- Calendar Switcher -->
    <div class="mb-4 flex items-center gap-3" >
        <select id="calendarType" x-model="calendarType" class="custom-select small" style="margin-left: 45px;">
            <option value="staff">👤 Staff</option>
            <option value="client">👤 Client</option>
        </select>

        <select id="status" class="custom-select large">
            <option value="all">⚪ All status</option>
            <option value="Job Board">🟣 Job Board</option>
            <option value="Pending">🔴 Pending</option>
            <option value="Cancelled">🟠 Cancelled</option>
            <option value="Booked">🟢 Booked</option>
            <option value="Approved">🟩 Approved</option>
            <option value="Rejected">❌ Rejected</option>
            <option value="Invoiced">🔵 Invoiced</option>
        </select>

          <div class="dropdown" id="dropdown">
                <button class="dropdown-btn">Filters</button>
                <div class="dropdown-content">
              <select>
                <option value="">All types</option>
                @foreach($this->shiftTypes as $shiftType)
                    <option value="{{ $shiftType->id }}">
                        {{ $shiftType->name }}
                    </option>
                @endforeach
            </select>

                <select>
                    <option>A-Z</option>
                    <option>Shift Counts</option>
                </select>
                </div>
            </div>

    <button class="today">Today</button>

      <button class="custom-calendar-btn" id="customCalendarToggle">📅</button>

  <!-- Calendar Popup -->
  <div class="custom-calendar-popup" id="customCalendarPopup">
    <input type="date" id="customDatePicker">
  </div>

        
            <x-filament::button 
            color="primary" 
            icon="heroicon-m-plus"
            onclick="openModal('shift-modal')"
        >
            Shift
        </x-filament::button>



    </div>

    <!-- Staff Calendar -->
    <div x-show="calendarType === 'staff'" class="calendar-container" id="staff-calendar">
        <div class="calendar-header">
            <button class="buto" onclick="prevWeek()">Previous Week</button>
            <h2 id="week-range"></h2>
            <button class="buto" onclick="nextWeek()">Next Week</button>
        </div>
        <div class="calendar-grid" id="staffCalendar">
            <div class="day-header">Staff</div>
            <div class="day-header" id="day0"></div>
            <div class="day-header" id="day1"></div>
            <div class="day-header" id="day2"></div>
            <div class="day-header" id="day3"></div>
            <div class="day-header" id="day4"></div>
            <div class="day-header" id="day5"></div>
            <div class="day-header" id="day6"></div>
        </div>
    </div>

    <!-- Client Calendar -->
    <div x-show="calendarType === 'client'" class="calendar-container" id="client-calendar">
        <div class="calendar-header">
            <button class="buto" onclick="prevWeek()">Previous Week</button>
            <h2 id="client-week-range"></h2>
            <button class="buto" onclick="nextWeek()">Next Week</button>
        </div>
        <div class="calendar-grid" id="clientCalendar">
            <div class="day-header">Client</div>
            <div class="day-header" id="cday0"></div>
            <div class="day-header" id="cday1"></div>
            <div class="day-header" id="cday2"></div>
            <div class="day-header" id="cday3"></div>
            <div class="day-header" id="cday4"></div>
            <div class="day-header" id="cday5"></div>
            <div class="day-header" id="cday6"></div>
        </div>
    </div>


    <div class="modal" id="taskModal">
        <div class="task-modal-content" id="taskModalContent">
            <div class="whiti">
            <button class="buto full-view-btn" onclick="toggleFullView('taskModalContent')">&#x26F6;</button>
           <a href="{{ route('filament.admin.pages.advanced-shift-form') }}">
            <button class="buto" >Advanced Edit</button>
            </a>
            </div>
            <div x-data="{ repeatChecked: false, jobBoardActive: @entangle('data.add_to_job_board'), recurrance: '' }">
                {{ $this->form }}
            </div>
            <div class="but-div">
                <x-filament::button color="primary" wire:click="createShift">SAVE</x-filament::button>
                <x-filament::button color="danger" onclick="closeModal()">CANCEL</x-filament::button>
            </div>
        </div>
    </div>

    <div class="modal" id="staffModal">
        <div class="staff-modal-content" id="staffModalContent">
            @livewire('app.filament.pages.staff-form-page')
        </div>
    </div>

    <!-- Right-side slider for shift details -->
    <div style="
      background-color:#EFEFEF;
" class="slider" id="shiftSlider" wire:ignore>
        <div class="slider-content">
            <button class="buto close-btn" style="padding: 5px 15px;" onclick="closeSlider()">&times;</button>
            <h2>Shift Details</h2>
               <livewire:shift-details :shift-id="$shiftId" :selected-date="$selectedDate" />
        </div>
    </div>
<script>
let currentDate = new Date();
const users = @json($users ?? []);
const shifts = @json($shifts ?? []);
const clientNames = @json($clientNames ?? []);
const shiftTypeNames = @json($shiftTypeNames ?? []);

console.log('Shift Data:', shifts);

function formatTime(time) {
    if (!time) return '';
    const [hours] = time.split(':').map(Number);
    const period = hours >= 12 ? 'pm' : 'am';
    const formattedHours = hours % 12 || 12;
    return `${formattedHours}${period}`;
}

function getInitials(name) {
    if (!name) return '';
    return name
        .split(' ')
        .map(word => word.charAt(0).toUpperCase())
        .join('');
}

/* ---------------- GLOBAL VARIABLES ---------------- */
let filteredShifts = shifts;
let currentSort = 'A-Z'; // Default sort option

/* ---------------- STAFF CALENDAR ---------------- */
function renderStaffCalendar(filteredShifts = shifts) {
    const calendar = document.getElementById('staffCalendar');
    if (!calendar) return;
    const weekRange = document.getElementById('week-range');

    const startOfWeek = new Date(currentDate);
    startOfWeek.setDate(currentDate.getDate() - currentDate.getDay());
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);

    weekRange.textContent = `${startOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${endOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;

    for (let i = 0; i < 7; i++) {
        const day = new Date(startOfWeek);
        day.setDate(startOfWeek.getDate() + i);
        const dayHeader = document.getElementById(`day${i}`);
        if (dayHeader) {
            dayHeader.textContent = `${day.toLocaleDateString('en-US', { weekday: 'short' })} ${day.getDate()}`;
        }
    }

    while (calendar.children.length > 8) {
        calendar.removeChild(calendar.lastChild);
    }

    const staticTasks = ['Vacant Shift', 'Job Board'];
    staticTasks.forEach(task => {
        const taskCell = document.createElement('div');
        taskCell.className = 'staff-cell';
        taskCell.textContent = task;
        calendar.appendChild(taskCell);

        for (let i = 0; i < 7; i++) {
            const day = new Date(startOfWeek);
            day.setDate(startOfWeek.getDate() + i);
            const dateKey = `${day.getFullYear()}-${(day.getMonth() + 1).toString().padStart(2, '0')}-${day.getDate().toString().padStart(2, '0')}`;
            const dayCell = document.createElement('div');
            dayCell.className = 'calendar-day';

            if (task === 'Vacant Shift') {
                const vacantShifts = filteredShifts.filter(shift => {
                    if (!shift.is_vacant || shift.is_vacant !== 1) return false;

                    const shiftStartDate = new Date(shift.start_date);
                    const shiftEndDate = shift.end_date ? new Date(shift.end_date) : new Date('9999-12-31');
                    const currentDay = new Date(dateKey);

                    if (isNaN(shiftStartDate) || isNaN(shiftEndDate)) return false;
                    if (currentDay < shiftStartDate || currentDay > shiftEndDate) return false;

                    const recurrance = shift.recurrance || 'None';
                    const deltaDays = Math.floor((currentDay - shiftStartDate) / (24 * 60 * 60 * 1000));

                    if (recurrance === 'Daily') {
                        const repeatEveryDaily = parseInt(shift.repeat_every_daily) || 1;
                        return deltaDays % repeatEveryDaily === 0;
                    } else if (recurrance === 'Weekly') {
                        const repeatEveryWeekly = parseInt(shift.repeat_every_weekly) || 1;
                        const deltaWeeks = Math.floor(deltaDays / 7);
                        if (deltaWeeks % repeatEveryWeekly === 0) {
                            const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                            const currentDayName = dayNames[currentDay.getUTCDay()];
                            return shift.occurs_on_weekly && shift.occurs_on_weekly[currentDayName] === true;
                        }
                        return false;
                    } else if (recurrance === 'Monthly') {
                        const repeatEveryMonthly = parseInt(shift.repeat_every_monthly) || 1;
                        const occursOnMonthly = parseInt(shift.occurs_on_monthly);
                        if (isNaN(occursOnMonthly)) return false;
                        const startYear = shiftStartDate.getUTCFullYear();
                        const startMonth = shiftStartDate.getUTCMonth();
                        const currentYear = currentDay.getUTCFullYear();
                        const currentMonth = currentDay.getUTCMonth();
                        const monthsDelta = (currentYear - startYear) * 12 + (currentMonth - startMonth);
                        if (monthsDelta % repeatEveryMonthly === 0) {
                            return currentDay.getUTCDate() === occursOnMonthly;
                        }
                        return false;
                    }
                    return shift.start_date === dateKey && (!shift.repeat || recurrance === 'None');
                });

                if (vacantShifts.length > 0) {
                    const groupedShifts = {};
                    vacantShifts.forEach(shift => {
                        if (!groupedShifts[shift.id]) {
                            groupedShifts[shift.id] = {
                                ...shift,
                                clientIds: shift.is_advanced_shift ? (shift.clientIds || []) : [shift.client_id]
                            };
                        } else {
                            groupedShifts[shift.id].clientIds.push(...(shift.is_advanced_shift ? (shift.clientIds || []) : [shift.client_id]));
                        }
                    });

                    Object.values(groupedShifts).forEach(shift => {
                        const taskDiv = document.createElement('div');
                        taskDiv.className = 'task task-vacant';
                        if (shift.is_advanced_shift) {
                            taskDiv.classList.add('task-advanced-vacant');
                        }
                        const timeRange = shift.start_time && shift.end_time
                            ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                            : 'No Time';
                        const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Unknown Shift';
                        const formattedShiftType = shift.is_cancelled === true ? `<del>${shiftType}</del>` : shiftType;

                        if (shift.is_advanced_shift) {
                            let clientIds = Array.isArray(shift.clientIds) ? shift.clientIds : [shift.clientIds];
                            let clientCount = 0;
                            const header = document.createElement("div");
                            header.innerHTML = `<strong>${timeRange}</strong> ${formattedShiftType}`;
                            taskDiv.appendChild(header);
                            const clientsWrapper = document.createElement("div");
                            clientsWrapper.style.display = "flex";
                            clientsWrapper.style.alignItems = "center";
                            clientsWrapper.style.marginTop = "4px";
                            clientIds.forEach(id => {
                                const clientName = clientNames[String(id)] || "Unknown";
                                const initials = getInitials(clientName);
                                const avatar = document.createElement("div");
                                avatar.classList.add("client-avatar");
                                avatar.textContent = initials;
                                clientsWrapper.appendChild(avatar);
                                clientCount++;
                            });
                            const countSpan = document.createElement("span");
                            countSpan.textContent = `${clientCount} Clients`;
                            countSpan.style.marginLeft = "6px";
                            countSpan.style.fontSize = "10px";
                            clientsWrapper.appendChild(countSpan);
                            taskDiv.appendChild(clientsWrapper);
                        } else {
                            const clientName = clientNames[String(shift.client_id)] || 'Unknown Client';
                            taskDiv.innerHTML = `${timeRange}<br>${formattedShiftType}<br>${clientName}`;
                        }

                        if (shift.is_approved === 1) {
                            const checkIcon = document.createElement('span');
                            checkIcon.innerHTML = '✔'; // Unicode for checkmark
                            checkIcon.style.color = 'green';
                            checkIcon.style.marginLeft = '5px';
                            taskDiv.appendChild(checkIcon);
                        }

                        taskDiv.onclick = (e) => {
                            e.stopPropagation();
                            openShiftSlider(shift.id, dateKey);
                        };
                        dayCell.appendChild(taskDiv);
                    });
                }
            }

            if (task === 'Job Board') {
                const jobBoardShifts = filteredShifts.filter(shift => {
                    if (!shift.add_to_job_board || shift.add_to_job_board !== true) return false;

                    const shiftStartDate = new Date(shift.start_date);
                    const shiftEndDate = shift.end_date ? new Date(shift.end_date) : new Date('9999-12-31');
                    const currentDay = new Date(dateKey);

                    if (isNaN(shiftStartDate) || isNaN(shiftEndDate)) return false;
                    if (currentDay < shiftStartDate || currentDay > shiftEndDate) return false;

                    const recurrance = shift.recurrance || 'None';
                    const deltaDays = Math.floor((currentDay - shiftStartDate) / (24 * 60 * 60 * 1000));

                    if (recurrance === 'Daily') {
                        const repeatEveryDaily = parseInt(shift.repeat_every_daily) || 1;
                        return deltaDays % repeatEveryDaily === 0;
                    } else if (recurrance === 'Weekly') {
                        const repeatEveryWeekly = parseInt(shift.repeat_every_weekly) || 1;
                        const deltaWeeks = Math.floor(deltaDays / 7);
                        if (deltaWeeks % repeatEveryWeekly === 0) {
                            const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                            const currentDayName = dayNames[currentDay.getUTCDay()];
                            return shift.occurs_on_weekly && shift.occurs_on_weekly[currentDayName] === true;
                        }
                        return false;
                    } else if (recurrance === 'Monthly') {
                        const repeatEveryMonthly = parseInt(shift.repeat_every_monthly) || 1;
                        const occursOnMonthly = parseInt(shift.occurs_on_monthly);
                        if (isNaN(occursOnMonthly)) return false;
                        const startYear = shiftStartDate.getUTCFullYear();
                        const startMonth = shiftStartDate.getUTCMonth();
                        const currentYear = currentDay.getUTCFullYear();
                        const currentMonth = currentDay.getUTCMonth();
                        const monthsDelta = (currentYear - startYear) * 12 + (currentMonth - startMonth);
                        if (monthsDelta % repeatEveryMonthly === 0) {
                            return currentDay.getUTCDate() === occursOnMonthly;
                        }
                        return false;
                    }
                    return shift.start_date === dateKey && (!shift.repeat || recurrance === 'None');
                });

                if (jobBoardShifts.length > 0) {
                    const groupedShifts = {};
                    jobBoardShifts.forEach(shift => {
                        if (!groupedShifts[shift.id]) {
                            groupedShifts[shift.id] = {
                                ...shift,
                                clientIds: shift.is_advanced_shift ? (shift.clientIds || []) : [shift.client_id]
                            };
                        } else {
                            groupedShifts[shift.id].clientIds.push(...(shift.is_advanced_shift ? (shift.clientIds || []) : [shift.client_id]));
                        }
                    });

                    Object.values(groupedShifts).forEach(shift => {
                        const taskDiv = document.createElement('div');
                        taskDiv.className = 'task';
                        if (shift.is_advanced_shift) {
                            taskDiv.classList.add('task-advanced');
                        }
                        const timeRange = shift.start_time && shift.end_time
                            ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                            : 'No Time';
                        const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Unknown Shift';
                        const formattedShiftType = shift.is_cancelled === true ? `<del>${shiftType}</del>` : shiftType;

                        if (shift.is_advanced_shift) {
                            let clientIds = Array.isArray(shift.clientIds) ? shift.clientIds : [shift.clientIds];
                            let clientCount = 0;
                            const header = document.createElement("div");
                            header.innerHTML = `<strong>${timeRange}</strong> ${formattedShiftType}`;
                            taskDiv.appendChild(header);
                            const clientsWrapper = document.createElement("div");
                            clientsWrapper.style.display = "flex";
                            clientsWrapper.style.alignItems = "center";
                            clientsWrapper.style.marginTop = "4px";
                            clientIds.forEach(id => {
                                const clientName = clientNames[String(id)] || "Unknown";
                                const initials = getInitials(clientName);
                                const avatar = document.createElement("div");
                                avatar.classList.add("client-avatar");
                                avatar.textContent = initials;
                                clientsWrapper.appendChild(avatar);
                                clientCount++;
                            });
                            const countSpan = document.createElement("span");
                            countSpan.textContent = `${clientCount} Clients`;
                            countSpan.style.marginLeft = "6px";
                            countSpan.style.fontSize = "10px";
                            clientsWrapper.appendChild(countSpan);
                            taskDiv.appendChild(clientsWrapper);
                        } else {
                            const clientName = clientNames[String(shift.client_id)] || 'Unknown Client';
                            taskDiv.innerHTML = `${timeRange}<br>${formattedShiftType}<br>${clientName}`;
                        }

                        if (shift.is_approved === 1) {
                            const checkIcon = document.createElement('span');
                            checkIcon.innerHTML = '✔'; // Unicode for checkmark
                            checkIcon.style.color = 'green';
                            checkIcon.style.marginLeft = '5px';
                            taskDiv.appendChild(checkIcon);
                        }

                        taskDiv.onclick = (e) => {
                            e.stopPropagation();
                            openShiftSlider(shift.id, dateKey);
                        };
                        dayCell.appendChild(taskDiv);
                    });
                }
            }

            dayCell.onclick = () => openModal(`${task}_${dateKey}`, dateKey);
            calendar.appendChild(dayCell);
        }
    });

    // Sort users based on currentSort
    let sortedUsers = Object.entries(users);
    if (currentSort === 'Shift Counts') {
        sortedUsers.sort((a, b) => {
            const countA = filteredShifts.filter(shift => shift.user_id === a[0]).length;
            const countB = filteredShifts.filter(shift => shift.user_id === b[0]).length;
            return countB - countA; // Descending order by shift count
        });
    } else {
        sortedUsers.sort((a, b) => a[1].localeCompare(b[1])); // Alphabetical A-Z
    }

    sortedUsers.forEach(([userId, userName]) => {
        const staffCell = document.createElement('div');
        staffCell.className = 'staff-cell';
        staffCell.textContent = userName;
        calendar.appendChild(staffCell);

        for (let i = 0; i < 7; i++) {
            const day = new Date(startOfWeek);
            day.setDate(startOfWeek.getDate() + i);
            const dateKey = `${day.getFullYear()}-${(day.getMonth() + 1).toString().padStart(2, '0')}-${day.getDate().toString().padStart(2, '0')}`;
            const dayCell = document.createElement('div');
            dayCell.className = 'calendar-day';

            // filter shifts for this user + day
            const userShifts = filteredShifts.filter(shift => {
                if (!shift || shift.user_id != userId) return false;

                const shiftStartDate = new Date(shift.start_date);
                const shiftEndDate = shift.end_date ? new Date(shift.end_date) : new Date('9999-12-31');
                const currentDay = new Date(dateKey);
                if (currentDay < shiftStartDate || currentDay > shiftEndDate) return false;

                const recurrance = shift.recurrance || 'None';
                const deltaDays = Math.floor((currentDay - shiftStartDate) / (24 * 60 * 60 * 1000));

                if (recurrance === 'Daily') {
                    const repeatEveryDaily = parseInt(shift.repeat_every_daily) || 1;
                    return deltaDays % repeatEveryDaily === 0;
                } else if (recurrance === 'Weekly') {
                    const repeatEveryWeekly = parseInt(shift.repeat_every_weekly) || 1;
                    const deltaWeeks = Math.floor(deltaDays / 7);
                    if (deltaWeeks % repeatEveryWeekly === 0) {
                        const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                        const currentDayName = dayNames[currentDay.getUTCDay()];
                        return shift.occurs_on_weekly && shift.occurs_on_weekly[currentDayName] === true;
                    }
                    return false;
                } else if (recurrance === 'Monthly') {
                    const repeatEveryMonthly = parseInt(shift.repeat_every_monthly) || 1;
                    const occursOnMonthly = parseInt(shift.occurs_on_monthly);
                    if (isNaN(occursOnMonthly)) return false;
                    const startYear = shiftStartDate.getUTCFullYear();
                    const startMonth = shiftStartDate.getUTCMonth();
                    const currentYear = currentDay.getUTCFullYear();
                    const currentMonth = currentDay.getUTCMonth();
                    const monthsDelta = (currentYear - startYear) * 12 + (currentMonth - startMonth);
                    return monthsDelta % repeatEveryMonthly === 0 && currentDay.getUTCDate() === occursOnMonthly;
                }
                return shift.start_date === dateKey;
            });

            if (userShifts.length > 0) {
                const groupedUserShifts = {};
                userShifts.forEach(shift => {
                    if (!groupedUserShifts[shift.id]) {
                        groupedUserShifts[shift.id] = {
                            ...shift,
                            clientIds: shift.is_advanced_shift ? (shift.clientIds || []) : [shift.client_id]
                        };
                    } else {
                        groupedUserShifts[shift.id].clientIds.push(...(shift.is_advanced_shift ? (shift.clientIds || []) : [shift.client_id]));
                    }
                });

                Object.values(groupedUserShifts).forEach(shift => {
                    const taskDiv = document.createElement('div');
                    taskDiv.className = 'task';
                    if (shift.is_advanced_shift) {
                        taskDiv.classList.add('task-advanced');
                    }
                    const timeRange = shift.start_time && shift.end_time ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}` : 'No Time';
                    const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Unknown Shift';
                    const formattedShiftType = shift.is_cancelled === true ? `<del>${shiftType}</del>` : shiftType;

                    if (shift.is_advanced_shift) {
                        let clientIds = Array.isArray(shift.clientIds) ? shift.clientIds : [shift.clientIds];
                        let clientCount = 0;
                        const header = document.createElement("div");
                        header.innerHTML = `<strong>${timeRange}</strong> ${formattedShiftType}`;
                        taskDiv.appendChild(header);
                        const clientsWrapper = document.createElement("div");
                        clientsWrapper.style.display = "flex";
                        clientsWrapper.style.alignItems = "center";
                        clientsWrapper.style.marginTop = "4px";
                        clientIds.forEach(id => {
                            const clientName = clientNames[String(id)] || "Unknown";
                            const initials = getInitials(clientName);
                            const avatar = document.createElement("div");
                            avatar.classList.add("client-avatar");
                            avatar.textContent = initials;
                            clientsWrapper.appendChild(avatar);
                            clientCount++;
                        });
                        const countSpan = document.createElement("span");
                        countSpan.textContent = `${clientCount} Clients`;
                        countSpan.style.marginLeft = "6px";
                        countSpan.style.fontSize = "10px";
                        clientsWrapper.appendChild(countSpan);
                        taskDiv.appendChild(clientsWrapper);
                    } else {
                        const clientName = clientNames[String(shift.client_id)] || 'Unknown Client';
                        taskDiv.innerHTML = `${timeRange}<br>${formattedShiftType}<br>${clientName}`;
                    }

                    if (shift.is_approved === 1) {
                        const checkIcon = document.createElement('span');
                        checkIcon.innerHTML = '✔'; // Unicode for checkmark
                        checkIcon.style.color = 'green';
                        checkIcon.style.marginLeft = '5px';
                        taskDiv.appendChild(checkIcon);
                    }

                    taskDiv.onclick = (e) => {
                        e.stopPropagation();
                        openShiftSlider(shift.id, dateKey);
                    };
                    dayCell.appendChild(taskDiv);
                });
            }

            dayCell.onclick = () => openModal(`${userName}_${dateKey}`, dateKey);
            calendar.appendChild(dayCell);
        }
    });

    const addStaffCell = document.createElement('div');
    addStaffCell.className = 'add-staff-cell';
    addStaffCell.innerHTML = `<button class="add-staff-btn" onclick="openStaffModal()">Add Staff</button>`;
    calendar.appendChild(addStaffCell);
}

/* ---------------- CLIENT CALENDAR ---------------- */
function renderClientCalendar(filteredShifts = shifts) {
    const calendar = document.getElementById('clientCalendar');
    if (!calendar) return;
    const weekRange = document.getElementById('client-week-range');

    const startOfWeek = new Date(currentDate);
    startOfWeek.setDate(currentDate.getDate() - currentDate.getDay());
    const endOfWeek = new Date(startOfWeek);
    endOfWeek.setDate(startOfWeek.getDate() + 6);

    weekRange.textContent = `${startOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${endOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;

    for (let i = 0; i < 7; i++) {
        const day = new Date(startOfWeek);
        day.setDate(startOfWeek.getDate() + i);
        const dayHeader = document.getElementById(`cday${i}`);
        if (dayHeader) {
            dayHeader.textContent = `${day.toLocaleDateString('en-US', { weekday: 'short' })} ${day.getDate()}`;
        }
    }

    while (calendar.children.length > 8) {
        calendar.removeChild(calendar.lastChild);
    }

    const staticTasks = ['Vacant Shift'];
    staticTasks.forEach(task => {
        const taskCell = document.createElement('div');
        taskCell.className = 'staff-cell';
        taskCell.textContent = task;
        calendar.appendChild(taskCell);

        for (let i = 0; i < 7; i++) {
            const day = new Date(startOfWeek);
            day.setDate(startOfWeek.getDate() + i);
            const dateKey = `${day.getFullYear()}-${(day.getMonth() + 1).toString().padStart(2, '0')}-${day.getDate().toString().padStart(2, '0')}`;
            const dayCell = document.createElement('div');
            dayCell.className = 'calendar-day';

            if (task === 'Vacant Shift') {
                const vacantShifts = filteredShifts.filter(shift => {
                    if (!shift.is_vacant || shift.is_vacant !== 1) return false;

                    const shiftStartDate = new Date(shift.start_date);
                    const shiftEndDate = shift.end_date ? new Date(shift.end_date) : new Date('9999-12-31');
                    const currentDay = new Date(dateKey);

                    if (isNaN(shiftStartDate) || isNaN(shiftEndDate)) return false;
                    if (currentDay < shiftStartDate || currentDay > shiftEndDate) return false;

                    const recurrance = shift.recurrance || 'None';
                    const deltaDays = Math.floor((currentDay - shiftStartDate) / (24 * 60 * 60 * 1000));

                    if (recurrance === 'Daily') {
                        const repeatEveryDaily = parseInt(shift.repeat_every_daily) || 1;
                        return deltaDays % repeatEveryDaily === 0;
                    } else if (recurrance === 'Weekly') {
                        const repeatEveryWeekly = parseInt(shift.repeat_every_weekly) || 1;
                        const deltaWeeks = Math.floor(deltaDays / 7);
                        if (deltaWeeks % repeatEveryWeekly === 0) {
                            const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                            const currentDayName = dayNames[currentDay.getUTCDay()];
                            return shift.occurs_on_weekly && shift.occurs_on_weekly[currentDayName] === true;
                        }
                        return false;
                    } else if (recurrance === 'Monthly') {
                        const repeatEveryMonthly = parseInt(shift.repeat_every_monthly) || 1;
                        const occursOnMonthly = parseInt(shift.occurs_on_monthly);
                        if (isNaN(occursOnMonthly)) return false;
                        const startYear = shiftStartDate.getUTCFullYear();
                        const startMonth = shiftStartDate.getUTCMonth();
                        const currentYear = currentDay.getUTCFullYear();
                        const currentMonth = currentDay.getUTCMonth();
                        const monthsDelta = (currentYear - startYear) * 12 + (currentMonth - startMonth);
                        if (monthsDelta % repeatEveryMonthly === 0) {
                            return currentDay.getUTCDate() === occursOnMonthly;
                        }
                        return false;
                    }
                    return shift.start_date === dateKey && (!shift.repeat || recurrance === 'None');
                });

                if (vacantShifts.length > 0) {
                    const groupedShifts = {};
                    vacantShifts.forEach(shift => {
                        const parsedClientSection = typeof shift.client_section === 'string' ? JSON.parse(shift.client_section) : shift.client_section || {};
                        const clientIdsFromSection = Array.isArray(parsedClientSection.client_id) ? parsedClientSection.client_id.map(id => String(id)) : [];
                        const clientIds = shift.is_advanced_shift ? clientIdsFromSection : [String(shift.client_id || '')].filter(id => id);

                        if (!groupedShifts[shift.id]) {
                            groupedShifts[shift.id] = {
                                ...shift,
                                clientIds: clientIds
                            };
                        } else {
                            const newIds = clientIds.filter(id => !groupedShifts[shift.id].clientIds.includes(id));
                            groupedShifts[shift.id].clientIds.push(...newIds);
                        }
                    });

                    Object.values(groupedShifts).forEach(shift => {
                        const taskDiv = document.createElement('div');
                        taskDiv.className = 'task task-vacant';
                        if (shift.is_advanced_shift) {
                            taskDiv.classList.add('task-advanced-vacant');
                        }
                        const timeRange = shift.start_time && shift.end_time
                            ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                            : 'No Time';
                        const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Unknown Shift';
                        const formattedShiftType = shift.is_cancelled === true ? `<del>${shiftType}</del>` : shiftType;

                        if (shift.is_advanced_shift) {
                            let clientIds = Array.isArray(shift.clientIds) ? shift.clientIds : [shift.clientIds];
                            let clientCount = 0;
                            const header = document.createElement("div");
                            header.innerHTML = `<strong>${timeRange}</strong> ${formattedShiftType}`;
                            taskDiv.appendChild(header);
                            const clientsWrapper = document.createElement("div");
                            clientsWrapper.style.display = "flex";
                            clientsWrapper.style.alignItems = "center";
                            clientsWrapper.style.marginTop = "4px";
                            clientIds.forEach(id => {
                                const clientName = clientNames[String(id)] || "Unknown";
                                const initials = getInitials(clientName);
                                const avatar = document.createElement("div");
                                avatar.classList.add("client-avatar");
                                avatar.textContent = initials;
                                clientsWrapper.appendChild(avatar);
                                clientCount++;
                            });
                            const countSpan = document.createElement("span");
                            countSpan.textContent = `${clientCount} Clients`;
                            countSpan.style.marginLeft = "6px";
                            countSpan.style.fontSize = "10px";
                            clientsWrapper.appendChild(countSpan);
                            taskDiv.appendChild(clientsWrapper);
                        } else {
                            const clientName = clientNames[String(shift.client_id)] || 'Unknown Client';
                            taskDiv.innerHTML = `${timeRange}<br>${formattedShiftType}<br>${clientName}`;
                        }


                           if (shift.is_approved === 1) {
                        const checkIcon = document.createElement('span');
                        checkIcon.innerHTML = '✔'; // Unicode for checkmark
                        checkIcon.style.color = 'green';
                        checkIcon.style.marginLeft = '5px';
                        taskDiv.appendChild(checkIcon);
                    }
                        taskDiv.onclick = (e) => {
                            e.stopPropagation();
                            openShiftSlider(shift.id, dateKey);
                        };
                        dayCell.appendChild(taskDiv);
                    });
                }
            }

            dayCell.onclick = () => openModal(`${task}_${dateKey}`, dateKey);
            calendar.appendChild(dayCell);
        }
    });

    // Sort clients based on currentSort
    let sortedClients = Object.entries(clientNames);
    if (currentSort === 'Shift Counts') {
        sortedClients.sort((a, b) => {
            const countA = filteredShifts.filter(shift => {
                let parsedClientSection;
                try {
                    parsedClientSection = typeof shift.client_section === 'string' ? JSON.parse(shift.client_section) : shift.client_section || {};
                } catch (e) {
                    parsedClientSection = {};
                }
                const clientIdsFromSection = Array.isArray(parsedClientSection.client_id) ? parsedClientSection.client_id.map(id => String(id)) : [];
                return String(shift.client_id) === a[0] || clientIdsFromSection.includes(a[0]);
            }).length;
            const countB = filteredShifts.filter(shift => {
                let parsedClientSection;
                try {
                    parsedClientSection = typeof shift.client_section === 'string' ? JSON.parse(shift.client_section) : shift.client_section || {};
                } catch (e) {
                    parsedClientSection = {};
                }
                const clientIdsFromSection = Array.isArray(parsedClientSection.client_id) ? parsedClientSection.client_id.map(id => String(id)) : [];
                return String(shift.client_id) === b[0] || clientIdsFromSection.includes(b[0]);
            }).length;
            return countB - countA; // Descending order by shift count
        });
    } else {
        sortedClients.sort((a, b) => a[1].localeCompare(b[1])); // Alphabetical A-Z
    }

    sortedClients.forEach(([clientId, clientName]) => {
        const clientCell = document.createElement('div');
        clientCell.className = 'staff-cell';
        clientCell.textContent = clientName;
        calendar.appendChild(clientCell);

        for (let i = 0; i < 7; i++) {
            const day = new Date(startOfWeek);
            day.setDate(startOfWeek.getDate() + i);
            const dateKey = `${day.getFullYear()}-${(day.getMonth() + 1).toString().padStart(2, '0')}-${day.getDate().toString().padStart(2, '0')}`;
            const dayCell = document.createElement('div');
            dayCell.className = 'calendar-day';

            // filter shifts for this client + day
            const clientShifts = filteredShifts.filter(shift => {
                let parsedClientSection;
                try {
                    parsedClientSection = typeof shift.client_section === 'string' ? JSON.parse(shift.client_section) : shift.client_section || {};
                } catch (e) {
                    parsedClientSection = {};
                }
                const clientIdsFromSection = Array.isArray(parsedClientSection.client_id) ? parsedClientSection.client_id.map(id => String(id)) : [];
                const clientMatch = String(shift.client_id) === String(clientId) || clientIdsFromSection.includes(String(clientId));

                if (!clientMatch) return false;

                const shiftStartDate = new Date(shift.start_date);
                const shiftEndDate = shift.end_date ? new Date(shift.end_date) : new Date('9999-12-31');
                const currentDay = new Date(dateKey);
                if (currentDay < shiftStartDate || currentDay > shiftEndDate) return false;

                const recurrance = shift.recurrance || 'None';
                const deltaDays = Math.floor((currentDay - shiftStartDate) / (24 * 60 * 60 * 1000));

                if (recurrance === 'Daily') {
                    const repeatEveryDaily = parseInt(shift.repeat_every_daily) || 1;
                    return deltaDays % repeatEveryDaily === 0;
                } else if (recurrance === 'Weekly') {
                    const repeatEveryWeekly = parseInt(shift.repeat_every_weekly) || 1;
                    const deltaWeeks = Math.floor(deltaDays / 7);
                    if (deltaWeeks % repeatEveryWeekly === 0) {
                        const dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                        const currentDayName = dayNames[currentDay.getUTCDay()];
                        return shift.occurs_on_weekly && shift.occurs_on_weekly[currentDayName] === true;
                    }
                    return false;
                } else if (recurrance === 'Monthly') {
                    const repeatEveryMonthly = parseInt(shift.repeat_every_monthly) || 1;
                    const occursOnMonthly = parseInt(shift.occurs_on_monthly);
                    if (isNaN(occursOnMonthly)) return false;
                    const startYear = shiftStartDate.getUTCFullYear();
                    const startMonth = shiftStartDate.getUTCMonth();
                    const currentYear = currentDay.getUTCFullYear();
                    const currentMonth = currentDay.getUTCMonth();
                    const monthsDelta = (currentYear - startYear) * 12 + (currentMonth - startMonth);
                    return monthsDelta % repeatEveryMonthly === 0 && currentDay.getUTCDate() === occursOnMonthly;
                }
                return shift.start_date === dateKey;
            });

            if (clientShifts.length > 0) {
                const groupedShifts = {};
                clientShifts.forEach(shift => {
                    let parsedCarerSection;
                    try {
                        parsedCarerSection = typeof shift.carer_section === 'string' ? JSON.parse(shift.carer_section) : shift.carer_section || {};
                    } catch (e) {
                        parsedCarerSection = {};
                    }
                    const userIdsFromSection = Array.isArray(parsedCarerSection.user_id) ? parsedCarerSection.user_id.map(id => String(id)) : [];
                    const userIds = shift.is_advanced_shift ? userIdsFromSection : [String(shift.user_id || '')].filter(id => id);

                    if (!groupedShifts[shift.id]) {
                        groupedShifts[shift.id] = {
                            ...shift,
                            userIds: userIds
                        };
                    } else {
                        const newIds = userIds.filter(id => !groupedShifts[shift.id].userIds.includes(id));
                        groupedShifts[shift.id].userIds.push(...newIds);
                    }
                });

                Object.values(groupedShifts).forEach(shift => {
                    const taskDiv = document.createElement('div');
                    taskDiv.className = 'task';
                    if (shift.is_advanced_shift) {
                        taskDiv.classList.add('task-advanced');
                    }
                    const timeRange = shift.start_time && shift.end_time
                        ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                        : 'No Time';
                    const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Unknown Shift';
                    const formattedShiftType = shift.is_cancelled === true ? `<del>${shiftType}</del>` : shiftType;

                    if (shift.is_advanced_shift) {
                        let userIds = Array.isArray(shift.userIds) ? shift.userIds : [shift.userIds];
                        let userCount = 0;
                        const header = document.createElement("div");
                        header.innerHTML = `<strong>${timeRange}</strong> ${formattedShiftType}`;
                        taskDiv.appendChild(header);
                        const usersWrapper = document.createElement("div");
                        usersWrapper.style.display = "flex";
                        usersWrapper.style.alignItems = "center";
                        usersWrapper.style.marginTop = "4px";
                        userIds.forEach(id => {
                            const userName = users[String(id)] || "Unassigned";
                            const initials = getInitials(userName);
                            const avatar = document.createElement("div");
                            avatar.classList.add("client-avatar");
                            avatar.textContent = initials;
                            usersWrapper.appendChild(avatar);
                            userCount++;
                        });
                        const countSpan = document.createElement("span");
                        countSpan.textContent = `${userCount} Staff`;
                        countSpan.style.marginLeft = "6px";
                        countSpan.style.fontSize = "10px";
                        usersWrapper.appendChild(countSpan);
                        taskDiv.appendChild(usersWrapper);
                    } else {
                        const staffName = users[String(shift.user_id)] || 'Unassigned';
                        taskDiv.innerHTML = `<strong>${timeRange}</strong><br>${formattedShiftType}<br>${staffName}`;
                    }

                    taskDiv.onclick = (e) => {
                        e.stopPropagation();
                        openShiftSlider(shift.id, dateKey);
                    };
                    dayCell.appendChild(taskDiv);
                });
            }

            dayCell.onclick = () => openModal(`${clientName}_${dateKey}`, dateKey);
            calendar.appendChild(dayCell);
        }
    });
}

function highlightToday() {
    const startOfWeek = new Date(currentDate);
    startOfWeek.setDate(currentDate.getDate() - currentDate.getDay());
    for (let i = 0; i < 7; i++) {
        const day = new Date(startOfWeek);
        day.setDate(startOfWeek.getDate() + i);
        const dayHeader = document.getElementById(`day${i}`);
        const cdayHeader = document.getElementById(`cday${i}`);
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Normalize to midnight for comparison
        if (day.toDateString() === today.toDateString()) {
            if (dayHeader) {
                dayHeader.classList.add('today-highlight');
                console.log(`Highlighting day${i} as today`);
            }
            if (cdayHeader) {
                cdayHeader.classList.add('today-highlight');
                console.log(`Highlighting cday${i} as today`);
            }
        } else {
            if (dayHeader) dayHeader.classList.remove('today-highlight');
            if (cdayHeader) cdayHeader.classList.remove('today-highlight');
        }
    }
}

document.querySelector('.today').addEventListener('click', function() {
    currentDate = new Date();
    highlightToday();
    renderStaffCalendar(filteredShifts);
    renderClientCalendar(filteredShifts);
}); 


document.addEventListener('DOMContentLoaded', () => {
    // Existing initialization code (e.g., renderStaffCalendar, renderClientCalendar)
    renderStaffCalendar();
    renderClientCalendar();

    const customBtn = document.createElement('button');

    const customPopup = document.createElement('div');
    customPopup.className = 'custom-calendar-popup';
    customPopup.id = 'customCalendarPopup';
    customPopup.innerHTML = '<input type="date" id="customDatePicker">';
    document.body.appendChild(customPopup);

    const customDatePicker = document.getElementById('customDatePicker');

    customBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        customPopup.style.display = customPopup.style.display === 'block' ? 'none' : 'block';
        const rect = customBtn.getBoundingClientRect();
        customPopup.style.left = rect.left + 'px';
        customPopup.style.top = rect.bottom + 'px';
    });

    document.addEventListener('click', (e) => {
        if (!customPopup.contains(e.target) && e.target !== customBtn) {
            customPopup.style.display = 'none';
        }
    });

    customDatePicker.addEventListener('change', function(e) {
        const selectedDate = new Date(e.target.value);
        if (selectedDate) {
            currentDate = selectedDate;
            currentDate.setDate(currentDate.getDate() - currentDate.getDay()); // Set to start of week
            highlightToday();
            renderStaffCalendar(filteredShifts);
            renderClientCalendar(filteredShifts);
            customPopup.style.display = 'none'; // Hide popup after selection
        }
    });
});


/* ---------------- SHARED FUNCTIONS ---------------- */
function prevWeek() {
    currentDate.setDate(currentDate.getDate() - 7);
    renderStaffCalendar(filteredShifts);
    renderClientCalendar(filteredShifts);
}

function nextWeek() {
    currentDate.setDate(currentDate.getDate() + 7);
    renderStaffCalendar(filteredShifts);
    renderClientCalendar(filteredShifts);
}

function openShiftSlider(shiftId, dateKey) {
    console.log('Opening slider for shift:', shiftId, 'on date:', dateKey);
    Livewire.dispatch('set-shift-details', { shiftId: shiftId, selectedDate: dateKey });
    const slider = document.getElementById('shiftSlider');
    if (slider) slider.classList.add('open');
}

function closeSlider() {
    const slider = document.getElementById('shiftSlider');
    if (slider) slider.classList.remove('open');
    Livewire.dispatch('set-shift-details', { shiftId: null, selectedDate: null });
}

function openModal(key, dateKey) {
    console.log('Key:', key, 'DateKey:', dateKey);
    const formattedDate = dateKey;
    fetch('/set-selected-date', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ dateKey: formattedDate })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Livewire.dispatch('refresh-and-open-modal');
        }
    })
    .catch(error => console.error('Error setting session:', error));

    const modal = document.getElementById('taskModal');
    if (modal) modal.style.display = 'flex';
}

function closeModal() {
    const modal = document.getElementById('taskModal');
    if (modal) modal.style.display = 'none';
}

function openStaffModal() {
    const modal = document.getElementById('staffModal');
    if (modal) modal.style.display = 'flex';
}

function closeStaffModal() {
    const modal = document.getElementById('staffModal');
    if (modal) modal.style.display = 'none';
}

function toggleFullView(modalId) {
    const modalContent = document.getElementById(modalId);
    if (modalContent) modalContent.classList.toggle('full-view');
}

const taskModalEl = document.getElementById('taskModal');
if (taskModalEl) {
    taskModalEl.addEventListener('click', function(event) {
        if (event.target === this) closeModal();
    });
}

const staffModalEl = document.getElementById('staffModal');
if (staffModalEl) {
    staffModalEl.addEventListener('click', function(event) {
        if (event.target === this) closeStaffModal();
    });
}

const shiftSliderEl = document.getElementById('shiftSlider');
if (shiftSliderEl) {
    shiftSliderEl.addEventListener('click', function(event) {
        if (event.target === this) closeSlider();
    });
}

document.addEventListener('livewire:initialized', () => {
    Livewire.on('open-task-modal', () => {
        console.log('Received open-task-modal event');
        const modal = document.getElementById('taskModal');
        if (modal) modal.style.display = 'flex';
    });

    Livewire.on('set-shift-details', ({ shiftId, selectedDate }) => {
        console.log('Set shift details:', { shiftId, selectedDate });
        Livewire.dispatch('updateShift', { shiftId, selectedDate });
    });
});

/* ---------------- FILTER AND SORT FUNCTIONS ---------------- */
document.getElementById('status').addEventListener('change', function() {
    const selectedStatus = this.value;
    filteredShifts = selectedStatus !== 'all' ? shifts.filter(shift => shift.status === selectedStatus) : shifts;
    applyFiltersAndSort();
});

document.querySelector('#dropdown select:nth-child(1)').addEventListener('change', function() {
    const selectedShiftType = this.value;
    filteredShifts = selectedShiftType ? shifts.filter(shift => String(shift.shift_type_id) === selectedShiftType) : shifts;
    applyFiltersAndSort();
});

document.querySelector('#dropdown select:nth-child(2)').addEventListener('change', function() {
    currentSort = this.value;
    applyFiltersAndSort();
});

function applyFiltersAndSort() {
    let tempShifts = filteredShifts;
    const selectedShiftType = document.querySelector('#dropdown select:nth-child(1)').value;
    if (selectedShiftType) {
        tempShifts = tempShifts.filter(shift => String(shift.shift_type_id) === selectedShiftType);
    }
    const selectedStatus = document.getElementById('status').value;
    if (selectedStatus !== 'all') {
        tempShifts = tempShifts.filter(shift => shift.status === selectedStatus);
    }
    filteredShifts = tempShifts;

    renderStaffCalendar(filteredShifts);
    renderClientCalendar(filteredShifts);
}

/* ---------------- INIT ---------------- */
document.addEventListener('DOMContentLoaded', () => {
    renderStaffCalendar();
    renderClientCalendar();
});
</script>
<script>
    const dropdown = document.getElementById("dropdown");
    const button = dropdown.querySelector(".dropdown-btn");

    button.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });

    document.addEventListener("click", (e) => {
      if (!dropdown.contains(e.target)) {
        dropdown.classList.remove("show");
      }
    });
</script>
 <script>
    const customBtn = document.getElementById("customCalendarToggle");
    const customPopup = document.getElementById("customCalendarPopup");

    customBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      customPopup.style.display = customPopup.style.display === "block" ? "none" : "block";
      const rect = customBtn.getBoundingClientRect();
      customPopup.style.left = rect.left + "px";
      customPopup.style.top = rect.bottom + "px";
    });

    document.addEventListener("click", (e) => {
      if (!customPopup.contains(e.target) && e.target !== customBtn) {
        customPopup.style.display = "none";
      }
    });
  </script>
</x-filament-panels::page>
