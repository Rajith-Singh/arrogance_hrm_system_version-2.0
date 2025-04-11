<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RemoteAttendanceController;
use App\Http\Controllers\EmployeeDocumentController;
use App\Http\Controllers\UserController;




// Public route, accessible without login
Route::get('/', function () {
    return view('welcome');
});

// Routes that require authentication, using 'auth' middleware
Route::middleware(['auth'])->group(function () {
    // Home and dashboard routes
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // User-specific routes
    Route::post('/support/send', [SupportController::class, 'send'])->name('support.send');
    Route::get('/support', function () {
        return view('support-desk');
    });

    // Leave management
    Route::post('/saveLeave', [LeaveController::class, 'storeLeave']);
    Route::get('/manage-leave', [LeaveController::class, 'viewLeaves']);
    Route::get('/editLeave/{id}', [LeaveController::class, 'editLeave']);
    Route::post('/updateLeave', [LeaveController::class, 'updateLeave']);
    Route::delete('/deleteLeave/{id}', [LeaveController::class, 'deleteLeave']);
    Route::get('/view-my-leaves', [LeaveController::class, 'viewMyLeaves']);
    Route::get('/get-remaining-leaves', [LeaveController::class, 'getRemainingLeaves']);
    Route::get('/request-leave', [LeaveController::class, 'getuser']);

    // Notifications routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadNotificationCount']);
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/leaves/{leaveId}/delete-with-reason', [LeaveController::class, 'deleteWithReason'])->name('leaves.deleteWithReason');

    // Chat routes
    Route::get('/chat/{receiverId?}', [ChatController::class, 'index'])->name('chat');
    Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('send.message');
    Route::get('/chat-history/{receiverId}', [ChatController::class, 'getChatHistory']);

    // Attendance routes
    Route::get('/check-late-count/{employeeId}', [AttendanceController::class, 'checkCount']);
    Route::get('/attendance-tracking', [AttendanceController::class, 'checkAttendance']);
    Route::get('/track-attendance', function () {
        return view('view-attendance');
    });
    Route::post('/submit-reason', [AttendanceController::class, 'submitReason'])->name('submit-reason');
    Route::get('/api/attendance', [AttendanceController::class, 'getAttendanceData'])->middleware('auth');
    Route::get('/api/leave-data', [LeaveController::class, 'getLeaveData'])->middleware('auth');

    ################

    Route::get('/api/employees/total', [EmployeeController::class, 'getTotalEmployees']);
    Route::get('/api/leaves/pending-approvals', [EmployeeController::class, 'getPendingApprovals']);

    Route::get('/api/holidays', [LeaveController::class, 'getHoliday']);
    
    ##Document Management
    Route::get('/add-documents', [EmployeeDocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [EmployeeDocumentController::class, 'store'])->name('documents.store');
    Route::get('/documents/{document}/download', [EmployeeDocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [EmployeeDocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/api/register/today', [EmployeeController::class, 'getTodayRegister']);



});

// Admin-only routes with 'role:admin' middleware
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/view-users', [LeaveController::class, 'viewUsers']);
    Route::get('/editUser/{id}', [LeaveController::class, 'editUser']);
    Route::post('/updateUser', [LeaveController::class, 'updateUser']);
});

// Supervisor-only routes with 'role:supervisor' middleware
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

    Route::get('/attendance-tracking-sup', [AttendanceController::class, 'checkAttendanceSup']);

    Route::get('/track-attendance-Sup', function () {
        return view('supervisor.sup-view-attendance');
    });
    Route::post('/submit-reason-sup', [AttendanceController::class, 'submitReasonSup'])->name('submit-reason-sup');

    
    Route::post('/support/send', [SupportController::class, 'send'])->name('support.send');

    Route::get('/supportSup', function () {
        return view('supervisor.support-desk');
    });
});


// Supervisor-in-chief-only routes with 'role:supervisor-in-chief' middleware
Route::middleware(['auth', 'role:supervisor-in-chief'])->group(function () {
    Route::get('/view-leaves-sic',[LeaveController::class,'viewEmpLeaveSic']);

    Route::get('/view-emp-leave-sic/{user_id}/{leave_id}', [LeaveController::class, 'viewSicLeaveRequest']);

    Route::post('/update-supervisor-approval-sic', [LeaveController::class, 'updateSICApproval']);

    Route::get('/request-supervisor-ic-leave', [LeaveController::class, 'getSicuser']);

    Route::post('/saveSicLeave',[LeaveController::class,'storeSicLeave']);

    Route::get('/manage-supervisor-ic-leave',[LeaveController::class,'viewSicLeaves']);

    Route::get('/editSicLeave/{id}', [LeaveController::class, 'editSicLeave']); ////

    Route::get('/view-sic-leaves',[LeaveController::class,'viewMySicLeaves']);

    Route::post('/notifications/supervisor-in-chief/approve/{id}', [NotificationController::class, 'approveSIC'])->name('notifications.supervisor-in-chief.approve');
    Route::post('/notifications/supervisor-in-chief/reject/{id}', [NotificationController::class, 'RejectSIC'])->name('notifications.supervisor-in-chief.reject');

    Route::get('/attendance-tracking-sic', [AttendanceController::class, 'checkAttendanceSic']);

    Route::get('/track-attendance-Sic', function () {
        return view('supervisor-in-chief.sic-view-attendance');
    });
    Route::post('/submit-reason-sic', [AttendanceController::class, 'submitReasonSic'])->name('submit-reason-sic');

    
    Route::post('/support/send', [SupportController::class, 'send'])->name('support.send');

    Route::get('/supportSic', function () {
        return view('supervisor.support-desk');
    });
});

