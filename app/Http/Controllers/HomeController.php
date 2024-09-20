<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LeaveController;

class HomeController extends Controller
{
    protected $leaveController;

    public function __construct(LeaveController $leaveController)
    {
        // Protect all methods in this controller using 'auth' middleware
        //$this->middleware('auth');

        // Set the LeaveController dependency
        $this->leaveController = $leaveController;
    }

    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Redirect to login if the user is not authenticated
            return redirect()->route('login');
        }

        // Initialize user data
        $userData = ['usertype' => Auth::user()->usertype];

        // Fetch leave data for authenticated user
        $leaves = $this->fetchLeaveData();
        $userData['leaves'] = $leaves; // Pass the leaves data to the view

        // Handle different user types
        if (Auth::user()->usertype === 'user') {
            // Fetch remaining leaves for user
            $remainingLeaves = $this->leaveController->getRemainingLeaves(request());
            $userData['remainingLeaves'] = $remainingLeaves;

            // Return the dashboard view with leaves and remaining leaves data
            return view('dashboard', $userData);

        } elseif (in_array(Auth::user()->usertype, ['hr', 'supervisor', 'management'])) {
            // Fetch remaining leaves for HR and supervisors
            if (Auth::user()->usertype !== 'management') {
                $remainingLeaves = $this->leaveController->getRemainingLeaves(request());
                $userData['remainingLeaves'] = $remainingLeaves;
            }

            // Return the appropriate view for HR, Supervisor, or Management
            return view($this->getViewForUserType(), $userData);
        }

        // Fallback for undefined user types (redirect to dashboard)
        return view('dashboard', $userData);
    }

    private function getViewForUserType()
    {
        // Make sure the user is authenticated before accessing their usertype
        if (!Auth::check()) {
            return 'auth.login'; // Return login view if the user is not authenticated
        }

        // Switch case to return the appropriate view for different user types
        switch (Auth::user()->usertype) {
            case 'admin':
                return 'admin.home';
            case 'supervisor':
                return 'supervisor.home';
            case 'management':
                return 'management.home';
            case 'hr':
                return 'hr.home';
            default:
                return 'dashboard'; // Fallback for 'user' and any other types
        }
    }

    public function fetchLeaveData()
    {
        // Ensure user is authenticated before fetching leave data
        if (!Auth::check()) {
            return collect(); // Return empty collection if not authenticated
        }

        // Fetch leave data based on user type
        switch (Auth::user()->usertype) {
            case 'hr':
                return $this->leaveController->getLeaveDataForCalendar();
            case 'management':
                return $this->leaveController->getLeaveMgtDataForCalendar();
            case 'supervisor':
                return $this->leaveController->getLeaveSupDataForCalendar();
            case 'user':
                // Fetch logged-in user's leaves
                return $this->leaveController->getUserLeaveDataForCalendar(Auth::id());
            default:
                return collect(); // Return empty collection if no leaves are available
        }
    }

    public function dashboard()
    {
        // Ensure user is authenticated before accessing the dashboard
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect to login if the user is not authenticated
        }

        // Initialize user data for the dashboard
        $userData = ['usertype' => Auth::user()->usertype];

        // Fetch remaining leaves if user type is 'user'
        if (Auth::user()->usertype == 'user') {
            $remainingLeaves = $this->leaveController->getRemainingLeaves(request());
            $userData['remainingLeaves'] = $remainingLeaves;
        }

        // Return the dashboard view with user data
        return view('dashboard', $userData);
    }
}
