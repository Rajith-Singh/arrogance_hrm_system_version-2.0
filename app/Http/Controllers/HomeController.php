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
        $userData = ['usertype' => Auth::user()->usertype];

        if (Auth::user()->usertype === 'user') {
            // Redirect to the dashboard route
            return redirect()->route('dashboard');
        } else if (Auth::user()->usertype === 'hr' || Auth::user()->usertype === 'supervisor' ) {
            $remainingLeaves = $this->leaveController->getRemainingLeaves(request());
            $userData['remainingLeaves'] = $remainingLeaves;

            // Fetch leave data for the calendar
            $leaves = $this->fetchLeaveData();
            $userData['leaves'] = $leaves;
        }
 
        return view($this->getViewForUserType(), $userData);
    }

    private function getViewForUserType()
    {
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

    // Fetch leave data to pass to the calendar component
    public function fetchLeaveData()
    {
        if (Auth::user()->usertype === 'hr'){
            return $this->leaveController->getLeaveDataForCalendar();
        }
        else if (Auth::user()->usertype === 'management'){
            return $this->leaveController->getLeaveDataForCalendar();
        }
        else if (Auth::user()->usertype === 'supervisor'){
            return $this->leaveController->getLeaveSupDataForCalendar();
        }
        return collect(); 
        // Assuming LeaveController has a method to fetch all leaves
    }

    public function dashboard()
    {
        $userData = ['usertype' => Auth::user()->usertype];

        // Fetch remaining leaves if user type is 'user'
        if (Auth::user()->usertype == 'user') {
            $remainingLeaves = $this->leaveController->getRemainingLeaves(request());
            $userData['remainingLeaves'] = $remainingLeaves;
        }

        return view('dashboard', $userData);
    }
}
