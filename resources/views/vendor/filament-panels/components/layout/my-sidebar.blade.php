<style>
    /* Importing Google Fonts - Poppins */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
        }
        body {
        min-height: 100vh;
        background: linear-gradient(#F1F3FF, #CBD4FF);
        }
        .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 10;
        width: 270px;
        height: 100vh;
        background: #151A2D;
        transition: all 0.4s ease;
        }
        .sidebar.collapsed {
        width: 85px;
        }
        .sidebar .sidebar-header {
        display: flex;
        position: relative;
        padding: 25px 20px;
        align-items: center;
        justify-content: space-between;
        }
        .sidebar-header .header-logo img {
        width: 46px;
        height: 46px;
        display: block;
        object-fit: contain;
        border-radius: 50%;
        }
        .sidebar-header .sidebar-toggler,
        .sidebar-menu-button {
        position: absolute;
        right: 20px;
        color: #151A2D;
        border: none;
        cursor: pointer;
        display: flex;
        background: #EEF2FF;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: 0.4s ease;
        margin-top: -25px;
        }
        .sidebar.collapsed .sidebar-header .sidebar-toggler {
        transform: translate(-4px, 65px);
        }
        .sidebar-header .sidebar-toggler span,
        .sidebar-menu-button span {
       font-size: 13.5px;
        transition: 0.4s ease;
        }
        .sidebar.collapsed .sidebar-header .sidebar-toggler span {
        transform: rotate(180deg);
        }
        .sidebar-header .sidebar-toggler:hover {
        background: #d9e1fd;
        }
        .sidebar-nav .nav-list {
        list-style: none;
        display: flex;
        gap: 4px;
        padding: 0 15px;
        flex-direction: column;
        transform: translateY(15px);
        transition: 0.4s ease;
        }
        .sidebar .sidebar-nav .primary-nav {
        overflow-y: auto;
        scrollbar-width: thin;
        padding-bottom: 20px;
        height: calc(100vh - 227px);
        scrollbar-color: transparent transparent;
        }
        .sidebar .sidebar-nav .primary-nav:hover {
        scrollbar-color: #EEF2FF transparent;
        }
        .sidebar.collapsed .sidebar-nav .primary-nav {
        overflow: unset;
        transform: translateY(65px);
        }
        .sidebar-nav .nav-item .nav-link {
        color: #fff;
        display: flex;
        gap: 12px;
        white-space: nowrap;
        border-radius: 8px;
        padding: 11px 15px;
        align-items: center;
        text-decoration: none;
        border: 1px solid #151A2D;
        transition: 0.4s ease;
        }
        .sidebar-nav .nav-item:is(:hover, .open)>.nav-link:not(.dropdown-title) {
        color: #E6E6E6;
        background: #EEF2FF2E;
        }
        .sidebar .nav-link .nav-label {
        transition: opacity 0.3s ease;
        font-size: 14px;
        }
        .sidebar.collapsed .nav-link :where(.nav-label, .dropdown-icon) {
        opacity: 0;
        pointer-events: none;
        }
        .sidebar.collapsed .dropdown-menu .nav-link .nav-label,
        .sidebar.collapsed .dropdown-menu .nav-link .dropdown-icon {
            opacity: 1 !important;
            pointer-events: auto;
        }
        .sidebar.collapsed .nav-link .dropdown-icon {
        transition: opacity 0.3s 0s ease;
        }
        .sidebar-nav .secondary-nav {
        position: absolute;
        bottom: 35px;
        width: 100%;
        background: #151A2D;
        }
        .sidebar-nav .nav-item {
        position: relative;
        }
        /* Dropdown Stylings */
        .sidebar-nav .dropdown-container .dropdown-icon {
        margin: 0 -4px 0 auto;
        transition: transform 0.4s ease, opacity 0.3s 0.2s ease;
        }
        .sidebar-nav .dropdown-container.open .dropdown-icon {
        transform: rotate(180deg);
        }
        .sidebar-nav .dropdown-menu {
        height: 0;
        overflow-y: hidden;
        list-style: none;
        padding-left: 15px;
        transition: height 0.4s ease;
        }
        .sidebar.collapsed .dropdown-menu {
        position: absolute;
        top: -10px;
        left: 100%;
        opacity: 0;
        height: auto !important;
        padding-right: 10px;
        overflow-y: unset;
        pointer-events: none;
        border-radius: 0 10px 10px 0;
        background: #151A2D;
        transition: 0s;
        }
        .sidebar.collapsed .dropdown-menu:has(.dropdown-link) {
        padding: 7px 10px 7px 24px;
        }
        .sidebar.sidebar.collapsed .nav-item:hover>.dropdown-menu {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(12px);
        transition: all 0.4s ease;
        }
        .sidebar.sidebar.collapsed .nav-item:hover>.dropdown-menu:has(.dropdown-link) {
        transform: translateY(10px);
        }
        .dropdown-menu .nav-item .nav-link {
        color: #F1F4FF;
        padding: 9px 15px;
        font-size: 13px;
        }
        .sidebar.collapsed .dropdown-menu .nav-link {
        padding: 7px 15px;
        }

   .dropdown-menu .nav-item .nav-link.dropdown-title { display:none; }          /* hidden by default */
