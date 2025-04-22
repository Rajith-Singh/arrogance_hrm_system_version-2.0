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
        $this->leaveController = $leaveController;
    }

    public function index()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            // Fetch leave data for the calendar
            $leaves = $this->fetchLeaveData();
            $userData['leaves'] = $leaves;

            // Redirect to the dashboard route with leaves data
            return view('dashboard', $userData);
        }

        $userData = ['usertype' => Auth::user()->usertype];

        // Fetch leave data for user and pass it to the view
        if (Auth::user()->usertype === 'user') {
            $leaves = $this->fetchLeaveData(); // Assuming this method fetches leave data for users
            $userData['leaves'] = $leaves; // Pass the leaves data to the view
            //dd($userData['leaves']);

            // Fetch remaining leaves data if necessary
            $remainingLeaves = $this->leaveController->getRemainingLeaves(request());
            $userData['remainingLeaves'] = $remainingLeaves;

            // Return the dashboard view with both leaves and remaining leaves data
            return view('dashboard', $userData);
        } else if (Auth::user()->usertype === 'hr' || Auth::user()->usertype === 'supervisor' || Auth::user()->usertype === 'supervisor-in-chief') {
            $remainingLeaves = $this->leaveController->getRemainingLeaves(request());
            $userData['remainingLeaves'] = $remainingLeaves;

            // Fetch leave data for the calendar
            $leaves = $this->fetchLeaveData();
            $userData['leaves'] = $leaves;
        } else if (Auth::user()->usertype === 'management') {
            // Fetch leave data for the calendar
            $leaves = $this->fetchLeaveData();
            $userData['leaves'] = $leaves;
        }

        return view($this->getViewForUserType(), $userData);
    }

    private function getViewForUserType()
    {
        if (!Auth::check()) {
            return 'auth.login'; // Return login view if user is not authenticated
        }

        switch (Auth::user()->usertype) {
            case 'admin':
                return 'admin.home';
            case 'supervisor':
                return 'supervisor.home';
            case 'supervisor-in-chief':
                return 'supervisor-in-chief.home';
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
        if (!Auth::check()) {
            return collect(); // Return empty if not authenticated
        }

        if (Auth::user()->usertype === 'hr') {
            return $this->leaveController->getLeaveDataForCalendar();
        } else if (Auth::user()->usertype === 'management') {
            return $this->leaveController->getLeaveMgtDataForCalendar();
        } else if (Auth::user()->usertype === 'supervisor') {
            return $this->leaveController->getLeaveSupDataForCalendar();
        } else if (Auth::user()->usertype === 'supervisor-in-chief') {
            return $this->leaveController->getLeaveSicDataForCalendar();
        } elseif (Auth::user()->usertype === 'user') {
            // Fetch the logged-in user's leaves
            return $this->leaveController->getUserLeaveDataForCalendar(Auth::id()); // Pass the user's ID
        }

        return collect(); // Return an empty collection if no leaves are available
    }

    public function dashboard()
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect to login if the user is not authenticated
        }

        $userData = ['usertype' => Auth::user()->usertype];

        // Fetch remaining leaves if user type is 'user'
        if (Auth::user()->usertype == 'user') {
            $remainingLeaves = $this->leaveController->getRemainingLeaves(request());
            $userData['remainingLeaves'] = $remainingLeaves;
        }

        return view('dashboard', $userData);
    }
}
