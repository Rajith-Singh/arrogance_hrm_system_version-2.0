<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageBody;
    public $attachmentPath;

    public function __construct($messageBody, $attachmentPath = null)
    {
        $this->messageBody = $messageBody;
        $this->attachmentPath = $attachmentPath;
    }

    public function build()
    {
        $email = $this->subject('Support Request')
            ->view('emails.support') // Use custom Blade view
            ->with([
                'messageBody' => $this->messageBody,
            ]);

        if ($this->attachmentPath) {
            $email->attachFromStorage($this->attachmentPath);
        }

        return $email;
    }
}