// Management-only routes with 'role:management' middleware
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

    // Route::post('/support/send', [SupportController::class, 'send'])->name('support.send');

    // Route::get('/supportMgt', function () {
    //     return view('management.support-desk');
    // });

    Route::get('/attendance-tracking-mgt', [AttendanceController::class, 'checkAttendanceMgt']);

    Route::get('/track-attendance-Mgt', function () {
        return view('management.mgt-view-attendance');
    });

    Route::post('/submit-reason-mgt', [AttendanceController::class, 'submitReasonMgt'])->name('submit-reason-mgt');

    Route::get('/api/register/today', [EmployeeController::class, 'getTodayRegister']);

    Route::get('/daily-register', [EmployeeController::class, 'showRegister'])->name('showRegister');
    // Fetch today's register
    Route::get('/api/register/today', [EmployeeController::class, 'getTodayRegister']);

    ##################
    Route::get('/daily-register', [EmployeeController::class, 'showRegister'])->name('showRegister');
    // Fetch today's register
    Route::get('/api/register/today', [EmployeeController::class, 'getTodayRegister']);

    // Submit the updated register
    Route::post('/api/register/submit', [EmployeeController::class, 'submitRegister']);
    Route::get('/api/register/counts', [EmployeeController::class, 'getRegisterCounts']);

    ################################

});

// HR-only routes with 'role:hr' middleware
Route::middleware(['auth', 'role:hr'])->group(function () {
    Route::get('/add-leave-type', function () {
        return view('hr.add-leave');
    });
    Route::post('/addLeave', [LeaveController::class, 'addLeave']);
    Route::get('/add-attendance', function () {
        return view('hr.add-attendance');
    });
    Route::get('/add-attendance-alternative', function () {
        return view('hr.add-attendance-alternative');
    });
    Route::get('/add-certificates', [CertificateController::class, 'create'])->name('certificates.create');
    Route::post('/certificates', [CertificateController::class, 'store'])->name('certificates.store');
    
    Route::post('/upload-attendance', [AttendanceController::class, 'uploadAttendance']);
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
    Route::post('/addManualLeave', [LeaveController::class, 'storeManualLeave']);
    Route::get('/edit-delete-leave', function () {
        return view('hr.edit-delete-leave-manual');
    });

    Route::get('/add-holiday', [AttendanceController::class, 'create'])->name('example.create');
    Route::post('/add-holiday', [AttendanceController::class, 'store'])->name('example.store');

    Route::get('/request-hr-leave', [LeaveController::class, 'getHruser']);

    Route::post('/saveHrLeave',[LeaveController::class,'storeHrLeave']);

    Route::post('/saveManualAttendance', [AttendanceController::class, 'storeManualAttendance']);
    Route::post('/attendance-report', [AttendanceController::class, 'attendanceReport']);
    Route::post('/attendance-summary-report', [AttendanceController::class, 'attendanceSummaryReport']);
    Route::post('/leave-report', [AttendanceController::class, 'leaveReport']);
    Route::post('/leave-summary-report', [AttendanceController::class, 'leaveSummaryReport']);
    Route::post('/leaves/search', [LeaveController::class, 'search'])->name('leaves.search');
    Route::post('/leaves/{id}/update', [LeaveController::class, 'update'])->name('leaves.update');
    Route::delete('/leaves/{id}', [LeaveController::class, 'destroy'])->name('leaves.destroy');
    Route::post('/support/send', [SupportController::class, 'send'])->name('support.send');
    Route::get('/supportHR', function () {
        return view('hr.support-desk');
    });

    Route::get('/attendance-tracking-hr', [AttendanceController::class, 'checkAttendanceHR']);

    Route::get('/track-attendance-HR', function () {
        return view('hr.hr-view-attendance');
    });

    Route::post('/submit-reason-hr', [AttendanceController::class, 'submitReasonHR'])->name('submit-reason-hr');

    #############

    Route::get('/daily-register', [EmployeeController::class, 'showRegister'])->name('showRegister');
    // Fetch today's register
    Route::get('/api/register/today', [EmployeeController::class, 'getTodayRegister']);

    // Submit the updated register
    Route::post('/api/register/submit', [EmployeeController::class, 'submitRegister']);
    Route::get('/api/register/counts', [EmployeeController::class, 'getRegisterCounts']);


    ####Remote attendance
    
    Route::get('/monitor-attendance', [RemoteAttendanceController::class, 'index'])->name('attendance.monitor');

    
    //User management
    
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    


    ##########################

});

// Test database connection route (publicly accessible)
Route::get('/test-db-connection', function () {
    $serverName = ".";
    $connectionInfo = ["Database" => "arrogance_db", "UID" => "sa", "PWD" => "error404@PHP"];
    $conn = sqlsrv_connect($serverName, $connectionInfo);

    if ($conn) {
        return "Connection established.<br />";
    } else {
        return "Connection could not be established.<br />" . print_r(sqlsrv_errors(), true);
    }
});