.sidebar.collapsed .dropdown-menu .nav-item .dropdown-title { display:block; } /* shown when collapsed */

.dropdown-menu:has(.dropdown-link) .nav-item .dropdown-title { font-weight:500; padding:7px 15px; }
     
        .sidebar-menu-button {
        display: none;
        }
        /* Responsive media query code for small screens */
        @media (max-width: 768px) {
        .sidebar-menu-button {
            position: fixed;
            left: 20px;
            top: 20px;
            height: 40px;
            width: 42px;
            display: flex;
            color: #F1F4FF;
            background: #151A2D;
        }
        .sidebar.collapsed {
            width: 270px;
            left: -270px;
        }
        .sidebar.collapsed .sidebar-header .sidebar-toggler {
            transform: none;
        }
        .sidebar.collapsed .sidebar-nav .primary-nav {
            transform: translateY(15px);
        }
        }
/* Default positions */
.main-content {
  background: #ECF0F5;
  position: relative;
  left: 270px;
  padding-right: 295px;
  transition: all 0.3s ease-in-out;
}

.extra-content {
  position: relative;
  left: 270px;
  padding-right: 265px;
  transition: all 0.3s ease-in-out;
}

/* When sidebar is collapsed */
body.sidebar-collapsed .main-content {
  left: 84px;
  padding-right: 110px;
}

body.sidebar-collapsed .extra-content {
  left: 85px;
  padding-right: 83px;
}


.nav-link.active {
  color: #E6E6E6 !important;
  background: #EEF2FF2E;
  font-weight: 600;
}



.dropdown-container.nested .dropdown-menu {
  transition: height 0.4s ease;
}

.dropdown-container.nested .dropdown-icon {
  font-size: 20px;
  margin-left: auto;
}
.material-symbols-rounded{
    font-size: 15px !important;
}

