<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Leave;
use Illuminate\Support\Facades\DB; 


class EmployeeController extends Controller
{
    public function getTotalEmployees()
    {
        // Get the count of users where status = 1
        $totalEmployees = User::where('status', 1)->count();

        // Return the count as a JSON response
        return response()->json([
            'total_employees' => $totalEmployees
        ]);
    }

    public function getPendingApprovals()
    {
        // Count all rows where management_approval is 'Pending'
        $pendingApprovals = Leave::where('management_approval', 'Pending')->count();

        // Return the count as a JSON response
        return response()->json([
            'pending_approvals' => $pendingApprovals
        ]);
    }

    public function showRegister()
    {
        // Fetch users with their employee ID (emp_no) and name
        $employees = User::select('id', 'name', 'emp_no')->where("status",1)->get();

        // Pass the employee data to the Blade view
        return view('hr.daily-register', compact('employees'));
    }


    public function getTodayRegister()
    {
        $today = now()->toDateString(); // Get today's date in YYYY-MM-DD format
        
        // Fetch today's register data
        $register = DB::table('register')
            ->where('date', $today)
            ->get();
    
        // Fetch all employees and merge their register status
        $employees = DB::table('users')
            ->select('id', 'name', 'emp_no')
            ->where('status', 1)
            ->get()
            ->map(function ($employee) use ($register) {
                $currentStatus = $register->firstWhere('user_id', $employee->id);
                $employee->status = $currentStatus ? $currentStatus->status : 0; // Default to 0 if no register entry
                return $employee;
            });

        $activeUser = DB::table('register')
            ->where('date', $today)
            ->where('status', 1)
            ->get();
    
        // Calculate the count of employees
        $employeeCount = $employees->count();

        // Calculate the count of active employees
        $activeEmployeeCount = $activeUser->count();
    
        // Include the count in the response
        return response()->json([
            'count' => $employeeCount,
            'activeCount' => $activeEmployeeCount,
            'employees' => $employees
        ]);
    }
    
    
    public function submitRegister(Request $request)
    {
        $today = now()->toDateString(); // Current date in YYYY-MM-DD
    
        // Validate the input data
        $request->validate([
            'register' => 'required|array',
            'register.*.user_id' => 'required|exists:users,id',
            'register.*.status' => 'required|in:1,0',
        ]);
    
        // Iterate through each register entry
        foreach ($request->register as $entry) {
            DB::table('register')->updateOrInsert(
                [
                    'user_id' => $entry['user_id'], // Match by user ID
                    'date' => $today,               // Match by today's date
                ],
                [
                    'status' => $entry['status'],  // Update the status (1 or 0)
                    'updated_at' => now(),         // Update the timestamp
                ]
            );
        }
    
        return response()->json(['message' => 'Register updated successfully!']);
    }
    
    public function getRegisterCounts()
    {
        $today = now()->toDateString(); // Current date
    
        // Fetch counts for Active and On Leave employees for today
        $activeCount = DB::table('register')
            ->where('date', $today)
            ->where('status', 1)
            ->count();
    
        $onLeaveCount = DB::table('register')
            ->where('date', $today)
            ->where('status', 0)
            ->count();
    
        // Return the counts as a JSON response
        return response()->json([
            'active_employees' => $activeCount,
            'on_leave_employees' => $onLeaveCount
        ]);
    }
    

}