<x-filament-panels::page>
    <!-- Ensure CSRF token is available for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

        .calendar-container {
            background: #fff;
            backdrop-filter: blur(10px);
            padding: 10px;
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
            font-size: 20px;
            font-weight: 700;
            color: #00000096;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .calendar-grid {
            display: grid;
            border-radius: 12px;
        }
        .calendar-grid.daily {
            grid-template-columns: 180px repeat(var(--hour-count), 0fr);
        }
        .calendar-grid.weekly {
            grid-template-columns: 180px repeat(7, 1fr);
        }
        .calendar-grid.fortnightly {
            grid-template-columns: 180px repeat(14, 0.5fr);
        }
        .calendar-day {
            padding: 0px;
            border: 1px solid rgba(58, 115, 224, 0.57);
            min-height: 140px;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease, background 0.3s ease;
            position: relative; /* For Daily view positioning */
        }
        .calendar-day:hover {
            transform: scale(1);
            background: rgba(0, 0, 0, 0.12);
            color: #fff;
        }
        .day-header {
            font-weight: 600;
            text-align: center;
            padding: 14px;
            background: #151A2D;
            color: white;
            font-size: 11px;
    height: 43px;
        }
        .staff-cell {
            border: 1px solid rgba(4, 168, 248, 0.65);
        }
        .add-staff-cell {
            padding: 16px;
            text-align: center;
        }
        .task {
            padding: 8px;
            margin: 8px 0;
            border-radius: 8px;
            font-size: 13px;
            color: #161414;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
            height: 80px;
            width: auto;
        }
        .task-vacant {
            background: #ffc164 !important;
            color: #121212;
            padding: 8px;
            margin: 4px 0;
            border-radius: 4px;
            cursor: pointer;
        }
        .task-advanced {
            background: #e3ffaf !important;
            color: #121212;
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
            padding: 7px 15px;
            border: none;
            background: #151A2D;
            color: white;
            font-size: 11px;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .buto:hover {
            transform: translateY(-2px);
        }
        .add-staff-btn {
            background: linear-gradient(45deg, #88AC46, #4f7011);
            font-weight: 600;
            font-size: 12px;
            padding: 8px 19px;
            color: white;
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
        .whiti {
            /* background-color: white; */
        }
        del {
            color: #000000ff;
        }
        .slider {
            position: fixed;
            top: 0;
            right: -700px;
            width: 700px;
            height: 100%;
            background-color: #fff;
            box-shadow: -2px 0 5px rgba(0,0,0,0.3);
            transition: right 0.3s ease-in-out;
            z-index: 1000;
            overflow-y: auto;
        }
        .slider.open {
            right: 0;
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
            background-color: #c9f3ff;
            border-radius: 0px;
        }
        .calendar-day {
                    min-height: auto;
                    border: 1px solid #ddd;
                    padding-bottom: 30px;
                 
        }
        .staff-cell {
              font-weight: 500;
                padding: 13px;
                font-size: 11px;
                background: #00000012;
                color: black;
                border: 1px #00000045 groove;
  width: auto;

        }
        .custom-select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-color: #fff;
            border: 1px solid #d1d5db;
            padding: 8px 36px 8px 12px;
            font-size: 14px;
            line-height: 1.4;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .custom-select.small {
              width: auto;
    font-size: 10px;
        }
        .custom-select.large {
                width: auto;
    font-size: 10px;
        }
        .custom-select:hover {
            border-color: #9ca3af;
        }
        .custom-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
        }
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
            padding: 7px 12px;
            cursor: pointer;
            font-size: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            width: auto;
        }
        .dropdown-btn::after {
            content: "";
            border: solid #555;
            border-width: 0 1px 1px 0;
            display: inline-block;
            transform: rotate(45deg);
            margin-left: auto;
            padding: 2px;
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
                padding: 2px 8px;
                margin-bottom: 10px;
                border: 1px solid #ccc;
                border-radius: 6px;
                font-size: 10px;
        }
        .dropdown.show .dropdown-content {
            display: block;
        }
        .today {
    background: #FFFFFF;
    border: 1px #cfcccc groove;
    padding: 6px 15px 6px 15px;
    font-size: 10px;
        }
        .calnedr-check {
            background: #FFFFFF;
            border: 1px #cfcccc groove;
            padding: 7px 10px 6px 10px;
            border-radius: 4px;
            font-size: 14px;
        }
        .today:hover {
            background: #d9d9d9;
        }
        .custom-calendar-btn:hover {
            background: #d9d9d9;
        }
        .custom-calendar-btn {
            background: #fff;
            border: 1px solid #ccc;
            padding: 6px 10px;
            cursor: pointer;
            font-size: 10px;
        }
        .custom-calendar-popup {
            display: none;
            position: absolute;
            margin-top: 8px;
            background: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            z-index: 1000;
        }
        .custom-calendar-popup input[type="date"] {
            border: 1px solid #ccc;
            padding: 8px;
            font-size: 14px;
            width: 100%;
        }
        .task.daily {
            position: absolute;
            margin: 2px 0;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        /* Daily timeline container row that spans the hour columns */
.daily-row {
    grid-column: 2 / -1; /* span across all hour columns after the left label column */
    min-height: 58px;
    border-bottom: 1px solid #eef2f7;
    position: relative;
    background: transparent;
    box-sizing: border-box;
   height: 70px;
}

/* timeline wrapper for each staff/client row in daily mode */
.timeline-wrapper {
    position: relative;
    width: 100%;
    height: 46px; /* row height for tasks */
    overflow: visible;
}

/* single timeline shift block */
.task.daily {
position: absolute;
  top: 4px;
  padding: 7px 15px 0px 13px;
  font-size: 13px;
  color: #202020;
  box-shadow: 0 6px 18px rgba(24, 24, 24, 0.12);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  height: 80px;
}

/* small visual difference when vacant or advanced */
.task.task-vacant.daily { background: linear-gradient(135deg, #f97316, #facc15); color: #111; }
.task.task-advanced.daily { background: linear-gradient(45deg, #22c55e, #86efac); color: #111; }
.task.daily.default { background: #c9f3ff; }
/* === FIX SCROLL + LAYOUT OVERFLOW === */
.calendar-section {
    width: 100%;
    background: white;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 1rem;
}

/* Make calendar scroll horizontally within the section */
.calendar-scroll {
    overflow-x: auto;
    overflow-y: hidden;
    width: 100%;
    padding-bottom: 10px;
}

/* Prevent header cells (time slots / days) from overflowing */
.calendar-grid {
    display: grid;
    min-width: max-content;
}

/* Scrollbar styling */
.calendar-scroll::-webkit-scrollbar {
    height: 8px;
}
.calendar-scroll::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.25);
    border-radius: 4px;
}
.calendar-scroll::-webkit-scrollbar-track {
    background: transparent;
}

/* Optional small visual polish */
.day-header {
background-color: #151A2D;
    color: white;
    font-weight: 600;
    text-align: center;
    border-right: 1px solid #e5e7eb;
    padding: 15px 15px;
    white-space: nowrap;
    font-size: 9px;
    height: 43px;

}
.vacant-staff-label{
    font-weight: 500;
padding: 20px;
  font-size: 11px;
  background: #F56954;
  color: white;
  border: 1px #00000045 groove;
  width: auto;
}

.jobboard-staff-label {
    font-weight: 500;
padding: 20px;
  font-size: 11px;
  background: #7879F1;
  color: white;
  width: auto;
  border: 1px #00000045 groove;
}
.day-header-staff{
background: #151A2D;
  color: white;
  font-weight: 600;
  text-align: center;
  border-right: 1px solid #e5e7eb;
  padding: 15px 15px;
  white-space: nowrap;
  width: auto;
      font-size: 11px;
    height: 43px;
}


/* badge base */
.label-badge {
display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 8px;
    font-weight: 700;
}

.vacant-staff-label-badge {
    background-color: #fff;
    color: #F56954;
}

.jobboard-staff-label-badge {
    background-color: white;    
    color: #7879F1;
}

.label-text {
    flex: 1;
    white-space: nowrap;
    padding-left: 5px;
}
/* 🧑‍💼 Staff/User label style */
.user-staff-label  {
    background-color: #f3f4f6; /* light gray */
    color: #111827;
}

/* Circular badge for staff initials */
.user-staff-label-badge {
 background-color: #4c4d51;
  color: white;
}

/* Client label style (similar to staff) */
.client-staff-label {
    background-color: #f3f4f6; /* light gray */
    color: #111827;
}

/* Circular badge for client initials */
.client-staff-label-badge {
 background-color: #4c4d51;
  color: white;
}
.main-content-sidebar{
    left: 200px !important;
    padding-right: 200px !important;
}
body.sidebar-collapsed .main-content-sidebar {
  left: 52px !important;
  padding-right: 50px !important;
}
@media (min-width: 640px) {
  .sm\:text-3xl {
    margin-left: 15px;
  }
}

#calendarWrapper.daily .calendar-day {
    overflow: visible !important;
    height: 110px; /* can adjust */
    position: relative;
}

#calendarWrapper.daily .task {
    position: absolute;
    left: 0;
    right: 0;
}

#calendarWrapper.weekly .calendar-day {
    overflow: visible !important;
    height: auto;
    position: relative;
}

#calendarWrapper.weekly .task {
    position: relative;
    width: 100% !important;
}

#calendarWrapper.fortnightly .calendar-day {
    overflow: hidden !important;    /* <-- Fix overflow fully */
    height: auto;                  /* adjust as needed */
    position: relative;
}

