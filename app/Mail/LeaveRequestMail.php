<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Leave;

class LeaveRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Leave $leave)
    {
        $this->leave = $leave;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $supervisorName = $this->leave->user->supervisor ? $this->leave->user->supervisor->name : 'Supervisor';
        $coveringPersonName = $this->leave->coveringPerson ? $this->leave->coveringPerson->name : 'N/A';

        return $this->view('emails.leave_request')
                    ->subject('New Leave Request from ' . $this->leave->user->name)
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->with([
                        'supervisorName' => $supervisorName,
                        'coveringPersonName' => $coveringPersonName,
                    ]);
    }
}


