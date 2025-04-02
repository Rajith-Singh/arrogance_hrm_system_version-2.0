<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RemoteAttendance;
use App\Models\User;
use Carbon\Carbon;

class RemoteAttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Get date filter from request, default to today
        $date = $request->get('date', Carbon::today()->toDateString());

        // Fetch data from database filtered by the selected date
        $attendances = RemoteAttendance::whereDate('date', $date)
            ->orderBy('check_in_time', 'asc')
            ->get();
            
        // Fetch employee names from the 'users' table based on 'employee_id'
        foreach ($attendances as $attendance) {
            $user = User::find($attendance->employee_id);
            $attendance->employee_name = $user ? $user->name : 'Unknown';
            $attendance->emp_id = $user ? $user->emp_no : 'Unknown';  // // Add employee name to the attendance object
            $attendance->check_in_time = Carbon::parse($attendance->check_in_time)->format('H:i:s'); // Format only time
            $attendance->check_out_time = $attendance->check_out_time ? Carbon::parse($attendance->check_out_time)->format('H:i:s') : null;
        }

        return view('hr.monitor', compact('attendances', 'date'));
    }
}
