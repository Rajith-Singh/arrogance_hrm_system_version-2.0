<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveRequestMail;
use App\Mail\ManagementApprovalMail;
use App\Mail\SupervisorInChiefApprovalMail;

class NotificationController extends Controller
{
    // Display the notifications page
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        return view('notifications.index', compact('notifications'));
    }

    // Get the count of unread notifications
    public function getUnreadNotificationCount(Request $request)
    {
        $count = Notification::where('user_id', auth()->id())
                             ->where('read', false)
                             ->count();

        return response()->json(['count' => $count]);
    }

    // Mark all notifications as read
    public function markAllAsRead(Request $request)
    {
        Notification::where('user_id', auth()->id())->where('read', false)->update(['read' => true]);
        return redirect()->route('notifications');
    }

    public function approve($id, Request $request)
    {
        Log::info("Approving leave request, Notification ID: $id");
    
        $notification = Notification::find($id);
        if (!$notification) {
            Log::error("Notification not found, ID: $id");
            return response()->json(['status' => 'error', 'message' => 'Notification not found.']);
        }
    
        // Eager-load the 'user' relationship with the Leave model
        $leave = Leave::with('user')->find($notification->leave_id);
        if (!$leave) {
            Log::error("Leave request not found, ID: {$notification->leave_id}");
            return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
        }
    
        // Approve the leave request
        $leave->supervisor_approval = "Approved";
        $leave->save();
        Log::info("Leave request approved, ID: {$leave->id}");
    
        $notification->read = 1;
        $notification->save();
        Log::info("Notification marked as read, ID: $id");
    
        // Find the department of the user making the leave request
        $userDept = User::where('id', $leave->user_id)->pluck('main_department')->first();
    
        // Find Supervisor-in-Chief in the same department
        $supervisor_in_chief_users = User::where('usertype', 'supervisor-in-chief')
                                        ->where('main_department', $userDept)
                                        ->get();
    
        // Notify the Supervisor-in-Chief
        foreach ($supervisor_in_chief_users as $supervisor_in_chief) {
    
            // Use a safe fallback for the employee's name when building the notification message.
            $employeeName = $leave->user ? $leave->user->name : 'Employee';
            $supervisor_in_chief_message = "Leave request from {$employeeName} has been approved by supervisor and requires your review.";
    
            Notification::create([
                'user_id' => $supervisor_in_chief->id,
                'message' => $supervisor_in_chief_message,
                'leave_id' => $leave->id,
                'emp_id' => $leave->user_id,
            ]);
    
            // Define the supervisor name for the email (hardcoded as "Supervisor" in this case)
            $supervisor_name = "Supervisor";
    
            try {
                Mail::to($supervisor_in_chief->email)
                    ->send(new SupervisorInChiefApprovalMail($leave, $supervisor_name));
                Log::info("Email sent to Supervisor-in-Chief, User ID: {$supervisor_in_chief->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send email to Supervisor-in-Chief, User ID: {$supervisor_in_chief->id}. Error: " . $e->getMessage());
                // Optionally, you might want to continue or handle the error further.
            }
    
            Log::info("Notification sent to Supervisor-in-Chief, User ID: {$supervisor_in_chief->id}");
        }
    
        return response()->json(['status' => 'success', 'message' => 'Leave request approved and notifications sent to Supervisor-in-Chief.']);
    }
    
    
    
    
    public function reject($id, Request $request)
    {
        // Find the notification by ID
        $notification = Notification::find($id);
        if (!$notification) {
            Log::error("Notification not found, ID: $id");
            return response()->json(['status' => 'error', 'message' => 'Notification not found.']);
        }
    
        // Find the leave request associated with the notification
        $leave = Leave::find($notification->leave_id);
        if (!$leave) {
            Log::error("Leave request not found, ID: {$notification->leave_id}");
            return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
        }
    
        // Mark the leave request as rejected
        $leave->supervisor_approval = "Rejected";
        $leave->save();
        Log::info("Leave request rejected, ID: {$leave->id}");
    
        // Find the user who made the leave request
        $user = $leave->user;
        if (!$user) {
            Log::error("User not found for leave request, Leave ID: {$leave->id}");
            return response()->json(['status' => 'error', 'message' => 'User not found for leave request.']);
        }
    
        // Mark the notification as read
        $notification->read = 1;
        $notification->save();
        Log::info("Notification marked as read, ID: $id");
    
        // Notify the user that their leave request has been rejected
        $message = "Your leave request has been rejected by the Supervisor.";
        Notification::create([
            'user_id' => $user->id,
            'message' => $message,
            'leave_id' => $leave->id,
            'emp_id' => $user->id,
        ]);
        Log::info("Notification sent to user, User ID: {$user->id}");
    
        // Return a success response
        return response()->json(['status' => 'success', 'message' => 'Leave request rejected.']);
    }
    

    public function approveSIC($id, Request $request)
    {
        Log::info("Approving leave request, Notification ID: $id");
    
        $notification = Notification::find($id);
        if (!$notification) {
            Log::error("Notification not found, ID: $id");
            return response()->json(['status' => 'error', 'message' => 'Notification not found.']);
        }
    
        $leave = Leave::find($notification->leave_id);
        if (!$leave) {
            Log::error("Leave request not found, ID: {$notification->leave_id}");
            return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
        }
    
        $leave->supervisor_in_chief_approval = "Approved";
        $leave->save();
        Log::info("Leave request approved, ID: {$leave->id}");
    
        // Mark the notification as read
        $notification->read = 1;
        $notification->save();
        Log::info("Notification marked as read, ID: $id");
    
        // Check if the user exists
        $user = $leave->user;
        if (!$user) {
            Log::error("User not found for leave request, Leave ID: {$leave->id}");
            return response()->json(['status' => 'error', 'message' => 'User not found for leave request.']);
        }
    
        // Notify management
        // $managementUsers = User::where('usertype', 'management')->get();
        // if ($managementUsers->isEmpty()) {
        //     Log::warning("No management users found");
        // }
        
        // Notify employee
        $message = "Your leave request has been approved by Supervisor in Chief.";
        Notification::create([
            'user_id' => $user->id,
            'message' => $message,
            'leave_id' => $leave->id,
            'emp_id' => $user->id,
        ]);
        Log::info("Notification sent to Supervisor in Chief, User ID: {$user->id}");


    
        return response()->json(['status' => 'success', 'message' => 'Leave request approved.']);
    }

    public function RejectSIC($id, Request $request)
    {
        Log::info("Supervisor in Chief rejecting leave request, Notification ID: $id");

        $notification = Notification::find($id);
        if (!$notification) {
            Log::error("Notification not found, ID: $id");
            return response()->json(['status' => 'error', 'message' => 'Notification not found.']);
        }

        $leave = Leave::find($notification->leave_id);
        if (!$leave) {
            Log::error("Leave request not found, ID: {$notification->leave_id}");
            return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
        }

        $leave->supervisor_in_chief_approval = "Rejected";
        $leave->save();
        Log::info("Leave request rejected by Supervisor in Chief, ID: {$leave->id}");

        // Mark the notification as read
        $notification->read = 1;
        $notification->save();
        Log::info("Notification marked as read, ID: $id");

        $user = $leave->user;
        if (!$user) {
            Log::error("User not found for leave request, Leave ID: {$leave->id}");
            return response()->json(['status' => 'error', 'message' => 'User not found for leave request.']);
        }

        // Notify employee
        $message = "Your leave request has been rejected by management.";
        Notification::create([
            'user_id' => $user->id,
            'message' => $message,
            'leave_id' => $leave->id,
            'emp_id' => $user->id,
        ]);
        Log::info("Notification sent to employee, User ID: {$user->id}");

        return response()->json(['status' => 'success', 'message' => 'Leave request rejected by management.']);
    }


    public function managementApprove($id, Request $request)
    {
        Log::info("Management approving leave request, Notification ID: $id");

        $notification = Notification::find($id);
        if (!$notification) {
            Log::error("Notification not found, ID: $id");
            return response()->json(['status' => 'error', 'message' => 'Notification not found.']);
        }

        $leave = Leave::find($notification->leave_id);
        if (!$leave) {
            Log::error("Leave request not found, ID: {$notification->leave_id}");
            return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
        }

        $leave->management_approval = "Approved";
        $leave->save();
        Log::info("Leave request approved by management, ID: {$leave->id}");

        // Mark the notification as read
        $notification->read = 1;
        $notification->save();
        Log::info("Notification marked as read, ID: $id");

        $user = $leave->user;
        if (!$user) {
            Log::error("User not found for leave request, Leave ID: {$leave->id}");
            return response()->json(['status' => 'error', 'message' => 'User not found for leave request.']);
        }

        // Notify employee
        $message = "Your leave request has been approved by management.";
        Notification::create([
            'user_id' => $user->id,
            'message' => $message,
            'leave_id' => $leave->id,
            'emp_id' => $user->id,
        ]);
        Log::info("Notification sent to employee, User ID: {$user->id}");

        return response()->json(['status' => 'success', 'message' => 'Leave request approved by management.']);
    }

    public function managementReject($id, Request $request)
    {
        Log::info("Management rejecting leave request, Notification ID: $id");

        $notification = Notification::find($id);
        if (!$notification) {
            Log::error("Notification not found, ID: $id");
            return response()->json(['status' => 'error', 'message' => 'Notification not found.']);
        }

        $leave = Leave::find($notification->leave_id);
        if (!$leave) {
            Log::error("Leave request not found, ID: {$notification->leave_id}");
            return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
        }

        $leave->management_approval = "Rejected";
        $leave->save();
        Log::info("Leave request rejected by management, ID: {$leave->id}");

        // Mark the notification as read
        $notification->read = 1;
        $notification->save();
        Log::info("Notification marked as read, ID: $id");

        $user = $leave->user;
        if (!$user) {
            Log::error("User not found for leave request, Leave ID: {$leave->id}");
            return response()->json(['status' => 'error', 'message' => 'User not found for leave request.']);
        }

        // Notify employee
        $message = "Your leave request has been rejected by management.";
        Notification::create([
            'user_id' => $user->id,
            'message' => $message,
            'leave_id' => $leave->id,
            'emp_id' => $user->id,
        ]);
        Log::info("Notification sent to employee, User ID: {$user->id}");

        return response()->json(['status' => 'success', 'message' => 'Leave request rejected by management.']);
    }
}



