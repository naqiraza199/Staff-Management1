<style>
    /* CSS Variables for easy color management */
    :root {
        --dp-primary: #4f46e5; /* Indigo-600 */
        --dp-primary-dark: #4338ca; /* Indigo-700 */
        --dp-secondary: #eef2ff; /* Indigo-50 */
        --dp-gray-bg: #f9fafb; /* gray-50 */
        --dp-gray-border: #d1d5db; /* gray-300 */
        --dp-shadow-strong: 0 15px 40px rgba(0, 0, 0, 0.25);
    }

    /* The overall container for the custom calendar */
    #dp-calendar-container {
        position: absolute;
        z-index: 1000; 
        background-color: white;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: var(--dp-shadow-strong);
        border: 1px solid #f3f4f6;
        width: 320px;
        user-select: none;
        display: none; 
    }

    /* Modal Animation Styles */
    #dp-calendar-container.dp-active {
        display: block; 
        transform: scale(1);
        opacity: 1;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    #dp-calendar-container:not(.dp-active) {
        transform: scale(0.95);
        opacity: 0;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    /* --- Header --- */
    .dp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        border-bottom: 1px solid #f3f4f6;
        padding-bottom: 0.75rem;
    }

    #dp-month-year-display {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        cursor: pointer; 
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        transition: background-color 0.15s;
    }
    
    #dp-month-year-display:hover {
        background-color: #f3f4f6; /* gray-100 */
    }

    /* Navigation Buttons */
    .dp-header button {
        background-color: var(--dp-secondary);
        color: var(--dp-primary);
        padding: 0.5rem;
        border-radius: 9999px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dp-header button:hover {
        background-color: var(--dp-primary);
        color: white;
    }

    .dp-header svg {
        width: 1.25rem;
        height: 1.25rem;
    }

    /* --- Weekdays --- */
    .dp-weekdays {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        text-align: center;
        font-size: 0.75rem;
        font-weight: 500;
        color: #6b7280; 
        margin-bottom: 0.5rem;
    }

    /* --- Grids and Cells --- */
    
    /* Day Grid */
    #dp-days-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 4px;
        min-height: 200px; /* Ensure consistent height */
    }
    
    /* Year Grid Specific Layout: 4 columns */
    #dp-year-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 8px;
        min-height: 200px; /* Ensure consistent height */
        align-items: center;
        justify-items: center;
        padding: 10px 0;
    }
    
    /* Cell base styles */
    .dp-day-cell, .dp-year-cell {
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        font-size: 0.875rem;
        min-height: 32px;
    }

    /* Year Cell Styles */
    .dp-year-cell {
        font-size: 1rem;
        font-weight: 500;
        width: 100%;
        max-width: 65px;
        height: 40px;
        border-radius: 8px;
    }

    .dp-year-cell:hover:not(.dp-selected) {
        background-color: #e5e7eb;
    }
    
    /* Day Cell Hover */
    .dp-day-cell:hover:not(.dp-empty):not(.dp-selected) {
        background-color: #e5e7eb;
        border-radius: 9999px;
    }
    
    /* Selected Date/Year Style: Primary Color + Shadow */
    .dp-day-cell.dp-selected, .dp-year-cell.dp-selected {
        background-color: var(--dp-primary);
        color: white;
        font-weight: 600; 
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.4);
    }
    .dp-day-cell.dp-selected {
        border-radius: 9999px;
    }
    .dp-year-cell.dp-selected {
        border-radius: 8px;
    }
    
    /* Today's Date/Current Year Style */
    .dp-day-cell.dp-today:not(.dp-selected), .dp-year-cell.dp-today:not(.dp-selected) {
        border: 2px solid #10b981; 
        color: #10b981;
        font-weight: 600;
    }
    .dp-day-cell.dp-today:not(.dp-selected) {
        border-radius: 9999px;
    }
    .dp-year-cell.dp-today:not(.dp-selected) {
        border-radius: 8px;
    }

    /* Days from other months */
    .dp-day-cell.dp-empty {
        opacity: 0.3;
        pointer-events: none;
    }
    
    /* Utility Class to Hide Elements */
    .dp-hidden {
        display: none !important;
    }

    /* --- Footer --- */
    .dp-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        margin-top: 1rem;
        border-top: 1px solid #f3f4f6;
    }

    .dp-footer button {
        background: none;
        border: none;
        cursor: pointer;
        transition: color 0.15s, background-color 0.15s;
        font-size: 0.875rem;
        font-weight: 600;
    }

    #dp-today-btn {
        color: var(--dp-primary);
    }

    #dp-today-btn:hover {
        color: var(--dp-primary-dark);
    }

    #dp-close-btn {
        color: #6b7280; 
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        background-color: #f3f4f6;
    }

    #dp-close-btn:hover {
        background-color: #e5e7eb;
    }