.dropdown-container:hover .dropdown-menu,
.dropdown-container .dropdown-toggle:focus ~ .dropdown-menu {
    display: block;
}
</style>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Mobile Sidebar Menu Button -->
    <button class="sidebar-menu-button">
      <span class="material-symbols-rounded">menu</span>
    </button>
    <aside class="sidebar">
      <!-- Sidebar Header -->
      <header class="sidebar-header">
        <a href="#" class="header-logo" style="color:white">
          <img src="logo.png" alt="LARA" />
        </a>
        <button class="sidebar-toggler">
          <span class="material-symbols-rounded">chevron_left</span>
        </button>
      </header>
      <nav class="sidebar-nav">
        <!-- Primary Top Nav -->
        <ul class="nav-list primary-nav">
          <li class="nav-item">
            <a href="{{ url('/admin/dashboard-view') }}" class="nav-link">
              <span class="material-symbols-rounded">dashboard</span>
              <span class="nav-label">Dashboard</span>
            </a>
            <ul class="dropdown-menu">
              <li class="nav-item"><a class="nav-link dropdown-title">Dashboard</a></li>
            </ul>
          </li>

           <li class="nav-item">
            <a href="{{ url('/admin/schedular') }}" class="nav-link">
              <span class="material-symbols-rounded">calendar_today</span>
              <span class="nav-label">Schedular</span>
            </a>
            <ul class="dropdown-menu">
              <li class="nav-item"><a class="nav-link dropdown-title">Schedular</a></li>
            </ul>
          </li>

          <!-- Staff -->
          <li class="nav-item dropdown-container">
            <a href="#" class="nav-link dropdown-toggle">
                <span class="material-symbols-rounded">person</span>
                <span class="nav-label">Staff</span>
                <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
            </a>

            <ul class="dropdown-menu">
                <li class="nav-item"><a class="nav-link dropdown-title">Staff</a></li>
                <li class="nav-item">
                <a href="{{ url('/admin/users') }}" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                    <span class="nav-label">List</span>
                </a>
                </li>

                <li class="nav-item dropdown-container nested">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                    <span class="nav-label">Teams</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>  
                <ul class="dropdown-menu">
                    <li class="nav-item">
                        <a href="{{ url('/admin/teams') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">List</span>
                        </a>
                        </li>
                        <li class="nav-item">
                        <a href="{{ url('/admin/teams/create') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">New</span>
                        </a>
                        </li>
                </ul>
                </li>

                <li class="nav-item">
                <a href="{{ url('/admin/archives') }}" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                    <span class="nav-label">Archived</span>
                </a>
                </li>

                <li class="nav-item dropdown-container nested">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                    <span class="nav-label">Document Hub</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>  
                <ul class="dropdown-menu">
                    <li class="nav-item">
                        <a href="{{ url('/admin/documents') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Shared</span>
                        </a>
                        </li>
                        <li class="nav-item">
                        <a href="{{ url('/admin/document-expires') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Expired</span>
                        </a>
                        </li>
                </ul>
                </li>

                <li class="nav-item">
                <a href="{{ url('/admin/users/create') }}" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                    <span class="nav-label">New</span>
                </a>
                </li>



                
            </ul>
            </li>

                <!-- CLIENTS -->

                <li class="nav-item dropdown-container">
                <a href="#" class="nav-link dropdown-toggle">
                    <span class="material-symbols-rounded">person</span>
                    <span class="nav-label">Clients</span>
                    <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                </a>

                <ul class="dropdown-menu">
                    <li class="nav-item"><a class="nav-link dropdown-title">Clients</a></li>
                    <li class="nav-item">
                    <a href="{{ url('/admin/clients') }}" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">keyboard_arrow_right</span>
                        <span class="nav-label">List</span>
                    </a>
                    </li>

                    <li class="nav-item dropdown-container nested">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">keyboard_arrow_right</span>
                        <span class="nav-label">Teams</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>  
                    <ul class="dropdown-menu">
                        <li class="nav-item">
                            <a href="{{ url('/admin/teams') }}" class="nav-link dropdown-toggle">
                                <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                <span class="nav-label">List</span>
                            </a>
                            </li>
                            <li class="nav-item">
                            <a href="{{ url('/admin/teams/create') }}" class="nav-link dropdown-toggle">
                                <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                <span class="nav-label">New</span>
                            </a>
                            </li>
                    </ul>
                    </li>

                    <li class="nav-item">
                    <a href="{{ url('/admin/client-archives') }}" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">keyboard_arrow_right</span>
                        <span class="nav-label">Archived</span>
                    </a>
                    </li>

                        <li class="nav-item">
                    <a href="{{ url('/admin/client-expire-documents') }}" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">keyboard_arrow_right</span>
                        <span class="nav-label">Expired Documents</span>
                    </a>
                    </li>


                    <li class="nav-item">
                    <a href="{{ url('/admin/clients/create') }}" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">keyboard_arrow_right</span>
                        <span class="nav-label">New</span>
                    </a>
                    </li>                    
                </ul>
                </li>

                <!-- TIMESHEET -->

                  <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">calendar_month</span>
                        <span class="nav-label">Timesheet</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>

                    <ul class="dropdown-menu">
                        <li class="nav-item"><a class="nav-link dropdown-title">Timesheet</a></li>
                        <li class="nav-item">
                        <a href="{{ url('/admin/timesheet-list') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">List</span>
                        </a>
                        </li>
                    </ul>
                </li>


                <!-- INVOICE -->

                 <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">account_balance</span>
                        <span class="nav-label">Invoice</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>

                    <ul class="dropdown-menu">
                    <li class="nav-item"><a class="nav-link dropdown-title">Invoice</a></li>

                        <li class="nav-item">
                        <a href="{{ url('/admin/invoice-list') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">List</span>
                        </a>
                        </li>

                        <li class="nav-item">
                        <a href="{{ url('/admin/invoice-void') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">List Void</span>
                        </a>
                        </li>

                        <li class="nav-item">
                        <a href="{{ url('/admin/invoice-generate') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Generate</span>
                        </a>
                        </li>
                    </ul>
                </li>


                 <!-- REPORTS -->

                 <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">pie_chart</span>
                        <span class="nav-label">Reports</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>

                    <ul class="dropdown-menu">
                    <li class="nav-item"><a class="nav-link dropdown-title">Reports</a></li>
                      
                        <li class="nav-item">
                        <a href="{{ url('/admin/activity') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Activity</span>
                        </a>
                        </li>

                        <li class="nav-item">
                        <a href="{{ url('/admin/billing') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Billing</span>
                        </a>
                        </li>

                        <li class="nav-item">
                        <a href="{{ url('/admin/performance') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Performance</span>
                        </a>
                        </li>

                        <li class="nav-item dropdown-container nested">
                                        <a href="#" class="nav-link dropdown-toggle">
                                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                            <span class="nav-label">Exception report</span>
                                            <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                                        </a>  
                                        <ul class="dropdown-menu">

                                            <li class="nav-item">
                                                <a href="{{ url('/admin/exception-report') }}" class="nav-link dropdown-toggle">
                                                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                                    <span class="nav-label">Delivered Vs Invoiced</span>
                                                </a>
                                                </li>
                                                
                                        </ul>
                                        </li>


                        <li class="nav-item">
                        <a href="{{ url('/admin/k-p-i') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">KPI</span>
                        </a>
                        </li>

                        <li class="nav-item">
                        <a href="{{ url('/admin/competency') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Competency</span>
                        </a>
                        </li>

                        <li class="nav-item">
                        <a href="{{ url('/admin/compliance') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Compliance</span>
                        </a>
                        </li>

                        <li class="nav-item">
                        <a href="{{ url('/admin/qualification') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Qualification</span>
                        </a>
                        </li>


                          <li class="nav-item dropdown-container nested">
                                        <a href="#" class="nav-link dropdown-toggle">
                                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                            <span class="nav-label">Events</span>
                                            <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                                        </a>  
                                        <ul class="dropdown-menu">
                                            <li class="nav-item">
                                                <a href="{{ url('/admin/event-summary') }}" class="nav-link dropdown-toggle">
                                                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                                    <span class="nav-label">Summary</span>
                                                </a>
                                                </li>
                                                <li class="nav-item">
                                                <a href="{{ url('/admin/event-detail') }}" class="nav-link dropdown-toggle">
                                                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                                    <span class="nav-label">Details</span>
                                                </a>
                                                </li>
                                        </ul>
                                        </li>
                    </ul>
                </li>


                <!-- ACCOUNT -->

                 <!-- INVOICE -->

                 <li class="nav-item dropdown-container">
                    <a href="#" class="nav-link dropdown-toggle">
                        <span class="material-symbols-rounded">settings</span>
                        <span class="nav-label">Account</span>
                        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                    </a>

                    <ul class="dropdown-menu">
                    <li class="nav-item"><a class="nav-link dropdown-title">Account</a></li>

                        <li class="nav-item">
                        <a href="{{ url('/admin/setting') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Settings</span>
                        </a>
                        </li>

                        <li class="nav-item">
                        <a href="{{ url('/admin/invoice-tax-settings') }}" class="nav-link dropdown-toggle">
                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                            <span class="nav-label">Invoice Settings</span>
                        </a>
                        </li>

                          <li class="nav-item dropdown-container nested">
                                        <a href="#" class="nav-link dropdown-toggle">
                                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                            <span class="nav-label">Price Items</span>
                                            <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                                        </a>  
                                        <ul class="dropdown-menu">
                                            <li class="nav-item">
                                                <a href="{{ url('/admin/prices') }}" class="nav-link dropdown-toggle">
                                                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                                    <span class="nav-label">List</span>
                                                </a>
                                                </li>
                                                <li class="nav-item">
                                                <a href="{{ url('/admin/price-setting') }}" class="nav-link dropdown-toggle">
                                                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                                    <span class="nav-label">Prices</span>
                                                </a>
                                                </li>
                                        </ul>
                                        </li>


                           <li class="nav-item dropdown-container nested">
                                        <a href="#" class="nav-link dropdown-toggle">
                                            <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                            <span class="nav-label">Pay Items</span>
                                            <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
                                        </a>  
                                        <ul class="dropdown-menu">
                                            <li class="nav-item">
                                                <a href="{{ url('/admin/pay-group-setting') }}" class="nav-link dropdown-toggle">
                                                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                                    <span class="nav-label">Pay Groups</span>
                                                </a>
                                                </li>
                                                <li class="nav-item">
                                                <a href="{{ url('/admin/allowances') }}" class="nav-link dropdown-toggle">
                                                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                                    <span class="nav-label">Allowances</span>
                                                </a>
                                                </li>
                                        </ul>
                                        </li>

                                             <li class="nav-item">
                                            <a href="{{ url('/admin/roles') }}" class="nav-link dropdown-toggle">
                                                <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                                <span class="nav-label">Roles</span>
                                            </a>
                                            </li>


                                                    <li class="nav-item">
                                                <a href="{{ url('/admin/permissions') }}" class="nav-link dropdown-toggle">
                                                    <span class="material-symbols-rounded">keyboard_arrow_right</span>
                                                    <span class="nav-label">Permissions</span>
                                                </a>
                                                </li>
                    </ul>
                </li>


                             <!-- OTHERS -->

              <li class="nav-item dropdown-container">
    <a href="#" class="nav-link dropdown-toggle">
        <span class="material-symbols-rounded">widgets</span>
        <span class="nav-label">Others</span>
        <span class="dropdown-icon material-symbols-rounded">keyboard_arrow_down</span>
    </a>

    <ul class="dropdown-menu">

        <!-- 1. Title – shown ONLY when .sidebar.collapsed -->
        <li class="nav-item">
            <a class="nav-link dropdown-title">Others</a>
        </li>

        <!-- 2. Real links – always visible -->
        <li class="nav-item">
            <a href="{{ url('/admin/media-managers') }}" class="nav-link dropdown-link">
                <span class="material-symbols-rounded">folder</span>
                <span class="nav-label">Media Manager</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('/admin/profile-setting') }}" class="nav-link dropdown-link">
                <span class="material-symbols-rounded">person</span>
                <span class="nav-label">Profile Setting</span>
            </a>
        </li>

    </ul>
