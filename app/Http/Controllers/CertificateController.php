<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Notification; // Ensure this is included
use App\Models\Leave;
use App\Models\User;
use App\Models\Certificate;
use App\Models\LeaveType;
use App\Models\LeaveDeletionRequest;
use Illuminate\Support\Facades\View; // Import the View facade
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\LeaveRequestMail;
use App\Mail\ManagementApprovalMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;




use Carbon\Carbon;


class CertificateController extends Controller
{
    public function create()
    {
        $users = User::all(); // Fetch all users
        return view('hr.certificate', compact('users')); // Pass users to the view
    }
    
    

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'certificate_type' => 'required|in:Expiration Based,Non-Expiration Based',
            'issued_date' => 'required|date',
            'expire_date' => 'nullable|date|after_or_equal:issued_date',
            'certificate_file' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $filePath = $request->file('certificate_file')->store('certificates');

        Certificate::create([
            'employee_id' => $request->employee_id,
            'certificate_type' => $request->certificate_type,
            'issued_date' => $request->issued_date,
            'expire_date' => $request->expire_date,
            'certificate_file' => $filePath,
        ]);

        return back()->with('msg', 'Certificate added successfully.');
    }
}