</style>

<!-- Calendar HTML Structure -->
<div 
    id="dp-calendar-container" 
>
    <!-- Header: Month/Year and Navigation -->
    <div class="dp-header">
        <button id="dp-prev-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <div id="dp-month-year-display"></div>
        <button id="dp-next-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>

    <!-- Month View: Days Grid (Initial View) -->
    <div id="dp-month-view-container">
        <!-- Weekdays -->
        <div class="dp-weekdays">
            <span>Sun</span>
            <span>Mon</span>
            <span>Tue</span>
            <span>Wed</span>
            <span>Thu</span>
            <span>Fri</span>
            <span>Sat</span>
        </div>
        <!-- Calendar Grid (Days) -->
        <div id="dp-days-grid">
            <!-- Days are generated here by JavaScript -->
        </div>
    </div>
    
    <!-- Year View: Years Grid (Hidden by default) -->
    <div id="dp-year-view-container" class="dp-hidden">
        <!-- Year Grid (4x3 layout) -->
        <div id="dp-year-grid">
            <!-- Years are generated here by JavaScript -->
        </div>
    </div>

    <!-- Footer: Today and Close Buttons -->
    <div class="dp-footer">
        <button id="dp-today-btn">
            Today
        </button>
        <button id="dp-close-btn">
            Close
        </button>
    </div>
</div>

