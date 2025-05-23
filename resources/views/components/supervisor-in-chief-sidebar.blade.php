<!doctype html>
<html lang="en">
  <head>
  	<title>Sidebar 01</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
		
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="css/style.css">
  </head>
  <body>
		
		<div class="wrapper d-flex align-items-stretch">
			<nav id="sidebar">
				<div class="p-4 pt-5">
                  <img class="img logo rounded-circle mb-5" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
	        <ul class="list-unstyled components mb-5">
                <li class="{{ Request::is('home') ? 'active' : '' }}">
                    <a href="/home">Supervisor in chief Dashboard</a>
                </li>
	          <li>

            <li class="{{ Request::is('request-supervisor-ic-leave') ? 'active' : '' }}">
              <a href="/request-supervisor-ic-leave">Request Leave</a>
	          </li>

            <li class="{{ Request::is('manage-supervisor-ic-leave') ? 'active' : '' }}">
              <a href="/manage-supervisor-ic-leave">Manage My Leave</a>
	          </li>

	          <li class="{{ Request::is('view-leaves-sic') ? 'active' : '' }}">
              <a href="/view-leaves-sic">Manage Employee Leave</a>
	          </li>

            <li class="{{ Request::is('track-attendance-Sic') ? 'active' : '' }}">
              <a href="/track-attendance-Sic">Track My Attendance</a>
	          </li>


            <li class="{{ Request::is('supportSup') ? 'active' : '' }}">
              <a href="/supportSup">Help and Support</a>
	          </li>


            <li class="{{ Request::is('view-sic-leaves') ? 'active' : '' }}">
              <a href="/view-sic-leaves">Leave History</a>
	          </li>

	        </ul>

	        <div class="footer">
	        	<p><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
						  Copyright &copy;<script>document.write(new Date().getFullYear());</script>  <a href="https://www.arrogance.lk/" target="_blank"> Arrogance Technologies (Pvt) Ltd </a> - All Rights Reserved.  
						  <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
	        </div>

	      </div>
    	</nav>

        

        <div id="content" class="p-0 p-md-0">

            <button type="button" id="sidebarCollapse" class="btn btn-primary">
                <i class="fa fa-bars"></i>
                <span class="sr-only">Toggle Menu</span>
            </button>
        
 





    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
  </body>
</html>