</li>

         
        </ul>
      </nav>
    </aside>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const toggleDropdown = (dropdown, menu, isOpen) => {
    if (!menu) return;
    if (isOpen) {
      dropdown.classList.add("open");
      menu.style.height = `${menu.scrollHeight}px`;
    } else {
      dropdown.classList.remove("open");
      menu.style.height = 0;
    }
    adjustAllOpenDropdownHeights();
  };

  const adjustAllOpenDropdownHeights = () => {
    requestAnimationFrame(() => {
      document.querySelectorAll(".dropdown-container.open").forEach((openDropdown) => {
        const m = openDropdown.querySelector(":scope > .dropdown-menu");
        if (m) m.style.height = `${m.scrollHeight}px`;
      });
    });
  };

  const closeAllDropdowns = (except = null) => {
    document.querySelectorAll(".dropdown-container.open").forEach((openDropdown) => {
      if (openDropdown !== except) {
        const menu = openDropdown.querySelector(":scope > .dropdown-menu");
        toggleDropdown(openDropdown, menu, false);
      }
    });
  };

  const getOwnMenuForToggle = (toggleEl) => {
    const parentLi = toggleEl.closest("li");
    if (parentLi && parentLi.classList.contains("dropdown-container")) {
      const ownMenu = parentLi.querySelector(":scope > .dropdown-menu");
      if (ownMenu) return { container: parentLi, menu: ownMenu };
    }
    const nextSibling = toggleEl.nextElementSibling;
    if (nextSibling && nextSibling.classList.contains("dropdown-menu")) {
      const container = toggleEl.closest(".dropdown-container") || parentLi;
      return { container, menu: nextSibling };
    }
    return null;
  };

  // --------------------
  // Handle dropdown toggles
  // --------------------
  document.querySelectorAll(".dropdown-toggle").forEach((dropdownToggle) => {
    dropdownToggle.addEventListener("click", (e) => {
      const own = getOwnMenuForToggle(dropdownToggle);

      if (!own) {
        // It's a normal link, not a toggle
        const parentMenu = dropdownToggle.closest(".dropdown-menu");
        if (parentMenu) {
          const siblingContainers = parentMenu.querySelectorAll(":scope > .dropdown-container.open");
          siblingContainers.forEach((sib) => {
            if (!sib.contains(dropdownToggle)) {
              const sm = sib.querySelector(":scope > .dropdown-menu");
              toggleDropdown(sib, sm, false);
            }
          });
        }
        return; // Let the link navigate normally
      }

      // It's a dropdown toggle
      e.preventDefault();
      const dropdown = own.container;
      const menu = own.menu;
      const isOpen = dropdown.classList.contains("open");

      // Close sibling dropdowns at same level
      const parentMenu = dropdown.parentElement.closest(".dropdown-menu");
      let siblings = [];
      if (parentMenu) {
        siblings = Array.from(parentMenu.querySelectorAll(":scope > .dropdown-container.open"));
      } else {
        const primary = document.querySelector(".primary-nav");
        siblings = primary ? Array.from(primary.querySelectorAll(":scope > .dropdown-container.open")) : [];
      }
      siblings.forEach((sib) => {
        if (sib !== dropdown) {
          const sm = sib.querySelector(":scope > .dropdown-menu");
          toggleDropdown(sib, sm, false);
        }
      });

      toggleDropdown(dropdown, menu, !isOpen);
    });
  });

  // --------------------
  // Sidebar toggler logic
  // --------------------
  document.querySelectorAll(".sidebar-toggler, .sidebar-menu-button").forEach((button) => {
    button.addEventListener("click", () => {
      closeAllDropdowns();
      const sidebar = document.querySelector(".sidebar");
      if (!sidebar) return;
      sidebar.classList.toggle("collapsed");
      document.body.classList.toggle("sidebar-collapsed", sidebar.classList.contains("collapsed"));
    });
  });

  if (window.innerWidth <= 1024) {
    const sb = document.querySelector(".sidebar");
    if (sb) {
      sb.classList.add("collapsed");
      document.body.classList.add("sidebar-collapsed");
    }
  }

  window.addEventListener("resize", adjustAllOpenDropdownHeights);

  // --------------------
  // ✅ Auto-activate current link + open dropdowns
  // --------------------
const currentUrl = window.location.pathname;
const links = Array.from(document.querySelectorAll(".nav-link[href]"));

let bestMatch = null;
let bestMatchLength = 0;

links.forEach((link) => {
  const href = link.getAttribute("href");
  if (!href || href === "#") return; // ignore dummy links

  const linkUrl = new URL(link.href, window.location.origin).pathname;

  // Match exact path or nested (e.g. /admin/teams matches /admin/teams/list)
  if (currentUrl === linkUrl || currentUrl.startsWith(linkUrl + "/")) {
    if (linkUrl.length > bestMatchLength) {
      bestMatch = link;
      bestMatchLength = linkUrl.length;
    }
  }
});

if (bestMatch) {
  bestMatch.classList.add("active");

  // Highlight its dropdown parents
  let parentDropdown = bestMatch.closest(".dropdown-container");
  while (parentDropdown) {
    const menu = parentDropdown.querySelector(":scope > .dropdown-menu");
    if (menu) {
      parentDropdown.classList.add("open");
      menu.style.height = `${menu.scrollHeight}px`;
    }
    parentDropdown = parentDropdown.parentElement.closest(".dropdown-container");
  }
}

adjustAllOpenDropdownHeights();
});
</script>