<script>
    // IIFE to encapsulate the component logic and ensure the function is defined globally
    (function () {
        // --- DOM Element Mappings (Unique IDs) ---
        const calendarContainer = document.getElementById('dp-calendar-container');
        const monthYearDisplay = document.getElementById('dp-month-year-display');
        const daysGrid = document.getElementById('dp-days-grid');
        const yearGrid = document.getElementById('dp-year-grid');
        const monthViewContainer = document.getElementById('dp-month-view-container');
        const yearViewContainer = document.getElementById('dp-year-view-container');
        const prevMonthBtn = document.getElementById('dp-prev-btn');
        const nextMonthBtn = document.getElementById('dp-next-btn');
        const todayBtn = document.getElementById('dp-today-btn');
        const closeBtn = document.getElementById('dp-close-btn');

        // --- Global State Variables ---
        // Current input element being interacted with
        let activeInput = null; 
        
        // These dates now hold the currently selected date and the date shown in the view
        let selectedDate = new Date();
        let currentViewDate = new Date(); 
        
        const today = new Date();
        const monthNames = ["January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        let currentView = 'month'; // 'month' or 'year'
        
        // Append the container to the body when the script runs
        document.body.appendChild(calendarContainer);


        // --- Utility Functions ---

        /** Formats the date and writes it to the currently active input field. */
        const formatSelectedDate = (date, input) => {
            const d = new Date(date);
            let month = '' + (d.getMonth() + 1);
            let day = '' + d.getDate();
            const year = d.getFullYear();

            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;

            const filamentFormat = `${year}-${month}-${day}`;
            
            if (input) {
                input.value = filamentFormat;
                // Dispatch events for Livewire/Filament to pick up the change
                input.dispatchEvent(new Event('change', { bubbles: true }));
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // Update the global state so render functions know what to highlight
            selectedDate = d;
        };

        const isSameDay = (d1, d2) => 
            d1.getFullYear() === d2.getFullYear() &&
            d1.getMonth() === d2.getMonth() &&
            d1.getDate() === d2.getDate();
            
        const isSameYear = (y1, y2) => y1 === y2;


        // --- Positioning Function ---
        const positionCalendar = () => {
            if (!activeInput) return; // Use activeInput

            const rect = activeInput.getBoundingClientRect();
            const containerWidth = calendarContainer.offsetWidth;
            const windowWidth = window.innerWidth;

            let left = rect.left;
            
            if (left + containerWidth > windowWidth - 10) { 
                left = windowWidth - containerWidth - 10;
            }
            if (left < 10) {
                left = 10;
            }

            calendarContainer.style.top = `${rect.bottom + window.scrollY + 8}px`;
            calendarContainer.style.left = `${left + window.scrollX}px`;
        };

        // --- View Switching Logic ---
        
        const showMonthView = () => {
            currentView = 'month';
            monthViewContainer.classList.remove('dp-hidden');
            yearViewContainer.classList.add('dp-hidden');
            renderCalendar();
        };

        const showYearView = () => {
            currentView = 'year';
            monthViewContainer.classList.add('dp-hidden');
            yearViewContainer.classList.remove('dp-hidden');
            renderYearGrid();
        };


        // --- Year Grid Rendering ---
        const renderYearGrid = () => {
            yearGrid.innerHTML = '';
            
            const currentYear = currentViewDate.getFullYear();
            const startYearBlock = currentYear - (currentYear % 12); 
            const displayStartYear = startYearBlock;
            const displayEndYear = startYearBlock + 11;

            monthYearDisplay.textContent = `${displayStartYear} - ${displayEndYear}`;

            for (let year = displayStartYear; year <= displayEndYear; year++) {
                const yearElement = document.createElement('div');
                yearElement.classList.add('dp-year-cell');
                yearElement.textContent = year;
                yearElement.dataset.year = year;

                if (isSameYear(year, today.getFullYear())) {
                    yearElement.classList.add('dp-today');
                }
                if (isSameYear(year, selectedDate.getFullYear())) {
                    yearElement.classList.add('dp-selected');
                }

                yearElement.addEventListener('click', (e) => {
                    const newYear = parseInt(e.target.dataset.year);
                    // Update state and re-render month view
                    currentViewDate.setFullYear(newYear); 
                    selectedDate.setFullYear(newYear);
                    showMonthView(); 
                });

                yearGrid.appendChild(yearElement);
            }
        };


        // --- Month View (Original Render Calendar Logic) ---
        const renderCalendar = () => {
            daysGrid.innerHTML = '';
            const month = currentViewDate.getMonth();
            const year = currentViewDate.getFullYear();
            
            monthYearDisplay.textContent = `${monthNames[month]} ${year}`;

            const firstDayOfMonth = new Date(year, month, 1);
            const lastDayOfMonth = new Date(year, month + 1, 0);
            const startDayOfWeek = firstDayOfMonth.getDay(); 
            const numDaysInMonth = lastDayOfMonth.getDate();

            for (let i = 0; i < startDayOfWeek; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.classList.add('dp-day-cell', 'dp-empty');
                daysGrid.appendChild(emptyDay);
            }

            for (let day = 1; day <= numDaysInMonth; day++) {
                const date = new Date(year, month, day);
                const dayElement = document.createElement('div');
                dayElement.classList.add('dp-day-cell');
                dayElement.textContent = day;
                dayElement.dataset.date = date.toDateString(); 

                if (isSameDay(date, today)) {
                    dayElement.classList.add('dp-today');
                }
                if (isSameDay(date, selectedDate)) {
                    dayElement.classList.add('dp-selected');
                }

                dayElement.addEventListener('click', (e) => {
                    document.querySelectorAll('.dp-day-cell.dp-selected').forEach(d => d.classList.remove('dp-selected'));
                    e.target.classList.add('dp-selected');
                    
                    const newSelectedDate = new Date(e.target.dataset.date);
                    // Pass the currently active input to the formatter
                    formatSelectedDate(newSelectedDate, activeInput); 
                    
                    setTimeout(hideCalendar, 200); 
                });

                daysGrid.appendChild(dayElement);
            }
        };

        // --- Show/Hide Logic ---
        
        /** Called when a DatePicker input is clicked. */
        const showCalendar = (inputElement) => {
            // 1. Set the active input for this session
            activeInput = inputElement; 

            // 2. Read the current value from the specific input
            if (activeInput && activeInput.value) {
                const parsedDate = new Date(activeInput.value + 'T00:00:00');
                if (!isNaN(parsedDate)) {
                    selectedDate = parsedDate;
                }
            } else {
                selectedDate = new Date(); // If no value, default to today
            }
            
            currentViewDate = new Date(selectedDate);
            showMonthView(); 
            positionCalendar();

            calendarContainer.classList.add('dp-active');
        }

        const hideCalendar = () => {
            calendarContainer.classList.remove('dp-active');
            activeInput = null; // Clear active input when closing
        }

        // --- Event Listeners ---
        
        // Navigation Handler (uses currentViewDate)
        const handleNavigation = (direction) => {
            if (!activeInput) return; // Prevent navigation if calendar isn't linked to an input

            if (currentView === 'month') {
                const step = direction === 'prev' ? -1 : 1;
                currentViewDate.setMonth(currentViewDate.getMonth() + step);
                renderCalendar();
            } else if (currentView === 'year') {
                const step = direction === 'prev' ? -12 : 12;
                currentViewDate.setFullYear(currentViewDate.getFullYear() + step);
                renderYearGrid();
            }
        };

        monthYearDisplay.addEventListener('click', () => currentView === 'month' ? showYearView() : showMonthView());
        prevMonthBtn.addEventListener('click', () => handleNavigation('prev'));
        nextMonthBtn.addEventListener('click', () => handleNavigation('next'));


        // Today button handler
        todayBtn.addEventListener('click', () => { 
            if (!activeInput) return;
            selectedDate = new Date(); 
            currentViewDate = new Date(); 
            formatSelectedDate(selectedDate, activeInput); 
            showMonthView(); 
            setTimeout(hideCalendar, 200); 
        });

        closeBtn.addEventListener('click', hideCalendar);
        
        // Handle outside clicks to close
        document.addEventListener('click', (e) => {
            if (calendarContainer.classList.contains('dp-active') && activeInput &&
                !activeInput.contains(e.target) && 
                !calendarContainer.contains(e.target)) {
                hideCalendar();
            }
        });

        window.addEventListener('resize', () => {
            if (calendarContainer.classList.contains('dp-active')) {
                positionCalendar();
            }
        });
        window.addEventListener('scroll', () => {
            if (calendarContainer.classList.contains('dp-active')) {
                positionCalendar();
            }
        }, true);

        // --- The Global Function (Initializer) ---
        window.initCustomDatePicker = (targetElementId) => {
            const inputElement = document.getElementById(targetElementId);
            
            if (inputElement) {
                inputElement.setAttribute('readonly', 'true');
                inputElement.setAttribute('type', 'text'); 

                // We attach an ANONYMOUS function here so it correctly calls showCalendar with itself
                inputElement.removeEventListener('click', inputElement.showCalendarHandler);
                inputElement.showCalendarHandler = () => showCalendar(inputElement);
                inputElement.addEventListener('click', inputElement.showCalendarHandler);
            } else {
                console.warn(`Custom DatePicker: Target input not found with ID: ${targetElementId}`);
            }
        };
    })();
</script>