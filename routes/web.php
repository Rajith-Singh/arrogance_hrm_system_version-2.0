<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChatController;

use Illuminate\Support\Facades\Mail;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    // 'verified',
])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');});


Route::get('/test-db-connection', function () {
    $serverName = ".";
    $connectionInfo = array("Database" => "arrogance_db", "UID" => "sa", "PWD" => "error404@PHP");
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if ($conn) {
        return "Connection established.<br />";
    } else {
        return "Connection could not be established.<br />" . print_r(sqlsrv_errors(), true);
    }
});



Route::get('/check-late-count/{employeeId}', [AttendanceController::class, 'checkCount']);

Route::get('/track-attendance', function () {
    return view('view-attendance');
});

Route::get('/attendance-tracking', [AttendanceController::class, 'checkAttendance']);



route::get('/home', [HomeController::class, 'index']);

// User (Employer) Routes

// Route::get('/request-leave', function () {
//     return view('emp-leave');
// });

Route::post('/saveLeave',[LeaveController::class,'storeLeave']);

Route::get('/manage-leave',[LeaveController::class,'viewLeaves']);

Route::get('/editLeave/{id}', [LeaveController::class, 'editLeave']);

Route::post('/updateLeave', [LeaveController::class, 'updateLeave']);

Route::delete('/deleteLeave/{id}', [LeaveController::class, 'deleteLeave']);

Route::get('/view-my-leaves',[LeaveController::class,'viewMyLeaves']);

Route::get('/get-remaining-leaves',[LeaveController::class,'getRemainingLeaves']);

Route::get('/request-leave', [LeaveController::class, 'getuser']);

// Define the routes for notifications
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadNotificationCount']);
Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');



Route::post('/leaves/{leaveId}/delete-with-reason', [LeaveController::class, 'deleteWithReason'])->name('leaves.deleteWithReason');

Route::get('/chat/{receiverId?}', [ChatController::class, 'index'])->name('chat');
Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('send.message');
Route::get('/chat-history/{receiverId}', [ChatController::class, 'getChatHistory']);















// // Supervisor Routes

// Route::get('/view-leaves',[LeaveController::class,'viewEmpLeave']);

// Route::get('/view-emp-leave/{user_id}/{leave_id}', [LeaveController::class, 'viewEmpLeaveRequest']);

// Route::post('/update-supervisor-approval', [LeaveController::class, 'updateSupervisorApproval']);

// // Management Routes

// Route::get('/view-leaves-mgt',[LeaveController::class,'viewEmpLeaveMgt']);

// Route::get('/view-mgt-leave/{user_id}/{leave_id}', [LeaveController::class, 'viewMgtLeaveRequest']);

// Route::post('/update-management-approval', [LeaveController::class, 'updateManagementApproval']);



// Routes accessible only to admins
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/view-users',[LeaveController::class,'viewUsers']);
    Route::get('/editUser/{id}', [LeaveController::class, 'editUser']);
    Route::post('/updateUser', [LeaveController::class, 'updateUser']);

    
});

// Routes accessible only to supervisors
Route::middleware(['auth', 'role:supervisor'])->group(function () {
    Route::get('/view-leaves',[LeaveController::class,'viewEmpLeave']);

    Route::get('/view-emp-leave/{user_id}/{leave_id}', [LeaveController::class, 'viewEmpLeaveRequest']);

    Route::post('/update-supervisor-approval', [LeaveController::class, 'updateSupervisorApproval']);

    Route::get('/request-supervisor-leave', [LeaveController::class, 'getSupuser']);

    Route::post('/saveSupLeave',[LeaveController::class,'storeSupLeave']);

    Route::get('/manage-supervisor-leave',[LeaveController::class,'viewSupLeaves']);

    Route::get('/editSupLeave/{id}', [LeaveController::class, 'editSupLeave']);

    Route::get('/view-sup-leaves',[LeaveController::class,'viewMySupLeaves']);

    Route::post('/notifications/supervisor/approve/{id}', [NotificationController::class, 'approve'])->name('notifications.supervisor.approve');
    Route::post('/notifications/supervisor/reject/{id}', [NotificationController::class, 'reject'])->name('notifications.supervisor.reject');


});

