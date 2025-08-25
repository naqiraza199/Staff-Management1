<x-filament-panels::page>

<style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

          body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            /* background: linear-gradient(135deg, #1e3a8a, #6d28d9); */
            color: #e5e7eb;
            overflow-x: auto;
        }
        .calendar-container {
            background: rgb(255, 255, 255);
            backdrop-filter: blur(10px);
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 1900px;
            margin: 20px;
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
            gap: 10px;
            border-radius: 12px;
        }
        .calendar-day {
            padding: 16px;
            border: 1px solid rgba(58, 115, 224, 0.57);
            min-height: 140px;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease, background 0.3s ease;
            border-radius: 8px;
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
            border-radius: 8px;
        }
        .staff-cell {
            font-weight: 600;
            padding: 16px;
            border: 1px solid rgba(4, 168, 248, 0.65);
            background: rgba(255, 255, 255, 0.1);
            color: #0a0a0a;
            font-size: 1.1rem;
            border-radius: 8px;
        }
        .add-staff-cell {
            padding: 16px;
            text-align: center;
        }
        .task {
            background: linear-gradient(45deg, #60a5fa, #a78bfa);
            padding: 8px;
            margin: 8px 0;
            border-radius: 8px;
            font-size: 0.9rem;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
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
        .but-div{
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
            color: #10b981; /* green like Filament */
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
        .funds{
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
    width: 600px;
    max-width: 95%;
    margin: auto;
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
    </style>

</head>
    <div class="calendar-container">
        <div class="calendar-header">
            <button class="buto" onclick="prevWeek()">Previous Week</button>
            <h2 id="week-range"></h2>
            <button class="buto" onclick="nextWeek()">Next Week</button>
        </div>
        <div class="calendar-grid" id="calendar">
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

    <div class="modal" id="taskModal">
        <div class="task-modal-content" id="taskModalContent">
            <button class="buto full-view-btn" style="float: right" onclick="toggleFullView('taskModalContent')">&#x26F6;</button>
                                  
          <div x-data="{ repeatChecked: false, jobBoardActive: false, recurrance: '' }">
              {{ $this->form }}
          </div>

              
            <div class="but-div">
            <button class="buto" onclick="addTask()">Save</button>
            <button class="buto" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>

<div class="modal" id="staffModal">
  <div class="staff-modal-content" id="staffModalContent">
<button class="buto full-view-btn" onclick="toggleFullView('staffModalContent')">&#x26F6;</button>
    <h3 class="staff-heading">Manage Staff</h3>

    <div class="staff-section-title">Staff Detail</div>

 <form class="staff-form">

      <!-- Salutation -->
      <label class="staff-check">
        <input type="checkbox" id="useSalutation" onchange="toggleSalutation()"> Use salutation
      </label>

      <div class="staff-group">
        <label class="staff-label">Name:</label>
        <div class="staff-flex-row">
          <select id="salutation" class="staff-input staff-salutation" style="display: none;">
            <option value="Mr">Mr</option>
            <option value="Mrs">Mrs</option>
            <option value="Ms">Ms</option>
            <option value="Dr">Dr</option>
          </select>
          <input type="text" class="staff-input staff-name" placeholder="Enter Name">
        </div>
      </div>

      <!-- Email -->
      <div class="staff-group">
        <label class="staff-label">Email:</label>
        <input type="email" class="staff-input staff-email" placeholder="Enter Email">
      </div>

      <!-- Contact -->
      <div class="staff-group">
        <label class="staff-label">Contact:</label>
        <div class="staff-flex-row">
          <input type="text" class="staff-input staff-mobile" placeholder="Enter Mobile Number">
          <input type="text" class="staff-input staff-phone" placeholder="Enter Phone Number">
        </div>
      </div>

      <!-- User Type Tabs -->
      <div class="staff-group">
        <label class="staff-label">User Type:</label>
        <div class="staff-toggle-btns">
          <button type="button" class="staff-toggle staff-toggle-active" onclick="setUserType('carer')">Carer</button>
          <button type="button" class="staff-toggle" onclick="setUserType('office')">Office User</button>
        </div>
      </div>

      <!-- Roles (only for Office User) -->
      <div class="staff-group" id="rolesGroup" style="display: none;">
        <label class="staff-label">Roles:</label>
        <input type="text" class="staff-input staff-roles" placeholder="Enter Roles">
      </div>

      <!-- Gender + DOB -->
      <div class="staff-group staff-flex-row">
        <div class="staff-flex-col">
          <label class="staff-label">Gender:</label>
          <select class="staff-input staff-gender">
            <option>Select</option>
            <option>Male</option>
            <option>Female</option>
            <option>Other</option>
          </select>
        </div>
        <div class="staff-flex-col">
          <label class="staff-label">Date Of Birth:</label>
          <input type="date" class="staff-input staff-dob">
        </div>
      </div>

      <!-- Employment Type -->
      <div class="staff-group">
        <label class="staff-label">Employment Type:</label>
        <select class="staff-input staff-employment">
          <option>Casual</option>
          <option>Full Time</option>
          <option>Part Time</option>
        </select>
      </div>

      <!-- Address -->
      <div class="staff-group">
        <label class="staff-label">Address:</label>
        <input type="text" class="staff-input staff-address" placeholder="Enter Address">
      </div>

      <!-- Buttons -->
      <div class="staff-actions">
        <button class="staff-btn staff-btn-primary" onclick="addStaff()">Create</button>
        <button class="staff-btn staff-btn-secondary" onclick="closeStaffModal()">Cancel</button>
      </div>

    </form>
  </div>
</div>



<script>
  function toggleSalutation() {
    let salutation = document.getElementById("salutation");
    salutation.style.display = document.getElementById("useSalutation").checked ? "block" : "none";
  }

  function setUserType(type) {
    const carerBtn = document.querySelectorAll(".staff-toggle")[0];
    const officeBtn = document.querySelectorAll(".staff-toggle")[1];
    const rolesGroup = document.getElementById("rolesGroup");

    if (type === "carer") {
      carerBtn.classList.add("staff-toggle-active");
      officeBtn.classList.remove("staff-toggle-active");
      rolesGroup.style.display = "none";
    } else {
      officeBtn.classList.add("staff-toggle-active");
      carerBtn.classList.remove("staff-toggle-active");
      rolesGroup.style.display = "flex";
      rolesGroup.style.flexDirection = "column";
      rolesGroup.style.gap = "5px";
    }
  }
</script>

<script>
let currentDate = new Date();
        let tasks = JSON.parse(localStorage.getItem('tasks')) || {};
        let staffList = @json($this->users); // Use database users including auth user

        function renderCalendar() {
            const calendar = document.getElementById('calendar');
            const weekRange = document.getElementById('week-range');
            const startOfWeek = new Date(currentDate);
            startOfWeek.setDate(currentDate.getDate() - currentDate.getDay());

            // Set week range in header
            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);
            weekRange.textContent = `${startOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })} - ${endOfWeek.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;

            // Set day headers
            for (let i = 0; i < 7; i++) {
                const day = new Date(startOfWeek);
                day.setDate(startOfWeek.getDate() + i);
                document.getElementById(`day${i}`).textContent = `${day.toLocaleDateString('en-US', { weekday: 'short' })} ${day.getDate()}`;
            }

            // Clear previous content
            while (calendar.children.length > 8) {
                calendar.removeChild(calendar.lastChild);
            }

            // Add static task rows
            const staticTasks = ['Vacant Shft', 'Job Board'];
            staticTasks.forEach(task => {
                const taskCell = document.createElement('div');
                taskCell.className = 'staff-cell';
                taskCell.textContent = task;
                calendar.appendChild(taskCell);

                for (let i = 0; i < 7; i++) {
                    const day = new Date(startOfWeek);
                    day.setDate(startOfWeek.getDate() + i);
                    const dateKey = `${day.getFullYear()}-${day.getMonth() + 1}-${day.getDate()}`;
                    const dayCell = document.createElement('div');
                    dayCell.className = 'calendar-day';
                    dayCell.onclick = () => openModal(`${task}_${dateKey}`, dateKey);
                    calendar.appendChild(dayCell);
                }
            });

            // Add staff rows from database users including auth user
            staffList.forEach(staff => {
                const staffCell = document.createElement('div');
                staffCell.className = 'staff-cell';
                staffCell.textContent = staff;
                calendar.appendChild(staffCell);

                for (let i = 0; i < 7; i++) {
                    const day = new Date(startOfWeek);
                    day.setDate(startOfWeek.getDate() + i);
                    const dateKey = `${day.getFullYear()}-${day.getMonth() + 1}-${day.getDate()}`;
                    const dayCell = document.createElement('div');
                    dayCell.className = 'calendar-day';
                    const key = `${staff}_${dateKey}`;
                    if (tasks[key]) {
                        tasks[key].forEach(task => {
                            const taskDiv = document.createElement('div');
                            taskDiv.className = 'task';
                            taskDiv.textContent = task;
                            dayCell.appendChild(taskDiv);
                        });
                    }
                    dayCell.onclick = () => openModal(key, dateKey);
                    calendar.appendChild(dayCell);
                }
            });

            // Add "Add Staff" button
            const addStaffCell = document.createElement('div');
            addStaffCell.className = 'add-staff-cell';
            addStaffCell.innerHTML = `<button class="add-staff-btn" onclick="openStaffModal()">Add Staff</button>`;
            calendar.appendChild(addStaffCell);
        }

        function prevWeek() {
            currentDate.setDate(currentDate.getDate() - 7);
            renderCalendar();
        }

        function nextWeek() {
            currentDate.setDate(currentDate.getDate() + 7);
            renderCalendar();
        }

        function openModal(key, dateKey) {
            console.log(key , dateKey);
            const modal = document.getElementById('taskModal');
            modal.style.display = 'flex';
            // Add logic here to populate shifts modal if needed
        }

        function closeModal() {
            document.getElementById('taskModal').style.display = 'none';
        }

        function openStaffModal() {
            const modal = document.getElementById('staffModal');
            modal.style.display = 'flex';
        }

        function closeStaffModal() {
            document.getElementById('staffModal').style.display = 'none';
        }

        // Initialize calendar
        renderCalendar();
</script>

<!-- TinyMCE CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>

<script>
  tinymce.init({
    selector: '#instructionEditor',
    height: 300,
    menubar: false,
    plugins: 'advlist autolink lists link image charmap preview anchor ' +
             'searchreplace visualblocks code fullscreen ' +
             'insertdatetime media table code help wordcount',
    toolbar: 'undo redo | formatselect | ' +
             'bold italic underline | alignleft aligncenter ' +
             'alignright alignjustify | bullist numlist outdent indent | ' +
             'removeformat | help',
    branding: false
  });
</script>
<script>
  const checkbox = document.getElementById('jobBoardCheckbox');
  const defaultForm = document.getElementById('defaultForm');
  const jobBoardForm = document.getElementById('jobBoardForm');

  checkbox.addEventListener('change', function () {
    if (this.checked) {
      defaultForm.classList.add('hidden');
      jobBoardForm.classList.remove('hidden');
    } else {
      jobBoardForm.classList.add('hidden');
      defaultForm.classList.remove('hidden');
    }
  });
</script>
</x-filament-panels::page>
