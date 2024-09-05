<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    
        $leave = Leave::find($notification->leave_id);
        if (!$leave) {
            Log::error("Leave request not found, ID: {$notification->leave_id}");
            return response()->json(['status' => 'error', 'message' => 'Leave request not found.']);
        }
    
        $leave->supervisor_approval = "Approved";
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
        $managementUsers = User::where('usertype', 'management')->get();
        if ($managementUsers->isEmpty()) {
            Log::warning("No management users found");
        }
    
        foreach ($managementUsers as $management) {
            $message = "Leave request from {$user->name} has been approved by the supervisor and requires your approval.";
            Notification::create([
                'user_id' => $management->id,
                'message' => $message,
                'leave_id' => $leave->id,
                'emp_id' => $user->id,
            ]);
            Log::info("Notification sent to management, Management ID: {$management->id}");
        }
    
        return response()->json(['status' => 'success', 'message' => 'Leave request approved and notifications sent to management.']);
    }
    
    public function reject($id, Request $request)
    {
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
    
        $leave->supervisor_approval = "Rejected";
        $leave->save();
        Log::info("Leave request rejected, ID: {$leave->id}");
    
        // Mark the notification as read
        $notification->read = 1;
        $notification->save();
        Log::info("Notification marked as read, ID: $id");
    
        return response()->json(['status' => 'success', 'message' => 'Leave request rejected.']);
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