#calendarWrapper.fortnightly .task {
    position: relative;
    white-space: normal;
    overflow: hidden;
    text-overflow: ellipsis;
    padding: 4px 6px;
    font-size: 11px !important;
}
.task.task-overnight { background-color: #c9f3ff !important; }

    </style>

    <div wire:ignore.self x-data="{ calendarType: 'staff', viewType: 'Weekly' }">
        <!-- Calendar Switcher -->
        <div class="mb-4 flex items-center gap-3" style="margin-left: 12px;">
            <select id="calendarType" x-model="calendarType" class="custom-select small">
                <option value="staff">👤 Staff</option>
                <option value="client">👤 Client</option>
            </select>

            <select id="viewType" x-model="viewType" class="custom-select small">
                <option value="Daily">Daily</option>
                <option value="Weekly">Weekly</option>
                <option value="Fortnightly">Fortnightly</option>
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
                    <select id="shiftTypeFilter">
                        <option value="">All types</option>
                        @foreach($this->shiftTypes as $shiftType)
                            <option value="{{ $shiftType->id }}">{{ $shiftType->name }}</option>
                        @endforeach
                    </select>
                    <select id="sortFilter">
                        <option value="A-Z">A-Z</option>
                        <option value="Shift Counts">Shift Counts</option>
                    </select>
                </div>
            </div>

            <button class="today" id="todayBtn">Today</button>

            <button class="custom-calendar-btn" id="customCalendarToggle">📅</button>

            <x-filament::button 
                color="primary" 
                icon="heroicon-m-plus"
                onclick="openModal('shift-modal')"
                size="sm"
            >
                Shift
            </x-filament::button>

            <!-- Calendar Popup -->
            <div class="custom-calendar-popup" id="customCalendarPopup">
                <input type="date" id="customDatePicker">
            </div>
        </div>
            <div id="calendarWrapper" :class="viewType.toLowerCase()">
                    <!-- Staff Calendar -->
                    <div x-show="calendarType === 'staff'" class="calendar-container" id="staff-calendar">
                        <div class="calendar-header">
                            <button class="buto" onclick="prevPeriod()">Previous</button>
                            <h2 id="week-range"></h2>
                            <button class="buto" onclick="nextPeriod()">Next</button>
                        </div>
                            <div class="calendar-section">
                                <div class="calendar-scroll">
                                    <div id="staffCalendar" class="calendar-grid" :class="viewType.toLowerCase()">
                                        <!-- Headers + rows populated dynamically -->
                                    </div>
                                </div>
                            </div>
                    </div>
            </div>


        <!-- Client Calendar -->
        <div x-show="calendarType === 'client'" class="calendar-container" id="client-calendar">
            <div class="calendar-header">
                <button class="buto" onclick="prevPeriod()">Previous</button>
                <h2 id="client-week-range"></h2>
                <button class="buto" onclick="nextPeriod()">Next</button>
            </div>
            <div class="calendar-section">
                <div class="calendar-scroll">
                    <div id="clientCalendar" class="calendar-grid" :class="viewType.toLowerCase()">
                        <!-- Headers + rows populated dynamically -->
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="taskModal">
            <div class="task-modal-content" id="taskModalContent">
                <div class="whiti">
                    <button class="buto full-view-btn" onclick="toggleFullView('taskModalContent')">&#x26F6;</button>
                    <a href="{{ route('filament.admin.pages.advanced-shift-form') }}">
                        <button class="buto">Advanced Edit</button>
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
        <div style="background-color:#EFEFEF;" class="slider" id="shiftSlider" wire:ignore>
            <div class="slider-content">
                <button class="buto close-btn" style="padding: 5px 15px;" onclick="closeSlider()">&times;</button>
                <h2>Shift Details</h2>
                <livewire:shift-details :shift-id="$shiftId" :selected-date="$selectedDate" />
            </div>
        </div>

        <script>
            const users = @json($users ?? []);
            const shifts = @json($shifts ?? []);
            const clientNames = @json($clientNames ?? []);
            const shiftTypeNames = @json($shiftTypeNames ?? []);

            let currentDate = new Date();
            let filteredShifts = shifts;
            let currentSort = 'A-Z';
            const DAY_START_HOUR = 0; // 4 AM
            const DAY_END_HOUR = 23; // 11 PM
            const TOTAL_HOURS = (DAY_END_HOUR - DAY_START_HOUR) + 1;

            function formatTime(time) {
                if (!time) return '';
                const [hours, minutes] = time.split(':').map(Number);
                const period = hours >= 12 ? 'pm' : 'am';
                const formattedHours = hours % 12 || 12;
                return `${formattedHours}:${minutes.toString().padStart(2, '0')}${period}`;
            }

            function getInitials(name) {
                if (!name) return '';
                return name
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase())
                    .join('');
            }

            function getDailyTimeSlots() {
                const slots = [];
                for (let h = DAY_START_HOUR; h <= DAY_END_HOUR; h++) {
                    slots.push(`${h % 12 || 12}:00 ${h >= 12 ? 'PM' : 'AM'}`);
                }
                return slots;
            }

            function getWeekStart(date) {
                const d = new Date(date);
                d.setDate(d.getDate() - d.getDay());
                return d;
            }

            function getPeriodDates(viewType, date) {
                const d = new Date(date);
                let startDate, endDate;
                if (viewType === 'Daily') {
                    startDate = new Date(d);
                    endDate = new Date(d);
                } else if (viewType === 'Weekly') {
                    startDate = getWeekStart(d);
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 6);
                } else { // Fortnightly
                    startDate = getWeekStart(d);
                    endDate = new Date(startDate);
                    endDate.setDate(startDate.getDate() + 13);
                }
                return { startDate, endDate };
            }

            function isShiftInDateRange(shift, dateKey) {
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
                    return monthsDelta % repeatEveryMonthly === 0 && currentDay.getUTCDate() === occursOnMonthly;
                }
                return shift.start_date === dateKey;
            }

