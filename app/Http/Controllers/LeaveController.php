<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification; // Ensure this is included
use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\User;
use App\Models\LeaveType;
use Illuminate\Support\Facades\View; // Import the View facade
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveRequestMail;
use App\Mail\ManagementApprovalMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;



use Carbon\Carbon;



class LeaveController extends Controller
{

    public function storeLeave(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'other_leave_type' => 'nullable|required_if:leave_type,Other', // Add validation for other_leave_type
            'start_date' => 'required',
            'end_date' => 'required',
            'reason' => 'required',
        ]);
    
        $leave = new Leave;
    
        $leave->user_id = auth()->user()->id;
        $user_name = auth()->user()->name;
    
        // Check if 'Other' was selected and use 'other_leave_type' if so
        if ($request->leave_type === 'Other') {
            $leave->leave_type = $request->other_leave_type;
        } else {
            $leave->leave_type = $request->leave_type;
        }
    
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->reason = $request->reason;
        $leave->additional_notes = $request->additional_notes;
        $leave->covering_person = $request->covering_person;
    
        // Set supervisor_approval and management_approval to "Approved" for Short Leave requests
        if ($leave->leave_type === 'Short Leave') {
            $leave->supervisor_approval = "Approved";
            $leave->management_approval = "Approved";
        } else {
            $leave->supervisor_approval = "Pending";
            $leave->management_approval = "Pending";
        }
    
        $leave->save();
    
        // Determine the supervisor for the user's department
        $department = auth()->user()->department;
        $supervisor = User::where('department', $department)
                        ->where('usertype', 'supervisor')
                        ->first();
    
        if ($supervisor) {
            // Create a notification for the supervisor
            $message = "New leave request from $user_name requires your approval.";
            Notification::create([
                'user_id' => $supervisor->id,
                'message' => $message,
                'leave_id' => $leave->id, // Add leave_id
                'emp_id' => auth()->user()->id, // Add emp_id
            ]);

            // Send email to the supervisor
            Mail::to($supervisor->email)->send(new LeaveRequestMail($leave));
        }
    
        return back()->with('msg', 'Your leave request has been successfully processed.');
    }



    public function viewLeaves(Request $request) {
        // $leaves = Leave::where('user_id', auth()->user()->id)->get();  Fetch leaves for the authenticated user

        $leaves = Leave::join('users', 'users.id', '=', 'leaves.user_id')
                    ->join('users as covering_users', 'covering_users.id', '=', 'leaves.covering_person')
                    ->select(
                        'users.id',
                        'covering_users.name',
                        'leaves.id',
                        'leaves.user_id',
                        'leaves.leave_type',
                        'leaves.start_date',
                        'leaves.end_date',
                        'leaves.reason',
                        'leaves.additional_notes',
                        'leaves.additional_notes'
                    )
                    ->where('leaves.user_id', auth()->user()->id)
                    ->where('leaves.supervisor_approval', "Pending")
                    ->get();


        $manageLeaveView = View::make('components.manage-leave', ['leave' => $leaves])->render(); // Render the manage-leave view
        return view('emp-manage-leave', ['manageLeaveView' => $manageLeaveView]);
    }

    public function editLeave($id){
        $data = DB::table('leaves')->where('id',$id)->first();
        $users = $this->fetchUsers();  // Fetch users using the refactored method
        return view('emp-edit-leave', compact('data', 'users'));
    }

    public function updateLeave(Request $request) {
        $request->validate([
            'leave_type' => 'required',
            'other_leave_type' => 'nullable|required_if:leave_type,Other', // Validation for other_leave_type
            'start_date' => 'required',
            'end_date' => 'required',
            'reason' => 'required',
            'covering_person' => 'required',
        ]);
    
        // Determine the correct leave type to store
        $leaveType = $request->leave_type === 'Other' ? $request->other_leave_type : $request->leave_type;
    
        // Update the leave record
        DB::table('leaves')->where('id', $request->id)->update([
            'leave_type' => $leaveType,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'additional_notes' => $request->additional_notes,
            'covering_person' => $request->covering_person,
        ]);
    
        // Redirect based on user type
        if (auth()->user()->usertype == 'user') {
            return redirect()->to('/manage-leave')->with('message', 'Leave successfully updated!');
        } else if (auth()->user()->usertype == 'supervisor') {
            return redirect()->to('/manage-supervisor-leave')->with('message', 'Leave successfully updated!');
        } else if (auth()->user()->usertype == 'management') {
            return redirect()->to('/manage-management-leave')->with('message', 'Leave successfully updated!');
        } else if (auth()->user()->usertype == 'admin') {
            return redirect()->to('/manage-leave')->with('message', 'Leave successfully updated!');
        }
    }

    public function deleteLeave($id){
        DB::table('leaves')->where('id',$id)->delete();
        return back()->with('deleteLeave')->with('del-message', 'Leave successfully deleted.');
    }

    public function viewEmpLeave(Request $request) {
        $thirtyDaysAgo = \Carbon\Carbon::now()->subDays(30);

        $leaves = Leave::join('users', 'users.id', '=', 'leaves.user_id')
                    ->select('users.name',
                            'users.department',
                            'leaves.id',
                            'leaves.user_id',
                            'leaves.leave_type', 
                            'leaves.supervisor_approval', 
                            'leaves.management_approval', 
                            )
                    ->where('users.department', auth()->user()->department)
                    ->where('users.usertype', 'user')
                    ->where('leaves.created_at', '>=', $thirtyDaysAgo)
                    ->orderBy('leaves.created_at', 'desc')
                    ->get();
        $manageLeavesView = View::make('components.sup-get-leave', ['leave' => $leaves])->render(); // Render the manage-leave view
        return view('supervisor.sup-manage-leave', ['manageLeavesView' => $manageLeavesView]);

    }

    // public function viewEmpLeaveRequest($user_id,$leave_id){
    //     $thirtyDaysAgo = \Carbon\Carbon::now()->subDays(30);

    //     $data = Leave::join('users', 'users.id', '=', 'leaves.user_id')
    //                 ->select(
    //                     'users.name',
    //                     'leaves.id',
    //                     'leaves.user_id',
    //                     'leaves.leave_type',
    //                     'leaves.start_date',
    //                     'leaves.end_date',
    //                     'leaves.reason',
    //                     'leaves.additional_notes'
    //                 )
    //                 ->where('leaves.user_id', $user_id)
    //                 ->where('leaves.id', $leave_id)
    //                 ->first();
                
    //     $LeaveView = view('components.sup-leave-view', compact('data'))->render(); // Render the edit-leave view
    //     return view('supervisor.sup-emp-leave-view', ['LeaveView' => $LeaveView]);
    // }


    public function viewEmpLeaveRequest($user_id, $leave_id)
    {
        $data = Leave::join('users', 'users.id', '=', 'leaves.user_id')
            ->select(
                'users.name',
                'leaves.id',
                'leaves.user_id',
                'leaves.leave_type',
                'leaves.start_date',
                'leaves.end_date',
                'leaves.reason',
                'leaves.additional_notes'
            )
            ->where('leaves.user_id', $user_id)
            ->where('leaves.id', $leave_id)
            ->first();

        $leaveHistory = Leave::select('leave_type', \DB::raw('count(*) as total'))
            ->where('user_id', $user_id)
            ->groupBy('leave_type')
            ->get();

        $LeaveView = view('components.sup-leave-view', compact('data', 'leaveHistory'))->render();
        return view('supervisor.sup-emp-leave-view', ['LeaveView' => $LeaveView]);
    }
    
    public function updateSupervisorApproval(Request $request)
    {
        // Update the leave status
        DB::table('leaves')->where('id', $request->leave_id)
                            ->where('user_id', $request->user_id)
                            ->update([
                                'supervisor_approval' => $request->approval_status,
                                'supervisor_note' => $request->supervisor_note,
                            ]);
    
        // Retrieve the leave information
        $leave = Leave::find($request->leave_id);
        $user = User::find($request->user_id);
        $supervisor_name = auth()->user()->name;
        $status_message = $request->approval_status === 'Approved' ? "approved" : "rejected";
        $message = "Your leave request has been $status_message by $supervisor_name.";
    
        // Mark the original notification as read
        Notification::where('leave_id', $request->leave_id)
                    ->where('emp_id', $request->user_id)
                    ->update(['read' => 1]);
    
        // Save the notification to the database
        Notification::create([
            'user_id' => $user->id,
            'message' => $message,
            'leave_id' => $request->leave_id,
            'emp_id' => $request->user_id,
        ]);
    
        // Emit notification event to the employee
        $data = [
            'user_id' => $user->id,
            'message' => $message,
            'leave_id' => $request->leave_id,
            'emp_id' => $request->user_id,
        ];
        Http::post('http://192.168.10.3:3001/notify', $data);
    
        // Notify management if the leave is approved
        if ($request->approval_status === 'Approved') {
            $management_users = User::where('usertype', 'management')->get();
            foreach ($management_users as $manager) {
                $management_message = "Leave request from $user->name has been approved by $supervisor_name and requires your review.";
                Notification::create([
                    'user_id' => $manager->id,
                    'message' => $management_message,
                    'leave_id' => $request->leave_id,
                    'emp_id' => $request->user_id,
                ]);
    
                // Emit notification event for management
                $data = [
                    'user_id' => $manager->id,
                    'message' => $management_message,
                    'leave_id' => $request->leave_id,
                    'emp_id' => $request->user_id,
                ];
                Http::post('http://192.168.10.3:3001/notify', $data);
    
                // Send email to management
                try {
                    Mail::to($manager->email)->send(new ManagementApprovalMail($leave, $supervisor_name));
                } catch (\Exception $e) {
                    Log::error('Failed to send email to management: ' . $e->getMessage());
                }
            }
        }
    
        return redirect()->to('/view-leaves')->with('message', 'Leave status successfully updated!');
    }
    


    public function viewEmpLeaveMgt(Request $request) {
        $thirtyDaysAgo = \Carbon\Carbon::now()->subDays(30);

        $leaves = Leave::join('users', 'users.id', '=', 'leaves.user_id')
                    ->select('users.name',
                            'leaves.id',
                            'leaves.user_id',
                            'leaves.leave_type', 
                            'leaves.supervisor_approval', 
                            'leaves.management_approval', 
                            )
                    ->where('supervisor_approval', 'Approved')
                    ->where('leaves.created_at', '>=', $thirtyDaysAgo)
                    ->orderBy('leaves.created_at', 'desc')
                    ->get();

                    
        $manageLeavesView = View::make('components.mgt-get-leave', ['leave' => $leaves])->render(); // Render the manage-leave view
        return view('management.mgt-manage-leave', ['manageLeavesView' => $manageLeavesView]);

    }

    public function viewMgtLeaveRequest($user_id, $leave_id)
    {
        $data = Leave::join('users', 'users.id', '=', 'leaves.user_id')
            ->select(
                'users.name',
                'leaves.id',
                'leaves.user_id',
                'leaves.leave_type',
                'leaves.start_date',
                'leaves.end_date',
                'leaves.reason',
                'leaves.additional_notes'
            )
            ->where('leaves.user_id', $user_id)
            ->where('leaves.id', $leave_id)
            ->first();

        $leaveHistory = Leave::select('leave_type', \DB::raw('count(*) as total'))
            ->where('user_id', $user_id)
            ->groupBy('leave_type')
            ->get();

        $LeaveView = view('components.mgt-leave-view', compact('data', 'leaveHistory'))->render();
        return view('management.mgt-emp-leave-view', ['LeaveView' => $LeaveView]);
    }
    
    public function updateManagementApproval(Request $request)
    {
        DB::table('leaves')->where('id', $request->leave_id)
                        ->where('user_id', $request->user_id)
                        ->update([
                            'management_approval' => $request->approval_status,
                            'management_note' => $request->management_note,
                        ]);
    
        $user = User::find($request->user_id);
        $manager_name = auth()->user()->name;
        $manager_id = auth()->user()->id;
        $status_message = $request->approval_status === 'Approved' 
                          ? "approved" 
                          : "rejected";
        $message = "Your leave request has been $status_message by $manager_name.";

        // Mark the original notification as read
        Notification::where('leave_id', $request->leave_id)
                    ->where('emp_id', $request->user_id)
                    ->update(['read' => 1]);
    
        // Save the notification to the database
        Notification::create([
            'user_id' => $user->id,
            'message' => $message,
            'leave_id' => $request->leave_id,
            'emp_id' => $request->user_id,

        ]);
    
        $data = [
            'user_id' => $user->id,
            'message' => $message,
            'leave_id' => $request->leave_id,
            'emp_id' => $request->user_id,

        ];

        // Create a specific message for HR
        $hrMessage = "$manager_name has $status_message the leave request of $user->name.";        

        // Get all HR users (assuming they have a role 'HR')
        $hrUsers = User::where('usertype', 'hr')->get();

        foreach ($hrUsers as $hrUser) {
            Notification::create([
                'user_id' => $hrUser->id,
                'message' => $hrMessage,
                'leave_id' => $request->leave_id,
                'emp_id' => $request->user_id,
    
            ]);
        }
    
        // Emit notification event
        Http::post('http://192.168.10.3:3001/notify', $data);
    
        return redirect()->to('/view-leaves-mgt')->with('message', 'Leave status successfully updated!');
    }
    

    public function viewMyLeaves(Request $request) {

        $leaves = Leave::join('users', 'users.id', '=', 'leaves.user_id')
                            ->select(
                                'users.name',
                                'leaves.id',
                                'leaves.user_id',
                                'leaves.leave_type',
                                'leaves.start_date',
                                'leaves.end_date',
                                'leaves.reason',
                                'leaves.additional_notes',
                                'leaves.supervisor_approval',
                                'leaves.supervisor_note',
                                'leaves.management_approval',
                                'leaves.management_note',
                            )
                            ->where('leaves.user_id', auth()->user()->id)
                            ->get();


        $manageLeaveView = View::make('components.view-emp-leaves', ['leave' => $leaves])->render(); // Render the manage-leave view
        return view('my-leaves', ['manageLeaveView' => $manageLeaveView]);
    }

    public function viewMySupLeaves(Request $request) {

        $leaves = Leave::join('users', 'users.id', '=', 'leaves.user_id')
                            ->select(
                                'users.name',
                                'leaves.id',
                                'leaves.user_id',
                                'leaves.leave_type',
                                'leaves.start_date',
                                'leaves.end_date',
                                'leaves.reason',
                                'leaves.additional_notes',
                                'leaves.supervisor_approval',
                                'leaves.supervisor_note',
                                'leaves.management_approval',
                                'leaves.management_note',
                            )
                            ->where('leaves.user_id', auth()->user()->id)
                            ->get();


        $manageLeaveView = View::make('components.view-emp-sup-leaves', ['leave' => $leaves])->render(); // Render the manage-leave view
        return view('supervisor.my-leaves', ['manageLeaveView' => $manageLeaveView]);
    }

    public function viewUsers() {
        $users = User::all();
        $manageUsers = View::make('components.admin-manage-users', ['users' => $users])->render(); // Render the manage-leave view
        return view('admin.manage-users', ['manageUsers' => $manageUsers]);
    }

    public function editUser($id){
        $user = DB::table('users')->where('id',$id)->first();
        $editUsers = view('components.edit-user', compact('user'))->render(); // Render the edit-leave view
        return view('admin.admin-edit-user', ['editUsers' => $editUsers]);
    }

    public function updateUser(Request $request){

        $request->validate([
            'usertype' => 'required',
            'category' =>  'required',
        ]);

        DB::table('users')->where('id', $request->id)->update([
            'usertype'=>$request->usertype,
            'category'=>$request->category,
        ]);
        return redirect()->to('/view-users')->with('message', 'User role successfully updated!');
    }
    

    /////////////////////////////////////////////////Leave Calculator///////////////////////////////////////////////////////////

    public function getRemainingLeaves(Request $request)
    {
        $userId = auth()->user()->id;
        $userCategory = trim(strtolower(auth()->user()->category));

        // Special handling for interns and probation employees
        if ($userCategory === 'internship' || $userCategory === 'probation') {
            return $this->getInternshipRemainingLeave($request);
        }

        // Fetch all leave types allowed for the user's category
        $leaveTypes = LeaveType::where('category', $userCategory)->get()->keyBy('leave_type');
        $yearStart = date('Y-01-01');
        $yearEnd = date('Y-12-31');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        // Initialize remaining leaves data structure
        $remainingLeaves = [];
        foreach ($leaveTypes as $type => $info) {
            // Handle missing 'count_per_month' by assigning a default value (e.g., null or 0)
            $remainingLeaves[$type] = [
                'Leave Type' => $type,
                'Total Allocated' => $info->count,
                'Allocated per month' => $info->count_per_month ?? 0, // Set default value if missing
                'Leaves Taken' => 0,
                'Remaining Leaves' => $info->count,
            ];
        }

        // Fetch and calculate leaves taken and approved for the current year
        $leavesTaken = Leave::select(
            'leave_type', 
            DB::raw('SUM(DATEDIFF(end_date, start_date) + 1) as days_taken'),
            DB::raw('YEAR(start_date) as year'), 
            DB::raw('MONTH(start_date) as month')
        )
        ->where('user_id', $userId)
        ->whereBetween('start_date', [$yearStart, $yearEnd])
        ->whereBetween('end_date', [$yearStart, $yearEnd])
        ->where('management_approval', 'Approved')
        ->where('supervisor_approval', 'Approved')
        ->groupBy('year', 'month', 'leave_type')
        ->get();

        // Calculate the total half-day leaves taken and their impact on casual leaves
        $totalHalfDaysTaken = Leave::where('user_id', $userId)
            ->whereBetween('start_date', [$yearStart, $yearEnd])
            ->whereBetween('end_date', [$yearStart, $yearEnd])
            ->where('leave_type', 'Half Day')
            ->where('management_approval', 'Approved')
            ->where('supervisor_approval', 'Approved')
            ->sum(DB::raw('DATEDIFF(end_date, start_date) + 1'));

        // Each half day is considered as 0.5 days of casual leave
        $halfDayDeductions = $totalHalfDaysTaken * 0.5;

        // Calculate the total short leaves taken for the current month
        $shortLeavesTaken = Leave::where('user_id', $userId)
            ->whereBetween('start_date', [$monthStart, $monthEnd])
            ->whereBetween('end_date', [$monthStart, $monthEnd])
            ->where('leave_type', 'Short Leave')
            ->where('management_approval', 'Approved')
            ->where('supervisor_approval', 'Approved')
            ->count();

        foreach ($leavesTaken as $leave) {
            $type = $leave->leave_type;
            $daysTaken = $leave->days_taken;

            if (isset($remainingLeaves[$type])) {
                $remainingLeaves[$type]['Leaves Taken'] += $daysTaken;
                if ($type !== 'Half Day') {  // Exclude Half Day from automatic reduction
                    $remainingLeaves[$type]['Remaining Leaves'] = max(0, $remainingLeaves[$type]['Remaining Leaves'] - $daysTaken);
                }
            }
        }

        // Apply half-day deductions specifically to Casual Leave
        if (isset($remainingLeaves['Casual Leave'])) {
            $remainingLeaves['Casual Leave']['Leaves Taken'] += $halfDayDeductions;
            $remainingLeaves['Casual Leave']['Remaining Leaves'] = max(0, $remainingLeaves['Casual Leave']['Remaining Leaves'] - $halfDayDeductions);
        }

        // Handle short leaves: Deduct the number of taken short leaves from the monthly quota
        if (isset($remainingLeaves['Short Leave'])) {
            $remainingLeaves['Short Leave']['Leaves Taken'] = $shortLeavesTaken;
            $remainingLeaves['Short Leave']['Remaining Leaves'] = max(0, $remainingLeaves['Short Leave']['Allocated per month'] - $shortLeavesTaken);
        }

        return $remainingLeaves;
    }

    public function showRemainingLeaves()
    {
        $userCategory = trim(strtolower(auth()->user()->category));
        $remainingLeaves = [];
        
        if ($userCategory === 'internship') {
            $remainingLeaves = $this->getInternshipRemainingLeave(request());
        } else {
            $remainingLeaves = $this->getRemainingLeaves(request());
        }

        // Fetch all leaves to be displayed on the calendar
        $leaves = Leave::with('user')->get();  // Ensure this line is included to fetch leave data

        return view('hr.home', [
            'remainingLeaves' => $remainingLeaves,
            'userCategory' => $userCategory,
            'leaves' => $leaves // Pass leaves data for calendar
        ]);
    }

    public function getInternshipRemainingLeave(Request $request)
    {
        $userId = auth()->user()->id;

        // Define the current month's start and end dates
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        // Fetch all approved half-day leaves for the user within the specified date range
        $halfDayLeaves = Leave::where('user_id', $userId)
            ->where('leave_type', 'Half Day')
            ->whereBetween('start_date', [$monthStart, $monthEnd])
            ->where('management_approval', 'Approved')
            ->where('supervisor_approval', 'Approved')
            ->get();

        // Initialize a counter for the number of half days
        $totalHalfDays = 0;

        // Iterate through each leave record
        foreach ($halfDayLeaves as $leave) {
            // Convert start_date and end_date to Carbon instances
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);

            // Calculate the number of half days for this leave period
            // If start_date and end_date are the same, count it as 1 half day
            if ($startDate->equalTo($endDate)) {
                $totalHalfDays += 1;
            } else {
                // Otherwise, calculate the number of days in the period and count each as a half day
                $daysCount = $startDate->diffInDays($endDate);
                $totalHalfDays += $daysCount;
            }
        }

        // Interns are allowed 1 half-day leave per month
        $HALFDAYLIMIT = 1;
        $halfDayLimit = $HALFDAYLIMIT;

        if ($totalHalfDays > $halfDayLimit) {
            // If the limit is exceeded, return "No Pay" for the month
            return [
                'Leave Type' => 'Half Day',
                'Leaves Taken' => $totalHalfDays,
                'Remaining Leaves' => 0,
                'Status' => 'No Pay'
            ];
        } else {
            // Otherwise, calculate remaining leaves
            return [
                'Leave Type' => 'Half Day',
                'Leaves Taken' => $totalHalfDays,
                'Remaining Leaves' => $halfDayLimit - $totalHalfDays,
                'Status' => 'Normal'
            ];
        }
    }

    public function showLeaveEntitlement()
    {
        $leaveEntitlement = $this->calculateLeaveEntitlement();

        $remainingLeavesView = View::make('components.user-dashboard', ['leaveEntitlement' => $leaveEntitlement])->render();

        return view('admin.dashboard', ['remainingLeavesView' => $remainingLeavesView]);
    }

    private function calculateLeaveEntitlement()
    {
        $userCategory = auth()->user()->category;
        $leaveTypes = LeaveType::where('category', $userCategory)->get();
        $leaveEntitlement = [];
        $casualLeaveIndex = null;
        $casualLeaveTotal = 7;
        $casualLeavesTaken = 0;
        $halfDaysTaken = 0;
        $shortLeavesTaken = 0;

        // Initialize entitlements and find the index and total of casual and short leaves
        foreach ($leaveTypes as $index => $leaveType) {
            $leavesTaken = $this->getLeavesTaken($leaveType->leave_type);
            $leaveEntitlement[$index] = [
                'leave_type' => $leaveType->leave_type,
                'total_allocated' => $leaveType->count,
                'allocated_per_month' => $leaveType->count_per_month,
                'leaves_taken' => $leavesTaken,
                'remaining_leaves' => $leaveType->count - $leavesTaken
            ];

            if ($leaveType->leave_type === 'Casual Leave') {
                $casualLeaveIndex = $index;
                $casualLeaveTotal = $leaveType->count;
                $casualLeavesTaken += $leavesTaken;
            }

            if ($leaveType->leave_type === 'Short Leave') {
                $shortLeavesTaken += $leavesTaken;
            }
        }

        // Apply deductions for Half Day
        foreach ($leaveTypes as $index => $leaveType) {
            if ($leaveType->leave_type === 'Half Day' &&
                ($userCategory === 'Permanent' || $userCategory === 'Probation')) {
                $halfDaysTaken = $leaveEntitlement[$index]['leaves_taken'];
                // Convert half days taken to equivalent casual leave days and add to the casual leaves taken
                $casualLeavesTaken += $halfDaysTaken * 0.5;
            }
        }

        // Update casual leaves remaining
        if ($casualLeaveIndex !== null) {
            $leaveEntitlement[$casualLeaveIndex]['remaining_leaves'] = $casualLeaveTotal - $casualLeavesTaken;
        }

        // For 'Internship' category, consider only one half day per month
        if ($userCategory === 'Internship') {
            $leaveEntitlement[] = [
                'leave_type' => 'Half Day',
                'total_allocated' => 1, // Only one half day per month for Internship
                'allocated_per_month' => null, // No monthly allocation for Internship
                'leaves_taken' => $this->getLeavesTaken('Half Day'),
                'remaining_leaves' => null, // Remaining leaves are not applicable for Internship
            ];
        }

        // Handle Short Leave Quota reset for every month
        if ($userCategory !== 'Internship') {
            $leaveEntitlement[] = [
                'leave_type' => 'Short Leave',
                'total_allocated' => $leaveTypes->where('leave_type', 'Short Leave')->first()->count_per_month,
                'allocated_per_month' => $leaveTypes->where('leave_type', 'Short Leave')->first()->count_per_month,
                'leaves_taken' => $shortLeavesTaken,
                'remaining_leaves' => $leaveTypes->where('leave_type', 'Short Leave')->first()->count_per_month - $shortLeavesTaken,
            ];
        }

        return $leaveEntitlement;
    }

    private function getLeavesTaken($leaveType)
    {
        // Fetch leaves taken by the user for the specified leave type
        return Leave::where('user_id', auth()->id())
                    ->where('leave_type', $leaveType)
                    ->count();
    }

    /////////////////////////////////////////////////End Leave Calculator////////////////////////////////////////////////////


    










    private function fetchUsers() {
        return User::where('usertype', '<>', 'management')
                    ->where('name', '<>', auth()->user()->name)
                    ->get();  // Fetch the list of users
    }


    public function getuser() {
        $users = $this->fetchUsers()->where('department', auth()->user()->department);
        $viewUsers = View::make('components.request-leave', ['users' => $users])->render(); // Render
        return view('emp-leave', ['viewUsers' => $viewUsers, 'users' => $users]);
    }


    public function getSupuser() {
        $users = $this->fetchUsers()->where('department', auth()->user()->department);
        $viewSupUsers = View::make('components.sup-request-leave', ['users' => $users])->render(); // Render
        return view('supervisor.sup-leave', ['viewSupUsers' => $viewSupUsers, 'users' => $users]);
    }

    public function getMgtUser() {
        $users = $this->fetchUsers()->where('department', auth()->user()->department);
        $viewMgtUsers = View::make('components.mgt-request-leave', ['users' => $users])->render(); // Render
        return view('management.mgt-leave', ['viewMgtUsers' => $viewMgtUsers, 'users' => $users]);
    }

    public function getHruser() {
        $users = $this->fetchUsers()->where('department', auth()->user()->department);
        $viewHrUsers = View::make('components.hr-request-leave', ['users' => $users])->render(); // Render
        return view('hr.hr-leave', ['viewHrUsers' => $viewHrUsers, 'users' => $users]);
    }
    
    public function storeSupLeave(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'other_leave_type' => 'nullable|required_if:leave_type,Other', // Add validation for other_leave_type
            'start_date' => 'required',
            'end_date' => 'required',
            'reason' => 'required',
            'covering_person' => 'required'
        ]);
    
        $leave = new Leave;
    
        $leave->user_id = auth()->user()->id;
    
        // Check if 'Other' was selected and use 'other_leave_type' if so
        if ($request->leave_type === 'Other') {
            $leave->leave_type = $request->other_leave_type;
        } else {
            $leave->leave_type = $request->leave_type;
        }
    
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->reason = $request->reason;
        $leave->additional_notes = $request->additional_notes;
        $leave->covering_person = $request->covering_person;
        $leave->supervisor_approval = "Approved";
        $leave->management_approval = "Pending";
    
        $leave->save();

        // Get the leave_id of the newly created leave request
        $leave_id = $leave->id;

        $management_users = User::where('usertype', 'management')->get();
        $user = User::find($leave->user_id);
        // dd($leave->user_id,);
        foreach ($management_users as $manager) {
            
            $management_message = "New leave request from $user->name requires your approval.";
            Notification::create([
                'user_id' => $manager->id,
                'message' => $management_message,
                'leave_id' => $leave_id, // Use the correct leave_id here
                'emp_id' => $leave->user_id, // Use the correct user_id here
            ]);

            $data = [
                'user_id' => $manager->id,
                'message' => $management_message,
                'leave_id' => $leave_id, // Use the correct leave_id here
                'emp_id' => $leave->user_id, // Use the correct user_id here
            ];
        }
    
        return back()->with('msg', 'Your leave request has been successfully processed.');
    }

    public function viewSupLeaves(Request $request) {
        // $leaves = Leave::where('user_id', auth()->user()->id)->get();  Fetch leaves for the authenticated user

        $leaves = Leave::join('users', 'users.id', '=', 'leaves.user_id')
                    ->join('users as covering_users', 'covering_users.id', '=', 'leaves.covering_person')
                    ->select(
                        'users.id',
                        'covering_users.name',
                        'leaves.id',
                        'leaves.user_id',
                        'leaves.leave_type',
                        'leaves.start_date',
                        'leaves.end_date',
                        'leaves.reason',
                        'leaves.additional_notes',
                        'leaves.additional_notes'
                    )
                    ->where('leaves.user_id', auth()->user()->id)
                    ->where('leaves.management_approval', "Pending")
                    ->get();


        $manageLeaveView = View::make('components.manage-leave', ['leave' => $leaves])->render(); // Render the manage-leave view
        return view('supervisor.manage-my-leave', ['manageLeaveView' => $manageLeaveView]);
    }

    public function editSupLeave($id) {
        $data = DB::table('leaves')->where('id', $id)->first();
        $users = $this->fetchUsers();  // Fetch users using the refactored method
    
        // Pass 'data' and 'users' directly to 'supervisor.edit-my-leave' without rendering to string
        return view('supervisor.edit-my-leave', compact('data', 'users'));
    }


    public function storeHrLeave(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'other_leave_type' => 'nullable|required_if:leave_type,Other', // Add validation for other_leave_type
            'start_date' => 'required',
            'end_date' => 'required',
            'reason' => 'required',
        ]);
    
        $leave = new Leave;
    
        $leave->user_id = auth()->user()->id;
    
        // Check if 'Other' was selected and use 'other_leave_type' if so
        if ($request->leave_type === 'Other') {
            $leave->leave_type = $request->other_leave_type;
        } else {
            $leave->leave_type = $request->leave_type;
        }
    
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->reason = $request->reason;
        $leave->additional_notes = $request->additional_notes;
        $leave->covering_person = $request->covering_person;
        $leave->supervisor_approval = "Approved";
        $leave->management_approval = "Pending";
    
        $leave->save();

        // Get the leave_id of the newly created leave request
        $leave_id = $leave->id;

        $management_users = User::where('usertype', 'management')->get();
        $user = User::find($leave->user_id);
        // dd($leave->user_id,);
        foreach ($management_users as $manager) {
            
            $management_message = "New leave request from $user->name requires your approval.";
            Notification::create([
                'user_id' => $manager->id,
                'message' => $management_message,
                'leave_id' => $leave_id, // Use the correct leave_id here
                'emp_id' => $leave->user_id, // Use the correct user_id here
            ]);

            $data = [
                'user_id' => $manager->id,
                'message' => $management_message,
                'leave_id' => $leave_id, // Use the correct leave_id here
                'emp_id' => $leave->user_id, // Use the correct user_id here
            ];
        }
    
        return back()->with('msg', 'Your leave request has been successfully processed.');
    }


    public function storeMgtLeave(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'other_leave_type' => 'nullable|required_if:leave_type,Other', // Add validation for other_leave_type
            'start_date' => 'required',
            'end_date' => 'required',
            'reason' => 'required',
            'covering_person' => 'required'
        ]);
    
        $leave = new Leave;
    
        $leave->user_id = auth()->user()->id;
    
        // Check if 'Other' was selected and use 'other_leave_type' if so
        if ($request->leave_type === 'Other') {
            $leave->leave_type = $request->other_leave_type;
        } else {
            $leave->leave_type = $request->leave_type;
        }
    
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->reason = $request->reason;
        $leave->additional_notes = $request->additional_notes;
        $leave->covering_person = $request->covering_person;
        $leave->supervisor_approval = "Approved";
        $leave->management_approval = "Approved";
    
        $leave->save();
    
        return back()->with('msg', 'Your leave request has been successfully processed.');
    }
    

    public function viewMgtLeaves(Request $request) {
        // $leaves = Leave::where('user_id', auth()->user()->id)->get();  Fetch leaves for the authenticated user

        $leaves = Leave::join('users', 'users.id', '=', 'leaves.user_id')
                    ->join('users as covering_users', 'covering_users.id', '=', 'leaves.covering_person')
                    ->select(
                        'users.id',
                        'covering_users.name',
                        'leaves.id',
                        'leaves.user_id',
                        'leaves.leave_type',
                        'leaves.start_date',
                        'leaves.end_date',
                        'leaves.reason',
                        'leaves.additional_notes',
                        'leaves.additional_notes'
                    )
                    ->where('leaves.user_id', auth()->user()->id)
                    ->get();


        $manageLeaveView = View::make('components.manage-leave', ['leave' => $leaves])->render(); // Render the manage-leave view
        return view('management.manage-my-leave', ['manageLeaveView' => $manageLeaveView]);
    }
    
    public function editMgtLeave($id) {
        $data = DB::table('leaves')->where('id', $id)->first();
        $users = $this->fetchUsers();  // Fetch users using the refactored method
    
        // Pass 'data' and 'users' directly to 'management.edit-my-leave' without rendering to string
        return view('management.edit-my-leave', compact('data', 'users'));
    }


    public function addLeave(Request $request)
    {
        $request->validate([
            'leave_type' => 'required',
            'category' => 'required',
            'count' => 'nullable|numeric',
            'count_per_month' => 'nullable|numeric',
        ]);
    
        $leave = new LeaveType;
    
        $leave->leave_type = $request->leave_type;
        $leave->category = $request->category;
        $leave->count = $request->count;
        $leave->count_per_month = $request->count_per_month;
    
        $leave->save();
    
        return back()->with('msg', 'Leave type has been successfully processed.');

    }



    public function search(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string|exists:users,emp_no',
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable'
        ]);
    
        $employee = User::where('emp_no', $request->employee_id)->firstOrFail();
        $query = Leave::where('user_id', $employee->id);
    
        if ($request->start_date) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
    
        if ($request->end_date) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }
    
        $leaves = $query->get();
    
        return response()->json([
            'leaves' => $leaves,
            'employee_name' => $employee->name,
            'profile_photo_path' => $employee->profile_photo_path
        ]);
    }

    // Update leave record by ID
    public function update(Request $request, $id)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $leave = Leave::findOrFail($id);
        $leave->leave_type = $request->leave_type;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->save();

        return response()->json(['success' => true]);
    }

    // Delete leave record by ID
    public function destroy($id)
    {
        Leave::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }



    public function storeManualLeave(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'emp_id' => 'required|exists:users,emp_no',  // Ensure the employer ID exists in the users table
            'leave_type' => 'required',                 // Leave type is required
            'start_date' => 'required|date',            // Start date must be a valid date
            'end_date' => 'required|date|after_or_equal:start_date',  // End date must be on or after the start date
        ]);
    
        // Find the user by Employer ID (emp_no)
        $user = User::where('emp_no', $request->emp_id)->firstOrFail();
    
        // Create a new leave record
        $leave = new Leave();
        $leave->user_id = $user->id;
        $leave->leave_type = $request->leave_type;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        // Set default reason if none provided or use the provided reason
        $leave->reason = $request->reason ?? 'Manual Entry';
        $leave->additional_notes = $request->additional_notes ?? null; // Optional field
        $leave->covering_person = 'manualentry';     // Set covering person as 'manualentry'
        $leave->supervisor_approval = 'Approved';    // Automatically set supervisor approval as 'Approved'
        $leave->management_approval = 'Approved';    // Automatically set management approval as 'Approved'
    
        // Save the leave
        $leave->save();
    
        // Redirect back with a success message
        return redirect()->back()->with('msg', 'Leave request submitted successfully.');
    }
    

    public function getLeaveDataForCalendar()
    {
        // Fetch all leave records with the associated user
        return Leave::with('user')->get();
    }

    public function getLeaveSupDataForCalendar()
    {
        // Fetch all leave records where the associated user's department matches the current user's department
        return Leave::with('user')
                    ->whereHas('user', function($query) {
                        $query->where('department', Auth::user()->department);
                    })
                    ->get();
    }


    



    
}



