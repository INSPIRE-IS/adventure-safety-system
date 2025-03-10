<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Log;

class MessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageContent;
    public $subjectLine;

    /**
     * Create a new message instance.
     */
    public function __construct(string $messageContent, string $subjectLine)
    {
        $this->messageContent = $messageContent;
        $this->subjectLine = $subjectLine;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        Log::info("Subject: {$this->subjectLine}");
        Log::info("Message: {$this->messageContent}");
        return $this->subject($this->subjectLine, $this->messageContent)
            ->view('emails.message') // Ensure you have a Blade template for the email
            ->with(['subject' => $this->subjectLine, 'message' => $this->messageContent]);
    }
}