function renderStaffCalendar(filteredShifts = shifts) {
    const calendar = document.getElementById('staffCalendar');
    if (!calendar) return;
    const weekRange = document.getElementById('week-range');
    const viewType = document.getElementById('viewType').value;

    const { startDate, endDate } = getPeriodDates(viewType, currentDate);
    const dates = [];

    // local helper (safe to be here; won't conflict with global if defined there)
    function isOvernightShift(shift) {
        return !!shift.shift_finishes_next_day ||
               (!!shift.start_time && !!shift.end_time && shift.end_time < shift.start_time);
    }

    calendar.innerHTML = '<div class="day-header-staff">Staff</div>';

    if (viewType === 'Daily') {
        // ----- DAILY VIEW -----
        weekRange.textContent = startDate.toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric', year: 'numeric' });
        const timeSlots = getDailyTimeSlots();
        const dayCount = timeSlots.length;
        calendar.className = 'calendar-grid daily';
        calendar.style.setProperty('--hour-count', dayCount);

        timeSlots.forEach((slot, i) => {
            const header = document.createElement('div');
            header.className = 'day-header';
            header.textContent = slot;
            calendar.appendChild(header);
        });

        // Render static rows

        const staticRows = ['Vacant Shift', 'Job Board'];

        staticRows.forEach(taskName => {
            // 🔹 distinct label + short code for badge
            const labelClass =
                taskName === 'Vacant Shift'
                    ? 'vacant-staff-label'
                    : 'jobboard-staff-label';

            const shortCode =
                taskName === 'Vacant Shift'
                    ? 'VS'
                    : 'JB';

            // create label cell
            const labelCell = document.createElement('div');
            labelCell.className = `staff-cell ${labelClass}`;
            labelCell.innerHTML = `
                <span class="label-badge ${labelClass}-badge">${shortCode}</span>
                <span class="label-text">${taskName}</span>
            `;
            calendar.appendChild(labelCell);

            // timeline cell (no color)
            const timelineCell = document.createElement('div');
            timelineCell.className = 'calendar-day daily-row';
            timelineCell.onclick = handleEmptyCalendarClick;

            const wrapper = document.createElement('div');
            wrapper.className = 'timeline-wrapper';
            timelineCell.appendChild(wrapper);

            const dateKey = formatDateKey(startDate);
            const relevant = filteredShifts.filter(s => {
                if (taskName === 'Vacant Shift' && !s.is_vacant) return false;
                if (taskName === 'Job Board' && !s.add_to_job_board) return false;
                return isShiftInDateRange(s, dateKey);
            });

            relevant.forEach(shift => {
                const { startMinutes, durationMinutes, totalMinutes } = calculateShiftPosition(shift, startDate);

                let cls = 'task daily default';
                if (shift.is_vacant) cls = 'task daily task-vacant';
                else if (shift.add_to_job_board) cls = 'task daily task-jobboard';
                else if (shift.is_advanced_shift) cls = 'task daily task-advanced';

                const taskDiv = document.createElement('div');
                taskDiv.className = cls;
                taskDiv.style.left = `${(startMinutes / totalMinutes) * 100}%`;
                taskDiv.style.width = `${Math.min((durationMinutes / totalMinutes) * 100, 100)}%`;

                const timeRange = shift.start_time && shift.end_time
                    ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                    : 'No Time';
                const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Shift';
                const clientName = clientNames[String(shift.client_id)] || '';

                taskDiv.innerHTML = `
                    <strong>${timeRange}</strong>
                    <div>${shiftType}</div>
                    <div class="small-text">${clientName}</div>
                `;
                taskDiv.onclick = e => { e.stopPropagation(); openShiftSlider(shift.id, dateKey); };

                wrapper.appendChild(taskDiv);
            });

            calendar.appendChild(timelineCell);
        });



        // 🧑‍💼 Staff rows
        const sortedUsers = sortUsersBy(viewType, filteredShifts);

        sortedUsers.forEach(([userId, userName]) => {
            // 🔹 Create initials from the user name (e.g. "Junaid Afzal" → "JA")
            const initials = userName
                .split(' ')
                .map(w => w[0])
                .join('')
                .substring(0, 2)
                .toUpperCase();

            // 🟢 Create the label cell with badge + name
            const staffCell = document.createElement('div');
            staffCell.className = 'staff-cell user-staff-label';
            staffCell.innerHTML = `
                <span class="label-badge user-staff-label-badge">${initials}</span>
                <span class="label-text">${userName}</span>
            `;
            calendar.appendChild(staffCell);

            // 🔹 Create the timeline cell for this staff row
            const timelineCell = document.createElement('div');
            timelineCell.className = 'calendar-day daily-row';
            timelineCell.onclick = handleEmptyCalendarClick;

            const wrapper = document.createElement('div');
            wrapper.className = 'timeline-wrapper';
            timelineCell.appendChild(wrapper);

            const dateKey = formatDateKey(startDate);
            const userShifts = filteredShifts.filter(
                s => String(s.user_id) === String(userId) && isShiftInDateRange(s, dateKey)
            );

            // Group by shift id to avoid duplicates
            const grouped = Object.values(Object.fromEntries(userShifts.map(s => [s.id, s])));

            grouped.forEach(shift => {
                const { startMinutes, durationMinutes, totalMinutes } = calculateShiftPosition(shift, startDate);

                const taskDiv = document.createElement('div');
                let cls = 'task daily default';
                if (shift.is_vacant) cls = 'task daily task-vacant';
                else if (shift.is_advanced_shift) cls = 'task daily task-advanced';
                else if (shift.add_to_job_board) cls = 'task daily task-jobboard';
                taskDiv.className = cls;

                taskDiv.style.left = `${(startMinutes / totalMinutes) * 100}%`;
                taskDiv.style.width = `${Math.min((durationMinutes / totalMinutes) * 100, 100)}%`;

                const timeRange =
                    shift.start_time && shift.end_time
                        ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                        : 'No Time';
                const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Shift';
                const clientName = clientNames[String(shift.client_id)] || '';

                taskDiv.innerHTML = `
                    <strong>${timeRange}</strong>
                    <div>${shiftType}</div>
                    <div class="small-text">${clientName}</div>
                `;
                taskDiv.onclick = e => {
                    e.stopPropagation();
                    openShiftSlider(shift.id, dateKey);
                };

                wrapper.appendChild(taskDiv);
            });

            calendar.appendChild(timelineCell);
        });


        const addStaffCell = document.createElement('div');
        addStaffCell.className = 'add-staff-cell';
        addStaffCell.innerHTML = `
            <button class="add-staff-btn" onclick="openStaffModal()">Add Staff</button>
        `;
        calendar.appendChild(addStaffCell);

    } else {
        // ----- WEEKLY / FORTNIGHTLY -----
        const dayCount = viewType === 'Weekly' ? 7 : 14;
        let day = new Date(startDate);
        while (day <= endDate) { dates.push(new Date(day)); day.setDate(day.getDate() + 1); }
        weekRange.textContent = `${formatDateShort(startDate)} - ${formatDateShort(endDate)}`;
        calendar.innerHTML = '<div class="day-header-staff">Staff</div>';

        // pending map stores next-day DOM pieces keyed by row & date
        const pendingOvernight = {};

        dates.forEach((d, i) => {
            const header = document.createElement('div');
            header.className = 'day-header';
            header.textContent = `${d.toLocaleDateString('en-US', { weekday: 'short' })} ${d.getDate()}`;
            calendar.appendChild(header);
        });
        calendar.className = `calendar-grid ${viewType.toLowerCase()}`;

        const staticTasks = ['Vacant Shift', 'Job Board'];

        staticTasks.forEach(taskName => {
            // 🔹 distinct label color and short code for badge
            const labelClass =
                taskName === 'Vacant Shift'
                    ? 'vacant-staff-label'
                    : 'jobboard-staff-label';

            const shortCode =
                taskName === 'Vacant Shift'
                    ? 'VS'
                    : 'JB';

            // 🟢 Create left label with circular badge
            const staffCell = document.createElement('div');
            staffCell.className = `staff-cell ${labelClass}`;
            staffCell.innerHTML = `
                <span class="label-badge ${labelClass}-badge">${shortCode}</span>
                <span class="label-text">${taskName}</span>
            `;
            calendar.appendChild(staffCell);

            // 🔹 create day cells for this row
            dates.forEach(d => {
                const dayCell = document.createElement('div');
                dayCell.className = 'calendar-day';
                dayCell.onclick = handleEmptyCalendarClick;

                const dateKey = formatDateKey(d);
                // mark row/date on cell (used for pending appends)
                dayCell.setAttribute('data-date', dateKey);
                dayCell.setAttribute('data-row', `static__${taskName}`);

                const relevant = filteredShifts.filter(s => {
                    if (taskName === 'Vacant Shift' && !s.is_vacant) return false;
                    if (taskName === 'Job Board' && !s.add_to_job_board) return false;
                    return isShiftInDateRange(s, dateKey);
                });

                relevant.forEach(shift => {
                    // normal (non-overnight)
                    if (!isOvernightShift(shift)) {
                        const div = document.createElement('div');
                        let cls = 'task default';
                        if (shift.is_vacant) cls = 'task task-vacant';
                        else if (shift.add_to_job_board) cls = 'task task-jobboard';
                        else if (shift.is_advanced_shift) cls = 'task task-advanced';
                        div.className = cls;

                        const clientName = clientNames[String(shift.client_id)] || '';
                        const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Shift';
                        const timeRange = shift.start_time && shift.end_time
                            ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                            : 'No Time';

                        div.innerHTML = `
                            <strong>${timeRange}</strong><br>
                            ${shiftType}<br>
                            <small>${clientName}</small>
                        `;

                        div.onclick = e => { e.stopPropagation(); openShiftSlider(shift.id, dateKey); };
                        dayCell.appendChild(div);
                        return;
                    }

                    // Overnight shift: render today's portion now, schedule tomorrow's portion
                    // PART 1: today's portion (start -> MIDNIGHT)
                    const part1 = document.createElement('div');
                    let cls1 = 'task task-overnight';
                    if (shift.is_vacant) cls1 = 'task task-vacant';
                    else if (shift.add_to_job_board) cls1 = 'task task-jobboard';
                    else if (shift.is_advanced_shift) cls1 = 'task task-advanced';
                    part1.className = cls1 + ' overnight-start';

                    part1.innerHTML = `
                        <strong>${formatTime(shift.start_time)} - NEXT DAY -</strong><br>
                        ${shiftTypeNames[String(shift.shift_type_id)] || 'Shift'}<br>
                    `;
                    part1.onclick = e => { e.stopPropagation(); openShiftSlider(shift.id, dateKey); };
                    dayCell.appendChild(part1);

                    // PART 2: create element for next day and store in pending map
                    const nextDay = new Date(d);
                    nextDay.setDate(d.getDate() + 1);
                    const nextDateKey = formatDateKey(nextDay);

                    const part2 = document.createElement('div');
                    part2.className = cls1 + ' overnight-end';
                    part2.innerHTML = `
                        <strong>${formatTime(shift.end_time)}</strong><br>
                        <small>${clientNames[String(shift.client_id)] || ''}</small>
                    `;
                    part2.onclick = e => { e.stopPropagation(); openShiftSlider(shift.id, nextDateKey); };

                    const pendingKey = `static__${taskName}__${nextDateKey}`;
                    if (!pendingOvernight[pendingKey]) pendingOvernight[pendingKey] = [];
                    pendingOvernight[pendingKey].push(part2);
                });

                // After adding today's shifts, append any pending continuation parts for this row/date
                const pendingHereKey = `static__${taskName}__${dateKey}`;
                if (pendingOvernight[pendingHereKey]) {
                    pendingOvernight[pendingHereKey].forEach(node => dayCell.appendChild(node));
                    delete pendingOvernight[pendingHereKey];
                }

                calendar.appendChild(dayCell);
            });
        });



        const sortedUsers = sortUsersBy(viewType, filteredShifts);

        sortedUsers.forEach(([userId, userName]) => {
            // 🟢 Create initials from user name (e.g., "Junaid Afzal" → "JA")
            const initials = userName
                .split(' ')
                .map(w => w[0])
                .join('')
                .substring(0, 2)
                .toUpperCase();

            // 🟩 Create the left label cell with badge + name
            const staffCell = document.createElement('div');
            staffCell.className = 'staff-cell user-staff-label';
            staffCell.innerHTML = `
                <span class="label-badge user-staff-label-badge">${initials}</span>
                <span class="label-text">${userName}</span>
            `;
            calendar.appendChild(staffCell);

            // 🔹 Create day cells for this user row
            dates.forEach(d => {
                const dayCell = document.createElement('div');
                dayCell.className = 'calendar-day';
                dayCell.onclick = handleEmptyCalendarClick;

                const dateKey = formatDateKey(d);
                // mark row/date on cell (used for pending appends)
                dayCell.setAttribute('data-date', dateKey);
                dayCell.setAttribute('data-row', `user__${userId}`);

                const userShifts = filteredShifts.filter(
                    s => String(s.user_id) === String(userId) && isShiftInDateRange(s, dateKey)
                );

                userShifts.forEach(shift => {
                    // Non-overnight: render as before
                    if (!isOvernightShift(shift)) {
                        const div = document.createElement('div');
                        let cls = 'task default';
                        if (shift.is_vacant) cls = 'task task-vacant';
                        else if (shift.add_to_job_board) cls = 'task task-jobboard';
                        else if (shift.is_advanced_shift) cls = 'task task-advanced';
                        div.className = cls;

                        const clientName = clientNames[String(shift.client_id)] || '';
                        const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Shift';
                        const timeRange = shift.start_time && shift.end_time
                            ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                            : 'No Time';

                        div.innerHTML = `
                            <strong>${timeRange}</strong><br>
                            ${shiftType}<br>
                            <small>${clientName}</small>
                        `;

                        div.onclick = e => { e.stopPropagation(); openShiftSlider(shift.id, dateKey); };
                        dayCell.appendChild(div);
                        return;
                    }

                    // Overnight: today's portion (start -> MIDNIGHT)
                    const part1 = document.createElement('div');
                    let cls1 = 'task task-overnight';
                    if (shift.is_vacant) cls1 = 'task task-vacant';
                    else if (shift.add_to_job_board) cls1 = 'task task-jobboard';
                    else if (shift.is_advanced_shift) cls1 = 'task task-advanced';
                    part1.className = cls1 + ' overnight-start';

                    part1.innerHTML = `
                        <strong>${formatTime(shift.start_time)} - NEXT DAY -</strong><br>
                        ${shiftTypeNames[String(shift.shift_type_id)] || 'Shift'}<br>
                    `;
                    part1.onclick = e => { e.stopPropagation(); openShiftSlider(shift.id, dateKey); };
                    dayCell.appendChild(part1);

                    // schedule next-day part
                    const nextDay = new Date(d);
                    nextDay.setDate(d.getDate() + 1);
                    const nextDateKey = formatDateKey(nextDay);

                    const part2 = document.createElement('div');
                    part2.className = cls1 + ' overnight-end';
                    part2.innerHTML = `
                        <strong> ${formatTime(shift.end_time)}</strong><br>
                        <small>${clientNames[String(shift.client_id)] || ''}</small>
                    `;
                    part2.onclick = e => { e.stopPropagation(); openShiftSlider(shift.id, nextDateKey); };

                    const pendingKey = `user__${userId}__${nextDateKey}`;
                    if (!pendingOvernight[pendingKey]) pendingOvernight[pendingKey] = [];
                    pendingOvernight[pendingKey].push(part2);
                });

                // append any pending continuation parts for this user/date
                const pendingHereKey = `user__${userId}__${dateKey}`;
                if (pendingOvernight[pendingHereKey]) {
                    pendingOvernight[pendingHereKey].forEach(node => dayCell.appendChild(node));
                    delete pendingOvernight[pendingHereKey];
                }

                calendar.appendChild(dayCell);
            });
        });
        const addStaffCell = document.createElement('div');
        addStaffCell.className = 'add-staff-cell';
        addStaffCell.innerHTML = `
            <button class="add-staff-btn" onclick="openStaffModal()">Add Staff</button>
        `;
        calendar.appendChild(addStaffCell);
    }
}