// Routes accessible only to management
Route::middleware(['auth', 'role:management'])->group(function () {
    Route::get('/view-leaves-mgt',[LeaveController::class,'viewEmpLeaveMgt']);

    Route::get('/view-mgt-leave/{user_id}/{leave_id}', [LeaveController::class, 'viewMgtLeaveRequest']);

    Route::post('/update-management-approval', [LeaveController::class, 'updateManagementApproval']);

    Route::get('/request-management-leave', [LeaveController::class, 'getMgtUser']);

    Route::post('/saveMgtLeave',[LeaveController::class,'storeMgtLeave']);

    Route::get('/manage-management-leave',[LeaveController::class,'viewMgtLeaves']);

    Route::get('/editMgtLeave/{id}', [LeaveController::class, 'editMgtLeave']);

    Route::get('/view-employee-attendance', function () {
        return view('management.view-emp-attendance');
    });

    Route::get('/employee-search', [UserController::class, 'searchEmployees']);

    Route::get('/emp-attendance-tracking-mgt', [AttendanceController::class, 'checkEmpAttendanceMgt']);

    Route::post('/notifications/management/approve/{id}', [NotificationController::class, 'managementApprove'])->name('notifications.management.approve');
    Route::post('/notifications/management/reject/{id}', [NotificationController::class, 'managementReject'])->name('notifications.management.reject');

    Route::get('/view-daily-attendance', function () {
        return view('management.mgt-daily-attendance-view');
    });
    
    Route::get('/emp-daily-attendance-tracking', [AttendanceController::class, 'getAttendanceRecords']);

    Route::get('/employee-search', [AttendanceController::class, 'searchEmployees']);




});



// Routes accessible only to hr
Route::middleware(['auth', 'role:hr'])->group(function () {

    Route::get('/add-leave-type', function () {
        return view('hr.add-leave');
    });

    Route::post('/addLeave',[LeaveController::class,'addLeave']);

    Route::get('/add-attendance', function () {
        return view('hr.add-attendance');
    });

    Route::get('/add-attendance-alternative', function () {
        return view('hr.add-attendance-alternative');
    });

    Route::post('/upload-attendance', [AttendanceController::class, 'uploadAttendance']);

    // Route::get('/track-attendance', function () {
    //     return view('view-attendance');
    // });

    // Route::get('/attendance-tracking', [AttendanceController::class, 'checkAttendance'])->name('attendance.tracking');

    Route::get('/view-emp-attendance', function () {
        return view('hr.view-emp-attendance');
    });

    Route::get('/emp-attendance-tracking', [AttendanceController::class, 'checkEmpAttendance']);

    Route::post('/update-checkout/{id}', [AttendanceController::class, 'updateCheckOut']);

    Route::get('/reports', function () {
        return view('hr.reports');
    });

    Route::get('/add-manual-attendance', function () {
        return view('hr.add-manual-attendance');
    });

    Route::get('/add-manual-leave', function () {
        return view('hr.add-manual-leave');
    });

    Route::post('/addManualLeave',[LeaveController::class,'storeManualLeave']);

    Route::get('/edit-delete-leave', function () {
        return view('hr.edit-delete-leave-manual');
    });

    Route::post('/saveManualAttendance',[AttendanceController::class,'storeManualAttendance']);

    Route::post('/attendance-report', [AttendanceController::class, 'attendanceReport']);
    Route::post('/attendance-summary-report', [AttendanceController::class, 'attendanceSummaryReport']);
    Route::post('/leave-report', [AttendanceController::class, 'leaveReport']);
    Route::post('/leave-summary-report', [AttendanceController::class, 'leaveSummaryReport']);


    Route::post('/leaves/search', [LeaveController::class, 'search'])->name('leaves.search');
    Route::post('/leaves/{id}/update', [LeaveController::class, 'update'])->name('leaves.update');
    Route::delete('/leaves/{id}', [LeaveController::class, 'destroy'])->name('leaves.destroy');

    Route::get('/add-holiday', [AttendanceController::class, 'create'])->name('example.create');
    Route::post('/add-holiday', [AttendanceController::class, 'store'])->name('example.store');


    Route::get('/request-hr-leave', [LeaveController::class, 'getHruser']);

    Route::post('/saveHrLeave',[LeaveController::class,'storeHrLeave']);



});











