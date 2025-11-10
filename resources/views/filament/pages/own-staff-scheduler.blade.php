<x-filament-panels::page>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .calendar-container {
            background: #fff;
            padding: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            width: 100%;
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
            color: #151a2d;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }
        .day-header {
            font-weight: 600;
            text-align: center;
            padding: 14px;
            background: #151A2D;
            color: white;
        }
        .calendar-day {
            padding: 10px;
            border: 1px solid rgba(58, 115, 224, 0.57);
            min-height: 140px;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.05);
        }
        .calendar-day:hover {
    background: rgb(0 0 0 / 5%);
    color: #000000;
        }
        .task {
            background: linear-gradient(45deg, #60a5fa, #a78bfa);
            padding: 8px;
            margin: 8px 0;
            border-radius: 8px;
            font-size: 12px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            height: 90px;
        }
        .task-advanced {
            background: linear-gradient(135deg, #4ade80, #22c55e);
            padding: 8px;
            margin: 8px 0;
            border-radius: 8px;
            font-size: 12px;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            height: 90px;
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
        .task-modal-content {
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
        }
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
        .buto {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            background: #151A2D;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .buto:hover {
            transform: translateY(-2px);
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
            background: #151A2D;
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
            padding: 5px;
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
            padding: 5px;
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

    <div wire:ignore.self>


        <div class="calendar-container" id="staff-calendar">
            <div class="calendar-header">
                <button class="buto" onclick="prevMonth()">Previous Month</button>
                <h2 id="month-range"></h2>
                <button class="buto" onclick="nextMonth()">Next Month</button>
            </div>
            <div class="calendar-grid" id="staffCalendar">
                <div class="day-header">Mon</div>
                <div class="day-header">Tue</div>
                <div class="day-header">Wed</div>
                <div class="day-header">Thu</div>
                <div class="day-header">Fri</div>
                <div class="day-header">Sat</div>
                <div class="day-header">Sun</div>
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
    </div>

    <script>
        let currentDate = new Date();
        const users = @json($users ?? []);
        const shifts = @json($shifts ?? []);
        const clientNames = @json($clientNames ?? []);
        const shiftTypeNames = @json($shiftTypeNames ?? []);
        const userId = {{ $userId ?? 'null' }};

        function formatTime(time) {
            if (!time) return '';
            const [hours] = time.split(':').map(Number);
            const period = hours >= 12 ? 'pm' : 'am';
            const formattedHours = hours % 12 || 12;
            return `${formattedHours}${period}`;
        }

      function renderStaffCalendar() {
    const calendar = document.getElementById('staffCalendar');
    if (!calendar) return;
    const monthRange = document.getElementById('month-range');

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    monthRange.textContent = new Date(year, month).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const daysInMonth = lastDay.getDate();
    const startingDay = (firstDay.getDay() + 6) % 7; // Adjust for Monday start

    // Clear existing content beyond headers
    while (calendar.children.length > 7) {
        calendar.removeChild(calendar.lastChild);
    }


    // Add empty cells for padding and days
    let date = 1;
    for (let i = 0; i < 6; i++) { // Up to 6 weeks
        for (let j = 0; j < 7; j++) {
            if ((i === 0 && j < startingDay) || date > daysInMonth) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'calendar-day';
                calendar.appendChild(emptyCell);
            } else {
                const dayCell = document.createElement('div');
                dayCell.className = 'calendar-day';
                dayCell.textContent = date;
                const dateKey = `${year}-${(month + 1).toString().padStart(2, '0')}-${date.toString().padStart(2, '0')}`;

                const userShifts = shifts.filter(shift => {
                    if (userId === null || shift.user_id === null) return false;
                    if (shift.user_id != userId) return false;

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

                if (userShifts.length > 0) {
                    userShifts.forEach(shift => {
                        const taskDiv = document.createElement('div');
                        taskDiv.className = 'task';
                        if (shift.is_advanced_shift) {
                            taskDiv.classList.add('task-advanced');
                            let clientIds = Array.isArray(shift.clientIds) ? shift.clientIds : [shift.clientIds];
                            let clientCount = 0;
                            const header = document.createElement('div');
                            const timeRange = shift.start_time && shift.end_time ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}` : 'No Time';
                            header.innerHTML = `<strong>${timeRange}</strong> ${shiftTypeNames[shift.shift_type_id] || 'Unknown'}<br>`;
                            taskDiv.appendChild(header);
                            const clientsWrapper = document.createElement('div');
                            clientsWrapper.style.display = 'flex';
                            clientsWrapper.style.alignItems = 'center';
                            clientsWrapper.style.marginTop = '4px';
                            clientIds.forEach(id => {
                                const clientName = clientNames[String(id)] || 'Unknown Client';
                                const initials = clientName.split(' ').map(word => word.charAt(0).toUpperCase()).join('');
                                const avatar = document.createElement('div');
                                avatar.className = 'client-avatar';
                                avatar.textContent = initials;
                                clientsWrapper.appendChild(avatar);
                                clientCount++;
                            });
                            const countSpan = document.createElement('span');
                            countSpan.textContent = `${clientCount} Clients`;
                            countSpan.style.marginLeft = '6px';
                            countSpan.style.fontSize = '10px';
                            clientsWrapper.appendChild(countSpan);
                            taskDiv.appendChild(clientsWrapper);
                        } else {
                            const clientName = clientNames[shift.client_id] || 'Unknown Client';
                            const timeRange = shift.start_time && shift.end_time ? `${formatTime(shift.start_time)} - ${formatTime(shift.end_time)}` : 'No Time';
                            taskDiv.innerHTML = `${timeRange}<br>${shiftTypeNames[shift.shift_type_id] || 'Unknown'}<br>${clientName}`;
                        }
                        if (shift.is_approved === 1) {
                            const checkIcon = document.createElement('span');
                            checkIcon.innerHTML = '✔';
                            checkIcon.style.color = 'green';
                            checkIcon.style.marginLeft = '5px';
                            taskDiv.appendChild(checkIcon);
                        }
                        if (shift.is_cancelled) {
                            taskDiv.style.textDecoration = 'line-through';
                            taskDiv.style.color = '#ff0000';
                        }
                        taskDiv.onclick = (e) => {
                            e.stopPropagation();
                            openShiftSlider(shift.id, dateKey);
                        };
                        dayCell.appendChild(taskDiv);
                    });
                }

                calendar.appendChild(dayCell);
                date++;
            }
        }
        if (date > daysInMonth) break;
    }
}

        function prevMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderStaffCalendar();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderStaffCalendar();
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

        document.addEventListener('DOMContentLoaded', () => {
            renderStaffCalendar();
        });
    </script>
</x-filament-panels::page>