function renderClientCalendar(filteredShifts = shifts) {
    const calendar = document.getElementById('clientCalendar');
    if (!calendar) return;
    const weekRange = document.getElementById('client-week-range');
    const viewType = document.getElementById('viewType').value;
    const { startDate, endDate } = getPeriodDates(viewType, currentDate);
    const dates = [];

    calendar.innerHTML = '<div class="day-header-staff">Client</div>';

    if (viewType === 'Daily') {
        weekRange.textContent = startDate.toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric', year: 'numeric' });
        const timeSlots = getDailyTimeSlots();
        calendar.className = 'calendar-grid daily';
        calendar.style.setProperty('--hour-count', timeSlots.length);

        timeSlots.forEach(slot => {
            const header = document.createElement('div');
            header.className = 'day-header';
            header.textContent = slot;
            calendar.appendChild(header);
        });

        // 🟥 Vacant row with circular badge
        const labelCell = document.createElement('div');
        labelCell.className = 'staff-cell vacant-staff-label';
        labelCell.innerHTML = `
            <span class="label-badge vacant-staff-label-badge">VS</span>
            <span class="label-text">Vacant Shift</span>
        `;
        calendar.appendChild(labelCell);

        const timelineVacant = document.createElement('div');
        timelineVacant.className = 'calendar-day daily-row';
        timelineVacant.onclick = handleEmptyCalendarClick;

        const wrapVacant = document.createElement('div');
        wrapVacant.className = 'timeline-wrapper';
        timelineVacant.appendChild(wrapVacant);

        const dateKey = formatDateKey(startDate);
        const vacantShifts = filteredShifts.filter(
            s => s.is_vacant && isShiftInDateRange(s, dateKey)
        );

        vacantShifts.forEach(shift => {
            const { startMinutes, durationMinutes, totalMinutes } = calculateShiftPosition(shift, startDate);
            const div = document.createElement('div');
            div.className = 'task task-vacant daily';
            div.style.left = `${(startMinutes / totalMinutes) * 100}%`;
            div.style.width = `${Math.min((durationMinutes / totalMinutes) * 100, 100)}%`;

            const staffName = users[String(shift.user_id)] || '';
            const shiftType = shiftTypeNames[String(shift.shift_type_id)] || '';
            const timeRange = shift.start_time && shift.end_time
                ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                : 'No Time';

            div.innerHTML = `
                <strong>${timeRange}</strong><br>
                ${shiftType}<br>
            `;

            div.onclick = e => { e.stopPropagation(); openShiftSlider(shift.id, dateKey); };
            wrapVacant.appendChild(div);
        });

        calendar.appendChild(timelineVacant);


        calendar.appendChild(timelineVacant);

        // Clients
  const sortedClients = sortClientsBy(viewType, filteredShifts);

sortedClients.forEach(([clientId, clientName]) => { 
    // 🔹 Create initials (e.g. "Adam Care" → "AC")
    const initials = clientName
        .split(' ')
        .map(w => w[0])
        .join('')
        .substring(0, 2)
        .toUpperCase();

    // 🟢 Create left label with badge + client name
    const clientCell = document.createElement('div');
    clientCell.className = 'staff-cell client-staff-label';
    clientCell.innerHTML = `
        <span class="label-badge client-staff-label-badge">${initials}</span>
        <span class="label-text">${clientName}</span>
    `;
    calendar.appendChild(clientCell);

    // 🔹 Create the timeline row
    const row = document.createElement('div');
    row.className = 'calendar-day daily-row';
    row.onclick = handleEmptyCalendarClick;

    const wrapper = document.createElement('div');
    wrapper.className = 'timeline-wrapper';
    row.appendChild(wrapper);

    const clientShifts = filteredShifts
        .filter(s => {
            try {
                const parsed = typeof s.client_section === 'string'
                    ? JSON.parse(s.client_section)
                    : s.client_section || {};
                const ids = Array.isArray(parsed.client_id)
                    ? parsed.client_id.map(String)
                    : [];
                return String(s.client_id) === String(clientId) || ids.includes(String(clientId));
            } catch {
                return String(s.client_id) === String(clientId);
            }
        })
        .filter(s => isShiftInDateRange(s, dateKey));

    clientShifts.forEach(shift => {
        const { startMinutes, durationMinutes, totalMinutes } = calculateShiftPosition(shift, startDate);
        const div = document.createElement('div');
        let cls = 'task daily default';
        if (shift.is_vacant) cls = 'task daily task-vacant';
        else if (shift.is_advanced_shift) cls = 'task daily task-advanced';
        else if (shift.add_to_job_board) cls = 'task daily task-jobboard';
        div.className = cls;
        div.style.left = `${(startMinutes / totalMinutes) * 100}%`;
        div.style.width = `${Math.min((durationMinutes / totalMinutes) * 100, 100)}%`;

        const staffName = users[String(shift.user_id)] || '';
        const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Shift';
        const timeRange =
            shift.start_time && shift.end_time
                ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                : 'No Time';

        div.innerHTML = `
            <strong>${timeRange}</strong><br>
            ${shiftType}<br>
        `;
        div.onclick = e => {
            e.stopPropagation();
            openShiftSlider(shift.id, dateKey);
        };

        wrapper.appendChild(div);
    });

    calendar.appendChild(row);
});


    } else {
        // Weekly / Fortnightly
        let d = new Date(startDate);
        while (d <= endDate) { dates.push(new Date(d)); d.setDate(d.getDate() + 1); }
        weekRange.textContent = `${formatDateShort(startDate)} - ${formatDateShort(endDate)}`;
        calendar.innerHTML = '<div class="day-header-staff">Client</div>';
        dates.forEach(day => {
            const header = document.createElement('div');
            header.className = 'day-header';
            header.textContent = `${day.toLocaleDateString('en-US', { weekday: 'short' })} ${day.getDate()}`;
            calendar.appendChild(header);
        });
        calendar.className = `calendar-grid ${viewType.toLowerCase()}`;

       // 🟥 Vacant row with badge
        const label = document.createElement('div');
        label.className = 'staff-cell vacant-staff-label';
        label.innerHTML = `
            <span class="label-badge vacant-staff-label-badge">VS</span>
            <span class="label-text">Vacant Shift</span>
        `;
        calendar.appendChild(label);

        dates.forEach(day => {
            const cell = document.createElement('div');
            cell.className = 'calendar-day';
            cell.onclick = handleEmptyCalendarClick;

            const dateKey = formatDateKey(day);
            const relevant = filteredShifts.filter(
                s => s.is_vacant && isShiftInDateRange(s, dateKey)
            );

            relevant.forEach(shift => {
                const div = document.createElement('div');
                div.className = 'task task-vacant';

                const staffName = users[String(shift.user_id)] || '';
                const shiftType = shiftTypeNames[String(shift.shift_type_id)] || '';
                const timeRange = shift.start_time && shift.end_time
                    ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                    : 'No Time';

                div.innerHTML = `
                    <strong>${timeRange}</strong><br>
                    ${shiftType}<br>
                `;

                div.onclick = e => {
                    e.stopPropagation();
                    openShiftSlider(shift.id, dateKey);
                };

                cell.appendChild(div);
            });

            calendar.appendChild(cell);
        });


    // 🟦 Clients
const sortedClients = sortClientsBy(viewType, filteredShifts);

sortedClients.forEach(([clientId, clientName]) => {
    // 🔹 Create initials (e.g. "Adam Care" → "AC")
    const initials = clientName
        .split(' ')
        .map(w => w[0])
        .join('')
        .substring(0, 2)
        .toUpperCase();

    // 🟢 Create left label with circular badge + client name
    const clientCell = document.createElement('div');
    clientCell.className = 'staff-cell client-staff-label';
    clientCell.innerHTML = `
        <span class="label-badge client-staff-label-badge">${initials}</span>
        <span class="label-text">${clientName}</span>
    `;
    calendar.appendChild(clientCell);

    // 🔹 Loop through all days in view (Weekly / Fortnightly)
    dates.forEach(day => {
        const cell = document.createElement('div');
        cell.className = 'calendar-day';
        cell.onclick = handleEmptyCalendarClick;

        const dateKey = formatDateKey(day);
        const clientShifts = filteredShifts
            .filter(s => {
                try {
                    const parsed = typeof s.client_section === 'string'
                        ? JSON.parse(s.client_section)
                        : s.client_section || {};
                    const ids = Array.isArray(parsed.client_id)
                        ? parsed.client_id.map(String)
                        : [];
                    return String(s.client_id) === String(clientId) || ids.includes(String(clientId));
                } catch {
                    return String(s.client_id) === String(clientId);
                }
            })
            .filter(s => isShiftInDateRange(s, dateKey));

        clientShifts.forEach(shift => {
            const div = document.createElement('div');
            let cls = 'task default';
            if (shift.is_vacant) cls = 'task task-vacant';
            else if (shift.is_advanced_shift) cls = 'task task-advanced';
            else if (shift.add_to_job_board) cls = 'task task-jobboard';
            div.className = cls;

            const staffName = users[String(shift.user_id)] || '';
            const shiftType = shiftTypeNames[String(shift.shift_type_id)] || 'Shift';
            const timeRange =
                shift.start_time && shift.end_time
                    ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}`
                    : 'No Time';

            div.innerHTML = `
                <strong>${timeRange}</strong><br>
                ${shiftType}<br>
            `;

            div.onclick = e => {
                e.stopPropagation();
                openShiftSlider(shift.id, dateKey);
            };

            cell.appendChild(div);
        });

        calendar.appendChild(cell);
    });
});

    }
}

// === Small helper utilities (already exist in your file, shown for clarity) ===
function formatDateKey(date) {
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
}
function formatDateShort(date) {
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}
function calculateShiftPosition(shift, refDate) {
    const shiftStart = new Date(`${shift.start_date}T${shift.start_time || '00:00'}`);
    const shiftEnd = new Date(`${shift.end_date || shift.start_date}T${shift.end_time || '23:59'}`);
    const dayStart = new Date(refDate); dayStart.setHours(DAY_START_HOUR, 0, 0, 0);
    const dayEnd = new Date(refDate); dayEnd.setHours(DAY_END_HOUR + 1, 0, 0, 0);
    const effectiveStart = shiftStart < dayStart ? dayStart : shiftStart;
    const effectiveEnd = shiftEnd > dayEnd ? dayEnd : shiftEnd;
    const totalMinutes = (DAY_END_HOUR - DAY_START_HOUR + 1) * 60;
    const startMinutes = (effectiveStart.getHours() * 60 + effectiveStart.getMinutes()) - (DAY_START_HOUR * 60);
    let durationMinutes = (effectiveEnd - effectiveStart) / (1000 * 60);
    if (durationMinutes < 1) durationMinutes = 1;
    return { startMinutes, durationMinutes, totalMinutes };
}
function sortUsersBy(viewType, filteredShifts) {
    let usersArr = Object.entries(users);
    if (currentSort === 'Shift Counts') {
        usersArr.sort((a, b) => {
            const countA = filteredShifts.filter(s => String(s.user_id) === String(a[0])).length;
            const countB = filteredShifts.filter(s => String(s.user_id) === String(b[0])).length;
            return countB - countA;
        });
    } else usersArr.sort((a, b) => a[1].localeCompare(b[1]));
    return usersArr;
}
function sortClientsBy(viewType, filteredShifts) {
    let clientsArr = Object.entries(clientNames);
    if (currentSort === 'Shift Counts') {
        clientsArr.sort((a, b) => {
            const countA = filteredShifts.filter(s => String(s.client_id) === String(a[0])).length;
            const countB = filteredShifts.filter(s => String(s.client_id) === String(b[0])).length;
            return countB - countA;
        });
    } else clientsArr.sort((a, b) => a[1].localeCompare(b[1]));
    return clientsArr;
}


            function highlightToday() {
                const viewType = document.getElementById('viewType').value;
                const { startDate, endDate } = getPeriodDates(viewType, currentDate);
                const dates = [];
                if (viewType === 'Daily') {
                    dates.push(startDate);
                } else {
                    let day = new Date(startDate);
                    while (day <= endDate) {
                        dates.push(new Date(day));
                        day.setDate(day.getDate() + 1);
                    }
                }

                dates.forEach((day, i) => {
                    const dayHeader = document.getElementById(`day${i}`);
                    const cdayHeader = document.getElementById(`cday${i}`);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    if (day.toDateString() === today.toDateString()) {
                        if (dayHeader) dayHeader.classList.add('today-highlight');
                        if (cdayHeader) cdayHeader.classList.add('today-highlight');
                    } else {
                        if (dayHeader) dayHeader.classList.remove('today-highlight');
                        if (cdayHeader) cdayHeader.classList.remove('today-highlight');
                    }
                });
            }

            function prevPeriod() {
                const viewType = document.getElementById('viewType').value;
                if (viewType === 'Daily') {
                    currentDate.setDate(currentDate.getDate() - 1);
                } else if (viewType === 'Weekly') {
                    currentDate.setDate(currentDate.getDate() - 7);
                } else {
                    currentDate.setDate(currentDate.getDate() - 14);
                }
                renderStaffCalendar(filteredShifts);
                renderClientCalendar(filteredShifts);
                highlightToday();
            }

            function nextPeriod() {
                const viewType = document.getElementById('viewType').value;
                if (viewType === 'Daily') {
                    currentDate.setDate(currentDate.getDate() + 1);
                } else if (viewType === 'Weekly') {
                    currentDate.setDate(currentDate.getDate() + 7);
                } else {
                    currentDate.setDate(currentDate.getDate() + 14);
                }
                renderStaffCalendar(filteredShifts);
                renderClientCalendar(filteredShifts);
                highlightToday();
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

            document.getElementById('todayBtn').addEventListener('click', function() {
                currentDate = new Date();
                renderStaffCalendar(filteredShifts);
                renderClientCalendar(filteredShifts);
                highlightToday();
            });

            document.getElementById('viewType').addEventListener('change', function() {
            const wrapper = document.getElementById('calendarWrapper');
            wrapper.classList.remove('daily', 'weekly', 'fortnightly');
            wrapper.classList.add(this.value.toLowerCase());

                renderStaffCalendar(filteredShifts);
                renderClientCalendar(filteredShifts);
                highlightToday();
            });

            document.getElementById('calendarType').addEventListener('change', function() {
                renderStaffCalendar(filteredShifts);
                renderClientCalendar(filteredShifts);
                highlightToday();
            });

            document.getElementById('status').addEventListener('change', function() {
                const selectedStatus = this.value;
                filteredShifts = selectedStatus !== 'all' ? shifts.filter(shift => shift.status === selectedStatus) : shifts;
                applyFiltersAndSort();
            });

            document.getElementById('shiftTypeFilter').addEventListener('change', function() {
                const selectedShiftType = this.value;
                filteredShifts = selectedShiftType ? shifts.filter(shift => String(shift.shift_type_id) === selectedShiftType) : shifts;
                applyFiltersAndSort();
            });

            document.getElementById('sortFilter').addEventListener('change', function() {
                currentSort = this.value;
                applyFiltersAndSort();
            });

            function applyFiltersAndSort() {
                let tempShifts = shifts;
                const selectedShiftType = document.getElementById('shiftTypeFilter').value;
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
                highlightToday();
            }

            document.getElementById('customCalendarToggle').addEventListener('click', (e) => {
                e.stopPropagation();
                const customPopup = document.getElementById('customCalendarPopup');
                customPopup.style.display = customPopup.style.display === 'block' ? 'none' : 'block';
                const rect = document.getElementById('customCalendarToggle').getBoundingClientRect();
                customPopup.style.left = '581.35px';
                customPopup.style.top = '135px';
            });

            document.addEventListener('click', (e) => {
                const customPopup = document.getElementById('customCalendarPopup');
                const customBtn = document.getElementById('customCalendarToggle');
                if (!customPopup.contains(e.target) && e.target !== customBtn) {
                    customPopup.style.display = 'none';
                }
            });

            document.getElementById('customDatePicker').addEventListener('change', function(e) {
                const selectedDate = new Date(e.target.value);
                if (selectedDate) {
                    currentDate = selectedDate;
                    if (document.getElementById('viewType').value !== 'Daily') {
                        currentDate.setDate(currentDate.getDate() - currentDate.getDay());
                    }
                    renderStaffCalendar(filteredShifts);
                    renderClientCalendar(filteredShifts);
                    highlightToday();
                    document.getElementById('customCalendarPopup').style.display = 'none';
                }
            });

            const dropdown = document.getElementById('dropdown');
            const button = dropdown.querySelector('.dropdown-btn');
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });

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

            document.addEventListener('DOMContentLoaded', () => {
                // Ensure initial render uses Weekly view
                document.getElementById('viewType').value = 'Weekly';
                renderStaffCalendar();
                renderClientCalendar();
                highlightToday();
            });
            // 🔹 Handles clicking empty blocks across all calendar views
function handleEmptyCalendarClick() {
    // Opens your Filament Shift modal globally
    openModal('shift-modal');
}

        </script>
    </x-filament-panels::page>