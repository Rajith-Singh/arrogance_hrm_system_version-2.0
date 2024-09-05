<!doctype html>
<html lang="en">
  <head>
  	<title>Sidebar</title>
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
              <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                    <a href="/home">User Dashboard</a>
                </li>
                <li class="{{ Request::is('request-leave') ? 'active' : '' }}">
                    <a href="/request-leave">Request Leave</a>
                </li>
                <li class="{{ Request::is('manage-leave') ? 'active' : '' }}">
                    <a href="/manage-leave">Manage Leaves</a>
                </li>
                <li class="{{ Request::is('track-attendance') ? 'active' : '' }}">
                    <a href="/track-attendance">Attendance Tracking</a>
                </li>

                <li class="{{ Request::is('view-my-leaves') ? 'active' : '' }}">
                  <a href="/view-my-leaves">Notifications</a>
                </li>

                <li>
                  <a href="#">Help and Support</a>
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