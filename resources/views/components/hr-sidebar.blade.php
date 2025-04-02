<!doctype html>
<html lang="en">
  <head>
    <title>Sidebar</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
      .submenu {
        display: none;
      }
      .submenu-active {
        display: block;
      }
      .expand-icon {
        float: right;
      }
    </style>
  </head>
  <body>
    <div class="wrapper d-flex align-items-stretch">
      <nav id="sidebar">
        <div class="p-4 pt-5">
          <img class="img logo rounded-circle mb-5" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
          <ul class="list-unstyled components mb-5">
            <li class="{{ Request::is('home') ? 'active' : '' }}">
              <a href="/home">HR Dashboard</a>
            </li>
            <li>
              <a href="#" class="main-category" data-category="salary-management">Employee Management <i class="fa fa-chevron-down expand-icon"></i></a>
              <ul class="submenu" id="salary-management-submenu">
                <li class="{{ Request::is('users') ? 'active' : '' }}">
                  <a href="/users">Employee Details</a>
                </li>
              </ul>
            </li>
            <li>
              <a href="#" class="main-category" data-category="leave-management">Leave Management <i class="fa fa-chevron-down expand-icon"></i></a>
              <ul class="submenu" id="leave-management-submenu">
                <li class="{{ Request::is('add-attendance') ? 'active' : '' }}">
                  <a href="/add-attendance">Add Attendance</a>
                </li>
                <li class="{{ Request::is('view-emp-attendance') ? 'active' : '' }}">
                  <a href="/view-emp-attendance">Edit Attendance</a>
                </li>
                <li class="{{ Request::is('add-manual-attendance') ? 'active' : '' }}">
                  <a href="/add-manual-attendance">Add Attendance (Manual)</a>
                </li>
                <li class="{{ Request::is('monitor-attendance') ? 'active' : '' }}">
                  <a href="/monitor-attendance">Remote Attendance</a>
                </li>
                <li class="{{ Request::is('add-manual-leave') ? 'active' : '' }}">
                  <a href="/add-manual-leave">Add Leave (Manual)</a>
                </li>
                <li class="{{ Request::is('add-leave-type') ? 'active' : '' }}">
                  <a href="/add-leave-type">Add Leave Type</a>
                </li>
                <li class="{{ Request::is('edit-delete-leave') ? 'active' : '' }}">
                  <a href="/edit-delete-leave">Edit/Delete Leave</a>
                </li>
                <li class="{{ Request::is('reports') ? 'active' : '' }}">
                  <a href="/reports">Report</a>
                </li>
                <li class="{{ Request::is('add-holiday') ? 'active' : '' }}">
                  <a href="/add-holiday">Add Holiday</a>
                </li>
              </ul>
            </li>
            <li>
              <a href="#" class="main-category" data-category="personal-leave-management">My Personal Leave Management <i class="fa fa-chevron-down expand-icon"></i></a>
              <ul class="submenu" id="personal-leave-management-submenu">
                <li class="{{ Request::is('request-hr-leave') ? 'active' : '' }}">
                  <a href="/request-hr-leave">Request Leave</a>
                </li>
                <li class="{{ Request::is('manage-leave') ? 'active' : '' }}">
                  <a href="/manage-leave">Manage My Leaves</a>
                </li>
                <li class="{{ Request::is('track-attendance-HR') ? 'active' : '' }}">
                  <a href="/track-attendance-HR">My Attendance Tracking</a>
                </li>
                <li class="{{ Request::is('view-my-leaves') ? 'active' : '' }}">
                  <a href="/view-my-leaves">Notifications</a>
                </li>
              </ul>
            </li>
            <li>
              <a href="#" class="main-category" data-category="salary-management">Salary Management <i class="fa fa-chevron-down expand-icon"></i></a>
              <ul class="submenu" id="salary-management-submenu">
                <li>
                  <a href="#">Salary Details</a>
                </li>
              </ul>
            </li>
            <li>
              <a href="#" class="main-category" data-category="help-support">Help and Support <i class="fa fa-chevron-down expand-icon"></i></a>
              <ul class="submenu" id="help-support-submenu">

                <li class="{{ Request::is('supportHR') ? 'active' : '' }}">
                  <a href="/supportHR">Help and Support</a>
                </li>

              </ul>
            </li>
          </ul>
          <div class="footer">
            <p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
              Copyright &copy;<script>document.write(new Date().getFullYear());</script> <a href="https://www.arrogance.lk/" target="_blank"> Arrogance Technologies (Pvt) Ltd </a> - All Rights Reserved.
              <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
          </div>
        </div>
      </nav>
      
      <div id="content" class="p-0 p-md-0">
        <button type="button" id="sidebarCollapse" class="btn btn-primary">
            <i class="fa fa-bars"></i>
            <span class="sr-only">Toggle Menu</span>
        </button>
      

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
      $(document).ready(function() {
        // Restore the state from localStorage
        const activeCategory = localStorage.getItem('activeCategory');
        if (activeCategory) {
          $('#' + activeCategory + '-submenu').addClass('submenu-active');
          $('a[data-category="' + activeCategory + '"]').find('.expand-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }

        $('.main-category').on('click', function() {
          // Collapse all submenus
          $('.submenu').removeClass('submenu-active');
          $('.expand-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');

          // Expand the clicked submenu
          const category = $(this).data('category');
          if (!$('#' + category + '-submenu').hasClass('submenu-active')) {
            $('#' + category + '-submenu').addClass('submenu-active');
            $(this).find('.expand-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            localStorage.setItem('activeCategory', category);
          } else {
            localStorage.removeItem('activeCategory');
          }
        });
      });
    </script>
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>

