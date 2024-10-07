<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportMail;

class SupportController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'department' => 'required|string',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf,docx,txt|max:2048'
        ]);

        $departmentEmails = [
            'HR' => 'thileeka.sewmini@arrogance.lk',
            'IT Admin' => 'tharusha.singh@arrogance.lk',
            'Management' => 'gayani.medawatta@arrogance.lk',
        ];

        // Get the recipient email based on department
        $recipient = $departmentEmails[$request->department];

        $attachment = $request->file('attachment');
        $attachmentPath = $attachment ? $attachment->store('attachments') : null;

        // Create the mail instance
        $mail = new SupportMail($request->message, $attachmentPath);

        // Add CC if the department is IT Admin
        if ($request->department === 'IT Admin') {
            Mail::to($recipient)
                ->cc('pahasara.tissera@arrogance.lk') // Add the CC for IT Admin
                ->send($mail);
        } else {
            // Send without CC for other departments
            Mail::to($recipient)->send($mail);
        }

        return back()->with('msg', 'Your support request has been sent!');
    }
}


