<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Leave;

class SupervisorInChiefApprovalMail extends Mailable
{
    use Queueable, SerializesModels;

    public $leave;
    public $supervisorName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Leave $leave, $supervisorName)
    {
        $this->leave = $leave;
        $this->supervisorName = $supervisorName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.supervisor_in_chief_approval')
                    ->subject('Leave Request Approval Required from Supervisor in Chief')
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    }
}



